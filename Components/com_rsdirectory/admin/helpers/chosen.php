<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

abstract class RSChosen
{
    /**
     * Array containing information for loaded files.
     *
     * @var array
     */
    protected static $loaded = array();
        
    /**
     * Method to load the Chosen JavaScript framework and supporting CSS into the document head
     *
     * @param string $selector Class for Chosen elements.
     * @param array $options An options array.
     * @param mixed $debug Is debugging mode on? [optional]
     *
     * @return void
     */
    public static function create( $selector = '.advandedSelect', $options = array(), $debug = null )
    {
        if ( isset(self::$loaded[__METHOD__][$selector]) )
            return;
            
        $doc = JFactory::getDocument();
            
        $options = array_merge(
            array(
                'disable_search_threshold' => 10,
                'allow_single_deselect' => true,
            ),
            $options
        );
            
        // Add chosen.jquery.js language strings
        JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
        JText::script('JGLOBAL_SELECT_AN_OPTION');
        JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
            
        // If no debugging value is set, use the configuration setting
        if ($debug === null)
        {
            $config = JFactory::getConfig();
            $debug = (boolean) $config->get('debug');
        }
            
        // Include jQuery
        if ( RSDirectoryHelper::isJ30() )
        {
            JHtml::_('jquery.framework');
            JHtml::_('script', 'jui/chosen.jquery.min.js', false, true, false, false, $debug);
            JHtml::_('stylesheet', 'jui/chosen.css', false, true);
        }
        else
        {
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery-1.7.2.min.js?v=' . RSDirectoryVersion::$version );
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/chosen.jquery.min.js?v=' . RSDirectoryVersion::$version );
            $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/chosen.css?v=' . RSDirectoryVersion::$version );
        }
            
        $options_string = '';
            
        foreach ($options as $key => $value)
        {
            $options_string .= "$key: $value,";
        }
            
        $doc->addScriptDeclaration("
            jQuery(function($)
            {
                $('" . $selector . "').chosen(" . ($options_string ? '{' . $options_string . '}' : '') . ");
            });
        ");
            
        self::$loaded[__METHOD__][$selector] = true;
    }
}