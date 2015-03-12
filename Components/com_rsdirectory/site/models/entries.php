<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entries model.
 */
class RSDirectoryModelEntries extends JModelList
{
    /**
     * Constructor.
     *
     * @access public
     *
     * @param array An optional associative array of configuration settings.
     */
    public function __construct( $config = array() )
    {
		if ( empty($config['filter_fields'] ) )
		{
			$config['filter_fields'] = array(
				'c.path',
				'c.lft',
				'e.published_time',
				'e.expiry_time',
				'e.title',
				'e.hits',
				'author',
			);
		}
			
		parent::__construct($config);
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
		$app = JFactory::getApplication();
			
		$params = $app->getParams();
		$this->setState('params', $params);
			
		// List state information.
		parent::populateState( $params->get('order_by', 'e.published_time'), $params->get('order_dir', 'desc') );
			
		if ( !$params->get('show_ordering') )
		{
			$this->setState( 'list.ordering', $params->get('order_by', 'e.published_time') );
			$this->setState( 'list.direction', $params->get('order_dir', 'desc') );
		}
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
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get the JInput object.
		$jinput = $app->input;
			
		// Get DBO.
		$db = JFactory::getDBO();
			
		// Get the menu item params.
		$params = $this->state->params;
			
		// Get the JUser object.
		$user = JFactory::getUser();
			
		// Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
			
		$select = array(
			$db->qn('e') . '.*',
			$db->qn('ec') . '.*',
			$db->qn('c.title', 'category_title'),
			$db->qn('c.path', 'category_path'),
			$db->qn('f.entry_id', 'faved'),
			$db->qn("u.$author", 'author'),
		);
			
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries', 'e') )
			   ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') .  ' = ' . $db->qn('u.id') )
			   ->leftJoin( $db->qn('#__rsdirectory_favorites', 'f') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('f.entry_id') . ' AND ' . $db->qn('f.user_id') .  ' = ' . $db->q($user->id) )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->group( $db->qn('e.id') );
			   
		// Process permissions.
		$can_view_all_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_all_unpublished_entries');
		$can_view_own_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_own_unpublished_entries');
			
		if ($can_view_all_unpublished_entries || $can_view_own_unpublished_entries)
		{
			$statuses = $jinput->get( 'status', array(0, 1), 'status' );
				
			$status_conditions = array();
				
			if ( in_array(0, $statuses) )
			{
				$unpublished_condition[] = '(' . $db->qn('e.published') . ' = ' . $db->q(0) . ' OR ' . $db->qn('e.published_time') . ' > ' . $db->q( JFactory::getDate()->toSql() ) . ')';
					
				if (!$can_view_all_unpublished_entries)
				{
					$unpublished_condition[] = '(' . $db->qn('e.user_id') . ' = ' . $db->q($user->id) . ')';
				}
					
				$status_conditions[] = implode(' AND ', $unpublished_condition);
			}
				
			if ( in_array(1, $statuses) )
			{
				$status_conditions[] = '(' . $db->qn('e.published') . ' = ' . $db->q(1) . ' AND ' . $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) . ')';
			}
				
			if ($status_conditions)
			{
				$query->where( '(' . implode(' OR ', $status_conditions) . ')' );
			}
		}
		else
		{
			$query->where( $db->qn('e.published') . ' = ' . $db->q(1) )
				  ->where( $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) )
				  ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' >= ' . $db->q( JFactory::getDate()->toSql() ) . ')' );	
		}
			
		// Initialize the featured categories array.
		$featured_categories = array();
			
		// Get categories filter.
		$categories = $jinput->get( 'categories', array(), 'array' );
			
		// Get a single category.
		$category = $jinput->getInt('category');
			
		if ($category)
		{
			$categories[] = $category;
		}
			
		if ( !empty($categories) )
		{
			$featured_categories = RSDirectoryHelper::arrayInt($categories);
		}
			
		// Get the featured categories from the module settings if there were no categories filters.
		if (!$featured_categories)
		{
			$featured_categories = RSDirectoryHelper::arrayInt( $params->get('featured_categories') );
		}
			
		if ( empty($featured_categories) || in_array(0, $featured_categories) )
		{
			$categories_query = $db->getQuery(true)
							  ->select( $db->qn('id') )
							  ->from( $db->qn('#__categories') )
							  ->where( $db->qn('published') . ' = ' . $db->q(1) )
							  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' );
								
			$db->setQuery($categories_query);
				
			$categories_ids = $db->loadColumn();
		}
		else
		{
			$categories_query = $db->getQuery(true)
							  ->select( $db->qn('lft') . ', ' . $db->qn('rgt') )
							  ->from( $db->qn('#__categories') )
							  ->where( $db->qn('id') . ' IN (' . implode(',', $featured_categories) . ')' )
							  ->where( $db->qn('published') . ' = ' . $db->q(1) )
							  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' );
								
			$db->setQuery($categories_query);
			$categories = $db->loadObjectList();
				
			if ($categories)
			{
				$categories_query = $db->getQuery(true)
								  ->select( $db->qn('id') )
								  ->from( $db->qn('#__categories') )
								  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' )
								  ->where( $db->qn('published') . ' = ' . $db->q(1) );
								  
				$or = array();
					
				foreach ($categories as $category)
				{
					$or[] = '(' . $db->qn('lft') . ' >= ' . $db->q($category->lft) . ' AND ' . $db->qn('rgt') . ' <= ' . $db->q($category->rgt) . ')';
				}
					
				$categories_query->where( '(' . implode(' OR ', $or) . ')' );
					
				$db->setQuery($categories_query);
					
				$categories_ids = $db->loadColumn();
			}
		}
			
		if ( empty($categories_ids) )
		{
			$categories_ids = array(0);
		}
			
		$query->where( $db->qn('e.category_id') . ' IN (' . implode( ',', array_unique($categories_ids) ) . ')' );
			
			
		// Process the users filter.
		$users = $jinput->get( 'users', array(), 'array' );
			
		// Get single user.
		$user = $jinput->getInt('user');
			
		if ($user)
		{
			$users[] = $user;
		}
			
		$users = RSDirectoryHelper::arrayInt($users);
			
		if ($users)
		{
			$query->where( $db->qn('e.user_id') . ' IN (' . implode( ',', array_unique($users) ) . ')' );
		}
			
			
		// Get the search query.
		list($q) = $jinput->get( 'q', array(''), 'array' );
			
		if ( trim($q) !== '')
		{
			// Initialize the forms ids array.
			$forms_ids = array();
				
			// Get all the categories that belong to RSDirectory!
			$categories = RSDirectoryHelper::getCategories();
				
			// Is "All categories" selected?
			$all_categories = in_array(0, $featured_categories);
				
			foreach ($categories as $category)
			{
				if ( empty($featured_categories) || in_array($category->id, $featured_categories) || $all_categories )
				{
					$form_id = RSDirectoryHelper::getCategoryInheritedFormId($category->id, $categories);
						
					if ($form_id)
					{
						$forms_ids[] = $form_id;
					}
				}
			}
				
			$forms_ids = array_unique($forms_ids);
				
			// Get form fields.
			$form_fields = RSDirectoryHelper::getFormFields($forms_ids);
				
			// Initialize the conditions array.
			$where = array();
				
			if ($form_fields)
			{
				$q = $db->q( '%' . $db->escape($q, true) . '%' );
					
				foreach ($form_fields as $form_field)
				{
					if ( $form_field->properties->get('searchable_simple') && $form_field->column_name)
					{
						$table = $form_field->create_column ? 'ec' : 'e';
							
						if ($form_field->field_type == 'map')
						{
							$column_name = "{$form_field->column_name}_address";
						}
						else
						{
							$column_name = $form_field->column_name;
						}
							
						$where[] = $db->qn("$table.$column_name") . " LIKE $q";	
					}
				}
			}
				
			if ($where)
			{
				$query->where('(' . implode(' OR ', $where) . ')');
			}
		}
			
		// Process filters.
		if ( $f = $jinput->get( 'f', array(), 'array' ) )
		{
			if ( $jinput->getInt('filter') )
			{
				$fields = RSDirectoryHelper::getFilterFields($featured_categories);
					
				RSDirectoryHelper::buildEntriesFilteringQuery($fields, $f, $query);
			}
			// Process "Categories from Field Values".
			else
			{
				$fields = RSDirectoryHelper::getFormFields(null, true, true);
					
				if ($fields)
				{
					foreach ($f as $form_field_name => $values)
					{
						if ( is_string($values) && $values === '' )
							continue;
							
						$field = RSDirectoryHelper::findElements( array('form_field_name' => $form_field_name), $fields );
							
						if (!$field)
							continue;
							
						$table = $field->create_column ? 'ec' : 'e';
							
						$column_name = $db->qn("$table.$field->column_name");
							
						// Initialize the condition array.
						$cond = array();
							
						if ( !is_array($values) )
						{
							$values = (array)$values;
						}
							
						foreach ($values as $value)
						{
							if ( is_string($value) && $value === '' )
								continue;
								
							$cond[] = $column_name . ' LIKE ' . $db->q( '%' . $db->escape($value, true) . '%' );
						}
							
						if ($cond)
						{
							$query->where( '(' . implode(' OR ', $cond) . ')' );
						}
					}
				}
			}
		}
			
		// Initialize the order array.
		$order = array(
			$db->qn('promoted') . ' DESC'
		);
			
		switch ( $params->get('orderby_pri') )
		{
			case 'alpha':
				$order[] = $db->qn('c.path');
				break;
					
			case 'ralpha':
				$order[] = $db->qn('c.path') . ' DESC ';
				break;
					
			case 'order':
				$order[] = $db->qn('c.lft');
				break;
		}
			
		$order[] = $db->qn( $this->getState('list.ordering') ) . ' ' . $db->escape( $this->getState('list.direction') );
			
		$query->order($order);
			
		return $query;
    }
        
    /**
     * Method to get a list of entries.
     *
     * @access public
     *
     * @return mixed An array of objects on success, false on failure.
     */
    public function getItems()
    {
		// Get items.
		$items = parent::getItems();
			
		// Exit the function if there are no results.
		if (!$items)
			return;
			
		return RSDirectoryHelper::getEntriesData($items);
    }
        
    /**
     * Method to get a select for the sorting options.
     *
     * @access public
     *
     * @return string
     */
    public function getSortField()
    {
		$options = array(
			JHtml::_( 'select.option', '', JText::_('COM_RSDIRECTORY_SORT_BY') ),
			JHtml::_( 'select.option', 'e.published_time', JText::_('COM_RSDIRECTORY_DATE_PUBLISHED') ),
			JHtml::_( 'select.option', 'e.expiry_time', JText::_('COM_RSDIRECTORY_EXPIRY_DATE') ),
			JHtml::_( 'select.option', 'e.title', JText::_('JGLOBAL_TITLE') ),
			JHtml::_( 'select.option', 'author', JText::_('JAUTHOR') ),
			JHtml::_( 'select.option', 'e.hits', JText::_('JGLOBAL_HITS') ),
		);
			
		return JHtml::_( 'select.genericlist', $options, 'filter_order', ' class="input-medium pull-right" onchange="adminForm.submit();"', 'value', 'text', $this->getState('list.ordering') );
    }
        
    /**
     * Method to get a select for the sort directions.
     *
     * @access public
     *
     * @return string
     */
    public function getSortDirField()
    {
		$options = array(
			JHtml::_( 'select.option', '', JText::_('COM_RSDIRECTORY_ORDERING_DIRECTION') ),
			JHtml::_( 'select.option', 'asc', JText::_('COM_RSDIRECTORY_ASC') ),
			JHtml::_( 'select.option', 'desc', JText::_('COM_RSDIRECTORY_DESC') ),
		);
			
		return JHtml::_( 'select.genericlist', $options, 'filter_order_Dir', ' class="input-medium pull-right" onchange="adminForm.submit();"', 'value', 'text', $this->getState('list.direction') );
    }
}