<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Radius Search view.
 */
class RSDirectoryViewRadius extends JViewLegacy
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
		$doc = JFactory::getDocument();
			
		if ( !$app->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway = JFactory::getApplication()->getPathway();
			$pathway->addItem( JText::_('COM_RSDIRECTORY_RADIUS_SEARCH') );
		}
			
		$state = $this->get('State');
			
			
		// Get the page/component configuration.
		$params = $state->params;
			
		$doc->addScript('https://maps.google.com/maps/api/js?sensor=false&libraries=geometry');
		$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.radius-search.js?v=' . RSDirectoryVersion::$version );
			
		$script = 'jQuery(function($)
		{
			jQuery( document.getElementById("rsdir-map-canvas") ).rsRadiusSearch(
			{
				address: ' . json_encode( $params->get('default_location') ) . ',
				form: document.getElementById("rsdir-radius-search"),
				zoom: ' . (int)$params->get('default_zoom') . ',
				infoWindowWidth: ' . (int)$params->get('info_window_width', 300) . ',
				infoWindowHeight: ' . (int)$params->get('info_window_height', 120) .  ',
			});
		});';
			
		// Add the script declaration.
		$doc->addScriptDeclaration($script);
			
		if ( $params->get('filtering') )
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/filter.php';
				
			$this->fields = RSDirectoryHelper::getFilterFields( $params->get('featured_categories') );
		}
			
		if ( $params->get('show_ratings') )
		{
			$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );	
		}
			
		if ( $params->get('show_favorites_button') )
		{
			JText::script('COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES');
			JText::script('COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES');	
		}
			
		$this->width = $params->get('width', '100%');
			
		if ( is_numeric($this->width) )
		{
			$this->width .= 'px';
		}
			
		$this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );
		$this->params = $params;
		$this->options = array(
			'more' => $params->get('more'),
		);
			
		if ( $params->get('more') )
		{
			JText::script('COM_RSDIRECTORY_SHOW_MORE');
			JText::script('COM_RSDIRECTORY_SHOW_LESS');
		}
			
		JText::script('COM_RSDIRECTORY_LOADING');
			
		parent::display($tpl);
    }
}