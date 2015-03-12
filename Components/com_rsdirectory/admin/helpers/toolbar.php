<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Toolbar helper.
 */
abstract class RSDirectoryToolbarHelper
{
    /**
     * Is Joomla 3.0?
     *
     * @var boolean
     * 
     * @access public
     * 
     * @static
     */
    public static $isJ30 = null;
        
    /**
     * Add toolbar.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $view
     */
    public static function addToolbar($view = '')
    {
        // Load language file (.sys because the toolbar has the same options as the components dropdown).
        JFactory::getLanguage()->load('com_rsdirectory.sys', JPATH_ADMINISTRATOR);
            
        // Add toolbar entries
        self::addEntry('DASHBOARD', 'index.php?option=com_rsdirectory', $view == 'dashboard');
        self::addEntry('ENTRIES', 'index.php?option=com_rsdirectory&view=entries', $view == 'entries');
        self::addEntry('REPORTED_ENTRIES', 'index.php?option=com_rsdirectory&view=reportedentries', $view == 'reportedentries');
        self::addEntry('RATINGS_AND_REVIEWS', 'index.php?option=com_rsdirectory&view=ratings', $view == 'ratings');
        self::addEntry('USERS', 'index.php?option=com_rsdirectory&view=users', $view == 'users');
        self::addEntry('TRANSACTIONS', 'index.php?option=com_rsdirectory&view=transactions', $view == 'transactions');
        self::addEntry('CREDITS_HISTORY', 'index.php?option=com_rsdirectory&view=creditshistory', $view == 'creditshistory');
        self::addEntry('FIELDS', 'index.php?option=com_rsdirectory&view=fields', $view == 'fields');
        self::addEntry('FORMS', 'index.php?option=com_rsdirectory&view=forms', $view == 'forms');
        self::addEntry('CATEGORIES', 'index.php?option=com_rsdirectory&view=categories', $view == 'categories');
        self::addEntry('GROUPS', 'index.php?option=com_rsdirectory&view=groups', $view == 'groups');
        self::addEntry('CREDIT_PACKAGES', 'index.php?option=com_rsdirectory&view=creditpackages', $view == 'creditpackages');
        self::addEntry('EMAIL_MESSAGES', 'index.php?option=com_rsdirectory&view=emailmessages', $view == 'emailmessages');
        self::addEntry('CONFIGURATION', 'index.php?option=com_rsdirectory&view=configuration', $view == 'configuration');
        self::addEntry('TOOLS', 'index.php?option=com_rsdirectory&view=tools', $view == 'tools');
        self::addEntry('UPDATES', 'index.php?option=com_rsdirectory&view=updates', $view == 'updates');
    }
        
    /**
     * Add toolbar entry.
     *
     * @access protected
     * 
     * @static
     */
    protected static function addEntry($lang_key, $url, $default = false)
    {
        $lang_key = "COM_RSDIRECTORY_$lang_key";
            
        if (self::$isJ30)
        {
            JHtmlSidebar::addEntry( JText::_($lang_key), JRoute::_($url), $default );
        }
        else
        {
            JSubMenuHelper::addEntry( JText::_($lang_key), JRoute::_($url), $default );
        }
    }
        
    /**
     * Add filter.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $text
     * @param string $key
     * @param string $options
     */
    public static function addFilter($text, $key, $options)
    {
        if (self::$isJ30)
        {
            JHtmlSidebar::addFilter($text, $key, $options);
        }
            
        // Nothing for 2.5.
    }
        
    /**
     * Render the siderbar.
     *
     * @access public
     * 
     * @static
     * 
     * @return string
     */
    public static function render()
    {
        if (self::$isJ30)
        {
            return JHtmlSidebar::render();
        }
            
        return '';
    }
}

$jversion = new JVersion();
RSDirectoryToolbarHelper::$isJ30 = $jversion->isCompatible('3.0');