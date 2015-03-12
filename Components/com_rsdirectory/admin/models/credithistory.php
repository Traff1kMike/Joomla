<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credit History model.
 */
class RSDirectoryModelCreditHistory extends JModelAdmin
{
    /**
     * Method to get a table object, load it if necessary.
     * 
     * @access public
     * 
     * @param string $type
     * @param string $prefix
     * @param array $config
     * 
     * @return object
     */
    public function getTable( $type = 'CreditHistory', $prefix = 'RSDirectoryTable', $config = array() )
    {
        return JTable::getInstance($type, $prefix, $config);
    }
        
    /**
     * Method for getting the form from the model.
     *
     * @access public
     * 
     * @param array $data
     * @param bool $loadData
     * 
     * @return mixed
     */
    public function getForm( $data = array(), $loadData = true )
    {
        return false;
    }
        
    /**
     * Method to mark one or more entries as paid/unpaid.
     *
     * @access public
     *
     * @return bool
     */
    public function markAsPaid($pks, $paid)
    {
        if ( empty($pks) )
            return false;
            
        if ( !is_array($pks) )
        {
            $pks = (array)$pks;
        }
            
        // Sanitize array.
        $pks = RSDirectoryHelper::arrayInt($pks, true, true);
            
        if ( empty($pks) )
            return false;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $pks = array_unique($pks);
            
        foreach ($pks as &$pk)
        {
            $pk = $db->q($pk);
        }
            
        $in_pks = implode(',' , $pks);
            
        $paid = $paid ? 1 : 0;
            
        // Update paid status.
        $query = $db->getQuery(true)
               ->update( $db->qn('#__rsdirectory_entries_credits') )
               ->set( $db->qn('paid') . ' = ' . $db->q($paid) )
               ->where( $db->qn('id') . ' IN (' . $in_pks . ')' );
               
        $db->setQuery($query);
        $db->execute();
            
        // Get the entries ids.
        $query = $db->getQuery(true)
               ->select( $db->qn('entry_id') )
               ->from( $db->qn('#__rsdirectory_entries_credits') )
               ->where( $db->qn('id') . ' IN (' . $in_pks . ')' )
               ->group( $db->qn('entry_id') );
               
        $db->setQuery($query);
        $entries_ids = $db->loadColumn();
            
        if ($entries_ids)
        {
            if ($paid)
            {
                // Get the ids of the entries that have unpaid entries.
                $query = $db->getQuery(true)
                       ->select( $db->qn('entry_id') )
                       ->from( $db->qn('#__rsdirectory_entries_credits') )
                       ->where( $db->qn('entry_id') . ' IN (' . implode(',', $entries_ids) . ')' )
                       ->where( $db->qn('paid') . ' = ' . $db->q(0) )
                       ->group( $db->qn('entry_id') );
                       
                $db->setQuery($query);
                $unpaid_entries_ids = $db->loadColumn();
                    
                // Get the ids of the entries that have no unpaid entries.
                $paid_entries_ids = array_diff($entries_ids, $unpaid_entries_ids);
                    
                // Mark entrie as paid.
                if ($paid_entries_ids)
                {
                    $query = $db->getQuery(true)
                           ->update( $db->qn('#__rsdirectory_entries') )
                           ->set( $db->qn('paid') . ' = ' . $db->q(1) )
                           ->where( $db->qn('id') . ' IN (' . implode(',', $paid_entries_ids) . ')' );
                           
                    $db->setQuery($query);
                    $db->execute();
                }
            }
            else
            {
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_entries') )
                       ->set( $db->qn('paid') . ' = ' . $db->q(0) )
                       ->where( $db->qn('id') . ' IN (' . implode(',', $entries_ids) . ')' );
                        
                $db->setQuery($query);
                $db->execute();
                    
                // Unpublish entries.
                JTable::getInstance('Entry', 'RSDirectoryTable')->publish($entries_ids, 0);
            }
        }
            
        return true;
    }
}