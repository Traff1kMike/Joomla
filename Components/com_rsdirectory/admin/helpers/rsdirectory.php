<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 


require_once dirname(__FILE__) . '/version.php';
require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/credits.php';
require_once dirname(__FILE__) . '/model.php';
require_once dirname(__FILE__) . '/route.php';


/**
 * RSDirectory! Helper class.
 */
abstract class RSDirectoryHelper
{
    /**
     * Load the language files.
     *
     * @access public
     * 
     * @static
     */ 
    public static function loadLang()
    {
        $app  = JFactory::getApplication();                
        $lang = JFactory::getLanguage();
        $from = $app->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE;
            
        $lang->load('com_rsdirectory', $from, 'en-GB', true);
        $lang->load( 'com_rsdirectory', $from, $lang->getDefault(), true );
        $lang->load('com_rsdirectory', $from, null, true);
    }
        
    /**
     * Load media.
     *
     * @access public
     * 
     * @static
     */
    public static function loadMedia()
    {
        $doc = JFactory::getDocument();
            
        JHtml::_('behavior.modal');
            
        // Load common CSS.
        $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/common.css?v=' . RSDirectoryVersion::$version );
            
        // Load the jQuery UI CSS file.
        $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/jquery-ui-1.10.4.custom.min.css?v=' . RSDirectoryVersion::$version );
            
        // Create a new JVersion object.
        $jversion = new JVersion();
            
            
        if ( JFactory::getApplication()->isAdmin() )
        {
            // Load CSS.
            $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/admin-style.css?v=' . RSDirectoryVersion::$version );
            
            if ( $jversion->isCompatible('3.0') )
            {
                // Load jQuery.
                JHtml::_('jquery.framework');
            }
            else
            {
                // Load Joomla! 2.5 CSS.
                $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/admin-style-2.5.css?v=' . RSDirectoryVersion::$version );
                    
                // Load jQuery.
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery-1.7.2.min.js?v=' . RSDirectoryVersion::$version );
                    
                // Load boostrap.
                $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/bootstrap.rsdir.min.css?v=' . RSDirectoryVersion::$version );
                $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/bootstrap-responsive.rsdir.min.css?v=' . RSDirectoryVersion::$version );
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/bootstrap.min.js?v=' . RSDirectoryVersion::$version );
            }
                
            // Load jQuery UI.
            if ( $jversion->isCompatible('3.0.2') )
            {
                JHtml::_( 'jquery.ui', array('core', 'sortable') );
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.ui.datepicker.min.js?v=' . RSDirectoryVersion::$version );
            }
            else
            {
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery-ui-1.10.4.custom.min.js?v=' . RSDirectoryVersion::$version );
            }
                
            // Load the common js script.
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/common.js?v=' . RSDirectoryVersion::$version );
                
            // Load the admin script.
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/admin-script.js?v=' . RSDirectoryVersion::$version );
        }
        else
        {
            // Get an instance of the config object.
            $config = RSDirectoryConfig::getInstance();
                
            // Load style.
            $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/style.css?v=' . RSDirectoryVersion::$version );
                
            // Load jQuery.
            if ( $config->get('load_jquery') )
            {
                if ( $jversion->isCompatible('3.0') )
                {
                    JHtml::_('jquery.framework');
                }
                else
                {
                    $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery-1.7.2.min.js?v=' . RSDirectoryVersion::$version );
                }
            }
                
            // Load bootstrap.
            if ( $config->get('load_bootstrap') )
            {
                if ( $jversion->isCompatible('3.0') )
                {
                    JHtml::_('bootstrap.framework');
                    JHtml::_('bootstrap.loadcss');
                }
                else
                {
                    $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/bootstrap.min.css?v=' . RSDirectoryVersion::$version );
                    $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/bootstrap-responsive.min.css?v=' . RSDirectoryVersion::$version );
                    $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/bootstrap.min.js?v=' . RSDirectoryVersion::$version );
                }
            }
                
            if ( self::isJ25() )
            {
                // Load Joomla! 2.5 CSS.
                $doc->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/style-2.5.css?v=' . RSDirectoryVersion::$version );   
            }
                
                
            // Load jQuery UI.
            if ( $jversion->isCompatible('3.0.2') )
            {
                JHtml::_( 'jquery.ui', array('core', 'sortable') );
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.ui.datepicker.min.js?v=' . RSDirectoryVersion::$version );
            }
            else
            {
                $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery-ui-1.10.4.custom.min.js?v=' . RSDirectoryVersion::$version );
            }
                
            // Load the common js script.
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/common.js?v=' . RSDirectoryVersion::$version );
                
            // Load script.
            $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/script.js?v=' . RSDirectoryVersion::$version );
        }
            
        self::loadJSData();
    }
        
    /**
     * Load JS data.
     *
     * @access public
     *
     * @static
     */
    public static function loadJSData()
    {
        static $loaded;
            
        if (!$loaded)
        {
            $loaded = true;
                
            $data = array(
                'version: "' . RSDirectoryVersion::$version . '"',
                'token: "' . JSession::getFormToken() . '"',
                'root: "' . JURI::root(true) . '/"',
                'base: "' . JURI::base(true) . '/"',
                'files_limit: {}',
            );
                
            JFactory::getDocument()->addScriptDeclaration('rsdir = {' . implode(',', $data) . '};');
        }
    }
    /**
     * Load adapter.
     *
     * @access public
     * 
     * @static
     */
    public static function loadAdapter()
    {
        require_once dirname(__FILE__) . '/adapter.php';
    }
        
    /**
     * Initialize the helper.
     *
     * @access public
     * 
     * @static
     */ 
    public static function init()
    {   
        // Load the language files.
        self::loadLang();
            
        // Load media.
        self::loadMedia();
            
        // Load adapter.
        self::loadAdapter();
    }
        
    /**
     * Add the elements of an array into another array starting at the position marked by the $index parameter.
     *
     * @access public
     * 
     * @static
     * 
     * @param array $toArray
     * @param array $array
     * @param int $index
     */
    public static function addTo(&$toArray, $array, $index)
    {
        $toArray = array_slice($toArray, 0, $index) + $array + array_slice($toArray, $index);        
    }
        
    /**
     * Get an array of options used for populating a dropdown field.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $values
     * 
     * @return array
     */
    public static function getOptions($values)
    {
        $values = explode ( "\n", str_replace( array("\r\n", "\r"), "\n", $values ) );
            
        $options = array();
            
        foreach ($values as $value)
        {
            $aux = explode('|', $value);
                
            if ( !isset($aux[1]) )
            {
                $aux[1] = $aux[0];
            }
                
            $options[] = JHtml::_('select.option', $aux[0], $aux[1]);
        }
            
        return $options;
    }
        
    /**
     * Get an array of grouped options for populating a dropdown field.
     *
     * @access public
     *
     * @static
     *
     * @param array $items
     *
     * @return mixed
     */
    public static function getGroupedListOptions($items)
    {
        if (!$items)
            return;
            
        // Initialize the options array.
        $options = array();
            
        foreach ($items as $item)
        {
            if ( stripos($item->value, '[g]') !== false || stripos($item->text, '[g]') !== false )
            {
                $group = array(
                    'text' => str_replace('[g]', '', $item->text),
                    'items' => array(),
                );
            }
            else if ( isset($group) && ( stripos($item->text, '[/g]') !== false || stripos($item->text, '[/g]') !== false || end($items) == $item ) )
            {
                $options[] = $group;
                unset($group);
            }
            else
            {     
                if ( stripos($item->value, '[d]') !== false || stripos($item->text, '[d]') !== false )
                {
                    $value = str_replace('[d]', '', $item->value);
                    $text = str_replace('[d]', '', $item->text);
                        
                    $disable = true;
                }
                else
                {
                    $value = $item->value;
                    $text = $item->text;
                        
                    $disable = false;
                }
                    
                $option = JHTML::_('select.option', $value, $text, 'value', 'text', $disable);
                    
                if ( isset($group) )
                {
                    $group['items'][] = $option;
                }
                else
                {
                    $options[] = array(
                        'items' => array(
                            $option,
                        ),
                    );
                }
            }
        }
            
        return $options;
    }
        
    /**
     * Validate name.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $name
     * 
     * @return
     */
    public static function validateName($name)
    {
        return preg_match('/^([a-z0-9_])+$/i', $name);
    }
        
    /**
     * Get $val if $val is numeric or zero if it's not.
     *
     * @access public
     * 
     * @static
     * 
     * @param mixed $val
     * 
     * @return int
     */
    public static function getInt($val)
    {
        return is_numeric($val) ? $val : 0;
    }
        
    /**
     * Get params.
     *
     * @access public
     * 
     * @static
     * 
     * @return mixed
     */
    public static function getParams()
    {
        $app = JFactory::getApplication();
            
        $itemid = $app->input->getInt('Itemid', 0);
        $params = array();
            
        if ($itemid)
        {
            $menu = $app->getMenu();
                
            if (!$menu)
                return;
                
            $active = $menu->getItem($itemid);
                
            if (!$active)
                return;
                
            $params = $active->params;
        }
            
        if ($params)
        {
            $params = $app->getParams();
        }
            
        return $params;
    }
        
    /**
     * Get category hierachy.
     *
     * @access public
     * 
     * @static
     * 
     * @param int $category_id
     * @param array $categories
     * 
     * @return mixed
     */
    public static function getCategoryHierarchy($category_id, $categories)
    {
        if (!$category_id || !$categories)
            return;
            
        // Initialize the hierarchy array.
        $hierarchy = array();
            
        $i = 0;
            
        // Get the array count.
        $count = count($categories);
            
        // Get the hierarchy of the category.
        while ($i < $count)
        {
            if ($categories[$i]->id == $category_id)
            {
                $hierarchy[] = $categories[$i];
                    
                if ($categories[$i]->parent_id < 2)
                    break;
                    
                $category_id = $categories[$i]->parent_id;
                $i = 0;
            }
            else
            {
                $i++;
            }
        }
           
        return array_reverse($hierarchy);
    }
        
    /**
     * Check if all characters in a string are alphabetic or belong to the extra values.
     * 
     * @access public
     * 
     * @static
     * 
     * @param string $param
     * @param mixed $extra
     * 
     * @return bool
     */
    public static function alpha($param, $extra = null)
    {
        if ( strpos($param, "\n") !== false)
        {
            $param = str_replace( array("\r", "\n"), '', $param);
        }
            
        $strlen = strlen($param);
            
        for ($i = 0; $i < $strlen; $i++)
        {
            if ( strpos($extra, $param[$i]) === false && preg_match('#([^a-zA-Z ])#', $param[$i]) )
                return false;
        }
            
        return true;
    }
        
    /**
     * Check if all characters in a string are numeric or belong to the extra values.
     * 
     * @access public
     * 
     * @static
     * 
     * @param string $param
     * @param mixed $extra
     * 
     * @return bool
     */
    public static function numeric($param, $extra = null)
    {
        if ( strpos($param, "\n") !== false )
        {
            $param = str_replace( array("\r", "\n"), '', $param );
        }
            
        $strlen = strlen($param);
            
        for ($i = 0; $i < $strlen; $i++)
        {
            if ( strpos($extra, $param[$i]) === false && !is_numeric($param[$i]) )
                return false;
        }
            
        return true;
    }
        
    /**
     * Check if all characters in a string are alphanumeric or belong to the extra values.
     * 
     * @access public
     * 
     * @static
     * 
     * @param string $param
     * @param mixed $extra
     * 
     * @return bool
     */
    public static function alphanumeric($param, $extra = null)
    {
        if ( strpos($param, "\n") !== false)
        {
            $param = str_replace( array("\r", "\n"), '', $param);
        }
            
        $strlen = strlen($param);
            
        for ($i = 0; $i < $strlen; $i++)
        {
            if ( strpos($extra, $param[$i]) === false && !preg_match('/^[A-Za-z0-9]$/', $param[$i]) )
                return false;
        }
            
        return true;
    }
        
    /**
     * Check if the string is a valid email.
     * 
     * @access public
     * 
     * @static
     * 
     * @param string $email
     * 
     * @return bool
     */
    public static function email($email)
    {
        jimport('joomla.mail.helper');
            
        return JMailHelper::isEmailAddress( trim($email) );
    }
        
    /**
     * Check if the string is a valid email.
     * 
     * @access public
     * 
     * @static
     * 
     * @param string $email
     * 
     * @return bool
     */
    public static function emaildns($email)
    {
        // Check if it's an email address format.
        if ( !self::email($email) )
            return false;
            
        $email = trim($email);
        list($user, $domain) = explode('@', $email, 2);
            
        // checkdnsrr for PHP < 5.3.0
        if ( !function_exists('checkdnsrr') && function_exists('exec') && is_callable('exec') )
        {
            @exec( 'nslookup -type=MX ' . escapeshellcmd($domain), $output );
                
            foreach ($output as $line)
            {
                if ( preg_match('/^' . preg_quote($domain) . '/', $line) )
                    return true;
            }
                
            return false;
        }
            
        // Fallback method.
        if ( !function_exists('checkdnsrr') || !is_callable('checkdnsrr') )
            return true;
            
        return checkdnsrr($domain, substr(PHP_OS, 0, 3) == 'WIN' ? 'A' : 'MX');
    }
        
    public static function uniquefield($value, $column)
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('id') )
               ->from( $db->qn('#__rsdirectory_entries') )
               ->rightJoin( $db->qn('#__rsdirectory_entries_custom') . ' ON ' . $db->qn('id') . ' = ' . $db->qn('entry_id') )
               ->where( $db->qn($column) . ' = ' . $db->q($value) );
              
        $db->setQuery($query, 0, 1);
            
        return !$db->loadResult();
    }
        
    public static function uniquefielduser($value, $column)
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get the JUser object.
        $user = JFactory::getUser();
            
        $query = $db->getQuery(true);
            
        $query->select( $db->qn('id') )
              ->from( $db->qn('#__rsdirectory_entries') )
              ->rightJoin( $db->qn('#__rsdirectory_entries_custom') . ' ON ' . $db->qn('id') . ' = ' . $db->qn('entry_id') )
              ->where( $db->qn($column) . ' = ' . $db->q($value) );
              
        if ( $user->get('guest') )
        {
            $query->where( $db->qn('ip') . ' = ' . $db->q($_SERVER['REMOTE_ADDR']) );
        }
        else
        {
            $query->where( $db->qn('user_id') . ' = ' . $db->q( $user->get('id') ) );
        }
            
        $db->setQuery($query, 0, 1);
            
        return !$db->loadResult();
    }
        
    public static function uszipcode($value)
    {
        return preg_match("/^([0-9]{5})(-[0-9]{4})?$/i", $value);
    }
        
    public static function phonenumber($value)
    {
        return preg_match("/\(?\b[0-9]{3}\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}\b/i", $value);
    }
        
    public static function creditcard($value)
    {
        $value = preg_replace('/[^0-9]+/', '', $value);
            
        if (!$value)
            return false;
            
        if ( preg_match("/^([34|37]{2})([0-9]{13})$/", $value) ) // Amex
            return true;
            
        if ( preg_match("/^([30|36|38]{2})([0-9]{12})$/", $value) ) // Diners
            return true;
            
        if ( preg_match("/^([6011]{4})([0-9]{12})$/", $value) ) // Discover
            return true;
            
        if ( preg_match("/^([51|52|53|54|55]{2})([0-9]{14})$/", $value) ) // MasterCard
            return true;
            
        if ( preg_match("/^([4]{1})([0-9]{12,15})$/", $value) ) // Visa
            return true;
            
        return false;
    }
        
    public static function custom($param, $extra = null)
    {
        if ( strpos($param, "\n") !== false )
        {
            $param = str_replace( array("\r", "\n"), '', $param );
        }
            
        $strlen = strlen($param);
            
        for ($i = 0; $i < $strlen; $i++)
        {
            if( strpos($extra, $param[$i]) === false )
                return false;
        }
            
        return true;
    }
        
    public static function ipaddress($param)
    {
        return preg_match('#\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b#', $param);
    }
        
    public static function validurl($param)
    {
        $format = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
        
        return preg_match($format, $param);
    }
        
    public static function regex($value, $pattern = null)
    {
        return @preg_match($pattern, $value);
    }
        
    public static function getIp($check_for_proxy = false)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
            
        if ($check_for_proxy)
        {
            $headers = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM');
                
            foreach ($headers as $header)
            {
                if ( !empty($_SERVER[$header]) )
                {
                    $ip = $_SERVER[$header];
                }
            }
        }
            
        return $ip;
    }
        
    /**
     * Get unique hash.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $prefix
     * @param bool $more_entropy
     * 
     * @return string
     */
    public static function getHash($prefix = '', $more_entropy = false)
    {
        return self::md5( uniqid($prefix, $more_entropy) );
    }
        
    /**
     * Get an array of the active supported comment systems.
     *
     * @access public
     * 
     * @static
     * 
     * @return array
     */
    public static function get_comment_systems()
    {
        $db = JFactory::getDBO();
            
        $supported = array('com_rscomments');
            
        $systems = array(
            'com_rscomments' => 'RSComments!',
            'facebook' => 'Facebook Comments',
            'disqus' => 'Disqus'
        );
            
        foreach ($supported as $com_name) 
        {
            $path = JPATH_SITE . "/administrator/components/$com_name";
                
            if ( file_exists($path) )
            {
                $query = $db->getQuery(true)
                       ->select( $db->qn('enabled') )
                       ->from( $db->qn('#__extensions') )
                       ->where( $db->qn('type') . ' = ' . $db->q('component') . ' AND ' . $db->qn('element') . ' = ' . $db->q($com_name) );
                      
                $db->setQuery($query, 0, 1);
                    
                if ( !$db->loadResult() )
                {
                    unset($systems[$com_name]);
                }
            }
            else
            {
                unset($systems[$com_name]);
            }
        }
            
        return $systems;
    }
        
    /**
     * Check if $haystack starts with $needle.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $haystack
     * @param string $needle
     * 
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return !strncmp( $haystack, $needle, strlen($needle) );
    }
    
    /**
     * Check if $haystack ends with $needle.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $haystack
     * @param string $needle
     * 
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        
        if ($length == 0)
            return true;
    
        return substr($haystack, -$length) === $needle;
    }
        
    /**
     * Cut a string to the specified length.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $str
     * @param int $length
     * 
     * @return string
     */
    public static function cut($str, $length)
    {
        return isset($str{$length}) ? substr($str, 0, $length) . '...' : $str;
    }
        
    /**
     * Get the absolute JRoute.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $url
     * @param bool $xhtml
     *
     * @return string
     */
    public static function absJRoute($url, $administrator = false, $xhtml = true)
    {
        return JURI::root() . ($administrator ? 'administrator/' : '') . substr( JRoute::_($url, $xhtml), strlen( JURI::base(true) ) + 1 );
    }
        
    /**
	 * Convert links in a text from relative to absolute.
	 *
	 * @param string $text The text processed
	 *
	 * @return string Text with converted links
	 */
    public static function relToAbs($text)
    {
        $base = JUri::base();
		$text = preg_replace("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", "$1=\"$base\$2\"", $text);
            
        return $text;
    }
        
    /**
     * Check user permission.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $permission
     * @param mixed $user Null to automatically load the logged in user, an user id or an user object.
     * 
     * @return bool
     */
    public static function checkUserPermission($permission, $user = null)
    {
        static $permissions;
            
        if ( empty($permissions) )
        {
            // Get DBO.
            $db = JFactory::getDBO();
                
            // Get the groups.
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_groups') )
				   ->where( $db->qn('published') . ' = ' . $db->q(1) );
                    
            $db->setQuery($query);
                
            $groups = $db->loadObjectList();
                
                
            // Get the groups relations.
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_groups_relations') );
                  
            $db->setQuery($query);
                
            $groups_relations = $db->loadObjectList();
                
            // Initialize the permissions array.
            $permissions = array();
                
            foreach ($groups_relations as $group_relation)
            {
                if ( !isset($permissions[$group_relation->jgroup_id]) )
                {
                    foreach ($groups as $group)
                    {
                        if ($group_relation->group_id == $group->id)
                        {
                            $permissions[$group_relation->jgroup_id] = $group;
                        }
                    }
                }
            }
        }
            
        if (!$permissions)
            return false;
            
        if ( is_null($user) )
        {
            $user = JFactory::getUser();    
        }
        else if ( is_numeric($user) )
        {
            $user = JFactory::getUser($user);
        }
            
        if ( empty($user) )
            return false;
            
        $jgroups = $user->getAuthorisedGroups();
            
        // Get the user jgroups.
        if ( $user->get('guest') && !$jgroups )
        {
            $jgroups = array(1);
        }
            
        if (!$jgroups)
            return false;
            
        // Check the permissions.
        foreach ($jgroups as $jgroup_id)
        {
            if ( !empty($permissions[$jgroup_id]->$permission) && (bool)$permissions[$jgroup_id]->$permission )
                return true;
        }
            
        return false;
    }
        
    /**
     * Check if an user can contact an entry author.
     *
     * @access public
     *
     * @static
     *
     * @param int $author_id
     * 
     * @return bool
     */
    public static function canContactEntryAuthor($author_id)
    {
        if ( empty($author_id) )
            return false;
            
        $can_contact_entries_authors = self::checkUserPermission('can_contact_entries_authors');
            
        if (!$can_contact_entries_authors)
            return false;
            
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('enable_contact_form') )
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($author_id) );
               
        $db->setQuery($query);
        $result = $db->loadResult();
            
        return is_null($result) || $result;
    }
        
    /**
     * Generate a random integer.
     *
     * @access public
     * 
     * @static
     * 
     * @param int $min
     * @param mixed $max
     * 
     * return int
     */
    public static function rand($min = 0, $max = null)
    {
        if ( function_exists('mt_rand') )
        {
            if ( is_numeric($max) )
                return mt_rand($min, $max);
            else
                return mt_rand($min);
        }
        else
        {
            if ( is_numeric($max) )
                return rand($min, $max);
            else
                return rand($min);
        }
    }
        
    /**
     * Generate a random string.
     *
     * @access public
     * 
     * @static
     * 
     * @param int $len
     * @param string $chars
     * 
     * return string
     */
    public static function randStr($len = 6, $chars = '1234567890abcdefghijklmnopqrstuvwxyz')
    {
        return substr( str_shuffle($chars), 0, $len );
    }
        
    /**
     * Get payment methods.
     *
     * @access public
     * 
     * @static
     * 
     * return mixed
     */
    public static function getPaymentMethods()
    {
        // Retrieve the payment options from the payment plugins.
        $payment_methods = JFactory::getApplication()->triggerEvent('rsdirectory_addPaymentOptions');
            
        if ($payment_methods)
        {
            foreach ($payment_methods as $i => $item)
            {
                if ( empty($item->value) )
                {
                    unset($payment_methods[$i]);
                }
            }
        }
            
        return $payment_methods;
    }
        
    /**
     * Method to get a list of credit packages.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $published
     *
     * @return mixed
     */
    public static function getCreditPackages($published = null)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_credit_packages') )
               ->order( $db->qn('ordering') );
               
        if ( !is_null($published) )
        {
            $query->where( $db->qn('published') . ' = ' . $db->q($published) );
        }
            
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Method to get a single credit package.
     *
     * @access public
     *
     * @static
     *
     * @param int $id The credit package id.
     *
     * @return mixed
     */
    public static function getCreditPackage($id)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_credit_packages') )
               ->where( $db->qn('id') . ' = ' . $db->q($id) );
               
        $db->setQuery($query);
            
        return $db->loadObject();
    }
        
    /**
     * Method to get a single user transaction.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $id Transaction id or hash.
     * 
     * @return mixed
     */
    public static function getUserTransaction($id)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_users_transactions') );
               
        if ( is_numeric($id) )
        {
            $query->where( $db->qn('id') . ' = ' . $db->q($id) );
        }
        else if ( is_string($id) )
        {
            $query->where( $db->qn('hash') . ' = ' . $db->q($id) );
        }
        else
        {
            return;    
        }
            
        $db->setQuery($query);
            
        return $db->loadObject();
    }
        
    /**
     * Save transaction log.
     *
     * @access public
     * 
     * @static
     * 
     * @param int $transaction_id
     * @param array $log
     * 
     * @return mixed
     */
    public static function saveTransactionLog($transaction_id, $log)
    {
        if (!$transaction_id || !$log)
            return;
            
        $transaction_id = (int)$transaction_id;
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        if ( !is_array($log) )
        {
            $log = (array)$log;
        }
            
        foreach ($log as $i => $item)
        {
            $log[$i] = '<b>' . JFactory::getDate()->format('Y/m/d') . '</b> ' . $item;
        }
            
        $log = implode('<br />', $log);
            
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_users_transactions') )
               ->set( $db->qn('gateway_log') . ' = CONCAT(' . $db->qn('gateway_log') . ', ' . $db->q($log) . ')' )
               ->where( $db->qn('id') . ' = ' . $db->q($transaction_id) );
               
        $db->setQuery($query);
            
        return $db->execute();
    }
        
    /**
     * Format price.
     *
     * @access public
     * 
     * @static
     * 
     * @param mixed $price
     * 
     * @return string
     */
    public static function formatPrice($price)
    {
        $config = RSDirectoryConfig::getInstance();
            
        $currency = $config->get('currency');
        $currency_sign = $config->get('currency_sign');
        $decimals = $config->get('decimals');
        $decimal_point = $config->get('decimal_point');
        $thousands_separator = $config->get('thousands_separator');
        $payment_mask = $config->get('payment_mask');
            
        $price = number_format($price, $decimals, $decimal_point, $thousands_separator);
            
        return str_replace( array('{currency}', '{currency_sign}', '{price}'), array($currency, $currency_sign, $price), $payment_mask );
    }
        
    /**
     * Get md5 hash.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $str
     * 
     * @return string
     */
    public static function md5($str)
    {
        return function_exists('hash') ? hash('md5', $str) : md5($str);
    }
        
    /**
     * Escape HTML.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $string
     * 
     * @return string
     */
    public static function escapeHTML($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }
        
    /**
     * Clean text.
     *
     * @access public
     * 
     * @static
     *
     * @param string $text
     * @param bool $js
     * @param bool $a
     *
     * @return string
     */
    public static function cleanText($text, $js = true, $a = true)
    {
        if ($js)
        {
            $text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);    
        }
            
        if ($a)
        {
            $text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
        }
            
		return $text;
    }
        
    /**
     * Transform the object type string into an user friendly string.
     * 
     * @access public
     * 
     * @static
     * 
     * @param sting $object_type
     * 
     * @return sting
     */
    public static function transformObjectType($object_type)
    {
        $object_types = array(
            'form_field' => 'COM_RSDIRECTORY_FORM_FIELD',
            'publishing_interval' => 'COM_RSDIRECTORY_PUBLISHING_INTERVAL',
            'renewal' => 'COM_RSDIRECTORY_RENEWAL',
            'promoted_entry' => 'COM_RSDIRECTORY_PROMOTED_ENTRY',
        );
            
        return isset($object_types[$object_type]) ? ucwords( JText::_($text) ) : '';
    }
    
    /**
     * Open a modal window.
     *
     * @access public
     * 
     * @static
     */
    public static function openModalWindow($url, $width = 600, $height = 450)
    {
        ?>
            
        SqueezeBox.open('<?php echo $url; ?>', {
            handler: 'iframe',
            size: {x: <?php echo $width; ?>, y: <?php echo $height; ?>},
        });
            
        <?php
    }
        
    /**
     * Is Joomla! 3.0?
     *
     * @access public
     * 
     * @static
     * 
     * @return bool
     */
    public static function isJ30()
    {
        return self::isJoomlaCompatible('3.0');
    }
        
    /**
     * Is Joomla! 2.5?
     *
     * @access public
     * 
     * @static
     * 
     * @return bool
     */
    public static function isJ25()
    { 
        return self::isJoomlaCompatible('2.5') && !self::isJoomlaCompatible('3.0');
    }
        
    /**
     * Method to check Joomla! version.
     *
     * @access public
     *
     * @static
     *
     * @param string $version
     *
     * @return bool
     */
    public static function isJoomlaCompatible($version)
    {
        static $jversion;
            
        if (!$jversion)
        {
            $jversion = new JVersion();
        }
            
        return $jversion->isCompatible($version);
    }
        
    /**
     * Get the ids of the core field types.
     *
     * @access public
     * 
     * @static
     *
     * @return array
     */
    public static function getCoreFieldTypesIds()
    {
        static $ids;
            
        if (!$ids)
        {
            // Get an instance of the FieldTypes model.
            $fieldtypes_model = RSDirectoryModel::getInstance('FieldTypes');
                
            // Get the core field types.
            $field_types = $fieldtypes_model->getFieldTypesObjectList(1);
                
            // Initialize the core field types ids array.
            $ids = array();
                
            // Populate the core field types ids array.
            if ($field_types)
            {
                foreach ($field_types as $field_type)
                {
                    $ids[] = $field_type->id;
                }
            }
        }
            
        return $ids;
    }
    
    /**
     * Get the ids of the field types that are always published.
     *
     * @access public
     * 
     * @static
     *
     * @return array
     */
    public static function getAlwaysPublishedFieldTypesIds()
    {
        static $ids;
            
        if (!$ids)
        {
            // Get an instance of the FieldTypes model.
            $fieldtypes_model = RSDirectoryModel::getInstance('FieldTypes');
                
            // Get the core field types.
            $field_types = $fieldtypes_model->getFieldTypesObjectList();
                
            // Initialize the core field types ids array.
            $ids = array();
                
            // Populate the field types ids array.
            if ($field_types)
            {
                foreach ($field_types as $field_type)
                {
                    if ($field_type->always_published)
                    {
                        $ids[] = $field_type->id;
                    }
                }
            } 
        }
            
        return $ids;    
    }
      
    /**
     * Get an image URL.
     *
     * @access public
     * 
     * @static
     *
     * @param string $hash
     * @param string $size
     *
     * @return string
     */  
    public static function getImageURL($hash, $size = 'normal')
    {
        static $config, $files;
            
        // Check the size and hash.
        if ( !in_array( $size, array('small', 'big', 'normal') ) || !$hash )
            return;
            
        if ( !is_array($files) )
        {
            $files = array();
        }
            
        // Get the file.
        if ( empty($files[$hash]) )
        {
            $files[$hash] = RSDirectoryHelper::getFileObject(0, $hash);
        }
            
        $file = $files[$hash];
            
        if (!$file)
            return;
            
        if ( empty($config) )
        {
            // Get an instance of the RSDirectory Config.
            $config = RSDirectoryConfig::getInstance();
        }
            
        if ($size == 'small')
        {
            $w = $config->get('small_thumbnail_width');
            $h = $config->get('small_thumbnail_height');
        }
        else if ($size == 'big')
        {
            $w = $config->get('big_thumbnail_width');
            $h = $config->get('big_thumbnail_height');
        }
        else
        {
            $w = $config->get('normal_thumbnail_width');
            $h = $config->get('normal_thumbnail_height');
            $far = 0;
        }
            
        if ( $config->get('watermark_images') && $size == 'normal' )
        {
            $watermark_file = JPATH_BASE . '/' . $config->get('watermark');
            $watermark_position = $config->get('watermark_position');
            $watermark_opacity = $config->get('watermark_opacity');
            $watermark_size = $config->get('watermark_size');
                
            $ext = JFile::getExt($file->file_name);
                
            $file_name = hash('md5', $file->file_name . $watermark_file . $watermark_position . $watermark_opacity . $watermark_size) . ($ext ? ".$ext" : '');
        }
        else
        {
            $file_name = $file->file_name;
        }
            
        // Set the cache dir path. 
        $cache_dir = JPATH_ROOT   . "/components/com_rsdirectory/files/cache/{$w}x{$h}/";
            
        // Set the cache file path.
        $cache_file = $cache_dir . $file_name;
            
        if ( file_exists($cache_file) )
        {
            return JURI::root() . "components/com_rsdirectory/files/cache/{$w}x{$h}/$file_name";
        }
        else
        {
            return self::absJRoute("index.php?option=com_rsdirectory&task=image.view&size=$size&hash=$hash");
        }
    }
        
    /**
     * Get the categories that belong to RSDirectory!
     *
     * @access public
     *
     * @static
     *
     * @return mixed
     */
    public static function getCategories($categories_ids = 0)
    {
        if ( $categories_ids && !is_array($categories_ids) )
        {
            $categories_ids = (array)$categories_ids;
        }
            
        $categories_ids = self::arrayInt($categories_ids, true, true);
        $categories_ids = array_unique($categories_ids);
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('id') . ', ' . $db->qn('parent_id') . ', ' . $db->qn('title') . ', ' . $db->qn('params') . ', ' . $db->qn('published') )
               ->from( $db->qn('#__categories') )
               ->where( $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') );
               
        if ($categories_ids)
        {
            $query->where('IN (' . implode(',', $categories_ids) . ')');
        }
            
        $db->setQuery($query);
          
        return $db->loadObjectList();
    }
        
    /**
     * Get a category's status based on the status it's parent categories.
     *
     * @access public
     * 
     * @static
     * 
     * @param int $category_id
     * @param array $categories
     * 
     * @return mixed
     */
    public static function getCategoryStatus($category_id, $categories)
    {
        $hierarchy = self::getCategoryHierarchy($category_id, $categories);
            
        if (!$hierarchy)
            return false;
            
        foreach ($hierarchy as $category)
        {
            if (!$category->published)
                return false;
        }
            
        return true;
    }
        
    /**
     * Get the inherited form associated to a category.
     *
     * @access public
     * @static
     *
     * @param int $category_id
     * @param mixed $categories
     *
     * @return object
     */
    public static function getCategoryInheritedForm($category_id, $categories = null)
    {
        if ( is_null($categories) )
        {
            // Get the categories.
            $categories = self::getCategories();
        }
            
        if (!$categories)
            return;
            
        // Get the form id.
        $form_id = self::getCategoryInheritedFormId($category_id, $categories);
            
        return self::getForm($form_id);
    }
        
    /**
     * Get the id of the inherited form associated to a category.
     *
     * @access public
     * @static
     *
     * @param int $category_id
     * @param mixed $categories
     *
     * @return object
     */
    public static function getCategoryInheritedFormId($category_id, $categories = null)
    {
        if ( is_null($categories) )
        {
            // Get the categories.
            $categories = self::getCategories();
        }
            
        if (!$categories)
            return;
        
        // Get the category hierarchy.
        $hierarchy = self::getCategoryHierarchy($category_id, $categories);
            
        if (!$hierarchy)
            return;
            
        // Get the index of the subject category.
        foreach ($hierarchy as $i => $category)
        {
            if ($category->id == $category_id)
            {
                $index = $i;
            }
        }
            
        // Exit the function if no category was found with the specified id.
        if ( !isset($index) )
            return;
            
        // Initialize the form id.
        $form_id = 0;
            
        for ($i = $index; $i >= 0; $i--)
        {
            // Get the category params.
            $params = new JRegistry($hierarchy[$i]->params);
                
            // Get the form id.
            $form_id = $params->get('form_id');
                
            // Exit the loop if we found the inherited form.
            if ($form_id)
                break;
        }
            
        return $form_id;
    }
        
    /**
     * Get form.
     *
     * @access public
     *
     * @static
     *
     * @param int $form_id
     *
     * @return object
     */
    public static function getForm($form_id)
    {
        static $forms = array();
            
        if ( !isset($forms[$form_id]) )
        {
            JTable::addIncludePath(JPATH_ADMINISTRATOR  . '/components/com_rsdirectory/tables');
            
            // Get the form.
            $form = JTable::getInstance('Form', 'RSDirectoryTable');
            $form->load($form_id);
                
            if (!$form_id)
            {
                $form->title = JText::_('JNONE');
            }
                
            $forms[$form_id] = $form;
        }
            
        return $forms[$form_id];
    }
        
    /**
     * Get a object list of all RSDirectory! forms.
     *
     * @access public
     *
     * @static
     *
     * @return mixed
     */
    public static function getForms()
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_forms') )
               ->order( $db->qn('id') );
              
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Get the forms count.
     *
     * @access public
     *
     * @static
     *
     * @return int
     */
    public static function getFormsCount()
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_forms') );
               
        $db->setQuery($query);
            
        return $db->loadResult();
    }
        
    /**
     * Get category.
     *
     * @access public
     *
     * @static
     *
     * @param int $category_id
     *
     * @return object
     */
    public static function getCategory($category_id)
    {
        if ( self::isJ25() )
        {
            // Import Joomla Categories library.
            jimport( 'joomla.application.categories' );
        }
            
        // Create a new JCategories object.
        $categories = new JCategories( array('extension' => 'com_rsdirectory', 'table' => '#__categories') );
            
        return $categories->get($category_id);
    }
        
    /**
     * Get the subcategories of the specified category.
     *
     * @access public
     *
     * @static
     *
     * @param int $category_id
     *
     * @return array
     */
    public static function getSubcategories($category_id)
    {
        // Get the parent category.
        $parent = self::getCategory($category_id);
            
        if ($parent)
            return $parent->getChildren();
    }
    
    /**
     * Get categories select.
     *
     * @access public
     *
     * @static
     *
     * @param array $categories
     * @param string $more
     *
     * @return mixed
     */
    public static function getCategoriesSelect($categories, $more = '>')
    {
        $str = '<select class="rsdir-category-select">';
            
        $str .= '<option value="0">' . JText::_('JOPTION_SELECT_CATEGORY') . '</option>';
            
        if ($categories)
        {
            foreach ($categories as $category)
            {
                $children = $category->getChildren();
                    
                $text = $category->title;
                    
                if ( $category->getChildren() )
                {
                    $text .= ' ' . $more;
                }
                    
                $str .= '<option value="' . self::escapeHTML($category->id) . '"' . ($children ? ' data-children="1"' : '') . '>' . self::escapeHTML($text) . '</option>';
            }
        }
            
        $str .= '</select>';
            
        $str .= ' <img class="rsdir-loader hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" width="16" height="16" alt="" />';
            
        return $str;
    }
        
    /**
     * Output subcategories HTML.
     *
     * @access public
     *
     * @static
     *
     * @param array $subcategories
     * @param JRegistry $params
     * @param int $level
     */
    public static function outputSubcategoriesHTML( $subcategories, $params, $level = 1 )
    {
        if (!$subcategories)
            return;
            
        // Get the subcategory levels.
        $subcategory_levels = $params->get('maxLevelcat', 2);
            
        // Get the subcategories count.
        $subcategories_count = $params->get('subcategories_count');
            
        // Get the description character limit.
        $subcat_desc_limit = $params->get('subcat_desc_limit', 0);
           
        // Get the Itemid. 
        $Itemid = $params->get('itemid');
            
        $hide = false;
            
        $is_numeric = is_numeric($subcategories_count);
            
        foreach ($subcategories as $i => $subcategory)
        {
            if ( $is_numeric && !$hide && $i == $subcategories_count)
            {
                echo '<div class="media-group-wrapper"><div class="media-group hide">';
                $hide = true;
            }
                
            ?>
                
            <div class="media">
                <?php if ( $params->get('show_subcategories_thumbnails') && !empty($subcategory->thumbnail_url) ) { ?>        
                <img class="pull-left" src="<?php echo $subcategory->thumbnail_url; ?>" alt="" width="<?php echo self::escapeHTML( $params->get('subcategories_thumbnails_width', 1) ); ?>" height="<?php echo self::escapeHTML( $params->get('subcategories_thumbnails_height') ); ?>" />
                <?php } ?>
                    
                <div class="media-body">
                    <div class="media-heading">
                        <a href="<?php echo RSDirectoryRoute::getCategoryEntriesURL($subcategory->id, $subcategory->title, $Itemid); ?>"><?php echo self::escapeHTML($subcategory->title); ?></a>
                        <?php echo $params->get('show_cat_num_articles_cat') ? ' (' . $subcategory->getNumItems(true) . ')' : ''; ?>
                    </div>
                    <?php
                        
                    if ( $params->get('show_subcat_desc_cat', 0) )
                    {
                        if ($subcat_desc_limit)
                        {
                            $description = self::escapeHTML( self::cut( strip_tags($subcategory->description), $subcat_desc_limit ) );
                        }
                        else
                        {
                            $description = $subcategory->description;    
                        }
                            
                        if ($description)
                        {
                            echo "<p>$description</p>";
                        }
                    }
                        
                    if ( ( $subcategory_levels == - 1 || $level + 1 < $subcategory_levels ) && $children = $subcategory->getChildren() )
                    {
                        self::outputSubcategoriesHTML($children, $params, $level + 1);
                    }
                        
                    ?>
                </div>
            </div>
                
            <?php
                
            if ( $is_numeric && $hide && $subcategory == end($subcategories) )
            {
                echo '</div>';
                echo '<a class="media-more" href="#"><i class="icon-chevron-down"></i> ' . JText::_('COM_RSDIRECTORY_SHOW_MORE') . '</a>';
                echo '<a class="media-less hide" href="#"><i class="icon-chevron-up"></i> ' . JText::_('COM_RSDIRECTORY_SHOW_LESS') . '</a>';
                echo '</div><!-- .media-group -->';
            }
        }
    }
        
    /**
     * Get form fields.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $forms_ids A single integer value or an array of integer values.
     * @param mixed $published
     * @param mixed $expect_value
     * @param mixed $field_type
     *
     * @return mixed
     */
    public static function getFormFields($forms_ids = null, $published = null, $expect_value = null, $field_type = null)
    {
        if ( !is_array($forms_ids) )
        {
            $forms_ids = (array)$forms_ids;
        }
            
        $forms_ids = self::arrayInt($forms_ids, true, true);
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        foreach ($forms_ids as $i => &$form_id)
        {
            $form_id = $db->q($form_id);
        }
            
        $select = array(
            $db->qn('f') . '.*',
            $db->qn('ft.type', 'field_type'),
            $db->qn('ft.core'),
            $db->qn('ft.create_column'),
            $db->qn('ft.expect_value'),
        );
            
        $query = $db->getQuery(true)
               ->select($select);
               
        if ($forms_ids)
        {
            $query->from( $db->qn('#__rsdirectory_forms_fields', 'ff') )
                  ->innerJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('ff.field_id') . ' = ' . $db->qn('f.id') )
                  ->where( $db->qn('ff.form_id') . ' IN (' . implode(',', $forms_ids) . ')' )
                  ->order( $db->qn('ff.ordering') );
        }
        else
        {
            $query->from( $db->qn('#__rsdirectory_fields', 'f') );
        }
            
        $query->innerJoin( $db->qn('#__rsdirectory_field_types', 'ft') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ft.id') );
            
        if ( !is_null($published) )
        {
            $query->where( $db->qn('f.published') . ' = ' . $db->q($published) );
        }
            
        if ( !is_null($expect_value) )
        {
            $query->where( $db->qn('ft.expect_value') . ' = ' . $db->q($expect_value) );
        }
            
        if ( !is_null($field_type) )
        {
            $query->where( $db->qn('ft.type') . ' = ' . $db->q($field_type) );
        }
            
        $db->setQuery($query);
            
        // Get the form fields.
        $form_fields = $db->loadObjectList();
            
        if ($form_fields)
        {
            // Initialize the ids array.
            $ids = array();
                
            foreach ($form_fields as &$form_field)
            {
                $ids[] = $db->q($form_field->id);
                    
                $form_field->properties = new JRegistry;
            }
                
            // Get the fields properties.
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from('#__rsdirectory_fields_properties')
                   ->where( $db->qn('field_id') . ' IN (' . implode(',', $ids) . ')' );
                  
            $db->setQuery($query);
                
            $properties = $db->loadObjectList();
                
            // Add the properties to their corresponding fields.
            if ($properties)
            {
                foreach ($properties as $property)
                {
                    foreach ($form_fields as &$form_field)
                    {
                        if ($property->field_id == $form_field->id)
                        {
                            $form_field->properties->set($property->property_name, $property->value);
                            break;
                        }
                    }
                }
            }
        }
            
        return $form_fields;
    }
        
    /**
     * Get entry.
     *
     * @access public
     *
     * @static
     *
     * @param int $entry_id
     *
     * @return object
     */
    public static function getEntry($entry_id)
    {
        $items = self::getEntriesObjectListByIds($entry_id);
            
        return isset($items[0]) ? $items[0] : null;
    }
        
    /**
     * Get a entries object list, selected by ids.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $ids
     * 
     * @return mixed
     */
    public static function getEntriesObjectListByIds($ids)
    {
        if (!$ids)
            return;
            
        if ( !is_array($ids) )
        {
            $ids = (array)$ids;
        }
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $select = array(
            $db->qn('e') . '.*',
            $db->qn('ec') . '.*',
            $db->qn('c.title', 'category_title'),
            $db->qn('c.path', 'category_path'),
            $db->qn("u.$author", 'author'),
        );
            
        // Quote the ids.
        foreach ($ids as &$id)
        {
            $id = $db->q($id);
        }
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_entries', 'e') )
               ->leftJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
               ->leftJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->where( $db->qn('e.id') . ' IN (' . implode(',', $ids) . ')' );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
    
    /**
     * Get a list of entries with forms, form fields and files attached to each entry.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $entry 
     *
     * @return array
     */
    public static function getEntryData($entry)
    {
        if (!$entry)
            return;
            
        if ( !is_array($entry) )
        {
            $entries = array($entry);
        }
            
        $entries = self::getEntriesData($entries);
            
        return isset($entries[0]) ? $entries[0] : null;
    }
        
    /**
     * Get a list of entries with forms, form fields and files attached to each entry.
     *
     * @access public
     *
     * @static
     *
     * @param array $entries 
     *
     * @return array
     */
    public static function getEntriesData($entries)
    {
        if ( empty($entries) || !is_array($entries) )
            return;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Initialize the forms ids array.
        $forms_ids = array();
            
        // Initialize the entries ids array.
        $entries_ids = array();
            
        foreach ($entries as $entry)
        {
            $forms_ids[] = $db->q($entry->form_id);
            $entries_ids[] = $db->q($entry->id);
        }
            
        // Remove duplicate forms ids.
        $forms_ids = array_unique($forms_ids);
            
        // Remove duplicate entries ids.
        $entries_ids = array_unique($entries_ids);
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_forms') )
               ->where( $db->qn('id') . ' IN (' . implode(',', $forms_ids) . ')' );
               
        $db->setQuery($query);
            
        $forms_list = $db->loadObjectList();
            
        // Initialize the forms array.
        $forms = array();
            
        // Place the forms in the array by form id.
        if ($forms_list)
        {
            foreach ($forms_list as $form)
            {
                $form->fields = self::getFormFields($form->id, 1);
                    
                $forms[$form->id] = $form;
            }
        }
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_uploaded_files', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_uploaded_files_fields_relations', 'fr') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fr.file_id')  )
               ->where( $db->qn('fr.entry_id') . ' IN ' . '(' . implode(',', $entries_ids) . ')' )
               ->order( $db->qn('fr.entry_id') . ', ' . $db->qn('fr.ordering') );
               
        $db->setQuery($query);
            
        $files_list = $db->loadObjectList();
            
        foreach ($entries as &$entry)
        {
            // Assign the form to the entry.
            $entry->form = empty($forms[$entry->form_id]) ? null : clone $forms[$entry->form_id];
                
            // Clone and overwrite fields.
            if ( !empty($entry->form->fields) )
            {
                foreach ($entry->form->fields as $i => $field)
                {
                    $entry->form->fields[$i] = clone $field;
                }
            }
                
            // Add the files to its corresponding field.
            if ($files_list)
            {
                foreach ($files_list as $file)
                {
                    foreach ($entry->form->fields as &$field)
                    {
                        if ($entry->id == $file->entry_id && $field->id == $file->field_id)
                        {
                            if ( empty($field->files) )
                            {
                                $field->files = array();
                            }
                                
                            $field->files[] = $file;
                        }
                    }
                }
            }
        }
            
        return $entries;
    }
        
    /**
     * Get field.
     *
     * @access public
     *
     * @static
     *
     * @param int $field_id
     *
     * @return mixed
     */ 
    public static function getField($field_id)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $select = array(
            $db->qn('f') . '.*',
            $db->qn('ft.type', 'field_type'),
            $db->qn('ft.core'),
            $db->qn('ft.all_forms'),
            $db->qn('ft.create_column'),
            $db->qn('ft.expect_value'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_forms_fields', 'ff') )
               ->innerJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('ff.field_id') . ' = ' . $db->qn('f.id') )
               ->innerJoin( $db->qn('#__rsdirectory_field_types', 'ft') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ft.id') )
               ->where( $db->qn('f.id') . ' = ' . $db->q($field_id) );
            
        $db->setQuery($query);
            
        // Get the field.
        $field = $db->loadObject();
            
        if (!$field)
            return;
            
        $field->properties = new JRegistry;
            
        // Get the fields properties.
        $query = $db->getQuery(true)
               ->select('*')
               ->from('#__rsdirectory_fields_properties')
               ->where( $db->qn('field_id') . ' = ' . $db->q($field->id) );
              
        $db->setQuery($query);
            
        $properties = $db->loadObjectList();
            
        // Add the properties to their corresponding fields.
        if ($properties)
        {
            foreach ($properties as $property)
            {
                $field->properties->set($property->property_name, $property->value);
            }
        }
            
        return $field;
    }
        
    /**
     * Get the fields displayed in the filtering form.
     *
     * @access public
     *
     * @static
     *
     * @param array $categories_ids
     *
     * @return mixed
     */
    public static function getFilterFields($categories_ids)
    {
		if (!$categories_ids)
		{
			$categories_ids = array();    
		}
			
		// Get all the categories.
		$categories = self::getCategories();
			
		if ( !$categories_ids || in_array(0, $categories_ids) || in_array('', $categories_ids) )
		{
			foreach ($categories as $category)
			{
				if ($category->published)
				{
					$categories_ids[] = $category->id;    
				}
			}
		}
			
		$categories_ids = array_unique($categories_ids);
            
        // Remove some values.
        $categories_ids = array_diff( $categories_ids, array(0, 1, '') );
			
		// Initialize the forms ids array.
		$forms_ids = array();
            
		foreach ($categories_ids as $category_id)
		{
			if ( self::getCategoryStatus($category_id, $categories) )
			{
				$forms_ids[] = self::getCategoryInheritedFormId($category_id, $categories);
			}
		}
			
		$forms_ids = array_unique($forms_ids);
		$forms_ids = self::arrayInt($forms_ids, true, true);
			
		if (!$forms_ids)
			return;
            
		// Initialize the forms fields array.
		$forms_fields = array();
			
		foreach ($forms_ids as $form_id)
		{
			$forms_fields[] = self::getFormFields($form_id, 1);
		}
			
		// Initialize holding the common form fields.
		$intersection = array();
			
		// Get the common form fields.
		foreach ($forms_fields as $form_fields)
		{
			if ($intersection)
			{
                $intersection = self::formFieldsIntersection($intersection, $form_fields);
			}
			else
			{
				$intersection = $form_fields;
			}
		}
			
		if ($intersection)
		{
			foreach ($intersection as $i => $form_field)
			{
				if ( !$form_field->properties->get('searchable_advanced') )
				{
					unset($intersection[$i]);
				}
			}
		}
			
		return $intersection;
    }
        
    /**
     * Compute the intersection between two form fields array.
     *
     * @access public
     *
     * @static
     *
     * @param array $form_fields1
     * @param array $form_fields2
     *
     * @return array
     */
    public static function formFieldsIntersection($form_fields1, $form_fields2)
    {
        $results = array();
            
        if ( is_array($form_fields1) && is_array($form_fields2) )
        {
           foreach ($form_fields1 as $k1 => $form_field1)
            {
                foreach($form_fields2 as $k2 => $form_field2)
                {
                    if ( isset($form_field1->name, $form_field2->name) && $form_field1->name === $form_field2->name )
                    {
                        $results[] = $form_field1;
                    }
                }
            } 
        }
            
        return $results;
    }
        
    /**
     * Regenerate the entries titles, big subtitles, small subtitles and descriptions.
     *
     * @access public
     *
     * @static
     *
     * @param array $entries
     * @param array $elements The elements to regenerate: titles, big subtitles, small subtitles and/or descriptions.
     *
     * @return bool
     */
    public static function regenerateEntriesTitles( $entries, $elements = array('title', 'big_subtitle', 'small_subtitle', 'description') )
    {
        if ( !$entries || !is_array($entries) )
            return false;
            
        require_once dirname(__FILE__) . '/placeholders.php';
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Initialize the forms ids array.
        $forms_ids = array();
            
        foreach ($entries as $entry)
        {
            $forms_ids[] = $db->q($entry->form_id);
        }
            
        // Remove duplicate forms ids.
        $forms_ids = array_unique($forms_ids);
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_forms') )
               ->where( $db->qn('id') . ' IN (' . implode(',', $forms_ids) . ')' );
           
        $db->setQuery($query);
            
        $forms_list = $db->loadObjectList();
            
        // Initialize the forms array.
        $forms = array();
            
        // Place the forms in the array by form id.
        if ($forms_list)
        {
            foreach ($forms_list as $form)
            {    
                $forms[$form->id] = $form;
            }
        }
            
        $form_fields = self::getFormFields();
            
        foreach ($entries as $entry)
        {
            if ( empty($forms[$entry->form_id]) )
                continue;
                
            $form = $forms[$entry->form_id];
                
            $values = (object)array(
                'id' => $entry->id,
            );
                
            if ( in_array('title', $elements) && $form->use_title_template )
            {
                $values->title = RSDirectoryPlaceholders::getInstance($form->title_template, $form_fields, $entry, $form)
                               ->setParams( array('title', 'database') )
                               ->process();
            }
                
            if ( in_array('big_subtitle', $elements) && $form->use_big_subtitle_template )
            {
                $values->big_subtitle = RSDirectoryPlaceholders::getInstance($form->big_subtitle_template, $form_fields, $entry, $form)
                                      ->setParams( array('title', 'database') )
                                      ->process();
            }
                
            if ( in_array('small_subtitle', $elements) && $form->use_small_subtitle_template )
            {
                $values->small_subtitle = RSDirectoryPlaceholders::getInstance($form->small_subtitle_template, $form_fields, $entry, $form)
                                        ->setParams( array('title', 'database') )
                                        ->process();
            }
                
            if ( in_array('description', $elements) && $form->use_description_template )
            {
                $values->description = RSDirectoryPlaceholders::getInstance($form->description_template, $form_fields, $entry, $form)
                                     ->setParams( array('description', 'database') )
                                     ->process();
            }
                
            $db->updateObject('#__rsdirectory_entries', $values, 'id');
        }
            
        return true;
    }
        
    /**
     * Get the files for a certain entry or field.
     *
     * @access public
     *
     * @static
     *
     * @param int $field_id
     * @param int $entry_id
     *
     * @return mixed
     */
    public static function getFilesObjectList($field_id = 0, $entry_id = 0)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_uploaded_files', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_uploaded_files_fields_relations', 'fr') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fr.file_id')  )
               ->order( $db->qn('fr.ordering') );
                
        if ( !empty($field_id) )
        {
            $query->where( $db->qn('fr.field_id') . ' = ' . $db->q($field_id) );
        }
            
        if ( !empty($entry_id) )
        {
            $query->where( $db->qn('fr.entry_id') . ' = ' . $db->q($entry_id) );
        }
            
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Get a category thumbnail from the database.
     *
     * @access public
     *
     * @static
     *
     * @param int $field_id
     * @param int $category_id
     *
     * @return mixed
     */
    public static function getCategoryThumbObject($file_id = 0, $category_id = 0)
    {
        if (!$file_id && !$category_id)
            return;
        
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_uploaded_files', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_uploaded_files_categories_relations', 'r') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('r.file_id')  );
               
        if ( !empty($file_id) )
        {
            $query->where( $db->qn('file_id') . ' = ' . $db->q($file_id) );
        }
            
        if ( !empty($category_id) )
        {
            $query->where( $db->qn('category_id') . ' = ' . $db->q($category_id) );
        }
            
        $db->setQuery($query, 0, 1);
            
        return $db->loadObject();
    }
        
    /** 
     * Get file object.
     *
     * @access public
     *
     * @static
     *
     * @param int $file_id
     * @param string $hash
     *
     * @return mixed
     */
    public static function getFileObject($file_id = 0, $hash = 0)
    {
        // Get DBO. 
        $db = JFactory::getDbo();
            
        $select = array(
            $db->qn('f') . '.*',
            $db->qn('cr.category_id'),
            $db->qn('fr.entry_id'),
            $db->qn('fr.field_id'),
        );
            
        // Get the file.    
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_uploaded_files', 'f') )
               ->leftJoin( $db->qn('#__rsdirectory_uploaded_files_categories_relations', 'cr') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('cr.file_id') )
               ->leftJoin( $db->qn('#__rsdirectory_uploaded_files_fields_relations', 'fr') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fr.file_id') );
               
        if ( !empty($file_id) )
        {
            $query->where( $db->qn('f.id') . ' = ' . $db->q($file_id) );
        }
            
        if ( !empty($hash) )
        {
            $query->where( $db->qn('f.hash') . ' = ' . $db->q($hash) );
        }
            
        $db->setQuery($query, 0, 1);
            
        return $db->loadObject();
    }
        
    /**
     * Delete one or more files from the disk and from the database.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $files_list A single file object or an array of file objects.
     */
    public static function deleteFiles($files_list)
    {
        if ( $files_list && !is_array($files_list) )
        {
            $files_list = array($files_list);
        }
            
        if (!$files_list)
            return;
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
            
        // Initialize the files ids array.
        $files_ids = array();
            
        foreach ($files_list as $file)
        {
            $path = JPATH_ROOT . "/components/com_rsdirectory/files/entries/$file->entry_id/$file->file_name";
                
            // Delete the file if it exists.
            if ( file_exists($path) )
            {
                JFile::delete($path);
            }
                
            $files_ids[] = $db->q($file->id);
        }
            
        // Get all the cache directories.
        $folders = JFolder::folders(JPATH_ROOT . '/components/com_rsdirectory/files/cache/');
            
        if ($folders)
        {
            foreach ($folders as $folder)
            {
                foreach ($files_list as $file)
                {
                    $path = JPATH_ROOT . "/components/com_rsdirectory/files/cache/$folder/$file->file_name";
                        
                    // Delete the file if it exists.
                    if ( file_exists($path) )
                    {
                        JFile::delete($path);  
                    }
                }
            }
        }
            
        $files_ids_str = implode(',', $files_ids);
            
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_uploaded_files') )
               ->where( $db->qn('id') . ' IN (' . $files_ids_str . ')' );
               
        $db->setQuery($query);
        $db->execute();
            
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
               ->where( $db->qn('file_id') . ' IN (' . $files_ids_str . ')' );
               
        $db->setQuery($query);
        $db->execute();
    }
        
    /**
     * Get global placeholders HTML.
     *
     * @access public
     *
     * @static
     *
     * @return string
     */
    public static function getGlobalPlaceholdersHTML()
    {
        return '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_GLOBAL_TITLE') . '</h4>' .
               '<div>{site.name}</div>' . 
               '<div>{site.url}</div>';
    }
        
    /**
     * Get user placeholders HTML.
     *
     * @access public
     *
     * @static
     *
     * @return string
     */
    public static function getUserPlaceholdersHTML()
    {
        return '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_USER_TITLE') . '</h4>'  .
               '<div>{username}</div>' .
               '<div>{name}</div>' .
               '<div>{email}</div>' .
               '<div>{userid}</div>';
    }
        
    /**
     * Get credits placeholders HTML.
     *
     * @access public
     *
     * @static
     *
     * @return string
     */
    public static function getCreditsPlaceholdersHTML()
    {
        return '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CREDITS_TITLE') . '</h4>' .
               '<div>{credits-remaining}</div>' .
               '<div>{credits-spent} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CREDITS_SPENT') .  '</div>';
    }
        
    /**
     * Get entry general placeholders HTML.
     *
     * @access public
     *
     * @static
     *
     * @return string
     */
    public static function getEntryGeneralPlacehodlersHTML()
    {
        return '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_ENTRY_TITLE') . '</h4>' .
               '<div>{id}</div>' .
               '<div>{url}</div>' .
               '<div>{category} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY') . '</div>' .
               '<div>{category-path} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY_PATH') . '</div>' .
               '<div>{images} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_IMAGES') . '</div>' .
               '<div>{small-thumb} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_SMALL_THUMB') . '</div>' .
               '<div>{big-thumb} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_BIG_THUMB') . '</div>' .
               '<div>{normal-thumb} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_NORMAL_THUMB') . '</div>' .
               '<div>{publishing-date} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_PUBLISHING_DATE') . '</div>' .
               '<div>{expiry-date} - ' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_EXPIRY_DATE') . '</div>' .
               '<div>{title}</div>' .
               '<div>{big-subtitle}</div>' .
               '<div>{small-subtitle}</div>' .
               '<div>{price}</div>' .
               '<div>{description}</div>';
    }
        
    /**
     * Get the custom fields placeholders HTML.
     *
     * @access public
     *
     * @static
     *
     * @param int $category_id
     * @param int $form_id
     * @preset mixed $preset
     *
     * @return string
     */
    public static function getCustomFieldsPlaceholdersHTML($category_id, $form_id = 0, $preset = null)
    {
        // Initialize the result string.
        $str = '';
            
        if ($category_id)
        {
            // Get the inherited form associated to the category.
            $form = self::getCategoryInheritedForm($category_id);
                
            $form_id = $form->id;
        }
            
        if (!$form_id)
        {
            $str .= JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY_NO_CUSTOM_FIELDS');
                
            // Close the application.
            return $str;
        }
            
        // Get the form fields associated to the form.
        $form_fields = self::getFormFields($form_id, null, 1);
            
        if ($form_fields)
        {
            $found = 0;
                
            foreach ($form_fields as $i => $form_field)
            {
                if ( $form_field->core || ( $preset == 'title' && in_array( $form_field->field_type, array('fileupload', 'images', 'image_upload') ) ) )
                {
                    unset($form_fields[$i]);
                    continue;
                }
                    
                $found = 1;
            }
                
            if ($form_fields)
            {
                $table = self::getTableStructure( array_values($form_fields), 4, 'cols' );
                    
                $str .= '<div class="row-fluid">';
                    
                foreach ($table as $cells)
                {
                    $str .= '<div class="span3">';
                        
                    foreach ($cells as $form_field)
                    {
                        if ($form_field)
                        {
                            $str .= '<div>{' . $form_field->name . '}</div>';    
                        }
                    }
                        
                    $str .= '</div>';
                }
                    
                $str .= '</div>';
            }
            else
            {
                $str .= JText::_('COM_RSDIRECTORY_PLACEHOLDERS_NO_CUSTOM_FIELDS');
            }
        }
        else
        {
            $str .= JText::_('COM_RSDIRECTORY_PLACEHOLDERS_NO_CUSTOM_FIELDS');
        }
            
        return $str;
    }
        
    /**
     * Format date according to the date settings.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $date A unix timestamp or a date in a valid format (see: http://www.php.net/manual/en/datetime.formats.php).
     * @param mixed $format Valid options: null (to get the global date and time format), "human_readable" or any valid date format.
     *
     * @return string
     */
    public static function formatDate($date, $format = null)
    {
        static $date_and_time_display;
            
        if ( is_null($format) )
        {
            if ( empty($date_and_time_display) )
            {
                // Get the date and time display method.
                $date_and_time_display = RSDirectoryConfig::getInstance()->get('date_and_time_display');
            }
                
            $format = $date_and_time_display;
        }
            
        $time = is_string($date) ? JFactory::getDate($date)->toUnix() : $date;
            
        if ($format == 'human_readable')
        {
            $date1 = $time;
            $date2 = JFactory::getDate()->toUnix();
                
            $diff_secs = abs($date1 - $date2);
            $base_year = min( JFactory::getDate($date1)->format('Y'), JFactory::getDate($date2)->format('Y') );
                
            $diff = gmmktime(0, 0, $diff_secs, 1, 1, $base_year);
                
            $data = (object)array(
                'years' => JFactory::getDate($diff)->format('Y') - $base_year,
                'months_total' => ( JFactory::getDate($diff)->format('Y') - $base_year ) * 12 + JFactory::getDate($diff)->format('n') - 1,
                'months' => JFactory::getDate($diff)->format('n') - 1,
                'days_total' => floor( $diff_secs / (3600 * 24) ),
                'days' => JFactory::getDate($diff)->format('j') - 1,
                'hours_total' => floor($diff_secs / 3600),
                'hours' => JFactory::getDate($diff)->format('G'),
                'minutes_total' => floor($diff_secs / 60),
                'minutes' => (int)JFactory::getDate($diff)->format('i'),
                'seconds_total' => $diff_secs,
                'seconds' => (int)JFactory::getDate($diff)->format('s'),
            );
                
            // Initialize the result array.
            $result = array();
                
            // Set the precision.
            $precision = 2;
                
            // Current precision.
            $current_precision = 0;
                
            $units = array('years', 'months', 'days', 'hours', 'minutes', 'seconds');
                
            foreach ($units as $unit)
            {
                if ( ($data->$unit || $current_precision) && $current_precision < $precision )
                {
                    if ($data->$unit)
                    {
                        $result[] = JText::plural( 'COM_RSDIRECTORY_NUMBER_OF_' . strtoupper($unit), $data->$unit );
                    }
                        
                    $current_precision++;
                }
            }
                
            return implode(' ', $result);
        }
            
        return JFactory::getDate($time)->format($format);
    }
        
    /**
     * Find one or more elements from an array.
     *
     * @access public
     *
     * @static
     *
     * @param array $conditions An array of one or more conditions.
     * @param array $list The array to search in.
     * @param bool $single Return a single element or an array of elements?
     *
     * @return mixed
     */
    public static function findElements($conditions, $list, $single = true)
    {
        if (!$conditions || !$list)
            return;
            
        // Initialize the restuls array. Will be used only if $single is set to false.
        $results = array();
            
        foreach ($list as $item)
        {
            foreach ($conditions as $column => $value)
            {
                if ($item->$column != $value)
                    continue 2;
            }
                
            if ($single)
            {
                return $item;
            }
            else
            {
                $results[] = $item;
            }
        }
            
        return $results ? $results : null;
    }
        
    /**
     * Find one or more form fields from an form fields array.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $conditions The search conditions. Can be an array or a string. If it's a string, then the search will be done using the field type.
     * @param array $form_fields The fields array to search in.
     * @param bool $single Return a single element or an array of elements?
     *
     * @return mixed 
     */
    public static function findFormField($conditions, $form_fields, $single = true)
    {
        if (!$conditions || !$form_fields)
            return;
            
        if ( !is_array($conditions) )
        {
            $conditions = array('field_type' => $conditions);
        }
            
        return self::findElements($conditions, $form_fields, $single);
    }
        
    /**
     * Get entry meta HTML string.
     *
     * @access public
     *
     * @static
     *
     * @param object $entry
     * @param object $form
     * @param string $section
     *
     * @return string
     */
    public static function getEntryMeta($entry, $form, $section = 'listing_row')
    {  
        if ( !$entry || !$form || !in_array( $section, array('listing_row', 'listing_detail') ) )
            return;
            
        static $date_and_time_display;
        static $view;
            
        if ( is_null($date_and_time_display) )
        {
            $date_and_time_display = RSDirectoryConfig::getInstance()->get('date_and_time_display');
        }
            
        if ( is_null($view) )
        {
            $view = JFactory::getApplication()->input->get('view');    
        }
            
        // Initialize the translation string.
        $trans = 'COM_RSDIRECTORY_LISTING_META';
            
        // Initialize the translation function params array.
        $trans_params = array();
            
        $show_author = $section . '_show_author';
            
        if ($form->$show_author && $view != 'myentries')
        {
            $trans .= '_AUTHOR';
                
            $author_url = RSDirectoryRoute::getUserEntriesURL($entry->user_id, $entry->author);
            $author_title = JText::sprintf( 'COM_RSDIRECTORY_VIEW_ENTRIES_POSTED_BY', self::escapeHTML($entry->author) );    
                
            $trans_params[] = '<a href="' . $author_url . '" title="' . $author_title . '">' . self::escapeHTML($entry->author) . '</a>';
        }
            
        $show_publishing_time = $section . '_show_publishing_time';
            
        if ( $form->$show_publishing_time && $entry->published && JFactory::getDate($entry->published_time)->toUnix() <= JFactory::getDate()->toUnix() )
        {
            $trans .= $date_and_time_display == 'human_readable' ? '_TIME_SINCE' : '_TIME';
                
            $trans_params[] = self::escapeHTML( RSDirectoryHelper::formatDate($entry->published_time) );
        }
            
        $show_category = $section . '_show_category';
            
        if ($form->$show_category)
        {
            $trans .= '_CATEGORY';
            $category_title = JText::sprintf( 'COM_RSDIRECTORY_VIEW_ENTRIES_POSTED_IN', self::escapeHTML($entry->category_title) );
                
            $trans_params[] = '<a href="' . RSDirectoryRoute::getCategoryEntriesURL($entry->category_id, $entry->category_title) . '" title="' . $category_title . '">' . self::escapeHTML($entry->category_title) . '</a>';
        }
            
        if ( isset($trans_params[2]) )
            return JText::sprintf($trans, $trans_params[0], $trans_params[1], $trans_params[2]);
        else if ( isset($trans_params[1]) )
            return JText::sprintf($trans, $trans_params[0], $trans_params[1]);
        else if ( isset($trans_params[0]) )
            return JText::sprintf($trans, $trans_params[0]);
    }
        
    /**
     * Get the ids of the custom fields for a certain form.
     *
     * @access public
     *
     * @static
     *
     * @param object $entry
     *
     * @return array
     */
    public static function getFormCustomFieldsIds($entry)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('f.id') )
               ->from( $db->qn('#__rsdirectory_forms_custom_fields', 'fcf') )
               ->innerJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fcf.field_id') )
               ->where( $db->qn('fcf.form_id') . ' = ' . $db->q($entry->form_id) )
               ->where( $db->qn('f.published') . ' = ' . $db->q(1) );
               
        $db->setQuery($query);
            
        $fields_ids = $db->loadColumn();
            
        if ($fields_ids)
        {
            $form_fields_aux = self::getFormFields($entry->form_id, true);
                
            $form_fields = array();
            
            if ($form_fields_aux)
            {
                foreach ($form_fields_aux as $form_field)
                {
                    $form_fields[$form_field->id] = $form_field;
                }
            }
                
            // Remove empty fields.
            foreach ($fields_ids as $i => $field_id)
            {
                if ( empty($form_fields[$field_id]) )
                {
                    unset($fields_ids[$i]);
                }
                else
                {
                    if ( isset($entry->{$form_fields[$field_id]->column_name}) )
                    {
                        $value = $entry->{$form_fields[$field_id]->column_name};
                            
                        if ( trim($value) === '' || $value == '0000-00-00 00:00:00' )
                        {
                            unset($fields_ids[$i]);
                        }
                    }
                    else
                    {
                        unset($fields_ids[$i]);
                    }
                }
            }
        }
            
        return $fields_ids ? array_values($fields_ids) : null;
    }
        
    /**
     * Get table structure.
     *
     * @access public
     *
     * @static
     *
     * @param array $list
     * @param int $columns
     * @return string $return Organise the result array by cols or by rows.
     *
     * @return array
     */
    public static function getTableStructure($list, $columns = 3, $return = 'rows')
    {
        if (!$list || !$columns)
            return;
            
        // Get the list length.
        $list_len = count($list);
            
        // Calculate the number of rows.
        $rows = ceil($list_len / $columns);
            
        // Initialize the table array.
        $table = array_fill( 0, $columns, array_fill(0, $rows, 0) );
            
        $column = 0;
        $row = 0;
            
        for ($i = 0; $i < $list_len; $i++)
        {        
            $table[$column][$row] = 1;
                
            if ($column < $columns - 1)
            {
                $column++;
            }
            else
            {
                $column = 0;
                $row++;
            }
        }
            
        $i = 0;
            
        if ($return == 'cols')
        {
            // Initialize the result array.
            $result = array_fill( 0, $columns, array_fill(0, $rows, null) );
                
            foreach ($table as $column => $rows)
            {
                foreach ($rows as $row => $value)
                {
                    if (!$value)
                        break;
                        
                    $result[$column][$row] = $list[$i];
                        
                    $i++;
                }
            }
        }
        else
        {
            // Initialize the result array.
            $result = array_fill( 0, $rows, array_fill(0, $columns, null) );
                
            foreach ($table as $column => $rows)
            {
                foreach ($rows as $row => $value)
                {
                    if (!$value)
                        break;
                        
                    $result[$row][$column] = $list[$i];
                        
                    $i++;
                }
            }
        }
            
        return $result;    
    }
        
    /**
     * Check if an user posted a review for a certain entry.
     *
     * @access public
     *
     * @static
     *
     * @param int $entry_id
     * @param mixed $user_id
     *
     * @return bool
     */
    public static function hasReview($entry_id, $user_id = null)
    {
        if (!$entry_id)
            return;
            
        if ( is_null($user_id) )
        {
            $user_id = JFactory::getUser()->id;
        }
            
        JTable::addIncludePath(JPATH_ADMINISTRATOR  . '/components/com_rsdirectory/tables');
            
        // Get an instance of the Review table.
        $review = JTable::getInstance('Review', 'RSDirectoryTable');
            
        $keys = array(
            'entry_id' => $entry_id,
            'user_id' => $user_id,
        );
            
        if (!$user_id)
        {
            $keys['ip'] = RSDirectoryHelper::getIp(true);
        }
            
        // Load review.
        $review->load($keys);
            
        return (bool)$review->id;
    }
        
    /**
     * Get a review.
     *
     * @access public
     *
     * @static
     *
     * @param int $id
     *
     * @return mixed
     */
    public static function getReview($id)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $select = array(
            $db->qn('r') . '.*',
            $db->qn('u.name', 'author_name'),
            $db->qn('u.username'),
            $db->qn('u.email', 'author_email'),
			$db->qn('e.user_id', 'entry_author_id'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_reviews', 'r') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('r.user_id') . ' = ' . $db->qn('u.id') )
               ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->where( $db->qn('r.id') . ' = ' . $db->q($id) );
                
        $db->setQuery($query);
            
        return $db->loadObject();
    }
        
    /**
     * Check if an user added an entry to favorites.
     *
     * @access public
     *
     * @static
     *
     * @param int $entry_id
     * @param int $user_id
     *
     * @return bool
     */
    public static function isFavorite($entry_id, $user_id = 0)
    {
        if (!$entry_id)
            return;
            
        if (!$user_id)
        {
            $user_id = JFactory::getUser()->id;
        }
            
        if (!$user_id)
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('entry_id') )
               ->from( $db->qn('#__rsdirectory_favorites') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) . ' AND ' . $db->qn('user_id') . ' = ' . $db->q($user_id) );
               
        $db->setQuery($query);
            
        return (bool)$db->loadResult();
    }
        
    /**
     * Create a entry folder.
     *
     * @access public
     *
     * @static
     *
     * @param int $entry_id
     *
     * @return mixed The entry dir on succes, or false or failure.
     */
    public static function createEntryDir($entry_id)
    {
        if (!$entry_id)
            return false;
            
        // Set the target directory.
        $entry_dir = JPATH_ROOT  . "/components/com_rsdirectory/files/entries/$entry_id";
            
        // Create the target directory if it does not exist.
        if ( !file_exists($entry_dir) && is_writable(JPATH_ROOT . '/components/com_rsdirectory/files/entries') )
        {
            if ( mkdir($entry_dir) )
            {
                // Put an index.html file to prevent snooping around.
                if ( file_put_contents("$entry_dir/index.html", '<html><body bgcolor="#FFFFFF"></body></html>') !== false )
                    return $entry_dir;
            }
                
            return false;
        }
            
        return $entry_dir;
    }
        
    /**
     * Get dependencies.
     *
     * @access public
     *
     * @static
     *
     * @param int $field_id
     * @param int $parent_id
     * @param mixed $value
     *
     * @return mixed
     */
    public static function getDependencies($field_id, $parent_id = 0, $value = null)
    {
        if (!$field_id && !$parent_id)
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_fields_dependencies') );
                
        if ($field_id)
        {
            $query->where( $db->qn('field_id') . ' = ' . $db->q($field_id) );
        }
            
        if ($parent_id)
        {
            $query->where( $db->qn('parent_id') . ' = ' . $db->q($parent_id) );
        }
            
        if ( !is_null($value) )
        {
            $query->where( $db->qn('value') . ' = ' . $db->q($value) );
        }
            
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Check wether a field is a dependency parent for other fields.
     *
     * @access public
     *
     * @static
     *
     * @param int $field_id
     *
     * @return bool
     */
    public static function isDependencyParent($field_id)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_fields_properties') )
               ->where( $db->qn('property_name') . ' = ' . $db->q('dependency') )
               ->where( $db->qn('value') . ' = ' . $db->q($field_id) );
                
        $db->setQuery($query);
        return (bool)$db->loadResult();
    }
        
    /**
     * Remove the elements of the array that are not integers.
     *
     * @access public
     *
     * @static
     *
     * @param array $array
     * @param bool $reset_keys
     * @param bool $remove_empty Remove the elements whose values are empty.
     *
     * @return mixed
     */
    public static function arrayInt($array, $reset_keys = true, $remove_empty = false)
    {
        if ( !is_array($array) || !$array )
            return array();
            
        foreach ($array as $key => $value)
        {
            if ( !is_numeric($value) || ( $remove_empty && empty($value) ) )
            {
                unset($array[$key]);
            }
        }
            
        return array_values($array);
    }
        
    /**
     * Find a category in a hierarchical list of categories.
     *
     * @access public
     *
     * @static
     *
     * @param int $category_id
     * @param array $categories
     * @param object &$category
     */
    public static function findCategory($category_id, $categories, &$category)
    {
        if ( !$category_id || !$categories || !is_array($categories) )
            return;
            
        foreach ($categories as $cat)
        {
            if ($cat->id == $category_id)
            {
                $category = $cat;
                break;
            }
                
            if ( $children = $cat->getChildren() )
            {
                self::findCategory($category_id, $children, $category);
            }
        }
    }
        
    /**
     * Check if an user exists.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $condition An integer (id) or an array of column => value pairs.
     */
    public static function userExists($condition)
        {
        if ( !is_array($condition) )
        {
            $condition = array('id' => $condition);
        }
            
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__users') );
               
        foreach ($condition as $column => $value)
        {
            $query->where( $db->qn($column) . ' = ' . $db->q($value) );
        }
            
        $db->setQuery($query, 0, 1);
            
        return (bool)$db->loadResult();
    }
        
    /**
     * SEF.
     *
     * @access public
     *
     * @static
     *
     * @param int $id
     * @param string $name
     *
     * @return string
     */
    public static function sef($id, $name)
    {
        return intval($id) . ':' . JFilterOutput::stringURLSafe($name);
    }
        
    /**
     * Get review HTML.
     *
     * @access public
     *
     * @static
     *
     * @param object $review
     *
     * @return string
     */
    public static function getReviewHTML($review)
    {
        static $user;
        static $enable_owner_reply;
        static $config;
            
        if ( is_null($user) )
        {
            $user = JFactory::getUser();
        }
            
        if ( is_null($enable_owner_reply) )
        {
            $enable_owner_reply = RSDirectoryConfig::getInstance()->get('enable_owner_reply');
        }
            
        if ( is_null($config) )
        {
            $config = RSDirectoryConfig::getInstance();
        }
        
        $email = $review->author_email ? $review->author_email : $review->email;
        $name = $review->author_name ? $review->author_name : $review->name;
           
        $str = '<div class="rsdir-review media" data-review-id="' . $review->id . '">';
        $str .= '<img class="thumbnail pull-left media-object" src="http://gravatar.com/avatar/' . hash('md5', $email) . '" alt="' . self::escapeHTML($name) . '" />';
		$str .= '<div class="media-body">';
		$str .= '<h5 class="media-heading">' . self::escapeHTML($review->subject) . '</h5>';
		$str .= '<div class="rsdir-detail-rating-wrapper control-group">';
            
		$created_time = self::formatDate($review->created_time);
			
        if ( trim($created_time) )
        {
            if ( RSDirectoryConfig::getInstance()->get('date_and_time_display') == 'human_readable' )
            {
                $str .= JText::sprintf( 'COM_RSDIRECTORY_LISTING_META_AUTHOR_TIME_SINCE', self::escapeHTML($name), $created_time );
            }
            else
            {
                $str .= JText::sprintf( 'COM_RSDIRECTORY_LISTING_META_AUTHOR_TIME', self::escapeHTML($name), $created_time );
            }
        }
        else
        {
            $str .= JText::sprintf( 'COM_RSDIRECTORY_LISTING_META_AUTHOR_NOW', self::escapeHTML($name) );
        }
			
        if ($review->score)
        {
            $str .= ' | ' . JText::_('COM_RSDIRECTORY_RATING_LABEL');
            $str .= '<div class="rsdir-detail-rating" data-rating="' . self::escapeHTML($review->score) . '"></div>';
        }
        
        $str .= '</div>'; // .rsdir-detail-rating-wrapper
            
        $str .= '<p class="rsdir-review-body">' . nl2br( self::escapeHTML($review->review) ) . '</p>';
            
        if ( trim($review->owner_reply) )
        {
            $str .= self::getOwnerReplyHTML($review->owner_reply);  
        }
            
        if ($enable_owner_reply && $user->id == $review->entry_author_id)
        {
            $url = RSDirectoryRoute::getURL('ownerreply', '', "review_id=$review->id&tmpl=component");
                
            $str .= '<div class="clearfix">';
            $str .= '<a class="rsdir-edit-owner-reply btn pull-right" data-iframe-src="' . $url . '">' . JText::_( trim($review->owner_reply) ? 'COM_RSDIRECTORY_EDIT_OWNER_REPLY' : 'COM_RSDIRECTORY_ADD_OWNER_REPLY' ) . '</a>';
            $str .= '</div>';
        }
            
        $str .= '</div>'; // .media-body
            
        $str .= '</div>'; // .media
            
        $str .= '<hr class="rsdir-sep" />';
            
        return $str;
    }
        
    /**
     * Get owner reply HTML.
     *
     * @access public
     *
     * @param string $owner_reply
     *
     * @return string
     */
    public static function getOwnerReplyHTML($owner_reply)
    {
        $str = '<div class="rsdir-owner-reply alert alert-info">';
        $str .= '<p><strong>' . JText::_('COM_RSDIRECTORY_OWNER_REPLY') . '</strong></p>';
        $str .= $owner_reply;
        $str .= '</div>';
            
        return $str;
    }
        
    /**
     * Get rating HTML.
     *
     * @access public
     *
     * @static
     *
     * @param int $avg_rating
     * @param int $ratings_count
     *
     * @return string
     */
    public static function getRatingHTML($avg_rating, $ratings_count)
    {
        $str = '<div class="rsdir-detail-rating-wrapper rsdir-entry-rating rsdir-detail-section row-fluid">';
        $str .= JText::_('COM_RSDIRECTORY_RATING_LABEL');
        $str .= '<div class="rsdir-detail-rating" data-rating="' . $avg_rating . '"></div>';
            
        if ($avg_rating)
        { 
            $str .= JText::sprintf( 'COM_RSDIRECTORY_AVERAGE_RATING_VALUE', number_format($avg_rating, 1), $ratings_count );
        }
            
        $str .= '</div>';
            
        return $str;
    }
        
    /**
     * Build the entries filtering query.
     *
     * @access public
     *
     * @static
     *
     * @param array $fields
     * @param array $filters
     * @param object &$query
     */
    public static function buildEntriesFilteringQuery($fields, $filters, &$query)
    {
        if (!$fields || !$filters || !$query)
            return;
            
        $db = JFactory::getDbo();
            
        foreach ($filters as $form_field_name => $values)
        {
            if ( is_string($values) && $values === '' )
                continue;
                
            $field = RSDirectoryHelper::findElements( array('form_field_name' => $form_field_name), $fields );
                
            if (!$field)
                continue;
                
            $table = $field->create_column ? 'ec' : 'e';
                
            if ($field->field_type == 'map')
            {
                $column_name = $db->qn("$table.{$field->column_name}_address");
            }
            else
            {
                $column_name = $db->qn("$table.$field->column_name");	
            }
                
            $searchable_advanced = $field->properties->get('searchable_advanced');
                
            // Process the range filter.
            if ($searchable_advanced == 'range')
            {
                // Proceed if everything's ok.
                if ( $values && is_array($values) )
                {
                    unset($from);
                    unset($to);
                        
                    // Initialize the ranges conditions array.
                    $ranges_cond = array();
                        
                    $column_cast = "CAST( $column_name AS DECIMAL(10, 6) )";
                        
                    foreach ($values as $range)
                    {
                        $range = trim($range);
                            
                        // Less than.
                        if ( strpos($range, 'lt') !== false )
                        {
                            $range = trim( str_replace('lt-', '', $range) );
                                
                            if ( !is_numeric($range) )
                                continue;
                                
                            $ranges_cond[] = "$column_cast <= CAST( " . $db->q($range) . " AS DECIMAL(10, 6) )";
                        }
                        // Greater than.
                        else if ( strpos($range, 'gt') !== false )
                        {
                            $range = trim( str_replace('gt-', '', $range) );
                                
                            if ( !is_numeric($range) )
                                continue;
                                
                            $ranges_cond[] = "$column_cast >= CAST( " . $db->q($range) . " AS DECIMAL(10, 6) )";
                        }
                        // Custom range.
                        else if ( strpos($range, 'from') !== false || strpos($range, 'to') !== false )
                        {
                            // From.
                            if ( strpos($range, 'from-') !== false )
                            {
                                $range = trim( str_replace('from-', '', $range) );
                                    
                                if ( is_numeric($range) )
                                {
                                    $from = $range;
                                }
                            }
                                
                            // To.
                            if ( strpos($range, 'to-') !== false )
                            {
                                $range = trim( str_replace('to-', '', $range) );
                                    
                                if ( is_numeric($range) )
                                {
                                    $to = $range;
                                }
                            }
                        }
                        // Normal range i - j.
                        else
                        {
                            $range = explode('-', $range);
                                
                            foreach ($range as &$v)
                            {
                                $v = trim($v);
                            }
                                
                            if ( isset($range[2]) || !is_numeric($range[0]) || !is_numeric($range[1]) )
                                continue;
                                
                            if ($range[0] > $range[1])
                            {
                                $ranges_cond[] = "( $column_cast >= CAST( " . $db->q($range[1]) . " AS DECIMAL(10, 6) ) AND $column_cast <= CAST( " . $db->q($range[0]) . " AS DECIMAL(10, 6) ) )";
                            }
                            else
                            {
                                $ranges_cond[] = "( $column_cast >= CAST( " . $db->q($range[0]) . " AS DECIMAL(10, 6) ) AND $column_cast <= CAST( " . $db->q($range[1]) . " AS DECIMAL(10, 6) ) )";
                            }
                        }
                    }
                        
                    if ( isset($from, $to) )
                    {
                        if ($from > $to)
                        {
                            $aux = $from;
                            $from = $to;
                            $to = $aux;
                        }
                            
                        $ranges_cond[] = "( $column_cast >= CAST( " . $db->q($from) . " AS DECIMAL(10, 6) ) AND $column_cast <= CAST( " . $db->q($to) . " AS DECIMAL(10, 6) ) )";
                    }
                    else if ( isset($from) )
                    {
                        $ranges_cond[] = "$column_cast >= CAST( " . $db->q($from) . " AS DECIMAL(10, 6) )";
                    }
                    else if ( isset($to) )
                    {
                        $ranges_cond[] = "$column_cast <= CAST( " . $db->q($to) . " AS DECIMAL(10, 6) )";
                    }
                        
                    if ($ranges_cond)
                    {
                        $query->where( '(' . implode(' OR ', $ranges_cond) . ')' );
                    }
                }
            }
            else if ($searchable_advanced == 'date_range')
            {
                if ( !empty($values['from']) )
                {
                    $query->where( $column_name . ' >= STR_TO_DATE(' . $db->q( JFactory::getDate($values['from'])->toSql() ) . ', ' . $db->q('%Y-%m-%d %H:%i:%s') . ')' );
                }
                    
                if ( !empty($values['to']) )
                {
                    $query->where( $column_name . ' <= STR_TO_DATE(' . $db->q( JFactory::getDate($values['to'])->toSql() ) . ', ' . $db->q('%Y-%m-%d %H:%i:%s') . ')' );
                }
            }
            else if ($searchable_advanced == 1)
            {
                if ( !in_array( $field->field_type, array('images', 'image_upload', 'fileupload') ) )
                    continue;
                    
                $alias = "fr$field->id";
                    
                $query->innerJoin( $db->qn('#__rsdirectory_uploaded_files_fields_relations', $alias) . ' ON ' . $db->qn("$alias.entry_id") . ' = ' . $db->qn('e.id') . ' AND ' . $db->qn("$alias.field_id") . ' = ' . $db->q($field->id) );
            }
            else
            {
                // Initialize the condition array.
                $cond = array();
                    
                if ( !is_array($values) )
                {
                    $values = (array)$values;
                }
                    
                foreach ($values as $value)
                {
                    if ( is_string($value) && $value === '' )
                        continue;
                        
                    $cond_str = $column_name;
                        
                    switch ( $field->properties->get('searchable_advanced_condition_type', 'strict') )
                    {
                        case 'starts_with':
                                
                            $cond_str .= ' LIKE ' . $db->q( $db->escape($value, true) . '%' );
                                
                            break;
                                
                        case 'ending_with':
                                
                            $cond_str .= ' LIKE ' . $db->q( '%' . $db->escape($value, true) );
                                
                            break;
                                
                        case 'containing':
                                
                            $cond_str .= ' LIKE ' . $db->q( '%' . $db->escape($value, true) . '%' );
                                
                            break;
                                
                        default:
                                
                            $cond_str .= ' LIKE ' . $db->q($value);
                                
                            break;
                    }
                        
                    $cond[] = $cond_str;
                }
                    
                if ($cond)
                {
                    $query->where( '(' . implode(' OR ', $cond) . ')' );
                }
            }
        }
    }
        
    /**
     * Add an entry id to a list of recently visited entries and save the array in a cookie.
     *
     * @access public
     *
     * @static
     *
     * @param int $id
     */
    public static function addRecentlyVisited($id)
    {
        if ( !is_numeric($id) )
            return;
            
        $conf = JFactory::getConfig();
            
        $name = 'rsdir_recently_visited';
        $expire = JFactory::getDate()->toUnix() + 7776000; // Remember for 90 days.
        $domain = $conf->get('config.cookie_domain', '');
		$path = $conf->get('config.cookie_path', '/');
            
        // Values as stored like this: id,id,id,id
        $values = explode( ',', JFactory::getApplication()->input->cookie->getString($name) );
        $values = self::arrayInt($values, true, true);
          
        // Delete the value if it already exists in the array to update it's position. 
        if ( ( $key = array_search($id, $values) ) !== false )
        {
            unset($values[$key]);
        }
            
        $values[] = $id;
            
        // Get the last 10 entries.
        $values = array_slice($values, -10);
            
		setcookie( $name, implode(',', $values), $expire, $path, $domain );
    }
        
    /**
     * Return the HTML code for a cached file table row.
     *
     * @access public
     *
     * @static
     *
     * @param object $file
     * @param int $i
     *
     * @return string
     */
    public static function getBackupCachedFileRowHTML($file, $i = 0)
    {
        $str = '<tr>' . 
                '<td><input class="cached-file" type="checkbox" value="' . $file->hash . '" /></td>' .
                '<td>' . $i . '</td>' .
                '<td><a href="' . $file->url . '">' . $file->name . '</a></td>' .
                '<td>' . $file->date . '</td>' .
                '<td><button class="restore-cache btn" data-path="' . self::escapeHTML($file->path) . '">' . JText::_('COM_RSDIRECTORY_RESTORE') . '</button></td>' .
                '</tr>';
                
        return $str;
    }
        
    /**
     * Trim data.
     *
     * @access public
     *
     * @static
     *
     * @param string $data
     * @param string $character_mask
     *
     * @return mixed
     */
    public static function trim($data)
    {
        if ( is_string($data) )
        {
            return trim($data);
        }
            
        return array_map( array('self', 'trim'), $data );
    }
        
    /**
	 * Method to get an array formed of a single column from an array.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param array $items
	 * @param string $column
	 *
	 * @return array
	 */
	public static function getColumn($items, $column)
	{
		$results = array();
			
		if ( $items && is_array($items) )
		{
			foreach ($items as $item)
			{
				if ( is_array($item) && isset($item[$column]) )
				{
					$results[] = $item[$column];
				}
				else if ( is_object($item) && isset($item->$column) )
				{
					$results[] = $item->$column;
				}
			}
		}
			
		return $results;
	}
        
    /**
     * Method to generate a hierarchy tree from an array.
     *
     * @access public
     *
     * @static
     *
     * @param array $array
     * @param int $parent_id
     * @param string $pk The name of the primary key.
     * @param string $ppk The name of the parent key.
     *
     * @return array
     */
    public static function arrayToTree($array, $parent_id = 0, $pk = 'id', $ppk = 'parent_id')
    {
        $results = array();
            
        foreach ($array as $i => $v)
        {
            if ( is_array($v) && $v[$ppk] == $parent_id )
            {
                $results[$i] = $v; 
                $results[$i]['children'] = self::arrayToTree($array, $v[$pk], $pk, $ppk);
            }
            else if ( is_object($v) && $v->$ppk == $parent_id )
            {
                $results[$i] = $v; 
                $results[$i]->children = self::arrayToTree($array, $v->$pk, $pk, $ppk);
            }
        }
            
        return $results;
    }
        
    /**
     * Method to print human-readable information about a variable inside a <pre> tag.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $expression
     */
    public static function print_r($expression)
    {
        echo '<pre>';
        print_r($expression);
        echo '</pre>';
    }
        
    /**
     * Method to check if a file is an image.
     *
     * @access public
     *
     * @static
     *
     * @param string $file_path
     *
     * @return bool
     */
    public static function isImage($file_path)
    {
        if ( empty($file_path) || !file_exists($file_path) || !is_readable($file_path) )
            return false;
            
        $return = false;
            
        if ( function_exists('exif_imagetype') )
        {
            @$return = (bool)exif_imagetype($file_path);
        }
        else if ( function_exists('finfo_open') )
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                
            if ( $mime = finfo_file($finfo, $file_path) )
            {
                if ( substr($mime, 0, strpos($mime, '/') ) == 'image' )
                {
                    $return = true;
                }
            }
                
            finfo_close($finfo);
        }
        else if ( function_exists('getimagesize') )
        {
            @$size = getimagesize($file_path);
                
            if ( !empty($size['mime']) )
            {
                if ( substr($size['mime'], 0, strpos($size['mime'], '/') ) == 'image' )
                {
                    $return = true;
                }
            }
        }
        else if ( function_exists('mime_content_type') )
        {
            if ( $mimetype = mime_content_type($file_path) )
            {
                if ( substr($mimetype, 0, strpos($mimetype, '/') ) == 'image' )
                {
                    $return = true;
                }
            }
        }
            
        return $return;
    }
        
    /**
     * Method to get tooltip text based on the Joomla! version.
     *
     * @access public
     *
     * @static
     *
     * @param string $text
     *
     * @return string
     */
    public static function getTooltipText($text)
    { 
        if ( self::isJoomlaCompatible('3.1.2') )
        {
            $arr = explode('::', $text);
                
            $title = empty($arr[0]) ? '' : $arr[0];
            $content = empty($arr[1]) ? '' : $arr[1];
                
            return JHtml::tooltipText($title, $content, 0, 0);
        }
            
        return $text;
    }
        
    /**
     * Method to get tooltip class based on the Joomla! version.
     *
     * @access public
     *
     * @static
     *
     * @return string
     */
    public static function getTooltipClass()
    {
        static $class;
            
        if (!$class)
        {
            if ( self::isJoomlaCompatible('3.1.2') )
            {
                JHtml::_('bootstrap.tooltip');
                $class = 'hasTooltip';
            }
            else
            {
                JHtml::_('behavior.tooltip');
                $class = 'hasTip';
            }
        }
            
        return $class;
    }
        
    /**
     * Method to calculate the difference betweem two dates.
     *
     * @access public
     *
     * @param string $date1
     * @param string $date2
     * @param int $divisor
     *
     * @return int
     */
    public static function getDateDiff($date1, $date2, $divisor = 1)
    {
        return round( abs( strtotime($date1) - strtotime($date2) ) / ($divisor ? $divisor : 1) );
    }
        
    /**
     * Quote an array of values.
     *
     * @access public
     *
     * @static
     *
     * @param array $array
     *
     * @return string
     */
    public static function quoteImplode($array)
    {
		$db = JFactory::getDbo();
            
		foreach ($array as &$value)
        {
			$value = $db->q($value);
		}
            
		return implode(',', $array);
	}
}