<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Fields List.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldFieldsList extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'fieldslist';
        
    /**
     * Method to get the field input markup.
     *
     * @access protected
     * 
     * @return string The field input markup.
     */
    protected function getInput()
    { 
        // Get DBO.
        $db = JFactory::getDbo();
            
        $select = array(
            $db->qn('f') . '.*',
            $db->qn('ft.all_forms'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_fields', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_field_types', 'ft') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ft.id') );
               
        $form_id = JFactory::getApplication()->input->getInt('id');
            
        // Keep the form's fields ordering if we are existing form.
        if ($form_id)
        {
            $query->leftJoin( $db->qn('#__rsdirectory_forms_fields', 'ff') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('ff.field_id') . ' AND ' . $db->qn('ff.form_id') . ' = ' . $db->q($form_id) );
                
            // Put the non-null values 1st and then the null values.
            $query->order( $db->qn('ff.ordering') . ' IS NULL, ' . $db->qn('ff.ordering') );
        }
            
        // Also order the fields by id.
        $query->order( $db->qn('f.id') );
            
        $db->setQuery($query);
            
        $results = $db->loadObjectList();
            
        $str = '';
            
        if ($results)
        {
            $table = RSDirectoryHelper::getTableStructure($results, 4, 'cols');
                
            $str .= '<div class="row-fluid">';
                
            foreach ($table as $cells)
            {
                $str .= '<div class="span3">';
                    
                foreach ($cells as $form_field)
                {
                    if (!$form_field)
                        continue;
                        
                    $attrs = ( is_array($this->value) && in_array($form_field->id, $this->value) ) || $form_field->all_forms ? ' checked="checked"' : '';
                        
                    $str .= '<label class="checkbox">';
                        
                    if ($form_field->all_forms)
                    {
                        $attrs .= ' disabled="disabled"';
                            
                        $str .= '<input type="hidden" name="' . $this->name . '" value="' . $form_field->id . '" /><input type="checkbox"' . $attrs . ' /> ';
                    }
                    else
                    {
                        $str .= '<input type="checkbox" name="' . $this->name . '" value="' . $form_field->id . '"' . $attrs . ' /> ';
                    }
                        
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
        }
            
        return $str;
    }
}