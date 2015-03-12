<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Form field generator class.
 */
class RSDirectoryFormField
{
    /**
     * The form field object.
     *
     * @var object
     * 
     * @access protected
     */
    protected $form_field;
        
    /**
     * Entry data.
     *
     * @var mixed
     * 
     * @access protected
     */
    protected $entry;
        
    /**
     * Entry credits object list.
     *
     * @var array
     * 
     * @access protected
     */
    protected $entry_credits;
        
    /**
     * Attributes array.
     *
     * @var array
     *
     * @access protected
     */
    protected $attrs = array();
        
    /**
     * Have we initialized the form_fields javascript array?
     *
     * @var bool
     * 
     * @access protected
     */
    protected static $form_fields_init;
        
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param object $form_field
     * @param object $entry
     * @param array $entry_credits
     */
    public function __construct( $form_field, $entry = null, $entry_credits = array() )
    {
        $this->form_field = $form_field;
        $this->entry = $entry;
        $this->entry_credits = $entry_credits;
    }
        
    /**
     * Get RSDirectoryFormField instance.
     *
     * @access public
     * 
     * @static
     * 
     * @param object $form_field
     * @param object $entry
     * @param array $entry_credits
     * 
     * @return RSDirectoryFormField
     */
    public static function getInstance( $form_field, $entry = null, $entry_credits = array() )
    {
        $rsdirectoryformfield = new RSDirectoryFormField($form_field, $entry, $entry_credits);
            
        return $rsdirectoryformfield;
    }
        
    /**
     * Set an attribute, creating a new one if it doesn't exist already, or adding to the current attribute string if it does.
     *
     * @access protected
     *
     * @param string $key
     * @param string $value
     */
    protected function setAttr($key, $value)
    {
        if ( isset($this->attrs[$key]) )
        {
            if ( substr($this->attrs[$key], -1, 1) !== ' ' )
            {
                $this->attrs[$key] .= ' ';
            }
                
            $this->attrs[$key] .= $value;
        }
        else
        {
            $this->attrs[$key] = $value;
        }
    }
        
    /**
     * Set multiple attributes at once.
     *
     * @access protected
     *
     * @param array $data
     */
    protected function setAttrs($data)
    {
        if ( is_array($data) && $data )
        {
            foreach ($data as $key => $value)
            {
                $this->setAttr($key, $value);
            }
        }
    }
        
    /**
     * Get attribute.
     *
     * @access protected
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getAttr($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }
        
    /**
     * Get the attributes string.
     *
     * @access protected
     *
     * @return string
     */
    protected function getAttrsString()
    {
        $str = '';
            
        if ($this->attrs)
        {
            foreach ($this->attrs as $key => $value)
            {
                $str .= " $key=\"$value\"";
            }
        }
            
        return $str;
    }
        
    /**
     * Generate HTML code.
     *
     * @access public
     * 
     * @return string
     */
    public function generate()
    {  
        $form_field = $this->form_field;
        $entry = $this->entry;
        $entry_credits = $this->entry_credits;
        $properties = empty($form_field->properties) ? new JRegistry : $form_field->properties;
          
        if ( in_array( $form_field->field_type, array('section_break', 'captcha') ) )
            return;
            
        if ( $form_field->field_type == 'publishing_period' && !unserialize( $properties->get('items') ) )
            return;
            
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Get the JDocument object.    
        $doc = JFactory::getDocument();
            
        // Get the JInput object.
        $jinput = $app->input;
            
        // Get the posted data.
        $data = $app->getUserState('com_rsdirectory.edit.entry.data');
            
        // Is root user?
        $isRoot = JFactory::getUser()->get('isRoot');
            
        $can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
            
        $is_site = $app->isSite();
        $view = $jinput->get('view');
        $layout = $jinput->get('layout');
            
        // Check if there was an error with the form field.
        $error_field_ids = $app->getUserState('com_rsdirectory.edit.entry.error_field_ids');
        $has_error = is_array($error_field_ids) && in_array($form_field->id, $error_field_ids);
            
            
        // Get the field model.
        $field_model = RSDirectoryModel::getInstance('Field');
            
        // Get an array field types that can be used as dependency parents.
        $dependency_parent_compatible = $field_model->getDependencyParentCompatible();
            
        // Check if the field is a dependency parent.
        if ( in_array($form_field->field_type, $dependency_parent_compatible) )
        {
            $is_dependency_parent = RSDirectoryHelper::isDependencyParent($form_field->id);
        }
            
            
        // Initialize the field wrapper attributes array.
        $field_wrapper_attrs = array(
            'class' => "rsdir-field-wrapper rsdir-field-wrapper-$form_field->id control-group" . ($has_error ? ' error' : ''),
        );
            
        // Initialize the items array.
        $items = array();
            
        // Get dependency items.
        if ( $properties->get('dependency') )
        {
            if ( $dependency_options = $this->getDependencyOptions( $properties->get('dependency') ) )
            {
                $items = $dependency_options;
            }
        }
        else if ( $properties->get('items') )
        {
            // Set the select/checkbox/radio items.
            $items = RSDirectoryHelper::getOptions( $properties->get('items') );
        }
            
            
        // Set attributes.
        if ( $properties->get('size') )
        {
            $this->setAttr( 'size', $properties->get('size') );
        }
            
        if ( $properties->get('max_size') )
        {
            $this->setAttr( 'max_size', $properties->get('max_size') );
        }
            
        if ( $properties->get('additional_html_attributes') )
        {
            try {
                @$element = new SimpleXMLElement("<element " . $properties->get('additional_html_attributes') . " />");
                    
                foreach ( $element->attributes() as $key => $value )
                {
                    $this->setAttr($key, $value);
                }
            }
            catch (Exception $e)
            {
            }
        }
            
        $this->setAttr('class', 'rsdir-field');
            
            
        // Initialize the name attribute.
        $name_attr = "fields[$form_field->form_field_name]";
         
            
        // Set the default value.
        if ( isset($data[$form_field->form_field_name]) )
        {
            $default = $data[$form_field->form_field_name];
        }
        else if ( isset($entry->{$form_field->column_name}) )
        {
            $default = $entry->{$form_field->column_name};
        }
        else
        {
            $default = $properties->get('default_value');
        }
            
        // Initialize the form_fields JS variable.
        if (!self::$form_fields_init)
        {
            self::$form_fields_init = true;
                
            $doc->addScriptDeclaration("var form_fields = {};");
        }
            
        // Initialize the result strings.
        $label_str = '';
        $field_str = '';
        $before_str = '';
        $after_str = '';
            
            
        $caption = self::escapeHTML( $properties->get('form_caption') );
            
        // Add a required mark.
        if ( !empty($form_field->required) )
        {
            $caption .= ' <span class="rsdir-required" title="' . JText::_('COM_RSDIRECTORY_FIELD_REQUIRED') . '">*</span>';
        }
            
        // Add a label to the form field, if the form caption is set.
        if ($caption)
        {
            $label_str = '<label class="rsdir-label">' . $caption . "</label>\n";
        }
            
        if ( !$isRoot && ($is_site || !$can_edit_all_entries) && $form_field->field_type != 'calendar' && $properties->get('readonly') )
        {
            $this->setAttr('readonly', 'readonly');
        }
            
        // Proceed based on the form field type.
        switch ($form_field->field_type)
        {
            case 'title':
            case 'big_subtitle':
            case 'small_subtitle':
            case 'youtube':
                    
                $this->setAttr('class', 'rsdir-textbox');
                    
                $field_str .= "<input type=\"text\" name=\"$name_attr\"" . ( $default ? ' value="' . self::escapeHTML($default) . '"' : '' ) . $this->getAttrsString() . " />\n";
                    
                break;
                    
            case 'description':
            case 'textarea':
                    
                if ( $properties->get('allow_html') )
                {
                    $editor = JFactory::getEditor();
                        
                    $field_str .= $editor->display( $name_attr, self::escapeHTML($default), '100%' , '20%' , '100' , '10' );
                        
                    if ($is_site || !$can_edit_all_entries)
                    {
                        $script = 'function rsdir_description_timeout()
                        {
                            setTimeout(function()
                            {
                                content = ' . $editor->getContent($name_attr) . '
                                updateTextCreditsCost( document.getElementById("' . $name_attr . '"), content );
                                rsdir_description_timeout();
                            }, 1000);
                        }
                            
                        jQuery(function()
                        {
                            rsdir_description_timeout();
                        });';
                            
                        $doc->addScriptDeclaration($script);
                    }
                }
                else
                {
                    $this->setAttr('class', 'rsdir-textarea');
                        
                    $field_str .= '<textarea name="' . $name_attr . '" ' . $this->getAttrsString() . '>' . self::escapeHTML($default) . "</textarea>\n";
                }
                    
                break;
                    
            case 'images':
            case 'image_upload':
            case 'fileupload':
                    
                $max_files_number = $properties->get('max_files_number');
                    
                $script = "rsdir.files_limit[$form_field->id] = $max_files_number";
                    
                // Add the script declaration.
                $doc->addScriptDeclaration($script);
                    
                if ($entry)
                {
                    // Get the files list for this field.
                    $files_list = RSDirectoryHelper::getFilesObjectList($form_field->id, $entry->id);
                }
                    
                $diff = $max_files_number - ( empty($files_list) ? 0 : count($files_list) );
                    
                $this->setAttr( 'class', in_array( $form_field->field_type, array('images', 'image_upload') ) ? 'rsdir-images' : 'rsdir-fileupload' );
                    
                $field_str .= '<div class="rsdir-file-upload' . (!$diff && $max_files_number ? ' hide' : '') . '">';
                $field_str .= '<input type="file" name="' . $name_attr . '[]"' . $this->getAttrsString() . ' />';
                $field_str .= '<i class="rsdir-clear-upload rsdir-icon-x"></i>';
                $field_str .= '</div>';
                    
                if ($max_files_number > 1 || $max_files_number == 0)
                {
                   $field_str .= '<div class="rsdir-add-file-upload btn btn-mini' . ( in_array( $diff, array(0, 1) ) && $max_files_number ? ' hidden' : '' ) . '"><i class="icon-plus"></i> ' . JText::_('COM_RSDIRECTORY_ADD_MORE') . '</div>';
                }
                    
                // Show the list of uploaded files for an entry.
                if ( !empty($files_list) )
                {
                    if ( in_array( $form_field->field_type, array('images', 'image_upload') ) )
                    {
                        $after_str .= self::getImagesList($files_list);
                    }
                    else
                    {
                        $after_str .= self::getFilesList($files_list);
                    }
                }
                    
                break;
                    
            case 'price':
                    
                $this->setAttr('class', 'rsdir-textbox input-small');
                    
                $field_str .= '<div class="input-append">';
                    
                $field_str .= '<input type="text" name="' . $name_attr . '"' . ( isset($default) ? ' value="' . self::escapeHTML($default) . '"' : '' ) . $this->getAttrsString() . " />\n ";
                   
                $field_str .= '<span class="add-on">' . self::escapeHTML( RSDirectoryConfig::getInstance()->get('currency') ) . '</span>';
                    
                $field_str .= '</div>';
                    
                break;
                    
            case 'publishing_period':
                    
                // Initialize the options array.
                $options = array();
                    
                // Get the items.
                $items = unserialize( $properties->get('items') );
                    
                // Initialize the credits array.
                $credits = array();
                    
                // Initialize the current value.
                $current = null;
                    
                if ( !empty($entry) && $items )
                {
                    foreach ($items as $i => $item)
                    {
                        if ($entry->period == $item->period)
                        {
                            if ( empty($default) )
                            {
                                $default = $i + 1;
                            }
                                
                            $current = $i + 1;
                                
                            break;
                        }
                    }
                }
                    
                if ($items)
                {
                    foreach ($items as $i => $item)
                    {
                        if ($item->period)
                        {
                            $label = strtolower( JText::plural('COM_RSDIRECTORY_NUMBER_OF_DAYS', $item->period) );    
                        }
                        else
                        {
                            $label = JText::_('COM_RSDIRECTORY_NO_EXPIRY');    
                        }
                            
                        if ($item->credits)
                        {
                            $label .= ' (' . JText::plural('COM_RSDIRECTORY_NUMBER_OF_CREDITS', $item->credits) . ')';
                        }
                            
                        $options[] = JHtml::_('select.option', $i + 1, $label);
                            
                        $credits[] = ($i + 1) . ":$item->credits";
                    }
                }
                    
                $this->setAttr('class', 'rsdir-publishing-period');
                    
                $field_str .= '<div class="control-group">';
                    
                if ( $properties->get('display_method') == 'dropdown' )
                { 
                    array_unshift( $options, JHtml::_( 'select.option', '', JText::_('COM_RSDIRECTORY_SELECT_PUBLISHING_PERIOD') ) );
                        
                    $field_str .= JHTML::_( 'select.genericlist', $options, $name_attr, $this->getAttrsString(), 'value', 'text', isset($default) ? $default : null );
                }
                else if ( $properties->get('display_method') == 'radiogroup' )
                {
                    $attrs = $this->getAttrsString();
                        
                    foreach ($options as $option)
                    { 
                        $checked = isset($default) && $option->value == $default;
                            
                        $field_str .= '<label class="rsdir-radio-label radio' . ( $properties->get('flow') == 'horizontal' ? ' inline' : '' ) . '">';
                            
                        $field_str .= '<input type="radio" name="' . $name_attr . '" value="' . self::escapeHTML($option->value) . "\"$attrs" . ($checked ? ' checked="checked"' : '') . ' /> ';
                            
                        $field_str .= self::escapeHTML($option->text) . "</label>\n";
                    }
                }
                    
                $field_str .= '</div>';
                    
                if ( $properties->get('show_republish') )
                {
                    $checked = !empty($data['renew']) || !empty($entry->renew);
                        
                    $after_str .= '<label class="rsdir-renew-label checkbox"><input type="checkbox" name="fields[renew]"' . ($checked ? ' checked="checked"' : '') . ' /> ' . JText::_('COM_RSDIRECTORY_AUTOMATICALLY_REPUBLISH_ENTRY_LABEL') . '</label>';
                }
                    
                    
                $expired = !empty($entry) && $entry->expiry_time != '0000-00-00 00:00:00' && JFactory::getDate($entry->expiry_time)->toUnix() < JFactory::getDate()->toUnix();
                    
                $unpaid = RSDirectoryHelper::findElements( array('object_type' => 'publishing_period', 'paid' => 0), $entry_credits );
                    
                $script = "form_fields[$form_field->id] = {" .
                          "credits: {" . implode(',', $credits) . "}," .
                          "current_value: " . ($current && !$expired && !$unpaid ? $current : 0) . "," .
                          "last_value: " . ($default ? $default : 0) . "," .
                          "};";
                            
                $doc->addScriptDeclaration($script);
                    
                break;
                    
            case 'promoted':
                    
                if ($default)
                {
                    $this->setAttr('checked', 'checked');
                }
                    
                $this->setAttr('class', 'rsdir-checkbox');
                    
                $field_str .= '<label class="checkbox"><input type="checkbox" name="' . $name_attr . '"' . $this->getAttrsString() . ' /> ' . JText::_('COM_RSDIRECTORY_PROMOTE_ENTRY_LABEL') . '</label>';
                    
                break;
                    
            case 'textbox':
                    
                $this->setAttr('class', 'rsdir-textbox');
                    
                $field_str .= "<input type=\"text\" name=\"$name_attr\"" . ( $default ? ' value="' . self::escapeHTML($default) . '"' : '' ) . $this->getAttrsString() . " />\n";
                    
                break;
                    
            case 'dropdown':
            case 'country':
                    
                $is_multiple = $properties->get('multiple') && !$is_dependency_parent;
                    
                // Set the default value.
                if ( isset($data[$form_field->form_field_name]) )
                {
                    $default = $data[$form_field->form_field_name];
                }
                else if ( isset($entry->{$form_field->column_name}) )
                {
                    if ($is_multiple)
                    {
                        $default = $entry->{$form_field->column_name} ? explode("\r\n", $entry->{$form_field->column_name}) : array();
                    }
                    else
                    {
                        $default = $entry->{$form_field->column_name};
                    }
                }
                else if ($is_multiple)
                {
                    $default = $properties->get('default_values') ? explode( "\r\n", $properties->get('default_values') ) : array();    
                }
                else
                {
                    $default = $properties->get('default_value');
                }
                    
                // Initialize the options array.
                $options = array();
                    
                if (!$is_multiple)
                {
                    $options[] = array(
                        'items' => array(
                            JHTML::_( 'select.option', '', JText::_('COM_RSDIRECTORY_PLEASE_SELECT_OPTION') )
                        ),
                    );
                }
                    
                if ($items)
                {
                    $options = array_merge(
                        $options,
                        RSDirectoryHelper::getGroupedListOptions($items)
                    );
                }
                    
                // Set the field name.
                $name = $name_attr . ($is_multiple ? '[]' : '');
                    
                $this->setAttr('class', 'rsdir-dropdown');
                    
                // Is the select multiple?
                if ($is_multiple)
                {
                    $this->setAttr('multiple', 'multiple');    
                }
                    
                $field_str .= JHtml::_(
                    'select.groupedlist',
                    $options,
                    $name,
                    array(
                        'id' => "fields$form_field->id",
                        'list.select' => isset($default) ? $default : null,
                        'list.attr' => $this->getAttrsString(),
                    )
                );
                    
                break;
                    
            // Process a checkbox group field.
            case 'checkboxgroup':
                    
                // Set the default value.
                if ( isset($data[$form_field->form_field_name]) )
                {
                    $default = $data[$form_field->form_field_name];
                }
                else if ( isset($entry->{$form_field->column_name}) )
                {
                    $default = $entry->{$form_field->column_name} ? explode("\r\n", $entry->{$form_field->column_name}) : array();
                }
                else
                {
                    $default = $properties->get('default_values') ? explode( "\r\n", $properties->get('default_values') ) : array();
                }
                    
                $this->setAttr('class', 'rsdir-checkbox');
                    
                $attrs = $this->getAttrsString();
                    
                $field_str .= '<div class="rsdir-items-group">';
                    
                // Get dependency items.
                if ( $properties->get('dependency') && empty($dependency_options) )
                {
                    $field_str .= JText::_('COM_RSDIRECTORY_NOTHING_TO_SELECT');
                }
                    
                foreach ($items as $item)
                {
                    $value = $item->value;
					$text = $item->text;
                        
                    $disabled = false;
                        
                    if ( stripos($value, '[d]') !== false || stripos($text, '[d]') !== false )
                    {
                        $value = str_replace('[d]', '', $value);
                        $text = str_replace('[d]', '', $text);
                            
                        $disabled = true;
                    }
                        
                    $checked = isset($default) && in_array($value, $default);
                        
                    $field_str .= '<label class="rsdir-checkbox-label checkbox' . ( $properties->get('flow') == 'horizontal' ? ' inline' : '' ) . '">';
                        
                    $field_str .= '<input type="checkbox" name="' . $name_attr . '[]" value="' . self::escapeHTML($value) . "\"$attrs" . ($checked ? ' checked="checked"' : '') . ($disabled ? ' disabled="disabled"' : '') . ' /> ';
                        
                    $field_str .= self::escapeHTML($text) . "</label>\n";
                }
                    
                $field_str .= '</div><!-- .rsdir-items-group -->';
                    
                break;
                    
            case 'radiogroup':
                    
                // Set the default value.
                if ( isset($data[$form_field->form_field_name]) )
                {
                    $default = $data[$form_field->form_field_name];
                }
                else if ( isset($entry->{$form_field->column_name}) )
                {
                    $default = $entry->{$form_field->column_name};
                }
                else
                {
                    $default = $properties->get('default_value');
                }
                    
                $this->setAttr('class', 'rsdir-radio');
                    
                if ($is_dependency_parent)
                {
                    $this->setAttr('class', 'rsdir-dependency');
                    $this->setAttr('data-id', $form_field->id);
                }
                    
                $attrs = $this->getAttrsString();
                    
                $field_str .= '<div class="rsdir-items-group">';
                    
                // Get dependency items.
                if ( $properties->get('dependency') && empty($dependency_options) )
                {
                    $field_str .= JText::_('COM_RSDIRECTORY_NOTHING_TO_SELECT');
                }
                    
                foreach ($items as $item)
                {
                    $value = $item->value;
					$text = $item->text;
                        
                    $disabled = false;
                        
                    if ( stripos($value, '[d]') !== false || stripos($text, '[d]') !== false )
                    {
                        $value = str_replace('[d]', '', $value);
                        $text = str_replace('[d]', '', $text);
                            
                        $disabled = true;
                    }
                        
                    $checked = isset($default) && $value == $default;
                        
                    $field_str .= '<label class="rsdir-radio-label radio' . ( $properties->get('flow') == 'horizontal' ? ' inline' : '' ) . '">';
                        
                    $field_str .= '<input type="radio" name="' . $name_attr . '" value="' . self::escapeHTML($value) . "\"$attrs" . ($checked ? ' checked="checked"' : '') . ($disabled ? ' disabled="disabled"' : '') . ' /> ';
                        
                    $field_str .= self::escapeHTML($text) . "</label>\n";
                }
                    
                $field_str .= '</div><!-- .rsdir-items-group -->';
                    
                break;
                    
            case 'calendar':
                    
                // Initialize the datepicker attributes array.
                $datepicker_attrs = array();
                    
                    
                // Set the default value.
                if ( isset($data[$form_field->form_field_name]) )
                {
                    $default = $data[$form_field->form_field_name];
                }
                else if ( isset($entry->{$form_field->column_name}) )
                {
                    $datetime = explode(' ', $entry->{$form_field->column_name});
                    $date = explode('-', $datetime[0]); 
                        
                    if ( isset($date[2]) )
                    {
                        list($year, $month, $day) = $date;
                            
                        $default = "$month/$day/$year";
                    }
                    else
                    {
                        $default = '';
                    }
                }
                else
                {
                    $default = $properties->get('default_date');
                }
                
                if ($default == '00/00/0000')
                {
                    $default = '';
                }
                    
                $calendar_layout = $properties->get('calendar_layout');
                    
                    
                $calendar_id = 'rsdir-calendar-' . strtolower( str_replace('_', '-' , $form_field->name) );
                    
                $field_str .= "<div id=\"$calendar_id\"" . ($calendar_layout == 'popup' ? ' class="input-append"' : '') . ">\n";
                    
                $this->setAttr('class', 'rsdir-calendar-input input-small');
                    
                if ( $properties->get('readonly') )
                {
                    $this->setAttr('readonly', 'readonly');    
                }
                    
                // Build the alt field.
                $field_str .= '<input type="text" name="' . $name_attr . '"' . ( isset($default) ? ' value="' . self::escapeHTML($default) . '"' : '' ) . $this->getAttrsString() . " />\n";
                    
                    
                // Process the min date field.
                if ( $properties->get('min_date') )
                {
                    $min_date = explode( '/', $properties->get('min_date') );
                        
                    if ( isset($min_date[2]) )
                    {
                        $datepicker_attrs['minDate'] = 'new Date(' . $min_date[2] . ', ' . ($min_date[0] - 1) . ', ' . $min_date[1] . ')';
                    }
                }
                    
                    
                // Process the max date field.
                if ( $properties->get('max_date') )
                {
                    $max_date = explode( '/', $properties->get('max_date') );
                        
                    if ( isset($max_date[2]) )
                    {
                        $datepicker_attrs['maxDate'] = 'new Date(' . $max_date[2] . ', ' . ($max_date[0] - 1) . ', ' . $max_date[1] . ')';
                    }
                }
                    
                // Open the document ready function.
                $script = "jQuery(function(){\n";
                    
                    
                // Proceed based on the calendar layout.
                if ($calendar_layout == 'popup')
                {  
                    $script .= "jQuery( document.getElementById('$calendar_id') ).find('.rsdir-calendar-input').datepicker({\n";
                        
                    $datepicker_attrs['buttonImage'] = "'" . JURI::root(true) . "/media/com_rsdirectory/images/icon-16-calendar.png'";
                    $datepicker_attrs['showOn'] = "'both'";
                }
                else
                {
                    $field_str .= "<div class=\"rsdir-calendar\">&nbsp;</div>\n";
                        
                    $script .= "jQuery( document.getElementById('$calendar_id') ).find('.rsdir-calendar').datepicker({\n";
                        
                    $datepicker_attrs['altField'] = "'#$calendar_id .rsdir-calendar-input'";
                }
                    
                $datepicker_attrs['firstDay'] = JFactory::getLanguage()->getFirstDay();
                  
                    
                foreach ($datepicker_attrs as $key => $value)
                {
                    $script .= "$key: $value,\n";
                }
                    
                    
                $script .= 'onSelect: calendarSelect,';
                    
                $script .= "});";
                    
                if ($default && $default != '00/00/0000')
                {
                    $script .= "jQuery( document.getElementById('$calendar_id') ).find('.rsdir-calendar').datepicker('setDate', '$default');\n";
                }
                    
                if ($calendar_layout == 'popup')
                {
                    $script .= "jQuery( document.getElementById('$calendar_id') ).find('.ui-datepicker-trigger').addClass('btn');\n";
                }  
                    
                // Close the document ready function.
                $script .= "});\n";
                    
                // Add the script declaration.
                $doc->addScriptDeclaration($script);
                    
                $field_str .= "</div>\n";
                    
                break;
                    
            case 'dropdown_date_picker':
                    
                // Set the default value.
                if ( isset($entry->{$form_field->column_name}) )
                {
                    $date = explode( '-', substr( $entry->{$form_field->column_name}, 0, strpos($entry->{$form_field->column_name}, ' ') ) );
                        
                    if ( isset($date[2]) )
                    {
                        list($default_year, $default_month, $default_day) = $date;
                            
                        // Remove leading 0.
                        $default_month = ltrim($default_month, 0);
                        $default_day = ltrim($default_day, 0);
                    }
                }
                    
                $start_year = $properties->get('start_year', 1900);
                $end_year = $properties->get('until_current_year') || !$properties->get('end_year') ? JFactory::getDate()->format('Y') : $properties->get('end_year');
                    
                $default = true;
                    
                $this->setAttr('class', 'span3');
                    
                // Build the year dropdown.
                if ( $properties->get('show_year_dropdown') )
                {
                    if ( isset($data[$form_field->form_field_name]['year']) )
                    {
                        $default_year = $data[$form_field->form_field_name]['year'];
                    }
                        
                    $default = empty($default_year) ? false : $default;
                        
                    // Initialize the years options array.
                    $options = array(
                        JHTML::_( 'select.option', 0, JText::_('COM_RSDIRECTORY_YEAR') ),
                    );
                        
                    for ($year = $end_year; $year >= $start_year; $year--)
                    {
                        $options[] = JHTML::_('select.option', $year, $year);
                    }
                        
                    $field_str .= JHTML::_(
                        'select.genericlist',
                        $options,
                        $name_attr . '[year]',
                        ' class="rsdir-year-dropdown ' . $this->getAttr('class') . '"',
                        'value',
                        'text',
                        empty($default_year) ? null : $default_year
                    );
                }
                    
                // Build the month dropdown.
                if ( $properties->get('show_month_dropdown') )
                {
                    if ( isset($data[$form_field->form_field_name]['month']) )
                    {
                        $default_month = $data[$form_field->form_field_name]['month'];
                    }
                        
                    $default = empty($default_month) ? false : $default;
                        
                    // Initialize the months options array.
                    $options = array(
                        JHTML::_( 'select.option', 0, JText::_('COM_RSDIRECTORY_MONTH') ),
                    );
                        
                    for ($month = 1; $month < 13; $month++)
                    {
                        $time = mktime(0, 0, 0, $month, 1, 1);
                            
                        if ( $properties->get('month_format') == 2 )
                        {
                            $formated_month = JFactory::getDate($time)->format('F');
                        }
                        else if ( $properties->get('month_format') == 3 )
                        {
                            $formated_month = JFactory::getDate($time)->format('M');
                        }
                        else
                        {
                            $formated_month = $month < 10 ? "0$month" : $month;
                        }
                            
                        $options[] = JHTML::_('select.option', $month, $formated_month);
                    }
                        
                    $field_str .= JHTML::_(
                        'select.genericlist',
                        $options,
                        $name_attr. '[month]',
                        ' class="rsdir-month-dropdown ' . $this->getAttr('class') . '"',
                        'value',
                        'text',
                        empty($default_month) ? null : $default_month
                    );
                }
                    
                // Build the day dropdown.
                if ( $properties->get('show_day_dropdown') )
                {
                    if ( isset($data[$form_field->form_field_name]['day']) )
                    {
                        $default_day = $data[$form_field->form_field_name]['day'];
                    }
                        
                    $default = empty($default_day) ? false : $default;
                        
                    // Initialize the days options array.
                    $options = array(
                        JHTML::_( 'select.option', 0, JText::_('COM_RSDIRECTORY_DAY') ),
                    );
                        
                    for ($day = 1; $day < 32; $day++)
                    {
                        $options[] = JHTML::_('select.option', $day, $day);
                    }
                        
                    $field_str .= JHTML::_(
                        'select.genericlist',
                        $options,
                        $name_attr . '[day]',
                        ' class="rsdir-day-dropdown ' . $this->getAttr('class') . '"',
                        'value',
                        'text',
                        empty($default_day) ? null : $default_day
                    );
                }
                    
                break;
                    
            case 'map':
                    
                // Reset the field prepend and append properties.
                $properties->set('field_prepend', null);
                $properties->set('field_append', null);
                    
                $address_column_name = "{$form_field->column_name}_address";
                $lat_column_name = "{$form_field->column_name}_lat";
                $lng_column_name = "{$form_field->column_name}_lng";
                    
                // Set the default value.
                if ( isset($data[$form_field->form_field_name]['address']) )
                {
                    $default_address = $data[$form_field->form_field_name]['address'];
                }
                else if ( isset($entry->$address_column_name) )
                {
                    $default_address = $entry->$address_column_name;
                }
                else
                {
                    $default_address = $properties->get('default_address');
                }
                
                if ( isset($data[$form_field->form_field_name]['lat'], $data[$form_field->form_field_name]['lng']) )
                {
                    $default_lat = $data[$form_field->form_field_name]['lat'];
                    $default_lng = $data[$form_field->form_field_name]['lng'];
                }
                else if ( isset($entry->$lat_column_name, $entry->$lng_column_name) )
                {
                    $default_lat = $entry->$lat_column_name;
                    $default_lng = $entry->$lng_column_name;
                }
                else
                {
                    $default_lat = $properties->get('default_lat');
                    $default_lng = $properties->get('default_lng');
                }
                    
                $this->setAttr('class', 'rsdir-textbox rsdir-map-address');
                    
                $id = "rsdir-$form_field->form_field_name";
                $this->setAttr('id', "$id-address");
                    
                if ( $properties->get('show_address_input', 1) )
                {
                    // ADDRESS
                    $field_str .= '<div class="rsdir-map-field">';
                        
                    $field_str .= '<div class="input-prepend">';
                        
                    $field_str .= '<span class="add-on">' . JText::_('COM_RSDIRECTORY_ADDRESS') . '</span>';
                        
                    $field_str .= '<input type="text" name="' . $name_attr . '[address]"' . ( $default_address ? ' value="' . self::escapeHTML($default_address) . '"' : '' ) . $this->getAttrsString() ." autocomplete=\"off\" />\n";
                        
                    $field_str .= '</div>';
                        
                    $field_str .= '</div>';
                }
                else
                {
                    $field_str .= '<input type="hidden" name="' . $name_attr . '[address]"' . ( $default_address ? ' value="' . self::escapeHTML($default_address) . '"' : '' ) . $this->getAttrsString() ." />\n";
                }
                    
                if ( $properties->get('show_coordinates_inputs', 1) )
                {
                    $field_str .= '<div class="rsdir-map-field">';
                        
                    // LATITUDE
                    $field_str .= '<div class="input-prepend">';
                        
                    $field_str .= '<span class="add-on">' . JText::_('COM_RSDIRECTORY_LATITUDE') . '</span>';
                        
                    $field_str .= '<input id="' . $id . '-lat" class="input-medium" type="text" name="' . $name_attr . '[lat]"' . ( $default_lat ? ' value="' . self::escapeHTML($default_lat) . '"' : '' ) . $this->getAttrsString() . ' />';
                        
                    $field_str .= '</div>';
                        
                    // LONGITUDE
                    $field_str .= '<div class="input-prepend">';
                        
                    $field_str .= '<span class="add-on">' . JText::_('COM_RSDIRECTORY_LONGITUDE') . '</span>';
                        
                    $field_str .= '<input id="' . $id . '-lng" class="input-medium" type="text" name="' . $name_attr . '[lng]"' . ( $default_lng ? ' value="' . self::escapeHTML($default_lng) . '"' : '' ) . $this->getAttrsString() . ' />';   
                        
                    $field_str .= '</div>';
                        
                    $field_str .= '</div>';
                }
                else
                {
                    $field_str .= '<input id="' . $id . '-lat" type="hidden" name="' . $name_attr . '[lat]"' . ( $default_lat ? ' value="' . self::escapeHTML($default_lat) . '"' : '' ) . $this->getAttrsString() . ' />';
                    $field_str .= '<input id="' . $id . '-lng" type="hidden" name="' . $name_attr . '[lng]"' . ( $default_lng ? ' value="' . self::escapeHTML($default_lng) . '"' : '' ) . $this->getAttrsString() . ' />';   
                }
                    
                $style = array();
                    
                $width = $properties->get('width', '100%');
                    
                if ( is_numeric($width) )
                {
                    $width .= 'px';
                }
                    
                $style[] = "width: $width;";
                    
                $style[] = 'height: ' . $properties->get('height') . 'px;';
                    
                $after_str .= '<div id="' . $id . '-canvas" class="rsdir-map" style="' . implode(' ', $style) . '"></div>';
                    
                $script = 'jQuery(function($)
                {
                    jQuery( document.getElementById("' . $id . '-canvas") ).rsMap(
                    {
                        zoom: ' . $properties->get('default_zoom') . ',
                        inputAddress: document.getElementById("' . $id . '-address"),
                        inputLat: document.getElementById("' . $id . '-lat"),
                        inputLng: document.getElementById("' . $id . '-lng"),
                        ' . ( !$isRoot && ($is_site || !$can_edit_all_entries) && $properties->get('readonly') ? '' : 'markerDraggable: true,' ) . '
                    });
                });';
                    
                // Add the script declaration.
                $doc->addScriptDeclaration($script);
                    
                break;
                
            case 'free_text':
                    
                $field_str .= '<div' . $this->getAttrsString() . '>' . $properties->get('text') . '</div>';
                    
                break;
        }
            
        // Add a loader gif after the field if it is a dependency parent.
        if ( !empty($is_dependency_parent) )
        {
            $after_str .= '<img class="rsdir-loader hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" alt="" width="16" height="11" />';    
        }
          
        if ( ($is_site || !$can_edit_all_entries) && ( $properties->get('credits') || $properties->get('credits_per_file') ) )
        {
            $add_class_attr = true;
                
            // Initialize the credits value.
            $credits = $properties->get('credits');
                
            if ($entry_credits)
            {
                foreach ($entry_credits as $object)
                {
                    if ( $object->paid && $object->object_type == 'form_field' && $object->object_id == $form_field->id)
                    {
                        if ( in_array( $form_field->field_type, array('images', 'image_upload', 'fileupload') ) )
                        {
                            $credits = 0;
                                
                            if ( $properties->get('credits_per_file') )
                                break;
                        }
                            
                        $add_class_attr = false;
                            
                        break;
                    }
                }
            }
                
            if ($add_class_attr)
            {
                // Mark the field as paid.
                $field_wrapper_attrs['class'] .= ' rsdir-paid-field';
                    
                $script = "form_fields[$form_field->id] = {" .
                          "credits: $credits,";
                            
                if ( in_array( $form_field->field_type, array('images', 'image_upload', 'fileupload') ) )
                {
                    $script .= 'credits_per_file: ' . $properties->get('credits_per_file') . ',';
                }
                else
                { 
                    $script .= "filled_in: " . ($default ? 1 : 0) . ",";
                }
                    
                $script .= "};";
                    
                $doc->addScriptDeclaration($script);
            }
        }
            
        $field_prepend = $properties->get('field_prepend');
        $field_append = $properties->get('field_append');
            
        if ( !empty($is_dependency_parent) )
        {
            $field_wrapper_attrs['class'] .= ' dependency-parent';
        }
            
        if ( $properties->get('dependency') )
        {
            $field_wrapper_attrs['class'] .= ' dependency-child';
            $field_wrapper_attrs['data-parent-id'] = $properties->get('dependency');
        }
            
        $field_wrapper_attrs['data-id'] = $form_field->id;
            
        $str = '<div';
            
        foreach ($field_wrapper_attrs as $attr => $value)
        {
            $str .= " $attr=\"$value\"";
        }
            
        $str .= '>';
        $str .= $label_str;
        $str .= $before_str;
            
        if ($field_prepend || $field_append)
        {
            $cls = array();
                
            if ($field_prepend)
            {
                $cls[] = 'input-prepend';
            }
                
            if ($field_append)
            {
                $cls[] = 'input-append';    
            }
                
            $str .= '<div class="' . implode(' ', $cls) . '">';
                
            if ($field_prepend)
            {
                $str .= '<div class="add-on">' . self::escapeHTML($field_prepend) . '</div>';    
            }
                
            $str .= $field_str;
                
            if ($field_append)
            {
                $str .= '<div class="add-on">' . self::escapeHTML($field_append) . '</div>';    
            }
                
            $str .= '</div>';
        }
        else
        {
            $str .= $field_str;
        }
            
        if ( $properties->get('show_help_tip') && $properties->get('help_tip') )
        {
            $str .= ' <i class="rsdir-toggle-help icon-question-sign" data-content="' . self::escapeHTML( $properties->get('help_tip') ) . '" data-toggle="tooltip"></i>';    
        }
            
        if ( $properties->get('show_help_text') && $properties->get('help_text') )
        {
            $cls = $properties->get('help_text_position') == 'inline' ? 'help-inline' : 'help-block';
                
            $str .= '<span class="' . $cls . '">' . self::escapeHTML( $properties->get('help_text') ) . '</span>';    
        }
            
        $str .= $after_str;
            
        $str .= "</div><!-- .rsdir-field-wrapper-$form_field->id -->\n";
            
        return $str;
    }
        
    /**
     * Method to get dependency options.
     *
     * @access public
     *
     * @param mixed $dependency
     *
     * @return mixed
     */
    public function getDependencyOptions($dependency)
    {
        if (!$dependency)
            return;
            
        // Get the posted data.
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.entry.data');
            
        // Get dependency items.
        $parent = RSDirectoryHelper::getField($dependency);
            
        if ( isset($data[$parent->form_field_name]) )
        {
            $parent_value = $data[$parent->form_field_name];
        }
        else if ( isset($this->entry->{$parent->column_name}) )
        {
            $parent_value = $this->entry->{$parent->column_name};
        }
        else
        {
            $parent_value = $parent->properties->get('default_value') ? $parent->properties->get('default_value') : '';
        }
            
        if ( isset($parent_value) )
        {
            $dependencies = RSDirectoryHelper::getDependencies(0, $dependency, $parent_value);
                
            if ( isset($dependencies[0]) )
            {
                return RSDirectoryHelper::getOptions($dependencies[0]->items);
            }
        }
    }
        
    /**
     * Get the CAPTCHA field.
     *
     * @access
     *
     * @static
     *
     * @return mixed
     */
    public static function getCaptchaField()
    {
        // Get the RSDirectory! configuration.
        $config = RSDirectoryConfig::getInstance();
            
        // Get the captcha error.
        $error = JFactory::getApplication()->getUserState('com_rsdirectory.edit.entry.captcha_error');  
            
        if ( RSDirectoryHelper::checkUserPermission('add_entry_captcha') )
        {
            $str = '<div class="rsdir-field-wrapper control-group clearfix' . ($error ? ' error' : '') . '">';
                
            $str .= '<label class="rsdir-label">' . JText::_('COM_RSDIRECTORY_CAPTCHA_LABEL') . '</label>';
                
            if ( $config->get('captcha_type') == 'built_in' )
            {
                $width = 30 * $config->get('captcha_characters_number') + 50;
                    
                $str .= '<img id="rsdir-captcha" src="' . JRoute::_( "index.php?option=com_rsdirectory&task=field.captcha&random=" . mt_rand() ) . '" width="' . $width . '" height="80" alt="CAPTCHA" />';
                    
                $str .= '<i id="rsdir-captcha-refresh" class="icon-refresh" title="' . JText::_('COM_RSDIRECTORY_REFRESH_CAPTCHA_DESC') . '"></i>';
                    
                $str .= '<img id="rsdir-captcha-loader" class="rsdir-hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" width="16" height="11" alt="" />';
                    
                $str .= '<br />';
                    
                $str .= '<input id="rsdir-captcha-input" type="text" name="fields[captcha]" />';
            }
            else
            {
                require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/recaptcha/recaptchalib.php';
                    
                $str .= RSDirectoryReCAPTCHA::getHTML( $config->get('recaptcha_public_key'), $config->get('recaptcha_theme') );
            }
                
            $str .= '</div>';
                
            return $str;
        }
    }
        
    /**
     * Get the HTML code of a images list.
     *
     * @access public
     *
     * @static
     *
     * @param array $files_list
     * @param int $enable_ordering
     * @param int $enable_delete
     *
     * @return mixed
     */
    public static function getImagesList($files_list, $enable_ordering = 1, $enable_delete = 1)
    {
        // Do a few checks.
        if ( !$files_list || !is_array($files_list) )
            return;
            
        JText::script('COM_RSDIRECTORY_FILE_DELETION_CONFIRMATION');
            
        // Get the document object.
        $doc = JFactory::getDocument();
            
        // Get an instance of the RSDirectoryConfig object.
        $config = RSDirectoryConfig::getInstance();
            
        // Get the width of the small thumbnail.
        $width = $config->get('small_thumbnail_width');
            
        // Get the height of the small thumbnail.
        $height = $config->get('small_thumbnail_height');
            
        $gallery_class = 'rsdir-gallery-' . $files_list[0]->field_id . ' rsdir-images-list rsdir-files clearfix';
            
        if ($enable_ordering)
        {
            $gallery_class .= ' rsdir-order-files';
        }
            
        $gallery_id = 'rsdir-gallery-' . RSDirectoryHelper::randStr();
            
        // Build the files list.
        $str = '<ul id="' . $gallery_id . '" class="' . $gallery_class . '">';
            
        foreach ($files_list as $file)
        {
            // Set the src.
            $src = RSDirectoryHelper::getImageURL($file->hash, 'small');
                
            // Set the alt.
            $alt = JFile::stripExt($file->original_file_name);
                
            // Set the href.
            $href = RSDirectoryHelper::getImageURL($file->hash);
                
            $str .= '<li class="rsdir-file">';
                
            $str .= '<a class="rsdir-img" href="' . $href . '">';    
            $str .= '<img src="' . $src . '" alt="' . self::escapeHTML($alt) . '" width="' . $width . '" height="' . $height . '" />';
            $str .= '</a>';
                
            if ($enable_delete)
            {
                $str .= '<span class="rsdir-delete-file btn btn-mini"><i class="rsdir-icon-x" title="' . JText::_('COM_RSDIRECTORY_DELETE_IMAGE') . '"></i></span>';
                $str .= '<img class="rsdir-image-loader hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" alt="" width="16" height="11" />';
            }
                
            if ($enable_ordering || $enable_delete)
            {
                $str .= '<input class="rsdir-file-pk" type="hidden" value="' . $file->id . '" />';
            }
                
            $str .= '</li>';
        }
            
        $str .= '</ul>';
            
        return $str;
    }
        
    /**
     * Output the user details fields.
     *
     * @access public
     *
     * @static
     */
    public static function outputRegFields()
    {
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get the posted data.
        $data = $app->getUserState('com_rsdirectory.edit.entry.data');
            
        // Get the names of the registration fields that contain errors.
        $error_reg_fields = $app->getUserState('com_rsdirectory.edit.entry.error_reg_fields');
            
        ?>
            
        <div class="rsdir-field-wrapper control-group<?php echo is_array($error_reg_fields) && in_array('name', $error_reg_fields) ? ' error' : ''; ?>">
            <label class="rsdir-label" for="rsdir-register-name"><?php echo JText::_('COM_RSDIRECTORY_NAME'); ?><span class="star">&nbsp;*</span></label>
            <input id="rsdir-register-name" class="rsdir-textbox" type="text" name="fields[reg][name]"<?php echo empty($data['reg']['name']) ? '' : ' value="' . self::escapeHTML($data['reg']['name']) . '"'; ?> />
        </div>
            
        <div class="rsdir-field-wrapper control-group<?php echo is_array($error_reg_fields) && in_array('email', $error_reg_fields) ? ' error' : ''; ?>">
            <label class="rsdir-label" for="rsdir-register-email"><?php echo JText::_('COM_RSDIRECTORY_EMAIL'); ?><span class="star">&nbsp;*</span></label>
            <input id="rsdir-register-email" class="rsdir-textbox" type="text" name="fields[reg][email]"<?php echo empty($data['reg']['email']) ? '' : ' value="' . self::escapeHTML($data['reg']['email']) . '"'; ?> />
        </div>
            
        <?php
    }
        
    /**
     * Get the HTML code of a files list.
     * 
     * @access public
     *
     * @static
     *
     * @param array $files_list
     * @param int $enable_ordering
     * @param int $enable_delete
     *
     * @return mixed
     */
    public static function getFilesList($files_list, $enable_ordering = 1, $enable_delete = 1)
    {
        // Do a few checks.
        if ( !$files_list || !is_array($files_list) )
            return;
            
        JText::script('COM_RSDIRECTORY_FILE_DELETION_CONFIRMATION');
            
        // Build the files list.
        $str = '<table class="rsdir-files-list rsdir-files table table-striped">';
            
        $str .= '<tbody' . ($enable_ordering ? ' class="rsdir-order-files"' : '') . '>';
            
        foreach ($files_list as $i => $file)
        {
            $href = RSDirectoryHelper::absJRoute("index.php?option=com_rsdirectory&task=file.download&hash=$file->hash");
                
            $str .= '<tr class="rsdir-file"><td>';
                
            if ($enable_delete)
            {
                $str .= '<i class="icon-remove pull-right rsdir-delete-file" title="' . JText::_('COM_RSDIRECTORY_DELETE_FILE') . '"></i>';
                $str .= '<img class="rsdir-image-loader hide pull-right" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" alt="" width="16" height="11" />';
            }
                
            $str .= '<a href="' . $href . '">' . self::escapeHTML($file->original_file_name) . '</a>';
                
            if ($enable_ordering || $enable_delete)
            {
                $str .= '<input class="rsdir-file-pk" type="hidden" value="' . $file->id . '" />';
            }
                
            $str .= '</td></tr>';
        }
            
        $str .= '</tbody>';
            
        $str .= '</table>';
            
        return $str;
    }
        
    /**
     * Escape HTML.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $string
     * 
     * @return string
     */
    public static function escapeHTML($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }
}