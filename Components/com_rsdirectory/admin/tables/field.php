<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Field table.
 */
class RSDirectoryTableField extends JTable
{
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param object Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__rsdirectory_fields', 'id', $db);
    }
        
    /**
     * Method to store a row in the database from the JTable instance properties.
     *
     * @param boolean $updateNulls True to update fields even if they are null.
     *
     * @return boolean True on success.
     */
    public function store($updateNulls = false)
    {
        // Get the name of the primary key.
        $k = $this->_tbl_key;
            
        // Get the field type.
        $field_type = self::getInstance('FieldType', 'RSDirectoryTable');
        $field_type->load($this->field_type_id);
            
        $this->published = $field_type->always_published || $this->published;
        $this->name = $field_type->core ? str_replace('_', '-', $field_type->type) : $this->name;
			
		$id = $this->$k;
           
        // We will create a database column in the entries custom fields values table.
        if (!$id && !$field_type->core && $field_type->create_column)
        {
            $create_column = true;
        }
            
        $return = parent::store($updateNulls);
            
        // Create a database column in the entries custom fields values table.
        if ($return)
        {
			// Get DBO.
			$db = JFactory::getDBO();
				
			$query = "ALTER TABLE " . $db->qn('#__rsdirectory_entries_custom');
				
			// Create 3 columns for the map field.
			if (!$id && $field_type->type == 'map')
			{
				$query .= " ADD " . $db->qn("f_{$this->$k}_address") . " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL," .
				          " ADD " . $db->qn("f_{$this->$k}_lat") . " FLOAT(10, 6) NOT NULL," .
						  " ADD " . $db->qn("f_{$this->$k}_lng") . " FLOAT(10, 6) NOT NULL";
			}
			// Create just a single column for the rest of the fields.
			else if ( !empty($create_column) )
			{
				// Create a datetime column for the calendar & dropdown date picker fields.
				if ( in_array( $field_type->type, array('calendar', 'dropdown_date_picker') ) )
				{
					$query .= " ADD " . $db->qn("f_" . $this->$k) . " DATETIME NOT NULL";
				}
				// Create a text column for the rest of the fields.
				else
				{
					$query .= " ADD " . $db->qn("f_" . $this->$k) . " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
				}
			}
				
			$db->setQuery($query);
				
			$db->execute();
        }
            
        return $return;
    }
        
    /**
     * Method to set the publishing state for a row or list of rows in the database table.  
     *
     * @param mixed $pks 
     * @param integer $state 
     * @param integer $userId
     *
     * @return boolean
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_key;
            
		// Sanitize input.
		JArrayHelper::toInteger($pks);
            
        // If there are no primary keys set check to see if the instance key is set.
		if ( empty($pks) )
		{
			if (!$this->$k)
                return false;
                
			$pks = array($this->$k);
		}
            
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('field_type_id') )
               ->from( $db->qn('#__rsdirectory_fields') )
               ->where( $db->qn('id') . ' IN (' . implode(',', $pks) . ')' );
               
        $db->setQuery($query);
            
        if ( $field_type_ids = $db->loadColumn() )
        {
            $always_published = RSDirectoryHelper::getAlwaysPublishedFieldTypesIds();
                
            foreach ($field_type_ids as $field_type_id)
            {
                if ( in_array($field_type_id, $always_published) )
                {
                    $this->setError( JText::_('COM_RSDIRECTORY_FIELD_ALWAYS_PUBLISHED_OPERATION_ERROR') );
                    return false;
                }
            }
        }
            
        return parent::publish($pks, $state, $userId);
    }
        
    /**
     * Method to delete a row from the database table by primary key value.
     *
     * @access public
     *
     * @param mixed
     *
     * @return boolean
     */
    public function delete($pk = null)
    {
        $k = $this->_tbl_key;
        $pk = is_null($pk) ? $this->$k : $pk;
            
        // If no primary key is given, return false.
        if ( is_null($pk) )
        {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }
        
        $core = RSDirectoryHelper::getCoreFieldTypesIds();
            
        if ( in_array($this->field_type_id, $core) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_CORE_FIELDS_OPERATION_ERROR') );
            return false;
        }
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get the field type.
        $field_type = self::getInstance('FieldType', 'RSDirectoryTable');
        $field_type->load($this->field_type_id);
            
        if ($field_type->type == 'fileupload')
        {
            $files_list = RSDirectoryHelper::getFilesObjectList($pk);
                
            if ($files_list)
            {
                RSDirectoryHelper::deleteFiles($files_list);
            }
        }
            
		if ($field_type->type == 'map')
		{
			$query = "ALTER TABLE " . $db->qn('#__rsdirectory_entries_custom') .
			         " DROP " . $db->qn("f_{$pk}_address") . "," .
					 " DROP " . $db->qn("f_{$pk}_lat") . "," .
					 " DROP " . $db->qn("f_{$pk}_lng");
					 
			$db->setQuery($query);
                
            $db->execute();		 
		}
		// Delete the custom field column.
		else if (!$field_type->core && $field_type->create_column)
        {
            $query = "ALTER TABLE " . $db->qn('#__rsdirectory_entries_custom') . " DROP " . $db->qn("f_$pk");
                
            $db->setQuery($query);
                
            $db->execute();
        }
            
        // Delete the field properties.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_fields_properties') )
               ->where( $db->qn('field_id') . ' = ' . $db->q($pk) );
                
        $db->setQuery($query);
        $db->execute();
            
        // Delete the form field.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_forms_fields') )
               ->where( $db->qn('field_id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
        $db->execute();
            
        // Delete dependencies.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_fields_dependencies') )
               ->where( $db->qn('field_id') . ' = ' . $db->q($pk) . ' OR ' . $db->qn('parent_id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
        $db->execute();
            
        return parent::delete($pk);
    }
}
