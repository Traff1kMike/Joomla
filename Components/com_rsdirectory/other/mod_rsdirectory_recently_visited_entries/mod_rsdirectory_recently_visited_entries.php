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

// Get the recently visited entries.
$entries = RSDirectoryRecentlyVisitedEntriesHelper::getEntries($params);

if (!$entries)
    return;

$config = RSDirectoryConfig::getInstance();

// Get the thumbnail width.
$width = $config->get('small_thumbnail_width');

// Get the thumbnail height.
$height = $config->get('small_thumbnail_height');

// Load language.
$lang = JFactory::getLanguage();
$lang->load('com_rsdirectory');

// Get params.
$itemid = $params->get('itemid');
$display = $params->get('display');
$display_thumbs = $params->get('display_thumbs');
$thumb_max_width = (int)$params->get('thumb_max_width');
$thumb_position = $params->get('thumb_position', 'left');
$display_titles = $params->get('display_titles');
$display_prices = $params->get('display_prices');
$display_ratings = $params->get('display_ratings');

$max_entries = $params->get('max_entries', 3);
	
// Ensure that max_entries is a factor of 12.
if (12 % $max_entries != 0)
{
	$max_entries = 3;	
}

$span = 12 / $max_entries;

$doc = JFactory::getDocument();

RSDirectoryHelper::loadMedia();

$doc->addStyleSheet( JURI::root(true) . '/media/mod_rsdirectory_recently_visited_entries/css/style.css?v=' . MOD_RSDIRECTORY_RECENTLY_VISITED_ENTRIES_VERSION );

if ($display_ratings)
{
	$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );
	$doc->addScript( JURI::root(true) . '/media/mod_rsdirectory_recently_visited_entries/js/script.js?v=' . MOD_RSDIRECTORY_RECENTLY_VISITED_ENTRIES_VERSION );
}


include JModuleHelper::getLayoutPath('mod_rsdirectory_recently_visited_entries', 'default');