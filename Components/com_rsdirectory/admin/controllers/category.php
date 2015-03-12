<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Category controller.
 */
class RSDirectoryControllerCategory extends JControllerForm
{  
    /**
     * Remove the category image Ajax task.
     * 
     * @access public
     */
    public function removeImageAjax()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
        // Get the category model.    
        $model = $this->getModel();
            
        // Get the category id.
        $category_id = JFactory::getApplication()->input->getInt('category_id');
            
        // Remove image.
        if ( $model->removeImage($category_id) )
        {
            echo 1;
        }
            
        // Close the application.
        JFactory::getApplication()->close();
    }
}