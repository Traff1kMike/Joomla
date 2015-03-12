<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories Batch model.
 */
class RSDirectoryModelCategoriesBatch extends JModelAdmin
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
        $form = $this->loadForm( 'com_rsdirectory.categoriesbatch', 'categoriesbatch', array('control' => 'jform', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
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
        if ( !trim($data['categories']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_CATEGORIES_BATCH_CATEGORIES_REQUIRED') );
            return false;
        }
            
        return $data;
    }
        
    /**
     * Save the email message.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        $categories = explode("\n", $data['categories']);
        $categories = array_filter($categories, 'trim');
            
        // Initialize the parents array.
        $parents = array(
            0 => $data['parent_id'],
        );
            
        // Initialize the count.
        $count = 0;
            
        foreach ($categories as $category)
        {
            // Initialize the current level.
            $current_level = 1;
                
            $category = trim($category);
                
            $len = strlen($category);
                
            for ($i = 0; $i < $len; $i++)
            {
                if ($category{$i} != '-')
                    break;
                    
                $current_level++;
            }
                
            // Skip the category if it doesn't have a parent. This can occur when the categories string is ill-formatted.
            if ( !isset($parents[$current_level - 1]) )
                continue;
                
            $category = trim( substr($category, $current_level - 1) );
                
            // Skip the category if its title is empty.
            if (!$category)
                continue;
                
            // Get the table.
            $table = $this->getTable();
                
            $table->setLocation($parents[$current_level - 1], 'last-child');
                
            $table_data = array(
                'title' => $category,
                'parent_id' => $parents[$current_level - 1],
                'language' => '*',
                'extension' => 'com_rsdirectory',
                'params' => array(
                    'form_id' => $data['form_id'],
                ),
            );
                
            // Try to save the current category and if there were any errors, skip to the next category.
            if ( !$table->save($table_data) )
                continue;
                
            if ( !$table->rebuildPath($table->id) )
                continue;
                
            if ( !$table->rebuild($table->id, $table->lft, $table->level, $table->path) )
                continue;
                
            $parents[$current_level] = $table->id;
                
            $parents = array_slice($parents, 0, $current_level + 1);
                
            // Increment the count.
            $count++;
        }
            
        // Save the data in the session.
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.categoriesbatch.count', $count);
            
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
}