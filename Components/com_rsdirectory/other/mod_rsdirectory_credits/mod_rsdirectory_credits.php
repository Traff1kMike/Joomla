<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php';

// Load language.
$lang = JFactory::getLanguage();
$lang->load('com_rsdirectory');

// Get the custom itemid.
$itemid = $params->get('itemid');

// Set the number of credits.
$credits = RSDirectoryCredits::getUserCredits();

// Set the url of the "Buy Credits" page.
$url = RSDirectoryRoute::getURL('credits', '', $itemid ? "&Itemid=$itemid" : '');


include JModuleHelper::getLayoutPath('mod_rsdirectory_credits', 'default');