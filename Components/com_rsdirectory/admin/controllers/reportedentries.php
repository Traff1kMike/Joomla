<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Reported Entries controller.
 */
class RSDirectoryControllerReportedEntries extends JControllerAdmin
{  
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
    public function getModel( $name = 'ReportedEntry', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
        
    /**
	 * Method to mark a list of reported entries as read/unread.
	 *
	 * @access public
	 */
	public function publish()
	{
        // Check for request forgeries
		JSession::checkToken() or die( JText::_('JINVALID_TOKEN') );
            
		$app = JFactory::getApplication();
            
		// Get items to publish from the request.
		$cid = $app->input->get( 'cid', array(), 'array' );
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
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
                
			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);
                
			// Mark the items as read/unread.
			if ( !$model->publish($cid, $value) )
			{
				JLog::add( $model->getError(), JLog::WARNING, 'jerror' );
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_RSDIRECTORY_N_ITEMS_MARKED_AS_READ';
				}
				elseif ($value == 0)
				{
					$ntext = 'COM_RSDIRECTORY_N_ITEMS_MARKED_AS_UNREAD';
				}
                    
				$this->setMessage( JText::plural( $ntext, count($cid) ) );
			}
		}
            
		$extension = $app->input->get('extension');
		$extensionURL = $extension ? '&extension=' . $extension : '';
		$this->setRedirect( JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false) );
    }
}