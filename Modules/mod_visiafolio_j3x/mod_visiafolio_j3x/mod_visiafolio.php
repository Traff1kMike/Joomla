<?php
/**
 * @version		$Id: mod_visiafolio.php 2.5.x
 * @package		Joomla 2.5.x
 * @subpackage	mod_visiafolio
 * Joomlified and crafted by Erwin Schro from www.joomla-labs.com
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// no direct access
defined('_JEXEC') or die;

$doc			= JFactory::getDocument();
$modbase		= ''.JURI::base(true).'/modules/mod_visiafolio/';
$modid			= $module->id;

$catids			= $params->get('catid');
//$column		= (int) $params->get('column', 2);

$show_filter    = $params->get('show_filter', 'debug');
$show_title	= (bool) $params->get('show_title');
$show_category	= (bool) $params->get('show_category', 1);
$show_introtext	= (bool) $params->get('show_introtext', 1);
$show_date	= (bool) $params->get('show_date');
$show_readmore	= (bool) $params->get('show_readmore', 1);

$thumb_width	= (int) $params->get('thumb_width', 461);
$thumb_height	= (int) $params->get('thumb_height', 461);
$thumb_option	= $params->get('thumb_option', 'crop');

$loadjQuery		= (int) $params->get('loadjQuery', 0);

//$tagline		= $params->get('tagline', '');
//$desc			= $params->get('desc', '');
//if ($tagline!='') $show_tagline = true;
//if ($desc!='') $show_desc = true;

// Include the syndicate functions only once
require_once dirname(__FILE__). '/helper.php';


// add stylesheet here
if ( $params->get('layout', 'default') == '_:default' ) {
	$doc->addStyleSheet($modbase.'assets/css/style.css');
} elseif ( $params->get('layout', 'default') == '_:miniart' ) {
	$doc->addStyleSheet($modbase.'assets/css/reuze.css');
	//IE hack
	$iecss = '<!--[if IE 8]>' ."\n";
	$iecss .= '<link rel="stylesheet" href="'.$modbase.'assets/css/ie8.css" media="screen" />' ."\n";
	$iecss .= '<![endif]-->' ."\n";
	$doc->addCustomTag($iecss);
}

// add javascripts here
// call jQuery library first
JHtml::_('jquery.framework');


if ( $params->get('layout', 'default') == '_:default' ) {
	$doc->addScript($modbase.'assets/js/jquery.mixitup.js');
	$doc->addScript($modbase.'assets/js/visiafolio.js');
}

// Get article items
$items 	= modVisiaFolioHelper::getList($params, $modid);
$cats 	= modVisiaFolioHelper::getCategoryTitles($catids);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_visiafolio', $params->get('layout', 'default'));
