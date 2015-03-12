<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry Report model.
 */
class RSDirectoryModelEntryReport extends JModelAdmin
{
    /**
     * Model context string.
     *
     * @var string
     */
    protected $_context = 'com_rsdirectory.entryreport';
		
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
        $form = $this->loadForm( 'com_rsdirectory.entryreport', 'entryreport', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        return JFactory::getApplication()->getUserState('com_rsdirectory.edit.entryreport.data');
    }
        
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
    public function getTable( $type = 'ReportedEntry', $prefix = 'RSDirectoryTable', $config = array() )
    {
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
			
		return JTable::getInstance($type, $prefix, $config);
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
			
		$return = $data;
			
		if ( empty($data['entry_id']) || !( $this->entry = RSDirectoryHelper::getEntry($data['entry_id']) ) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_INVALID_ENTRY_SPECIFIED') );
            $return = false;
		}
			
		$config = RSDirectoryConfig::getInstance();
			
		// Sanitize data.
		foreach ($data as &$value)
		{
			$value = trim($value);    
		}
			
		$user = JFactory::getUser();
			
		if ( empty($user->id) )
		{
			// Validate the name.
			if ( empty($data['name']) )
			{
				$this->setError( JText::_('COM_RSDIRECTORY_NAME_REQUIRED') );
				$return = false;
			}
				
			// Validate the email address.
			if ( empty($data['email']) )
			{
				$this->setError( JText::_('COM_RSDIRECTORY_EMAIL_REQUIRED') );
				$return = false;
			}
			else if ( !RSDirectoryHelper::email($data['email']) )
			{
				$this->setError( JText::_('COM_RSDIRECTORY_PROVIDE_VALID_EMAIL') );
				$return = false;
			}
		}
			
		// Validate the reason.	
		if ( $config->get('reporting_show_reason_dropdown') )
		{
			if ( empty($data['reason']) )
			{
				$this->setError( JText::_('COM_RSDIRECTORY_REASON_REQUIRED') );
				$return = false;
			}
			else
			{
				$options = RSDirectoryHelper::getOptions( $config->get('reporting_reasons') );
					
				if ( !in_array( $data['reason'], RSDirectoryHelper::getColumn($options, 'value') ) )
				{
					$this->setError( JText::_('COM_RSDIRECTORY_INVALID_REASON') );
					$return = false;
				}
			}
		}
		else if ( $config->get('reporting_show_message_box') && ( empty($data['message']) || !trim($data['message']) ) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_MESSAGE_REQUIRED') );
			$return = false;
		}
			
		return $return;
    }
        
    /**
     * Save the report.
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
			
		// Get the Entry Report table.
		$entryreport = $this->getTable();
			
		$entryreport->user_id = JFactory::getUser()->id;
		$entryreport->ip = RSDirectoryHelper::getIp(true);
		$entryreport->created_time = JFactory::getDate()->toSql();
			
		return $entryreport->save($data);
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
		$config = RSDirectoryConfig::getInstance();
			
		$skipped = array();
			
		if ( JFactory::getUser()->id )
		{
			$skipped[] = 'name';
			$skipped[] = 'email';
		}
			
		if ( !$config->get('reporting_show_reason_dropdown') || !trim( $config->get('reporting_reasons') ) )
		{
			$skipped[] = 'reason';
		}
			
		if ( !$config->get('reporting_show_message_box') )
		{
			$skipped[] = 'message';
		}
			
		return $skipped;
	}
}