<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credit History table.
 */
class RSDirectoryTableCreditHistory extends JTable
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
        parent::__construct('#__rsdirectory_entries_credits', 'id', $db);
    }
}