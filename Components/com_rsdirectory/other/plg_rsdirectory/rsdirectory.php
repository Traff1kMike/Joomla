<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');

// Load the RSDirectory! Plugin helper class file if it exists or stop the execution of the script if it doesn't exist.
if ( !file_exists(JPATH_ADMINISTRATOR  . '/components/com_rsdirectory/helpers/plugin.php') )
    return;

require_once JPATH_ADMINISTRATOR  . '/components/com_rsdirectory/helpers/plugin.php';

/**
 * RSDirectory! Plugin.
 */
class plgSystemRSDirectory extends RSDirectoryPlugin
{
    /**
     * Class constructor.
     *
     * @access public
     * 
     * @param object &$subject
     * @param array $config
     */
    public function __construct(&$subject, $config)
    {
		parent::__construct($subject, $config);
			
		if ( file_exists(JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php') )
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/rsdirectory.php';
		}
    }
        
    /**
     * On after initialise.
     *
     * @access public
     */
    public function onAfterInitialise()
    {
		// Exit the function if the RSDirectory! helper class is not loaded.
		if ( !class_exists('RSDirectoryHelper') )
			return;
			
		$this->renewEntries();
		$this->sendOnEntriesExpiration();
		$this->sendEntriesExpirationNotifications();
    }
		
	/**
	 * Method to renew entries.
	 *
	 * @access private
	 */
	private function renewEntries()
	{
		$last_entries_renewals_time = 0;RSDirectoryConfig::getInstance()->get('last_entries_renewals_time');
			
		$entries_renewals_interval = RSDirectoryConfig::getInstance()->get('entries_renewals_interval');
			
		// Exit the function if the renewal interval did not pass yet.
		if ( $last_entries_renewals_time + $entries_renewals_interval > JFactory::getDate()->toUnix () )
			return;
			
		// Get DBO.
		$db = JFactory::getDBO();
			
		   
		// Update the last entries renewals time.
		$option = (object)array(
			'name' => 'last_entries_renewals_time',
			'value' => JFactory::getDate()->toUnix (),
		);
			
		$db->updateObject('#__rsdirectory_config', $option, 'name');
			
		$query = $db->getQuery(true)
			   ->select('*')
			   ->from( $db->qn('#__rsdirectory_entries') )
			   ->where( $db->qn('published') . ' = ' . $db->q(1) )
			   ->where( $db->qn('renew') . ' = ' . $db->q(1) )
			   ->where( $db->qn('expiry_time') . ' != ' . $db->q('0000-00-00 00:00:00') )
			   ->where( $db->qn('expiry_time') . ' < ' . $db->q( JFactory::getDate()->toSql() ) );
				
		$db->setQuery($query);    
		$entries = $db->loadObjectList();
			
			
		// Exit the function if there are no entries to be renewed.
		if (!$entries)
			return;
			
			
		// Load language.
		$lang = JFactory::getLanguage();
		$lang->load('com_rsdirectory', JPATH_SITE);
			
		// Process each entry.
		foreach ($entries as $entry)
		{
			// Skip this entry if the user does not have enough credits.
			if ( !RSDirectoryCredits::checkUserCredits($entry->period_credits, $entry->user_id) )
				continue;
				
			$entry_credits_objects = array(
				(object)array(
					'object_type' => 'renewal',
					'object_id' => 0,
					'credits' => $entry->period_credits,
				),
			);
				
			// Skip this entry if the user was not successfully charged.
			if ( !RSDirectoryCredits::addEntryCreditsObjects($entry->id, $entry_credits_objects, $entry->user_id) )
				continue;
				
			$expiry_time = JFactory::getDate();
			$expiry_time->modify("+$entry->period days");
				
			// Update expiry time.
			$entry_data = (object)array(
				'id' => $entry->id,
				'published_time' => JFactory::getDate()->toSql(),
				'expiry_time' => $expiry_time->toSql(),
			);
				
			$db->updateObject('#__rsdirectory_entries', $entry_data, 'id');
		}
			
		// Regenerate entries titles.
		RSDirectoryHelper::regenerateEntriesTitles($entries);
	}
		
	/**
	 * Method to send notifications after the entries expired.
	 *
	 * @access private
	 */
	private function sendOnEntriesExpiration()
	{
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->select('*')
			   ->from( $db->qn('#__rsdirectory_email_messages') )
			   ->where( $db->qn('type') . ' = ' . $db->q('entry_expiration') )
			   ->where( $db->qn('published') . ' = ' . $db->q(1) );
				
		$db->setQuery($query);
			
		$emails = $db->loadObjectList();
			
		if (!$emails)
			return;
			
		require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/email.php';
			
		foreach ($emails as $email)
		{
			$query = $db->getQuery(true)
		           ->select( $db->qn('e') . '.*' )
			       ->from( $db->qn('#__rsdirectory_entries', 'e') )
				   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') .  ' = ' . $db->qn('u.id') )
				   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
				   ->where( $db->qn('e.expiry_time') . ' != ' . $db->q('0000-00-00 00:00:00') )
			       ->where( $db->qn('e.expiry_time') . ' < ' . $db->q( JFactory::getDate()->toSql() ) )
				   ->where( $db->qn('e.expiration_notice_time') . ' < ' . $db->qn('e.expiry_time') )
				   ->where( $db->qn('u.block') . ' = ' . $db->q(0) );
				   
			if ($email->category_id)
			{
				$query->where( $db->qn('e.category_id') . ' = ' . $db->q($email->category_id) );
			}
				
			$db->setQuery($query);
				
			$entries = $db->loadObjectList();
				
			if (!$entries)
				continue;
				
			$entries = RSDirectoryHelper::getEntriesData($entries);
				
			foreach ($entries as $entry)
			{
				RSDirectoryEmail::getInstance('entry_expiration', $entry->form->fields, $entry, $entry->form)->send();
			}
				
			$ids = RSDirectoryHelper::getColumn($entries, 'id');
				
			// Update expiration_notice_time.
			$query = $db->getQuery(true)
			       ->update( $db->qn('#__rsdirectory_entries') )
				   ->set( $db->qn('expiration_notice_time') . ' = ' . $db->q( JFactory::getDate()->toSql() ) )
				   ->where( $db->qn('id') . ' IN (' . RSDirectoryHelper::quoteImplode($ids) . ')' );
				   
			$db->setQuery($query);
			$db->execute();
		}
	}
		
	/**
	 * Method to send notifications x hours before the entries expire.
	 *
	 * @access private
	 */
	private function sendEntriesExpirationNotifications()
	{
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->select('*')
			   ->from( $db->qn('#__rsdirectory_email_messages') )
			   ->where( $db->qn('type') . ' = ' . $db->q('entry_expiration_notice') )
			   ->where( $db->qn('published') . ' = ' . $db->q(1) )
			   ->order( $db->qn('entry_expiration_period') . ' ASC' );
				
		$db->setQuery($query);
			
		$emails = $db->loadObjectList();
			
		if (!$emails)
			return;
			
		require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/email.php';
			
		foreach ($emails as $email)
		{
			$expiry_time = JFactory::getDate();
            $expiry_time->modify("+$email->entry_expiration_period hours");
				
			$query = $db->getQuery(true)
		           ->select( $db->qn('e') . '.*' )
			       ->from( $db->qn('#__rsdirectory_entries', 'e') )
				   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') .  ' = ' . $db->qn('u.id') )
				   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
				   ->where( $db->qn('e.expiry_time') . ' != ' . $db->q('0000-00-00 00:00:00') )
			       ->where( $db->qn('e.expiry_time') . ' <= ' . $db->q( $expiry_time->toSql() ) )
				   ->where( $db->qn('e.expiration_notice_time') . " < DATE_SUB( " . $db->qn('e.expiry_time') . ", INTERVAL $email->entry_expiration_period HOUR )" )
				   ->where( $db->qn('u.block') . ' = ' . $db->q(0) );
				   
			if ($email->category_id)
			{
				$query->where( $db->qn('e.category_id') . ' = ' . $db->q($email->category_id) );
			}
				
			$db->setQuery($query);
				
			$entries = $db->loadObjectList();
				
			if (!$entries)
				continue;
				
			$entries = RSDirectoryHelper::getEntriesData($entries);
				
			foreach ($entries as $entry)
			{
				RSDirectoryEmail::getInstance('entry_expiration_notice', $entry->form->fields, $entry, $entry->form)->send();
			}
				
			$ids = RSDirectoryHelper::getColumn($entries, 'id');
				
			// Update expiration_notice_time.
			$query = $db->getQuery(true)
			       ->update( $db->qn('#__rsdirectory_entries') )
				   ->set( $db->qn('expiration_notice_time') . ' = ' . $db->q( JFactory::getDate()->toSql() ) )
				   ->where( $db->qn('id') . ' IN (' . RSDirectoryHelper::quoteImplode($ids) . ')' );
				   
			$db->setQuery($query);
			$db->execute();
		}
	}
}