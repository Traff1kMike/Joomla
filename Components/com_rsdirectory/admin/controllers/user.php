<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * User controller.
 */
class RSDirectoryControllerUser extends JControllerForm
{  
    /**
	 * Method to save a record.
	 *
	 * @param string $key The name of the primary key of the URL variable.
	 * @param string $urlVar The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return boolean True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
            
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$model = $this->getModel();
		$data = $app->input->post->get('jform', array(), 'array');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();
            
		// Determine the name of the primary key for the data.
		if ( empty($key) )
		{
			$key = 'id';
		}
            
		// To avoid data collisions the urlVar may be different from the primary key.
		if ( empty($urlVar) )
		{
			$urlVar = $key;
		}
            
		$recordId = $app->input->getInt($urlVar);
            
		if ( !$this->checkEditId($context, $recordId) )
		{
			// Somehow the person just went to the form and tried to save it. We don't allow that.
			$this->setError( JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId) );
			$this->setMessage( $this->getError(), 'error' );
                
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
                
			return false;
		}
            
		// Populate the row id from the session.
		$data[$key] = $recordId;
            
		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Reset the ID and then treat the request as for Apply.
			$data[$key] = 0;
			$task = 'apply';
		}
            
		// Access check.
		if ( !$this->allowSave($data, $key) )
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage( $this->getError(), 'error' );
                
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
                
			return false;
		}
            
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);
            
		if (!$form)
		{
			$app->enqueueMessage( $model->getError(), 'error' );
                
			return false;
		}
            
		// Test whether the data is valid.
		$validData = $model->validate($form, $data);
            
		// Check for validation errors.
		if ($validData === false)
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
			$app->setUserState($context . '.data', $data);
                
			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
                
			return false;
		}
            
		// Attempt to save the data.
		if ( !$model->save($validData) )
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);
                
			// Redirect back to the edit screen.
			$this->setError( JText::sprintf( 'JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError() ) );
			$this->setMessage( $this->getError(), 'error' );
                
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
                
			return false;
		}
            
		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ( $recordId == 0 && $app->isSite() ? '_SUBMIT' : '' ) . '_SAVE_SUCCESS')
					? $this->text_prefix
					: 'JLIB_APPLICATION') . ( $recordId == 0 && $app->isSite() ? '_SUBMIT' : '' ) . '_SAVE_SUCCESS'
			)
		);
            
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
                    
				// Set the record data in the session.
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
                    
				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
                    
				break;
                    
			case 'save2new':
                    
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
                    
				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend(null, $urlVar), false
					)
				);
                    
				break;
                    
			default:
                    
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
                    
				// Redirect to the list screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
                    
				break;
		}
            
		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);
            
		return true;
	}
        
    /**
	 * Method to edit an existing record.
	 *
	 * @param string $key The name of the primary key of the URL variable.
	 * @param string $urlVar The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return boolean True if access level check and checkout passes, false otherwise.
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$cid = $app->input->post->get( 'cid', array(), 'array' );
		$context = "$this->option.edit.$this->context";
            
		// Determine the name of the primary key for the data.
		if ( empty($key) )
		{
			$key = 'id';
		}
            
		// To avoid data collisions the urlVar may be different from the primary key.
		if ( empty($urlVar) )
		{
			$urlVar = $key;
		}
            
		// Get the previous record id (if any) and the current record id.
		$recordId = (int)( count($cid) ? $cid[0] : $app->input->getInt($urlVar) );
            
		// Access check.
		if ( !$this->allowEdit( array($key => $recordId), $key ) )
		{
			$this->setError( JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED') );
			$this->setMessage( $this->getError(), 'error' );
                
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
                
			return false;
		}
            
		// Check-out succeeded, push the new record id into the session.
        $this->holdEditId($context, $recordId);
        $app->setUserState($context . '.data', null);
            
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($recordId, $urlVar), false
            )
        );
            
        return true;
	}
        
    /**
	 * Method to cancel an edit.
	 *
	 * @param string $key The name of the primary key of the URL variable.
	 *
	 * @return boolean True if access level checks pass, false otherwise.
	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
            
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$context = "$this->option.edit.$this->context";
            
		if ( empty($key) )
		{
			$key = 'id';
		}
            
		$recordId = $app->input->getInt($key);
            
		// Attempt to check-in the current record.
		if ($recordId)
		{
			// Check we are holding the id in the edit list.
			if ( !$this->checkEditId($context, $recordId) )
			{
				// Somehow the person just went to the form - we don't allow that.
				$this->setError( JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId) );
				$this->setMessage( $this->getError(), 'error' );
                    
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
                    
				return false;
			}
		}
            
		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);
            
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);
            
		return true;
	}
}