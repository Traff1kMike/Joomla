<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories model.
 */
class RSDirectoryModelCategories extends JModelList
{
    /**
     * Model context string.
     *
     * @var string
     *
     * @access public
     */
    public $_context = 'com_rsdirectory.categories';
        
    /**
     * Parent category object.
     *
     * @var object
     *
     * @access private
     */
    private $_parent = null;
     
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
		$app = JFactory::getApplication();
		$this->setState('filter.extension', 'com_rsdirectory');
			
		// Get the parent id if defined.
		$parentId = $app->input->getInt('id');
		$this->setState('filter.parentId', $parentId);
			
		$params = $app->getParams();
		$this->setState('params', $params);
			
		$this->setState('filter.published', 1);
		$this->setState('filter.access', true);
    }
        
    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string $id A prefix for the store id.
     *
     * @return  string  A store id.
     */
    protected function getStoreId($id = '')
    {
		// Compile the store id.
		$id .= ':' . $this->getState('filter.extension');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.parentId');
			
		return parent::getStoreId($id);
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
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;
				
			if ($active)
			{
				$params->loadString($active->params);
			}
				
			$options = array(
				'extension' => 'com_rsdirectory',
				'table' => '#__rsdirectory_entries',
				'field' => 'category_id',
				'statefield' => 'published',
				'countItems' => $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0),
			);
				
			if ( RSDirectoryHelper::isJ25() )
			{
				// Import Joomla Categories library.
				jimport( 'joomla.application.categories' );
			}
				
			// Create a new JCategories object.
			$categories = new JCategories($options);
				
			$this->_parent = $categories->get( $this->getState('filter.parentId', 'root') );
				
			if ( is_object($this->_parent) )
			{
				// Get the items.
				if ( $items = $this->_parent->getChildren($recursive) )
				{
					if ( $params->get('show_subcategories_thumbnails') )
					{
						self::getCategoriesIds($items, $ids);
							
						if ($ids)
						{
							// Get DBO.
							$db = JFactory::getDbo();
								
							$query = $db->getQuery(true)
								   ->select('*')
								   ->from( $db->qn('#__rsdirectory_uploaded_files', 'f') )
								   ->innerJoin( $db->qn('#__rsdirectory_uploaded_files_categories_relations', 'r') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('r.file_id')  );
								
							$db->setQuery($query);
								
							$files_list = $db->loadObjectList();
								
							$files = array();
								
							foreach ($files_list as $file)
							{
								$files[$file->category_id] = $file;
							}
								
							self::setCategoriesThumbnails($items, $files);
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
			}
		}
			
		return $this->_items;
    }
        
	/**
	 * Get parent.
	 *
	 * @access public
	 *
	 * @return object
	 */
    public function getParent()
    {
        if ( !is_object($this->_parent) )
        {
            $this->getItems();
        }
            
        return $this->_parent;
    }
		
	/**
	 * Get categories ids.
	 *
	 * @access private
	 *
	 * @static
	 *
	 * @param array $items
	 * @param array &$ids
	 */
	private static function getCategoriesIds($items, &$ids)
	{
		foreach ($items as $item)
		{
			$ids[] = $item->id;
				
			if ( $children = $item->getChildren(false) )
			{
				self::getCategoriesIds($children, $ids);
			}
		}
	}
		
	/**
	 * Set categories thumbnails.
	 *
	 * @access private
	 *
	 * @static
	 *
	 * @param array &$items
	 * @param array $files
	 */
	private static function setCategoriesThumbnails(&$items, $files)
	{
		foreach ($items as &$item)
		{
			$item->thumbnail_url = isset($files[$item->id]) ? RSDirectoryHelper::getImageURL($files[$item->id]->hash, 'small') : '';
				
			if ( $children = $item->getChildren(true) )
			{
				self::setCategoriesThumbnails($children, $files);
			}
		}
	}
}