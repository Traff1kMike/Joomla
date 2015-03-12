<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * RSDirectory! Plugin helper.
 */
class RSDirectoryPlugin extends JPlugin
{
    /**
     * Is Joomla 3.0+ ?
     *
     * @var bool
     * 
     * @access protected
     */
    private $is30;
        
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
            
        $jversion = new JVersion();
         
        // Is Joomla 3.0+ ?
        $this->isJ30  = $jversion->isCompatible('3.0');
    }
        
    /**
     * Output transaction details.
     *
     * @access public
     * 
     * @static
     */
    public static function outputTransactionDetails($vars)
    {
        ?>
        <fieldset class="rsdir-transaction-details form-horizontal">
            <legend><?php echo JText::_('COM_RSDIRECTORY_TRANSACTION_DETAILS'); ?></legend>
                
            <div class="control-group">
                <div class="control-label"><strong><?php echo JText::_('COM_RSDIRECTORY_PACKAGE'); ?>:</strong></div>
                <div class="controls">
                    <?php echo self::escape($vars->item_name); ?>
                </div>
            </div>
                
            <div class="control-group">
                <div class="control-label"><strong><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?>:</strong></div>
                <div class="controls">
                    <span class="label label-info"><?php
					if ($vars->credits)
					{
						echo JText::plural('COM_RSDIRECTORY_NUMBER_OF_CREDITS', $vars->credits);
					}
					else
					{
						echo JText::_('COM_RSDIRECTORY_UNLIMITED_CREDITS');
					}
					?></span>
                </div>
            </div>
                
            <?php if ( empty($vars->tax) ) { ?>
            <div class="control-group">
                <div class="control-label"><strong><?php echo JText::_('COM_RSDIRECTORY_COST'); ?>:</strong></div>
                <div class="controls">
                    <span class="label label-success"><?php echo self::escape($vars->formatted_price); ?></span>
                </div>
            </div>    
            <?php
            }
            else
            {
                ?>
                    
                <div class="control-group">
                    <div class="control-label"><strong><?php echo JText::_('COM_RSDIRECTORY_PRICE'); ?>:</strong></div>
                    <div class="controls">
                        <span class="label label-success"><?php echo self::escape($vars->formatted_price); ?></span>
                    </div>
                </div>
                    
                <div class="control-group">
                    <div class="control-label"><strong><?php echo JText::_('COM_RSDIRECTORY_TAX_VALUE'); ?>:</strong></div>
                    <div class="controls">
                        <span class="label label-success"><?php echo self::escape( RSDirectoryHelper::formatPrice($vars->tax) ); ?></span>
                    </div>
                </div>
                    
                <div class="control-group">
                    <div class="control-label"><strong><?php echo JText::_('COM_RSDIRECTORY_TOTAL_PRICE'); ?>:</strong></div>
                    <div class="controls">
                        <span class="label label-success"><?php echo self::escape( RSDirectoryHelper::formatPrice($vars->total) ); ?></span>
                    </div>
                </div>
                    
                <?php
            }
            ?>
        </fieldset>
        <?php
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
        return file_exists(JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php') && JFactory::getApplication()->isSite();
    }
        
    /**
     * Escape string.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $string
     * 
     * @return string
     */
    public static function escape($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
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
        return number_format($price, 2, '.', '');
    }
}