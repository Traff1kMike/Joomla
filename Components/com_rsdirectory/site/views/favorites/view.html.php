<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Favorites view.
 */
class RSDirectoryViewFavorites extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
		$app = JFactory::getApplication();
			
		// Only logged in users can access this page.
		if ( !JFactory::getUser()->id )
		{
			return $app->enqueueMessage( JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST') );
		}
			
		if ( !$app->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway = $app->getPathway();
			$pathway->addItem( JText::_('COM_RSDIRECTORY_FAVORITES') );
		}
			
		$doc = JFactory::getDocument();
			
		$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );
		$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/script.js?v=' . RSDirectoryVersion::$version );
			
		$state = $this->get('State');
			
		// Get the page/component configuration.
		$params = $state->params;
			
		$this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->sort_field = $this->get('SortField');
		$this->sort_dir_field = $this->get('SortDirField');
		$this->params = $params;
			
		// Get the view.
		$this->view = JFactory::getApplication()->input->get('view');
			
		$config = RSDirectoryConfig::getInstance();
			
		$this->date_and_time_display = $config->get('date_and_time_display');
		$this->width = $config->get('small_thumbnail_width');
		$this->height = $config->get('small_thumbnail_height');
		$this->images_listing_detail_position = $config->get('images_listing_detail_position');
		
		// Get permissions.
		$this->can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
		$this->can_edit_own_entries = RSDirectoryHelper::checkUserPermission('can_edit_own_entries');
		$this->can_delete_all_entries = RSDirectoryHelper::checkUserPermission('can_delete_all_entries');
        $this->can_delete_own_entries = RSDirectoryHelper::checkUserPermission('can_delete_own_entries');
			
		$this->user = JFactory::getUser();
			
		JText::script('COM_RSDIRECTORY_ENTRY_DELETION_CONFIRMATION');
		JText::script('COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES');
		JText::script('COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES');
			
		parent::display($tpl);
    }
}