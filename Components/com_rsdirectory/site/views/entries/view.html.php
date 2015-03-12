<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entries view.
 */
class RSDirectoryViewEntries extends JViewLegacy
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
			
		if ( !$app->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway = JFactory::getApplication()->getPathway();
			$pathway->addItem( JText::_('COM_RSDIRECTORY_ENTRIES_VIEW_DEFAULT_TITLE') );
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
		$this->view = $app->input->get('view');
			
		$config = RSDirectoryConfig::getInstance();
			
		// Get the date and time display setting.
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
			
		// Add feed links
		if ( $params->get('show_feed_link', 1) )
		{
			$link = '&format=feed&limitstart=';
				
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink( JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs );
				
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink( JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs );
		}
			
		$this->_prepareDocument();
			
		parent::display($tpl);
    }
		
	/**
	 * Prepares the document.
	 *
	 * @access protected
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
			
		// Because the application sets a default page title, we need to get it from the menu item itself.
		$menu = $menus->getActive();
			
		// Get the categories filter.
		$categories = $app->input->get( 'categories', array(), 'array' );
			
		if ( isset($categories[0]) && !isset($categories[1]) )
		{
			$category = RSDirectoryHelper::getCategory($categories[0]);	
		}
			
		// Get the users filter.
		$users = $app->input->get( 'users', array(), 'array' );
			
		if ( isset($users[0]) && !isset($users[1]) )
		{
			$user = JFactory::getUser($users[0]);
		}
			
		if ( !empty($category) && $category->id != 'root' )
		{
			$page_heading = $category->title;
			$title = $category->title;
			$metadesc = $category->metadesc;
			$metakey = $category->metakey;
				
			$registry = new JRegistry($category->metadata);
			$metadata = $registry->toArray();
		}
		else
		{
			if ( empty($user->id) )
			{
				$page_heading = $this->params->get( 'page_title', empty($menu) ? JText::_('COM_RSDIRECTORY_ENTRIES_VIEW_DEFAULT_TITLE') : $menu->title );	
			}
			else
			{
				$page_heading = JText::sprintf( 'COM_RSDIRECTORY_ENTRIES_POSTED_BY', $this->escape($user->name) );
			}
				
			$title = $this->params->get('page_title', '');
			$metadesc = $this->params->get('menu-meta_description');
			$metakey = $this->params->get('menu-meta_keywords');
				
			$metadata = array(
				'robots' => $this->params->get('robots'),
			);
		}
			
		$this->params->set('page_heading', $page_heading);
			
		// Set the browser title.
		$sitename = $app->getCfg('sitename');
		$sitename_pagetitles = $app->getCfg('sitename_pagetitles', 0);
			
		if ( empty($title) )
		{
			$title = $sitename;
		}
		else if ($sitename_pagetitles == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $sitename, $title);
		}
		else if ($sitename_pagetitles == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $sitename);
		}
			
		$this->document->setTitle($title);
			
		// Set the meta description.
		if ($metadesc)
		{
			$metadesc = str_replace( array("\r\n", "\n"), ' ', $metadesc );
				
			$this->document->setDescription($metadesc);
		}
			
		// Set the meta keywords.
		if ($metakey)
		{
			$this->document->setMetadata('keywords', $metakey);
		}
			
		// Set the robots tag.
		if ( !empty($metadata['robots']) )
		{
			$this->document->setMetadata('robots', $metadata['robots']);
		}
	}
}