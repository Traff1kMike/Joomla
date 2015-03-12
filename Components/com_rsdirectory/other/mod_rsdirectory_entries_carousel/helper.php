<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

define('MOD_RSDIRECTORY_ENTRIES_CAROUSEL_VERSION', '1.0.0');

/**
 * RSDirectory! Carousel Module Helper.
 */
abstract class RSDirectoryCarouselHelper
{
    /**
     * Get entries.
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
			
		$select = array(
			$db->qn('e') . '.*',
            $db->qn('c.title', 'category_title'),
            $db->qn('c.path', 'category_path'),
            $db->qn('u.name', 'author'),
		);
			
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries', 'e') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
			   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' > ' . $db->q( JFactory::getDate()->toSql() ) . ')' );
			   
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
			
		// Set the ordering criteria.
		$order_by = $params->get('order_by');
		$order = $params->get('order');
			
		if ($order_by == 'rating')
		{
			$query->order( $db->qn('e.avg_rating') . ' ' . $db->escape($order) . ', ' . $db->qn('e.ratings_count') . ' ASC' );
		}
		else
		{
			$query->order(  $db->qn($order_by) . ' ' . $db->escape($order) );
		}
			
		$db->setQuery( $query, 0, $params->get('max_entries') );
			
		$entries = $db->loadObjectList();
			
		if ( $params->get('display_thumbs') )
		{
			return RSDirectoryHelper::getEntriesData($entries);
		}
			
		return $entries;
    }
		
	/**
	 * Method to get a list of entries per slide.s
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param array $entries
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public static function getEntriesPerSlide($entries, $limit)
	{
		$slides = array();
			
		$count = 0;
		$index = 0;
			
		foreach ($entries as $entry)
		{
			$count++;
				
			if ($count > $limit)
			{
				$count = 1;
				$index++;
			}
				
			if ( !isset($slides[$index]) )
			{
				$slides[$index] = array();	
			}
				
			$slides[$index][] = $entry;
		}
			
		return $slides;
	}
}