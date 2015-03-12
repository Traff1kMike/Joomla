<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Group table.
 */
class RSDirectoryTableGroup extends JTable
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
        parent::__construct('#__rsdirectory_groups', 'id', $db);
    }
        
    /**
     * Method to delete a row from the database table by primary key value.
     *
     * @access public
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
            
        // Delete group - jgroup relations.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_groups_relations') )
               ->where( $db->qn('group_id') . ' = ' . $db->q($pk) );
                
        $db->setQuery($query);
        $db->execute();
            
        return parent::delete($pk);
    }
}
