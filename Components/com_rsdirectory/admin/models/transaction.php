<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transaction model.
 */
class RSDirectoryModelTransaction extends JModelAdmin
{
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
    public function getTable( $type = 'Transaction', $prefix = 'RSDirectoryTable', $config = array() )
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
     * @return bool
     */
    public function getForm( $data = array(), $loadData = true )
    {
        return false;
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
            
        if (!$pk)
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        $select = array(
            $db->qn('t') . '.*',
            $db->qn("u.$author", 'user'),
            $db->qn('e.id', 'entry_id'),
            $db->qn('e.title', 'entry_title'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_users_transactions', 't') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('t.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('t.entry_id') . ' = ' . $db->qn('e.id') )
               ->where( $db->qn('t.id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
            
        return $db->loadObject();
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
    