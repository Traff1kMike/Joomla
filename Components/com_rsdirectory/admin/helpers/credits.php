<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 

/**
 * RSDirectory! Credits Helper class.
 */
abstract class RSDirectoryCredits
{
	/**
     * Check if an user has enough credits.
     *
     * @access public
     *
     * @static
     * 
     * @param int $credits
     * @param mixed $user_id
     * 
     * @return bool
     */
    public static function checkUserCredits($credits, $user_id = null)
    {
		if (!$credits)
			return true;
			
        if ($user_id === null)
        {
            $user = JFactory::getUser();
            $user_id = $user->id;
        }
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('user_id') )
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($user_id) )
               ->where( '(' . $db->qn('credits') . ' >= ' . $db->q($credits) . ' OR ' . $db->qn('unlimited_credits') . ' = ' . $db->q(1) . ')' );
               
        $db->setQuery($query);
            
        return (bool)$db->loadResult();
    }
        
    /**
     * Get an user's credits amount.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $user_id
     *
     * @return mixed
     */
    public static function getUserCredits($user_id = null)
    {
        if ($user_id === null)
        {
            $user = JFactory::getUser();
            $user_id = $user->id;
        }
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' =' . $db->q($user_id) );
               
        $db->setQuery($query);
            
        $item = $db->loadObject();
            
        if ($item)
        {
            return $item->unlimited_credits ? 'unlimited' : $item->credits;
        }
            
        return 0;
    }
        
    /**
     * Get the total amount an user spent.
     *
     * @access public
     *
     * @static
     *
     * @param mixed $user_id
     *
     * @return int
     */
    public static function getUserSpentCredits($user_id = null)
    {
        if ($user_id === null)
        {
            $user = JFactory::getUser();
            $user_id = $user->id;
        }
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( 'SUM(' . $db->qn('credits') . ')' )
               ->from( $db->qn('#__rsdirectory_entries_credits') )
               ->group( $db->qn('user_id') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($user_id) )
               ->where( $db->qn('free') . ' = ' . $db->q(0) )
               ->where( $db->qn('paid') . ' = ' . $db->q(1) );
                
        $db->setQuery($query);
            
        return $db->loadResult();
    }
        
    /**
     * Calculate the sum of the entry credits objects.
     *
     * @access public
     *
     * @static
     *
     * @param array $entry_credits_objects
     * 
     * @return mixed
     */
    public static function calculateCredits($entry_credits_objects)
    {
        if ( !is_array($entry_credits_objects) || empty($entry_credits_objects) )
            return false;
            
        $sum = 0 ;
            
        foreach ($entry_credits_objects as $entry_credits_object)
        {
            $sum += $entry_credits_object->credits;
        }
            
        return $sum;
    }
        
    /**
     * Add entry credits objects.
     *
     * Be sure to check if the user has enough credits before trying to charge him.
     *
     * @access public
     *
     * @static
     * 
     * @param int $entry_id
     * @param array $objects
     * @param int $user_id
     * @param int $free
     * @param int $paid
     * 
     * @return bool
     */
    public static function addEntryCreditsObjects($entry_id, $objects, $user_id = 0, $free = 0, $paid = 1)
    {
        if ( !$entry_id || !is_numeric($entry_id) || !$objects || !is_array($objects) )
            return false;
            
            
        if (!$user_id)
        {
            $user = JFactory::getUser();
            $user_id = $user->id;
        }
            
        // Exit the function if the user id is not numeric.
        if ( !$user_id || !is_numeric($user_id) )
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('unlimited_credits') )
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($user_id) );
               
        $db->setQuery($query);
        $unlimited = $db->loadResult();
            
        // Initialize the total cost.    
        $total_credits = 0;
            
        $columns = array(
            'entry_id',
            'user_id',
            'object_type',
            'object_id',
            'credits',
            'free',
            'unlimited_credits',
            'paid',
            'created_time',
        );
            
        $query = $db->getQuery(true)
               ->insert( $db->qn('#__rsdirectory_entries_credits') )
               ->columns( $db->qn($columns) );
                
        foreach ($objects as $key => $object)
        {
            $values = array(
                $db->q($entry_id),
                $db->q($user_id),
                $db->q($object->object_type),
                $db->q($object->object_id),
                $db->q($object->credits),
                $db->q($free),
                $db->q($unlimited),
                $db->q($paid),
                $db->q( JFactory::getDate()->toSql() ),
            );
                
            $query->values( implode(',', $values) );
                
            $total_credits += $object->credits;
        }
            
        $db->setQuery($query);
        $db->execute();
            
        if (!$free && !$unlimited && $paid)
        {
            // Charge the user's credits.
            $query = $db->getQuery(true)
                   ->update( $db->qn('#__rsdirectory_users') )
                   ->set( $db->qn('credits') . ' = ' . $db->qn('credits') . ' - ' . $db->q($total_credits) )
                   ->where( $db->qn('user_id') . ' = ' . $db->q($user_id) );
                
            $db->setQuery($query);
            $db->execute();
        }
            
        return true;
    }
        
    /**
     * Return the credits spent on an entry to the user.
     * 
     * Note that the credits will be returned only if the entry was new (never published).
     * 
     * @access public
     *
     * @static
     *
     * @param int $entry_id
     */
    public static function returnEntryCredits($entry_id)
    {
        $entry = RSDirectoryHelper::getEntry($entry_id);
            
        if (!$entry || !$entry->new)
            return;
            
        $items = self::getEntryCreditsObjectList($entry_id);
            
        if (!$items)
            return;
            
        $total = 0;
            
        foreach ($items as $item)
        {
            if (!$item->free && $item->paid && !$item->unlimited_credits)
            {
                $total += $item->credits;
            }
        }
            
        if ($total)
        {
            self::addUserCredits($entry->user_id, $total);
        }
            
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_entries_credits') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) );
               
        $db->setQuery($query);
        $db->execute();
    }
        
    /**
     * Add a number of credits to an user.
     * 
     * @access public
     *
     * @static
     * 
     * @param int $user_id
     * @param int $credits Number of credits.
     * 
     * @return bool
     */
    public static function addUserCredits($user_id, $credits)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($user_id) );
                
        $db->setQuery($query);
            
        $result = $db->loadObject();
            
        $data = (object)array(
            'user_id' => $user_id,
            'credits' => empty($result) ? $credits : $result->credits + $credits,
        );
            
        if (!$credits)
        {
            $data->unlimited_credits = 1;
        }
            
        // Insert credits.
        if ( empty($result) )
            return $db->insertObject('#__rsdirectory_users', $data, 'user_id');
            
        // Update credits.
        return $db->updateObject('#__rsdirectory_users', $data, 'user_id');
    }
	
	/**
     * Get entry credits object list.
     *
     * @access public
     *
     * @static
     * 
     * @param int $entry_id
     * 
     * @return mixed
     */
    public static function getEntryCreditsObjectList($entry_id)
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('*')
               ->from( $db->qn('#__rsdirectory_entries_credits') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
		
	/**
	 * Check if an entry has unpaid credits objects.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $entry_id
	 *
	 * @return bool
	 */
	public static function hasUnpaidEntryCredits($entry_id)
	{
		// Get DBO.
        $db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->select( $db->qn('id') )
			   ->from( $db->qn('#__rsdirectory_entries_credits') )
			   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) )
			   ->where( $db->qn('paid') . ' = ' . $db->q(0) );
			   
		$db->setQuery($query, 0, 1);
			
		return $db->loadResult();
	}
		
	/**
	 * Get the sum of unpaid entry credits.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $entry_id
	 *
	 * @return int
	 */
	public static function getUnpaidEntryCreditsSum($entry_id)
	{
		// Get DBO.
        $db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->select( 'SUM(' . $db->qn('credits') . ')' )
			   ->from( $db->qn('#__rsdirectory_entries_credits') )
			   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) )
			   ->where( $db->qn('free') . ' = ' . $db->q(0) )
			   ->where( $db->qn('paid') . ' = ' . $db->q(0) );
			   
		$db->setQuery($query);
			
		return (int)$db->loadResult();
	}
		
    /**
     * Return the entry credits objects that were not already paid.
     *
     * @access public
     *
     * @static
     *
     * @param array $entry_credits_objects_new
     * @param int $entry_id 
     *
     * @return array
     */
    public static function getEntryCreditsDiff($entry_credits_objects_new, $entry_id)
    {
        if ( !$entry_credits_objects_new || !is_array($entry_credits_objects_new) )
            return array();
            
        // Get the old entry credits objects array.
        $entry_credits_objects_old = self::getEntryCreditsObjectList($entry_id);
            
        if (!$entry_credits_objects_old)
            return $entry_credits_objects_new;
            
        foreach ($entry_credits_objects_new as $i => $entry_credits_object_new)
        {
            foreach ($entry_credits_objects_old as $j => $entry_credits_object_old)
            {
                // Remove the object if it exists in the database.
                if (
                    $entry_credits_object_new->object_type == 'form_field' &&
                    $entry_credits_object_old->object_type == 'form_field' &&
                    $entry_credits_object_new->object_id == $entry_credits_object_old->object_id
                )
                {
                    unset($entry_credits_objects_new[$i]);
                }
            }
        }
            
        return $entry_credits_objects_new;
    }
        
    /**
     * Do a little cleaning to the entry credits objects array.
     *
     * @access public
     *
     * @static
     *
     * @param array $entry_credits_objects
     *
     * @return array
     */
    public static function cleanEntryCredits($entry_credits_objects)
    {
        // Initialize the results array.
        $results = array();
            
        if ( $entry_credits_objects && is_array($entry_credits_objects) )
        {
            foreach ($entry_credits_objects as $entry_credits_object)
            {
                // Remove duplicates.
                if ($entry_credits_object->object_type != 'uploaded_file')
                {
                    foreach ($results as $result)
                    {
                        if ($entry_credits_object->object_type == $result->object_type && $entry_credits_object->object_id == $result->object_id)
                            continue 2;
                    }
                }
                    
                if ($entry_credits_object->credits)
                {
                    $results[] = $entry_credits_object;
                }
            }
        }
            
        return $results;
    }
		
	/**
	 * Get the minimum required credit package for the specified entry.
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $entry_id
	 *
	 * @return mixed
	 */
	public static function getEntryMinimumRequiredCreditPackage($entry_id)
	{
		$user_credits = self::getUserCredits();
		$unpaid_entry_credits = self::getUnpaidEntryCreditsSum($entry_id);
			
		$required_credits = $user_credits === 'unlimited' || $user_credits >= $unpaid_entry_credits ? 0 : $unpaid_entry_credits - $user_credits;
			
		if (!$required_credits)
			return false;
			
		$price = $required_credits * (float)RSDirectoryConfig::getInstance()->get('credit_cost');
			
		return (object)array(
			'id' => 'minimum',
			'title' => JText::_('COM_RSDIRECTORY_MINIMUM_REQUIRED_CREDITS_TITLE'),
			'credits' => $required_credits,
			'price' => $price,
			'description' => JText::_('COM_RSDIRECTORY_MINIMUM_REQUIRED_CREDITS_DESC'),
			'class' => 'minimum-required-credits',
		);
	}
		
	/**
	 * Method to get entry summary
	 *
	 * @access public
	 *
	 * @static
	 *
	 * @param int $entry_id
	 * @param bool $substract_user_credits
	 *
	 * @return mixed
	 */
	public static function getEntrySummary($entry_id, $substract_user_credits = true)
	{
		if (!$entry_id)
			return false;
			
        // Get DBO.
		$db = JFactory::getDbo();
			
		$select = array(
			$db->qn('ec') . '.*',
			$db->qn('f.name', 'field_name'),
		);
			
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries_credits', 'ec') )
			   ->leftJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('ec.object_id') . ' = ' . $db->qn('f.id') . ' AND ' . $db->qn('ec.object_type') . ' = ' . $db->q('form_field') )
			   ->where( $db->qn('ec.entry_id') . ' = ' . $db->q($entry_id) )
			   ->where( $db->qn('ec.free') . ' = ' . $db->q(0) )
			   ->where( $db->qn('ec.paid') . ' = ' . $db->q(0) );
			   
		$db->setQuery($query);
			
		$items = $db->loadObjectList();
			
		if (!$items)
			return false;
			
		$results = array();
		$total = 0;
			
		foreach ($items as $item)
		{
			$results[] = (object)array(
				'text' => JText::_( 'COM_RSDIRECTORY_CREDIT_OBJECT_TYPE_' . strtoupper($item->object_type) ) . ($item->field_name ? " ($item->field_name)" : ''),
				'credits' => $item->credits,
			);
				
			$total += $item->credits;
		}
			
		if ($substract_user_credits)
		{
			$user_credits = RSDirectoryCredits::getUserCredits();
				
			$results[] = (object)array(
				'text' => JText::_('COM_RSDIRECTORY_CURRENT_CREDITS'),
				'credits' => $user_credits ? "-$user_credits" : 0,
			);
				
			$total -= $user_credits;
		}
			
		$results[] = (object)array(
			'is_total' => 1,
			'text' => JText::_('COM_RSDIRECTORY_TOTAL'),
			'credits' => $total,
		);
			
		return $results;
	}
		
	/**
     * Method to check if there's anything that requires credits.
     *
     * @access public
     *
     * @static
     *
     * @return bool
     */   
    public static function requiresCredits()
    {
        $db = JFactory::getDbo();
			
		// Check if there are fields that require credits.
		$query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_fields_properties', 'fp') )
               ->innerJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('fp.field_id') . ' = ' . $db->qn('f.id') )
               ->innerJoin( $db->qn('#__rsdirectory_forms_fields', 'ff') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('ff.field_id') )
               ->where(
					'(' . $db->qn('fp.property_name') . ' = ' . $db->q('credits') . ' AND '. $db->qn('value') . ' > ' . $db->q(0) . ')' . ' OR ' .
					'(' . $db->qn('fp.property_name') . ' = ' . $db->q('credits_per_file') . ' AND '. $db->qn('value') . ' > ' . $db->q(0) . ')'
				)
               ->where( $db->qn('f.published') . ' = ' . $db->q(1) );
               
        $db->setQuery($query);
        $result = (bool)$db->loadResult();
            
        // Check if there are unpaid entries.
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_entries') )
               ->where( $db->qn('paid') . ' = ' . $db->q(0) );
               
        $db->setQuery($query);
            
        return $result || (bool)$db->loadResult();
    }
}