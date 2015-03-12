<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


/**
 * Checkbox group field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldCheckboxGroup extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'checkboxgroup';
        
    /**
     * Method to get the field input markup.
     *
     * @access protected
     *
     * @return string The field input markup.
     */
    protected function getInput()
    {
        $str = '';
            
        foreach ( $this->element->children() as $option )
        {
            $attrs = '';
                
            if ( !empty($this->element['class']) )
            {
                $attrs .= ' class="' . $this->element['class'] . '"';
            }
                
            if ( !empty($this->element['name']) )
            {
                $attrs .= ' name="' . $this->name . '[]"';
            }
                
            $str .= '<label class="checkbox">';
                
            $str .= '<input type="checkbox" value="' . RSDirectoryHelper::escapeHTML($option['value']) . '"' . $attrs . ' /> ';
                
            $str .= JText::_($option);
                
            $str .= '</label>';
        }
            
        return $str;
    }
}