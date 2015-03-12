<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Reported Entry model.
 */
class RSDirectoryModelReportedEntry extends JModelAdmin
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
    public function getTable( $type = 'ReportedEntry', $prefix = 'RSDirectoryTable', $config = array() )
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
        $pk = $pk ? $pk : JFactory::getApplication()->input->getInt('id');
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        $select = array(
            $db->qn('r') . '.*',
            $db->qn('e.title'),
            $db->qn("u.$author", 'entry_author'),
            $db->qn('u.id', 'entry_author_id'),
            $db->qn("ru.$author", 'report_author'),
            $db->qn('ru.id', 'report_author_id'),
        );
            
        // Get query.
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_entries_reported', 'r') )
               ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__users', 'ru') . ' ON ' . $db->qn('r.user_id') . ' = ' . $db->qn('ru.id') )
               ->where( $db->qn('r.id') . ' = ' . $db->q($pk) );
               
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