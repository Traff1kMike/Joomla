<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Fields controller.
 */
class RSDirectoryControllerFields extends JControllerAdmin
{        
    /**
     * Method to get a model object, loading it if required.
     *
     * @access public
     * 
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     */
    public function getModel( $name = 'Field', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
    
    /**
	 * Method to publish a list of items.
	 *
	 * @access public
	 */
	public function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or die( JText::_('JINVALID_TOKEN') );
            
		$app = JFactory::getApplication();
			
		$cid = $app->input->get( 'cid', array(), 'array' );
		$data = array('publish' => 1, 'unpublish' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
			
		if ( empty($cid) )
		{
			$this->setMessage( JText::_('JERROR_NO_ITEMS_SELECTED'), 'warning' );
			//JLog::add( JText::_('JERROR_NO_ITEMS_SELECTED'), JLog::WARNING, 'jerror' );
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
                
			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);
                
			// Publish the items.
			if ( $model->publish($cid, $value) )
			{
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				}
				else if ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				else if ($value == 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}
                    
				$this->setMessage( JText::plural( $ntext, count($cid) ) );
			}
			else
			{
				$this->setMessage( $model->getError(), 'warning' );
				//JLog::add( $model->getError(), JLog::WARNING, 'jerror' );
			}
		}
            
		$extension = $app->input->get('extension');
		$extensionURL = $extension ? '&extension=' . $extension : '';
		$this->setRedirect( JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false) );
	}
        
    /**
     * Save order ajax.
     *
     * @access public
     */
    public function saveOrderAjax()
    {
		$app = JFactory::getApplication();
            
        $pks = $app->input->post->get( 'cid', array(), 'array' );
        $order = $app->input->post->get( 'order', array(), 'array' );
            
        // Sanitize the input.
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);
            
        // Get the model.
        $model = $this->getModel();
            
        // Save the ordering.
        if ( $model->saveOrder($pks, $order) )
        {
            echo 1;
        }
            
        // Close the application.
        $app->close();
    }
        
    /**
     * Get custom fields palceholders.
     *
     * @access public
     */
    public function getCustomFieldsPlaceholdersAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		$app = JFactory::getApplication();
            
        // Get the category id.
        $category_id = $app->input->getInt('category_id');
            
        // Get the form id.
        $form_id = $app->input->getInt('form_id');
            
        echo RSDirectoryHelper::getCustomFieldsPlaceholdersHTML($category_id, $form_id);
            
        // Close the application.
        $app->close();
    }
        
    /**
     * Assign the form fields to a specified form.
     *
     * @access public
     */
    public function assign2FormAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
        $app = JFactory::getApplication();
            
        $field_ids = $app->input->get( 'field_ids', array(), 'array' );
        $form_id = $app->input->getInt('form_id');
        $model = $this->getModel();
            
        if ( $model->assign2Form($field_ids, $form_id) )
        {
            $app->enqueueMessage( JText::_('COM_RSDIRECTORY_FIELDS_SUCCESSFULLY_ASSIGNED_TO_FORM') );
        }
        else
        {
            // Get the validation messages.
            $errors = $model->getErrors();
                
            // Push up to three validation messages out to the user.
            for ( $i = 0, $n = count($errors); $i < $n && $i < 3; $i++ )
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage( $errors[$i]->getMessage(), 'error' );
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'error');
                }
            }
        }
            
        // Output the rendered message.
        $document = JFactory::getDocument();
        $renderer = $document->loadRenderer('message');
        $msg = $renderer->render(null);
        echo $msg;
            
        // Close the application.
        $app->close();
    }
}