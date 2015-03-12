<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Contact model.
 */
class RSDirectoryModelContact extends JModelAdmin
{
	/**
	 * The entry from which the contact form was sent.
	 *
	 * @access protected
	 *
	 * @var object
	 */
	protected $entry;
		
	/**
	 * Submitted data.
	 *
	 * @access protected
	 *
	 * @var object
	 */
	protected $data;
		
	/**
     * Method for getting the form from the model.
     *
     * @access public
     * 
     * @param array $data
     * @param bool $loadData
     * 
     * @return mixed
     */
    public function getForm( $data = array(), $loadData = true )
    {
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.contact', 'contact', array('control' => 'jform', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
		
	/**
     * Method to get the data that should be injected in the form.
     *
     * @access protected
     * 
     * @return array
     */
    protected function loadFormData()
    {
        return JFactory::getApplication()->getUserState('com_rsdirectory.edit.contact.data');
    }
		
	/**
     * Validate form data.
     *
     * @access public
     * 
     * @param object $form The form to validate against.
     * @param array $data The data to validate.
     * @param string $group The name of the field group to validate.
     * 
     * @return mixed
     */
    public function validate($form, $data, $group = null)
    {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
			
		if ($user->id)
		{
			$data['name'] = $user->name;
			$data['email'] = $user->email;
		}
			
        $return = parent::validate($form, $data, $group);
			
		$entry_id = $app->input->getInt('entry_id');
			
		if ( !$entry_id || !( $this->entry = RSDirectoryHelper::getEntry($entry_id) ) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_INVALID_ENTRY_SPECIFIED') );
            $return = false;
		}
			
		if ( RSDirectoryHelper::checkUserPermission('contact_captcha') )
		{
			$config = RSDirectoryConfig::getInstance();
				
			if ( $config->get('captcha_type') == 'built_in' )
            {
                // Using securimage.
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/securimage/securimage.php';
                    
                // Initialize the CAPTCHA object.
                $captcha = new JSecurImage();
                $captcha->case_sensitive = $config->get('captcha_case_sensitive');
                    
                // Validate the CAPTCHA value.
                if ( empty($data['captcha']) || !$captcha->check( trim($data['captcha']) ) )
                {
                    $this->setError( JText::_('COM_RSDIRECTORY_CAPTCHA_ERROR') );
					$return = false;
                }
            }
            else
            {
                // Using reCAPTCHA.
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/recaptcha/recaptchalib.php';
                    
                $response = RSDirectoryReCAPTCHA::checkAnswer(
                    $config->get('recaptcha_private_key'),
                    isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
                    isset($_POST['recaptcha_challenge_field']) ? $_POST['recaptcha_challenge_field'] : '',
                    isset($_POST['recaptcha_response_field']) ? $_POST['recaptcha_response_field'] : ''
                );
                    
                if ( empty($response->is_valid) )
                {
                    $this->setError( JText::_('COM_RSDIRECTORY_CAPTCHA_ERROR') );
					$return = false;
                }
            }
		}
			
		return $return;
    }
		
	/**
     * Send email.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        // Do a few checks.
        if (!$data || !$this->entry)
            return false;
			
		require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/placeholders.php';
			
		$this->data = (object)$data;
		$this->entry = RSDirectoryHelper::getEntryData($this->entry);
		$form = $this->entry->form;
			
		// Process placeholders.
		$from_name = $this->processPlaceholders($this->entry->form->contact_from_name);
		$from_email = $this->processPlaceholders($this->entry->form->contact_from_email);
		$to_name = $this->processPlaceholders($this->entry->form->contact_to_name);
		$to_email = $this->processPlaceholders($this->entry->form->contact_to_email);
		$cc = $this->processPlaceholders($this->entry->form->contact_cc);
		$bcc = $this->processPlaceholders($this->entry->form->contact_bcc);
		$subject = $this->processPlaceholders($this->entry->form->contact_subject);
		$message = $this->processPlaceholders($this->entry->form->contact_message);
			
		// Get mailer.
        $mailer = JFactory::getMailer();
			
		// Set sender.
		$mailer->setSender( array($from_email, $from_name) );
			
		// Add recipient(s).
		$mailer->addRecipient(
			explode(',', $to_email),
			explode(',', $to_name)
		);
			
		// Set CC.	
		if ($cc)
		{
			$mailer->addCC( explode(',', $cc) );
		}
			
		// Set BCC.
		if ($bcc)
		{
			$mailer->addBCC( explode(',', $bcc) );
		}
			
		// Set subject.
		$mailer->setSubject($subject);
			
		// Set body.
		$mailer->setBody($message);
			
		// Send as HTML.
		$mailer->isHtml($this->entry->form->contact_send_html);
			
		if ( !empty($mailer->ErrorInfo) )
		{
			$this->setError($mailer->ErrorInfo);
			return false;
		}
			
		// Unset the contact form data.	
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.contact.data', null);
			
		return $mailer->Send();
	}
		
	/**
	 * Get an array of fields that should be skipped when displaying the form to the current user.
	 *
	 * @access public
	 *
	 * @return array
	 */
	public function getSkippedFields()
	{
		if ( JFactory::getUser()->id )
		{
			return array(
				'name',
				'email',
			);
		}
			
		return array();
	}
		
	/**
	 * Process placeholders.
	 *
	 * @access protected
	 *
	 * @param string $text
	 * 
	 * @return string
	 */
	protected function processPlaceholders($text)
	{
		// Process the regular placeholders.
		$text = RSDirectoryPlaceholders::getInstance($text, $this->entry->form->fields, $this->entry, $this->entry->form)
              ->setParams('email')
              ->process();
			  
		// Process the contact specific placeholders.
		$text = str_replace(
			array(
				'{contact.name}',
				'{contact.email}',
				'{contact.message}'),
			array(
				strip_tags($this->data->name),
				strip_tags($this->data->email),
				strip_tags($this->data->message),
			),
			$text
		);
			
		return $text;
	}
}