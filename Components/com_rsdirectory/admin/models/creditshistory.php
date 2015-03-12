<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits History model.
 */
class RSDirectoryModelCreditsHistory extends JModelList
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
                'e.title',
				'user',
				'ec.object_type',
				'ec.credits',
				'ec.free',
				'ec.created_time',
				'ec.paid',
				'ec.id',
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
			$db->qn('ec') . '.*',
			$db->qn("u.$author", 'user'),
			$db->qn('e.title'),
			$db->qn('f.name', 'field_name'),
		);
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_entries_credits', 'ec') )
			   ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('ec.entry_id') . ' = ' . $db->qn('e.id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('ec.user_id') . ' = ' . $db->qn('u.id') )
			   ->leftJoin( $db->qn('#__rsdirectory_fields', 'f') . ' ON ' . $db->qn('ec.object_id') . ' = ' . $db->qn('f.id') . ' AND ' . $db->qn('ec.object_type') . ' = ' . $db->q('form_field') );
			   
		// Filter by entry.  
        if ( $entry_id = $this->getState('filter.entry_id') )
        {
            $query->where( $db->qn('ec.entry_id') . ' = ' . $db->q($entry_id) );
        }
			
		// Filter by user.  
        if ( $user_id = $this->getState('filter.user_id') )
        {
            $query->where( $db->qn('ec.user_id') . ' = ' . $db->q($user_id) );
        }
			
		// Filter by type.  
        if ( $type = $this->getState('filter.type') )
        {
            $query->where( $db->qn('ec.object_type') . ' = ' . $db->q($type) );
        }
			
		// Filter by credits.      
        if ( $credits = $this->getState('filter.credits') )
        {
            $query->where( $db->qn('ec.credits') . ' = ' . $db->q($credits) );
        }
			
		// Filter by credits.
		$is_free = $this->getState('filter.is_free');
			
        if ($is_free !== '')
        {
            $query->where( $db->qn('ec.free') . ' = ' . $db->q($is_free) );
        }
			
		// Paid Y/N ?
        $paid = $this->getState('filter.paid');
            
        if ( is_numeric($paid) )
        {
            $query->where( $db->qn('ec.paid') . ' = ' . $db->q($paid) );    
        }
			
		// Filter by search.
        $search = $this->getState('filter.search');
			
		if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $condition = array(
                $db->qn('e.title') . " LIKE $search",
                $db->qn('u.name') . " LIKE $search",
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
			
		$entry_id = $this->getUserStateFromRequest("$this->context.filter.entry_id", 'filter_entry_id', '');
        $this->setState('filter.entry_id', $entry_id);
			
		$user_id = $this->getUserStateFromRequest("$this->context.filter.user_id", 'filter_user_id', '');
        $this->setState('filter.user_id', $user_id);
			
		$type = $this->getUserStateFromRequest("$this->context.filter.type", 'filter_type', '');
        $this->setState('filter.type', $type);
			
		$credits = $this->getUserStateFromRequest("$this->context.filter.credits", 'filter_credits', '');
        $this->setState('filter.credits', $credits);
			
		$is_free = $this->getUserStateFromRequest("$this->context.filter.is_free", 'filter_is_free', '');
        $this->setState('filter.is_free', $is_free);
			
		$paid = $this->getUserStateFromRequest("$this->context.filter.paid", 'filter_paid');
        $this->setState('filter.paid', $paid);
			
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
			JHtml::_( 'select.option', 'e.title', JText::_('COM_RSDIRECTORY_ENTRY') ),
            JHtml::_( 'select.option', 'user', JText::_('COM_RSDIRECTORY_USER') ),
			JHtml::_( 'select.option', 'ec.object_type', JText::_('COM_RSDIRECTORY_TYPE') ),
			JHtml::_( 'select.option', 'ec.credits', JText::_('COM_RSDIRECTORY_CREDITS') ),
			JHtml::_( 'select.option', 'ec.free', JText::_('COM_RSDIRECTORY_FREE') ),
			JHtml::_( 'select.option', 'ec.paid', JText::_('COM_RSDIRECTORY_PAID') ),
			JHtml::_( 'select.option', 'ec.created_time', JText::_('JGLOBAL_CREATED') ),
            JHtml::_( 'select.option', 'ec.id', JText::_('JGRID_HEADING_ID') ),
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
                    'input' => '<select name="filter_paid" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_PAID') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.paid'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_is_free" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_IS_FREE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.is_free'), true ) .
                               '</select>',
                ),
				array(
                    'input' => '<select name="filter_credits" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_CREDITS') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getCredits(), 'value', 'text', $this->getState('filter.credits'), true ) .
                               '</select>',
                ),
				array(
                    'input' => '<select name="filter_type" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_TYPE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getTypes(), 'value', 'text', $this->getState('filter.type'), true ) .
                               '</select>',
                ),
				array(
                    'input' => '<select name="filter_user_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_USER') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getUsers(), 'value', 'text', $this->getState('filter.user_id'), true ) .
                               '</select>',
                ),
				array(
                    'input' => '<select name="filter_entry_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_ENTRY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id'), true ) .
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
			
		// Entries filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_ENTRY'),
            'filter_entry_id',
            JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id') )
        );
			
		// Users filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_USER'),
            'filter_user_id',
            JHtml::_( 'select.options', $this->getUsers(), 'value', 'text', $this->getState('filter.user_id') )
        );
			
		// Types filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_TYPE'),
            'filter_type',
            JHtml::_( 'select.options', $this->getTypes(), 'value', 'text', $this->getState('filter.type') )
        );
			
		// Credits filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_CREDITS'),
            'filter_credits',
            JHtml::_( 'select.options', $this->getCredits(), 'value', 'text', $this->getState('filter.credits') )
        );
			
		// Is free?
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_IS_FREE'),
            'filter_is_free',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.is_free'), true )
        );
			
		// Paid.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_PAID'),
            'filter_paid',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.paid') )
        );
            
        return RSDirectoryToolbarHelper::render();
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
               ->innerJoin( $db->qn('#__rsdirectory_entries_credits', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
               ->group( $db->qn('e.id') )
               ->order( $db->qn('e.title') );
                
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
               ->innerJoin( $db->qn('#__rsdirectory_entries_credits', 'ec') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('ec.user_id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
		
	/**
     * Build a list of object types.
     *
     * @access public
     *
     * @return mixed
     */
    public function getTypes()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('object_type') )
               ->from( $db->qn('#__rsdirectory_entries_credits') )
               ->group( $db->qn('object_type') )
               ->order( $db->qn('object_type') );
                
        $db->setQuery($query);
			
		$results = $db->loadColumn();
			
		$types = array();
			
		if ($results)
		{
            foreach ($results as $result)
			{
				$types[] = (object)array(
					'value' => $result,
					'text' => JText::_( 'COM_RSDIRECTORY_CREDIT_OBJECT_TYPE_' . strtoupper($result) ),
				);
			}
		}
			
        return $types;
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
               ->from( $db->qn('#__rsdirectory_entries_credits') )
               ->group( $db->qn('credits') )
               ->order( $db->qn('credits') );
               
        $db->setQuery($query);
            
        return $db->loadObjectList();
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