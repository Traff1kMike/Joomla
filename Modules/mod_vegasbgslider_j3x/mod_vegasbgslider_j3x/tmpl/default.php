<?php
/**
	 * @package   Fullscreen Background Image
	 * @version   1.0
	 * @author    Erwin Schro (http://www.joomla-labs.com)
	 * @author	  Based on Vargas and Supersized Fullscreen Background Image scripts
	 * @copyright Copyright (C) 2012 J!Labs. All rights reserved.
	 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 *
	 * Fullpage Background Image module has been developed and distributed under the terms of the GPL 
	 * @copyright Joomla is Copyright (C) 2005 Open Source Matters. All rights reserved.
	 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
	 */
	 

defined('_JEXEC') or die('Restricted access');

$doc 	= JFactory::getDocument();
$is_slider 			= $params->get( 'is_slider' );
$bgimg1 			= $params->get( 'bgimg_1' );
$bgimg2 			= $params->get( 'bgimg_2' );
$bgimg3 			= $params->get( 'bgimg_3' );
$overlaysimg 		= $params->get( 'overlays' );
$delay				= $params->get( 'delay', 6000 );
$fade				= $params->get( 'fade', 2500 );
$base_url 			= JURI::base(true); /* will not added slash at the path end */
$modbase 			= $base_url .'/modules/mod_vegasbgslider';

$doc->addStyleSheet($modbase . '/assets/css/vegas.css');
$doc->addScript($modbase . '/assets/js/vegas.js');

if ($bgimg1) {
	$bg = "{ src:'$base_url/$bgimg1', fade:2500 }";
}
if ($bgimg2) {
	$bg .= ",{ src:'$base_url/$bgimg2', fade:2500 }";
}
if ($bgimg3) {
	$bg .= ",{ src:'$base_url/$bgimg3', fade:2500 }";
}


$js = "
jQuery( function($) {
";

$js .= "
jQuery.vegas('slideshow', {
	backgrounds:[
		".$bg."
	]
	})
;
";

if ( $overlaysimg != -1) {
$js .= "
jQuery.vegas('overlay', {
	src:'$modbase/assets/images/overlays/$overlaysimg'
});
";
}

$js .= "});"; // end jquery function

$js .= "
	//FULLSCREEN SLIDER CONTROLS
	jQuery(document).ready(function(){
		jQuery('#vegas-next').click(function(){
			jQuery.vegas('next');
			return false;
		});
		jQuery('#vegas-prev').click(function(){
			jQuery.vegas('previous');
			return false;
		});
	});
";
	

$nosliderjs = "
jQuery( function($) {
	$.vegas( {
		src: '$base_url/$bgimg1'
	})
";
		
if ($overlaysimg!=-1) {
$nosliderjs .= "
	('overlay', {
		src: '$modbase/assets/images/overlays/$overlaysimg'
	});
";
}

$nosliderjs .= "
}); // closing jquery function
";


if ($is_slider) {
	$doc->addScriptDeclaration($js);
} else {
	$doc->addScriptDeclaration($nosliderjs);
}


if ($is_slider) { ?>
<!-- Slider Controls -->
<ul class="slider-controls">
	<li><a id="vegas-next" href="#">Next</a></li>
	<li><a id="vegas-prev" href="#">Prev</a></li>
</ul>
<?php } ?>
