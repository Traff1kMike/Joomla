<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * RSDirectory! Configuration helper.
 */
class RSDirectoryConfig
{
    /**
     * The class instance.
     *
     * @var RSDirectoryConfig
     * 
     * @access private
     * 
     * @static
     */
    private static $instance;
            
    /**
     * The configuration object.
     *
     * @var object
     * 
     * @access private
     */
    private $config;
        
    /**
     * The class constructor.
     *
     * @access private
     */
    private function __construct()
    {
        $this->load();
    }
        
    /**
     * Get the class instance.
     *
     * @access public
     * 
     * @static
     * 
     * @return RSDirectoryConfig
     */
    public static function getInstance()
    {
        if ( !isset(self::$instance) )
        {
            self::$instance = new RSDirectoryConfig;
        }
            
        return self::$instance;
    }
        
    /**
     * Load the configuration from the database.
     *
     * @access private
     */
    private function load()
    {
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_config') );
                
        $db->setQuery($query);
            
        $config = new stdClass();
            
        if ( $results = $db->loadObjectList() )
        {
            foreach ($results as $result)
            {
                $config->{$result->name} = $result->value;
            }
        }
            
        // Get the Joomla! config object.
        $jconfig = JFactory::getConfig();
            
        $config->from_email = !empty($config->use_joomla_email_configuration) || empty($config->from_email) ?  $jconfig->get('mailfrom') : $config->from_email;
        
        $config->from_name = !empty($config->use_joomla_email_configuration) || empty($config->from_name) ?  $jconfig->get('fromname') : $config->from_name;
            
        $this->config = $config;
    }
        
    /**
     * Reload the configuration from the database.
     *
     * @access public
     */
    public function reload()
    {
        $this->load();
    }
        
    /**
     * Get settings keys.
     *
     * @access public
     * 
     * @return array
     */
    public function getKeys()
    {
        return array_keys( (array)$this->config);
    }
        
    /**
     * Get configuration object.
     *
     * @access public
     * 
     * @return array
     */
    public function getData()
    {
        return $this->config;
    }
        
    /**
     * Get setting value.
     *
     * @access public
     * 
     * @param string $key
     * @param mixed $default
     * @param bool $explode
     *
     * @return mixed
     */
    public function get($key, $default = false, $explode = false)
    {
        if ( isset($this->config->$key) )
        {
            return $explode ? self::explode($this->config->$key) : $this->config->$key;
        }
            
        return $default;
    }
        
    /**
     * Set setting value.
     *
     * @access public
     * 
     * @param string $key
     * @param string $value
     * 
     * @return bool
     */
    public function set($key, $value)
    {
        static $db;
            
        if ( empty($db) )
        {
            $db = JFactory::getDbo();
        }
            
        if ( empty($this->config->$key) || $this->config->$key !== $value )
        {  
            // Refresh our value.
            $this->config->$key = $value;
                
            // Array are converted to strings here.
            if ( is_array($value) )
            {
                $value = implode("\n", $value);
            }
             
            // Check if the value already exists.
            $query = $db->getQuery(true)
                   ->select('COUNT(*)')
                   ->from( $db->qn('#__rsdirectory_config') )
                   ->where( $db->qn('name') . '=' . $db->q($key) );
                   
            $db->setQuery($query);
                
            if ( $db->loadResult() )
            {
                // Update the existing value.
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_config') )
                       ->set( $db->qn('value') . '=' . $db->q($value) )
                       ->where( $db->qn('name') . '=' . $db->q($key) );
            }
            else
            {
                // Insert new value.
                $query = $db->getQuery(true)
                       ->insert( $db->qn('#__rsdirectory_config') )
                       ->set( $db->qn('name') . '=' . $db->q($key) . ', ' . $db->qn('value') . '=' . $db->q($value) );
            }
                
            $db->setQuery($query);
                
            // Execute the query.
            return $db->execute();
        }
            
        return false;
    }
        
    /**
     * Explode string.
     *
     * @access protected
     * 
     * @static
     * 
     * @param string $string
     */
    protected static function explode($string)
    {
        $string = str_replace( array("\r\n", "\r"), "\n", $string );
        return explode("\n", $string);
    }
}