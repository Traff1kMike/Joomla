<?php
/**
 * @version	$Id: index.php 17268 2013-09-13 20:32:21Z schro $
 * @package	Joomla.Site
 * @subpackage	tpl_visia
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');

// initialize template
require dirname(__FILE__) . DS . 'helper.php';
$tpl_options = TplVisiaHelper::initializeTemplate($this);

//unset($doc->_scripts[$this->baseurl . '/media/system/js/mootools-core.js']);
//unset($doc->_scripts[$this->baseurl . '/media/system/js/core.js']);
//unset($doc->_scripts[$this->baseurl . '/media/system/js/caption.js']);

?>

<!DOCTYPE html>
<head>

		
	<jdoc:include type="head" />
	
	
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Retina Images -->
	<script>if((window.devicePixelRatio===undefined?1:window.devicePixelRatio)>1)
		document.cookie='HTTP_IS_RETINA=1;path=/';</script>
	<!-- End Retina Images -->
	
	<!-- Google site verification script here -->
	<?php if ($tpl_options->googlemeta):?>
	<meta content="<?php echo $tpl_options->googlemeta; ?>" name="google-site-verification">
	<?php endif; ?>	

	<!-- Google Analytic script here -->
	<?php if ($tpl_options->analytics):?>
	<script type="text/javascript">

              var _gaq = _gaq || [];
              _gaq.push(['_setAccount', '<?php echo $tpl_options->analytics;?>']);
              _gaq.push(['_trackPageview']);

              (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
              })();

	</script>
	<?php endif; ?>

	<!--[if lt IE 9]>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/stylesheets/ie.css" />
	<![endif]-->

</head>

<body class="<?php if ($tpl_options->enable_loader) echo 'royal_loader'; ?>">

	<!-- Begin Navigation -->
	<nav class="clearfix">
		<a id="top" href="#"></a>
		<!-- Logo -->
		<div class="logo">
			<a href="<?php echo $this->baseurl.'/'; ?>">
				<img src="<?php echo $tpl_options->logo; ?>" alt="<?php echo $tpl_options->brandname.' logo'; ?>" width="91" height="30" /></a>
		</div>

		<!-- Mobile Nav Button -->
		<button type="button" class="nav-button" data-toggle="collapse" data-target=".nav-content">
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	    </button>

	    <!-- Navigation Links -->
	    <div class="navigation">
			<!-- Navigation bar for main menu module  -->
			<jdoc:include type="modules" name="menu" style="raw" />
			<!-- end menu module -->
		</div>

	</nav>
	<!-- End Navigation -->	


	<?php if ( $this->countModules('ticker') || $this->countModules('hero') ) { ?>	
	<!-- Begin Ticker and Hero section -->
	<section id="section1" class="<?php echo $tpl_options->cls_parallaxbg1; ?>hero <?php /*echo $tpl_options->cls_extra;*/ ?> clearfix">

		
		<!-- Container start -->
		<div class="content container <?php echo $tpl_options->cls_extra; ?>">

			<?php if ($this->countModules('ticker')) { ?>
			<!-- Ticker module position -->
			<jdoc:include type="modules" name="ticker" style="raw" />
			<?php } ?>

			<!-- hero module position -->
		 	<?php if ($this->countModules('hero')) { ?>
		 	<jdoc:include type="modules" name="hero" style="raw" />
		 	<?php } ?>

		</div>
		<!-- container end -->

	</section>
	<!-- End Ticker and Hero -->
	<?php } ?>
	

	<?php if ($this->countModules('top-a') || $this->countModules('top-b')) { ?>
	
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>

	<!-- Begin About Section -->
	<section id="section2" class="content container">
		
		<!-- top-a and top-b module positions -->
		<?php if ($this->countModules('top-a') ) { ?>
			<!-- About your company or you short description text here -->
			<jdoc:include type="modules" name="top-a" style="headsubhead" />
		<?php } ?>

		<?php if ($this->countModules('top-b') ) { ?>
			<!-- About Image like in the demo page example -->
			<jdoc:include type="modules" name="top-b" style="raw" />
		<?php } ?>

	</section>
	<!-- End About section -->

	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>


	<!-- Begin Services section -->
	<?php if ( $this->countModules('user1') || $this->countModules('user2') || $this->countModules('user3') || $this->countModules('user4') || $this->countModules('breadcrumb') ) { ?>
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>
	<section id="section3" class="parallax-bg2 parallax colored clearfix">
		
		<?php if ( $this->countModules('user1') || $this->countModules('user2') || $this->countModules('user3') || $this->countModules('user4') ) { ?>
		<!-- Content start -->
		<div class="content dark padded container">

			<!-- call module published in user1 position -->
			<?php if ($this->countModules('user1')) { ?>
			<jdoc:include type="modules" name="user1" style="headsubhead" />
			<?php } ?>
		
			<!-- call module published in user2 position -->
			<?php if ($this->countModules('user2')) { ?>
			<div class="feature <?php echo $tpl_options->gridu234; ?>">		
				<jdoc:include type="modules" name="user2" style="raw" />
			</div>
			<?php } ?>

			<!-- call module published in user3 position -->
			<?php if ($this->countModules('user3')) { ?>
			<div class="feature <?php echo $tpl_options->gridu234; ?>">		
				<jdoc:include type="modules" name="user3" style="raw" />
			</div>
			<?php } ?>

			<!-- call module published in user4 position -->
			<?php if ($this->countModules('user4')) { ?>
			<div class="feature <?php echo $tpl_options->gridu234; ?>">		
				<jdoc:include type="modules" name="user4" style="raw" />
			</div>
			<?php } ?>
					
			
		</div><!-- End content container -->
		<?php } ?>
		
		<!-- call module published in breadcrumb position -->
		<?php if ($this->countModules('breadcrumb')) { ?>
		<div class="content dark medium-padded container">
			<jdoc:include type="modules" name="breadcrumb" style="bottom" />
		</div>
		<?php } ?>

	</section>
	<!-- End Services section -->	
	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>


	<!-- Begin Portfolio section  -->
	<?php if ( $this->countModules('content-top') || $this->countModules('portfolio') ) { ?>
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>
	<section id="section4" class="portfolio clearfix">
		
		<!-- call module published in portfolio position here -->
		<?php if ( $this->countModules('portfolio') ) { ?>
			<jdoc:include type="modules" name="portfolio" style="raw" />
		<?php } ?>

		<!-- call module published in content-top position here -->
		<?php if ( $this->countModules('content-top') ) { ?>
			<div class="content padded container">
			<jdoc:include type="modules" name="content-top" style="raw" />
			</div>
		<?php } ?>

	</section>
	
	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>
	<!-- End Portfolio section -->	

	
	
	<?php 
	if ($tpl_options->hide_menu_itemids!='') {
		$arrHiddenItemIds = explode(", ", $tpl_options->hide_menu_itemids);
		$isHidden = in_array($menu->getActive()->id, $arrHiddenItemIds);
	} else {
		$arrHiddenItemIds = null;
		$isHidden = false;
	}
	
	$menu = JFactory::getApplication()->getMenu();
	/* hide Component output at specific menu items */
	if ( ( !$tpl_options->show_component && ($menu->getActive() == $menu->getDefault()) ) ||  $isHidden ) {
		echo '';
	} else { 
	?>
	
	<!-- begin Main blog/content section -->
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>
	<section id="blog" class="content <?php echo $tpl_options->blog_class; ?> container">
					
		<?php if ($this->countModules('content-above')) { ?>			
		<div class="grid-6">
			<jdoc:include type="modules" name="content-above" style="raw" />
		</div>
		<?php } ?>

			
		<?php 
		$menu = JFactory::getApplication()->getMenu();
			
		if ( !$tpl_options->show_component && ($menu->getActive() == $menu->getDefault()) ) {
			echo '';
		} else { 
			?>

			<!-- Component section -->
			<div class="<?php echo $tpl_options->clsgrid_content; ?>">
				<!-- component start here -->
				<jdoc:include type="message" />
				<jdoc:include type="component" />
			</div>
			<!-- end component section -->
			<?php } ?>

			<!-- Call modules published in sidebar-r position here -->
			<?php if ($this->countModules('sidebar-r')) { ?>
			<div class="<?php echo $tpl_options->clsgrid_sidebar; ?>">
				<div class="sidebar">
				<jdoc:include type="modules" name="sidebar-r" style="sidebar" />
				</div>
			</div>
			<?php } ?>
			<!-- end sidebar-r position -->

	</section>

	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>
	<!-- end main section --> 

	<!--  -->
	<?php if ( $this->countModules('content-bottom') || $this->countModules('content-bottom-a') || $this->countModules('content-bottom-b') || $this->countModules('content-bottom-c')) { ?>
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>
	
	<!-- Begin Team section -->
	<section id="section5" class="content container">

		<div class="grid-full">
			<jdoc:include type="modules" name="content-bottom-above" style="headsubhead" />
		</div>

		<?php if ( $this->countModules('content-bottom-a') || $this->countModules('content-bottom-b') || $this->countModules('content-bottom-c')) { ?>
		<div class="grid-full clearfix">

			<?php if ( $this->countModules('content-bottom-a') ) { ?>
			<div class="<?php echo $tpl_options->grid_contentbotabc; ?>">
				<jdoc:include type="modules" name="content-bottom-a" style="raw" />
			</div>
			<?php } ?>
			<?php if ( $this->countModules('content-bottom-b') ) { ?>
			<div class="<?php echo $tpl_options->grid_contentbotabc; ?>">
				<jdoc:include type="modules" name="content-bottom-b" style="raw" />
			</div>
			<?php } ?>
			<?php if ( $this->countModules('content-bottom-c') ) { ?>
			<div class="<?php echo $tpl_options->grid_contentbotabc; ?>">
				<jdoc:include type="modules" name="content-bottom-c" style="raw" />
			</div>
			<?php } ?>

		</div>
		<?php } ?>

		<?php if ( $this->countModules('content-bottom') ) { ?>
		<div class="grid-full">
			<jdoc:include type="modules" name="content-bottom" style="headsubhead" />
		</div>
		<?php } ?>
		
	</section>
	<!-- End Team section -->

	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>


	<!-- begin bottom-above section -->
	<?php if ($this->countModules('bottom-above')) { ?>
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>
	
	<section class="parallax-bg4 parallax colored clearfix">
		<!-- start container -->
		<div class="content dark padded container">
		
			<!-- Subscribe module -->
			<jdoc:include type="modules" name="bottom-above" style="bottom" />
			<!-- Subscribe module end -->
			
		</div>
		<!-- End container -->
	</section>

	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>	
	<!-- end bottom-above section -->


	<!-- begin Bottom Section-->
	<?php if ($this->countModules('bottom')) { ?>
	<?php if ($tpl_options->is_slider) { ?><div class="slider-cover"><?php } ?>
	<section id="section7" class="<?php echo $tpl_options->cls_parallaxbg3; ?>dark clearfix">
		<!-- start container -->
		<div class="content padded container">
		
			<!-- module for client logos here -->
			<jdoc:include type="modules" name="bottom" style="bottom" />
			
			
		</div>
		<!-- end Container -->

		<?php if ( $this->countModules('bottom-a') || $this->countModules('bottom-b') || $this->countModules('bottom-c') ) { ?>
		<!-- start another container -->
		<div class="content padded container">

			<?php if ( $this->countModules('bottom-a') ) { ?>
			<div class="<?php echo $tpl_options->grid_bottomabc; ?>">
				<jdoc:include type="modules" name="bottom-a" style="raw" />
			</div>
			<?php } ?>
			<?php if ( $this->countModules('bottom-b') ) { ?>
			<div class="<?php echo $tpl_options->grid_bottomabc; ?>">
				<jdoc:include type="modules" name="bottom-b" style="raw" />
			</div>
			<?php } ?>
			<?php if ( $this->countModules('bottom-c') ) { ?>
			<div class="<?php echo $tpl_options->grid_bottomabc; ?>">
				<jdoc:include type="modules" name="bottom-c" style="raw" />
			</div>
			<?php } ?>

		</div>
		<!-- end container -->
		<?php } ?>

	</section>
	<?php if ($tpl_options->is_slider) { ?></div><?php } ?>
	<?php } ?>
	<!-- end Bottom Section-->
	
				
	<!-- begin Footer section -->
	<?php if ( $this->countModules('footer-a') || $this->countModules('footer-b') || $this->countModules('footer-c') || 
		$this->countModules('footer-d') || $tpl_options->copyright!='' ) { ?>
	<footer id="footer" class="clearfix">

		<?php if ( $this->countModules('footer-a') || $this->countModules('footer-b') || $this->countModules('footer-c') ) { ?>
		<div class="content dark container">
			<!-- About -->
			<?php if ($this->countModules('footer-a')) { ?>
			<div class="<?php echo $tpl_options->gridfooterabcd; ?> animated hatch">
				<jdoc:include type="modules" name="footer-a" style="footer" />
			</div>
			<?php } ?>

			<?php if ($this->countModules('footer-b')) { ?>
			<div class="<?php echo $tpl_options->gridfooterabcd; ?> animated hatch">
				<jdoc:include type="modules" name="footer-b" style="footer" />
			</div>
			<?php } ?>

			<?php if ($this->countModules('footer-c')) { ?>
			<div class="<?php echo $tpl_options->gridfooterabcd; ?> animated hatch">
				<jdoc:include type="modules" name="footer-c" style="footer" />
			</div>
			<?php } ?>

		</div>
		<?php } ?>

		<?php if ( $this->countModules('footer-d') ) { ?>
		<jdoc:include type="modules" name="footer-d" style="footer" />
		<?php } ?>

		<?php if ( $this->countModules('footer-e') || $tpl_options->copyright!='' ) { ?>
		<div class="container">

			<!-- Social Icon links here -->
			<?php if ($this->countModules('footer-e')) { ?>
			<div class="grid-full">
				<jdoc:include type="modules" name="footer-e" style="raw" />
			</div>
			<?php } ?>
			<!-- end social icons -->

			<!-- Copyright -->
			<div class="copyright grid-full">
				<h6><?php echo $tpl_options->copyright; ?></h6>
			</div>
			<!-- End Copyright -->
		</div>
		<?php } ?>

	</footer>
	<?php } ?>	
	<!-- end Footer section -->

<!-- Call javascripts here -->
<?php echo $tpl_options->footerscripts; ?>

<!-- call debug module -->
<jdoc:include type="modules" name="debug" />

</body>
</html>