<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Group model.
 */
class RSDirectoryModelGroup extends JModelAdmin
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
    public function getTable( $type = 'Group', $prefix = 'RSDirectoryTable', $config = array() )
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
        $form = $this->loadForm( 'com_rsdirectory.group', 'group', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.group.data');
            
        return $data ? $data : $this->getItem();
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
        $pk = $pk ? $pk : (int) $this->getState( $this->getName() . '.id' );
            
        $item = parent::getItem($pk);
            
        if ($pk)
        {
            // Get DBO.
            $db = JFactory::getDbo();
                
            $query = $db->getQuery(true)
                   ->select( $db->qn('jgroup_id') )
                   ->from( $db->qn('#__rsdirectory_groups_relations') )
                   ->where( $db->qn('group_id') . ' = ' . $db->q($pk) );
                  
            $db->setQuery($query);
                
            // Get the jgroups.
            $item->jgroups = $db->loadColumn();
        }
            
        return $item;
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
            
        if ( !empty($data['jgroups']) )
        {
            foreach ($data['jgroups'] as $jgroup)
            {
                if ( !is_numeric($jgroup) || !$jgroup)
                {
                    $this->setError( JText::_('COM_RSDIRECTORY_INVALID_JGROUP_PROVIDED') );
                    return false;
                }
            }
        }
        else
        {
            $this->setError( JText::_('COM_RSDIRECTORY_SELECT_JGROUP') );
            return false;
        }
			
        return $return;
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
            
        parent::save($data);
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get the group id.
        $id = $this->getState( $this->getName() . '.id' );
            
            
        // Delete all the previously assigned jgroups.
        $query = $db->getQuery(true)
               ->delete( $db->qn('#__rsdirectory_groups_relations') )
               ->where( $db->qn('group_id') . ' = ' . $db->q($id) );
              
        $db->setQuery($query);
        $db->execute();
            
        if ($data['jgroups'])
        {
            // Insert the new jgroups.
            $query = $db->getQuery(true)
                   ->insert( $db->qn('#__rsdirectory_groups_relations') )
                   ->columns( $db->qn( array('group_id', 'jgroup_id') ) );
                  
            foreach ($data['jgroups'] as $jgroup)
            {
                $values = array(
                    $db->q($id),
                    $db->q($jgroup),
                );
                    
                $query->values( implode(',', $values) );
            }
                
            $db->setQuery($query);
            $db->execute();
        }
            
        // Clean the session data.
        JFactory::getApplication()->setUserState('com_rsdirectory.edit.group.data', null);
            
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
}