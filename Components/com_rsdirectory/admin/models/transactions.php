<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transactions model.
 */
class RSDirectoryModelTransactions extends JModelList
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
                't.credit_title',
                'user',
                'entry_title',
                'entry_paid',
                't.gateway',
                't.currency',
                't.price',
                't.tax',
                't.total',
                't.credits',
                't.status',
                't.date_created',
                't.date_finalized',
                't.id',
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
            $db->qn('t') . '.*',
            $db->qn("u.$author", 'user'),
            $db->qn('e.id', 'entry_id'),
            $db->qn('e.title', 'entry_title'),
            $db->qn('e.paid', 'entry_paid'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_users_transactions', 't') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('t.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('t.entry_id') . ' = ' . $db->qn('e.id') );
               
        // Filter by credit package.  
        if ( $credit_package = $this->getState('filter.credit_package') )
        {
            $query->where( $db->qn('t.credit_title') . ' = ' . $db->q($credit_package) );
        }
            
        // Filter by user.  
        if ( $user_id = $this->getState('filter.user_id') )
        {
            $query->where( $db->qn('t.user_id') . ' = ' . $db->q($user_id) );
        }
            
        // Filter by entry.  
        if ( $entry_id = $this->getState('filter.entry_id') )
        {
            $query->where( $db->qn('t.entry_id') . ' = ' . $db->q($entry_id) );
        }
            
        // Filter by gateway.
        if ( $gateway = $this->getState('filter.gateway') )
        {
            $query->where( $db->qn('t.gateway') . ' = ' . $db->q($gateway) );
        }
            
        // Filter by currency.      
        if ( $currency = $this->getState('filter.currency') )
        {
            $query->where( $db->qn('t.currency') . ' = ' . $db->q($currency) );
        }
            
        // Filter by price.  
        if ( $price = $this->getState('filter.price') )
        {
            $query->where( $db->qn('t.price') . ' = ' . $db->q($price) );
        }
            
        // Filter by credits.      
        if ( $credits = $this->getState('filter.credits') )
        {
            $query->where( $db->qn('t.credits') . ' = ' . $db->q($credits) );
        }
            
        // Filter by status.      
        if ( $status = $this->getState('filter.status') )
        {
            $query->where( $db->qn('t.status') . ' = ' . $db->q($status) );
        }
            
        // Filter by search.
        $search = $this->getState('filter.search');
            
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $conditions = array(
                $db->qn('t.credit_title') . " LIKE $search",
                $db->qn('t.gateway_order_number') . " LIKE $search",
                $db->qn('t.gateway_order_type') . " LIKE $search",
                $db->qn('t.gateway_params') . " LIKE $search",
                $db->qn('t.gateway_log') . " LIKE $search",
                $db->qn('e.title') . " LIKE $search",
            );
                
            $query->where( '(' . implode(' OR ', $conditions) . ')' );
        }
            
        $ordering = $this->getState('list.ordering', 'id');
        $direction = $this->getState('list.direction', 'asc');
            
        if ($ordering == 't.credits')
        {
            $query->order( $db->qn($ordering) . ' = 0 ' . $db->escape($direction) . ', ' . $db->qn($ordering) . ' ' . $db->escape($direction) );
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
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search');
        $this->setState('filter.search', $search);
            
        $credit_package = $this->getUserStateFromRequest("$this->context.filter.credit_package", 'filter_credit_package');
        $this->setState('filter.credit_package', $credit_package);
            
        $user_id = $this->getUserStateFromRequest("$this->context.filter.user_id", 'filter_user_id');
        $this->setState('filter.user_id', $user_id);
            
        $entry_id = $this->getUserStateFromRequest("$this->context.filter.entry_id", 'filter_entry_id');
        $this->setState('filter.entry_id', $entry_id);
            
        $gateway = $this->getUserStateFromRequest("$this->context.filter.gateway", 'filter_gateway');
        $this->setState('filter.gateway', $gateway);
            
        $currency = $this->getUserStateFromRequest("$this->context.filter.currency", 'filter_currency');
        $this->setState('filter.currency', $currency);
            
        $price = $this->getUserStateFromRequest("$this->context.filter.price", 'filter_price');
        $this->setState('filter.price', $price);
            
        $credits = $this->getUserStateFromRequest("$this->context.filter.credits", 'filter_credits');
        $this->setState('filter.credits', $credits);
            
        $status = $this->getUserStateFromRequest("$this->context.filter.status", 'filter_status');
        $this->setState('filter.status', $status);
            
        // List state information.
        parent::populateState('t.date_created', 'desc');
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
            JHtml::_( 'select.option', 't.credit_title', JText::_('COM_RSDIRECTORY_CREDIT_PACKAGE_TITLE') ),
            JHtml::_( 'select.option', 'user', JText::_('COM_RSDIRECTORY_USER') ),
            JHtml::_( 'select.option', 'entry_title', JText::_('COM_RSDIRECTORY_ENTRY') ),
            JHtml::_( 'select.option', 'entry_paid', JText::_('COM_RSDIRECTORY_ENTRY_PAID') ),
            JHtml::_( 'select.option', 't.gateway', JText::_('COM_RSDIRECTORY_GATEWAY') ),
            JHtml::_( 'select.option', 't.currency', JText::_('COM_RSDIRECTORY_CURRENCY') ),
            JHtml::_( 'select.option', 't.price', JText::_('COM_RSDIRECTORY_PRICE') ),
            JHtml::_( 'select.option', 't.tax', JText::_('COM_RSDIRECTORY_TAX') ),
            JHtml::_( 'select.option', 't.total', JText::_('COM_RSDIRECTORY_TOTAL') ),
            JHtml::_( 'select.option', 't.credits', JText::_('COM_RSDIRECTORY_CREDITS') ),
            JHtml::_( 'select.option', 't.status', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 't.date_created', JText::_('COM_RSDIRECTORY_DATE_CREATED') ),
            JHtml::_( 'select.option', 't.date_finalized', JText::_('COM_RSDIRECTORY_DATE_FINALIZED') ),
            JHtml::_( 'select.option', 't.id', JText::_('JGRID_HEADING_ID') ),
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
                    'input' => '<select name="filter_credits" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_CREDITS') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getCredits(), 'value', 'text', $this->getState('filter.credits'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_price" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_PRICE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getPrices(), 'value', 'text', $this->getState('filter.price'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_currency" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_CURRENCY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getCurrencies(), 'value', 'text', $this->getState('filter.currency'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_gateway" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_GATEWAY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getGateways(), 'value', 'text', $this->getState('filter.gateway'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_entry_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_ENTRY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_user_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_USER') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getUsers(), 'value', 'text', $this->getState('filter.user_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_credit_package" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_CREDIT_PACKAGE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getCreditPackages(), 'value', 'text', $this->getState('filter.credit_package'), true ) .
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
            
        // Credit package filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_CREDIT_PACKAGE'),
            'filter_credit_package',
            JHtml::_( 'select.options', $this->getCreditPackages(), 'value', 'text', $this->getState('filter.credit_package') )
        );
            
        // User filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_USER'),
            'filter_user_id',
            JHtml::_( 'select.options', $this->getUsers(), 'value', 'text', $this->getState('filter.user_id') )
        );
            
        // Entry filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_ENTRY'),
            'filter_entry_id',
            JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id') )
        );
            
        // Gateway filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_GATEWAY'),
            'filter_gateway',
            JHtml::_( 'select.options', $this->getGateways(), 'value', 'text', $this->getState('filter.gateway') )
        );
            
        // Currency filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_CURRENCY'),
            'filter_currency',
            JHtml::_( 'select.options', $this->getCurrencies(), 'value', 'text', $this->getState('filter.currency') )
        );
            
        // Price filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_PRICE'),
            'filter_price',
            JHtml::_( 'select.options', $this->getPrices(), 'value', 'text', $this->getState('filter.price') )
        );
            
        // Credits filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_CREDITS'),
            'filter_credits',
            JHtml::_( 'select.options', $this->getCredits(), 'value', 'text', $this->getState('filter.credits') )
        );
            
        return RSDirectoryToolbarHelper::render();
    }
        
    /**
     * Build a list of credit packages.
     *
     * @access public
     *
     * @return mixed
     */
    public function getCreditPackages()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('credit_title', 'value') . ', ' . $db->qn('credit_title', 'text') )
               ->from( $db->qn('#__rsdirectory_users_transactions') )
               ->group( $db->qn('credit_title') )
               ->order( $db->qn('credit_title') );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of users.
     *
     * @access public
     *
     * @return mixed
     */
    public function getUsers()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('u.id', 'value') . ', ' . $db->qn('u.name', 'text') )
               ->from( $db->qn('#__users', 'u') )
               ->innerJoin( $db->qn('#__rsdirectory_users_transactions', 't') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('t.user_id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
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
               ->innerJoin( $db->qn('#__rsdirectory_users_transactions', 't') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('t.entry_id') )
               ->group( $db->qn('e.id') )
               ->order( $db->qn('e.title') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of gateways.
     *
     * @access public
     *
     * @return mixed
     */
    public function getGateways()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('gateway') )
               ->from( $db->qn('#__rsdirectory_users_transactions') )
               ->group( $db->qn('gateway') )
               ->order( $db->qn('gateway') );
               
        $db->setQuery($query);
            
        // Get the results.
        $gateways = $db->loadColumn();
            
        // Initialize the options array.
        $options = array();
            
        if ($gateways)
        {
            foreach ($gateways as $gateway)
            {
                $options[] = (object)array(
                    'value' => $gateway,
                    'text' => JText::_( 'COM_RSDIRECTORY_TRANSACTION_GATEWAY_' . strtoupper($gateway) ),
                );
            }
        }
            
        return $options;
    }
        
    /**
     * Build a list of currencies.
     *
     * @access public
     *
     * @return mixed
     */
    public function getCurrencies()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('currency', 'value') . ', ' . $db->qn('currency', 'text') )
               ->from( $db->qn('#__rsdirectory_users_transactions') )
               ->group( $db->qn('currency') )
               ->order( $db->qn('currency') );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of prices.
     *
     * @access public
     *
     * @return mixed
     */
    public function getPrices()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('price', 'value') . ', ' . $db->qn('price', 'text') )
               ->from( $db->qn('#__rsdirectory_users_transactions') )
               ->group( $db->qn('price') )
               ->order( $db->qn('price') );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of credits.
     *
     * @access public
     *
     * @return mixed
     */
    public function getCredits()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('credits', 'value') . ', ' . $db->qn('credits', 'text') )
               ->from( $db->qn('#__rsdirectory_users_transactions') )
               ->group( $db->qn('credits') )
               ->order( $db->qn('credits') . ' = 0, ' . $db->qn('credits') );
               
        $db->setQuery($query);
            
        $items = $db->loadObjectList();
            
        if ($items)
        {
            foreach ($items as $item)
            {
                if (!$item->value)
                {
                    $item->text = JText::_('COM_RSDIRECTORY_UNLIMITED');
                }
            }
        }
            
        return $items;
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
            JHtml::_( 'select.option', 'finalized', ucfirst( JText::_('COM_RSDIRECTORY_TRANSACTION_STATUS_FINALIZED') ) ),
            JHtml::_( 'select.option', 'pending', ucfirst( JText::_('COM_RSDIRECTORY_TRANSACTION_STATUS_PENDING') ) ),
        );
    }
}