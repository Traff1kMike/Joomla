<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The Fields view.
 */
class RSDirectoryViewFieldTypes extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/modalitems.php';
            
        // Get the fields.
        $fields = $this->get('CustomFieldTypesObjectList');
            
        // Initialize the items array.
        $items = array();
            
        foreach ($fields as $field)
        {
            $items[] = array(
                'text' => $field->name,
                'onclick' => "window.parent.newField($field->id)",
            );
        }
            
        $options = array(
            'title' => JText::_('COM_RSDIRECTORY_SELECT_FIELD_TYPE'),
            'accordion' => 0,
            'groups' => array(
                array(
                    'accordion' => 0,
                    'items' => $items,
                ),
            ),
        );
            
        $this->rsmodalitems = RSModalItems::getInstance($options); 
            
        parent::display($tpl);
    }
}