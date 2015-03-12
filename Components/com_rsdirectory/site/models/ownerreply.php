<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Owner reply model.
 */
class RSDirectoryModelOwnerReply extends JModelAdmin
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
    public function getTable( $type = 'Review', $prefix = 'RSDirectoryTable', $config = array() )
    {
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
			
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
        $form = $this->loadForm( 'com_rsdirectory.ownerreply', 'ownerreply', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        return (object)array( 'message' => $this->getItem() );
    }
        
    /**
     * Method to get a single record.
     *
     * @access public
     *
     * @param int $pk The id of the primary key.
     *
     * @return mixed Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        // Get the id of the primary key.
        $pk = $pk ? $pk : JFactory::getApplication()->input->getInt('review_id');
			
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->select( $db->qn('owner_reply') )
			   ->from( $db->qn('#__rsdirectory_reviews', 'r') )
			   ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
			   ->where( $db->qn('r.id') . ' = ' . $db->q($pk) )
			   ->where( $db->qn('e.user_id') . ' = ' . $db->q( JFactory::getUser()->id ) );
			   
		$db->setQuery($query);
			
        return $db->loadResult();
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
        // Exit the function if the data array is invalid.
        if (!$data)
            return false;
			
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
		       ->update( $db->qn('#__rsdirectory_reviews', 'r') )
			   ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
			   ->set( $db->qn('owner_reply') . ' = ' . $db->q($data['message']) )
			   ->where( $db->qn('r.id') . ' = ' . $db->q( JFactory::getApplication()->input->getInt('review_id') ) );
				
		$db->setQuery($query);
            
        return $db->execute();
    }
}