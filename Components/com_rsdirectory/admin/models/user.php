<?php
/**
 * @package RDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com 
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * User model.
 */
class RSDirectoryModelUser extends JModelAdmin
{        
    /**
	 * Stock method to auto-populate the model state.
	 *
	 * @access protected
	 */
	protected function populateState()
	{
		// Get the pk of the record from the request.
		$pk = JFactory::getApplication()->input->getInt('id');
		$this->setState( $this->getName() . '.id', $pk );
            
		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
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
        $form = $this->loadForm( 'com_rsdirectory.user', 'user', array('control' => 'jform', 'load_data' => $loadData) );
            
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
        $data = JFactory::getApplication()->getUserState('com_rsdirectory.edit.user.data');
            
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
            
        // Get DBO.
        $db = JFactory::getDBO();
            
        $select = array(
            $db->qn('u.id'),
            $db->qn('u.id', 'user_id'),
            $db->qn('u.name'),
            $db->qn('u.username'),
            $db->qn('uc.credits'),
			$db->qn('uc.unlimited_credits'),
			$db->qn('uc.enable_contact_form'),
            $db->qn('ec.spent_credits'),
            $db->qn('e.entries_count'),
            $db->qn('rev.reviews_count'),
            $db->qn('rep.reports_count'),
			$db->qn('t.transactions_count'),
        );
           
        // Create the entries subquery. 
        $subquery1 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('entries_count') )
                   ->from( $db->qn('#__rsdirectory_entries') )
                   ->group( $db->qn('user_id') );
                    
        // Create the entries credits subquery.
        $subquery2 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', SUM(' . $db->qn('credits') . ') AS ' . $db->qn('spent_credits') )
                   ->from( $db->qn('#__rsdirectory_entries_credits') )
                   ->where( $db->qn('free') . ' = ' . $db->q(0) )
				   ->where( $db->qn('paid') . ' = ' . $db->q(1) )
                   ->group( $db->qn('user_id') );
                   
        // Create the reviews subquery.
        $subquery3 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('reviews_count') )
                   ->from( $db->qn('#__rsdirectory_reviews') )
                   ->group( $db->qn('user_id') );
                   
        // Create the reports subquery.
        $subquery4 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('reports_count') )
                   ->from( $db->qn('#__rsdirectory_entries_reported') )
                   ->group( $db->qn('user_id') );
                   
        // Create the transactions subquery.
        $subquery5 = $db->getQuery(true)
                   ->select( $db->qn('user_id') . ', COUNT(*) AS ' . $db->qn('transactions_count') )
                   ->from( $db->qn('#__rsdirectory_users_transactions') )
                   ->group( $db->qn('user_id') );
					
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__users', 'u') )
               ->leftJoin( $db->qn('#__rsdirectory_users', 'uc') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('uc.user_id') )
               ->leftJoin( '(' . $subquery1 . ') AS ' . $db->qn('e') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('e.user_id') )
               ->leftJoin( '(' . $subquery2 . ') AS ' . $db->qn('ec') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('ec.user_id') )
               ->leftJoin( '(' . $subquery3 . ') AS ' . $db->qn('rev') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('rev.user_id') )
               ->leftJoin( '(' . $subquery4 . ') AS ' . $db->qn('rep') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('rep.user_id') )
               ->leftJoin( '(' . $subquery5 . ') AS ' . $db->qn('t') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('t.user_id') )
               ->where( $db->qn('u.id') . ' = ' . $db->q($pk) );
               
        $db->setQuery($query);
			
        return $db->loadObject();
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
        $data = parent::validate($form, $data, $group);
            
        if (!$data)
            return $data;
            
        if ( empty($data['user_id']) )
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_USER_PROVIDED_ERROR') );
            return false;
        }
            
        return $data;
    }
        
    /**
     * Save the user data.
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
            
        // Convert the data array to an object.
        $data = (object)$data;
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select('COUNT(*)')
               ->from( $db->qn('#__rsdirectory_users') )
               ->where( $db->qn('user_id') . ' = ' . $db->q($data->user_id) );
               
        $db->setQuery($query);
            
        // Remember the item id.
        $this->setState( $this->getName() . '.id', $data->user_id );
            
        if ( $db->loadResult() )
            return $db->updateObject( '#__rsdirectory_users', $data, 'user_id' );
            
            
        return $db->insertObject( '#__rsdirectory_users', $data, 'user_id' );
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