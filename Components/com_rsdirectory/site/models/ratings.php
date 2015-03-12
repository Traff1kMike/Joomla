<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Ratings model.
 */
class RSDirectoryModelRatings extends JModelList
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
		$db = JFactory::getDBO();
		$jinput = JFactory::getApplication()->input;
            
        $select = array(
            $db->qn('r') . '.*',
            $db->qn('u.name', 'author_name'),
            $db->qn('u.username'),
            $db->qn('u.email', 'author_email'),
			$db->qn('e.user_id', 'entry_author_id'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_reviews', 'r') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('r.user_id') . ' = ' . $db->qn('u.id') )
			   ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
			   ->where( $db->qn('r.published') . ' = ' . $db->q(1) )
               ->where( $db->qn('r.entry_id') . ' = ' . $db->q( $jinput->getInt('id') ) );
			   
		if ( $excluded = $jinput->get( 'excluded', array(), 'array' ) )
		{
			if ( !is_array($excluded) )
			{
				$excluded = (array)$excluded;
			}
				
			$excluded = RSDirectoryHelper::arrayInt($excluded, true, true);
				
			if ($excluded)
			{
				$query->where( $db->qn('r.id') . ' NOT IN (' . implode(',', $excluded) . ')' );
			}
		}
			
		$ordering = $this->getState('list.ordering', 'r.created_time');
        $direction = $this->getState('list.direction', 'desc');
            
        $query->order( $db->qn($ordering) . ' ' . $db->escape($direction) );
			
		return $query;
	}
		
	/**
     * Check if the user has a posted review in a reviews list.
     *
     * @access public
     *
     * @param int $entry_id
     * @param mixed $user_id
     *
     * @return bool
     */
	public function hasPostedReview($entry_id, $user_id = null)
	{
		if (!$entry_id)
			return false;
			
        if ( is_null($user_id) )
        {
            $user_id = JFactory::getUser()->id;
        }
            
        if (!$user_id)
        {
            $ip = RSDirectoryHelper::getIp(true);
        }
			
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->select('COUNT(*)')
			   ->from( $db->qn('#__rsdirectory_reviews') )
			   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) );
			   
        if ($user_id)
		{
			$query->where( $db->qn('user_id') . ' = ' . $db->q($user_id) );
		}
		else
		{
			$query->where( $db->qn('user_id') . ' = ' . $db->q(0) )
			      ->where( $db->qn('ip') . ' = ' . $db->q($ip) );
		}
			
		$db->setQuery($query);
            
        return $db->loadResult();
	}
}