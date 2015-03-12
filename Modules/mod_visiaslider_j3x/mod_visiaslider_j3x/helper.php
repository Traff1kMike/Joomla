<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modVisiaSliderHelper
{
	public static function getItems( &$params )
	{
		$numbox = 3;
		$items = array();

		for( $n=1; $n <= $numbox; $n++ )
		{
			
			$img 		= $params->get( 'img_'.$n );
			//$title 		= $params->get( 'imgtitle_'.$n );
			//$desc 		= $params->get( 'imgdesc_'.$n );
			//$url 		= $params->get( 'url_' . $n );
			
			if( !empty( $img ) /*&& !empty( $title ) && !empty( $desc )*/ )
			{
				$slide = new JObject;
				$slide->img = $img;
				//$slide->title = $title;
				//$slide->desc = $desc;
				//$slide->url = $url;

				$items[] = $slide;
			}
		}
		
		return $items;
	}
}
