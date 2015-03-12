<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Form table.
 */
class RSDirectoryTableForm extends JTable
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
        parent::__construct('#__rsdirectory_forms', 'id', $db);
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
        parent::store($updateNulls);
            
        // Get the name of the primary key.
        $k = $this->_tbl_key;
            
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get the JInput object.
        $category_id = $app->input->getInt('category_id');
            
        if ( !$app->isSite() && $category_id )
        {
            $category = JTable::getInstance('Category', 'RSDirectoryTable');
                
            $category->load($category_id);
                
            if ($category->id)
            {
                $params = new JRegistry($category->params);
                    
                $params->set('form_id', $this->$k);
                    
                $category->params = $params->toString();
                    
                $category->store();
            }
        }
    }
        
    /**
     * Method to delete a row from the database table by primary key value.
     *
     * @param mixed
     *
     * @return boolean
     *
     * @throws UnexpectedValueException
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
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Delete the fields assigned to the form.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_forms_fields') )
               ->where( $db->qn('form_id') . ' = ' . $db->q($pk) );
                
        $db->setQuery($query);
        $db->execute();
            
        return parent::delete($pk);
    }
}
