<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories Batch controller.
 */
class RSDirectoryControllerCategoriesBatch extends JControllerForm
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
        // Get mainframe.
        $app = JFactory::getApplication();
            
        $return = parent::save($key, $urlVar);
            
        $this->message = JText::plural( 'COM_RSDIRECTORY_CATEGORIES_ADDED', $app->getUserState('com_rsdirectory.edit.categoriesbatch.count') );
            
        if ( $this->getTask() == 'save' )
        {
            $this->redirect = 'index.php?option=com_rsdirectory&view=categories';
        }
            
        $app->setUserState('com_rsdirectory.edit.categoriesbatch.count', null);
            
        return $return;
    }
        
    /**
     * The cancel task.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
     */
    public function cancel($key = null)
    {
        parent::cancel($key);
            
        // Redirect to the categories list.
        $this->redirect = 'index.php?option=com_rsdirectory&view=categories';
    }
}