<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits view.
 */
class RSDirectoryViewCredits extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
		$app = JFactory::getApplication();
			
		if ( !$app->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway = JFactory::getApplication()->getPathway();
			$pathway->addItem( JText::_('COM_RSDIRECTORY_CREDITS') );
		}
			
		$state = $this->get('State');
			
		// Get the page/component configuration.
		$params = $state->params;
			
		$this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );
		$this->payment_methods = RSDirectoryHelper::getPaymentMethods();
		$this->credit_packages = RSDirectoryHelper::getCreditPackages(1);
		$this->params = $params;
		$this->rsfieldset = $this->get('RSFieldset');
		$this->data = $app->getUserState('com_rsdirectory.credits.data');
			
		$messages_list = array(
			'info' => array(),
			'success' => array(),
			'warning' => array(),
			'error' => array(),
		);
			
		if ( $app->input->getInt('wiretransfer') )
		{
			// Get user transaction.
			$user_transaction = RSDirectoryHelper::getUserTransaction( $app->input->getInt('id') );
				
			$user_id = JFactory::getUser()->id;
			$user_id = empty($user_id) ? $app->getUserState('com_rsdirectory.registration.user.id') : $user_id;
				
			if ( !$user_transaction || $user_transaction->user_id != $user_id || $user_transaction->gateway != 'wiretransfer' )
			{
				JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
			}
		}
		else if ($state->id)
		{
			// Get user transaction.
			$user_transaction = RSDirectoryHelper::getUserTransaction($state->id);
		}
			
		if ( !empty($user_transaction) )
		{
			$this->payment_form_data = array(
				(object)array(
					'method' => $user_transaction->gateway,
					'item_name' => $user_transaction->credit_title,
					'credits' => $user_transaction->credits,
					'currency' => $user_transaction->currency,
					'price' => $user_transaction->price,
					'tax' => $user_transaction->tax,
					'tax_type' => $user_transaction->tax_type,
					'tax_value' => $user_transaction->tax_value,
					'total' => $user_transaction->total,
					'formatted_price' => RSDirectoryHelper::formatPrice($user_transaction->price),
					'hash' => $user_transaction->hash,
					'user_transaction_id' => $state->id,
				),
			);
				
			// Clear the transaction id from the session.
			$app->setUserState('com_rsdirectory.credits.id', null);
		}
			
			
		if ( $entry_id = $app->input->getInt('entry_id') )
		{
			$entry = RSDirectoryHelper::getEntry($entry_id);
				
			if (!$entry)
			{
				JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
			}
				
			$user = JFactory::getUser();
				
			if (!$user->id)
			{
				$user = JFactory::getUser( $app->getUserState('com_rsdirectory.registration.user.id') );
			}
				
			if ($entry->user_id != $user->id)
			{
				JError::raiseError( 500, JText::_('COM_RSDIRECTORY_PERMISSION_DENIED') );
			}
				
			$href = RSDirectoryRoute::getEntryURL($entry_id, $entry->title);
			$link = '<a href="' . $href . '">' . $this->escape($entry->title) . '</a>';
				
			if ($entry->paid)
			{
				$messages_list['info'][] = JText::sprintf('COM_RSDIRECTORY_ENTRY_ALREADY_PAID', $link);
			}
			else
			{
				// Get the entry minimum required credit package.
				$credit_packages = RSDirectoryCredits::getEntryMinimumRequiredCreditPackage($entry_id);
					
				if ($credit_packages)
				{
					// Add it to the beginning of the array.
					array_unshift($this->credit_packages, $credit_packages);
				}
					
				if ( $app->input->get('finalize') )
				{
					$messages_list['success'][] = JText::sprintf('COM_RSDIRECTORY_ENTRY_FINALIZE', $link);
				}
				else if ( empty($user_transaction) )
				{
					$messages_list['success'][] = JText::sprintf('COM_RSDIRECTORY_SAVED_ENTRY_REQUIRES_CREDITS', $link);
				}
					
				$this->entry_summary = RSDirectoryCredits::getEntrySummary($entry_id);
			}
				
			$this->user = $user;
			$this->entry = $entry;
		}
		else
		{
			$this->user = JFactory::getUser();
		}
			
		// Get the names of the registration fields that contain errors.
        $this->error_reg_fields = $app->getUserState('com_rsdirectory.edit.credits.error_reg_fields');
			
		$this->messages_list = $messages_list;
			
		// Call the parent display function.
		parent::display($tpl);
			
		// Clear errors.
		$app->setUserState('com_rsdirectory.edit.credits.error_reg_fields', null);
    }
}