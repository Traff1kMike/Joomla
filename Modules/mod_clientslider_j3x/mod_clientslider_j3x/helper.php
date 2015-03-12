<?php
/*
 * @package     Module for Joomla!
 * @subpackage  mod_clientslider
 * @copyright   Copyright (C) 2013 j!Labs and AetherThemes. All rights reserved.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modClientSliderHelper
{
	public static function getItems( &$params )
	{
		$numitems = $params->get( 'num_of_items');;
		$items = array();

		for( $n=1; $n <= $numitems; $n++ )
		{
			$logoimg	= $params->get( 'logo_img_'.$n );
			$ctext 		= $params->get( 'ctext_'.$n );
			$cname 		= $params->get( 'cname_'.$n );
			$curl 		= $params->get( 'curl_'.$n );
			
			if ( $params->get('layout', 'default') == '_:ticker') {
				$slogan = new JObject;
				$slogan->ctext = $ctext;
				$items[] = $slogan;

			} else {

				if( !empty( $logoimg ) && !empty( $cname ) && !empty( $ctext ) )
				{
					$testi = new JObject;
					$testi->logoimg = $logoimg;
					$testi->ctext = $ctext;
					$testi->cname = $cname;
					$testi->curl = $curl;

					$items[] = $testi;
				}
			}
		}
		
		return $items;
	}

}

