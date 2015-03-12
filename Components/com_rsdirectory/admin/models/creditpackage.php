<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The Credit Package model.
 */
class RSDirectoryModelCreditPackage extends JModelAdmin
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
    public function getTable( $type = 'CreditPackage', $prefix = 'RSDirectoryTable', $config = array() )
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
        $form = $this->loadForm( 'com_rsdirectory.creditpackage', 'creditpackage', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.creditpackage.data');
            
        return $data ? $data : $this->getItem();
    }
        
    /**
     * Save the field.
     *
     * @access public
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function save($data)
    {
        if ( empty($data['id']) )
        {
            $db = JFactory::getDbo();
                
            $query = $db->getQuery(true)
                   ->select( 'MAX(' . $db->qn('ordering') . ')' )
                   ->from( $db->qn('#__rsdirectory_credit_packages') );
                   
            $db->setQuery($query);
                
            $data['ordering'] = (int)$db->loadResult() + 1;
        }
            
        $return = parent::save($data);
            
        // Clean the session data.
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.creditpackage.data', null);
            
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
            
        return new RSTabs('com-rsdirectory-creditpackage');
    }
}