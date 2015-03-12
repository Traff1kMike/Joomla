<?php

/**
 * @package     Template for Joomla!
 * @subpackage  Visia Template
 * @copyright   Copyright (C) 2013 J!Labs & AetherThemes. All rights reserved.
 * @license     GNU/GPL v3 or later
 * @link        http://www.joomla-labs.com
 */

 // no direct access
defined('_JEXEC') or die;

class TplVisiaHelper {

    static function initializeTemplate($document) {

    	$site_url = $document->baseurl .'/';
        $base_url = $document->baseurl . '/templates/' . $document->template;
		
		$brandname = 'Visia'; $slogan = 'Site Slogan Here';
		$copyrighttext = '&copy;2013 Visia. All Rights Reserved.';
		
        // default styles
        $default_styles = array(
            'logo' => '',
			'brandname' => $brandname,
			'slogan' => $slogan,
			'copyright' => $copyrighttext,
			'google_font' => 1, /* Google Font enabled */
			'google_font_body' => 'Open+Sans:400,300', /* choose Google font here, use comma separated value if more than one */
			'google_font_body_style' => 'Open Sans', /* specify the font family here, use comma separated if more than one */
			'analytics' => '',
			'googlemeta' => '', 
			'show_component' => 0,
			'hide_menu_itemids' => '',
			'color_theme' => 'ruby-red',
			'jquery' => 0,
            'enable_loader' => 2,
            'is_slider' => 0,
            'enable_parallax_bg1' => 0,
            'parallax_bg_1' => '',
            'parallax_bg_2' => '',
            'parallax_bg_3' => '',
            'parallax_bg_4' => ''
		
        );
		
		$tpl_options = new stdClass();

		$tpl_options->templatepath = $base_url;
        
		// get template parameters
        foreach ($default_styles as $option => $value) {
            $tpl_options->$option = $value;
            if ($document->params->get($option, $value) != -1) {
                $tpl_options->$option = $document->params->get($option, $value);
            }
        }

        // load CSS files
        
        // Call Google Font
        if ( $tpl_options->google_font && $tpl_options->google_font_body && $tpl_options->google_font_body_style) {
            $document->addStyleSheet('http://fonts.googleapis.com/css?family=' . $tpl_options->google_font_body);
            $document->addStyleDeclaration("body{font-family: $tpl_options->google_font_body_style, Helvetica, Arial, sans-serif;}");
        }


        $menu = JFactory::getApplication()->getMenu();
        

        $document->addStyleSheet($base_url . '/stylesheets/reset.css');
        //$document->addStyleSheet($base_url . '/stylesheets/joomla.css');
        
        $document->addStyleSheet($base_url . '/stylesheets/shortcodes.css');
        $document->addStyleSheet($base_url . '/stylesheets/grid.css');
        $document->addStyleSheet($base_url . '/stylesheets/style.css');
        		
		if( $tpl_options->color_theme ) {
		    $document->addStyleSheet($base_url . '/stylesheets/colors/' . $tpl_options->color_theme . '.css');
		}
        
		$nheroticker = $document->countModules('hero') + $document->countModules('ticker');

        // Get parallax image background 1 if set up
        if ($tpl_options->enable_parallax_bg1 && $tpl_options->parallax_bg_1) {
        	$parallax_bg1_path = $site_url.$tpl_options->parallax_bg_1;
        	$style_parbg1 = ".parallax-bg1 { background-image: url(".$parallax_bg1_path."); }";
        	$tpl_options->cls_parallaxbg1 = 'parallax-bg1 parallax ';
        	$tpl_options->cls_extra = 'dark';
    	} elseif( !$tpl_options->enable_parallax_bg1 && ($nheroticker) ) {
    		$style_parbg1 = ''; 
    		if ($tpl_options->is_slider) {
				$tpl_options->cls_parallaxbg1 = 'dark ';
			} else {
				$tpl_options->cls_parallaxbg1 = 'darkbg dark ';
			}
    		$tpl_options->cls_extra = 'dark';
    	} elseif(!$tpl_options->enable_parallax_bg1 && !$nheroticker ) { 
    		$style_parbg1 = ''; 
    		$tpl_options->cls_parallaxbg1 = '';
        	$tpl_options->cls_extra = '';
    	}

		if ($tpl_options->parallax_bg_3) {
			$tpl_options->cls_parallaxbg3 = 'parallax-bg3 parallax ';
        	$tpl_options->cls_extra = 'dark';
		} else {
			$tpl_options->cls_parallaxbg3 = 'darkbg ';
			$tpl_options->cls_extra = 'dark';
		}
		
		// add animation stylesheet
		$document->addStyleSheet($base_url . '/stylesheets/animations.css');
		
		$isLoader = false;
    	// set parallax background image path if selected
		if ($tpl_options->enable_parallax_bg1 && $tpl_options->parallax_bg_1) {
			$parallax_bg1_path = $site_url.$tpl_options->parallax_bg_1;
			// set parallax style definitions
			$style_parbg1 = '.parallax-bg1 { background-image: url('.$parallax_bg1_path.'); }';
			$isLoader = true;
		} else {
			$style_parbg1 = '';
		}
		
        if ($tpl_options->parallax_bg_2) {
			$parallax_bg2_path = $site_url.$tpl_options->parallax_bg_2;
			// set parallax style definitions
			$style_parbg2 = '.parallax-bg2 { background-image: url('.$parallax_bg2_path.'); }';
			$isLoader = true;
		} else {
			$style_parbg2 = '';
		}
        if ($tpl_options->parallax_bg_3) {
			$parallax_bg3_path = $site_url.$tpl_options->parallax_bg_3;
			// set parallax style definitions
			$style_parbg3 = '.parallax-bg3 { background-image: url('.$parallax_bg3_path.'); }';
			$isLoader = true;
		} else {
			$style_parbg3 = '';
		}
        if ($tpl_options->parallax_bg_4) {
			$parallax_bg4_path = $site_url.$tpl_options->parallax_bg_4;
			// set parallax style definitions
			$style_parbg4 = '.parallax-bg4 { background-image: url('.$parallax_bg4_path.'); }';
			$isLoader = true;
		} else {
			$style_parbg4 = '';
		}

		// Page loader options
		if( $tpl_options->enable_loader && $isLoader) {
			// on home page only
			$document->addStyleSheet($base_url . '/stylesheets/loader.css');
		} else {
			$document->addStyleDeclaration("body.royal_loader {visibility: visible;}");
		}
        
        // add parallax background inline styles
        $document->addStyleDeclaration("
        $style_parbg1
		$style_parbg2
		$style_parbg3
		$style_parbg4
        ");
        
		
		// load Javascripts
		// jquery library first
        JHtml::_('jquery.framework');
		
		// declare images object for preloader script
		$document->addScriptDeclaration('var images = {};');
		
		
        // set up javascripts to be called right before the body tag close
        $tpl_options->footerscripts = '';

        if ( $tpl_options->enable_loader && $isLoader )
		{
            // add loader script
			$tpl_options->footerscripts  .= '
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/royal-preloader.js"></script>
			';
			
			$tpl_options->footerscripts  .= '
			<script type="text/javascript">
			';

			if ($tpl_options->enable_parallax_bg1 && ($tpl_options->parallax_bg_1 != '') ) {
				$tpl_options->footerscripts .= '
images["parallax1"] = "'.$parallax_bg1_path.'";
				';
			}
			if ($tpl_options->parallax_bg_2 != '') {
				$tpl_options->footerscripts .= '
images["parallax2"] = "'.$parallax_bg2_path.'";
				';
			}
			if ($tpl_options->parallax_bg_3 != '') {
				$tpl_options->footerscripts .= '
images["parallax3"] = "'.$parallax_bg3_path.'";
				';
			}
			if($tpl_options->parallax_bg_4 != '') {
				$tpl_options->footerscripts .= '
images["parallax4"] = "'.$parallax_bg4_path.'";
				';
			}


			$tpl_options->footerscripts  .= '

// config
Royal_Preloader.config({
mode:       "number",
images:     images,
timeout:    10,
showInfo:   true,
background: ["#ffffff"]
});
</script>
			';
			
		} // end add loader script
		

		$tpl_options->footerscripts  .= '
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/smoothscroll.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/waypoints.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/parallax.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/navigation.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.easing.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.fittext.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.localscroll.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.scrollto.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.appear.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.waitforimages.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/jquery.fitvids.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/main.js"></script>
			<script charset="utf-8" type="text/javascript" src="'. $base_url . '/javascripts/shortcodes.js"></script>
			
		';

						
	    /* Generator and author metadatas, edit these here */
	    $document->setMetaData('generator', ''); 
	    $document->setMetaData('author', 'schro');

	    // mobile stuffs
	    //$document->setMetaData('X-UA-Compatible', 'IE=edge,chrome=1', true);
	    $document->setMetaData('viewport', 'width=device-width, initial-scale=1, maximum-scale=1');
	    $document->addHeadLink($base_url . '/images/apple-touch-icon.png', 'apple-touch-icon');
	    $document->addHeadLink($base_url . '/images/apple-touch-icon-72x72.png', 'apple-touch-icon', 'rel', array('sizes' => '72x72'));
	    $document->addHeadLink($base_url . '/images/apple-touch-icon-114x114.png', 'apple-touch-icon', 'rel', array('sizes' => '114x114'));

        
		/* Grid and section specific class definitions based on module published */
		$nheroticker = $document->countModules('hero') + $document->countModules('ticker');
		$nportfolio = $document->countModules('portfolio');
		$ncontop = $document->countModules('content-top');
		
		/* top-a top-b top-c grid class calculation */
		
		$ntopa = $document->countModules('top-a'); $ntopb = $document->countModules('top-b'); $ntopc = $document->countModules('top-c'); 

		if ($ntopa>=1) $ntopa = 1;else $ntopa=0;
		if ($ntopb>=1) $ntopb = 1;else $ntopb=0;
		if ($ntopc>=1) $ntopc = 1;else $ntopc=0;
		$ntopabc = $ntopa + $ntopb + $ntopc;

		if ( $ntopabc == 3 ) {
		    $tpl_options->gridtopabc = 'grid-2';
		} elseif ( $ntopabc == 2 ) {
		    $tpl_options->gridtopabc = 'grid-3';
		} else {
		    $tpl_options->gridtopabc = 'grid-6';
		}
		

		/* user2, 3, 4 grid class calculation and definition */
		$nu1 = $document->countModules('user1'); $nu2 = $document->countModules('user2');
		$nu3 = $document->countModules('user3'); $nu4 = $document->countModules('user4');
		$nu234 = $nu2+$nu3+$nu4; 
		
		if ($nheroticker + $nportfolio + $ntopabc + $ncontop + $nu234 === 0) { $tpl_options->blog_class = 'padded'; }
		else { $tpl_options->blog_class = 'no-padded no-margin-top'; }

		if ($nu2>=1) $nu2 = 1; else $nu2 = 0;
		if ($nu3>=1) $nu3 = 1; else $nu3 = 0;
		if ($nu4>=1) $nu4 = 1; else $nu4 = 0;
		if ( $nu2 + $nu3 + $nu4 == 3 ) {
		    $tpl_options->gridu234 = 'grid-2';
		} elseif ( $nu2 + $nu3 + $nu4 == 2 ) {
		    $tpl_options->gridu234 = 'grid-3';
		} else {
		    $tpl_options->gridu234 = 'grid-6';
		}

		/* main content and sidebar grid calculation and definition */
		$nsbr = $document->countModules('sidebar-r');
		if ($nsbr>=1) $nsbr = 1; else $nsbr=0;

		if ( $nsbr == 1 ) {
			$tpl_options->clsgrid_content = 'grid-4';
			$tpl_options->clsgrid_sidebar = 'grid-2';
		} else {
			$tpl_options->clsgrid_content = 'grid-6';
			$tpl_options->clsgrid_sidebar = '';
		}

		/* content bottom section */
		$ncbota = $document->countModules('content-bottom-a'); $ncbotb = $document->countModules('content-bottom-b'); $ncbotc = $document->countModules('content-bottom-c'); 
		if ($ncbota>=1) $ncbota = 1;else $ncbota=0;
		if ($ncbotb>=1) $ncbotb = 1;else $ncbotb=0;
		if ($ncbotc>=1) $ncbotc = 1;else $ncbotc=0;
		
		if ( $ncbota + $ncbotb + $ncbotc == 3 ) {
			$tpl_options->grid_contentbotabc = 'grid-2';
		} elseif ( $ncbota + $ncbotb + $ncbotc == 2 ) {
			$tpl_options->grid_contentbotabc = 'grid-3';
		} else {
			$tpl_options->grid_contentbotabc = 'grid-6';
		}

		/* bottom section */
		$nbota = $document->countModules('bottom-a'); $nbotb = $document->countModules('bottom-b'); $nbotc = $document->countModules('bottom-c'); 
		if ($nbota>=1) $nbota = 1;else $nbota=0;
		if ($nbotb>=1) $nbotb = 1;else $nbotb=0;
		if ($nbotc>=1) $nbotc = 1;else $nbotc=0;
		
		if ( $nbota + $nbotb + $nbotc == 3 ) {
			$tpl_options->grid_bottomabc = 'grid-2';
		} elseif ( $nbota + $nbotb + $nbotc == 2 ) {
			$tpl_options->grid_bottomabc = 'grid-3';
		} else {
			$tpl_options->grid_bottomabc = 'grid-6';
		}

		/* Footer grid width class*/
		$nfoota = $document->countModules('footer-a'); $nfootb = $document->countModules('footer-b'); $nfootc = $document->countModules('footer-c'); 
		if ($nfoota>=1) $nfoota = 1;else $nfoota=0;
		if ($nfootb>=1) $nfootb = 1;else $nfootb=0;
		if ($nfootc>=1) $nfootc = 1;else $nfootc=0;
		
		if ( $nfoota + $nfootb + $nfootc == 3 ) {
			$tpl_options->gridfooterabcd = 'grid-2';
		} elseif ( $nfoota + $nfootb + $nfootc == 2 ) {
			$tpl_options->gridfooterabcd = 'grid-3';
		} else {
			$tpl_options->gridfooterabcd = 'grid-6';
		}		

        return $tpl_options;
    }

}