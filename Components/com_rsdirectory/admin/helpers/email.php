<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 

/**
 * Email sender class.
 */
class RSDirectoryEmail
{
    /**
     * The email type.
     *
     * @var string
     * 
     * @access protected
     */
    protected $email_type;
        
    /**
     * Form fields.
     *
     * @var array
     * 
     * @access protected
     */
    protected $form_fields;
        
    /**
     * Entry data.
     *
     * @var object
     * 
     * @access protected
     */
    protected $entry;
        
    /**
     * Parent form.
     *
     * @var object
     *
     * @access protected
     */
    protected $form;
        
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param string $email_type
     * @param array $form_fields
     * @param object $entry
     * @param object $form
     */
    public function __construct($email_type, $form_fields, $entry, $form)
    {
        $this->email_type = $email_type;
        $this->form_fields = $form_fields;
        $this->entry = $entry;
        $this->form = $form;
    }
        
    /**
     * Get RSDirectoryEmail instance.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $email_type
     * @param array $form_fields
     * @param object $entry
     * @param object $form
     * 
     * @return RSDirectoryEmail
     */
    public static function getInstance($email_type, $form_fields, $entry, $form)
    {
        $rsdirectoryemail = new RSDirectoryEmail($email_type, $form_fields, $entry, $form);
            
        return $rsdirectoryemail;
    }
        
    /**
     * Get the email based on the entry's parent category and email type.
     *
     * @access private
     *
     * @return mixed
     */
    private function getEmail()
    {
        // Initialize the category id.
        $category_id = $this->entry->category_id;
            
        if (!$category_id)
            return false;
            
        static $categories;
        static $emails;
            
        $email_type = $this->email_type;
            
        if ( empty($categories) )
        {
            $categories = RSDirectoryHelper::getCategories();
        }
            
        if ( empty($emails) )
        {
            // Get DBO.
            $db = JFactory::getDbo();
                
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_email_messages') )
                   ->where( $db->qn('published') . ' = ' . $db->q(1) );
                   
            $db->setQuery($query);
                
            $emails = $db->loadObjectList();
        }
            
        // Get the category hierarchy.
        $hierarchy = array_reverse( RSDirectoryHelper::getCategoryHierarchy($category_id, $categories) );
            
        while (true)
        {
            foreach ($emails as $email)
            {
                if ($email_type == $email->type && $email->category_id == $category_id)
                    return $email;
            }
                
            $category = next($hierarchy);
                
            if ($category)
            {
                $category_id = $category->id;
            }
            else
            {
                if (!$category_id)
                    return false;
                    
                $category_id = 0;
            }
        }
    }
        
    /**
     * Process and send email.
     *
     * @access public
     *
     * @return bool
     */
    public function send()
    {
        // Get the email.
        $email = $this->getEmail();
            
        if (!$email)
            return false;
            
        require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/placeholders.php';
            
        // Process placeholders.
		$to_name = $this->processPlaceholders($email->to_name);
        $to_email = $this->processPlaceholders($email->to_email);
		$cc = $this->processPlaceholders($email->cc);
		$bcc = $this->processPlaceholders($email->bcc);
		$subject = $this->processPlaceholders($email->subject);
		$message = $this->processPlaceholders($email->text);
            
        // Get mailer.
        $mailer = JFactory::getMailer();
            
        // Get the RSDirectory! config object.
        $config = RSDirectoryConfig::getInstance();
            
        // Set sender.
		$mailer->setSender( array( $config->get('from_email'), $config->get('from_name') ) );
			
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
		$mailer->isHtml($email->send_html);
            
        return $mailer->Send();
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
		$text = RSDirectoryPlaceholders::getInstance($text, $this->form_fields, $this->entry, $this->form)
              ->setParams('email')
              ->process();
                
		return $text;
	}
}