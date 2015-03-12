<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Rating model.
 */
class RSDirectoryModelRating extends JModelAdmin
{
    /**
     * Model context string.
     *
     * @var string
     */
    protected $_context = 'com_rsdirectory.rating';
        
    /**
     * Error messages.
     *
     * @var array
     */
    protected $error_messages = array();
        
    /**
     * The fields that contain errors.
     *
     * @var array
     */
    protected $error_fields = array();
        
    /**
     * Set error message.
     *
     * @access private
     *
     * @param string $message
     */
    private function setErrorMessage($message)
    {
        if ( !in_array($message, $this->error_messages) )
        {
            $this->error_messages[] = $message;
        }
    }
        
    /**
     * Set field error.
     *
     * @access private
     *
     * @param string $field_name
     */
    private function setErrorField($field_name)
    {
        if ( !in_array($field_name, $this->error_fields) )
        {
            $this->error_fields[] = $field_name;
        }
    }
        
    /**
     * Get error messages.
     *
     * @access public
     *
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->error_messages;
    }
        
    /**
     * Get error fields.
     *
     * @access public
     *
     * @return array
     */
    public function getErrorFields()
    {
        return $this->error_fields;
    }
        
    /**
     * Method to get a table object, load it if necessary.
     * 
     * @access public
     * 
     * @param string $type
     * @param string $prefix
     * @param array $config
     * 
     * @return object
     */
    public function getTable( $type = 'Review', $prefix = 'RSDirectoryTable', $config = array() )
    {
        return JTable::getInstance($type, $prefix, $config);
    }
        
    /**
     * Method for getting the form from the model.
     *
     * @access public
     * 
     * @param array $data
     * @param bool $loadData
     * 
     * @return bool
     */
    public function getForm( $data = array(), $loadData = true )
    {
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.review', 'review', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.rating.data');
            
        return $data ? $data : $this->getItem();
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
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Front-end?
        $is_site = $app->isSite();
            
        if (!$is_site)
        {
            $return = parent::validate($form, $data, $group);
                
            if (!$return)
                return false;
                
            foreach ($return as $key => $value)
            {
                $data[$key] = $value;
            }
        }
            
        $return = $data;
            
        // Sanitize data.
        foreach ($data as &$value)
        {
            $value = trim($value);    
        }
            
        if ( empty($data['entry_id']) )
        {
            if ($is_site)
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_INVALID_ENTRY_ID_PROVIDED') );
                return false;
            }
            else
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_SELECT_ENTRY_ERROR') );
                $return = false;
            }
        }
        else
        {
            $entry = RSDirectoryHelper::getEntry($data['entry_id']);
                
            if (!$entry)
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_INVALID_ENTRY_ID_PROVIDED') );
                return false;
            }
        }
            
        if ($is_site)
        {
            // Do a security check.
            if ( !empty($data['id']) )
                return false;
            
            $user = JFactory::getUser();
                
            // Can the user vote his own entry?
            if ( $entry->user_id == $user->id && !RSDirectoryHelper::checkUserPermission('can_vote_own_entries') )
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_CANNOT_VOTE_OWN_ENTRY') );
                return false;
            }
                
            // Display a error message if the user already posted a review for this entry.
            if ( RSDirectoryHelper::hasReview($data['entry_id'], $user->id) )
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_ALREADY_POSTED_REVIEW') );
                $return = false;
            }
                
            // Get the RSDirectory Config object.
            $config = RSDirectoryConfig::getInstance();
                
            // Are reviews enabled?
            $enable_reviews = $config->get('enable_reviews');
                
            // Are ratings enabled?
            $enable_ratings = $config->get('enable_ratings');
                
            // Can the user post reviews?
            $can_post_reviews = RSDirectoryHelper::checkUserPermission('can_post_reviews');
                
            // Can the user cast votes?
            $can_cast_votes = RSDirectoryHelper::checkUserPermission('can_cast_votes');
                
            if ($user->guest)
            {
                if ( empty($data['name']) )
                {
                    $this->setErrorMessage( JText::_('COM_RSDIRECTORY_NAME_REQUIRED') );
                    $this->setErrorField('name');
                        
                    $return = false;
                }   
                    
                if ( empty($data['email']) )
                {
                    $this->setErrorMessage( JText::_('COM_RSDIRECTORY_EMAIL_REQUIRED') );
                    $this->setErrorField('email');
                        
                    $return = false;
                }
                else if ( !RSDirectoryHelper::email($data['email']) )
                {
                    $this->setErrorMessage( JText::_('COM_RSDIRECTORY_PROVIDE_VALID_EMAIL') );
                    $this->setErrorField('email');
                        
                    $return = false;
                }
            }
                
            if ($enable_reviews && $can_post_reviews)
            {
                if ( empty($data['subject']) )
                {
                    $this->setErrorMessage( JText::_('COM_RSDIRECTORY_SUBJECT_REQUIRED') );
                    $this->setErrorField('subject');
                        
                    $return = false;
                }
                    
                if ( empty($data['review']) )
                {
                    $this->setErrorMessage( JText::_('COM_RSDIRECTORY_REVIEW_REQUIRED') );
                    $this->setErrorField('review');
                        
                    $return = false;
                }
            }
            else if ( !empty($data['subject']) || !empty($data['review']) )
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_REVIEW_PERMISSION_ERROR') );
                $this->setErrorField('subject');
                $this->setErrorField('review');
                    
                $return = false;
            }
                
            if ( $enable_ratings && $can_cast_votes && !in_array( $data['score'], range(1, 5) ) )
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_SCORE_REQUIRED') );
                $this->setErrorField('score');
                    
                $return = false;
            }
            else if ( !($enable_ratings && $can_cast_votes) && !empty($data['score']) )
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_SCORE_PERMISSION_ERROR') );
                $this->setErrorField('score');
                    
                $return = false;
            }
        }
        else
        {
            if ( empty($data['user_id']) && empty($data['name']) )
            {
                $this->setErrorMessage( JText::_('COM_RSDIRECTORY_REVIEW_AUTHOR_ERROR') );
                    
                $return = false;
            }
        }
            
        return $return;
    }
        
    /**
     * Save the review.
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
            
        // Get mainframe.
        $app = JFactory::getApplication();
            
        $is_site = $app->isSite();
            
        // Get the review table.
        $review = $this->getTable();
            
        if ($is_site)
        {
            $review->user_id = JFactory::getUser()->id;
            $data['published'] = RSDirectoryHelper::checkUserPermission('auto_publish_reviews') ? 1 : 0;
        }
        else
        {
            if ( $app->input->get('task') == 'save2copy' && isset($data['id']) )
            {
                unset($data['id']);
            }
        }
            
        $return = $review->save($data);
            
        $this->setState( $this->getName() . '.id', $review->id );    
            
        return $return;
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
        // Get the id of the primary key.
        $pk = $pk ? $pk : (int) $this->getState( $this->getName() . '.id' );
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        $select = array(
            $db->qn('r') . '.*',
            $db->qn('e.title'),
            $db->qn('u.id', 'entry_author_id'),
            $db->qn("u.$author", 'entry_author'),
            $db->qn("ru.$author", 'review_author'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_reviews', 'r') )
               ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__users', 'ru') . ' ON ' . $db->qn('r.user_id') . ' = ' . $db->qn('ru.id') )
               ->where( $db->qn('r.id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
            
        return $db->loadObject();
    }
        
    /**
     * Method to get the owner reply.
     *
     * @access public
     *
     * @return mixed
     */
    public function getOwnerReply($pk)
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('r.owner_reply') )
               ->from( $db->qn('#__rsdirectory_reviews', 'r') )
               ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->where( $db->qn('r.id') . ' = ' . $db->q($pk) )
               ->where( $db->qn('e.user_id') . ' = ' . $db->q( JFactory::getUser()->id ) );
             
        $db->setQuery($query);
            
        $owner_reply = $db->loadResult();
            
        if ( is_null($owner_reply) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_REQUEST') );
            return false;
        }
            
        return $owner_reply;
    }
        
    /**
     * Method to save a owner reply.
     *
     * @access public
     *
     * @param array $data
     *
     * @return bool
     */
    public function saveOwnerReply($data)
    {
        if ( empty($data['review_id']) || !isset($data['owner_reply']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_REQUEST') );
            return false;
        }
            
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_reviews', 'r') )
               ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->where( $db->qn('r.id') . ' = ' . $db->q($data['review_id']) )
               ->where( $db->qn('e.user_id') . ' = ' . $db->q( JFactory::getUser()->id ) );
               
        $db->setQuery($query);
            
        // Check if the entry belongs to the user that posted the reply.
        if ( !$db->loadResult() )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_REQUEST') );
            return false;
        }
            
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_reviews') )
               ->set( $db->qn('owner_reply') . ' = ' . $db->q($data['owner_reply']) )
               ->where( $db->qn('id') . ' = ' . $db->q( trim($data['review_id']) ) );
               
        $db->setQuery($query);
        $db->execute();
            
        return true;
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
        require_once JPATH_COMPONENT . '/helpers/adapters/fieldset.php';
            
        return new RSFieldset();
    }
}