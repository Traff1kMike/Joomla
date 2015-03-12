<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * RSDirectory! Version class.
 */
abstract class RSDirectoryVersion
{
	/**
	 * Product name.
	 *
	 * @access public
	 *
	 * @var string
	 */
	public static $product = 'RSDirectory!';
		
	/**
	 * RSDirectory! version.
	 *
	 * @access public
	 *
	 * @var string
	 */
	public static $version = '1.4.3';
		
	/**
	 * RSDirectory! key.
	 *
	 * @access public
	 *
	 * @var string
	 */
	public static $key = 'DIR54JHY12';
}