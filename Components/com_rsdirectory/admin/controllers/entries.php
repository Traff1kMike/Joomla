<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entries controller.
 */
class RSDirectoryControllerEntries extends JControllerAdmin
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
            
        $this->registerTask('markAsUnpaid', 'markAsPaid');
    }
		
    /**
     * Method to get a model object, loading it if required.
     *
     * @access public
     * 
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     */
    public function getModel( $name = 'Entry', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
        
    /**
     * Method to process a batch process.
     *
     * @access public
     */
    public function batch()
    {
        // Check for request forgeries.
		JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        $app = JFactory::getApplication();
            
        $data = $app->input->get( 'batch', array(), 'array' );
        $cid = $app->input->get( 'cid', array(), 'array' );
            
        // Get an instance of the Entries model.
        $entries_model = RSDirectoryModel::getInstance('Entries');
            
        // Check for validation errors.
        if ( !$entries_model->batchValidate($data, $cid) )
        {
            // Get the validation messages.
			$errors = $entries_model->getErrors();
                
			// Push up to three validation messages out to the user.
			for ( $i = 0, $n = count($errors); $i < $n && $i < 3; $i++ )
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage( $errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
                
            $app->redirect( JUri::getInstance()->toString() );
        }
            
        // Attempt to process the batch.
        if ( !$entries_model->batchProcess($data, $cid) )
        {
            $message = JText::sprintf( 'JERROR_SAVE_FAILED', $model->getError() );
                
            $app->enqueueMessage($message, 'error');
            $app->redirect( JUri::getInstance()->toString() );
        }
            
        $app->enqueueMessage( JText::_('JLIB_APPLICATION_SUCCESS_BATCH') );
        $app->redirect( JUri::getInstance()->toString() );
    }
		
	/**
	 * Method to mark a list of items as paid/unpaid.
	 * 
	 * @access public
	 */
	public function markAsPaid()
	{
		// Check for request forgeries
		JSession::checkToken() or die( JText::_('JINVALID_TOKEN') );
			
		// Get items to publish from the request.
		$cid = JFactory::getApplication()->input->get( 'cid', array(), 'array' );
		$data = array('markAsPaid' => 1, 'markAsUnpaid' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
			
		if ( empty($cid) )
		{
			JLog::add( JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror' );
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
				
			// Make sure the item ids are integers.
			JArrayHelper::toInteger($cid);
				
			// Mark the items as paid/unpaid.
			try
			{
				$model->markAsPaid($cid, $value);
					
				if ($value == 1)
				{
					$ntext = 'COM_RSDIRECTORY_N_ITEMS_MARKED_AS_PAID';
				}
				else if ($value == 0)
				{
					$ntext = 'COM_RSDIRECTORY_N_ITEMS_MARKED_AS_UNPAID';
				}
					
				$this->setMessage( JText::plural( $ntext, count($cid) ) );
			}
			catch (Exception $e)
			{
				$this->setMessage( JText::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'), 'error' );
			}
		}
			
		$extension = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}
}