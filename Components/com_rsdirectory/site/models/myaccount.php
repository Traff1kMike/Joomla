<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * My Account model.
 */
class RSDirectoryModelMyAccount extends JModelAdmin
{
    /**
     * Method for getting the form from the model.
     *
     * @access public
     * 
     * @param array $data
     * @param bool $loadData
     * 
     * @return mixed
     */
    public function getForm( $data = array(), $loadData = true )
    {
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.user', 'user', array('control' => 'jform', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
		
	/**
     * Method to get the data that should be injected in the form.
     *
     * @access protected
     * 
     * @return array
     */
    protected function loadFormData()
    {
        // Check for data in the session.
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.user.data');
            
        return $data ? $data : $this->getItem();
    }
		
	/**
     * Method to get a single record.
     *
     * @access public
     *
     * @param int $pk The id of the primary key.
     *
     * @return mixed Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q( JFactory::getUser()->id ) );
               
        $db->setQuery($query);
            
        return $db->loadObject();
    }
		
	/**
     * Validate form data.
     *
     * @access public
     * 
     * @param object $form The form to validate against.
     * @param array $data The data to validate.
     * @param string $group The name of the field group to validate.
     * 
     * @return mixed
     */
    public function validate($form, $data, $group = null)
    {
        $data = parent::validate($form, $data, $group);
            
        if (!$data)
            return $data;
            
        if ( empty( JFactory::getUser()->id ) )
        {
            $this->setError( JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST') );
            return false;
        }
            
        return $data;
    }
		
	/**
     * Save the user data.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        // Exit the function if the data array is invalid.
        if (!$data)
            return false;
			
		// Initialize an array of settings that can be edited by the user.
		$allowed_settings = array(
			'enable_contact_form',
		);
			
		// Remove unallowed settings.
		foreach ($data as $setting)
		{
            if ( !in_array($setting, $allowed_settings) )
			{
				unset($data[$setting]);
			}
		}
			
        // Convert the data array to an object.
        $data = (object)$data;
			
		// Set the user id.
		$data->user_id = JFactory::getUser()->id;
			
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($data->user_id) );
               
        $db->setQuery($query);
            
        if ( $db->loadResult() )
            return $db->updateObject( '#__rsdirectory_users', $data, 'user_id' );
            
            
        return $db->insertObject( '#__rsdirectory_users', $data, 'user_id' );
    }
		
    /**
     * Method to auto-populate the model state. 
     * 
     * @access protected
     * 
     * @param string $ordering
     * @param string $direction
     */
    protected function populateState()
    {
		$app = JFactory::getApplication();
			
		$params = $app->getParams();
		$this->setState('params', $params);
    }
        
    /**
     * Method to get user transactions.
     *
     * @access public
     *
     * @return mixed
     */
    public function getTransactions()
    {
		// Get DBO.
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
			   ->select('*')
			   ->from( $db->qn('#__rsdirectory_users_transactions') )
			   ->where( $db->qn('user_id') . ' = ' . $db->q( JFactory::getUser()->id ) )
			   ->order( $db->qn('date_created') . ' DESC' );
			   
		$db->setQuery($query);
			
		return $db->loadObjectList();
    }
         
    /**
     * The the number of entries posted by the currently logged in user.
     *
     * @access public
     *
     * @return int
     */
    public function getPostedEntriesCount()
    {
		// Get DBO.
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
			   ->select('COUNT(*)')
			   ->from( $db->qn('#__rsdirectory_entries') )
			   ->where( $db->qn('user_id') . ' = ' . $db->q( JFactory::getUser()->id ) );
			   
		$db->setQuery($query);
			
		return (int)$db->loadResult();
    }
        
    /**
     * Get RSTabs.
     *
     * @access public
     * 
     * @return RSTabs
     */
    public function getRSTabs()
    {
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/tabs.php';
			
		return new RSTabs('com-rsdirectory-configuration');
    }
		
	/**
     * Get RSFieldset.
     *
     * @access public
     * 
     * @return RSFieldset
     */
    public function getRSFieldset()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/fieldset.php';
            
        return new RSFieldset();
    }
}