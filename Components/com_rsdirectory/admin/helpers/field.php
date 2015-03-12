<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Generate field value.
 */
class RSDirectoryField
{
    /**
     * The form field.
     *
     * @var object
     * 
     * @access protected
     */
    protected $form_field;
        
    /**
     * Entry data.
     *
     * @var object
     * 
     * @access protected
     */
    protected $entry;
        
    /**
     * The files list of a fileupload field.
     * 
     * @var array
     *
     * @access protected
     */
    protected $files_list;
        
    /**
     * Set to true when we call the setFilesList function.
     *
     * @var bool
     *
     * @access protected
     */
    protected $set_files_list;
        
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param object $form_field
     * @param object $entry
     */
    public function __construct($form_field, $entry)
    {
        $this->form_field = $form_field;
        $this->entry = $entry;
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
     * @param object $form
     * 
     * @return RSDirectoryField
     */
    public static function getInstance($form_field, $entry)
    {
        $rsdirectoryfield = new RSDirectoryField($form_field, $entry);
            
        return $rsdirectoryfield;
    }
        
    /**
     * Set files list.
     *
     * @access public
     *
     * @param array $files_list
     *
     * @return RSDirectoryField
     */
    public function setFilesList($files_list)
    {
        $this->files_list = $files_list;
        $this->set_files_list = true;
            
        return $this;
    }
        
    /**
     * Generate the field value.
     *
     * @access public
     *
     * @return string
     */
    public function generate()
    {
        $form_field = $this->form_field;
        $properties = $form_field->properties;
        $entry = $this->entry;
            
        // Get the stored value.
        $field_value = isset($entry->{$form_field->column_name}) ? $entry->{$form_field->column_name} : '';
            
        switch ($form_field->field_type)
        {
            case 'big_subtitle':
            case 'small_subtitle':
            case 'description':
            case 'textarea':
                    
                $value = RSDirectoryHelper::cleanText( $field_value, true, $properties->get('clean_links') );
					
                if ( !$properties->get('allow_html') )
                {
                    $value = strip_tags($value);
                }
                    
                break;
                    
            case 'dropdown':
            case 'checkboxgroup':
            case 'radiogroup':
                    
                $value = str_replace("\r\n", ', ', $field_value);
                break;
                    
            case 'calendar':
            case 'dropdown_date_picker':
                    
                if ( $properties->get('date_mask') == 'human_readable' )
                {
                    $value = RSDirectoryHelper::formatDate( $field_value, $properties->get('date_mask') );
                }
                else
                {
                    $date = array(
                        JFactory::getDate($field_value)->format( $properties->get('date_mask') ),
                        JFactory::getDate($field_value)->format( $properties->get('time_mask') ),
                    );
                        
                    $value = trim( implode(' ', $date) );
                }
                    
                break;
                    
            case 'fileupload':
            case 'image_upload':
                    
                if ($this->set_files_list)
                {
                    // Get the files list.
                    $files_list = RSDirectoryHelper::getFilesObjectList($form_field->id, $entry->id);
                }
                else
                {
                    $files_list = $this->files_list;
                }
                    
                if ( !empty($files_list) )
                {
                    if ($form_field->field_type == 'image_upload')
                    {
                        $value = RSDirectoryFormField::getImagesList($files_list, 0, 0);
                    }
                    else
                    {
                        $value = RSDirectoryFormField::getFilesList($files_list, 0, 0);
                    }
                    
                }
                    
                break;
                    
            case 'map':
                    
                $address_column_name = "{$form_field->column_name}_address";
                $value = isset($entry->$address_column_name) ? $entry->{$address_column_name} : '';
                break;    
                    
            case 'free_text':
                    
                $value = $properties->get('text');
                break;
                    
            default:
                    
                $value = $field_value;
                break;
        }
            
        return empty($value) ? null : $value;
    }
}