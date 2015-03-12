<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Field controller.
 */
class RSDirectoryControllerField extends JControllerForm
{ 
    /**
     * Method to save the field.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
	 * @param string $urlVar The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return boolean True if successful, false otherwise.
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Get the JInput object.
        $jinput = $app->input;
            
        // Get the Field model.
        $model = $this->getModel('Field');
            
        // Get the form.
        $form = $model->getForm();
            
        // Get the data.
        $data = $jinput->get( 'jform', array(), 'array' );
            
        // Validate the posted data.
        $return = $model->validate($form, $data);
            
        // Get the field id.
        $id = $jinput->getInt('id');
            
        // Get the field type id.
        $field_type_id = $jinput->getInt('field_type_id');
            
        // Initialize the URL string.
        if ($id)
        {
            $url = "index.php?option=com_rsdirectory&view=field&layout=edit&id=$id";
        }
        else
        {
            $url = "index.php?option=com_rsdirectory&view=field&layout=edit&field_type_id=$field_type_id";
        }
            
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
            $app->setUserState('com_rsdirectory.edit.field.data', $data);
                
            // Redirect back to the edit screen.
            $app->redirect( JUri::getInstance()->toString() );
        }
            
            
        // Attempt to save the configuration.
        $data = $return;
        $return = $model->save($data);
            
            
        // Check the return value.
        if ($return === false)
        {
            // Save the data in the session.
            $app->setUserState('com_rsdirectory.edit.field.data', $data);
                
            $message = JText::sprintf( 'JERROR_SAVE_FAILED', $model->getError() );
                
            // Redirect back to the edit screen.
			$app->enqueueMessage($message, 'error');
            $app->redirect( JUri::getInstance()->toString() );
        }
            
        // Get the task.
        $task = $this->getTask();
            
        // Apply or Save as Copy.
        if ($task == 'apply' || $task == 'save2copy')
        {
            // Get the field id.
            $id = $model->getState($this->context . '.id');
                
            $url = "index.php?option=com_rsdirectory&task=field.edit&id=$id";
                
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_FIELD_SAVED') );
            $app->redirect( JRoute::_($url, false) );
        }
        // Save & New.
        else if ($task == 'save2new')
        {
            $url = "index.php?option=com_rsdirectory&task=field.edit&field_type_id=$field_type_id";
                
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_FIELD_SAVED') );
            $app->redirect( JRoute::_($url, false) );
        }
        else
        {
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_FIELD_SAVED') );
            $app->redirect( JRoute::_('index.php?option=com_rsdirectory&view=fields', false) );
        }
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
            
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the field type id.
        $field_type_id = $jinput->getInt('field_type_id');
            
        return  "$append" . ($field_type_id ? "&field_type_id=$field_type_id" : '');
    }
        
    /**
     * Generate a CAPTCHA image.
     *
     * @access public
     */
    public function captcha()
    {
        ob_end_clean();
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/securimage/securimage.php';
            
        // Get the RSDirectory! configuration.
        $config = RSDirectoryConfig::getInstance();
            
        // Create the JSecurImage object.
        $captcha = new JSecurImage();
            
        $characters = $config->get('captcha_characters_number');
            
        // Configure and display the CAPTCHA image.
        $captcha->num_lines = $config->get('captcha_generate_line') ? 8 : 0;
        $captcha->code_length = $characters;
        $captcha->image_width = 30 * $characters + 50;
        $captcha->case_sensitive = $config->get('captcha_case_sensitive');
        $captcha->show();
            
        // Close the application.
        JFactory::getApplication()->close();
    }
        
    /**
     * Order files ajax.
     *
     * @access public
     */
    public function orderFilesAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		$app = JFactory::getApplication();
            
        // Get the files ids.
        $pks = $app->input->post->get( 'cids', array(), 'array' );
            
        // Sanitize the input.
        JArrayHelper::toInteger($pks);
            
        // Get the model.
        $model = $this->getModel();
            
        // Save the ordering.
        if ( $model->saveFilesOrder($pks) )
        {
            echo 1;
        }
            
        // Close the application.
        $app->close();
    }
        
    /**
     * Delete file ajax.
     *
     * @access public
     */
    public function deleteFileAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		$app = JFactory::getApplication();
            
        // Get the file id.    
        $pk = $app->input->post->getInt('cid');
			
		// Initialize the response array.
		$response = array();
			
        // Get the model.
        $model = $this->getModel();
            
        // Delete the file.
        if ( $model->deleteFile($pk) )
        {
            $response['ok'] = 1;
        }
		else if ( $errors = $model->getErrors() )
		{
			$response['errors'] = implode("\n", $errors);
		}
			
		echo json_encode($response);
			
        // Close the application.
        $app->close();
    }
        
    /**
     * Get a dropdown field's items under the form of select options.
     *
     * @access public
     */
    public function getItemsOptionsAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		$app = JFactory::getApplication();
            
        // Get the field id.    
        $pk = $app->input->post->getInt('cid');
            
        // Get the model.
        $model = $this->getModel();
            
        // Output the options.
        echo $model->getItemsOptions($pk);
            
        // Close the application.
        $app->close();
    }
        
    /**
     * Get the data related to a selected dependency value.
     *
     * @access public
     */
    public function getDependencyOptionsAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		$app = JFactory::getApplication();
            
        // Get the field id.    
        $parent_id = $app->input->post->getInt('parent_id');
            
        // Get the field value.
        @list($value) = $app->input->post->get( 'value', array(), 'array' );
            
        // Initialize the response array.
        $response = array();
            
		// Get the model.
        $model = $this->getModel();
			
		if ( $app->input->post->getInt('filters') )
		{
			// Get module options.
			$options = $app->input->post->get( 'options', array(), 'array' );
				
			// Get dependency items for the filtering module.
			$response = $model->getFiltersDependencyItems($parent_id, $value, $options);
		}
		else
		{
			// Get dependency items.
			$response = $model->getDependencyItems($parent_id, $value);
		}
            
        echo json_encode($response);
            
        // Close the application.
        $app->close();
    }
}