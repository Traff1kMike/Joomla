<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

define('MOD_RSDIRECTORY_RELATED_ENTRIES_VERSION', '1.0.2');

/**
 * RSDirectory! Related Entries Module Helper.
 */
abstract class RSDirectoryRelatedEntriesHelper
{
    /**
     * Get the related entries.
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
		// Get the JInput object.
		$jinput = JFactory::getApplication()->input;
			
		// Get entry id.    
		$entry_id = $jinput->getInt('id');
			
		// Get entry.
		$entry = RSDirectoryHelper::getEntry($entry_id);
			
		// Get DBO.
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
			   ->select( $db->qn('e') . '.*' )
			   ->from( $db->qn('#__rsdirectory_entries', 'e') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
			   ->where( $db->qn('e.id') . ' != ' . $entry_id )
			   ->where( $db->qn('e.category_id') . ' = ' . $db->q($entry->category_id) )
			   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' > ' . $db->q( JFactory::getDate()->toSql() ) . ')' );
			   
		if ( $params->get('enable_price_variation') )
		{
			$percentage = $entry->price * ( (int)$params->get('price_variation') / 100 );
				
			$query->where( $db->qn('e.price') . ' >= ' . $db->q($entry->price - $percentage) )
			      ->where( $db->qn('e.price') . ' <= ' . $db->q($entry->price + $percentage) );
		}
			
		$query->order( $db->qn('e.promoted') . ' DESC' );
			
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