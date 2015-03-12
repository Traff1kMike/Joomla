<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * The Form model.
 */
class RSDirectoryModelForm extends JModelAdmin
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
    public function getTable( $type = 'Form', $prefix = 'RSDirectoryTable', $config = array() )
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
        $form = $this->loadForm( 'com_rsdirectory.form', 'form', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.form.data');
            
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
                
            // Get the form fields ids.
            $query = $db->getQuery(true)
                   ->select( $db->qn('field_id') )
                   ->from( $db->qn('#__rsdirectory_forms_fields') )
                   ->where( $db->qn('form_id') . ' = ' . $db->q($pk) );
                  
            $db->setQuery($query);
                
            $item->form_fields = $db->loadColumn();
                
            // Get the custom form fields ids.
            $query = $db->getQuery(true)
                   ->select( $db->qn('field_id') )
                   ->from( $db->qn('#__rsdirectory_forms_custom_fields') )
                   ->where( $db->qn('form_id') . ' = ' . $db->q($pk) );
                   
            $db->setQuery($query);
                
            $item->listing_detail_custom_fields = $db->loadColumn();
        }
            
        return $item;
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
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get the JInput object.
        $jinput = $app->input;
            
        // Get the field id.
        $id = $jinput->getInt('id');
          
          
        // Get the form table.
        $form = $this->getTable();
            
            
        if ( $id && $jinput->get('task') != 'save2copy' )
        {
            // Load field object.
            $form->load($id);
                
            // Delete all the form fields for this form.
            $query = $db->getQuery(true)
                   ->delete( $db->qn('#__rsdirectory_forms_fields') )
                   ->where( $db->qn('form_id') . ' = ' . $db->q($id) );
                   
            $db->setQuery($query);
            $db->execute();
                
            // Delete all the custom form fields for this form.
            $query = $db->getQuery(true)
                   ->delete( $db->qn('#__rsdirectory_forms_custom_fields') )
                   ->where( $db->qn('form_id') . ' = ' . $db->q($id) );
                   
            $db->setQuery($query);
            $db->execute();
        }
            
            
        $form->save($data);
            
        $id = $form->id;
            
        // Remember the id of the newly inserted form.
        $this->setState( $this->getName() . '.id', $id );
           
        if ( empty($data['form_fields']) )
        {
            $data['form_fields'] = array();
        }
            
        // Add the all_forms fields to the form fields array.
        $query = $db->getQuery(true)
               ->select( $db->qn('f.id') )
               ->from( $db->qn('#__rsdirectory_fields', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_field_types', 'ft') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ft.id') )
               ->where( $db->qn('ft.all_forms') . ' = ' . $db->q(1) );
               
        $db->setQuery($query);
            
        if ( $all_forms_field_ids = $db->loadColumn() )
        {
            $data['form_fields'] = array_merge($data['form_fields'], $all_forms_field_ids);
        }
            
        if ( !empty($data['form_fields']) )
        {
            $data['form_fields'] = array_unique($data['form_fields']);
                
            // Insert columns.
            $columns = array('form_id', 'field_id', 'ordering');
                
            // Insert the form fields in the database.
            $query = $db->getQuery(true)
                   ->insert( $db->qn('#__rsdirectory_forms_fields') )
                   ->columns( $db->qn($columns) );
                    
            foreach ($data['form_fields'] as $i => $field_id)
            {
                $values = array(
                    $db->q($id),
                    $db->q($field_id),
                    $db->q($i + 1),
                );
                    
                $query->values( implode(',', $values) );
            }
                
            $db->setQuery($query);
            $db->execute();
        }
            
        if ( !empty($data['listing_detail_custom_fields']) )
        {
            $data['listing_detail_custom_fields'] = array_unique($data['listing_detail_custom_fields']);
                
            // Insert columns.
            $columns = array('form_id', 'field_id');
                
            // Insert the custom form fields in the database.
            $query = $db->getQuery(true)
                   ->insert( $db->qn('#__rsdirectory_forms_custom_fields') )
                   ->columns( $db->qn($columns) );
                    
            foreach ($data['listing_detail_custom_fields'] as $field_id)
            {
                $values = array(
                    $db->q($id),
                    $db->q($field_id),
                );
                    
                $query->values( implode(',', $values) );
            }
                
            $db->setQuery($query);
            $db->execute();
        }
            
        // Clean the session data.
        $app->setUserState('com_rsdirectory.edit.form.data', null);
            
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
            
        return new RSTabs('com-rsdirectory-form');
    }
}