<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry controller.
 */
class RSDirectoryControllerEntry extends JControllerForm
{
    /**
	 * Constructor.
	 *
	 * @access public
	 *
	 * @param array $config An optional associative array of configuration settings.
	 */
	public function __construct( $config = array() )
	{
        parent::__construct($config);
            
        $this->registerTask('saveAndBuyCredits', 'save');
    }
        
    /**
     * Method to get a model object, loading it if required.
     *
     * @access public
     * 
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     *
     * @return object
     */
    public function getModel( $name = 'Entry', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
		
    /**
     * Method to select a category.
     *
     * @access public
     */
    public function selectCategory()
    {
        $app = JFactory::getApplication();
            
        // Get the data.
        $fields = $app->input->get( 'fields', array(), 'array' );
            
        $message = null;
        $message_type = 'message';
			
		$uri = JUri::getInstance();
            
        if ( empty($fields['category_id']) )
        {
            $message = JText::_('COM_RSDIRECTORY_FIELD_ERROR_SELECT_CATEGORY');   
            $message_type = 'error';
        }
        else
        {
            if ( RSDirectoryHelper::getSubcategories($fields['category_id']) )
            {
                $message = JText::_('COM_RSDIRECTORY_FIELD_ERROR_INVALID_CATEGORY');
                $message_type = 'error';
            }
            else
            {
				$uri->setVar('category_id', $fields['category_id']);
            }
        }
            
        // Delete the task variable to avoid an infinite redirection loop when logging in from the "Add entry page".
        $uri->delVar('task');
            
        // Delete the fields variable to clean the url.
        $uri->delVar('fields');
			
        $this->setRedirect( $uri->toString(), $message, $message_type );
    }
        
    /**
     * Method to go back to the entry page.
     *
     * @access public
     */
    public function back()
    {
        $app = JFactory::getApplication();
            
        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
            
        $fields = $app->input->get( 'fields', array(), 'array' );
            
        $entry = JTable::getInstance('Entry', 'RSDirectoryTable');
            
        $entry->load( isset($fields['id']) ? $fields['id'] : 0 );
            
        $app->redirect( RSDirectoryRoute::getEntryURL($entry->id, $entry->title, '', 0, false, false) );
    }
        
    /**
     * Method to save a record.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
     * @param string $urlVar The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return bool True if successful, false otherwise.
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        // Get the mainframe.
        $app = JFactory::getApplication();
        
        // Is site?
        $is_site = $app->isSite();
            
        // Get the data.
        $data = $app->input->get( 'fields', array(), 'array' );
            
        if ( empty($data['id']) )
        {
            if ( $is_site && !RSDirectoryHelper::checkUserPermission('can_add_entries') )
            {
                $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_ADD_PERMISSION_ERROR'), 'error' );
                $app->redirect( JURI::getInstance()->toString() );
            }
        }
        else
        {
            JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
                
            // Get an instance of the Entry table.
            $entry = JTable::getInstance('Entry', 'RSDirectoryTable');
                
            // Load entry.
            $entry->load($data['id']);
                
            // Return false if no entry was found with the specified entry id.
            if (!$entry->id)
                return false;
                
            if ($is_site)
            {
                // Get user object.
                $user = JFactory::getUser();
                    
                // Get persmissions.
                $can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
                $can_edit_own_entries = RSDirectoryHelper::checkUserPermission('can_edit_own_entries') && $user->id == $entry->user_id;
                    
                // Check permissions.
                if ( !( $user->id != 0 && ($can_edit_all_entries || $can_edit_own_entries) ) )
                {
                    $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_EDIT_PERMISSION_ERROR'), 'error' );
                    $app->redirect( JURI::getInstance()->toString() );
                }
            }
        }
            
        // Get the Entry model.
        $model = $this->getModel();
            
        // Get the form.
        $form = $model->getForm();
            
        // Validate the posted data.
        $return = $model->validate($form, $data);
            
        // Check for validation errors.
        if ($return === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();
                
            // Push up to three validation messages out to the user.
            for ( $i = 0, $n = count($errors); $i < $n && $i < 3; $i++ )
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage( $errors[$i]->getMessage(), 'warning' );
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }
                
            // Save the data in the session.
            $app->setUserState('com_rsdirectory.edit.entry.data', $data);
                
            // Redirect back to the edit screen.
            $app->redirect( JUri::getInstance()->toString() );
        }
            
            
        // Attempt to save the entry.
        $data = $return;
        $return = $model->save($data);
            
            
        // Check the return value.
        if ($return === false)
        {
            // Save the data in the session.
            $app->setUserState('com_rsdirectory.edit.entry.data', $data);
                
            $message = JText::sprintf( 'JERROR_SAVE_FAILED', $model->getError() );
                
            // Redirect back to the edit screen.
            $app->enqueueMessage($message, 'error');
            $app->redirect( JUri::getInstance()->toString() );
        }
            
        // Get the task.
        $task = $this->getTask();
            
        if ( $app->isSite() )
        {
            // Get the entry id.
            $id = $model->getState($this->context . '.id');
                
            if ( RSDirectoryCredits::hasUnpaidEntryCredits($id) )
            {
                if ($task == 'saveAndBuyCredits')
                {
                    $app->redirect( RSDirectoryRoute::getURL('credits', '', "entry_id=$id", false, false) );
                }
                else
                {
                    if ( JFactory::getUser() )
                    {
                        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
                            
                        // Get an instance of the Entry table.
                        $entry = JTable::getInstance('Entry', 'RSDirectoryTable');
                            
                        $entry->load($id);
                            
                        $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_SAVED') );
                        $app->redirect( RSDirectoryRoute::getEntryURL($id, $entry->title, 'edit', 0, false, false) );
                    }
                    else
                    {
                        $app->setUserState('com_rsdirectory.edit.entry.id', $id);
                            
                        $url = "index.php?option=com_rsdirectory&view=entry&layout=thank_you";
                            
                        // Redirect to a thank you page.
                        $app->redirect( JRoute::_($url, false) );
                    }
                }
            }
            else
            {
                if ( empty($data['id']) )
                {
                    $app->setUserState('com_rsdirectory.edit.entry.id', $id);
                        
                    $url = "index.php?option=com_rsdirectory&view=entry&layout=thank_you";
                        
                    // Redirect to a thank you page.
                    $app->redirect( JRoute::_($url, false) );
                }
                else
                {
                    JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
                        
                    // Get an instance of the Entry table.
                    $entry = JTable::getInstance('Entry', 'RSDirectoryTable');
                        
                    $entry->load($id);
                        
                    $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_SAVED') );
                    $app->redirect( RSDirectoryRoute::getEntryURL($id, $entry->title, 'edit', 0, false, false) );
                }
            }
        }
        else
        {  
            // Apply or Save as Copy.
            if ($task == 'apply' || $task == 'save2copy')
            {
                // Get the field id.
                $id = $model->getState($this->context . '.id');
                    
                $url = "index.php?option=com_rsdirectory&task=entry.edit&id=$id";
                    
                $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_SAVED') );
                $app->redirect( JRoute::_($url, false) );
            }
            // Save & New.
            else if ($task == 'save2new')
            {
                if ( !$category_id = $app->input->getInt('category_id') )
                {
                    // Get the field id.
                    $id = $model->getState($this->context . '.id');
                        
                    $entry = RSDirectoryHelper::getEntry($id);
                        
                    $category_id = $entry->category_id;
                }
                    
                $url = "index.php?option=com_rsdirectory&task=entry.edit&category_id=$category_id";
                    
                $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_SAVED') );
                $app->redirect( JRoute::_($url, false) );
            }
            else
            {
                $app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_SAVED') );
                $app->redirect( JRoute::_('index.php?option=com_rsdirectory&view=entries', false) );
            }
        }
            
        return true;
    }
        
    /**
     * Method to delete an entry.
     *
     * @access public
     */
    public function deleteAjax()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
          
        $app = JFactory::getApplication();
          
        // Get the entry id.
        $id = $app->input->get('id');
            
        JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
            
        // Get an instance of the Entry table.
        $entry = JTable::getInstance('Entry', 'RSDirectoryTable');
            
        // Load entry.
        $entry->load($id);
            
        // Get the JUser object.
        $user = JFactory::getUser();
            
        // Initialize the response array.
        $response = array();
            
        // Get permissions.
        $can_delete_all_entries = RSDirectoryHelper::checkUserPermission('can_delete_all_entries');
        $can_delete_own_entries = RSDirectoryHelper::checkUserPermission('can_delete_own_entries') && $entry->user_id == $user->id;
            
        if ($can_delete_all_entries || $can_delete_own_entries)
        {
            $entry->delete();
            $response['ok'] = 1;
                
            if ( $app->input->getInt('message') )
            {
                $response['message'] = JText::sprintf( 'COM_RSDIRECTORY_ENTRY_SUCCESSFULLY_DELETED', JUri::root(true) );
            }
        }
        else
        {
            $response['error'] = JText::_('COM_RSDIRECTORY_ENTRY_DELETE_PERMISSION_ERROR');
        }
            
        // Output the JSON response.
        echo json_encode($response);
            
        $app->close();
    }
        
    /**
     * Method to add entry to favorites.
     *
     * @access public
     */
    public function addToFavoritesAjax()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        $app = JFactory::getApplication();
            
        // Get the entry id.
        $entry_id = $app->input->get('id');
            
        // Get the JUser object.
        $user = JFactory::getUser();
            
        // Initialize the response array.
        $reponse = array();
            
        if ($user->id)
        {
            // Get DBO.
            $db = JFactory::getDbo();
                
            $query = $db->getQuery(true)
                   ->select( $db->qn('entry_id') )
                   ->from( $db->qn('#__rsdirectory_favorites') )
                   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) . ' AND ' . $db->qn('user_id') . ' = ' . $db->q($user->id) );
                   
            $db->setQuery($query);
                
            if ( $db->loadResult() )
            {
                $query = $db->getQuery(true)
                       ->delete( $db->qn('#__rsdirectory_favorites') )
                       ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) . ' AND ' . $db->qn('user_id') . ' = ' . $db->q($user->id) );
                       
                $db->setQuery($query);
                $db->execute();
            }
            else
            {
                $fav = (object)array(
                    'entry_id' => $entry_id,
                    'user_id' => $user->id,
                    'created_time' => JFactory::getDate()->toSql(),
                );
                    
                $db->insertObject('#__rsdirectory_favorites', $fav);
            }
                
            $reponse['ok'] = 1;
        }
        else
        {
            $reponse['error'] = JText::_('COM_RSDIRECTORY_LOG_IN_TO_USE_FEATURE');
        }
            
        // Output the JSON response.
        echo json_encode($reponse);
            
        $app->close();
    }
        
    /**
     * Method to check user credits.
     *
     * @access public
     */    
    public function checkCreditsAjax()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        $app = JFactory::getApplication();
            
        $cost = $app->input->getInt('cost');
            
        if ( RSDirectoryCredits::checkUserCredits($cost) )
        {
            $response = array('ok' => 1);
        }
        else
        {
            $response = array('ok' => 0);
        }
            
        echo json_encode($response);
            
        $app->close();
    }
        
    /**
     * Method to handle entry finalization.
     *
     * @access public
     */    
    public function finalize()
    {
        $app = JFactory::getApplication();
            
        // Get entry id.
        $id = $app->input->getInt('id');
            
        // Load entry.
        $entry = RSDirectoryHelper::getEntry($id);
            
        if (!$entry)
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
			
		// Get user object.
		$user = JFactory::getUser();
			
		// Get persmissions.
		$can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
		$can_edit_own_entries = RSDirectoryHelper::checkUserPermission('can_edit_own_entries') && $user->id == $entry->user_id;
			
		// Check permissions.
		if ( !( $user->id != 0 && ($can_edit_all_entries || $can_edit_own_entries) ) )
		{
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_PERMISSION_DENIED'), 'error' );
			$app->redirect( JURI::getInstance()->toString() );
		}
            
        // Get the number of unpaid entry credits.
        $cost = RSDirectoryCredits::getUnpaidEntryCreditsSum($id);
            
        if ( $can_edit_all_entries || RSDirectoryCredits::checkUserCredits($cost) )
        {
			// If the user has unlimited credits just go ahead and mark the entry as paid without any confirmation.
			if ( $can_edit_all_entries || RSDirectoryCredits::getUserCredits() == 'unlimited' )
			{
				RSDirectoryModel::getInstance('Entry')->finalize($entry->id);
					
				$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_FINALIZED') );
					
				// Redirect to the entry page.
				$app->redirect( RSDirectoryRoute::getEntryURL($entry->id, $entry->title, '', 0, false, false) );
			}
			else
			{
				// Redirect to the confirmation page.
				$app->redirect( RSDirectoryRoute::getURL( 'entry', 'finalize_confirm', 'id=' . RSDirectoryHelper::sef($entry->id, $entry->title), false, false ) );
			}
        }
        else
        {
            // Redirect to the Buy Credits page.
            $app->redirect( RSDirectoryRoute::getURL('credits', '', "finalize=1&entry_id=$entry->id", false, false) );
        }
    }
		
	/**
	 * Method to confirm an entry finalization.
	 *
	 * @access public
	 */	
	public function finalizeConfirm()
	{
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        $app = JFactory::getApplication();
			
        $fields = $app->input->get( 'fields', array(), 'array' );
			
		if ( empty($fields['id']) )
			return;
			
		// Get entry id.
		$id = $fields['id'];
            
        // Load entry.
        $entry = RSDirectoryHelper::getEntry($id);
            
        if (!$entry)
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
			
		// Get user object.
		$user = JFactory::getUser();
			
		// Get persmissions.
		$can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
		$can_edit_own_entries = RSDirectoryHelper::checkUserPermission('can_edit_own_entries') && $user->id == $entry->user_id;
			
		// Check permissions.
		if ( !( $user->id != 0 && ($can_edit_all_entries || $can_edit_own_entries) ) )
		{
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_PERMISSION_DENIED'), 'error' );
			$app->redirect( JURI::getInstance()->toString() );
		}
			
		// Get the number of unpaid entry credits.
        $cost = RSDirectoryCredits::getUnpaidEntryCreditsSum($id);
			
		if ( $can_edit_all_entries || RSDirectoryCredits::checkUserCredits($cost) )
        {
			RSDirectoryModel::getInstance('Entry')->finalize($id);
				
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_FINALIZED') );
				
			// Redirect to the entry page.
			$app->redirect( RSDirectoryRoute::getEntryURL($entry->id, $entry->title, '', 0, false, false) );
		}
		else
		{
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ENTRY_FINALIZE_INSUFFICIENT_CREDITS_ERROR'), 'error' );
		}
	}
		
	/**
	 * Method to check a category when the "Change category" button is pressed.
	 *
	 * @access public
	 */
	public function checkCategoryAjax()
	{
		$app = JFactory::getApplication();
			
		$current_category_id = $app->input->getInt('current_category_id');
		$new_category_id = $app->input->getInt('new_category_id');
			
		// Initialize the changed value.
		$changed = false;
			
		if ($current_category_id != $new_category_id)
		{
			$current_form_id = RSDirectoryHelper::getCategoryInheritedFormId($current_category_id);
			$new_form_id = RSDirectoryHelper::getCategoryInheritedFormId($new_category_id);
				
			if ($current_form_id != $new_form_id)
			{
				$changed = true;
			}
		}
			
		$response = array(
			'changed' => $changed,
			'message' => JText::_($changed ? 'COM_RSDIRECTORY_CHANGE_CATEGORY_CONFIRMATION' : 'COM_RSDIRECTORY_CATEGORY_CHANGED')
		);
			
		echo json_encode($response);
			
		$app->close();
	}
		
	/**
	 * Method to change the category of an entry.
	 *
	 * @access public
	 */	
	public function changeCategory()
	{
        $app = JFactory::getApplication();
			
		$fields = $app->input->get( 'fields', array(), 'array' );
			
		$id = empty($fields['id']) ? '' : $fields['id'];
		$category_id = empty($fields['category_id']) ? '' : $fields['category_id'];
			
		if ( $app->isSite() )
		{
			$uri = JUri::getInstance();
				
			if ($category_id)
			{
				$uri->setVar('category_id', $category_id);
			}
				
			$url = JRoute::_( $uri->toString(), false );
		}
		else
		{
			$url = JRoute::_( "index.php?option=com_rsdirectory&task=entry.edit&id=$id&category_id=$category_id", false );
		}
			
		// Redirect to the entry page.
		$app->enqueueMessage( JText::_('COM_RSDIRECTORY_CATEGORY_CHANGED'), 'notice' );
		$app->redirect($url);
	}
		
    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @access protected
     * 
     * @param int $recordId
     * @param string $urlVar
     * 
     * @return string
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
            
        // Get the category id.
        $category_id = JFactory::getApplication()->input->getInt('category_id');
            
        return  $append . ($category_id ? "&category_id=$category_id" : '');
    }
}