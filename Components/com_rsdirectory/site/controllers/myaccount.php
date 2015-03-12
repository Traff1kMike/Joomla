<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * My Account controller.
 */
class RSDirectoryControllerMyAccount extends JControllerForm
{
	/**
     * Method to save a record.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
     * @param string $urlVar The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return bool True if successful, false otherwise.
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
			
		// Get the mainframe.
        $app = JFactory::getApplication();
			
		 // Get the data.
        $data = $app->input->get( 'jform', array(), 'array' );
			
		// Get the Entry model.
        $model = $this->getModel();
            
        // Get the form.
        $form = $model->getForm();
            
        // Validate the posted data.
        $return = $model->validate($form, $data);
            
        // Check for validation errors.
        if ($return === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();
                
            // Push up to three validation messages out to the user.
            for ( $i = 0, $n = count($errors); $i < $n && $i < 3; $i++ )
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage( $errors[$i]->getMessage(), 'warning' );
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }
                
            // Save the data in the session.
            $app->setUserState('com_rsdirectory.edit.user.data', $data);
                
            // Redirect back to the edit screen.
            $app->redirect( JUri::getInstance()->toString() );
        }
			
		// Attempt to save the configuration.
        $data = $return;
        $return = $model->save($data);
            
            
        // Check the return value.
        if ($return === false)
        {
            // Save the data in the session.
            $app->setUserState('com_rsdirectory.edit.user.data', $data);
                
            $message = JText::sprintf( 'COM_RSDIRECTORY_SAVE_FAILED', $model->getError() );
                
            // Redirect back to the edit screen.
            $app->enqueueMessage($message, 'error');
            $app->redirect( JUri::getInstance()->toString() );
        }
			
		$app->enqueueMessage( JText::_('COM_RSDIRECTORY_ACCOUNT_SETTINGS_SAVED') );
		$app->redirect( JUri::getInstance()->toString() );
			
		return true;
	}
}