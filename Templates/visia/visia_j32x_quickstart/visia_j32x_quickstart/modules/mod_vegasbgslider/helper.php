<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modVegasBgSliderHelper
{
	public static function getItems( &$params )
	{
		$numbox = 4;
		$items = array();

		for( $n=1; $n <= $numbox; $n++ )
		{
			
			$bgimg 		= $params->get( 'bgimg_'.$n );
			$title 		= $params->get( 'imgtitle_'.$n );
			$desc 		= $params->get( 'imgdesc_'.$n );
			$url 		= $params->get( 'url_' . $n );
			
			if( !empty( $bgimg ) && !empty( $title ) && !empty( $desc ) )
			{
				$slide = new JObject;
				$slide->bgimg = $bgimg;
				$slide->title = $title;
				$slide->desc = $desc;
				$slide->url = $url;

				$items[] = $slide;
			}
		}
		
		return $items;
	}
}
