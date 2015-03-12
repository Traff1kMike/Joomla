<?php
/**
 * @version		$Id: offline.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();

$templateparams	=  JFactory::getApplication()->getTemplate(true)->params;

//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo JURI::base(true)."/templates/".$this->template; ?>/stylesheets/reset.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo JURI::base(true)."/templates/".$this->template; ?>/stylesheets/grid.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo JURI::base(true)."/templates/".$this->template; ?>/stylesheets/style.css" type="text/css" />
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" type="text/css"  />
	<style>
	.parallax-bg3 { background-image: url( <?php echo $templateparams->get('parallax_bg_3'); ?>); }
	#contact-form { background: transparent; }
	</style>	

	
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
				<img src="<?php echo $templateparams->get('logo'); ?>" alt="<?php echo $templateparams->get('brandname').' logo'; ?>" width="91" height="30" /></a>
		</div>
	</nav>
	<!-- End Navigation -->	

	<!-- Begin Hero -->
	<section class="parallax-bg3 hero parallax dark clearfix">

		<!-- Content -->
		<div class="content container">
			<div class="ticker">
				<?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
				<h1><?php echo $app->getCfg('offline_message'); ?></h1>
				<?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
				<h1><?php echo JText::_('JOFFLINE_MESSAGE'); ?></h1>
				<?php  endif; ?>
				<h1><?php echo htmlspecialchars($app->getCfg('sitename')); ?></h1>
			</div>
			<jdoc:include type="message" />
		</div>

		<div class="dark clearfix" id="contact-form" style="display: block;">

			<form class="container" action="index.php" method="post" name="login" id="form-login">
			<fieldset>
				<div class="form-field grid-half" id="form-login-username">
					<label for="username"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
					<span><input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" /></span>
				</div>
				<div class="form-field grid-half" id="form-login-password">
					<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
					<span><input type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" /></span>
				</div>
				<!--
				<div class="form-field grid-full" id="form-login-remember">
					<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
					<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
				</div>
				-->
				<div class="form-click grid-full">
					<span><input type="submit" name="Submit" class="button offline" value="<?php echo JText::_('JLOGIN') ?>" /></span>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</fieldset>
			</form>

		</div>

	</section>
	<!-- End section -->
	
	<footer id="footer" class="clearfix">
		<div class="container">

			<!-- Copyright -->
			<div class="copyright grid-full">
				<h6><?php echo $templateparams->get('copyright'); ?></h6>
			</div>
			<!-- End Copyright -->
		</div>
	</footer>

<script charset="utf-8" type="text/javascript" src="<?php echo JURI::base(true)."/templates/".$this->template; ?>/javascripts/parallax.js"></script>
<script charset="utf-8" type="text/javascript" src="<?php echo JURI::base(true)."/templates/".$this->template; ?>/javascripts/jquery.fittext.js"></script>

</body>
</html>
