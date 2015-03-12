<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');


if ( version_compare(JVERSION, '3.0', '>=') ) 
{
    // Joomla! 3.0
}
else if ( version_compare(JVERSION, '2.5.0', '>=') ) 
{
    // Joomla! 2.5
    jimport('joomla.application.component.model');
	jimport('joomla.application.component.modelform');
	jimport('joomla.application.component.modellist');
	jimport('joomla.application.component.modeladmin');
	jimport('joomla.application.component.modelitem');
	jimport('joomla.application.component.view');
	jimport('joomla.application.component.controller');
	jimport('joomla.application.component.controlleradmin');
	jimport('joomla.application.component.controllerform');
        
        
    if ( !class_exists('JModelLegacy') )
    {
        class JModelLegacy extends JModel
        { 
            public static function addIncludePath($path = '', $prefix = '')
            {
                return parent::addIncludePath($path, $prefix);
            }
        }
    }
        
    if ( !class_exists('JViewLegacy') )
    {
        class JViewLegacy extends JView
        {
        }
    }
        
    if ( !class_exists('JControllerLegacy') )
    {
        class JControllerLegacy extends JController
        {
        }
    }
}