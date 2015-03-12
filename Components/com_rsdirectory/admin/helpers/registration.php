<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * RSDirectory! Registration class.
 */
class RSDirectoryRegistration
{
    /**
     * Class options.
     *
     * @access private
     *
     * @var array
     */
    private $options = array(
        'password_length' => 6, // The length of the autogenerated password.
        'username_use_alternative' => true, // Automatically generate an alternative for the username if it is already in use.
    );
        
    /**
     * Registration data.
     *
     * @access private
     *
     * @var array
     */
    private $data = array();
        
    /**
     * Registration errors.
     *
     * @access private
     *
     * @var array
     */
    private $errors = array();
        
    /**
     * The id of the last registered user.
     *
     * @access private
     *
     * @var int
     */
    private $user_id;
        
    /**
     * Set option.
     *
     * @access public
     *
     * @param string $option
     * @param mixed $value
     *
     * @return RSDirectoryRegistration
     */
    public function setOption($option, $value)
    {
        if ( isset($this->options[$option]) )
        {
            $this->options[$option] = $value;
        }
            
        return $this;
    }
        
    /**
     * Set registration data.
     *
     * @access public
     *
     * @param array $data
     *
     * @return RSDirectoryRegistration
     */
    public function setData($data)
    {
        if ( is_array($data) )
        {
            $this->data = $data;
        }
            
        return $this;
    }
        
    /**
     * Set data element.
     *
     * @access public
     *
     * @param string $element
     * @param mixed $value
     *
     * @return RSDirectoryRegistration
     */
    public function setDataElement($element, $value)
    {
        $this->data[$element] = $value;
            
        return $this;
    }
        
    /**
     * Set error.
     *
     * @access public
     *
     * @param string $error
     *
     * @return bool
     */
    public function setError($error)
    {
        if ( !in_array($error, $this->errors) )
        {
            $this->errors[] = $error;
        }
            
        return false;
    }
        
    /**
     * Get errors.
     *
     * @access public
     *
     * @return arrayt
     */
    public function getErrors()
    {
        return $this->errors;
    }
        
    /**
     * Get the id of the last registered user.
     *
     * @access public
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }
        
    /**
     * Attempt a user registration based on the registration data.
     *
     * @access public
     *
     * @return bool
     */
    public function register()
    {
        $data = $this->data;
            
        // Get the user configuration.
        $params = JComponentHelper::getParams('com_users');
            
        // Exit the function if the email is empty.
        if ( empty($data['email']) )
        {
            return $this->setError( JText::_('COM_RSDIRECTORY_EMAIL_REQUIRED') );
        }
            
        // If the username was not specified, create one from the email address, by using the string before the "@".
        if ( empty($data['username']) )
        {
            list($data['username']) = explode('@', $data['email']);
        }
            
        // If the name was not specified, create one from the email address, by using the string before the "@".
        if ( empty($data['name']) )
        {
            list($data['name']) = explode('@', $data['email']);
        }
            
        // Generate an alternative for the username if it is already in use.
        if ($this->options['username_use_alternative'])
        {
            while ( RSDirectoryHelper::userExists( array('username' => $data['username']) ) )
            {
                $data['username'] .= RSDirectoryHelper::randStr(1, '0123456789');
            }
        }
        // Set an error if the username is already in use.
        else if ( RSDirectoryHelper::userExists( array('username' => $data['username']) ) )
        {
            return $this->setError( JText::_('COM_RSDIRECTORY_USERNAME_IN_USE') );
        }
            
        $data['groups'] = array( $params->get('new_usertype', 2) );
            
        // Create a data object for the triggered event.
        $event_data = (object)$data;
        $event_data->email1 = $data['email'];
        $event_data->email2 = $data['email'];
            
        unset($event_data->password);
            
        // Trigger the data preparation event.
        JPluginHelper::importPlugin('user');
		$results = JFactory::getApplication()->triggerEvent( 'onContentPrepareData', array('com_users.registration', $data) );
            
        // If the password was not specified, generate a random one.
        if ( empty($data['password']) )
        {
            $data['password'] = RSDirectoryHelper::randStr($this->options['password_length']);
        }
            
        $useractivation = $params->get('useractivation');
            
		// Check if the user needs to activate their account.
		if ( in_array( $useractivation, array(1, 2) ) )
		{
			$data['activation'] = JApplication::getHash( JUserHelper::genRandomPassword() );
			$data['block'] = 1;
		}
            
        // Sanitize the name.
        $data['name'] = JComponentHelper::filterText($data['name']);
            
        // Create a new JUser object.
        $user = new JUser;
            
        // Bind the data.
		if ( !$user->bind($data) )
		{
			return $this->setError( JText::sprintf( 'COM_RSDIRECTORY_REGISTRATION_BIND_FAILED', $user->getError() ) );
		}
            
        // Store the data.
		if ( !$user->save() )
		{
			return $this->setError( JText::sprintf('COM_RSDIRECTORY_REGISTRATION_SAVE_FAILED', $user->getError() ) );
		}
            
        // Used for retrieving the id of the newly registered user.
        $this->user_id = $user->id;
            
        $config = RSDirectoryConfig::getInstance();
            
        $jconfig = JFactory::getConfig();
            
        $uri = JURI::getInstance();
            
        $base = $uri->toString( array('scheme', 'user', 'pass', 'host', 'port') );
            
        // Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname'] = $config->get('from_name');
		$data['mailfrom'] = $config->get('from_email');
		$data['sitename'] = $jconfig->get('sitename');
		$data['siteurl'] = JUri::root();
			
		// Admin activation.
		if ($useractivation == 2)
		{
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);
				
			$emailSubject = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);
				
			$emailBody = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		// Self activation.
		else if ($useractivation == 1)
		{
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);
				
			$emailSubject = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);
				
			$emailBody = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		// No activation.
		else
		{
			$emailSubject = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);
				
			$emailBody = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
            
        $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
            
        $db = JFactory::getDbo();
            
        // Send Notification mail to administrators.
		if ( $useractivation < 2 && $params->get('mail_to_admin') == 1 )
		{
			$emailSubject = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);
				
			$emailBodyAdmin = JText::sprintf(
				'COM_RSDIRECTORY_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);
				
			// Get all admin users.
			$query = $db->getQuery(true)
		           ->select( $db->qn( array('name', 'email', 'sendEmail') ) )
		           ->from( $db->qn('#__users') )
		           ->where( $db->qn('sendEmail') . ' = ' . $db->q(1) );
					
			$db->setQuery($query);
			$rows = $db->loadObjectList();
				
			// Send mail to all superadministrators.
			foreach ($rows as $row)
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
                    
                // Check for an error.
				if ($return !== true)
				{
					$this->setError(JText::_('COM_RSDIRECTORY_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					return false;
				}
			}
		}
            
        // Check for an error.
		if ($return !== true)
		{
			// Send a system message to administrators receiving system mails.
			$query = $db->getQuery(true)
		           ->select( $db->qn('id') )
		           ->from( $db->qn('#__users') )
		           ->where( $db->qn('sendEmail') . ' = ' . $db->q(1) . ' AND ' . $db->qn('block') . ' = ' . $db->q(0) );
					
			$db->setQuery($query);
			$results = $db->loadColumn();
				
			if ($results)
			{
				$jdate = new JDate;
					
                // Build the query to add the messages.
				$columns = array(
					'user_id_from',
					'user_id_to',
					'date_time',
					'subject',
					'message',
				);
					
				$query = $db->getQuery(true)
		               ->insert( $db->qn('#__messages') )
		               ->columns( $db->qn($columns) );
						
				foreach ($results as $userid)
				{
					$values = array(
						$db->q($userid),
						$db->q($userid),
						$db->q( $jdate->toSql() ),
						$db->q( JText::_('COM_RSDIRECTORY_MAIL_SEND_FAILURE_SUBJECT') ),
						$db->q( JText::sprintf('COM_RSDIRECTORY_MAIL_SEND_FAILURE_BODY', $return, $data['username']) ),
					);
						
					$query->values( implode(',', $values) );
				}
					
				$db->setQuery($query);
				$db->execute();
			}
		}
            
        return true;
    }
}