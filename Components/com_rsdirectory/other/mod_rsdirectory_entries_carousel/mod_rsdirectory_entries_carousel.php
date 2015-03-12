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

// Get entries.
$entries = RSDirectoryCarouselHelper::getEntries($params);

if (!$entries)
    return;

$config = RSDirectoryConfig::getInstance();

// Get the thumbnail width.
$width = $config->get('big_thumbnail_width');

// Get the thumbnail height.
$height = $config->get('big_thumbnail_height');

// Load language.
$lang = JFactory::getLanguage();
$lang->load('com_rsdirectory');

// Get params.
$itemid = $params->get('itemid');
$entries_per_slide = (int)$params->get('entries_per_slide', 3);
$span = 12 / $entries_per_slide;
$display_thumbs = $params->get('display_thumbs', 3);
$thumb_max_width = (int)$params->get('thumb_max_width');
$display_titles = $params->get('display_titles');
$display_prices = $params->get('display_prices');
$display_ratings = $params->get('display_ratings');
$interval = (int)$params->get('interval', 5000);
$display_indicators = $params->get('display_indicators', 1);
$display_nav = $params->get('display_nav', 1);

RSDirectoryHelper::loadMedia();

$doc = JFactory::getDocument();

$doc->addStyleSheet( JUri::root(true) . '/media/mod_rsdirectory_entries_carousel/css/style.css?v=' . MOD_RSDIRECTORY_ENTRIES_CAROUSEL_VERSION );

if ($display_ratings)
{
	$doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );
}

$doc->addScript( JURI::root(true) . '/media/mod_rsdirectory_entries_carousel/js/script.js?v=' . MOD_RSDIRECTORY_ENTRIES_CAROUSEL_VERSION );

// Generate carousel ID.
$carousel_id = 'carousel-' . RSDirectoryHelper::randStr();

// Initialize the carousel.
$script = 'jQuery(function($){$( document.getElementById("' . $carousel_id . '") ).carousel({interval: ' . $interval . '});});';

$doc->addScriptDeclaration($script);


$slides = RSDirectoryCarouselHelper::getEntriesPerSlide($entries, $entries_per_slide);


include JModuleHelper::getLayoutPath('mod_rsdirectory_entries_carousel', 'default');