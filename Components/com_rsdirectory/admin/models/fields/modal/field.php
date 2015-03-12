<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Support a modal field picker.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldModal_Field extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'Modal_Field';
        
    /**
     * Method to get the field input markup.
     *
     * @access protected
     *
     * @return string The field input markup.
     */
    protected function getInput()
    {
        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'e.modal');
            
        // Build the script.
        $script = array();
        $script[] = 'function jSelectField_' . $this->id . '(id, name) {';
        $script[] = 'document.id("' . $this->id . '_id").value = id;';
        $script[] = 'document.id("' . $this->id . '_name").value = name;';
        $script[] = 'SqueezeBox.close();';
        $script[] = '}';
            
        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration( implode("\n", $script) );
            
        // Setup variables for display.
        $html = array();
        $link = 'index.php?option=com_rsdirectory&amp;view=fields&amp;layout=modal&amp;tmpl=component&amp;function=jSelectField_' . $this->id;
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('name') )
               ->from( $db->qn('#__rsdirectory_fields') )
               ->where( $db->qn('id') . ' = ' . $db->q($this->value) );
            
        $db->setQuery($query);
            
        try
        {
            $title = $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning( 500, $e->getMessage() );
        }
            
        if ( empty($title) )
        {
            $title = JText::_('COM_RSDIRECTORY_SELECT_A_FIELD');
        }
            
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            
        // The current user display field.
        $html[] = '<span class="input-append">';
        $html[] = '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" /><a class="modal btn" title="' . JText::_('COM_RSDIRECTORY_CHANGE_FIELD') . '"  href="' . $link . '&amp;' . JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . JText::_('JSELECT') . '</a>';
        $html[] = '</span>';
            
        // The active entry id field.
        $value = 0 == (int) $this->value ? '' : (int) $this->value;
            
        // class='required' for client side validation.
        $class = $this->required ? ' class="required modal-value"' : '';
            
        $html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';
            
        return implode("\n", $html);
    }
}
