<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ( !file_exists(JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php') )
    return;

require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php';
require_once dirname(__FILE__) . '/helper.php';

// Get the JInput object.
$jinput = JFactory::getApplication()->input;

// Load language.
$lang = JFactory::getLanguage();
$lang->load('com_rsdirectory');

$doc = JFactory::getDocument();

RSDirectoryHelper::loadMedia();

$doc->addStyleSheet( JUri::root(true) . '/media/mod_rsdirectory_simple_search/css/style.css?v=' . MOD_RSDIRECTORY_SIMPLE_SEARCH_VERSION );

if ( $params->get('show_categories') )
{
    $doc->addScript( JUri::root(true) . '/media/mod_rsdirectory_simple_search/js/script.js?v=' . MOD_RSDIRECTORY_SIMPLE_SEARCH_VERSION );
        
    // Get categories.
    $categories_list = RSDirectoryHelper::getSubcategories(0);
        
    // Get the selected categories.
    list($categories) = $jinput->get( 'categories', array(''), 'array' );
        
    $selected_category_text = JText::_('MOD_RSDIRECTORY_SIMPLE_SEARCH_ALL_CATEGORIES');
    $selected_category_value = 0;
        
    if ($categories !== '')
    {
		$categories = explode(',', $categories);
			
		if ( !isset($categories[1]) )
		{
			list($category_id) = $categories;
				
			RSDirectoryHelper::findCategory($category_id, $categories_list, $category);
				
			if ($category)
			{
				$selected_category_text = RSDirectoryHelper::escapeHTML($category->title);
				$selected_category_value = $category_id;
			}
		}
    }
}

// Get the custom itemid.
$itemid = $params->get('itemid');

// Get the search query.
list($q) = $jinput->get( 'q', array(''), 'array' );

// Sanitize the search query.
$q = urldecode($q);
$q = htmlspecialchars($q);

include JModuleHelper::getLayoutPath('mod_rsdirectory_simple_search', 'default');