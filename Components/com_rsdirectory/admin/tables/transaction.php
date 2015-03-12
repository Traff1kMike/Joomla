<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transaction table.
 */
class RSDirectoryTableTransaction extends JTable
{
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param object Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__rsdirectory_users_transactions', 'id', $db);
    }
        
    /**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 *
	 * @link    http://docs.joomla.org/JTable/publish
	 * @since   11.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;
            
		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state = (int)$state;

		// If there are no primary keys set check to see if the instance key is set.
		if ( empty($pks) )
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				return false;
			}
		}
            
        $db = $this->_db;
			
		$where = $k . ' = ' . implode(' OR ' . $k . ' = ', $pks);
			
		$query = $db->getQuery(true)
		       ->select('*')
			   ->from($this->_tbl)
			   ->where($where);
			   
		$db->setQuery($query);
			
		$transactions = $db->loadObjectList();
			
		// Get an instance of the Entry table.
		$entry_table = JTable::getInstance('Entry', 'RSDirectoryTable');
			
		foreach ($transactions as $transaction)
		{
			$transaction_data = (object)array(
				'id' => $transaction->id,
				'status' => $state ? 'finalized' : 'pending',
				'date_finalized' => $state ? JFactory::getDate()->toSql() : '0000-00-00 00:00:00',
			);
			
            if ($state && !$transaction->finalized)
			{
				RSDirectoryCredits::addUserCredits($transaction->user_id, $transaction->credits);
				$transaction_data->finalized = 1;
					
				if ($transaction->entry_id)
				{
					$entry = RSDirectoryHelper::getEntry($transaction->entry_id);
						
					// Proceed only if the entry is unpaid.
					if (!$entry->paid)
					{
						$unpaid_credits = RSDirectoryCredits::getUnpaidEntryCreditsSum($entry->id);
							
						// Proceed only if the user has enough credits.	
						if ( RSDirectoryCredits::checkUserCredits($unpaid_credits, $entry->user_id) )
						{
							// Mark the entry as paid.
							$query = $db->getQuery(true)
							       ->update( $db->qn('#__rsdirectory_entries') )
								   ->set( $db->qn('paid') . ' = ' . $db->q(1) )
								   ->where( $db->qn('id') . ' = ' . $db->q($entry->id) );
								   
							$db->setQuery($query);
							$db->execute($db);
								
							// Mark entry credit objects as paid.
							$query = $db->getQuery(true)
							       ->update( $db->qn('#__rsdirectory_entries_credits') )
								   ->set( $db->qn('paid') . ' = ' . $db->q(1) )
								   ->where( $db->qn('entry_id') . ' = ' . $db->q($entry->id) );
								   
							$db->setQuery($query);
							$db->execute($db);
								
							// Charge user credits.
							if ( RSDirectoryCredits::getUserCredits($entry->user_id) != 'unlimited' )
							{
								$query = $db->getQuery(true)
								       ->update( $db->qn('#__rsdirectory_users') )
									   ->set( $db->qn('credits') . ' = ' . $db->qn('credits') . ' - ' . $db->q($unpaid_credits) )
									   ->where( $db->qn('user_id') . ' = ' . $db->q($entry->user_id) );
									   
								$db->setQuery($query);
								$db->execute();
							}
								
							// Publish the entry if the user has auto-publish permission.
							if ( RSDirectoryHelper::checkUserPermission('auto_publish_entries', $entry->user_id) )
							{
								$entry_table->publish($entry->id);
							}
						}
					}
				}
			}
				
			$db->updateObject($this->_tbl, $transaction_data, $k);
		}
            
		$this->setError('');
		return true;
	}
}