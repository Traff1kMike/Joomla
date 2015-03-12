<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Favorites model.
 */
class RSDirectoryModelFavorites extends JModelList
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
				'e.published_time',
				'e.expiry_time',
				'e.title',
				'e.hits',
				'author',
				'f.created_time',
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
		parent::populateState( $params->get('order_by', 'e.created_time'), $params->get('order_dir', 'desc') );
			
		if ( !$params->get('show_ordering') )
		{
			$this->setState( 'list.ordering', $params->get('order_by', 'f.created_time') );
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
		// Get DBO.
		$db = JFactory::getDBO();
			
		// Get the JUser object.
		$user = JFactory::getUser();
			
		// Get the menu item params.
		$params = $this->state->params;
			
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
			   ->innerJoin( $db->qn('#__rsdirectory_favorites', 'f') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('f.entry_id') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
			   ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' >= ' . $db->q( JFactory::getDate()->toSql() ) . ')' )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->where( $db->qn('f.user_id') .  ' = ' . $db->q($user->id) );
			   
		// Process permissions.
		if ( !RSDirectoryHelper::checkUserPermission('can_view_all_unpublished_entries') )
		{
			if ( RSDirectoryHelper::checkUserPermission('can_view_own_unpublished_entries') )
			{
				$cond1 = '(' . $db->qn('e.published') . ' = ' . $db->q(1) . ' AND ' . $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) . ')';
				$cond2 = $db->qn('u.id') . ' = ' . $db->q($user->id);
					
				$query->where("($cond1 OR $cond2)");
			}
			else
			{
				$query->where( $db->qn('e.published') . ' = ' . $db->q(1) )
			          ->where( $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) );	
			}
		}
			   
		$categories_query = $db->getQuery(true)
						  ->select( $db->qn('id') )
						  ->from( $db->qn('#__categories') )
						  ->where( $db->qn('published') . ' = ' . $db->q(1) )
						  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' );
							
		$db->setQuery($categories_query);
		  
		$categories_ids = $db->loadColumn();
			
		if ( empty($categories_ids) )
		{
			$categories_ids = array(0);
		}
			
		$query->where( $db->qn('e.category_id') . ' IN (' . implode( ',', array_unique($categories_ids) ) . ')' );
			
		// Initialize the order array.
		$order = array();
			
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
			JHtml::_( 'select.option', 'f.created_time', JText::_('COM_RSDIRECTORY_DATE_FAVORITED') ),
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