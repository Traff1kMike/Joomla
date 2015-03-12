<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Email Message model.
 */
class RSDirectoryModelEmailMessage extends JModelAdmin
{
    /**
     * Method to get a table object, load it if necessary.
     * 
     * @access public
     * 
     * @param string $type
     * @param string $prefix
     * @param array $config
     * 
     * @return object
     */
    public function getTable( $type = 'EmailMessage', $prefix = 'RSDirectoryTable', $config = array() )
    {
        return JTable::getInstance($type, $prefix, $config);
    }
        
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
        $form = $this->loadForm( 'com_rsdirectory.emailmessage', 'emailmessage', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        // Check for data in the session.
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.emailmessage.data');
            
        return $data ? $data : $this->getItem();
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
        $return = parent::validate($form, $data, $group);
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        // Get query.
        $query = $db->getQuery(true)
               ->select( $db->qn('id') )
               ->from( $db->qn('#__rsdirectory_email_messages') )
               ->where( $db->qn('type') . ' = ' . $db->q($return['type']) . ' AND ' . $db->qn('category_id') . ' = ' . $db->q($return['category_id']) . ' AND ' . $db->qn('id') . ' != ' . $db->q($data['id']) );
            
            
        $db->setQuery($query);
            
        if ( $db->loadResult() )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_DUPLICATE_EMAIL_MESSAGES_ERROR') );
            $return = false;
        }
            
        if ( !empty($data['type']) && $data['type'] == 'entry_expiration_notice' && empty($data['entry_expiration_period']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_EMAIL_MESSAGES_ENTRY_EXPIRATION_PERIOD_ERROR') );
            $return = false;
        }
            
        $fields = array(
            'to' => JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_TO_LABEL'),
            'bcc' => JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_BCC_LABEL'),
            'cc' => JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_CC_LABEL'),
        );
        
        foreach ($fields as $field => $label)
        {
            if ( !empty($data[$field]) )
            {
                $emails = explode(',', $data[$field]);
                    
                if ($emails)
                {
                    foreach ($emails as &$email)
                    {
                        $email = trim($email);
                            
                        if ( !RSDirectoryHelper::email($email) )
                        {
                            $this->setError( JText::sprintf('COM_RSDIRECTORY_EMAIL_FIELDS_INVALID_EMAIL_MESSAGE', $label) );
                            $return = false;
                            continue 2;
                        }
                    }
                }
            }
        }
            
        return $return;
    }
        
    /**
     * Save the email message.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        if ( JFactory::getApplication()->input->get('task') == 'save2copy' )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_OPERATION_NOT_ALLOWED') );
            return false;
        }
            
        $return = parent::save($data);
            
        return $return;
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