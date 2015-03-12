<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits controller.
 */
class RSDirectoryControllerCredits extends JControllerLegacy
{         
    /**
     * Process the submitted data for purchase.
     *
     * @access public
     */
    public function purchase()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get the credits model.
		$model = $this->getModel('Credits');
			
		// Get the data.
		$data = $app->input->get( 'jform', array(), 'array' );
			
		// Validate the posted data.
		$return = $model->validate($data);
			
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
			$app->setUserState('com_rsdirectory.credits.data', $data);
				
			$app->redirect( JUri::getInstance()->toString() );
		}
		
		// Attempt to save the transaction.
		$data = $return;
		$return = $model->save($data);
			
		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_rsdirectory.credits.data', $data);
				
			$message = JText::sprintf( 'JERROR_SAVE_FAILED', $model->getError() );
				
			$app->enqueueMessage($message, 'error');
			$app->redirect( JUri::getInstance()->toString() );
		}
			
		if ( $model->getState('payment_method') == 'wiretransfer' )
		{                   
			$app->redirect( RSDirectoryRoute::getURL('credits', '', 'wiretransfer=1&id=' . $model->getState('id'), false, false ) );
		}
			
		$app->redirect( JUri::getInstance()->toString() );
    }
        
    /**
     * Process the Authorize.Net form data.
     *
     * @access public
     */
    public function authorizeNet()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get the credits model.
		$model = $this->getModel('Credits');
			
		// Get data.
		$data = $app->input->get( 'jform', array(), 'array' );
			
		// Process the Authorize.Net form data.
		$return = $model->authorizeNet($data);
			
		// Save the data in the session.
		$app->setUserState('com_rsdirectory.authorizenet.data', $data);
			
		// Save the user transaction id in the session.
		$app->setUserState( 'com_rsdirectory.credits.id', empty($data['user_transaction_id']) ? null : $data['user_transaction_id'] );
			
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
				
			$app->redirect( JUri::getInstance()->toString() );
		}
			
		// Triger the RSDirectory! Authorize.Net Plugin form processing function.
		$app->triggerEvent( 'RSDIRECTORYAUTHORIZEProcessForm', array( (object)$return ) );
    }
		
	/**
	 * Method to calculate tax and total.
	 *
	 * @access public
	 */
	public function calculatePriceAjax()
	{
		// Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
			
		// Get mainframe.
		$app = JFactory::getApplication();
			
		$credit_package = $app->input->get('credit_package');
		$payment_method = $app->input->get('payment_method');
		$entry_id = $app->input->getInt('entry_id');
			
		// Get the credits model.
		$model = $this->getModel('Credits');
			
		$response = $model->calculatePrice($credit_package, $payment_method, $entry_id);
			
		echo json_encode($response);
			
		$app->close();
	}
}