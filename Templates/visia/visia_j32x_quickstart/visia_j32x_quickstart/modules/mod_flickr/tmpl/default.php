<?php
/**
	 * @package   Visia Image Slider
	 * @version   1.0
	 * @author    Erwin Schro (http://www.joomla-labs.com)
	 * @author	  Based on BxSlider jQuery plugin script
	 * @copyright Copyright (C) 2012 J!Labs. All rights reserved.
	 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 *
	 * Image Slider module has not been developed under the terms of the GPL 
	 * @copyright Joomla is Copyright (C) 2005 Open Source Matters. All rights reserved.
	 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
	 */
	 

defined('_JEXEC') or die('Restricted access');

$doc 	= JFactory::getDocument();

$modbase = JURI::base(true) .'/modules/mod_flickr';

$doc->addStyleSheet($modbase . '/assets/style.css');
$doc->addScript($modbase . '/assets/jquery.flickr.js');
		

$js = "
jQuery(document).ready(function() {
	jQuery('.flickrs').jflickrfeed({
		limit: ".$count.",
		qstrings: {
			id: '".$flickr_id."' // Set your flickr ID here
		},
		itemTemplate: 
		'<li>' +
		'<a class=\'screen-roll\' href=\'{{image_b}}\'><span></span><img src=\'{{image_s}}\' alt=\'{{title}}\' /></a>' +
		'</li>'
	});
});
";

$doc->addScriptDeclaration($js);

?>

<ul class="flickrs clearfix"></ul>