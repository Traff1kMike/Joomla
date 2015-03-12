<?php
/**
* @version		$Id: mod_countdown.php 10000 2013-10-29 03:35:53Z schro $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/// no direct access
defined('_JEXEC') or die('Restricted access');

$doc		= JFactory::getDocument();
$modulebase 	= ''.JURI::base().'modules/mod_countdown/';

$show_pretext	= $params->get('show_pretext', 1);
$pretext		= $params->get('pretext');
$lang_code		= $params->get('lang_code'); 
$cdowntime		= $params->get('launchtime', '');
$region_code	= $params->get('region', 'fr');


$splitDate 	= explode('-', $cdowntime);
$month 		= $splitDate[0];
$day 		= $splitDate[1];
$year 		= $splitDate[2];


// add javascripts
JHtml::_('jquery.framework');

$doc->addScript($modulebase.'assets/js/jquery.countdown.min.js');
if($lang_code !='none'){
	$doc->addScript($modulebase.'assets/js/language/jquery.countdown-'.$lang_code.'.js');
}

$js = "
jQuery(function () {
	
	jQuery('#vcountdown".$module->id."').countdown({
		until: new Date(".$year.",".($month)."-1,".$day."),
		format: 'WHS', /* WHS WdH */
		layout: '<div class=\"milestone grid-2\"><span class=\"value\">{wn}</span><h4>{wl}</h4></div>' +
				'<div class=\"milestone grid-2\"><span class=\"value\">{hn}</span><h4>{hl}</h4></div>' +
				'<div class=\"milestone grid-2\"><span class=\"value\">{sn}</span><h4>{sl}</h4></div>'
	});
	
}); 
";
$doc->addScriptDeclaration($js);

$doc->addStylesheet($modulebase.'assets/css/style.css');

require( JModuleHelper::getLayoutPath('mod_countdown', $params->get('layout', 'default')) );
