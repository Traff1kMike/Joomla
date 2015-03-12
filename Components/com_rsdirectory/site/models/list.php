<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Fields List model.
 */
class RSDirectoryModelList extends JModelList
{
    /**
     * Model context string.
     *
     * @var string
     *
     * @access public
     */
    public $_context = 'com_rsdirectory.list';
     
    /**
     * Subcategories array.
     *
     * @var array
     *
     * @access private
     */
    private $_items = null;
        
    /**
     * Method to auto-populate the model state.
     *
     * @access protected
     */
    protected function populateState($ordering = null, $direction = null)
    {
		$params = JFactory::getApplication()->getParams();
		$this->setState('params', $params);
			
		$this->setState('filter.published', 1);
    }
        
    /**
     * Redefine the function an add some properties to make the styling more easy
     * 
     * @param bool $recursive True if you want to return children recursively.
     *
     * @return mixed An array of data items on success, false on failure.
     */
    public function getItems($recursive = false)
    {
		if ( empty($this->_items) )
		{
			$app = JFactory::getApplication();
				
			$id = $app->input->getInt('id');
				
			if (!$id)
				return;
				
			$form_field = RSDirectoryHelper::getField($id);
				
			if (!$form_field)
				return;
				
			// Get the JUser object.
			$user = JFactory::getUser();
				
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;
				
			if ($active)
			{
				$params->loadString($active->params);
			}
				
			$db = JFactory::getDbo();
				
			$query = $db->getQuery(true)
			       ->select( $db->qn('value') )
				   ->from( $db->qn('#__rsdirectory_fields_properties') )
				   ->where( $db->qn('property_name') . ' = ' . $db->q('items') . ' AND ' . $db->qn('field_id') . ' = ' . $db->q($id) );
				   
			$db->setQuery($query);
				
			$result = $db->loadResult();
			
			if ( !trim($result) )
				return;
				
			$items_array = RSDirectoryHelper::getOptions($result);
				
			$items = array();
				
			$num_entries = $params->get('num_entries');
				
			foreach ($items_array as $item)
			{
				$item_aux = clone $item;
					
				if ($num_entries)
				{
					$item_aux->entries_count = 0;
				}
					
				$items[] = $item_aux;
			}
				
			$column = $db->qn( ($form_field->create_column ? 'ec' : 'e') . ".$form_field->column_name" );
				
			if ($num_entries)
			{
				$can_view_all_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_all_unpublished_entries');
				$can_view_own_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_own_unpublished_entries');
					
				foreach ($items as $item)
				{
					$query = $db->getQuery(true)
				           ->select( 'COUNT(*) AS ' . $db->qn('count') )
					       ->from( $db->qn('#__rsdirectory_entries', 'e') )
					       ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
					       ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') .  ' = ' . $db->qn('u.id') )
					       ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' >= ' . $db->q( JFactory::getDate()->toSql() ) . ')' )
					       ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
					       ->where( $column . ' LIKE ' . $db->q( '%' . $db->escape($item->value, true) . '%' ) );
						
					if (!$can_view_all_unpublished_entries)
					{
						if ($can_view_own_unpublished_entries)
						{
						   $query->where(
								'( (' . $db->qn('e.published') . ' = ' . $db->q(1) . ' AND ' . $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) . ')' .
								' OR ' . $db->qn('e.user_id') . ' = ' . $db->q($user->id) . ')'
							);
						}
						else
						{
							$query->where( $db->qn('e.published') . ' = ' . $db->q(1) )
							      ->where( $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) );	
						}
					}
						
					$db->setQuery($query);
					$item->entries_count = $db->loadResult();
				}
			}
				
			// Get the items count.
			$items_count = $items ? count($items) : 0;
				
			// Get the number of columns.
			$num_columns = $params->get('num_columns', 3);
				
			// Get the ordering method.
			$multi_column_order = $params->get('multi_column_order', 1);
				
			// Calculate the number of rows.
			$num_rows = ceil($items_count/$num_columns);
				
			// Initialize the data array.
			$data = array();
				
			$col = 0;
			$row = 0;
				
			// Order down.
			if ($multi_column_order == 0)
			{
				foreach ($items as $item)
				{
					$data[$row][$col] = 1;
						
					if ($col >= $num_columns - 1)
					{
						$row++;
						$col = 0;
					}
					else
					{
						$col++;
					}
				}
					
				$col = 0;
				$row = 0;
					
				foreach ($items as $item)
				{
					$data[$row][$col] = $item;
						
					$row++;
						
					if ( !isset($data[$row][$col]) || $row == $num_rows )
					{
						$row = 0;
						$col++;
					}
				}
			}
			// Order across.
			else
			{
				foreach ($items as $item)
				{
					$data[$row][$col] = $item;
						
					if ($col >= $num_columns - 1)
					{
						$row++;
						$col = 0;
					}
					else
					{
						$col++;
					}
				}
			}
				
			$this->_items = $data;
		}
			
		return $this->_items;
    }
}