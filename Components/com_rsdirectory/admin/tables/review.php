<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Review table.
 */
class RSDirectoryTableReview extends JTable
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
        parent::__construct('#__rsdirectory_reviews', 'id', $db);
    }
        
    /**
     * Method to store a row in the database from the JTable instance properties.
     *
     * @param bool $updateNulls 
     *
     * @return bool True on success.
     */
    public function store($updateNulls = false)
    {
        if ( empty($this->id) )
        {
            $this->ip = RSDirectoryHelper::getIp(true);
            $this->created_time = JFactory::getDate()->toSql();
        }
        else
        {
            // Get the old review.
            $old_review = JTable::getInstance('Review', 'RSDirectoryTable');
            $old_review->load($this->id);    
        }
            
        $return = parent::store($updateNulls);
            
        $is_admin = JFactory::getApplication()->isAdmin();
            
        // Update the entry rating if ratings are enabled.
        if ( $is_admin || RSDirectoryConfig::getInstance()->get('enable_ratings') )
        {
            $this->updateEntryRating();
        }
            
        // Update the entry rating if the entry was changed for the current review.
        if ( $is_admin && !empty($old_review) && $old_review->entry_id != $this->entry_id )
        {
            $this->updateEntryRating($old_review->entry_id);
        }
            
        return $return;
    }
        
    /**
     * Method to set the publishing state for a row or list of rows in the database table.  
     *
     * @param mixed $pks 
     * @param integer $state 
     * @param integer $userId
     *
     * @return boolean
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        if ( $return = parent::publish($pks, $state, $userId) )
        {
            $this->updateEntryRating();
        }
            
        return $return;
    }
        
    /**
     * Method to delete a row from the database table by primary key value.
     *
     * @access public
     *
     * @param mixed
     *
     * @return boolean
     */
    public function delete($pk = null)
    {
        if ( $return = parent::delete($pk) )
        {
            $this->updateEntryRating();
        }
            
        return $return;
    }
        
    /**
     * Update entry rating.
     *
     * @access private
     *
     * @param int $entry_id
     *
     * @return bool
     */
    private function updateEntryRating($entry_id = null)
    {
        $entry_id = is_null($entry_id) ? $this->entry_id : $entry_id;
            
        if ( empty($entry_id) )
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Get the number of ratings and their sum.
        $query = $db->getQuery(true)
               ->select( 'COUNT(*) AS ' . $db->qn('count') . ', SUM(' . $db->qn('score') . ') AS ' . $db->qn('sum') )
               ->from( $db->qn('#__rsdirectory_reviews') )
               ->where( $db->qn('entry_id') . ' = ' . $db->q($entry_id) )
               ->where( $db->qn('score') . ' > ' . $db->q(0) )
               ->where( $db->qn('published') . ' = ' . $db->q(1) );
                    
        $db->setQuery($query);
            
        $data = $db->loadObject();
            
        // Update entry.
        $entry = (object)array(
            'id' => $entry_id,
            'avg_rating' => $data->sum && $data->count ? $data->sum / $data->count : 0,
            'ratings_count' => $data->count,
        );
            
        $db->updateObject('#__rsdirectory_entries', $entry, 'id');
            
        return true;
    }
}
