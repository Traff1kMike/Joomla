<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$rsfieldset = $this->rsfieldset;
$rstabs = $this->rstabs;
$form = $this->form;

?>

<script type="text/javascript">
        
    Joomla.submitbutton = function(task)
    {
        if ( task == 'field.cancel' || document.formvalidator.isValid( document.id('adminForm') ) )
        {
            Joomla.submitform( task, document.getElementById('adminForm') );
        }
    }
        
    <?php if ( in_array( $this->field_type->type, array('calendar', 'dropdown_date_picker')) ) { ?>
        
    jQuery(function($)
    {
        date_mask = $('.date-mask');
        time_mask = $('.time-mask');
            
        if ( date_mask.val() == 'human_readable' )
        {
            time_mask.parents('.control-group').addClass('hide');
        }
            
        date_mask.keyup(function()
        {
            if ( date_mask.val() == 'human_readable' )
            {
                time_mask.parents('.control-group').addClass('hide');
            }
            else
            {
                time_mask.parents('.control-group').removeClass('hide');
            }
        });
    });
        
    <?php } ?>
</script>

<div class="rsdir">
    <form id="adminForm" class="form-validate" name="adminForm" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=field&layout=edit&id=' . ( (int)$this->id ) . ($this->id ? '' : "&field_type_id={$this->field_type->id}") ); ?>" method="post">
            
        <?php        
            
        foreach ($this->fieldsets as $name => $fieldset)
        {
            // Add tab title.
            $rstabs->addTitle($fieldset->label, $name);
                
            $fields = $form->getFieldset($name);
                
            $content = $rsfieldset->getFieldsetStart();
                
            if ($name == 'general')
            {
                if ($this->field_type->type == 'section_break')
                {
                    $break_type = RSDirectoryConfig::getInstance()->get('break_type');
                        
                    $field_type = JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($break_type) );
                }
                else
                {
                    $field_type = JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($this->field_type->type) );
                }
                    
                $label = '<label>' . JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_TYPE_LABEL') . '</label>';
                $input = '<input type="text" value="' . $field_type . '" readonly="readonly" />';
                    
                $content .= $rsfieldset->getField($label, $input);
            }
                
            foreach ($fields as $field)
            {
                // Skip the id and field_type_id fields.
                if ( in_array( $field->fieldname, array('id', 'field_type_id') ) )
                    continue;
                    
                // Load the publishing_period template.
                if ($this->field_type->type == 'publishing_period' && $field->fieldname == 'items')
                {
                    $this->field = $field;
                    $content .= $this->loadTemplate('publishing_period');
                        
                    continue;    
                }
                    
                $input = $field->input;
                    
                switch ($field->fieldname)
                {
                    // Add the map element after the default longitude field.
                    case 'default_lng':
                            
                        $input .= '<div id="rsdir-field-map"></div>';
                            
                        break;
                            
                    // Add a description after the searchable_advanced_items field.
                    case 'searchable_advanced_items':
                            
                        $input .= '<div class="help-block">' . JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_SEARCHABLE_ADVANCED_ITEMS_DESC') . '</div>';
                            
                        break;
                            
                    // Add a description after the maximum_file_size field.
                    case 'maximum_file_size':
                            
                        $input .= '<div class="help-block">' . JText::sprintf('COM_RSDIRECTORY_SERVER_MAXIMUM_FILE_SIZE', '<strong>' . (int)ini_get('upload_max_filesize') . 'M</strong>') . '</div>';
                            
                        break;
                        
                    // Add a description after the max_files_number field.
                    case 'max_files_number':
                            
                        $input .= '<div class="help-block">' . JText::sprintf('COM_RSDIRECTORY_SERVER_MAXIMUM_FILES_NUMBER', '<strong>' . (int)ini_get('max_file_uploads') . '</strong>') . '</div>';
                            
                        break;
                }
                    
                $options = array();
                    
                if ($this->field_type->type == 'dropdown')
                {
                    if ($this->is_dependency_parent)
                    {
                        if ( in_array( $field->fieldname, array('default_values', 'multiple') ) )
                        {
                            $options = array('hide' => true);
                        }
                    }
                    else if ($field->fieldname == 'default_value')
                    {
                        $options = array('hide' => true);
                    }
                }
                    
                if ($field->fieldname == 'dependency')
                {
                    $input .= ' <img class="rsdir-loader hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" width="16" height="11" alt="" />';;
                }
                    
                if ($field->hidden)
                {
                    $content .= $input;
                }
                else
                {
                    $content .= $rsfieldset->getField($field->label, $input, $options);
                }
                    
                // Display the dependency items fields.
                if ( in_array($this->field_type->type, $this->dependency_compatible) && $field->fieldname == 'items' && !empty($this->dependencies) )
                {
                    foreach ($this->dependencies as $dependency)
                    {
                        $base64 = base64_encode($dependency->value);
                            
                        $id_value = preg_replace( '/^[^a-z0-9]+/', '', strtolower($base64) );
                        $id_value = preg_replace('/[^a-z0-9]+$/', '', $id_value);
                        $id_value = preg_replace('/[^a-z0-9\-]+/', '', $id_value);
                            
                        $textarea_id = "jform_items_{$dependency->parent_id}_{$id_value}";
                        $textarea_name = "jform[items][$dependency->parent_id][" . $base64 . "]";
                            
                        $textarea = '<textarea id="' . $textarea_id . '" class="input-xxlarge" name="' . $textarea_name . '" rows="8">' . $this->escape($dependency->items) . '</textarea>';
                            
                        $items_field = $rsfieldset->getField($field->label, $textarea);
                            
                        $items_field = str_replace('control-group', 'control-group hide', $items_field);
                            
                        $content .= $items_field;
                    }
                }
            }
                
            $content .= $rsfieldset->getFieldsetEnd();
                
            // Add tab content.
            $rstabs->addContent($content);
        }
            
        // Render tabs.
        $rstabs->render();
            
        ?>
            
        <div>
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="jform[id]" value="<?php echo $this->id; ?>" />
            <input type="hidden" name="jform[field_type_id]" value="<?php echo $this->field_type->id; ?>" />
        </div>
    </form>
</div>