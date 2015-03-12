<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

define('MOD_RSDIRECTORY_POPULAR_ENTRIES_VERSION', '1.0.2');

/**
 * RSDirectory! Popular Entries Module Helper.
 */
abstract class RSDirectoryPopularEntriesHelper
{
    /**
     * Get the most popular entries.
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
			   ->order( $db->qn('e.hits') . ' DESC, ' . $db->qn('e.promoted') . ' DESC' );
			   
		$featured_categories = RSDirectoryHelper::arrayInt( $params->get('featured_categories') );
			
		if ( empty($featured_categories) || in_array(0, $featured_categories) )
		{
			$categories_query = $db->getQuery(true)
							  ->select( $db->qn('id') )
							  ->from( $db->qn('#__categories') )
							  ->where( $db->qn('published') . ' = ' . $db->q(1) );
								
			$db->setQuery($categories_query);
				
			$categories_ids = $db->loadColumn();
		}
		else
		{
			$categories_query = $db->getQuery(true)
							  ->select( $db->qn('lft') . ', ' . $db->qn('rgt') )
							  ->from( $db->qn('#__categories') )
							  ->where( $db->qn('id') . ' IN (' . implode(',', $featured_categories) . ')' )
							  ->where( $db->qn('published') . ' = ' . $db->q(1) );
								
			$db->setQuery($categories_query);
			$categories = $db->loadObjectList();
				
			if ($categories)
			{
				$categories_query = $db->getQuery(true)
								  ->select( $db->qn('id') )
								  ->from( $db->qn('#__categories') );
									
				foreach ($categories as $category)
				{
					$categories_query->where( '(' . $db->qn('lft') . ' >= ' . $db->q($category->lft) . ' AND ' . $db->qn('rgt') . ' <= ' . $db->q($category->rgt) . ')' )
									 ->where( $db->qn('published') . ' = ' . $db->q(1) );
				}
					
				$db->setQuery($categories_query);
					
				$categories_ids = $db->loadColumn();
			}
		}
			
		if ( empty($categories_ids) )
		{
			$categories_ids = array(0);    
		}
			
		// Add the categories condition.
		$query->where( $db->qn('e.category_id') . ' IN (' . implode( ',', array_unique($categories_ids) ) . ')' );
		
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