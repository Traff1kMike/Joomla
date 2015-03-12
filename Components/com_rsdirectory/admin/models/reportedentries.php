<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Reported Entries model.
 */
class RSDirectoryModelReportedEntries extends JModelList
{
    /**
     * Class constructor.
     * 
     * @param array $config
     */
    public function __construct( $config = array() )
    {
        if ( empty($config['filter_fields']) )
        {
            $config['filter_fields'] = array(
                'r.published',
                'e.title',
                'entry_author',
                'r.reason',
                'report_author',
                'r.name',
                'r.email',
                'r.created_time',
                'r.id',
            );
        }
            
        parent::__construct($config);
    }
        
    /**
     * Method to get a JDatabaseQuery object for retrieving the data set from a database.
     *
     * @access protected
     *
     * @return object A JDatabaseQuery object to retrieve the data set.
     */
    protected function getListQuery()
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        $select = array(
            $db->qn('r') . '.*',
            $db->qn('e.title'),
            $db->qn("u.$author", 'entry_author'),
            $db->qn('u.id', 'entry_author_id'),
            $db->qn("ru.$author", 'report_author'),
            $db->qn('ru.id', 'report_author_id'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_entries_reported', 'r') )
               ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__users', 'ru') . ' ON ' . $db->qn('r.user_id') . ' = ' . $db->qn('ru.id') );
               
        // Filter by status.  
        $status = $this->getState('filter.status');
            
        if ($status !== '')
        {
            $query->where( $db->qn('r.published') . ' = ' . $db->q($status) );
        }
            
        // Filter by entry.  
        $entry_id = $this->getState('filter.entry_id');
            
        if ($entry_id !== '')
        {
            $query->where( $db->qn('r.entry_id') . ' = ' . $db->q($entry_id) );
        }
            
        // Filter by entries author.  
        $entries_author_id = $this->getState('filter.entries_author_id');
            
        if ($entries_author_id !== '')
        {
            $query->where( $db->qn('e.user_id') . ' = ' . $db->q($entries_author_id) );
        }
            
        // Filter by reason.  
        $reason = $this->getState('filter.reason');
            
        if ($reason !== '')
        {
            $query->where( $db->qn('r.reason') . ' = ' . $db->q($reason) );
        }
            
        // Filter by reports author.  
        $reports_author_id = $this->getState('filter.reports_author_id');
            
        if ($reports_author_id !== '')
        {
            $query->where( $db->qn('r.user_id') . ' = ' . $db->q($reports_author_id) );
        }
            
        // Filter by email author.  
        $email = $this->getState('filter.email');
            
        if ($email !== '')
        {
            $query->where( $db->qn('r.email') . ' = ' . $db->q($email) );
        }
            
        // Filter by search.
        $search = $this->getState('filter.search');
            
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $condition = array(
                $db->qn('e.title') . " LIKE $search",
                $db->qn('r.name') . " LIKE $search",
                $db->qn('r.email') . " LIKE $search",
                $db->qn('r.message') . " LIKE $search",
                $db->qn('r.reason') . " LIKE $search",
            );
                
            $query->where( '(' . implode(' OR ', $condition) . ')' );
        }
            
        $ordering = $this->getState('list.ordering', 'id');
        $direction = $this->getState('list.direction', 'asc');
            
        $query->order( $db->qn($ordering) . ' ' . $db->escape($direction) );
            
        return $query;
    }
        
    /**
     * Method to auto-populate the model state. 
     * 
     * @access protected
     * 
     * @param string $ordering
     * @param string $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search');
        $this->setState('filter.search', $search);
            
        $status = $this->getUserStateFromRequest("$this->context.filter.status", 'filter_status', '');
        $this->setState('filter.status', $status);
            
        $entry_id = $this->getUserStateFromRequest("$this->context.filter.entry_id", 'filter_entry_id', '');
        $this->setState('filter.entry_id', $entry_id);
            
        $entries_author_id = $this->getUserStateFromRequest("$this->context.filter.entries_author_id", 'filter_entries_author_id', '');
        $this->setState('filter.entries_author_id', $entries_author_id);
            
        $reason = $this->getUserStateFromRequest("$this->context.filter.reason", 'filter_reason', '');
        $this->setState('filter.reason', $reason);
            
        $reports_author_id = $this->getUserStateFromRequest("$this->context.filter.reports_author_id", 'filter_reports_author_id', '');
        $this->setState('filter.reports_author_id', $reports_author_id);
            
        $email = $this->getUserStateFromRequest("$this->context.filter.email", 'filter_email', '');
        $this->setState('filter.email', $email);
            
        // List state information.
        parent::populateState('id', 'asc');
    }
        
    /**
     * Get filter bar.
     *
     * @access public
     *
     * @return RSFilterBar
     */
    public function getFilterBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/filterbar.php';
            
        $sortFields = array(
            JHtml::_( 'select.option', 'r.published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'e.title', JText::_('COM_RSDIRECTORY_ENTRY') ),
            JHtml::_( 'select.option', 'entry_author', JText::_('COM_RSDIRECTORY_ENTRY_AUTHOR') ),
            JHtml::_( 'select.option', 'r.reason', JText::_('COM_RSDIRECTORY_REASON') ),
            JHtml::_( 'select.option', 'report_author', JText::_('COM_RSDIRECTORY_REPORT_AUTHOR') ),
            JHtml::_( 'select.option', 'r.name', JText::_('COM_RSDIRECTORY_NAME') ),
            JHtml::_( 'select.option', 'r.email', JText::_('COM_RSDIRECTORY_EMAIL') ),
            JHtml::_( 'select.option', 'r.created_time', JText::_('JGLOBAL_CREATED') ),
            JHtml::_( 'select.option', 'r.id', JText::_('JGRID_HEADING_ID') ),
        );
            
        // Initialize the options array.
        $options = array(
            'search' => array(
                'label' => JText::_('JSEARCH_FILTER'),
                'title' => JText::_('JSEARCH_FILTER'),
                'placeholder' => JText::_('JSEARCH_FILTER'),
                'value' => $this->getState('filter.search'),
            ),
            'limitBox' => $this->getPagination()->getLimitBox(),
            'listDirn' => $this->getState('list.direction', 'desc'),
            'listOrder' => $this->getState('list.ordering', 'date'),
            'sortFields' => $sortFields,
            'rightItems' => array(
                array(
                    'input' => '<select name="filter_email" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_EMAIL') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEmails(), 'value', 'text', $this->getState('filter.email'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_reports_author_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_REPORTS_AUTHOR') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getReportsAuthors(), 'value', 'text', $this->getState('filter.reports_author_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_reason" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_REASON') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getReasons(), 'value', 'text', $this->getState('filter.reason'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_entries_author_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_ENTRIES_AUTHOR') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEntriesAuthors(), 'value', 'text', $this->getState('filter.entries_author_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_entry_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_ENTRY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_status" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_PUBLISHED') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.status'), true ) .
                               '</select>',
                ),
            ),
        );
            
        $bar = new RSFilterBar($options);
            
        return $bar;
    }
        
    /**
     * Get sidebar.
     *
     * @access public
     * 
     * @return string
     */
    public function getSideBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
            
        // Status filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_status',
            JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.status'), true )
        );
            
        // Entries filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_ENTRY'),
            'filter_entry_id',
            JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id') )
        );
            
        // Entries authors filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_ENTRIES_AUTHOR'),
            'filter_entries_author_id',
            JHtml::_( 'select.options', $this->getEntriesAuthors(), 'value', 'text', $this->getState('filter.entries_author_id') )
        );
            
        // Reason filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_REASON'),
            'filter_reason',
            JHtml::_( 'select.options', $this->getReasons(), 'value', 'text', $this->getState('filter.reason') )
        );
            
        // Reports authors filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_REPORTS_AUTHOR'),
            'filter_reports_author_id',
            JHtml::_( 'select.options', $this->getReportsAuthors(), 'value', 'text', $this->getState('filter.reports_author_id') )
        );
            
        // Email filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_EMAIL'),
            'filter_email',
            JHtml::_( 'select.options', $this->getEmails(), 'value', 'text', $this->getState('filter.email') )
        );
            
        return RSDirectoryToolbarHelper::render();
    }
        
    /**
     * Get status options.
     *
     * @access public
     *
     * @return array
     */
    public function getStatusOptions()
    {
        return array(
            JHtml::_( 'select.option', 1, JText::_('COM_RSDIRECTORY_READ') ),
            JHtml::_( 'select.option', 0, JText::_('COM_RSDIRECTORY_UNREAD') ),
        );
    }
        
    /**
     * Build a list of entries.
     *
     * @access public
     *
     * @return mixed
     */
    public function getEntries()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('e.id', 'value') . ', ' . $db->qn('e.title', 'text') )
               ->from( $db->qn('#__rsdirectory_entries', 'e') )
               ->innerJoin( $db->qn('#__rsdirectory_entries_reported', 'r') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('r.entry_id') )
               ->group( $db->qn('e.id') )
               ->order( $db->qn('e.title') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of entries authors.
     *
     * @access public
     *
     * @return mixed
     */
    public function getEntriesAuthors()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('u.id', 'value') . ', ' . $db->qn('u.name', 'text') )
               ->from( $db->qn('#__users', 'u') )
               ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->innerJoin( $db->qn('#__rsdirectory_entries_reported', 'r') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('r.entry_id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of reasons.
     *
     * @access public
     *
     * @return mixed
     */
    public function getReasons()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('reason', 'value') . ', ' . $db->qn('reason', 'text') )
               ->from( $db->qn('#__rsdirectory_entries_reported') )
               ->group( $db->qn('reason') )
               ->order( $db->qn('reason') );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of reports authors.
     *
     * @access public
     *
     * @return mixed
     */
    public function getReportsAuthors()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('u.id', 'value') . ', ' . $db->qn('u.name', 'text') )
               ->from( $db->qn('#__users', 'u') )
               ->innerJoin( $db->qn('#__rsdirectory_entries_reported', 'r') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('r.user_id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of emails.
     *
     * @access public
     *
     * @return mixed
     */
    public function getEmails()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('email', 'value') . ', ' . $db->qn('email', 'text') )
               ->from( $db->qn('#__rsdirectory_entries_reported') )
               ->group( $db->qn('email') )
               ->order( $db->qn('email') );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
}