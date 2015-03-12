<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

define('MOD_RSDIRECTORY_RECENTLY_VISITED_ENTRIES_VERSION', '1.0.2');

/**
 * RSDirectory! Recently Visited Entries Module Helper.
 */
abstract class RSDirectoryRecentlyVisitedEntriesHelper
{
    /**
     * Get the top rated entries.
     *
     * @access public
     *
     * @static
     *
     * @param JRegistry $params
     *
     * @return mixed
     */
    public static function getEntries($params)
    {
		$jinput = JFactory::getApplication()->input;
			
		$entries_ids = $jinput->cookie->getString('rsdir_recently_visited');
		$entries_ids = explode(',', $entries_ids);
		$entries_ids = RSDirectoryHelper::arrayInt($entries_ids, true, true);
			
		// Excluded the currently viewed entry.
		if ( $jinput->get('view') == 'entry' && $jinput->get('layout', 'default') == 'default' )
		{
			if ( $id = $jinput->get('id') )
			{
				if ( ( $key = array_search($id, $entries_ids) ) !== false )
				{
					unset($entries_ids[$key]);
				}
			}
		}
			
		if (!$entries_ids)
			return;
			
		$entries_ids = implode(',', $entries_ids);
			
		// Get DBO.
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
			   ->select( $db->qn('e') . '.*' )
			   ->from( $db->qn('#__rsdirectory_entries', 'e') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
			   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' > ' . $db->q( JFactory::getDate()->toSql() ) . ')' )
			   ->where( $db->qn('e.id') . ' IN (' . $entries_ids . ')' )
			   ->order( 'FIELD(' . $db->qn('e.id') . ', ' . $entries_ids . ') DESC' ); // A "trick" to order the entries by how they appear in the array.
				
		$max_entries = $params->get('max_entries', 3);
			
		// Ensure that max_entries is a factor of 12.
		if (12 % $max_entries != 0)
		{
			$max_entries = 3;	
		}
			
		$db->setQuery($query, 0, $max_entries);
			
		$entries = $db->loadObjectList();
			
		if ( $params->get('display_thumbs') )
		{
			return RSDirectoryHelper::getEntriesData($entries);
		}
			
		return $entries; 
    }
}