<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration controller.
 */
class RSDirectoryControllerConfiguration extends JControllerForm
{
    /**
     * Method to save the configuration.
     *
     * @access public
     *
     * @param string $key The name of the primary key of the URL variable.
	 * @param string $urlVar The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit( JText::_('JINVALID_TOKEN') );
            
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Get the Configuration model.
        $model = $this->getModel('Configuration');
            
        // Get the form.
        $form = $model->getForm();
            
        // Get the data.
        $data = $app->input->get( 'jform', array(), 'array' );
            
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
            $app->setUserState('com_rsdirectory.edit.configuration.data', $data);
                
            // Redirect back to the edit screen.
            $app->redirect( JRoute::_('index.php?option=com_rsdirectory&view=configuration', false) );
        }
            
            
        // Attempt to save the configuration.
        $data = $return;
        $return = $model->save($data);
            
            
        // Check the return value.
        if ($return === false)
        {
            // Save the data in the session.
            $app->setUserState('com_rsdirectory.edit.configuration.data', $data);
                
            $message = JText::sprintf( 'JERROR_SAVE_FAILED', $model->getError() );
                
            // Redirect back to the edit screen.
			$app->enqueueMessage($message, 'error');
            $app->redirect( JRoute::_('index.php?option=com_rsdirectory&view=configuration', false) );
        }
            
            
        // Redirect.
        if ( $this->getTask() == 'apply' )
        {
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_CONFIGURATION_SAVED') );
            $app->redirect( JRoute::_('index.php?option=com_rsdirectory&view=configuration', false) );
        }
        else
        {
			$app->enqueueMessage( JText::_('COM_RSDIRECTORY_CONFIGURATION_SAVED') );
            $app->redirect( JRoute::_('index.php?option=com_rsdirectory', false) );
        }
            
        return true;
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
        // Clean the session data.
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.configuration.data', null);
            
        // Redirect to the dashboard.
        $this->setRedirect('index.php?option=com_rsdirectory');
    }
}