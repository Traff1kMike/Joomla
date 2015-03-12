<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * The RSDirectory controller.
 */
class RSDirectoryController extends JControllerLegacy
{
    /**
     * The class constructor.
     *
     * @access public
     *
     * @param array $config
     */
    public function __construct( $config = array() )
    {
        parent::__construct($config);
    }
}