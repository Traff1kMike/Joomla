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

//$doc 	= JFactory::getDocument();
//$modbase 		= JURI::base(true) .'/modules/mod_clientslider';

// add inline javascript if any			
$inlinejs = "
jQuery(document).ready(function(){
	//HERO TICKER
	var current = 1; 
	var height = jQuery('.ticker').height(); 
	var numberDivs = jQuery('.ticker').children().length; 
	var first = jQuery('.ticker h1:nth-child(1)'); 
	setInterval(function() {
	    var number = current * -height;
	    first.css('margin-top', number + 'px');
	    if (current === numberDivs) {
	        first.css('margin-top', '0px');
	        current = 1;
	    } else current++;
	}, 2500);
});
";
$doc->addScriptDeclaration($inlinejs);

?>

<!-- Begin Ticker Slider -->
	
	
	<div class="ticker">
		
		<?php foreach($items as $item) { ?>
		
			<h1 style="font-size: 52px;">
				<?php echo $item->ctext; ?>
			</h1>
				
		<?php } ?>
		
	</div>
