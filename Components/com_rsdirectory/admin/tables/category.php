<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');
 
if ( RSDirectoryHelper::isJ25() )
{
    // Import JTableCategory.
    JLoader::register('JTableCategory', JPATH_PLATFORM . '/joomla/database/table/category.php');
}

/**
 * Category table.
 */
class RSDirectoryTableCategory extends JTableCategory
{
    /**
     * Method to delete a node and, optionally, its child nodes from the table.
     *
     * @access public
     *
     * @param int $pk The primary key of the node to delete.
     * @param bool $children  True to delete child nodes, false to move them up a level.
     *
     * @return boolean True on success.
     */
    public function delete($pk = null, $children = false)
    {
        $this->removeImage($pk);
        return parent::delete($pk, $children);
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
    public function removeImage($pk = null)
    {
        $k = $this->_tbl_key;
        $pk = is_null($pk) ? $this->$k : $pk;
            
        $this->load($pk);
            
        // Get params.
        $params = new JRegistry($this->params);
            
        // Convert the params to an array.
        $params = $params->toArray();
            
        // Get the file id.
        $file_id = $params['thumbnail'];
            
        if ($file_id)
        {
            jimport('joomla.filesystem.file');
                
            // Unset the thumbnail value.
            unset($params['thumbnail']);
                
            // Convert the params array to a JRegistry.
            $params = new JRegistry($params);
                
            // Set the new params value for the category.
            $this->params = $params->toString();
                
            // Save the new values.
            $this->store();
                
            // Get the file data.
            $file = RSDirectoryHelper::getCategoryThumbObject($file_id);
                
            // Get DBO.
            $db = JFactory::getDbo();
                
            $query = $db->getQuery(true)
                   ->delete( $db->qn('#__rsdirectory_uploaded_files') )
                   ->where( $db->qn('id') . ' = ' . $db->q($file_id) );
                   
            $db->setQuery($query);
            $db->execute();
                
            $query = $db->getQuery(true)
                   ->delete( $db->qn('#__rsdirectory_uploaded_files_categories_relations') )
                   ->where( $db->qn('file_id') . ' = ' . $db->q($file_id) );
                   
            $db->setQuery($query);
            $db->execute();
                
            return JFile::delete(JPATH_ROOT . "/components/com_rsdirectory/files/images/categories/$file->file_name");
        }
            
        return false;
    }
}