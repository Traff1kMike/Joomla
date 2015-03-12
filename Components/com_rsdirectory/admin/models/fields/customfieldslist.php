<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Custom fields list.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldCustomFieldsList extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'customfieldslist';
        
    /**
     * Method to get the field input markup.
     *
     * @access protected
     * @return string The field input markup.
     */
    protected function getInput()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Get the form id.
        $form_id = JFactory::getApplication()->input->getInt('id');
            
        $where = array(
            $db->qn('ff.form_id') . ' = ' . $db->q($form_id),
            $db->qn('ft.core') . ' = ' . $db->q(0),
            $db->qn('ft.expect_value') . ' = ' . $db->q(1),
            $db->qn('ft.create_column') . ' = ' . $db->q(1),
        );
            
        $query = $db->getQuery(true)
               ->select( $db->qn('f') . '.*' )
               ->from( $db->qn('#__rsdirectory_fields', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_forms_fields', 'ff') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('ff.field_id') )
               ->innerJoin( $db->qn('#__rsdirectory_field_types', 'ft') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ft.id') )
               ->where($where);
                
        $db->setQuery($query);
            
        $results = $db->loadObjectList();
            
        if ($results)
        {
            $table = RSDirectoryHelper::getTableStructure($results, 4, 'cols');
                
            $str = '<div class="row-fluid">';
                
            foreach ($table as $cells)
            {
                $str .= '<div class="span3">';
                    
                foreach ($cells as $form_field)
                {
                    if (!$form_field)
                        continue;
                        
                    $attrs = is_array($this->value) && in_array($form_field->id, $this->value) ? ' checked="checked"' : '';
                        
                    $str .= '<label class="checkbox">';
                        
                    $str .= '<input type="checkbox" name="' . $this->name . '" value="' . $form_field->id . '"' . $attrs . ' /> ';
                        
                    $str .= RSDirectoryHelper::escapeHTML($form_field->name);
                        
                    if (!$form_field->published)
                    {
                        $str .= ' <span class="label label-warning">' . JText::_('JUNPUBLISHED') . '</span>';
                    }
                        
                    $str .= '</label>';
                }
                    
                $str .= '</div>';
            }
                
            $str .= '</div>';
                
            return $str;
        }
        else
        {
            return JText::_('COM_RSDIRECTORY_NO_CUSTOM_FORM_FIELDS');
        }
    }
}