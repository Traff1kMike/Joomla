<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

// Load the RSDirectory! Plugin helper class file if it exists or stop the execution of the script if it doesn't exist.
if ( file_exists(JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/plugin.php') )
{
    require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/plugin.php';
}
else
{
    return;
}

/**
 * RSDirectory! Wire Transfer Payment Plugin.
 */
class plgSystemRSDirectoryWireTransfer extends RSDirectoryPlugin
{
    const PAYMENT_METHOD = 'wiretransfer';
    const PLUGIN = 'rsdirectorywiretransfer';
    const EXTENSION = 'plg_system_rsdirectorywiretransfer';
    const OPTION = 'PLG_SYSTEM_RSDIRECTORYWIRETRANSFER_NAME';
        
    /**
     * Class constructor.
     * 
     * @access public
     * 
     * @param object &$subject
     * @param array $config
     */
    public function __construct(&$subject, $config)
    {
		parent::__construct($subject, $config);
			
		$app = JFactory::getApplication();
    }
        
    /**
     * Can the plugin run?
     *
     * @access public
     * 
     * @return bool
     */
    public function canRun()
    {
        return JPluginHelper::isEnabled('system', self::PLUGIN);
    }
     
    /**
     * Add the current payment option to the payments list.
     *
     * @access public
     * 
     * @return string
     */
    public function rsdirectory_addPaymentOptions()
    {
		$lang = JFactory::getLanguage();
		$lang->load(self::EXTENSION, JPATH_ADMINISTRATOR);
			
		if ( !$this->canRun() )
			return;
			
		$tax_text = '';
			
        if ( $tax_value = $this->params->get('tax_value') )
		{
			if ( $this->params->get('tax_type') )
			{
				$tax_text = "$tax_value%";
			}
			else
			{
				$tax_text = RSDirectoryHelper::formatPrice($tax_value);
			}
		}
			
		return (object)array(
			'value' => self::PAYMENT_METHOD,
			'text' => JText::_(self::OPTION),
			'tax_text' => $tax_text,
		);
    }
        
    /**
     * Show payment form.
     *
     * @access public
     * 
     * @param object $vars
     */
    public function rsdirectory_showForm($vars)
    {
		// Do a few checks and exit the function if the conditions are not met.
		if ( !$this->canRun() )
			return;
			
			
		if ( isset($vars->method) && $vars->method == self::PAYMENT_METHOD )
		{
			$lang = JFactory::getLanguage();
			$lang->load(self::EXTENSION, JPATH_ADMINISTRATOR);
			$lang->load('com_rsdirectory', JPATH_SITE);
				
			self::outputTransactionDetails($vars);
				
			echo '<hr class="rsdir-sep">';
				
			echo $this->params->get('details');
		}
    }
		
	/**
     * Get taxa data.
     *
     * @access public
     *
     * @param object $vars
     *
     * @return array
     */
    public function rsdirectory_GetTaxData($vars)
    {
        if ( !$this->canRun() || !isset($vars->method) || $vars->method != self::PAYMENT_METHOD )
            return;
            
        // Calculate the tax.
        $tax_type = $this->params->get('tax_type');
		$tax_value = $this->params->get('tax_value');
        $tax = $tax_value && $tax_type ? $vars->price * ($tax_value/100) : $tax_value;
            
        $data = array(
            'tax_type' => $tax_type ? 'percent' : 'fixed',
            'tax_value' => $tax_value,
            'tax' => $tax,
        );
			
		return $data;
    }
}