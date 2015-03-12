<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transaction model.
 */
class RSDirectoryModelTransaction extends JModelItem
{
    /**
     * Model context string.
     *
     * @var string
     */
    protected $_context = 'com_rsdirectory.transaction';
        
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
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
			
		return JTable::getInstance($type, $prefix, $config);
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
			
		$table = $this->getTable();
		$table->load( array( 'id' => $pk, 'user_id' => JFactory::getUser()->id ) );
			
		return $table->id ? $table : null;
    }
}