<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('editor');

/**
 * Forms RSEditor field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldRSEditor extends JFormFieldEditor
{
    /**
     * The form field type.
     *
     * @var string
     */
    public $type = 'rseditor';
        
    /**
	 * Method to get the field input markup for the editor area
	 *
	 * @return string The field input markup.
	 *
	 * @since  11.1
	 */
	protected function getInput()
	{
		return '<div style="clear: both;">' . parent::getInput() . '</div>';
	}
}