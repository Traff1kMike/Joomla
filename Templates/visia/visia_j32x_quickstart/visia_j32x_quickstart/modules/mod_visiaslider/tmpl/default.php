<?php
/**
	 * @package   Visia Image Slider
	 * @version   1.0
	 * @author    Erwin Schro (http://www.joomla-labs.com)
	 * @author	  Based on BxSlider jQuery plugin script
	 * @copyright Copyright (C) 2013 J!Labs. All rights reserved.
	 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 *
	 * @copyright Joomla is Copyright (C) 2005-2013 Open Source Matters. All rights reserved.
	 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
	 */
	 

defined('_JEXEC') or die('Restricted access');

$doc 	= JFactory::getDocument();

$mode				= $params->get( 'mode', 0);
$modbase 		= JURI::base(true) .'/modules/mod_visiaslider'; /* juri::base(true) will not added full path and slash at the path end */

$doc->addStyleSheet($modbase . '/assets/css/style.css');
$doc->addScript($modbase . '/assets/js/jquery.bxslider.js');
		
if ($mode==0) {
$js = '
//BLOG SLIDER
jQuery(document).ready(function(){
	
	jQuery(".gallery").bxSlider({
		pager: false,
		nextSelector: ".gallery-next",
		prevSelector: ".gallery-prev",
		nextText: "next",
		prevText: "prev"
	});	
});	
';
} else {
$js = '';
}

$doc->addScriptDeclaration($js);

?>

<?php if ($mode==1) { ?>

<script type="text/javascript">
jQuery(document).ready(function(){
jQuery('.slider').bxSlider({
	mode: 'horizontal',
	touchEnabled: true,
	swipeThreshold: 50,
	oneToOneTouch: true,
	pagerSelector: '.slider-pager',
	nextSelector: '.project-gallery-next',
	prevSelector: '.project-gallery-prev',
	nextText: 'next',
	prevText: 'prev',
	tickerHover: true
});
});
</script>
<div class="project-hero grid-full">

	<!-- Slider -->
	<ul class="slider<?php /*echo $module->id;*/ ?> clearfix">
		<?php foreach ($items as $index=>$item) {  ?>
		<li>
			<img src="<?php echo $item->img; ?>" alt="image slider item">
		</li>
		<?php } ?>
	</ul>

	<!-- Pager -->
	<div class="slider-pager"></div>
	<div class="small-border"></div>

	<!-- Prev/Next -->
	<div class="project-gallery-next"></div>
	<div class="project-gallery-prev"></div>

</div>

<?php } else { ?>

<div class="post-media">
	<ul class="gallery clearfix">
		<?php foreach ($items as $index=>$item) {  ?>
		<li><img src="<?php echo $item->img; ?>" alt="gallery item"></li>
		<?php } ?>	
	</ul>
	<div class="gallery-next"></div>
	<div class="gallery-prev"></div>
</div>
<?php } ?>