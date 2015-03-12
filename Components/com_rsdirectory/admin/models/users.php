<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Users model.
 */
class RSDirectoryModelUsers extends JModelList
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
                'u.name',
                'u.username',
                'uc.credits',
                'ec.spent_credits',
                't.transactions_count',
                'e.entries_count',
                'rev.reviews_count',
                'rep.reports_count',
                'u.id',
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
            
        $select = array(
            $db->qn('u.id'),
            $db->qn('u.name'),
            $db->qn('u.username'),
            $db->qn('uc.credits'),
            $db->qn('uc.unlimited_credits'),
            $db->qn('ec.spent_credits'),
            $db->qn('e.entries_count'),
            $db->qn('rev.reviews_count'),
            $db->qn('rep.reports_count'),
            $db->qn('t.transactions_count'),
        );
           
        // Create the entries subquery. 
        $subquery1 = $db->getQuery(true)
                       ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('entries_count') )
                       ->from( $db->qn('#__rsdirectory_entries') )
                       ->group( $db->qn('user_id') );
                       
        // Create the entries credits subquery.
        $subquery2 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', SUM(' . $db->qn('credits') . ') AS ' . $db->qn('spent_credits') )
                   ->from( $db->qn('#__rsdirectory_entries_credits') )
                   ->where( $db->qn('free') . ' = ' . $db->q(0) )
                   ->where( $db->qn('paid') . ' = ' . $db->q(1) )
                   ->group( $db->qn('user_id') );
                   
        // Create the reviews subquery.
        $subquery3 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('reviews_count') )
                   ->from( $db->qn('#__rsdirectory_reviews') )
                   ->group( $db->qn('user_id') );
                   
        // Create the reports subquery.
        $subquery4 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('reports_count') )
                   ->from( $db->qn('#__rsdirectory_entries_reported') )
                   ->group( $db->qn('user_id') );
                   
        // Create the transactions subquery.
        $subquery5 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('transactions_count') )
                   ->from( $db->qn('#__rsdirectory_users_transactions') )
                   ->group( $db->qn('user_id') );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__users', 'u') )
               ->leftJoin( $db->qn('#__rsdirectory_users', 'uc') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('uc.user_id') )
               ->leftJoin( '(' . $subquery1 . ') AS ' . $db->qn('e') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('e.user_id') )
               ->leftJoin( '(' . $subquery2 . ') AS ' . $db->qn('ec') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('ec.user_id') )
               ->leftJoin( '(' . $subquery3 . ') AS ' . $db->qn('rev') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('rev.user_id') )
               ->leftJoin( '(' . $subquery4 . ') AS ' . $db->qn('rep') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('rep.user_id') )
               ->leftJoin( '(' . $subquery5 . ') AS ' . $db->qn('t') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('t.user_id') );
               
        // Filter by available credits.
        $has_credits = $this->getState('filter.has_credits');
            
        if ($has_credits !== '')
        {
            if ($has_credits)
            {
                $query->where( '(' . $db->qn('uc.credits') . ' > ' . $db->q(0) . ' OR ' . $db->qn('uc.unlimited_credits') . ' = ' . $db->q(1) . ')' );
            }
            else
            {
                $query->where( '(' . $db->qn('uc.credits') . ' = ' . $db->q(0) . ' OR ' . $db->qn('uc.credits') . ' IS NULL)' )
                      ->where( '(' . $db->qn('uc.unlimited_credits') . ' = ' . $db->q(0) . ' OR ' . $db->qn('uc.unlimited_credits') . ' IS NULL)' );
            }
        }
            
        // Filter by spent credits.
        $spent_credits = $this->getState('filter.spent_credits');
            
        if ($spent_credits !== '')
        {
            $query->where( $db->qn('ec.spent_credits') . ($spent_credits ? ' > ' . $db->q(0) : ' IS NULL') );
        }
            
        // Filter by transactions.
        $has_transactions = $this->getState('filter.has_transactions');
            
        if ($has_transactions !== '')
        {
            $query->where( $db->qn('t.transactions_count') . ($has_transactions ? ' > ' . $db->q(0) : ' IS NULL') );
        }
            
        // Filter by entries.
        $posted_entries = $this->getState('filter.posted_entries');
            
        if ($posted_entries !== '')
        {
            $query->where( $db->qn('e.entries_count') . ($posted_entries ? ' > ' . $db->q(0) : ' IS NULL') );
        }
            
        // Filter by reviews.
        $posted_reviews = $this->getState('filter.posted_reviews');
            
        if ($posted_reviews !== '')
        {
            $query->where( $db->qn('rev.reviews_count') . ($posted_reviews ? ' > ' . $db->q(0) : ' IS NULL') );
        }
            
        // Filter by reports.
        $posted_reports = $this->getState('filter.posted_reports');
            
        if ($posted_reports !== '')
        {
            $query->where( $db->qn('rep.reports_count') . ($posted_reports ? ' > ' . $db->q(0) : ' IS NULL') );
        }
            
        // Filter by search.
        $search = $this->getState('filter.search');
            
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $condition = array(
                $db->qn('u.name') . " LIKE $search",
                $db->qn('u.username') . " LIKE $search",
            );
                
            $query->where( '(' . implode(' OR ', $condition) . ')' );
        }
            
        $ordering = $this->getState('list.ordering', 'id');
        $direction = $this->getState('list.direction', 'asc');
        
        if ($ordering == 'uc.credits')
        {
            $query->order( $db->qn('uc.unlimited_credits') . ' = 1 ' . $db->escape($direction) . ', ' . $db->qn($ordering) . ' ' . $db->escape($direction) );
        }
        else
        {
            $query->order( $db->qn($ordering) . ' ' . $db->escape($direction) );   
        }
            
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
    protected function populateState($ordering = 'u.id', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search');
        $this->setState('filter.search', $search);
            
        $has_credits = $this->getUserStateFromRequest("$this->context.filter.has_credits", 'filter_has_credits', '');
        $this->setState('filter.has_credits', $has_credits);
            
        $spent_credits = $this->getUserStateFromRequest("$this->context.filter.spent_credits", 'filter_spent_credits', '');
        $this->setState('filter.spent_credits', $spent_credits);
            
        $has_transactions = $this->getUserStateFromRequest("$this->context.filter.has_transactions", 'filter_has_transactions', '');
        $this->setState('filter.has_transactions', $has_transactions);
            
        $posted_entries = $this->getUserStateFromRequest("$this->context.filter.posted_entries", 'filter_posted_entries', '');
        $this->setState('filter.posted_entries', $posted_entries);
            
        $posted_reviews = $this->getUserStateFromRequest("$this->context.filter.posted_reviews", 'filter_posted_reviews', '');
        $this->setState('filter.posted_reviews', $posted_reviews);
            
        $posted_reports = $this->getUserStateFromRequest("$this->context.filter.posted_reports", 'filter_posted_reports', '');
        $this->setState('filter.posted_reports', $posted_reports);
            
        // List state information.
        parent::populateState($ordering, $direction);
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
            JHtml::_( 'select.option', 'u.name', JText::_('COM_RSDIRECTORY_NAME') ),
            JHtml::_( 'select.option', 'u.username', JText::_('COM_RSDIRECTORY_USERNAME') ),
            JHtml::_( 'select.option', 'uc.credits', JText::_('COM_RSDIRECTORY_AVAILABLE_CREDITS') ),
            JHtml::_( 'select.option', 'ec.spent_credits', JText::_('COM_RSDIRECTORY_SPENT_CREDITS') ),
            JHtml::_( 'select.option', 't.transactions_count', JText::_('COM_RSDIRECTORY_TRANSACTIONS_COUNT') ),
            JHtml::_( 'select.option', 'e.entries_count', JText::_('COM_RSDIRECTORY_ENTRIES_COUNT') ),
            JHtml::_( 'select.option', 'rev.reviews_count', JText::_('COM_RSDIRECTORY_REVIEWS_COUNT') ),
            JHtml::_( 'select.option', 'rep.reports_count', JText::_('COM_RSDIRECTORY_REPORTS_COUNT') ),
            JHtml::_( 'select.option', 'u.id', JText::_('JGRID_HEADING_ID') ),
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
                    'input' => '<select name="filter_posted_reports" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_POSTED_REPORTS_OPTION') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.posted_reports'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_posted_reviews" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_POSTED_REVIEWS_OPTION') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.posted_reviews'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_posted_entries" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_POSTED_ENTRIES_OPTION') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.posted_entries'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_has_transactions" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_HAS_TRANSACTIONS_OPTION') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.has_transactions'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_spent_credits" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SPENT_CREDITS_OPTION') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.spent_credits'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_has_credits" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_HAS_CREDITS_OPTION') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.has_credits'), true ) .
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
            
        // Get bool options.
        $options = $this->getBoolOptions();
            
        // Available credits filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_HAS_CREDITS_OPTION'),
            'filter_has_credits',
            JHtml::_( 'select.options', $options, 'value', 'text', $this->getState('filter.has_credits'), true )
        );
            
        // Spent credits filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SPENT_CREDITS_OPTION'),
            'filter_spent_credits',
            JHtml::_( 'select.options', $options, 'value', 'text', $this->getState('filter.spent_credits'), true )
        );
        
        // Has transactions filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_HAS_TRANSACTIONS_OPTION'),
            'filter_has_transactions',
            JHtml::_( 'select.options', $options, 'value', 'text', $this->getState('filter.has_transactions'), true )
        );
            
        // Entries filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_POSTED_ENTRIES_OPTION'),
            'filter_posted_entries',
            JHtml::_( 'select.options', $options, 'value', 'text', $this->getState('filter.posted_entries'), true )
        );
            
        // Reviews filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_POSTED_REVIEWS_OPTION'),
            'filter_posted_reviews',
            JHtml::_( 'select.options', $options, 'value', 'text', $this->getState('filter.posted_reviews'), true )
        );
            
        // Reports filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_POSTED_REPORTS_OPTION'),
            'filter_posted_reports',
            JHtml::_( 'select.options', $options, 'value', 'text', $this->getState('filter.posted_reports'), true )
        );
            
        return RSDirectoryToolbarHelper::render();
    }
        
    /**
     * Get boolean options.
     *
     * @access public
     *
     * @return array
     */
    public function getBoolOptions()
    {
        return array(
            JHtml::_( 'select.option', 1, JText::_('JYES') ),
            JHtml::_( 'select.option', 0, JText::_('JNO') ),
        );
    }
}