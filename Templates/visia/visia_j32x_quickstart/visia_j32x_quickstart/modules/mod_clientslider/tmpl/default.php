<?php
/**
	 * @package   Client Testimonials Slider
	 * @version   1.0
	 * @author    Erwin Schro (http://www.joomla-labs.com)
	 * @author	  Based on testimonial slider in Visia html template
	 * @copyright Copyright (C) 2013 J!Labs. All rights reserved.
	 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 *
	 * @copyright Joomla is Copyright (C) 2005-2013 Open Source Matters. All rights reserved.
	 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 only
	 */
	 

defined('_JEXEC') or die('Restricted access');

$inlinejs = "
jQuery(document).ready(function(){
//QUOTES
	jQuery('.bxslider').bxSlider({
		mode: 'fade',
		touchEnabled: true,
		oneToOneTouch: true,
		pagerCustom: '#bx-pager',
		nextSelector: '#bx-next',
  		prevSelector: '#bx-prev',
		nextText: 'next',
		prevText: 'prev'
	});
});
";
$doc->addScriptDeclaration($inlinejs);
?>

<!-- Begin Slider -->
	<!-- Logos -->
	<ul id="bx-pager" class="animated fade grid-full" data-appear-bottom-offset="150">
		<?php 
		$n = 0;
		foreach($items as $item) { ?>
			<li><a data-slide-index="<?php echo $n; ?>" href=""><img src="<?php echo $item->logoimg; ?>" alt="logo" width="142" height="95"></a></li>
		<?php $n++; } ?>
	</ul>

	<div id="bx-prev"></div>
	<div id="bx-next"></div>

	<!-- Testimonials -->
	<div class="grid-full">
		<ul class="bxslider">
			<?php foreach($items as $item) { ?>
			<li>
				<h3>&#8220;<?php echo $item->ctext; ?>&#8221;</h3>
				<h6><?php echo $item->cname; ?></h6>
			</li>
			<?php } ?>
		</ul>
	</div>
