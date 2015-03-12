<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry model.
 */
class RSDirectoryModelEntry extends JModelAdmin
{
    /**
     * Model context string.
     *
     * @var string
     */
    protected $_context = 'com_rsdirectory.entry';
        
    /**
     * An array holding the field names of the registration fields that contain errors.
     * 
     * @var array
     */
    protected $error_reg_fields = array();
    
    /**
     * An array holding the ids of the fields that contain errors.
     * 
     * @var array
     */
    protected $error_field_ids = array();
        
    /**
     * The CAPTCHA error.
     *
     * @var mixed
     */
    protected $captcha_error;
        
    /**
     * Set a field error.
     *
     * @access private
     *
     * @param int $field_id
     * @param string $error_message
     */
    private function setFieldError($field_id, $error_message)
    {
        $this->error_field_ids[] = $field_id;
        $this->setError($error_message);
    }
        
    /**
     * Set CAPTCHA error.
     *
     * @access private
     *
     * @param string $error_message
     */
    private function setCaptchaError($error_message)
    {
        $this->captcha_error = $error_message;
        $this->setError($error_message);
    }
        
    /**
     * Set registration field error.
     *
     * @access private
     *
     * @param string $field_name
     * @param string $error_message
     */
    private function setRegError($field_name, $error_message)
    {
        $this->error_reg_fields[] = $field_name;
        $this->setError($error_message);
    }
        
    /**
     * Method to get a table object, load it if necessary.
     * 
     * @access public
     * 
     * @param string $type
     * @param string $prefix
     * @param array $config
     * 
     * @return object
     */
    public function getTable( $type = 'Entry', $prefix = 'RSDirectoryTable', $config = array() )
    {
        return JTable::getInstance($type, $prefix, $config);
    }
        
    /**
     * Method for getting the form from the model.
     *
     * @access public
     * 
     * @param array $data
     * @param bool $loadData
     * 
     * @return mixed
     */
    public function getForm( $data = array(), $loadData = true )
    {
        JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');
            
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.entry', 'entry', array('control' => 'fields', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
        
    /**
     * Method to get the data that should be injected in the form.
     *
     * @access protected
     * 
     * @return array
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
            
        $data = $app->getUserState( 'com_rsdirectory.edit.entry.data', array() );
            
        $category_id = empty($data['category_id']) ? $app->input->getInt('category_id') : $data['category_id'];
            
        // Check the session for previously entered form data.
        if ( empty($data) )
        {
            $data = (array)$this->getItem();
        }
			
		if ($category_id)
        {
            $data['category_id'] = $category_id;
        }
            
        return $data;
    }
        
    /**
     * Method to get a single record.
     *
     * @access public
     *
     * @param int $pk The id of the primary key.
     *
     * @return mixed Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        // Get the id of the primary key.
        $pk = $pk ? $pk : (int) $this->getState( $this->getName() . '.id' );
            
        static $cache = array();
            
        if ( !isset($cache[$pk]) )
        {
            $cache[$pk] = RSDirectoryHelper::getEntry($pk);
        }
            
        return $cache[$pk];
    }
        
    /**
     * Validate form data.
     *
     * @access public
     * 
     * @param object $form The form to validate against.
     * @param array $data The data to validate.
     * @param string $group The name of the field group to validate.
     * 
     * @return mixed
     */
    public function validate($form, $data, $group = null)
    {
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Front-end?
        $is_site = $app->isSite();
            
        if (!$is_site)
        {
            $return = parent::validate($form, $data, $group);
                
            if (!$return)
                return false;
                
            foreach ($return as $key => $value)
            {
                $data[$key] = $value;
            }
        }
            
        // Get the task.
        $task = $app->input->get('task');
            
        // On front-end enable only the save & saveAndBuyCredits tasks.
        if ( $is_site && !in_array( $task, array('save', 'saveAndBuyCredits') ) )
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Load entry.
        if ( !empty($data['id']) )
        {
            $entry = $this->getItem($data['id']);
                
            if (!$entry)
                return false;
                
            if ( empty($data['category_id']) )
            {
                $data['category_id'] = $entry->category_id;   
            }
        }
            
        // Can moderate?
        $can_moderate = !$is_site || RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
            
        if ( empty($data['category_id']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_FIELD_ERROR_SELECT_CATEGORY') );
            return false;
        }
            
        // Check if the category exists.
        $query = $db->getQuery(true)
               ->select( $db->qn('id') )
               ->from( $db->qn('#__categories') )
               ->where( $db->qn('id') . ' = ' . $db->q($data['category_id']) . ' AND ' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') );
              
        $db->setQuery($query);
            
        if ( !$db->loadResult() )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_FIELD_ERROR_INVALID_CATEGORY') );
            return false;
        }
            
        // Get the form associated to the category.
        $form = RSDirectoryHelper::getCategoryInheritedForm($data['category_id']);
            
        // Return false if no form was associated to the category.
        if (!$form)
            return false;
            
        // Get the published form fields assigned to the form.
        $form_fields = RSDirectoryHelper::getFormFields($form->id, 1, 1);
            
        // Return false if there are no form fields assigned to the form.
        if (!$form_fields)
            return false;
            
        // Get the JUser object.
        $user = JFactory::getUser();
            
        // Get the RSDirectory! configuration.
        $config = RSDirectoryConfig::getInstance();
            
        // Get the uploaded files.
        $form_files = $app->input->files->get('fields');
            
        // Set the unallowed file extensions.
        $unallowed_files = array('php', 'php5', 'phtml', 'php4', 'php3', 'phps', 'js', 'vbs', 'asp', 'exe');
            
            
        // Validate the user id.
        if ( !$is_site && empty($data['user_id']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_FIELD_ERROR_SELECT_ENTRY_AUTHOR') );
        }
            
            
        // Validate the CAPTCHA field.
        if ( $is_site && RSDirectoryHelper::checkUserPermission('add_entry_captcha') && empty($entry->id) )
        {
            if ( $config->get('captcha_type') == 'built_in' )
            {
                // Using securimage.
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/securimage/securimage.php';
                    
                // Initialize the CAPTCHA object.
                $captcha = new JSecurImage();
                $captcha->case_sensitive = $config->get('captcha_case_sensitive');
                    
                // Validate the CAPTCHA value.
                if ( empty($data['captcha']) || !$captcha->check( trim($data['captcha']) ) )
                {
                    $this->setCaptchaError( JText::_('COM_RSDIRECTORY_CAPTCHA_ERROR') );
                }
            }
            else
            {
                // Using reCAPTCHA.
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/recaptcha/recaptchalib.php';
                    
                $response = RSDirectoryReCAPTCHA::checkAnswer(
                    $config->get('recaptcha_private_key'),
                    isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                    isset($_POST['recaptcha_challenge_field']) ? $_POST['recaptcha_challenge_field'] : '',
                    isset($_POST['recaptcha_response_field']) ? $_POST['recaptcha_response_field'] : ''
                );
                    
                if ( empty($response->is_valid) )
                {
                    $this->setCaptchaError( JText::_('COM_RSDIRECTORY_CAPTCHA_ERROR') );
                }
            }
        }
            
        // Validate the registration fields.            
        if ($is_site && !$user->id)
        {
            $reg = empty($data['reg']) ? array() : $data['reg'];
                
            if ( empty($reg['name']) )
            {
                $this->setRegError( 'name', JText::_('COM_RSDIRECTORY_NAME_REQUIRED') );
            }
            else if ( strlen( trim($reg['name']) ) < 2 )
            {
                $this->setRegError( 'name', JText::_('COM_RSDIRECTORY_NAME_LENGTH_ERROR') );
            }
                
            if ( empty($reg['email']) )
            {
                $this->setRegError( 'email', JText::_('COM_RSDIRECTORY_EMAIL_REQUIRED') );
            }
            else if ( !RSDirectoryHelper::email($reg['email']) )
            {
                $this->setRegError( 'email', JText::_('COM_RSDIRECTORY_PROVIDE_VALID_EMAIL') );
            }
            else if ( RSDirectoryHelper::userExists( array('email' => $reg['email']) ) )
            {
                $this->setRegError( 'email', JText::_('COM_RSDIRECTORY_EMAIL_IN_USE') );
            }
        }
            
         
        // Validate each form field.
        foreach ($form_fields as $form_field)
        {
            if ($form->use_title_template && $form_field->field_type == 'title')
                continue;
                
            if ($form->use_big_subtitle_template && $form_field->field_type == 'big_subtitle')
                continue;
                
            if ($form->use_small_subtitle_template && $form_field->field_type == 'small_subtitle')
                continue;
                
            if ($form->use_description_template && $form_field->field_type == 'description')
                continue;
                
            // Get the field properties.
            $properties = empty($form_field->properties) ? new JRegistry : $form_field->properties;
                
            if ( $form_field->field_type == 'publishing_period' && !unserialize( $properties->get('items') ) )
                continue;
                
            if ( isset($data[$form_field->form_field_name]) )
            {
                $data[$form_field->form_field_name] = RSDirectoryHelper::trim($data[$form_field->form_field_name]);
            }
                
            if ($form_field->field_type == 'map')
            {
                $field_value = isset($data[$form_field->form_field_name]['address']) && $data[$form_field->form_field_name]['address'] != '' ? $data[$form_field->form_field_name]['address'] : '';
            }
            else
            {   
                $field_value = isset($data[$form_field->form_field_name]) && $data[$form_field->form_field_name] != '' ? $data[$form_field->form_field_name] : '';    
            }
                
            // Get the validation message.
            $validation_message = $properties->get('validation_message');
                
            // Unset any previously set items.
			unset($items);
                
            // Unset any previously set valid keys.
            unset($valid_keys);
                
            // Process DROPDOWN DATE PICKER field.
            if ($form_field->field_type == 'dropdown_date_picker')
            {
                $date = (object)$field_value;
                    
                $show_day_dropdown = $properties->get('show_day_dropdown');
                $show_month_dropdown = $properties->get('show_month_dropdown');
                $show_year_dropdown = $properties->get('show_year_dropdown');
                    
                // Field is required.
                if ($form_field->required)
                {
                    // Set an error if one of the values is empty.
                    if ( ( $show_day_dropdown && empty($date->day) ) || ( $show_month_dropdown && empty($date->month) ) || ( $show_year_dropdown && empty($date->year) ) )
                    {
                        $this->setFieldError($form_field->id, $validation_message);
                        continue;
                    }
                }
                    
                if ( $show_year_dropdown && !empty($date->year) )
                {
                    $start_year = $properties->get('start_year');
                    $end_year = $properties->get('until_current_year') || !$properties->get('end_year') ? JFactory::getDate()->format('Y') : $properties->get('end_year');
                        
                    if ($date->year < $start_year || $date->year > $end_year)
                    {
                        $this->setFieldError($form_field->id, $validation_message);
                        continue;
                    }
                }
                    
                if ( $show_month_dropdown && !empty($date->month) && ($date->month < 1 || $date->month > 12) )
                {
                    $this->setFieldError($form_field->id, $validation_message);
                    continue;
                }
                    
                if (
                    $show_year_dropdown && $show_month_dropdown && $show_day_dropdown &&
                    !empty($date->year) && !empty($date->month) && !empty($date->day) &&
                    !checkdate($date->month, $date->day, $date->year)
                )
                {
                    $this->setFieldError($form_field->id, $validation_message);
                    continue;
                }
                    
                $valid = true;
                    
                // If one field was selected, then all fields must be selected.
                if ($show_year_dropdown && $show_month_dropdown && $show_day_dropdown)
                {
                    if ( ( !empty($date->year) || !empty($date->month) || !empty($date->day) ) && ( empty($date->year) || empty($date->month) || empty($date->day) ) )
                    {
                        $valid = false;
                    }
                    else if ( !empty($date->year) && !empty($date->month) && !empty($date->day) )
                    {
                        $filled_in = true;
                    }
                }
                else if ($show_year_dropdown && $show_month_dropdown)
                {
                    if ( ( !empty($date->year) || !empty($date->month) ) && ( empty($date->year) || empty($date->month) ) )
                    {
                        $valid = false;
                    }
                    else if ( !empty($date->year) && !empty($date->month) )
                    {
                        $filled_in = true;
                    }
                }
                else if ($show_year_dropdown && $show_day_dropdown)
                {
                    if ( ( !empty($date->year) || !empty($date->day) ) && ( empty($date->year) || empty($date->day) ) )
                    {
                        $valid = false;
                    }
                    else if ( !empty($date->year) && !empty($date->day) )
                    {
                        $filled_in = true;
                    }
                }
                else if ($show_month_dropdown && $show_day_dropdown)
                {
                    if ( ( !empty($date->month) || !empty($date->day) ) && ( empty($date->month) || empty($date->day) ) )
                    {
                        $valid = false;
                    }
                    else if ( !empty($date->month) && !empty($date->day) )
                    {
                        $filled_in = true;
                    }
                }
                    
                if ( empty($filled_in) && !$valid )
                {
                    $this->setFieldError($form_field->id, $validation_message);
                }
                    
                continue;
            }
                
            // Process FILEUPLOAD and IMAGES fields.
            if ( in_array( $form_field->field_type, array('images', 'image_upload', 'fileupload') ) )
            {
                // Get the max filecount.
                $max_file_count = $properties->get('max_files_number', 0);
                    
                if ( !empty($entry) )
                {
                    $existing_files = RSDirectoryHelper::getFilesObjectList($form_field->id, $entry->id);
                        
                    // Set the target directory.
                    $entry_dir = JPATH_COMPONENT_SITE . "/files/entries/$entry->id";
                }
                    
                // Initialize the files count.
                $files_count = empty($existing_files) ? 0 : count($existing_files);
                    
                // Get the accepted file extensions.
                $accepted_files = $properties->get('accepted_files') ? explode( "\r\n", $properties->get('accepted_files') ) : array();
                    
                    
                // Validate each file.
                if ( !empty($form_files[$form_field->form_field_name]) )
                {
                    foreach ($form_files[$form_field->form_field_name] as $key => $file)
                    {
                        if (!$file['name'])
                            continue;
                            
                        // Validate the FILE TYPE of the IMAGES field.
                        if ( in_array( $form_field->field_type, array('images', 'image_upload') ) && !RSDirectoryHelper::isImage($file['tmp_name']) )
                        {
                            $this->setFieldError($form_field->id, $validation_message);
                            continue 2;
                        }
                            
                        // Increment the files count.
                        $files_count++;
                            
                        // The maximum file count has been reached.
                        if ($max_file_count && $files_count >  $max_file_count)
                        {
                            $this->setFieldError($form_field->id, $validation_message);
                            continue 2;
                        }
                            
                        // Get the extension.
                        $ext = strtolower( JFile::getExt($file['name']) );
                            
                        // Skip this file if it's unallowed.
                        if ( in_array($ext, $unallowed_files) )
                        {
                            $this->setFieldError($form_field->id, $validation_message);
                            continue 2;
                        }
                            
                        if ($accepted_files)
                        {
                            // Convert all the accepted files extensions to lowercase.
                            foreach ($accepted_files as &$value)
                            {
                                $value = strtolower($value);
                            }
                                
                            // Skip this file if it's unallowed.
                            if ( !in_array($ext, $accepted_files) )
                            {
                                $this->setFieldError($form_field->id, $validation_message);
                                continue 2;
                            }
                        }
                            
                        // Skip this file if it's too big.
                        if ( $properties->get('maximum_file_size') && $properties->get('maximum_file_size') * 1024 < $file['size'] )
                        {
                            $this->setFieldError($form_field->id, $validation_message);
                            continue 2;
                        }
                    }
                }
                    
                // Set an error if the field is required and nothing was uploaded.
                if ( $form_field->required && empty($files_count) )
                {
                    $this->setFieldError($form_field->id, $validation_message);
                    continue;
                }
                   
                continue;
            }
                
            // Set an error if the field is required and the value is empty.
            if ($form_field->required && $field_value === '')
            {
                $this->setFieldError($form_field->id, $validation_message);
                continue;
            }
                
            // Proceed with validating the field if the posted value is not empty.
            if ( !empty($field_value) )
            {
                if ($form_field->field_type == 'price')
                {
                    $field_value = trim($field_value);
                    $data[$form_field->form_field_name] = $field_value;
                        
                    if ( !is_numeric($field_value) || $field_value < 0 )
                    {
                        $this->setFieldError($form_field->id, $validation_message);
                        continue;
                    }
                }
                    
                // Check if the field allows an array of values.
                if ( $form_field->field_type != 'checkboxgroup' && !$properties->get('multiple') && is_array($field_value) )
                {
                    $this->setFieldError($form_field->id, $validation_message);
                    continue;
                }
                    
                if ($form_field->field_type == 'youtube')
                {
                    $uri = new JURI($field_value);
                        
                    if ( !( $uri->getHost() == 'youtu.be' && trim( substr( $uri->getPath(), 1 ) ) ) && !$uri->getVar('v') )
                    {
                        $this->setFieldError($form_field->id, $validation_message);
                        continue;
                    }
                }
                    
                // Get the items array if the field has one.
                switch ($form_field->field_type)
                {
                    case 'publishing_period':
                            
                        @$items = $properties->get('items') ? unserialize( $properties->get('items') ) : array();
                        $items = $items ? $items : array();
                            
                        $valid_keys = array();
                            
                        for ( $i = 1; $i <= count($items); $i++ )
                        {
                            $valid_keys[] = $i;
                        }
                        
                        break;
                            
                    case 'dropdown':
                    case 'checkboxgroup':
                    case 'radiogroup':
                    case 'country':
                            
                        $items = $properties->get('items') ? RSDirectoryHelper::getOptions( $properties->get('items') ) : array();
                            
                        if ($items)
                        {
							$valid_keys = array();
								
                            foreach ($items as $item)
                            {
                                if (
                                    stripos($item->value, '[g]') === false && stripos($item->text, '[g]') === false &&
                                    stripos($item->value, '[d]') === false && stripos($item->text, '[d]') === false
                                )
                                {
                                    $valid_keys[] = $item->value;
                                }
                            }
                        }
                            
                        break;
                }
                    
                $valid = true;
                    
                // If the field has an items array, then validate if the posted values are valid.
                if ( isset($valid_keys) )
                {
                    if (!$valid_keys)
                    {
                        $valid = false;
                    }
                        
                    if ( is_array($field_value) )
                    {
                        foreach ($field_value as $value)
                        {
                            if ( !in_array($value, $valid_keys) )
                            {
                                $valid = false;
                                break;
                            }
                        }
                    }
                    else
                    {
                        if ( !in_array($field_value, $valid_keys) )
                        {
                            $valid = false;
                        }
                    }
                }
                    
                // Validate the value if there is a validation rule set.
                if ( $valid && $properties->get('default_validation_rule') != 'none' )
                {
                    // Validated the value of the form field.
                    switch ( $properties->get('default_validation_rule') )
                    {
                        case 'alpha':
                            $valid = RSDirectoryHelper::alpha( $field_value, $properties->get('extra_accepted_chars') );
                            break;
                            
                        case 'numeric':
                            $valid = RSDirectoryHelper::numeric( $field_value, $properties->get('extra_accepted_chars') );
                            break;
                            
                        case 'alphanumeric':
                            $valid = RSDirectoryHelper::alphanumeric( $field_value, $properties->get('extra_accepted_chars') );
                            break;
                            
                        case 'email':
                            $valid = RSDirectoryHelper::email($field_value);
                            break;
                            
                        case 'emaildns':
                            $valid = RSDirectoryHelper::emaildns($field_value);
                            break;
                            
                        case 'uniquefield':
                            $valid = RSDirectoryHelper::uniquefield($field_value, $form_field->column_name);
                            break;
                            
                        case 'uniquefielduser':
                            $valid = RSDirectoryHelper::uniquefielduser($field_value, $form_field->column_name);
                            break;
                            
                        case 'uszipcode':
                            $valid = RSDirectoryHelper::uszipcode($field_value);
                            break;
                            
                        case 'phonenumber':
                            $valid = RSDirectoryHelper::phonenumber($field_value);
                            break;
                            
                        case 'creditcard':
                            $valid = RSDirectoryHelper::creditcard($field_value);
                            break;
                            
                        case 'custom_characters':
                            $valid = RSDirectoryHelper::custom( $field_value, $properties->get('custom_validation_rule') );
                            break;
                            
                        case 'custom_php':
                            @$valid = eval( $properties->get('custom_validation_rule') );
                            break;
                            
                        case 'ipaddress':
                            $valid = RSDirectoryHelper::ipaddress($field_value);
                            break;
                            
                        case 'validurl':
                            $valid = RSDirectoryHelper::validurl($field_value);
                            break;
                            
                        case 'regex':
                            $valid = RSDirectoryHelper::regex( $field_value, $properties->get('regex_syntax') );
                            break;
                            
                        default:
                            $valid = true;
                            break;
                    }
                }
                    
                // Validate characters limit.
                $characters_limit = $properties->get('characters_limit');
                    
                if ( $valid && $characters_limit && strlen( strip_tags($field_value) ) > $characters_limit )
                {
                    $valid = false;    
                }
                    
                // Set an error if the value is invalid.
                if (!$valid)
                {
                    $this->setFieldError($form_field->id, $validation_message);
                    continue;
                }
            }
        }
            
        // Store the ids of the fields that contain errors.
        $app->setUserState('com_rsdirectory.edit.entry.error_field_ids' , $this->error_field_ids);
            
        // Store the CAPTCHA error.
        $app->setUserState('com_rsdirectory.edit.entry.captcha_error', $this->captcha_error);
            
        // Store the names of the registration fields that contain errors.
        $app->setUserState('com_rsdirectory.edit.entry.error_reg_fields' , $this->error_reg_fields);
            
        if ( $this->getErrors() )
            return false;
			
        return $data;
    }
        
    /**
     * Save the field.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Front-end?
        $is_site = $app->isSite();
            
        // Get the task.
        $task = $app->input->get('task');
            
        // Get the JUser object.
        $user = JFactory::getUser();
            
        // Load entry.
        if ( !empty($data['id']) )
        {
            $current_entry = $this->getItem($data['id']);
              
            if ( empty($data['category_id']) )
            {
                $data['category_id'] = $current_entry->category_id;
            }
                
            if ($task == 'save2copy')
            {
                // Copy the entry object.
                $entry_copy = clone $current_entry;
                    
                // Unset entry.
                unset($current_entry);
            }
        }
            
        $can_moderate = !$is_site || RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
            
        // Get the uploaded files.
        $form_files = $app->input->files->get('fields');
            
        // Initialize the entries credits objects array.
        $entry_credits_objects = array();
            
        // Initialize the emails array. E.g.: submit_entry, publish_entry..
        $emails = array();
            
        // Get the form associated to the category.
        $form = RSDirectoryHelper::getCategoryInheritedForm($data['category_id']);
            
        // Get all the form fields.
        $form_fields = RSDirectoryHelper::getFormFields();
            
            
        // Go through the form fields to process the PUBLISHING PERIOD, PROMOTED entry option.
        // We will process the other fields further down.
        foreach ($form_fields as $form_field)
        {
            if ( empty($data[$form_field->form_field_name]) )
                continue;
                
            // Get the field properties.
            $properties = empty($form_field->properties) ? new JRegistry : $form_field->properties;
                
            if ($form_field->field_type == 'publishing_period')
            {
                @$items = $properties->get('items') ? unserialize( $properties->get('items') ) : array();
                $items = $items ? $items : array();
                    
                $period = isset($items[$data[$form_field->form_field_name] - 1]) ? $items[$data[$form_field->form_field_name] - 1]->period : 0;
                $period_credits = isset($items[$data[$form_field->form_field_name] - 1]) ? $items[$data[$form_field->form_field_name] - 1]->credits : 0;
            }
            else if ($form_field->field_type == 'promoted')
            {
                $promoted_credits = $properties->get('credits');
            }
                
            if ( in_array( $form_field->field_type, array('big_subtitle', 'small_subtitle', 'description', 'price', 'promoted') ) )
            {
                $entry_credits_objects[] = (object)array(
                    'object_type' => 'form_field',
                    'object_id' => $form_field->id,
                    'credits' => $properties->get('credits'),
                );
            }
        }
            
            
        // Get the publishing period.
        $period = empty($period) ? 0 : $period;
            
        // Get the publishing period credits.
        $period_credits = empty($period_credits) ? 0 : $period_credits;
            
            
        // Process user.
        if ($can_moderate)
        {
            if ($is_site)
            {
                if ( empty($current_entry) )
                {
                    $user_id = JFactory::getUser()->id;
                }
                else
                {
                    $user_id = $current_entry->user_id;
                }
            }
            else
            {
                // Get the selected user id.
                $user_id = $data['user_id'];
            }
        }
        else
        {
            if ( JFactory::getUser()->id )
            {
                // Get the logged in user id.
                $user_id = JFactory::getUser()->id;
            }
            // Proceed with the user registration.
            else
            {
                require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/registration.php';
                    
                $reg = new RSDirectoryRegistration;
                $reg->setData( array(
                    'name' => $data['reg']['name'],
                    'email' => $data['reg']['email'],
                ) );
                    
                if ( $reg->register() )
                {
                    $user_id = $reg->getUserId();
                        
                    // Remember the id of the newly registered user.
                    $app->setUserState('com_rsdirectory.registration.user.id', $user_id);
                }
                else
                {
                    $reg_errors = $reg->getErrors();
                        
                    if ($reg_errors)
                    {
                        foreach ($reg_errors as $reg_error)
                        {
                            $this->setError($reg_error);
                        }
                    }
                    else
                    {
                        $this->setError( JText::_('COM_RSDIRECTORY_ENTRY_SAVE_ERROR') );
                    }
                        
                    return false;
                }
            }
        }
            
            
        // Initialize the entry data.
        $entry_data = (object)array(
            'category_id' => $data['category_id'],
            'form_id' => $form->id,
            'user_id' => $user_id,
            'title' => empty($data['title']) ? '' : $data['title'],
            'big_subtitle' => empty($data['big_subtitle']) ? '' : $data['big_subtitle'],
            'small_subtitle' => empty($data['small_subtitle']) ? '' : $data['small_subtitle'],
            'description' => empty($data['description']) ? '' : $data['description'],
            'price' => empty($data['price']) ? '' : $data['price'],
            'period' => $period,
            'period_credits' => $period_credits,
            'renew' => empty($data['renew']) ? 0 : 1,
            'promoted' => empty($data['promoted']) ? 0 : 1,
            'promoted_credits' => empty($promoted_credits) ? 0 : $promoted_credits,
        );
            
            
        // Inserting a new entry.
        if ( empty($current_entry) )
        {
            $operation = 'insert';
                
            $entry_data->ip = RSDirectoryHelper::getIp(true);
            $entry_data->created_time = JFactory::getDate()->toSql();
                
            $entry_credits_objects[] = (object)array(
                'object_type' => 'publishing_period',
                'object_id' => 0,
                'credits' => $period_credits,
            );
                
            // Insert the entry.
            $db->insertObject('#__rsdirectory_entries', $entry_data, 'id');
                
            // Get the id of the inserted entry.
            $entry_id = $entry_data->id;
                
            $emails[] = 'submit_entry';
        }
        // Saving an existing entry.
        else
        {
            $operation = 'save';
				
			$expired = $current_entry->expiry_time != '0000-00-00 00:00:00' && JFactory::getDate($current_entry->expiry_time)->toUnix() < JFactory::getDate()->toUnix();
                
            // Get the entry id.
            $entry_id = $current_entry->id;
                
            $entry_data->id = $current_entry->id;
            $entry_data->modified_time = JFactory::getDate()->toSql();
            $entry_data->modified_user_id = JFactory::getUser()->id;
                
            // Update the entry.
            $db->updateObject('#__rsdirectory_entries', $entry_data, 'id');
                
            // Check if the publishing period is unpaid.
            $query = $db->getQuery(true)
                   ->select( $db->qn('id') )
                   ->from( $db->qn('#__rsdirectory_entries_credits') )
                   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) )
                   ->where( $db->qn('object_type') . ' = ' . $db->q('publishing_period') )
                   ->where( $db->qn('paid') . ' = ' . $db->q(0) );
                   
            $db->setQuery($query);
            $unpaid_publishing_period = $db->loadResult();
                
            if ($period != $current_entry->period || $expired || $unpaid_publishing_period)
            {
                $entry_credits_objects[] = (object)array(
                    'object_type' => 'publishing_period',
                    'object_id' => 0,
                    'credits' => $period_credits,
                );
            }
                
            // If the entry author has changed...
            if ($current_entry->user_id != $user_id)
            {
                // Assign the entries credits to the new user.
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_entries_credits') )
                       ->set( $db->qn('user_id') . ' = ' . $db->q($user_id) )
                       ->where( $db->qn('entry_id') . ' = ' . $db->q($current_entry->id) );
                        
                $db->setQuery($query);
                $db->execute();
                    
                // Assign the entries files to the new user.
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_uploaded_files', 'f') )
                       ->innerJoin( $db->qn('#__rsdirectory_uploaded_files_fields_relations', 'r') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('r.file_id') )
                       ->set( $db->qn('f.user_id') . ' = ' . $db->q($user_id) )
                       ->where( $db->qn('r.entry_id') . ' = ' . $db->q($current_entry->id) );
                       
                $db->setQuery($query);
                $db->execute();
            }
        }
            
            
        // Copy the files from the old entry to the new entry, if the task is save2copy.
        if ( !empty($entry_copy) )
        {
            // Get the files uploaded to the entry.
            $old_files = RSDirectoryHelper::getFilesObjectList(0, $entry_copy->id);
                
            if ($old_files)
            {
                // Set the src dir.
                $src_dir = JPATH_ROOT  . "/components/com_rsdirectory/files/entries/$entry_copy->id";
                    
                // Create the destination folder and get its path.
                $dst_dir = RSDirectoryHelper::createEntryDir($entry_id);
                    
                // Proceed if the destination folder was successfully created.
                if ($dst_dir)
                {
                    // Initialize the files array (to be inserted into the #__rsdirectory_uploaded_files_fields_relations table).
                    $new_files = array();
                        
                    foreach ($old_files as $old_file)
                    {
                        // Get the extension.
                        $ext = strtolower( JFile::getExt($old_file->original_file_name) );
                            
                        // Get a hash.
                        $hash = RSDirectoryHelper::getHash();
                            
                        // Generate a file name.
                        $file_name = $hash . ($ext ? ".$ext" : '');
                            
                        if ( JFile::copy("$src_dir/$old_file->file_name", "$dst_dir/$file_name") )
                        {
                            // Insert the file.
                            $new_file = (object)array(
                                'user_id' => $user_id,
                                'hash' => $hash,
                                'file_name' => $file_name,
                                'original_file_name' => $old_file->original_file_name,
                            );
                                
                            $db->insertObject('#__rsdirectory_uploaded_files', $new_file, 'id');
                                
                            $new_files[] = array(
                                $db->q($new_file->id),
                                $db->q($entry_id),
                                $db->q($old_file->field_id),
                                $db->q($old_file->ordering),
                            );
                                
                            // Get the form field.
                            $form_field = RSDirectoryHelper::findFormField( array('id' => $old_file->field_id), $form_fields );
                                
                            $entry_credits_objects[] = (object)array(
                                'object_type' => 'uploaded_file',
                                'object_id' => $form_field->id,
                                'credits' => $form_field->properties->get('credits_per_file'),
                            );
                                
                            $entry_credits_objects[] = (object)array(
                                'object_type' => 'form_field',
                                'object_id' => $form_field->id,
                                'credits' => $form_field->properties->get('credits'),
                            );
                        }   
                    }
                        
                    if ( !empty($new_files) )
                    {    
                        $columns = array(
                            'file_id',
                            'entry_id',
                            'field_id',
                            'ordering',
                        );
                            
                        $query = $db->getQuery(true)
                               ->insert( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
                               ->columns( $db->qn($columns) );
                                
                        foreach ($new_files as $file)
                        {
                            $query->values( implode(',', $file) );
                        }
                            
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }
        }
            
        // Remember the item id.
        $this->setState( $this->getName() . '.id', $entry_id );
            
            
        // Initialize the entry custom fields data object.
        $entry_custom_data = (object)array(
            'entry_id' => $entry_id,
        );
            
            
        // Process the IMAGES field and the non-core fields.
        // We are processing the IMAGES field here, because we need the entry id.
        foreach ($form_fields as $form_field)
        {
            if ( $form_field->create_column || in_array( $form_field->field_type, array('images', 'image_upload', 'fileupload') ) )
            {
                // Get the field properties.
                $properties = empty($form_field->properties) ? null : $form_field->properties;
                    
                if ( in_array( $form_field->field_type, array('images', 'image_upload', 'fileupload') ) )
                {
                    $entry_dir = RSDirectoryHelper::createEntryDir($entry_id);
                        
                    if ( $entry_dir && is_writable($entry_dir) )
                    {
                        // Initialize the files array (to be inserted into the #__rsdirectory_uploaded_files_fields_relations table).
                        $files = array();
                            
                        // Get the ordering.
                        $query = $db->getQuery(true)
                               ->select( 'MAX(' . $db->qn('ordering') . ')' )
                               ->from( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
                               ->where( $db->qn('field_id') . ' = ' . $db->q($form_field->id) );
                                
                        $db->setQuery($query);
                        $ordering = (int)$db->loadResult();
                            
                        if ( !empty($form_files[$form_field->form_field_name]) )
                        {
                            foreach ($form_files[$form_field->form_field_name] as $file)
                            {
                                if (!$file['name'])
                                    continue;
                                    
                                // Increment the ordering value.
                                $ordering++;
                                    
                                // Get the extension.
                                $ext = strtolower( JFile::getExt($file['name']) );
                                    
                                // Get a hash.
                                $hash = RSDirectoryHelper::getHash();
                                    
                                // Generate a file name.
                                $file_name = $hash . ($ext ? ".$ext" : '');
                                    
                                // Move the uploaded file from tmp to its new directory.
                                JFile::upload($file['tmp_name'], "$entry_dir/$file_name");
                                    
                                    
                                $entry_credits_objects[] = (object)array(
                                    'object_type' => 'uploaded_file',
                                    'object_id' => $form_field->id,
                                    'credits' => $properties->get('credits_per_file'),
                                );
                                    
                                // Insert the file.
                                $file = (object)array(
                                    'user_id' => $user_id,
                                    'hash' => $hash,
                                    'file_name' => $file_name,
                                    'original_file_name' => $file['name'],
                                );
                                    
                                $db->insertObject('#__rsdirectory_uploaded_files', $file, 'id');
                                    
                                $files[] = array(
                                    $db->q($file->id),
                                    $db->q($entry_id),
                                    $db->q($form_field->id),
                                    $db->q($ordering),
                                );
                            }
                        }
                            
                        if ( !empty($files) )
                        {
                            $entry_credits_objects[] = (object)array(
                                'object_type' => 'form_field',
                                'object_id' => $form_field->id,
                                'credits' => $properties->get('credits'),
                            );
                                
                            $columns = array(
                                'file_id',
                                'entry_id',
                                'field_id',
                                'ordering',
                            );
                                
                            $query = $db->getQuery(true)
                                   ->insert( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
                                   ->columns( $db->qn($columns) );
                                    
                            foreach ($files as $file)
                            {
                                $query->values( implode(',', $file) );
                            }
                                
                            $db->setQuery($query);
                            $db->execute();
                        }
                    }
                }
                else
                {
                    $filled_in = false;
                        
                    if ($form_field->field_type == 'calendar')
                    {
                        if ( !empty($data[$form_field->form_field_name]) )
                        {
                            $date = explode('/', $data[$form_field->form_field_name]);    
                        }
                            
                        list($month, $day, $year) = isset($date[2]) ? $date : array('00', '00', '0000');
                            
                        if ($month != '00' && $day != '00' && $year != '0000')
                        {
                            $filled_in = true;
                        }
                            
                        $value = "$year-$month-$day 00:00:00";
                    }
                    else if ($form_field->field_type == 'dropdown_date_picker')
                    {
                        $year = empty($data[$form_field->form_field_name]['year']) ? '0000' : $data[$form_field->form_field_name]['year'];
                        $month = empty($data[$form_field->form_field_name]['month']) ? '00' : $data[$form_field->form_field_name]['month'];
                        $day = empty($data[$form_field->form_field_name]['day']) ? '00' : $data[$form_field->form_field_name]['day'];
                            
                        $show_day_dropdown = $properties->get('show_day_dropdown');
                        $show_month_dropdown = $properties->get('show_month_dropdown');
                        $show_year_dropdown = $properties->get('show_year_dropdown');
                            
                        // The form field was filled in.
                        if (
                            ($show_year_dropdown && $show_month_dropdown && $show_day_dropdown && $year && $month && $day) ||
                            ($show_year_dropdown && $show_month_dropdown && $year && $month) ||
                            ($show_year_dropdown && $show_day_dropdown && $year && $day) ||
                            ($show_month_dropdown && $show_day_dropdown && $month && $day)
                        )
                        {
                            $filled_in = true;
                        }
                            
                        $value = "$year-$month-$day 00:00:00";
                    }
                    else if ($form_field->field_type == 'map')
                    {
                        if ( isset($data[$form_field->form_field_name]['address']) )
                        {
                            $address_column_name = "{$form_field->column_name}_address";
                                
                            $entry_custom_data->$address_column_name = $data[$form_field->form_field_name]['address'];
                        }
                            
                        if ( isset($data[$form_field->form_field_name]['lat'], $data[$form_field->form_field_name]['lng']) )
                        {
                            $lat_column_name = "{$form_field->column_name}_lat";
                            $lng_column_name = "{$form_field->column_name}_lng";
                                
                            $entry_custom_data->$lat_column_name = (float)$data[$form_field->form_field_name]['lat'];
                            $entry_custom_data->$lng_column_name = (float)$data[$form_field->form_field_name]['lng'];
                                
                            $filled_in = true;
                        }
                            
                        continue;
                    }
                    else
                    {
                        if ( empty($data[$form_field->form_field_name]) )
                        {
                            $value = '';
                        }
                        else
                        {
                            if ( $form_field->field_type == 'checkboxgroup' || $properties->get('multiple') )
                            {
                                $value = implode("\r\n", $data[$form_field->form_field_name]);
                            }
                            else
                            {
                                $value = $data[$form_field->form_field_name];
                            }
                                
                            $filled_in = true;
                        }
                    }
                        
                    if ($filled_in)
                    {
                        $entry_credits_objects[] = (object)array(
                            'object_type' => 'form_field',
                            'object_id' => $form_field->id,
                            'credits' => $properties->get('credits'),
                        );    
                    }
                        
                    $entry_custom_data->{$form_field->column_name} = $value;
                }
            }
        }
            
        // Handle custom fields values.
        if ( empty($current_entry) )
        {
            // Insert the entry.
            $db->insertObject('#__rsdirectory_entries_custom', $entry_custom_data);
        }
        else
        {
            // Update the entry.
            $db->updateObject('#__rsdirectory_entries_custom', $entry_custom_data, 'entry_id');
        }
            
            
        // Process credits.
        if ( !empty($current_entry) )
        {
            // Remove existing unpaid credits objects.
            $query = $db->getQuery(true)
                   ->delete( $db->qn('#__rsdirectory_entries_credits') )
                   ->where( $db->qn('entry_id') . ' = ' . $db->q($current_entry->id) . ' AND ' . $db->qn('paid') . ' = ' . $db->q(0) );
               
            $db->setQuery($query);
            $db->execute();
               
            // Get the difference between the existing credits objects and the new credits objects.
            $entry_credits_objects = RSDirectoryCredits::getEntryCreditsDiff($entry_credits_objects, $current_entry->id);
        }
            
        // Clean the entry credits objects array.
        $entry_credits_objects = RSDirectoryCredits::cleanEntryCredits($entry_credits_objects);
            
        // Calculate the total credits count.
        $total = RSDirectoryCredits::calculateCredits($entry_credits_objects);
            
        // Check if the user has sufficient credits.
        $sufficient_credits = RSDirectoryCredits::checkUserCredits($total);
			
		// Verify if we can mark the entry as paid.
		$paid = $can_moderate ||
				( empty($current_entry) && $sufficient_credits ) ||
				( $sufficient_credits && !empty($data['finalize']) ) ||
				( !empty($current_entry->paid) && $sufficient_credits );
            
        // Insert the entries credits objects into the database.
        if ( !empty($entry_credits_objects) )
        {
            RSDirectoryCredits::addEntryCreditsObjects(
                $entry_id,
                $entry_credits_objects,
                $user_id,
                $can_moderate ? 1 : 0, // Grant the author free access to the filled in fields.
                $paid ? 1 : 0 // Mark the fields as paid.
            );
        }
            
            
        // Set publishing data.
        $entry_data = (object)array(
            'id' => $entry_id,
            'paid' => $paid ? 1 : 0,
        );
            
        // Set the publishing status.
        if ($can_moderate)
        {
            $published = $data['published'];
        }
        else
        {
            $published = $paid && RSDirectoryHelper::checkUserPermission('auto_publish_entries') ? 1 : 0;
        }
            
        // Reset the published_time and expiry_time if the entry is UNPUBLISHED.
        if (!$published)
        {
            $entry_data->published_time = '0000-00-00 00:00:00';
            $entry_data->expiry_time = '0000-00-00 00:00:00';
        }
            
        if ($operation == 'insert')
        {
            $entry_data->new = !$published;
                
            if ($published)
            {
                if ( !$can_moderate || !trim($data['published_time']) || $data['published_time'] == '0000-00-00 00:00:00' )
                {
                    $entry_data->published_time = JFactory::getDate()->toSql();
                }
                else
                {
                    $entry_data->published_time = JFactory::getDate($data['published_time'])->toSql();
                }
                    
                // If the expiry time value is specified, calculate the period from that.
                if ( $can_moderate && isset($data['expiry_time']) && $data['expiry_time'] != '0000-00-00 00:00:00' )
                {
                    $period = RSDirectoryHelper::getDateDiff( $entry_data->published_time, JFactory::getDate($data['expiry_time'])->toSql(), 86400 );    
                }
                    
                if ($period)
                {
                    $expiry_time = JFactory::getDate($entry_data->published_time);
                    $expiry_time->modify("+$period days");
                        
                    $entry_data->expiry_time = $expiry_time->toSql();
                }
                else
                {
                    $entry_data->expiry_time = '0000-00-00 00:00:00';
                }
                    
                $emails[] = 'publish_entry';
            }
        }
        else
        {
            if ($published && !$current_entry->published)
            {
                $emails[] = 'publish_entry';
            }
            else if (!$published && $current_entry->published)
            {
                $emails[] = 'unpublish_entry';
            }
                
            if ($published)
            {
                $entry_data->new = 0;    
                    
                if ($can_moderate)
                {
                    if ( !trim($data['published_time']) || $data['published_time'] == '0000-00-00 00:00:00' )
                    {
                        $published_time = JFactory::getDate()->toSql();
                    }
                    else
                    {
                        $published_time = JFactory::getDate($data['published_time'])->toSql();
                    }
                        
                    // Set a new published time if the status changed from unpublished to published, if the period change or if the published time changed.
                    if ( !$current_entry->published || $period != $current_entry->period || $published_time != $current_entry->published_time )
                    {
                        $entry_data->published_time = $published_time;
                        $update_expiry_time = true;
                    }
                }
                // Set a new published time if the entry is auto-published and the user selected a new publishing period.
                else if ($period != $current_entry->period || $expired)
                {
                    $entry_data->published_time = JFactory::getDate()->toSql();
                    $update_expiry_time = true;
                }
            }
                
            // Set a new expiry time if the published time changed.
            if ( !empty($update_expiry_time) )
            {
                // If the expiry time value is specified, calculate the period from that.
                if ( $can_moderate && isset($data['expiry_time']) && $data['expiry_time'] != '0000-00-00 00:00:00' )
                {
                    $period = RSDirectoryHelper::getDateDiff( $entry_data->published_time, JFactory::getDate($data['expiry_time'])->toSql(), 86400 );    
                }
                    
                if ($period)
                {
                    $expiry_time = JFactory::getDate($entry_data->published_time);
                    $expiry_time->modify("+$period days");
                        
                    $entry_data->expiry_time = $expiry_time->toSql();
                }
                else
                {
                    $entry_data->expiry_time = '0000-00-00 00:00:00';
                }
            }
        }
            
        if ($can_moderate)
        {
            // Mark the entry and entry credits as paid if we are saving the entry as published or as paid.
            if ($published || !empty($data['paid']) )
            {
                $entry_data->paid = 1;
            }
            // Mark the entry and entry credits an unpaid if we are saving the entry as unpaid.
            else if ( empty($data['paid']) )
            {
                $entry_data->paid = 0;
            }
                
            $query = $db->getQuery(true)
                   ->update( $db->qn('#__rsdirectory_entries_credits') )
                   ->set( $db->qn('paid') . ' = ' . $db->q($entry_data->paid) )
                   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) );
                 
            $db->setQuery($query);
            $db->execute();
        }
			
		$entry_data->published = $published;
            
        // Save publishing data.
        $db->updateObject('#__rsdirectory_entries', $entry_data, 'id');
            
            
        // Autogenerate title/big subtitle/small subtitle and update entry.
        if ($form->use_title_template || $form->use_big_subtitle_template || $form->use_small_subtitle_template || $form->use_description_template)
        {
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/placeholders.php';
                
            // Get a fresh copy of the entry.
            $entry = RSDirectoryHelper::getEntry($entry_id);
                
            $values = (object)array(
                'id' => $entry_id,
            );
                
            if ($form->use_title_template)
            {
                $values->title = RSDirectoryPlaceholders::getInstance($form->title_template, $form_fields, $entry, $form)
                               ->setParams( array('title', 'database') )
                               ->process();
            }
                
            if ($form->use_big_subtitle_template)
            {
                $values->big_subtitle = RSDirectoryPlaceholders::getInstance($form->big_subtitle_template, $form_fields, $entry, $form)
                                      ->setParams( array('title', 'database') )
                                      ->process();
            }
                
            if ($form->use_small_subtitle_template)
            {
                $values->small_subtitle = RSDirectoryPlaceholders::getInstance($form->small_subtitle_template, $form_fields, $entry, $form)
                                        ->setParams( array('title', 'database') )
                                        ->process();
            }
                
            if ($form->use_description_template)
            {
                $values->description = RSDirectoryPlaceholders::getInstance($form->description_template, $form_fields, $entry, $form)
                                     ->setParams( array('description', 'database') )
                                     ->process();
            }
                
            $db->updateObject('#__rsdirectory_entries', $values, 'id');
        }
            
        if ( !empty($published) && ( $operation == 'insert' || !empty($current_entry->new) ) )
        {
            $table = $this->getTable();
            $table->jomSocialActivity($entry_id);
        }
            
        // Send emails.
        if ($emails)
        {
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/email.php';
                
            // Get a fresh copy of the entry.
            $entry = RSDirectoryHelper::getEntry($entry_id);
                
            foreach ($emails as $email)
            {
                RSDirectoryEmail::getInstance($email, $form_fields, $entry, $form)->send();
            }
        }
            
        return true;
    }
		
    /**
     * Method to auto-populate the model state. 
     * 
     * @access protected
     */
    protected function populateState()
    {
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get the pk of the record from the request.
        $pk = $app->input->getInt('id');
        $this->setState( $this->getName() . '.id', $pk );
            
        if ( $app->isSite() )
        {
            $app = JFactory::getApplication('site');
                
            $params = $app->getParams();
            $this->setState('params', $params);
        }
    }
        
    /**
     * Method to mark an entry as paid/unpaid.
     *
     * @access public
     *
     * @param mixed $cid
     * @param bool $paid
     *
     * @return bool
     */
    public function markAsPaid($cid, $paid)
    {
        if (!$cid)
            return false;
			
		if ( !is_array($cid) )
		{
			$cid = (array)$cid;
		}
			
		// Sanitize input.
		$cid = RSDirectoryHelper::arrayInt($cid);
			
		if (!$cid)
			return false;
		
		// Get Dbo.
        $db = JFactory::getDbo();
			
		$in_values = array();
			
		foreach ($cid as $id)
		{
            $in_values[] = $db->q($id);
		}
			
		$in_values = implode(',', $in_values);;
			
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_entries') )
               ->set( $db->qn('paid') . ' = ' . $db->q($paid ? 1 : 0) )
               ->where( $db->qn('id') . ' IN (' . $in_values . ')' );
               
        $db->setQuery($query);
        $db->execute();
            
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_entries_credits') )
               ->set( $db->qn('paid') . ' = ' . $db->q($paid ? 1 : 0) )
               ->where( $db->qn('entry_id') . ' IN (' . $in_values . ')' );
                 
        $db->setQuery($query);
        $db->execute();
			
		if (!$paid)
		{
			$this->publish($cid, 0);
		}
			
        return true;
    }
		
	/**
	 * Method to finalize an entry.
	 *
	 * @access public
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public function finalize($id)
	{
		// Get Dbo.
		$db = JFactory::getDbo();
			
		// Get user object.
		$user = JFactory::getUser();
			
		// Get persmissions.
		$can_edit_all_entries = RSDirectoryHelper::checkUserPermission('can_edit_all_entries');
			
		if ( !$can_edit_all_entries && RSDirectoryCredits::getUserCredits() != 'unlimited' )
		{
			// Get the number of unpaid entry credits.
			$cost = RSDirectoryCredits::getUnpaidEntryCreditsSum($id);
				
			// Charge user.
			$query = $db->getQuery(true)
			       ->update( $db->qn('#__rsdirectory_users') )
				   ->set( $db->qn('credits') . ' = ' . $db->qn('credits') . ' - ' . $db->q($cost) )
				   ->where( $db->qn('user_id') . ' = ' . $db->q($user->id) );
				   
			$db->setQuery($query);
			$db->execute();
		}
			
		$this->markAsPaid($id, 1);
			
		// Publish the entry if the user has the right permission.
		if ( RSDirectoryHelper::checkUserPermission('auto_publish_entries') )
		{
			$this->getTable()->publish($id, 1);
		}
			
		return true;
	}
		
    /**
     * Get RSFieldset.
     *
     * @access public
     * 
     * @return RSFieldset
     */
    public function getRSFieldset()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/fieldset.php';
            
        return new RSFieldset();
    }
}