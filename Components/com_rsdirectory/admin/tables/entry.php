<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry table.
 */
class RSDirectoryTableEntry extends JTable
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
        parent::__construct('#__rsdirectory_entries', 'id', $db);
    }
        
    /**
     * Method to set the publishing state for a row or list of rows in the database table.
     *
     * @param mixed $pks An optional array of primary key values to update.
     * @param int $state The publishing state. eg. [0 = unpublished, 1 = published]
     * @param int $userId The user id of the user performing the operation.
     *
     * @return bool True on success; false if $pks is empty.
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_key;
            
        $pks = (array) $pks;
        $userId = (int) $userId;
        $state = (int) $state;
            
        // Sanitize input.
        JArrayHelper::toInteger($pks);
            
        // If there are no primary keys set check to see if the instance key is set.
        if ( empty($pks) )
        {
            // Nothing to set publishing state on, return false.
            if (!$this->$k)
                return false;
                
            $pks = array($this->$k);
        }
            
        $db = JFactory::getDbo();
            
        if ( $entries = RSDirectoryHelper::getEntriesObjectListByIds($pks) )
        {
            foreach ($entries as $i => $entry)
            {
                if ($state == $entry->published)
                {
                    unset($entries[$i]);
                }
                else
                {
                    $entry_data = (object)array(
                        'id' => $entry->id,
                        'new' => 0,
                    );
                        
                    if ($state)
                    {
                        if ($entry->new)
                        {
                            $this->jomSocialActivity($entry->id);
                        }
                            
                        $entry_data->published_time = JFactory::getDate()->toSql();
                            
                        // Mark the entry as paid.
                        $entry_data->paid = 1;
                        $entry->paid = 1;
                            
                        // Mark the entries credits as paid.
                        $query = $db->getQuery(true)
                               ->update( $db->qn('#__rsdirectory_entries_credits') )
                               ->set( $db->qn('paid') . ' = ' . $db->q(1) )
                               ->where( $db->qn('entry_id') . ' = ' . $db->q($entry->id) );
                            
                        $db->setQuery($query);
                        $db->execute();
                            
                        if ($entry->period)
                        {
                            $expiry_time = JFactory::getDate($entry_data->published_time);
                            $expiry_time->modify("+$entry->period days");
                                
                            $entry_data->expiry_time = $expiry_time->toSql();
                        }
                        else
                        {
                            $entry_data->expiry_time = '0000-00-00 00:00:00';
                        }
                    }
                    else
                    {
                        $entry_data->published_time = '0000-00-00 00:00:00';
                        $entry_data->expiry_time = '0000-00-00 00:00:00';
                    }
                        
                    $entry->published_time = $entry_data->published_time;
                    $entry->expiry_time = $entry_data->expiry_time;
                    $entry->published = $state;
                    $entry->new = 0;
                        
                    // Update the published time.
                    $db->updateObject('#__rsdirectory_entries', $entry_data, 'id');
                }
            } 
        }
         
        // Call parent publish function.   
        $return = parent::publish($pks, $state, $userId);
            
        if ($return)
        {
            if ($entries)
            {
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/email.php';
                    
                $entries = RSDirectoryHelper::getEntriesData($entries);
                    
                // Regenerate entries titles.
                RSDirectoryHelper::regenerateEntriesTitles($entries);
                    
                // Send emails.
                $email = $state == 0 ? 'unpublish_entry' : 'publish_entry';
                    
                foreach ($entries as $entry)
                {
                    if ( !empty($entry) && !empty($entry->form->fields) && !empty($entry->form) )
                    {
                        RSDirectoryEmail::getInstance($email, $entry->form->fields, $entry, $entry->form)->send();    
                    }
                }
            }
        }
            
        return $return;
    }
        
    /**
     * Method to delete a row from the database table by primary key value.
     *
     * @param mixed $pk
     *
     * @return boolean
     *
     * @throws UnexpectedValueException
     */
    public function delete($pk = null)
    {
        $k = $this->_tbl_key;
        $pk = is_null($pk) ? $this->$k : $pk;
            
        // If no primary key is given, return false.
        if ( is_null($pk) )
        {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }
            
        if ( $entry = RSDirectoryHelper::getEntry($pk) )
        {
            // Return credits.
            RSDirectoryCredits::returnEntryCredits($pk);
                
            // Send email.
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/email.php';
                
            $entry = RSDirectoryHelper::getEntryData($entry);
                
            if ( !empty($entry) && !empty($entry->form->fields) && !empty($entry->form) )
            {
                RSDirectoryEmail::getInstance('delete_entry', $entry->form->fields, $entry, $entry->form)->send();   
            }
        }
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Delete the entry custom fields row.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_entries_custom') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($pk) );
                
        $db->setQuery($query);
        $db->execute();
            
        // Delete uploaded files.
            
        $files_list = RSDirectoryHelper::getFilesObjectList(0, $pk);
            
        RSDirectoryHelper::deleteFiles($files_list);
            
        $path = JPATH_ROOT . "/components/com_rsdirectory/files/entries/$pk";
            
        if ( file_exists($path) )
        {
            jimport('joomla.filesystem.folder');
            JFolder::delete($path);
        }
            
        // Delete ratings and reviews.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_reviews') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
        $db->execute();
            
        // Delete entry reports.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_entries_reported') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
        $db->execute();
            
        return parent::delete($pk);
    }
        
    /**
     * Method for JomSocial integration.
     *
     * @access public
     *
     * @param mixed $pk
     *
     * @return bool
     *
     * @throws UnexpectedValueException
     */
    public function jomSocialActivity($pk = null)
    {
        $k = $this->_tbl_key;
        $pk = is_null($pk) ? $this->$k : $pk;
            
        // If no primary key is given, return false.
        if ( is_null($pk) )
        {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }
            
        // Exit the function if JomSocial activities are disabled.
        if ( !RSDirectoryConfig::getInstance()->get('jomsocial_activities', 1) )
            return false;
            
        if ( !file_exists(JPATH_BASE . '/components/com_community/libraries/core.php') )
			return false;
			
        // Get DBO.
		$db = JFactory::getDbo();
            
        $select = array(
            $db->qn('user_id'),
            $db->qn('title'),
            $db->qn('description'),
        );
            
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries') )
			   ->where( $db->qn('id') . ' = ' . $db->q($pk) );
                
		$db->setQuery($query);
		$entry = $db->loadObject();
            
		if ($entry)
        {
			require_once JPATH_BASE.'/components/com_community/libraries/core.php';
                
			$lang = JFactory::getLanguage();
                
			$query = $db->getQuery(true)
				   ->select( $db->qn('id') )
				   ->from( $db->qn('#__community_activities') )
				   ->where( $db->qn('actor') . ' = ' . $db->q($entry->user_id) )
				   ->where( $db->qn('params') . ' LIKE ' . $db->q( '%' . $db->escape('rsdirectory.entry', true) .'%' ) )
				   ->where( $db->qn('cid') . ' = ' . $db->q($pk) );
                    
			$db->setQuery($query);
			$activity = $db->loadResult();
                
			if ( empty($activity) )
            {
                $href = RSDirectoryRoute::getEntryURL($pk, $entry->title, '', 0, true);
				$link = '<a href="' . $href . '">' . $entry->title . '</a>';
                $content = substr( strip_tags($entry->description), 0, 255);
                    
				$act = (object)array(
                    'cmd' => 'rsdirectory.create',
                    'actor' => $entry->user_id,
                    'target' => 0,
                    'title' => JText::sprintf('COM_RSDIRECTORY_JOMSOCIAL_ACTIVITY_POST', $link),
                    'content' => $content,
                    'app' => 'profile',
                    'cid' => $pk,
                    'params' => json_encode( array('rsdirectory.entry' => 1) ),
                );
                    
				CFactory::load('libraries', 'activities');
                    
				$act->comment_type = 'profile.status';
				$act->comment_id = CActivities::COMMENT_SELF;
                    
				$act->like_type = 'profile.status';
				$act->like_id = CActivities::LIKE_SELF;
                    
				CActivities::add($act);
			}
		}
    }
}
