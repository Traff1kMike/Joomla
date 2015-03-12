<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories controller.
 */
class RSDirectoryControllerCategories extends JControllerAdmin
{
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param array $config
     */
    public function __construct( $config = array() )
    {
        // Call the parent constructor.
        parent::__construct($config);
			
		$app = JFactory::getApplication();
            
		if ( !JFactory::getUser()->authorise('categories.manage', 'com_rsdirectory') && !( $app->isSite() && $app->input->get('task') == 'getCategoriesSelectAjax' ) )
		{
			$app->enqueueMessage( JText::_('JERROR_ALERTNOAUTHOR'), 'error' );
			$app->redirect( JRoute::_('index.php?option=com_rsdirectory', false) );
		}
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
    public function getModel( $name = 'Category', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
		
	/**
	 * Rebuild the nested set tree.
	 *
	 * @access public
	 *
	 * @return bool False on failure or error, true on success.
	 */
	public function rebuild()
	{
		JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$this->setRedirect( JRoute::_('index.php?option=com_rsdirectory&view=categories', false) );
			
		$model = $this->getModel();
			
		if ( $model->rebuild() )
		{
			// Rebuild succeeded.
			$this->setMessage( JText::_('COM_RSDIRECTORY_REBUILD_SUCCESS') );
			return true;
		}
		else
		{
			// Rebuild failed.
			$this->setMessage( JText::_('COM_RSDIRECTORY_REBUILD_FAILURE') );
			return false;
		}
	}
        
    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @access public
     */
    public function saveOrderAjax()
    {
		JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
		$jinput = $app->input;
			
		// Get the arrays from the Request
		$pks = $jinput->post->get('cid', null, 'array');
		$order = $jinput->post->get('order', null, 'array');
		$originalOrder = explode( ',', $jinput->getString('original_order_values') );
			
		// Make sure something has changed
		if ($order !== $originalOrder)
		{
			// Get the model.
			$model = $this->getModel();
				
			// Save the ordering.
			$return = $model->saveorder($pks, $order);
				
			if ($return)
			{
				echo 1;
			}
		}
			
		// Close the application
		$app->close();
    }
		
	/**
	 * Method to get a categories select via AJAX.
	 *
	 * @access public
	 */
	public function getCategoriesSelectAjax()
	{
		JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		$app = JFactory::getApplication();
			
		$category_id = $app->input->get('category_id');
			
		$subcategories = RSDirectoryHelper::getSubcategories($category_id);
			
		echo '<div class="control-group">' . RSDirectoryHelper::getCategoriesSelect($subcategories) . '</div>';
			
		// Close the application
		$app->close();
	}
}