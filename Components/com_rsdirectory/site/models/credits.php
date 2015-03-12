<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits model.
 */
class RSDirectoryModelCredits extends JModelLegacy
{
    /**
     * An array holding the names of the fields that contain errors.
     * 
     * @var array
     */
    protected $error_fields = array();
        
    /**
     * An array holding the names of the registration fields that contain errors.
     * 
     * @var array
     */
    protected $error_reg_fields = array();
        
    /**
     * Set a field error.
     *
     * @access private
     *
     * @param int $field_id
     * @param string $error_message
     */
    private function setFieldError($field_name, $error_message)
    {
		$this->error_fields[] = $field_name;
		$this->setError($error_message);
    }
		
	/**
     * Set registration field error.
     *
     * @access private
     *
     * @param string $field_name
     * @param string $error_message
     */
    private function setRegError($field_name, $error_message)
    {
        $this->error_reg_fields[] = $field_name;
        $this->setError($error_message);
    }
        
    /**
     * Method to auto-populate the model state. 
     * 
     * @access protected
     */
    protected function populateState()
    {
		// Get mainframe.
		$app = JFactory::getApplication();
			
		$params = $app->getParams();
		$this->setState('params', $params);
			
		$id = $app->getUserState('com_rsdirectory.credits.id');
		$this->setState('id', $id);
    }
        
    /**
     * Validate form data.
     *
     * @access public
     * 
     * @param array $data The data to validate.
     * 
     * @return mixed
     */
    public function validate($data)
    {
		$data = RSDirectoryHelper::trim($data);
		$return = $data;
			
		if ( !empty($data['entry_id']) && !RSDirectoryHelper::getEntry($data['entry_id']) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
			$return = false;
		}
			
		$reg = empty($data['reg']) ? array() : $data['reg'];
			
		$user = JFactory::getUser();
			
		if (!$user->id)
		{
			$user = JFactory::getUser( JFactory::getApplication()->getUserState('com_rsdirectory.registration.user.id') );
				
			if ( isset($reg['email']) && $user->email != $reg['email'])
			{
				$user = null;
			}
		}
			
		if ( empty($user->id) )
		{
            if ( empty($reg['name']) )
            {
                $this->setRegError( 'name', JText::_('COM_RSDIRECTORY_NAME_REQUIRED') );
				$return = false;
            }
            else if ( strlen( trim($reg['name']) ) < 2 )
            {
                $this->setRegError( 'name', JText::_('COM_RSDIRECTORY_NAME_LENGTH_ERROR') );
				$return = false;
            }
                
            if ( empty($reg['email']) )
            {
                $this->setRegError( 'email', JText::_('COM_RSDIRECTORY_EMAIL_REQUIRED') );
				$return = false;
            }
            else if ( !RSDirectoryHelper::email($reg['email']) )
            {
                $this->setRegError( 'email', JText::_('COM_RSDIRECTORY_PROVIDE_VALID_EMAIL') );
				$return = false;
            }
            else if ( RSDirectoryHelper::userExists( array('email' => $reg['email']) ) )
            {
                $this->setRegError( 'email', JText::_('COM_RSDIRECTORY_EMAIL_IN_USE') );
				$return = false;
            }
		}
			
		if ( empty($data['credit_package']) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_SELECT_CREDIT_PACKAGE_ERROR') );
			$return = false;
		}
		else
		{
			$credit_package = RSDirectoryHelper::getCreditPackage($data['credit_package']);
				
			if (
				!( !empty($data['entry_id']) && $data['credit_package'] == 'minimum' && RSDirectoryCredits::getEntryMinimumRequiredCreditPackage($data['entry_id']) ) &&
				!($credit_package && $credit_package->published) )
			{
				$this->setError( JText::_('COM_RSDIRECTORY_CREDIT_PACKAGE_INVALID_SELECTION_ERROR') );
				$return = false;
			}
		}
			
		if ( empty($data['payment_method']) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_SELECT_PAYMENT_METHOD_ERROR') );
			$return = false;
		}
		else
		{
			$payment_methods = RSDirectoryHelper::getPaymentMethods();
				
			$valid = false;
				
			foreach ($payment_methods as $payment_method)
			{
				if ($data['payment_method'] == $payment_method->value)
				{
					$valid = true;
					break;
				}
			}
				
			if (!$valid)
			{
				$this->setError( JText::_('COM_RSDIRECTORY_PAYMENT_METHOD_INVALID_SELECTION_ERROR') );
				$return = false;
			}
		}
			
		// Store the names of the registration fields that contain errors.
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.credits.error_reg_fields' , $this->error_reg_fields);
			
		return $return;
    }
        
    /**
     * Save the transaction.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
		// Exit the function if the data array is invalid.
		if (!$data)
			return false;
			
		// Get mainframe.
		$app = JFactory::getApplication();
			
		$user = JFactory::getUser();
			
		if (!$user->id)
		{
			$user = JFactory::getUser( $app->getUserState('com_rsdirectory.registration.user.id') );
				
			if ( isset($data['reg']['email']) && $user->email != $data['reg']['email'])
			{
				$user = null;
			}
		}
			
		if ( empty($user->id) )
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/registration.php';
				
			$reg = new RSDirectoryRegistration;
			$reg->setData( array(
				'name' => $data['reg']['name'],
				'email' => $data['reg']['email'],
			) );
				
			if ( $reg->register() )
			{
				$user_id = $reg->getUserId();
					
				// Remember the id of the newly registered user.
                $app->setUserState('com_rsdirectory.registration.user.id', $user_id);
			}
			else
			{
				$reg_errors = $reg->getErrors();
					
				if ($reg_errors)
				{
					foreach ($reg_errors as $reg_error)
					{
						$this->setError($reg_error);
					}
				}
				else
				{
					$this->setError( JText::_('COM_RSDIRECTORY_ENTRY_SAVE_ERROR') );
				}
					
				return false;
			}
		}
		// Proceed with the user registration.
		else
		{
			// Get the logged in user id.
			$user_id = $user->id;
		}
			
		// Get DBO.
		$db = JFactory::getDbo();
			
		// Load credit package.
		if ( !empty($data['entry_id']) && $data['credit_package'] == 'minimum' )
		{
			$credit_package = RSDirectoryCredits::getEntryMinimumRequiredCreditPackage($data['entry_id']);
		}
		else
		{
			$credit_package = RSDirectoryHelper::getCreditPackage($data['credit_package']);
		}
			
		// Generate hash.
		$hash = RSDirectoryHelper::getHash();
			
		$vars = array(
			(object)array(
				'method' => $data['payment_method'],
				'price' => $credit_package->price,
			),
		);
			
		$tax_data = JFactory::getApplication()->triggerEvent('rsdirectory_GetTaxData', $vars);
		$tax_data = new JRegistry( isset($tax_data[0]) ? $tax_data[0] : null );	
		$tax = $tax_data->get('tax', 0);
		$total = $credit_package->price + $tax;
			
		$transaction = (object)array(
			'credit_title' => $credit_package->title,
			'user_id' => $user_id,
			'type' => 'purchase',
			'price' => $credit_package->price,
			'tax' => $tax,
			'tax_type' => $tax_data->get('tax_type', ''),
			'tax_value' => $tax_data->get('tax_value', 0),
			'total' => $total,
			'currency' => RSDirectoryConfig::getInstance()->get('currency'),
			'credits' => $credit_package->credits,
			'hash' => $hash,
			'ip' => RSDirectoryHelper::getIp(true),
			'gateway' => $data['payment_method'],
			'date_created' => JFactory::getDate()->toSql(),
		);
			
		if ( !empty($data['entry_id']) )
		{
			$transaction->entry_id = $data['entry_id'];
		}
			
		$db->insertObject('#__rsdirectory_users_transactions', $transaction, 'id');
			
		// Remember the transaction id.
		$app->setUserState('com_rsdirectory.credits.id', $transaction->id);
		$this->setState('id', $transaction->id);
			
		// Remember the transaction payment method.
		$this->setState('payment_method', $data['payment_method']);
			
		// Clean the session data.
		$app->setUserState('com_rsdirectory.credits.data', null);
			
		return true;
    }
		
    /**
     * Process the Authorize.Net form data.
     *
     * @access public
     *
     * @param array $data
     *
     * @return mixed
     */
    public function authorizeNet($data)
    {
		if ( !class_exists('plgSystemRSDirectoryAuthorize') )
		{
			$this->setError( JText::_('COM_RSDIRECTORYAUTHORIZE_NOT_LOADED_ERROR') );
			return false;
		}
			
		$return = $data;
			
		// Load the RSDirectory! Authorize.Net Payment Plugin language file.
		JFactory::getLanguage()->load(plgSystemRSDirectoryAuthorize::EXTENSION, JPATH_ADMINISTRATOR);
		
		// Initialize the error fields array.
		$error_fields = array();
			
		if ( empty($data['cc_number']) || !isset($data['cc_number']{12}) || isset($data['cc_number']{16}) )
		{
			$this->setFieldError( 'cc_number', JText::_('PLG_SYSTEM_RSDIRECTORYAUTHORIZE_VALIDATE_CC_NUMBER') );
			$return = false;
		}
			
		// Validate the CCV code.
		if ( empty($data['cc_csc']) || !isset($data['cc_csc']{2}) || isset($data['cc_csc']{4}) )
		{
			$this->setFieldError( 'cc_csc', JText::_('PLG_SYSTEM_RSDIRECTORYAUTHORIZE_VALIDATE_CC_CCV') );
			$return = false;
		}
			
		// Validate the expiration date.
		if ( empty($data['cc_exp_m']) || empty($data['cc_exp_y']) )
		{
			$this->setFieldError( 'cc_exp_m', JText::_('PLG_SYSTEM_RSDIRECTORYAUTHORIZE_VALIDATE_CC_EXP') );
			$return = false;
		}
			
		// Validate the first name.
		if ( empty($data['firstname']) )
		{
			$this->setFieldError( 'firstname', JText::_('PLG_SYSTEM_RSDIRECTORYAUTHORIZE_VALIDATE_FIRST_NAME') );
			$return = false;
		}
			
		// Validate the first name.
		if ( empty($data['lastname']) )
		{
			$this->setFieldError( 'lastname', JText::_('PLG_SYSTEM_RSDIRECTORYAUTHORIZE_VALIDATE_LAST_NAME') );
			$return = false;
		}
			
		// Store the names of the fields that contain errors.
		JFactory::getApplication()->setUserState('com_rsdirectory.authorizenet.error_fields' , $this->error_fields);
			
		return $return;
    }
		
	/**
	 * Method to calculate tax and total.
	 *
	 * @access public
	 *
	 * @param mixed $credit_package
	 * @param string $payment_method
	 * @param int $entry_id
	 */
	public function calculatePrice($credit_package, $payment_method, $entry_id)
	{
		// Load credit package.
		if ( !empty($entry_id) && $credit_package == 'minimum' )
		{
			$credit_package = RSDirectoryCredits::getEntryMinimumRequiredCreditPackage($entry_id);
		}
		else
		{
			$credit_package = RSDirectoryHelper::getCreditPackage($credit_package);
		}
			
		if (!$credit_package)
		{
			return (object)array(
				'price' => RSDirectoryHelper::formatPrice(0),
				'tax' => RSDirectoryHelper::formatPrice(0),
				'total' => RSDirectoryHelper::formatPrice(0),
			);
		}
			
		$vars = array(
			(object)array(
				'method' => $payment_method,
				'price' => $credit_package->price,
			),
		);
			
		$tax_data = JFactory::getApplication()->triggerEvent('rsdirectory_GetTaxData', $vars);
		$tax_data = new JRegistry( isset($tax_data[0]) ? $tax_data[0] : null );
		$tax = $tax_data->get('tax', 0);
			
		return (object)array(
			'price' => RSDirectoryHelper::formatPrice($credit_package->price),
			'tax' => RSDirectoryHelper::formatPrice($tax),
			'total' => RSDirectoryHelper::formatPrice($credit_package->price + $tax),
		);
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
