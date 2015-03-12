<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Email Messages controller.
 */
class RSDirectoryControllerEmailMessages extends JControllerAdmin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @access public
     * 
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     */
    public function getModel( $name = 'EmailMessage', $prefix = 'RSDirectoryModel', $config = array('ignore_request' => true) )
    {
        return parent::getModel($name, $prefix, $config);
    }
}