<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories view.
 */
class RSDirectoryViewCategories extends JViewLegacy
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
		if ( !JFactory::getApplication()->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway = JFactory::getApplication()->getPathway();
			$pathway->addItem( JText::_('COM_RSDIRECTORY_CATEGORIES') );
		}
			
		$state = $this->get('State');
			
		// Get the page/component configuration.
		$params = $state->params;
			
		$config = RSDirectoryConfig::getInstance();
			
		$this->params = $params;
		$this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );
		$this->parent = $this->get('Parent');
		$this->items = $this->get('Items');
			
		$this->small_thumbnail_width = $config->get('small_thumbnail_width');
		$this->small_thumbnail_height = $config->get('small_thumbnail_height');
			
		$width = $params->get('subcategories_thumbnails_width');
		$this->width = $width ? $width : $this->small_thumbnail_width;
			
		$height = $params->get('subcategories_thumbnails_height');
		$this->height = $height ? $height : $this->small_thumbnail_height;
			
		// Override the subcategories_thumbnails_width param value.
		$this->params->set('subcategories_thumbnails_width', $this->width);
			
		// Override the subcategories_thumbnails_width param value.
		$this->params->set('subcategories_thumbnails_height', $this->height);
			
		$this->Itemid = $this->params->get('itemid');
			
		parent::display($tpl);
    }
}