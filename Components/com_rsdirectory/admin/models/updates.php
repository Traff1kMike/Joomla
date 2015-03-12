<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Updates model.
 */
class RSDirectoryModelUpdates extends JModelList
{
	/**
	 * Get hash.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function getHash()
	{
		return hash( 'md5', RSDirectoryConfig::getInstance()->get('code') . RSDirectoryVersion::$key );
	}
		
	/**
	 * Get the Joomla! short version.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function getJoomlaVersion()
	{
		$jversion = new JVersion();
		return $jversion->getShortVersion();
	}
		
	/**
     * Get sidebar.
     *
     * @access public
     * 
     * @return string
     */
    public function getSideBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
            
        return RSDirectoryToolbarHelper::render();
    }
}