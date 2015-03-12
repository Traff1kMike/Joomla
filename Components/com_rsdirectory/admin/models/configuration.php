<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration model.
 */
class RSDirectoryModelConfiguration extends JModelForm
{
    /**
     * Method to get a form object.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure.
     *
     * @since 1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm( 'com_rsdirectory.configuration', 'configuration', array('control' => 'jform', 'load_data' => $loadData) );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
        
    /**
     * Load form data.
     *
     * @access protected
     * 
     * @return array
     */
    protected function loadFormData()
    {
        // Get the mainframe.
        $app = JFactory::getApplication();
            
        // Check for data in the session.
        $data = $app->getUserState('com_rsdirectory.edit.configuration.data');
            
        if ( empty($data) )
        {
            $db = JFactory::getDbo();
                
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_config') );
                   
            $db->setQuery($query);
                
            $rows = $db->loadObjectList();
                
            if ($rows)
            {
                $data = array();
                    
                foreach ($rows as $row)
                {
                    $data[$row->name] = $row->value;
                }
            }
        }
            
        return $data;
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
			
        if ( !$data['use_joomla_email_configuration'] )
        {
            // Validate the from email.
            if ( !$data['from_email'] )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_SPECIFY_FROM_EMAIL') );
                $return = false;
            }
                
            // Validate the from name.
            if ( !$data['from_name'] )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_SPECIFY_FROM_NAME') );
                $return = false;
            }
        }
            
        // Validate the currency.
        $value = preg_replace( '/[^a-zA-Z]/', '', strtoupper($data['currency']) );
            
        if ( strlen($value) != 3 )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_CURRENCY_FORMAT') );
            $return = false;
        }
			
		// Validate the credit cost.
		if ($data['credit_cost'] < 0)
		{
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_CREDIT_COST') );
            $return = false;
		}
			
        if ($data['watermark_images'])
        {
            // Validate the watermark.
            if ( !$data['watermark'] )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_SELECT_WATERMARK') );
                $return = false;
            }
        }
            
        if ( !in_array( $data['watermark_position'], array('TL', 'TR', 'BL', 'BR') ) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_WATERMARK_POSITION') );
            $return = false;
        }
            
        // Validate the watermark opacity.
        $value = $data['watermark_opacity'];
            
        if ($value < 0 || $value > 100)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_WATERMARK_OPACITY') );
            $return = false;
        }
            
        // Validate the watermark opacity.
        $value = $data['watermark_size'];
            
        if ($value < 0 || $value > 100)
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_WATERMARK_SIZE') );
            $return = false;
        }
            
        if ($data['captcha_type'] == 'recaptcha')
        {
            // Validate the reCAPTCHA public key.
            if ( !trim($data['recaptcha_public_key']) )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_SPECIFY_RECAPTCHA_PUBLIC_KEY') );
                $return = false;
            }
                
            // Validate the reCAPTCHA private key.
            if ( !trim($data['recaptcha_private_key']) )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_SPECIFY_RECAPTCHA_PRIVATE_KEY') );
                $return = false;
            }
        }
            
        if ( $data['enable_comments'] && $data['commenting_system'] == 'disqus' && !trim($data['disqus_short_name']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_SPECIFY_DISQUS_SHORT_NAME') );
            $return = false;
        }
		
		if ( $data['reporting_show_reason_dropdown'] && !trim($data['reporting_reasons']) )
		{
			$this->setError( JText::_('COM_RSDIRECTORY_REASONS_ERROR') );
            $return = false;
		}
			
            
        // Validate the code.
        $value = trim($data['code']);
            
        if ( $value !== '' && strlen($value) != 20 )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_CODE') );
            $return = false;
        }
            
        return $return;
    }
        
    /**
     * Save the configuration.
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
            
        $config = RSDirectoryConfig::getInstance();
            
        foreach ($data as $key => $value)
        {
            $config->set($key, $value);
        }
            
        // Clean the session data.
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.configuration.data', null);
           
        return true;
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
        
    /**
     * Get RSTabs.
     *
     * @access public
     * 
     * @return RSTabs
     */
    public function getRSTabs()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/tabs.php';
            
        return new RSTabs('com-rsdirectory-configuration');
    }
        
    /**
     * Get sidebar.
     *
     * @access public
     * 
     * @return string
     */
    public function getSideBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
            
        return RSDirectoryToolbarHelper::render();
    }
}