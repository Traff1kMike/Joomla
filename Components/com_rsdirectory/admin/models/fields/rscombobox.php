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
 * Implements a combo box field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldRSCombobox extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 *
	 * @access public
	 */
	public $type = 'RSCombobox';
		
	/**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @access protected
	 * 
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
			
		// Initialize some field attributes.
		$attr .= ' class="rsdir-combobox' . (  $this->element['class'] ?  ' ' . (string) $this->element['class'] : '' ) . '"';
		$attr .= (string) $this->element['readonly'] == 'true' ? ' readonly="readonly"' : '';
		$attr .= (string) $this->element['disabled'] == 'true' ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
			
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
			
		// Get the field options.
		$options = $this->getOptions();
			
		// Build the list for the combo box.
		$html[] = '<select id="rscombobox-' . $this->id . '" class="rscomboboxoptions" size="1" onchange="jQuery(\'#'.$this->id.'\').val(this.value).keyup();">';
			
		foreach ($options as $option)
		{
			$html[] = '<option value="'. $option->value .'"' . ($option->value == $this->value ? ' selected="selected"' : '') . '>' . $option->text . '</option>';
		}
			
		$html[] = '</select>';
			
		// Build the input for the combo box.
		$html[] = '<input id="' . $this->id . '"' . ' type="text" name="' . $this->name . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $attr . '/>';
			
		return implode($html);
	}
}
