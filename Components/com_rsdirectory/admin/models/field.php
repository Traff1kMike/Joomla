<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Field model.
 */
class RSDirectoryModelField extends JModelAdmin
{
    /**
     * An array of reserved keywords.
     *
     * Fields cannot be named as these reserved keywords.
     *
     * @access private
     *
     * @static
     *
     * @var array
     */
    private static $reserved_keywords = array(
        'reg',
        'user-id',
        'published-time',
        'published',
        'paid',
        'finalize',
    );
        
    /**
     * An array of dependency compatible field types.
     *
     * @access private
     *
     * @static
     *
     * @var array
     */
    private static $dependency_compatible = array(
        'country',
        'dropdown',
        'checkboxgroup',
        'radiogroup',
    );
        
    /**
     * An array of field types that can be used as dependency parents.
     *
     * @access private
     *
     * @static
     *
     * @var array
     */
    private static $dependency_parent_compatible = array(
        'country',
        'dropdown',
        'radiogroup',
    );
        
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
    public function getTable( $type = 'Field', $prefix = 'RSDirectoryTable', $config = array() )
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
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the field id.   
        $id = $jinput->getInt('id');
            
        if ($id)
        {
            // Load the field.
            $field = $this->getTable();
            $field->load($id);
            $field_type_id = $field->field_type_id;
        }
        else
        {
            // Get the field type id.
            $field_type_id = $jinput->getInt('field_type_id');
        }
            
        // Get the Field Types model.
        $fieldtypes_model = RSDirectoryModel::getInstance('FieldTypes');
            
        // Get the field type object.
        $field_type = $fieldtypes_model->getFieldTypeObjectById($field_type_id);
            
        if ($field_type)
        {
            // Get the form.
            $form = $this->loadForm("com_rsdirectoy.{$field_type->xml_file}_properties", "{$field_type->xml_file}_properties", array('control' => 'jform', 'load_data' => $loadData) );
                
            if ( empty($form) )
            {
                return false;
            }
                
            return $form;
        }
            
        return false;
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
        // Check the session for previously entered form data.
        $app  = JFactory::getApplication();
            
        $data = $app->getUserState( 'com_rsdirectory.edit.field.data', array() );
            
        if ( empty($data) )
        {
            // Get the field id.   
            $id = $app->input->getInt('id');
                
            if (!$id)
            {
                // Get the field type id.
                $field_type_id = $app->input->getInt('field_type_id');
                    
                // Get the Field Types model.
                $fieldtypes_model = RSDirectoryModel::getInstance('FieldTypes');
                    
                // Get the field type object.
                $field_type = $fieldtypes_model->getFieldTypeObjectById($field_type_id);
                    
                switch ($field_type->type)
                {
                    case 'country':
                            
                        // Get DBO.
                        $db = JFactory::getDbo();
                            
                        $query = $db->getQuery(true)
                               ->select( $db->qn('name') )
                               ->from( $db->qn('#__rsdirectory_countries') );
                              
                        $db->setQuery($query);
                            
                        // Get the countries.
                        $countries = $db->loadColumn();
                            
                        return array(
                            'items' => implode("\n", $countries),
                        );
                            
                    case 'calendar':
                    case 'dropdown_date_picker':
                            
                        return array(
                            'date_mask' => 'd F Y',
                            'time_mask' => 'g:i a',
                        );
                            
                    default:
                            
                        return array();
                }
            }
                
                
            // Get the field.
            $item = $this->getItem();
                
            $data = array_merge(
                array(
                    'name' => $item->name,
                    'required' => $item->required,
                    'published' => $item->published,
                ),
                (array)$this->getFieldPropertiesObject($id)
            );
        }
            
        return $data;
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
        $return = parent::validate($form, $data, $group);
            
        if (!$return)
            return false;
            
        $data = $return;
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the task.
        $task = $jinput->get('task');     
            
        // Get the field type.
        $field_type = JTable::getInstance('FieldType', 'RSDirectoryTable');
        $field_type->load( empty($data['field_type_id']) ? 0 : $data['field_type_id'] );
            
        if (!$field_type->id)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
            return false;
        }
            
        if ( $field_type->core && !in_array( $task, array('apply', 'save') ) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_REQUEST') );
            return false;
        }
            
        if ( !empty($data['id']) )
        {
            $item = $this->getItem($data['id']);
                
            if (!$item->id)
            {
                $this->setError( JText::_('COM_RSDIRECTORY_INVALID_REQUEST') );
                return false;
            }
                
            if ($data['field_type_id'] != $item->field_type_id)
            {
                $this->setError( JText::_('COM_RSDIRECTORY_CANNOT_CHANGE_FIELD_TYPE') );
                return false;
            }
        }
            
        // Get the fieldsets.
        $fieldsets = $form->getFieldsets();
            
        foreach ($fieldsets as $fieldset)
        {
            $fields = $form->getFieldset($fieldset->name);
                
            foreach ($fields as $field)
            {
                $value = isset($data[$field->fieldname]) ? $data[$field->fieldname] : '';
                    
                switch ($field->fieldname)
                {
                    case 'name':
                            
                        if ( in_array($value, self::$reserved_keywords) )
                        {
                            $this->setError( JText::sprintf('COM_RSDIRECTORY_FIELD_PROPERTY_ERROR_RESERVED_KEYWORD', $value) );
                            $return = false;
                                
                            continue;
                        }
                            
                        // Check if the name property is valid.
                        if ( !preg_match('/^([a-z0-9-])+$/i', $value) )
                        {
                            $this->setError( JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_ERROR_INVALID_NAME') );
                            $return = false;
                                
                            continue;
                        }
                            
                        // Do not validate the uniqueness of the name if the task is Save as Copy.
                        if ($task != 'save2copy')
                        {
                            $query = $db->getQuery(true)
                                   ->select( $db->qn('id') )
                                   ->from( $db->qn('#__rsdirectory_fields') )
                                   ->where( $db->qn('name') . ' = ' . $db->q($value) )
                                   ->where( $db->qn('id') . ' != ' . $db->q($data['id']) );
                                
                            $db->setQuery($query);
                                
                            // Check if the name property value is unique.
                            if ( $db->loadResult() )
                            {
                                $this->setError( JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_ERROR_INVALID_NAME') );
                                $return = false;
                                    
                                continue;
                            }
                        }
                            
                        break;
                }
            }
        }
            
        switch ($field_type->type)
        {
            case 'publishing_period':
                
                $items = isset($data['items']) ? $data['items'] : array();
                    
                if ($items)
                {
                    if ( isset($items['periods'], $items['credits']) && is_array($items['periods']) && is_array($items['credits']) )
                    {
                        $periods = $items['periods'];
                        $credits = $items['credits'];
                            
                        $periods_len = count($periods);
                        $credits_len = count($credits);
                            
                        $len = $periods_len > $credits_len ? $periods_len : $credits_len;
                            
                        for ($i = 0; $i < $len; $i++)
                        {
                            if ( !isset($periods[$i], $credits[$i]) || !is_numeric($periods[$i]) || !is_numeric($credits[$i]) )
                            {
                                $this->setError( JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_ERROR_INVALID_PUBLISHING_PERIOD') );
                                return false;
                            }
                        }
                    }
                    else
                    {
                        $this->setError( JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_ERROR_INVALID_PUBLISHING_PERIOD') );
                        return false;
                    }
                }
                    
                break;
                    
            case 'youtube':
                   
                if ( !empty($data['video_size']) && $data['video_size'] != 'custom')
                    break;
                  
                if (
                    ( empty($data['video_width']) || empty($data['video_height']) ) ||
                    !( is_numeric($data['video_width']) && is_numeric($data['video_height']) )
                )
                {
                    $this->setError( JText::_('COM_RSDIRECTORY_FIELD_PROPERTY_ERROR_INVALID_CUSTOM_VIDEO_SIZE') );
                    return false;
                }
                    
                break;
        }
            
        return $return;
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
        // Exit the function if the data array is invalid.
        if (!$data)
            return false;
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get the JInput object.
        $jinput = $app->input;
            
        // Get the field type.
        $field_type = JTable::getInstance('FieldType', 'RSDirectoryTable');
        $field_type->load($data['field_type_id']);
          
            
        // Get the field table.
        $field = $this->getTable();
            
        // Save as Copy.
        if ( $jinput->get('task') == 'save2copy' )
        {
            unset($data['id']);
                
            $name = isset($data['name']) ? $data['name'] : '';
            $name_tmp = $name;
                
            $i = 2;
                
            do
            {
                $query = $db->getQuery(true)
                       ->select( $db->qn('id') )
                       ->from( $db->qn('#__rsdirectory_fields') )
                       ->where( $db->qn('name') . ' = ' . $db->q($name_tmp) );
                    
                $db->setQuery($query);
                    
                // Check if the name property value is unique.
                if ( $result = $db->loadResult() )
                {
                    $name_tmp = $name . $i;
                        
                    $i++;
                }
            }
            while ($result);
                
            $data['name'] = $name_tmp;
        }
        // Process an existing field.
        else if ($data['id'])
        {
            // Load field object.
            $field->load($data['id']);
                
            // Delete all the existing properties.
            $query = $db->getQuery(true)
                   ->delete( $db->qn('#__rsdirectory_fields_properties') )
                   ->where( $db->qn('field_id') . ' = ' . $db->q($data['id']) );
                    
            $db->setQuery($query);
            $db->execute();
        }
            
        $field->save($data);
            
        // Get the field id.
        $id = $field->id;
            
        // Set the column names and form field names.
        if ($field_type->expect_value)
        {
            if ($field_type->create_column)
            {
                $field->column_name = $field_type->core ? $field_type->field_type : "f_$id";  
            }
                
            $field->form_field_name = str_replace('-', '_', $data['name']);
                
            $field->store();
        }
            
        // Handle form assignation.
        if (!$field_type->all_forms)
        {
            if ( empty($data['forms']) )
            {
                $data['forms'] = array();
            }
            else
            {
                $data['forms'] = RSDirectoryHelper::arrayInt($data['forms']);    
            }
                
            $query = $db->getQuery(true)
                   ->select( $db->qn('form_id') )
                   ->from( $db->qn('#__rsdirectory_forms_fields') )
                   ->where( $db->qn('field_id') . ' = ' . $db->q($field->id) );
                   
            $db->setQuery($query);
            $forms = $db->loadColumn();
               
            if ( $remove = array_diff($forms, $data['forms']) )
            {
                $query = $db->getQuery(true)
                       ->delete( $db->qn('#__rsdirectory_forms_fields') )
                       ->where( $db->qn('field_id') . ' = ' . $db->q($id) )
                       ->where( $db->qn('form_id') . ' IN (' . implode(',', $remove) . ')' );
                       
                $db->setQuery($query);
                $db->execute();
            }
                
            if ( $add = array_diff($data['forms'], $forms) )
            {
                foreach ($add as $form_id)
                {
                    $this->assign2Form($id, $form_id);
                }
            }
        }
            
        $this->setState( $this->getName() . '.id', $id );
            
            
        // Insert the properties in the database.
        $query = $db->getQuery(true)
               ->insert( $db->qn('#__rsdirectory_fields_properties') )
               ->columns( $db->qn( array('field_id', 'property_name', 'value') ) );
                
        // Do not insert these properties.
        $skip = array('name', 'required', 'published', 'dependency_value', 'field_type_id', 'forms');
            
        foreach ($data as $property => $value)
        {
            if ( in_array($property, $skip) )
                continue;
                
            // Process the publishing periods.
            if ($property == 'items')
            {
                if ($field_type->type == 'publishing_period')
                {
                    $items = array();
                        
                    foreach ($value['periods'] as $i => $period)
                    {
                        $items[] = (object)array(
                            'period' => $period,
                            'credits' => $value['credits'][$i],
                        );
                    }
                        
                    $value = serialize($items);
                }
                else if ( in_array($field_type->type, self::$dependency_compatible) && is_array($value) )
                {
                    // Initialize the dependencies insert query.
                    $dependencies_query = $db->getQuery(true)
                                        ->insert( $db->qn('#__rsdirectory_fields_dependencies') )
                                        ->columns( $db->qn( array('field_id', 'parent_id', 'value', 'items') ) );
                                        
                    foreach ($value as $parent_id => $items_list)
                    {
                        // Skip anything else besides the selected dependency.
                        if ($data['dependency'] != $parent_id)
                            continue;
                            
                        // Delete old dependencies.
                        $delete_query = $db->getQuery(true)
                                      ->delete( $db->qn('#__rsdirectory_fields_dependencies') )
                                      ->where( $db->qn('field_id') . ' = ' . $db->q($id) . ' AND ' . $db->qn('parent_id') . ' = ' . $db->q($parent_id) );
                                        
                        $db->setQuery($delete_query);
                        $db->execute();
                            
                        foreach ($items_list as $parent_value => $items)
                        {
                            // Skip empty files.
                            if ( !trim($items) )
                                continue;
                                
                            $dependency = array(
                                $db->q($id),
                                $db->q($parent_id),
                                $db->q( base64_decode($parent_value) ),
                                $db->q($items),
                            );
                                
                            $dependencies_query->values( implode(',', $dependency) );
                        }
                    }
                        
                    if ($dependencies_query->values)
                    {
                        $db->setQuery($dependencies_query);
                        $db->execute();
                    }
                        
                    continue;
                }
            }
                
            $values = array(
                $db->q($id),
                $db->q($property),
                $db->q($value),
            );
                
            $query->values( implode(',', $values) );
        }
            
        if ($query->values)
        {
            $db->setQuery($query);
            $db->execute();    
        }
            
            
        // Clean the session data.
        $app->setUserState('com_rsdirectory.edit.field.data', null);
            
        return true;
    }
        
    /**
     * Save the order.
     *
     * @access public
     *
     * @param array $pks
     * @param array $order
     *
     * @return mixed
     */
    public function saveOrder($pks = null, $order = null)
    {    
        if ( !JFactory::getUser()->authorise('core.edit.state', $this->option) )
        {
            JLog::add( JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror' );
            return false;
        }
            
        $form_id = JFactory::getApplication()->input->get('form_id');
            
        if (!$form_id)
        {
            return JError::raiseWarning( 500, JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
        }
            
        if (!$pks)
        {
            return JError::raiseWarning( 500, JText::_('JERROR_NO_ITEMS_SELECTED') );
        }
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Update ordering values.
        foreach ($pks as $i => $pk)
        {
            $query = $db->getQuery(true)
                   ->update( $db->qn('#__rsdirectory_forms_fields') )
                   ->set( $db->qn('ordering') . ' = ' . $db->q( isset($order[$i]) ? $order[$i] : 0 ) )
                   ->where( $db->qn('form_id') . ' = ' . $db->q($form_id) . ' AND ' . $db->qn('field_id') . ' = ' . $db->q($pk) );
                  
            $db->setQuery($query);
            $db->execute();
        }
            
        // Clear the component's cache.
        $this->cleanCache();
            
        return true;
    }
        
    /**
	 * Method to adjust the ordering of a row.
	 *
	 * Returns NULL if the user did not have edit
	 * privileges for any of the selected primary keys.
	 *
	 * @param   integer  $pks    The ID of the primary key to move.
	 * @param   integer  $delta  Increment, usually +1 or -1
	 *
	 * @return  mixed  False on failure or error, true on success, null if the $pk is empty (no items selected).
	 *
	 * @since   11.1
	 */
	public function reorder($pks, $delta = 0)
	{
        if ( !JFactory::getUser()->authorise('core.edit.state', $this->option) )
        {
            JLog::add( JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror' );
            return false;
        }
            
        $form_id = JFactory::getApplication()->input->get('form_id');
            
        if (!$form_id)
        {
            return JError::raiseWarning( 500, JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
        }
            
        if (!$pks)
        {
            return JError::raiseWarning( 500, JText::_('JERROR_NO_ITEMS_SELECTED') );
        }
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        foreach ($pks as $pk)
        {
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_forms_fields') )
                   ->where( $db->qn('form_id') . ' = ' . $db->q($form_id) )
                   ->where( $db->qn('field_id') . ' = ' . $db->q($pk) );
                   
            $db->setQuery($query);
                
            // Get the currently moved field.
            if ( $field = $db->loadObject() )
            {
                $query = $db->getQuery(true)
                       ->select('*')
                       ->from( $db->qn('#__rsdirectory_forms_fields') )
                       ->where( $db->qn('form_id') . ' = ' . $db->q($form_id) );
                        
                // If the movement delta is negative move the row up.
                if ($delta < 0)
                {
                    $query->where( $db->qn('ordering') . ' < ' . $db->q($field->ordering) )
                          ->order( $db->qn('ordering') . ' DESC' );
                }
                // If the movement delta is positive move the row down.
                else if ($delta > 0)
                {
                    $query->where( $db->qn('ordering') . ' > ' . $db->q($field->ordering) )
                          ->order( $db->qn('ordering') . ' ASC' );
                }
                    
                $db->setQuery($query, 0, 1);
                    
                // Get the other field that will be swapped with the current one.
                if ( $other_field = $db->loadObject() )
                {
                    // Update the current field.
                    $query = $db->getQuery(true)
                           ->update( $db->qn('#__rsdirectory_forms_fields') )
                           ->set( $db->qn('ordering') . ' = ' . $db->q($field->ordering) )
                           ->where( $db->qn('form_id') . ' = ' . $db->q($form_id) )
                           ->where( $db->qn('field_id') . ' = ' . $db->q($other_field->field_id) );
                           
                    $db->setQuery($query);
                    $db->execute();
                        
                    // Update the other field.
                    $query = $db->getQuery(true)
                           ->update( $db->qn('#__rsdirectory_forms_fields') )
                           ->set( $db->qn('ordering') . ' = ' . $db->q($other_field->ordering) )
                           ->where( $db->qn('form_id') . ' = ' . $db->q($form_id) )
                           ->where( $db->qn('field_id') . ' = ' . $db->q($field->field_id) );
                           
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
            
        return true;
    }
        
    /**
     * Get field properties object.
     *
     * @access public
     * 
     * @param int $id The field id.
     * 
     * @return mixed
     */
    public function getFieldPropertiesObject($id)
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('property_name') . ', ' . $db->qn('value') )
               ->from( $db->qn('#__rsdirectory_fields_properties') )
               ->where( $db->qn('field_id') . ' = ' . $db->q($id) );
                
        $db->setQuery($query);
            
        $results = $db->loadObjectList();
            
        if (!$results)
            return false;
        
        // Initialize the properties object.    
        $properties = new stdClass;
            
        foreach ($results as $result)
        {
            $properties->{$result->property_name} = $result->value;
        }
            
        return $properties;
    }
        
    /**
     * Save files order.
     *
     * @access public
     *
     * @param array $pks Files ids.
     *
     * @return bool
     */
    public function saveFilesOrder($pks)
    {
        if ( $pks && is_array($pks) )
        {
            // Get DBO.
            $db = JFactory::getDbo();
                
            // Get user object.
            $user = JFactory::getUser();
                
            $authorised = $user->authorise('core.manage', 'com_rsdirectory');
                
            foreach ($pks as $i => $pk)
            {
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_uploaded_files_fields_relations', 'fr') )
                       ->innerJoin( $db->qn('#__rsdirectory_uploaded_files', 'f') . ' ON ' . $db->qn('fr.file_id') . ' = ' . $db->qn('f.id')  )
                       ->set( $db->qn('fr.ordering') . ' = ' . $db->q($i + 1) )
                       ->where( $db->qn('f.id') . ' = ' . $db->q($pk) );
                        
                if (!$authorised)
                {
                    $query->where( $db->qn('f.user_id') . ' = ' . $db->q($user->id) );   
                }
                    
                $db->setQuery($query);
                $db->execute();
            }
        }
            
        return true;
    }
        
    /**
     * Delete file.
     *
     * @access public
     *
     * @param int $pk File id.
     *
     * @return bool
     */
    public function deleteFile($pk)
    {
        if (!$pk)
            return false;
            
        $user = JFactory::getUser();
        $authorised = $user->authorise('core.manage', 'com_rsdirectory');
            
        // Get the file.
        $file = RSDirectoryHelper::getFileObject($pk);
            
        if (!$file)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_FILE_DELETION_INVALID_ERROR') );
            return false;
        }
            
        if ($file->user_id != $user->id && !$authorised)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_FILE_DELETION_PERMISSION_ERROR') );
            return false;
        }
            
        // Get the field.
        $field = RSDirectoryHelper::getField($file->field_id);
            
        if ( !empty($field->required) )
        {
            // Get the files list for this field and entry.
            $files_list = RSDirectoryHelper::getFilesObjectList($field->id, $file->entry_id);
                
            if ( !isset($files_list[1]) )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_FILE_DELETION_REQUIRED_ERROR') );
                return false;
            }
        }
            
        // Delete the file.            
        JFile::delete(JPATH_ROOT . "/components/com_rsdirectory/files/entries/$file->entry_id/$file->file_name");
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Remove the file from the database.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_uploaded_files') )
               ->where( $db->qn('id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
        $db->execute();
            
        // Remove the file -> field relation.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
               ->where( $db->qn('file_id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
        $db->execute();
            
        return true;    
    }
    
    /**
     * Get a dropdown field's items under the form of select options.
     *
     * @access public
     *
     * @param int Primary key value.
     *
     * @return mixed
     */
    public function getItemsOptions($pk)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Check if the items are in the dependencies table.
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_fields_dependencies') )
               ->where( $db->qn('field_id') . ' = ' . $db->q($pk) )
               ->order( $db->qn('value') );
               
        $db->setQuery($query);
            
        if ( $dependencies = $db->loadObjectList() )
        {
            // Initialize the items.
            $items = '';
                
            foreach ($dependencies as $i => $dependency)
            {
                $items .= ($items ? "\n" : '') . "--- $dependency->value ---[d]\n";
                $items .= $dependency->items;
            }
                
            $items = RSDirectoryHelper::getOptions($items);
                
            $options = RSDirectoryHelper::getGroupedListOptions($items);
        }
        else
        {
            $query = $db->getQuery(true)
                   ->select( $db->qn('value') )
                   ->from( $db->qn('#__rsdirectory_fields_properties') )
                   ->where( $db->qn('property_name') . ' = ' . $db->q('items') . ' AND ' . $db->qn('field_id') . ' = ' . $db->q($pk) );
                    
            $db->setQuery($query);
                
            $items = $db->loadResult();
                
            $items = RSDirectoryHelper::getOptions($items);
                
            $options = RSDirectoryHelper::getGroupedListOptions($items);
        }
            
        if ($options)
        {
            $result = JHtml::_('select.groupedlist', $options, '');
                
            return preg_replace( array('/<select.*?>/i', '/<\/select>/'), '',  $result );
        }
            
        return false;
    }
        
    /**
     * Assign the form fields to a specified form.
     *
     * @access public
     *
     * @param mixed $field_ids
     * @param int $form_id
     *
     * @return bool
     */
    public function assign2Form($field_ids, $form_id)
    {
        if ( !is_array($field_ids) )
        {
            $field_ids = (array)$field_ids;
        }
            
        $db = JFactory::getDbo();
            
        // Check if the form exists.
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_forms') )
               ->where( $db->qn('id') . ' = ' . $db->q($form_id) );
               
        $db->setQuery($query);
            
        if ( !$db->loadResult() )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_SPECIFIED_FORM_NOT_FOUND') );
            return false;
        }
            
        $field_ids = RSDirectoryHelper::arrayInt($field_ids);
            
        if (!$field_ids)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_FIELDS_PROVIDED') );
            return false;
        }
            
        // Filter the field ids.
        $query = $db->getQuery(true)
               ->select( $db->qn('id') )
               ->from( $db->qn('#__rsdirectory_fields') )
               ->where( $db->qn('id') . ' IN (' . implode(',', $field_ids) . ')' );
               
        $db->setQuery($query);
            
        $field_ids = $db->loadColumn();
            
        if (!$field_ids)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_FIELDS_PROVIDED') );
            return false;
        }
            
        // Remove the values that already exist in the database.
        $query = $db->getQuery(true)
               ->select( $db->qn('field_id') )
               ->from( $db->qn('#__rsdirectory_forms_fields') )
               ->where( $db->qn('field_id') . ' IN (' . implode(',', $field_ids) . ') AND ' . $db->qn('form_id') . ' = ' . $db->q($form_id) );
               
        $db->setQuery($query);
            
        $existing = $db->loadColumn();
            
        $field_ids = array_diff($field_ids, $existing);
            
        // Exit the function if the values already exist in the database.
        if (!$field_ids)
            return true;
            
        // Get the maximum ordering.
        $query = $db->getQuery(true)
               ->select('MAX(' . $db->qn('ordering') . ')')
               ->from( $db->qn('#__rsdirectory_forms_fields') )
               ->where( $db->qn('form_id') . ' = ' . $db->q($form_id) );
               
        $db->setQuery($query);
            
        $ordering = $db->loadResult();
            
        $columns = array(
            'form_id',
            'field_id',
            'ordering',
        );
            
        // Build the query to add the messages.
        $query = $db->getQuery(true)
               ->insert( $db->qn('#__rsdirectory_forms_fields') )
               ->columns( $db->qn($columns) );
                
        foreach ($field_ids as $field_id)
        {
            $values = array(
                $db->q($form_id),
                $db->q($field_id),
                $db->q(++$ordering),
            );
                
            $query->values( implode(',', $values) );
        }
            
        $db->setQuery($query);
        return $db->execute();
    }
        
    /**
     * Method to get dependency items.
     *
     * @access public
     *
     * @param int $parent_id
     * @param mixed $value
     *
     * @return array
     */
    public function getDependencyItems($parent_id, $value)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Initialize the results array.
        $results = array();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('field_id') . ', ' . $db->qn('value', 'parent_id') )
               ->from( $db->qn('#__rsdirectory_fields_properties') )
               ->where( $db->qn('property_name') . ' = ' . $db->q('dependency') );
                
        $db->setQuery($query);
            
        $list = $db->loadObjectList();
            
        $children_ids = array();
            
        self::getDependencyChildrenIds($list, $parent_id, $children_ids);
            
        $fields = array();
            
        foreach ($children_ids as $field_id)
        {
            if ( empty($fields[$field_id]) )
            {
                $fields[$field_id] = RSDirectoryHelper::getField($field_id);
            }
                
            if ($fields[$field_id]->field_type == 'dropdown')
            {
               $results[$field_id] =  array(
                    'options' => '',
                );
            }
            else
            {
                $results[$field_id] =  array(
                    'items_group' => JText::_('COM_RSDIRECTORY_NOTHING_TO_SELECT'),
                );
            }
        }
            
        $dependencies = RSDirectoryHelper::getDependencies(0, $parent_id, $value);
            
        if ($dependencies)
        {
            $parent_field = RSDirectoryHelper::getField($parent_id);
                
            $data = array(
                $parent_field->form_field_name => $value,
            );
                
            JFactory::getApplication()->setUserState('com_rsdirectory.edit.entry.data', $data);
                
            require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/formfield.php';
                
            foreach ($dependencies as $dependency)
            {
                if ( empty($fields[$dependency->field_id]) )
                    continue;
                    
                $field = $fields[$dependency->field_id];
                    
                if ($field->field_type == 'dropdown')
                {
                    $items = RSDirectoryHelper::getOptions($dependency->items);
                        
                    $options = RSDirectoryHelper::getGroupedListOptions($items);
                        
                    $options = JHtml::_('select.groupedlist', $options, '');
                        
                    $results[$dependency->field_id] =  array(
                        'options' => preg_replace( array('/<select.*?>/i', '/<\/select>/'), '',  $options ),
                    );
                }
                else
                {
                    $form_field = RSDirectoryFormField::getInstance($field, null)->generate();
                        
                    // Get the inner content of rsdir-items-group.
                    $pattern = '#<div class="rsdir-items-group">(.*?)</div><!-- .rsdir-items-group -->#is';
                        
                    preg_match($pattern, $form_field, $matches);
                        
                    $results[$dependency->field_id] =  array(
                        'items_group' => empty($matches[1]) ? '' : $matches[1],
                    );
                }
            }
        }
            
        return $results;
    }
        
    /**
     * Method to get dependency items for the filtering module.
     *
     * @access public
     *
     * @param int $parent_id
     * @param mixed $value
     * @param array $module_options
     *
     * @return array
     */
    public function getFiltersDependencyItems( $parent_id, $value, $module_options = array() )
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Initialize the results array.
        $results = array();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('p1.field_id') . ', ' . $db->qn('p1.value', 'parent_id') )
               ->from( $db->qn('#__rsdirectory_fields_properties', 'p1') )
               ->innerJoin($db->qn('#__rsdirectory_fields_properties', 'p2') . ' ON ' . $db->qn('p1.field_id') . ' = ' . $db->qn

('p2.field_id'))
               ->where( $db->qn('p1.property_name') . ' = ' . $db->q('dependency') )
               ->where( $db->qn('p2.property_name') . ' = ' . $db->q('use_dependency') )
               ->where( $db->qn('p2.value') . ' = ' . $db->q(1) );
                
        $db->setQuery($query);
            
        $list = $db->loadObjectList();
            
        $children_ids = array();
            
        self::getDependencyChildrenIds($list, $parent_id, $children_ids);
            
        $fields = array();
            
        foreach ($children_ids as $field_id)
        {
            if ( empty($fields[$field_id]) )
            {
                $fields[$field_id] = RSDirectoryHelper::getField($field_id);
            }
                
            if ($fields[$field_id]->field_type == 'dropdown')
            {
               $results[$field_id] =  array(
                    'options' => '',
                );
            }
            else
            {
                $results[$field_id] =  array(
                    'items_group' => JText::_('COM_RSDIRECTORY_NOTHING_TO_SELECT'),
                );
            }
        }
            
        $dependencies = RSDirectoryHelper::getDependencies(0, $parent_id, $value);
            
        if ($dependencies)
        {
            $parent_field = RSDirectoryHelper::getField($parent_id);
                
            $f = array(
                $parent_field->form_field_name => $value,
            );
                
            JFactory::getApplication()->input->set('f', $f);
                
            require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/filter.php';
                
            foreach ($dependencies as $dependency)
            {
                if ( empty($fields[$dependency->field_id]) )
                    continue;
                    
                $field = $fields[$dependency->field_id];
                    
                if ($field->field_type == 'dropdown')
                {
                    $items = RSDirectoryHelper::getOptions($dependency->items);
                        
                    $options = RSDirectoryHelper::getGroupedListOptions($items);
                        
                    $options = JHtml::_('select.groupedlist', $options, '');
                        
                    $results[$dependency->field_id] =  array(
                        'options' => preg_replace( array('/<select.*?>/i', '/<\/select>/'), '',  $options ),
                    );
                }
                else
                {
                    $form_field = RSDirectoryFilter::getInstance($field, $module_options)->generate();
                        
                    // Get the inner content of rsdir-items-group.
                    $pattern = '#<div class="rsdir-items-group">(.*?)</div><!-- .rsdir-items-group -->#is';
                        
                    preg_match($pattern, $form_field, $matches);
                        
                    $results[$dependency->field_id] =  array(
                        'items_group' => empty($matches[1]) ? '' : $matches[1],
                    );
                }
            }
        }
            
        return $results;
    }
        
    /**
     * Method to get an array of dependency compatible field types.
     *
     * @access public
     *
     * @return array
     */
    public function getDependencyCompatible()
    {
        return self::$dependency_compatible;
    }
        
    /**
     * Method to get an array field types that can be used as dependency parents.
     *
     * @access public
     *
     * @return array
     */
    public function getDependencyParentCompatible()
    {
        return self::$dependency_parent_compatible;
    }
        
    /**
     *
     * @access public
     *
     * @static
     *
     * @param array $array
     * @param int $parent_id
     * @param array $results
     */
    public static function getDependencyChildrenIds($list, $parent_id, &$results)
    {
        foreach ($list as $item)
        {
            if ($item->parent_id == $parent_id)
            {
                $results[] = $item->field_id;
                    
                self::getDependencyChildrenIds($list, $item->field_id, $results);
            }
        }
    }
        
    /**
     * Method override to skip the check-out.
     *
     * @access public
     *
     * @param mixed $pk
     *
     * @return bool
     */
	public function checkout($pk = null)
	{
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
        
    /**
     * Get RSTabs.
     *
     * @access public
     * 
     * @return RSTabs
     */
    public function getRSTabs()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/tabs.php';
            
        return new RSTabs('com-rsdirectory-field');
    }
}