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
require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/filter.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/chosen.php';
require_once dirname(__FILE__) . '/helper.php';

// Get mainframe.
$app = JFactory::getApplication();

// Get the JInput object.
$jinput = $app->input;

$doc = JFactory::getDocument();

$menu = $app->getMenu();
$active = $menu->getActive();

// Restrict the module only to the entries page.
if ( $jinput->get('view') != 'entries' )
    return;

// Load language.
$lang = JFactory::getLanguage();
$lang->load('com_rsdirectory');

// Get the custom itemid.
$itemid = $params->get('itemid');


$doc->addStyleSheet( JUri::root(true) . '/media/mod_rsdirectory_filtering/css/style.css?v=' . MOD_RSDIRECTORY_FILTERING_VERSION );
$doc->addScript( JUri::root(true) . '/media/mod_rsdirectory_filtering/js/script.js?v=' . MOD_RSDIRECTORY_FILTERING_VERSION );

if ( $params->get('show_categories') )
{
    // Get categories.
    $categories_list = RSDirectoryHelper::getSubcategories(0);
}

// Initialize the featured categories array.
$featured_categories = array();
    
// Get categories filter.
list($categories) = $jinput->get( 'categories', array(''), 'array' );
    
if ( trim($categories) !== '')
{
    $featured_categories = RSDirectoryHelper::arrayInt( explode(',', $categories) );
}

$menuitem_params = $app->getParams();

if ( !$featured_categories && $menuitem_params->get('featured_categories') )
{
    foreach ( $menuitem_params->get('featured_categories') as $category )
    {
		// Reset the featured categories array if the category is 0 or '' ("All categories" is selected ) as this will make the getFilterFields to use all the available categories.
		if (!$category)
		{
			$featured_categories = array();
			break;
		}
			
		$featured_categories[] = $category;
    }
}

$status = $jinput->get( 'status', array(0, 1), 'array' );


$fields = RSDirectoryHelper::getFilterFields($featured_categories);
$options = array(
	'more' => $params->get('more'),
);

if ( $params->get('more') )
{
	JText::script('COM_RSDIRECTORY_SHOW_MORE');
	JText::script('COM_RSDIRECTORY_SHOW_LESS');
}

include JModuleHelper::getLayoutPath('mod_rsdirectory_filtering', 'default');