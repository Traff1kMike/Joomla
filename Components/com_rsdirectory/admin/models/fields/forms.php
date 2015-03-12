<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('checkboxes');

/**
 * Forms checkboxes field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldForms extends JFormFieldCheckboxes
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'forms';
        
    /**
	 * Method to get the field options.
     *
     * @access protected
     *
	 * @return array The field option objects.
     */
	protected function getOptions()
    {
        $app = JFactory::getApplication();
        $jinput = $app->input;
            
        // Initialize the options array.
        $options = array();
            
        // Get the forms object list.
        if ( $forms = RSDirectoryHelper::getForms() )
        {
            // Initialize the disabled value.
            $disabled = false;
				
            if ( $app->isAdmin() && $jinput->get('view') == 'field' && $jinput->getInt('id') )
			{
				if ( empty($this->value) )
				{
					$db = JFactory::getDbo();
						
					$this->value = array();
						
					$query = $db->getQuery(true)
						   ->select( $db->qn('form_id') )
						   ->from( $db->qn('#__rsdirectory_forms_fields') )
						   ->where( $db->qn('field_id') . ' = ' . $db->q( $jinput->getInt('id') ) );
							
					$db->setQuery($query);
						
					$this->value = $db->loadColumn();
				}
					
				$field = RSDirectoryHelper::getField( $jinput->getInt('id') );
					
				if ( !empty($field->all_forms) )
				{
					$disabled = true;
				}
			}
				
            foreach ($forms as $form)
            {
                // Create a new option object based on the <option /> element.
                $option = JHtml::_('select.option', $form->id, $form->title, 'value', 'text', $disabled);
                    
				$option->class = 'checkbox';
				$option->checked = false;
                    
                if ( !empty($this->value) && in_array($form->id, $this->value) )
                {
                    $option->checked = true;
                }
                    
                // Add the option object to the result set.
                $options[] = $option;
            }
        }
            
		reset($options);
            
		return $options;
    }
}