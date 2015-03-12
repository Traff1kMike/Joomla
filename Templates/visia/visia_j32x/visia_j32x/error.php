<?php
/**
 * @version		$Id: error.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!isset($this->error)) {
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false; 
}

$templateparams	=  JFactory::getApplication()->getTemplate(true)->params;

//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
<?php if ($this->error->getCode()>=400 && $this->error->getCode() < 500) { 	?>

	<link rel="stylesheet" href="<?php echo JURI::base(true)."/templates/".$this->template; ?>/stylesheets/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo JURI::base(true)."/templates/".$this->template; ?>/stylesheets/grid.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo JURI::base(true)."/templates/".$this->template; ?>/stylesheets/style.css" type="text/css" />
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" type="text/css"  />
	<style>.parallax-bg1 { background-image: url( <?php echo JURI::base().$templateparams->get('parallax_bg_1'); ?>); }</style>

	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<!--[if lt IE 9]>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/stylesheets/ie.css" />
	<![endif]-->

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript">
/* =Document Ready Trigger
-------------------------------------------------------------- */
jQuery(document).ready(function(){

	function initializeVisia() {
		"use strict";

		//IE9 RECOGNITION
		if (jQuery.browser.msie && jQuery.browser.version == 9) {
			jQuery('html').addClass('ie9');
		}
		//RESPONSIVE HEADINGS
		jQuery("h1").fitText(1.8, { minFontSize: '30px', maxFontSize: '52px' });

		//HERO DIMENSTION AND CENTER
		(function() {
		    function heroInit(){
		       var hero = jQuery('.hero'),
					ww = jQuery(window).width(),
					wh = jQuery(window).height(),
					heroHeight = wh;

				hero.css({
					height: heroHeight+"px",
				});

				var heroContent = jQuery('.hero .content'),
					contentHeight = heroContent.height(),
					parentHeight = hero.height(),
					topMargin = (parentHeight - contentHeight) / 2;

				heroContent.css({
					"margin-top" : topMargin+"px"
				});
		    }

		    jQuery(window).on("resize", heroInit);
		    jQuery(document).on("ready", heroInit);
		})();

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

	}

	initializeVisia();
	//initializePortfolio();

});
/* END ------------------------------------------------------- */
	</script>
</head>

<body>

	<!-- Begin Navigation -->
	<nav class="clearfix">
		<!-- Logo -->
		<div class="logo">
			<a href="<?php echo $this->baseurl.'/'; ?>">
				<img src="<?php echo JURI::base().$templateparams->get('logo'); ?>" alt="<?php echo $templateparams->get('brandname').' logo'; ?>" width="91" height="30" /></a>
		</div>
	</nav>
	<!-- End Navigation -->	


	
	<!-- Begin Hero -->
	<section id="section1" class="parallax-bg1 hero parallax dark clearfix">

		<!-- Content -->
		<div class="content container">
			<div class="ticker">
				<h1><?php echo $this->error->getCode(); ?></h1>
				<h1><?php echo $this->error->getMessage(); ?></h1>
			</div>
			<ul class="call-to-action">
				<li>
					<a class="button" href="<?php echo $this->baseurl; ?>" 
						title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>"><?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a>
				</li>
			</ul>

			<p><?php if ($this->debug) : echo $this->renderBacktrace(); endif; ?></p>
		</div>

	</section>
	<!-- End Hero -->

	<footer id="footer" class="clearfix">
		<div class="container">

			<!-- Copyright -->
			<div class="copyright grid-full">
				<h6><?php echo $templateparams->get('copyright'); ?></h6>
			</div>
			<!-- End Copyright -->
		</div>
	</footer>


<script charset="utf-8" type="text/javascript" src="<?php echo JURI::base(true)."/templates/".$this->template; ?>/javascripts/waypoints.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo JURI::base(true)."/templates/".$this->template; ?>/javascripts/parallax.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo JURI::base(true)."/templates/".$this->template; ?>/javascripts/jquery.fittext.js"></script>
</body>
</html>
<?php } /* end if error 404 or 500 */ ?>
