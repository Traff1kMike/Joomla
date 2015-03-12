<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits History controller.
 */
class RSDirectoryControllerCreditsHistory extends JControllerAdmin
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
    public function getModel( $name = 'CreditHistory', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
        
    /**
     * Method to mark on or more entries credits as paid/unpaid.
     *
     * @access public
     */    
    public function markAsPaid()
    {
        // Check for request forgeries
		JSession::checkToken() or die( JText::_('JINVALID_TOKEN') );
            
        $app = JFactory::getApplication();
            
		// Get items to publish from the request.
		$cid = $app->input->get( 'cid', array(), 'array' );
		$data = array('markAsPaid' => 1, 'markAsUnpaid' => 0);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
			
		if ( empty($cid) )
		{
			JLog::add( JText::_('JERROR_NO_ITEMS_SELECTED'), JLog::WARNING, 'jerror' );
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
                
			// Make sure the item ids are integers.
			JArrayHelper::toInteger($cid);
                
			// Mark the items as paid/unpaid.
			if ( !$model->markAsPaid($cid, $value) )
			{
				JLog::add( $model->getError(), JLog::WARNING, 'jerror' );
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_RSDIRECTORY_N_ITEMS_MARKED_AS_PAID';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_RSDIRECTORY_N_ITEMS_MARKED_AS_UNPAID';
				}
                    
				$this->setMessage( JText::plural( $ntext, count($cid) ) );
			}
		}
            
		$extension = $app->input->get('extension');
		$extensionURL = $extension ? '&extension=' . $extension : '';
		$this->setRedirect( JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false) );
    }
}