<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * Reasons options field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldReasons extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'reasons';
        
    /**
     * Method to get the field options.
     *
     * @access protected
     * 
     * @return array The field option objects.
     */
    protected function getOptions()
    {
		$reasons = RSDirectoryHelper::getOptions( RSDirectoryConfig::getInstance()->get('reporting_reasons') );
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $reasons );
            
        return $options;
    }
}