<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Form controller.
 */
class RSDirectoryControllerForm extends JControllerForm
{
    /**
     * Method to save a record.
     *
     * @access public
     *
     * @return boolean True if successful, false otherwise.
     */
    public function save($key = null, $urlVar = null)
    {
        $return = parent::save($key, $urlVar);
            
        $category_id = JFactory::getApplication()->input->getInt('category_id');
            
        if ( $category_id && $this->getTask() == 'save' )
        {
            $this->redirect = "index.php?option=com_rsdirectory&task=category.edit&id=$category_id";
        }
            
        return $return;
    }
        
    /**
     * The cancel task.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
     *
     * @return boolean True if access level checks pass, false otherwise.
     */
    public function cancel($key = null)
    {
        $return = parent::cancel($key);
            
        $category_id = JFactory::getApplication()->input->getInt('category_id');
            
        if ($category_id)
        {
            $this->setRedirect("index.php?option=com_rsdirectory&task=category.edit&id=$category_id");
        }
            
        return $return;
    }
        
    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @access protected
     * 
     * @param int $recordId
     * @param string $urlVar
     * 
     * @return string
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);
            
        // Get the category id.
        $category_id = JFactory::getApplication()->input->getInt('category_id');
            
        // Get the task.
        $task = $this->getTask();
            
        if ( $category_id && in_array( $task, array('add', 'apply', 'save') ) )
        {
            $append .= "&category_id=$category_id";
        }
            
        return  $append;
    }
}