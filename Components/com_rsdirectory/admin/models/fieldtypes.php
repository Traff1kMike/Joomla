<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Field Types model.
 */
class RSDirectoryModelFieldTypes extends JModelLegacy
{
    /**
     * The database resource.
     *
     * @var JDatabaseMySQLi
     * 
     * @access protected
     */ 
    protected $_db;
        
    /**
     * Get an object list of field types.
     *
     * @access public
     * 
     * @param mixed $core
     * 
     * @return mixed
     */
    public function getFieldTypesObjectList($core = null)
    {
        $db = $this->_db;
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_field_types') )
               ->order( $db->qn('ordering') );
              
        if ($core !== null)
        {
            $query->where( $db->qn('core') . ' = ' . $db->q($core) );      
        }
            
        $db->setQuery($query);
            
        $fields = $db->loadObjectList();
            
        // Get the break type.
        $break_type = RSDirectoryConfig::getInstance()->get('break_type');
            
        if ($fields)
        {
            foreach ($fields as $field)
            {
                if ($field->type == 'section_break')
                {
                    $field->name = JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($break_type) );
                }
                else
                {
                    $field->name = JText::_( 'COM_RSDIRECTORY_FIELDS_' . strtoupper($field->type) );
                }
            }
        }
            
        return $fields;
    }
        
    /**
     * Get field type object by id.
     *
     * @access public
     * 
     * @param int $id
     * 
     * @return mixed
     */
    public function getFieldTypeObjectById($id)
    {
        $db = $this->_db;
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_field_types') )
               ->where( $db->qn('id') . ' = ' . $db->q($id) );
              
        $db->setQuery($query);
            
        return $db->loadObject();
    }
        
    /**
     * Get an object list of custom field types.
     *
     * @access public
     * 
     * @return mixed
     */
    public function getCustomFieldTypesObjectList()
    {
        return $this->getFieldTypesObjectList(0);
    }
}