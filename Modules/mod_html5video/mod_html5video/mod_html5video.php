<?php
/**
* @version		$Id: mod_html5video.php 10000 2013-09-22 03:35:53Z schro $
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
$modbase 	= ''.JURI::base().'modules/mod_html5video/';


$video_path	= $params->get('video_path');
$video_alt_path	= $params->get('video_alt_path');
$video_poster_path = $params->get('video_poster_path');
$loadjQuery = $params->get('jquery', 0);

// load css
$doc->addStylesheet($modbase.'assets/style.css');
$doc->addStyleDeclaration("
/* Tablet Landscape */
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (orientation : landscape) {
	.hero-video { background: url(".$video_poster_path.") no-repeat; background-size: cover; background-position: top center;}
}
/* Smaller than 960px */
@media only screen and (max-width: 959px) {
	.hero-video { background: url(".$video_poster_path.") no-repeat; background-size: cover; background-position: top center;}
}
");

/* assume jquery lib already loaded - please check your template file */
JLoader::import( 'joomla.version' );
$version = new JVersion();
if (version_compare( $version->RELEASE, '2.5', '<=')) 
{
	if ($loadjQuery) {
		/*if( $loadjQuery == 1 ) {
			$doc->addScript($modulebase.'assets/js/jquery.min.js');
		}
		*/
		if( $loadjQuery == 2 ) {
			$doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
		}
	}
} else {
	JHtml::_('jquery.framework');
}

$doc->addScript($modbase.'assets/jqueryui.js');
$doc->addScript($modbase.'assets/video.js');
$doc->addScript($modbase.'assets/bigvideo.js');
$doc->addScriptDeclaration("
jQuery(document).ready(function(){
	//FULLSCREEN VIDEO
	jQuery(function() {
	    var BV = new jQuery.BigVideo({useFlashForFirefox:false});
	    BV.init();
	    BV.show('".$video_path."',{altSource:'".$video_alt_path."', ambient:true});
	});
});
");

require( JModuleHelper::getLayoutPath('mod_html5video', $params->get('layout', 'default')) );
