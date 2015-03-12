<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Model helper.
 */
class RSDirectoryModel extends JModelLegacy
{
	/**
	 * Returns a Model object, always creating it.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param string $type The model type to instantiate
	 * @param string $prefix Prefix for the model class name. Optional.
	 * @param array $config Configuration array for model. Optional.
	 *
	 * @return mixed A model object or false on failure
	 */
	public static function getInstance( $type, $prefix = 'RSDirectoryModel', $config = array() )
	{
		return parent::getInstance($type, $prefix, $config);
	}
}