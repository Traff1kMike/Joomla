<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Category model.
 */
class RSDirectoryModelCategory extends JModelAdmin
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
    public function getTable( $type = 'Category', $prefix = 'RSDirectoryTable', $config = array() )
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
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.category', 'category', array('control' => 'jform', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
        
    /**
     * Method to get the data that should be injected in the form.
     *
     * @access protected
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState( 'com_rsdirectory.edit.category.data', array() );
            
        return $data ? $data : $this->getItem();
    }
        
    /**
	 * Method to get a single record.
	 *
	 * @param int $pk The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
        $item = parent::getItem($pk);
            
        if ( isset($item->metadata) )
        {
            $registry = new JRegistry;
            $registry->loadString($item->metadata);
            $item->metadata = $registry->toArray();
        }
            
        return $item;
    }
        
    /**
     * Validate form data.
     *
     * @access public
     * 
     * @param object $form The form to validate against.
     * @param array $data The data to validate.
     * @param string $group The name of the field group to validate.
     * 
     * @return mixed
     */
    public function validate($form, $data, $group = null)
    {
        $data = parent::validate($form, $data, $group);
            
        if (!$data)
            return $data;
            
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the thumbnail.
        $thumb = $jinput->files->get('jform');
            
        $thumb = $thumb && isset($thumb['params']['thumbnail']) ? $thumb['params']['thumbnail'] : '';
         
        // Validate thumbnail.
        if ( $thumb && isset($thumb['name']) && $thumb['name'] )
        {
            // Allowed file types.
            $allowed_file_types = array('jpg', 'jpeg', 'pjpeg', 'gif', 'png', 'x-png');
                
            // Allowed file types string.
            $allowed_file_types_str = implode(', ', $allowed_file_types);
            
            // Get the file type.
            list(,$type) = explode('/', $thumb['type']);
                
            if ( !in_array($type, $allowed_file_types) )
            {
                $this->setError( JText::sprintf('COM_RSDIRECTORY_IMAGE_UNALLOWED_FILE_TYPE', $allowed_file_types_str) );
                return false;
            }
        }
            
        return $data;
    }
        
    /**
     * Save the field.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        // Exit the function if the data array is invalid.
        if (!$data)
            return false;
			
        $dispatcher = RSDirectoryHelper::isJ30() ? JEventDispatcher::getInstance() : JDispatcher::getInstance();
        $is_new = true;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Set the extension.
        $data['extension'] = 'com_rsdirectory';
            
        // Get the table.
        $table = $this->getTable();
            
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the task.
        $task = $jinput->get('task');
            
        // Get the category id.
        $id = $jinput->getInt('id');
        $id = $id ? $id : (int)$this->getState( $this->getName() . '.id' );
            
        // Load the row if saving an existing category.
        if ($id)
        {
            $table->load($id);
                
            // Get thumbnail data.
            $old_thumb = RSDirectoryHelper::getCategoryThumbObject(0, $id);
                
            $is_new = false;
        }
            
        // Set the new parent id if parent id not matched OR while New/Save as Copy.
        if ( $table->parent_id != $data['parent_id'] || !$jinput->getInt('id') )
        {
            $table->setLocation($data['parent_id'], 'last-child');
        }
            
            
        // Alter the title for save as copy.
        if ($task == 'save2copy')
        {
            list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['alias'], $data['title']);
            $data['id'] = null;
            $data['title'] = $title;
            $data['alias'] = $alias;
                
            // Overwrite the previously loaded table.
            $table = $this->getTable();
                
            $table->setLocation($data['parent_id'], 'last-child');
        }
		else if ($table->id)
		{
			$params = new JRegistry($table->params);
				
			$old_form_id = $params->get('form_id');
			$new_form_id = $data['params']['form_id'];
				
			if (!$old_form_id || !$new_form_id)
			{
				$inherited_form_id = RSDirectoryHelper::getCategoryInheritedFormId($table->id);
					
				if (!$old_form_id)
				{
					$old_form_id = $inherited_form_id;
				}
					
				if (!$new_form_id)
				{
					$new_form_id = $inherited_form_id;
				}
			}
				
			if ($old_form_id != $new_form_id)
			{
				self::changeEntriesForms($table->id, $new_form_id);
			}
		}
			
        // Bind the data.
        if ( !$table->bind($data) )
        {
            $this->setError( $table->getError() );
            return false;
        }
            
        // Check the data.
        if ( !$table->check() )
        {
            $this->setError( $table->getError() );
            return false;
        }
            
        // Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger( $this->event_before_save, array($this->option . '.' . $this->name, &$table, $is_new) );
            
		if ( in_array(false, $result, true) )
		{
			$this->setError($table->getError());
			return false;
		}
			
        // Store the data.
        if ( !$table->store() )
        {
            $this->setError( $table->getError() );
            return false;
        }
            
        // Get the thumbnail.
        $thumb = $jinput->files->get('jform');
            
        $thumb = empty($thumb['params']['thumbnail']) ? '' : $thumb['params']['thumbnail'];
            
        // Set the destination directory.
        $dest_dir = JPATH_ROOT . '/components/com_rsdirectory/files/images/categories/';
            
        // Process thumbnail.
        if ( !empty($thumb['name']) )
        {
            jimport('joomla.filesystem.file');
                
            // Delete old thumb.
            if ( $task != 'save2copy' && !empty($old_thumb) )
            {
                JFile::delete($dest_dir . $old_thumb->file_name);
                    
                $query = $db->getQuery(true)
                       ->delete( $db->qn('#__rsdirectory_uploaded_files') )
                       ->where( $db->qn('id') . ' = ' . $db->q($old_thumb->id) );
                       
                $db->setQuery($query);
                $db->execute();
                    
                $query = $db->getQuery(true)
                       ->delete( $db->qn('#__rsdirectory_uploaded_files_categories_relations') )
                       ->where( $db->qn('file_id') . ' = ' . $db->q($old_thumb->id) );
                       
                $db->setQuery($query);
                $db->execute();
            }
                
            // Get the extension.
            $ext = strtolower( JFile::getExt($thumb['name']) );
                
            // Get the hash.
            $hash = RSDirectoryHelper::getHash();
                
            // Generate a file name.
            $file_name = $hash . ($ext ? ".$ext" : '');
                
            if ( JFile::upload($thumb['tmp_name'], $dest_dir . $file_name) )
            {
                $thumb = (object)array(
                    'user_id' => $table->created_user_id,
                    'hash' => $hash,
                    'file_name' => $file_name,
                    'original_file_name' => $thumb['name'],
                );
                    
                $db->insertObject('#__rsdirectory_uploaded_files', $thumb, 'id');
                    
                // Get the file id.
                $file_id = $thumb->id;
                    
                // Insert the file - category relation.
                $relation = (object)array(
                    'file_id' => $file_id,
                    'category_id' => $table->id,
                );
                    
                $db->insertObject('#__rsdirectory_uploaded_files_categories_relations', $relation);
            }
        }
        else if ( $task == 'save2copy' && !empty($old_thumb) )
        {
            // Get the extension.
            $ext = strtolower( JFile::getExt($old_thumb->file_name) );
                
            // Get the hash.
            $hash = RSDirectoryHelper::getHash();
                
            // Generate a file name.
            $file_name = $hash . ($ext ? ".$ext" : '');
                
            // Make a copy of the original thumbnail.
            if ( JFile::copy($old_thumb->file_name, $file_name, $dest_dir) )
            {
                $thumb = (object)array(
                    'user_id' => $table->created_user_id,
                    'hash' => $hash,
                    'file_name' => $file_name,
                    'original_file_name' => $old_thumb->original_file_name,
                );
                    
                $db->insertObject('#__rsdirectory_uploaded_files', $thumb, 'id');
                    
                // Get the file id.
                $file_id = $thumb->id;
                    
                // Insert the file - category relation.
                $relation = (object)array(
                    'file_id' => $file_id,
                    'category_id' => $table->id,
                );
                    
                $db->insertObject('#__rsdirectory_uploaded_files_categories_relations', $relation);
            }
        }
        else if ( !empty($old_thumb) )
        {
            $file_id = $old_thumb->file_id;
        }
            
        // Save the thumbnail id.
		$data['params']['thumbnail'] = empty($file_id) ? 0 : $file_id;
			
		$query = $db->getQuery(true)
		       ->update( $db->qn('#__categories') )
			   ->set( $db->qn('params') . ' = ' . $db->q( json_encode($data['params']) ) )
			   ->where( $db->qn('id') . ' = ' . $db->q($table->id) );
			   
		$db->setQuery($query);
		$db->execute();
            
        // Trigger the onContentAfterSave event.
		$dispatcher->trigger( $this->event_after_save, array($this->option . '.' . $this->name, &$table, $is_new) );
            
        // Rebuild the path for the category.
        if ( !$table->rebuildPath($table->id) )
        {
            $this->setError( $table->getError() );
            return false;
        }
            
        // Rebuild the paths of the category's children.
        if ( !$table->rebuild($table->id, $table->lft, $table->level, $table->path) )
        {
            $this->setError( $table->getError() );
            return false;
        }
            
        $this->setState( $this->getName() . '.id', $table->id );
            
        // Clear the cache
        $this->cleanCache();
			
        return true;
    }
        
    /**
	 * Method rebuild the entire nested set tree.
	 *
	 * @access public
	 *
	 * @return boolean False on failure or error, true otherwise.
	 */
	public function rebuild()
	{
		// Get an instance of the table object.
		$table = $this->getTable();
            
		if ( !$table->rebuild() )
		{
			$this->setError( $table->getError() );
			return false;
		}
            
		// Clear the cache
		$this->cleanCache();
            
		return true;
	}
        
    /**
     * Method to save the reordered nested set tree.
     *
     * @param array $idArray An array of primary key ids.
     * @param integer $lft_array The lft value.
     *
     * @return boolean False on failure or error, True otherwise
     */
    public function saveorder($idArray = null, $lft_array = null)
    {
        // Get an instance of the table object.
        $table = $this->getTable();
            
        if ( !$table->saveorder($idArray, $lft_array) )
        {
            $this->setError( $table->getError() );
            return false;
        }
            
        // Clear the cache
        $this->cleanCache();
            
        return true;
    }
        
    /**
     * Method to change the title & alias.
     *
     * @param integer $parent_id The id of the parent.
     * @param string $alias The alias.
     * @param string $title The title.
     *
     * @return array Contains the modified title and alias.
     */
    protected function generateNewTitle($parent_id, $alias, $title)
    {
        // Alter the title & alias.
        $table = $this->getTable();
            
        while ( $table->load( array('alias' => $alias, 'parent_id' => $parent_id) ) )
        {
            $title = JString::increment($title);
            $alias = JString::increment($alias, 'dash');
        }
            
        return array($title, $alias);
    }
        
    /**
     * Remove category image.
     *
     * @access public
     *
     * @param int $pk The category id.
     *
     * @return bool
     */
    public function removeImage($pk)
    {
        // Get the category table.
        $table = $this->getTable();
            
        return $table->removeImage($pk);
    }
		
	/**
     * Method to delete all RSDirectory! categories.
     *
     * @access public
     *
     * @return bool
     */    
    public function deleteAll()
    {
		$db = JFactory::getDbo();
			
        // Delete all RSDirectory! categories.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__categories') )
               ->where( $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') );
               
        $db->setQuery($query);
		$db->execute();
            
        // Delete all RSDirectory! assets.
		$query = $db->getQuery(true)
               ->delete( $db->qn('#__assets') )
               ->where( $db->qn('name') . ' LIKE ' . $db->q('%com_rsdirectory.category%') );
               
        $db->setQuery($query);
		$db->execute();
			
		// Clear the component's cache
        $this->cleanCache();
            
		return true;
    }
        
    /**
     * Get RSFieldset.
     *
     * @access public
     * 
     * @return RSFieldset
     */
    public function getRSFieldset()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/fieldset.php';
            
        return new RSFieldset();
    }
        
    /**
     * Get RSTabs.
     *
     * @access public
     * 
     * @return RSTabs
     */
    public function getRSTabs()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/tabs.php';
            
        return new RSTabs('com-rsdirectory-category');
    }
		
	/**
	 * Change the forms of all entries posted in a category and all its relevant subcategories.
	 *
	 * @access private
	 *
	 * @static
	 *
	 * @param int $category_id
	 * @param int $form_id
	 */
	private static function changeEntriesForms($category_id, $form_id)
	{
		$ids = array($category_id);
			
		$children = RSDirectoryHelper::getSubcategories($category_id);
			
		if ($children)
		{
			self::buildCategoriesIdsArray($children, $form_id, $ids);
		}
			
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->update( $db->qn('#__rsdirectory_entries') )
			   ->set( $db->qn('form_id') . ' = ' . $db->q($form_id) )
			   ->where( $db->qn('category_id') . ' IN (' . implode(',', $ids) . ')' );
				
		$db->setQuery($query);
		$db->execute();
	}
		
	/**
	 * Build a list of relevant categories ids.
	 *
	 * @access private
	 *
	 * @static
	 *
	 * @param array $categories
	 * @param int $form_id
	 * @param array &$ids
	 *
	 * @see RSDirectoryModelCategory::changeEntriesForms
	 */
	private static function buildCategoriesIdsArray($categories, $form_id, &$ids)
	{
		foreach ($categories as $category)
		{
			$params = new JRegistry($category->params);
				
			if ( !$params->get('form_id') )
			{
				$ids[] = $category->id;
					
				$children = $category->getChildren();
					
				if ($children)
				{
					self::buildCategoriesIdsArray($children, $form_id, $ids);
				}
			}
		}
	}
}