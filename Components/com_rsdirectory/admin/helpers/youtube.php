<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * RSDirectory! Youtube embed code generator helper.
 */
class RSDirectoryYoutube
{
	/**
	 * The Youtube field properties.
	 *
	 * @access private
	 *
	 * @var JRegistry
	 */
	private $properties;
		
	/**
	 * The Youtube video url.
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $url;
		
	/**
     * The class constructor.
     *
     * @access public
     *
     * @param JRegistry $properties The Youtube field properties.
     * @param string $url The Youtube video url.
     */
	public function __construct($properties, $url)
	{
		$this->properties = empty($properties) ? new JRegistry : $properties;
		$this->url = $url;
	}
		
	/**
	 * Generate the embed code.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function generate()
	{
		$properties = $this->properties;
			
		// Get the video id.	
		$uri = new JURI($this->url);
		$id = $uri->getHost() == 'youtu.be' ? substr( $uri->getPath(), 1 ) : $uri->getVar('v');
			
		if ( $properties->get('video_size') == 'custom' )
		{
			$width = (int)$properties->get('video_width');
			$height = (int)$properties->get('video_height');
		}
		else
		{
			list($width, $height) = explode( 'x', $properties->get('video_size') );
				
			$width = (int)$width;
			$height = (int)$height;
		}
			
		if ( $properties->get('privacy_enhanced_mode') )
		{
			$url = '//www.youtube-nocookie.com/';
		}
		else
		{
			$url = '//www.youtube.com/';
		}
			
		if ( $properties->get('use_old_embed_code') )
		{
			$url .= "v/$id?version=3&amp;hl=en_US";
				
			if ( !$properties->get('show_suggested_videos') )
			{
				$url .= '&rel=0';
			}
				
			return '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' . $url . '"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="' . $url . '" type="application/x-shockwave-flash" width="' . $width . '" height="' . $height . '" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
			
		}
		else
		{
			$url .= "embed/$id";
				
			if ( !$properties->get('show_suggested_videos') )
			{
				$url .= '?rel=0';
			}
				
			return '<iframe width="' . $width . '" height="' . $height . '" src="' . $url . '" frameborder="0" allowfullscreen></iframe>';
		}
	}
}