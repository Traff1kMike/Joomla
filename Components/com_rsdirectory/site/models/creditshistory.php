<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits History model.
 */
class RSDirectoryModelCreditsHistory extends JModelList
{
    /**
     * Method to get a JDatabaseQuery object for retrieving the data set from a database.
     *
     * @access protected
     *
     * @return object A JDatabaseQuery object to retrieve the data set.
     */
    protected function getListQuery()
    {
		// Get DBO.
		$db = JFactory::getDbo();
			
		$select = array(
			$db->qn('ec') . '.*',
			$db->qn('e.title', 'entry_title'),
			$db->qn('f.name', 'field_name'),
		);
			
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries_credits', 'ec') )
			   ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('ec.entry_id') . ' = ' . $db->qn('e.id') )
			   ->leftJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('ec.object_id') . ' = ' . $db->qn('f.id') . ' AND ' . $db->qn('ec.object_type') . ' = ' . $db->q('form_field') )
			   ->where( $db->qn('ec.user_id') . ' = ' . $db->q( JFactory::getUser()->id ) )
			   ->order( $db->qn('created_time') . ' DESC' );
			   
		return $query;
    }
}