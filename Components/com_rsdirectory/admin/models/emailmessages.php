<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The EmailMessages model.
 */
class RSDirectoryModelEmailMessages extends JModelList
{
    /**
     * Class constructor.
     * 
     * @param array $config
     */
    public function __construct( $config = array() )
    {
        if ( empty($config['filter_fields']) )
        {
            $config['filter_fields'] = array(
                'em.published', 'em.subject', 'em.type', 'c.title', 'em.id',
            );
        }
            
        parent::__construct($config);
    }
        
    /**
     * Method to get a JDatabaseQuery object for retrieving the data set from a database.
     *
     * @access protected
     *
     * @return object A JDatabaseQuery object to retrieve the data set.
     */
    protected function getListQuery()
    {
        // Get DBO.
        $db = JFactory::getDBO();
            
        // Get filtering states.
        $search = $this->getState('filter.search');
        $type = $this->getState('filter.type');
        $published = $this->getState('filter.published');
            
        $query = $db->getQuery(true)
               ->select( $db->qn('em') . '.*, ' . $db->qn('c.title') )
               ->from( $db->qn('#__rsdirectory_email_messages', 'em') )
               ->join( 'LEFT', $db->qn('#__categories', 'c') . ' ON ' . $db->qn('em.category_id') . ' = ' . $db->qn('c.id') );
              
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $where = array(
                $db->qn('em.subject') . " LIKE $search",
                $db->qn('em.text') . " LIKE $search",
                $db->qn('c.title') . " LIKE $search",
            );
                
            $query->where( '(' .implode(' OR ', $where) . ')' );
        }
            
        if ($type)
        {
            $query->where( '(' . $db->qn('em.type') . ' = ' . $db->q($type) . ')' );    
        }        
            
        if ($published != '')
        {
            $query->where( '(' . $db->qn('em.published') . ' = ' . $db->q($published) . ')' );
        }
            
        $ordering = $this->getState('list.ordering', 'id');
        $direction = $this->getState('list.direction', 'asc');
            
        $query->order( $db->qn($ordering) . ' ' . $db->escape($direction) );
            
        return $query;
    }
        
    /**
     * Method to auto-populate the model state. 
     * 
     * @access protected
     * @param string $ordering
     * @param string $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $this->setState( 'filter.search', $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search') );
        $this->setState( 'filter.type', $this->getUserStateFromRequest("$this->context.filter.type", 'filter_type') );
        $this->setState( 'filter.published', $this->getUserStateFromRequest("$this->context.filter.type", 'filter_published') );
            
        // List state information.
        parent::populateState('id', 'asc');
    }
        
    /**
     * Get filter bar.
     *
     * @access public
     * 
     */
    public function getFilterBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/filterbar.php';
            
        $sortFields = array(
            JHtml::_( 'select.option', 'em.published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'em.subject', JText::_('COM_RSDIRECTORY_SUBJECT') ),
            JHtml::_( 'select.option', 'em.type', JText::_('COM_RSDIRECTORY_TYPE') ),
            JHtml::_( 'select.option', 'c.title', JText::_('COM_RSDIRECTORY_CATEGORY') ),
            JHtml::_( 'select.option', 'em.id', JText::_('JGRID_HEADING_ID') ),
        );
            
        // Initialize the options array.
        $options = array(
            'search' => array(
                'label' => JText::_('JSEARCH_FILTER'),
                'title' => JText::_('JSEARCH_FILTER'),
                'placeholder' => JText::_('JSEARCH_FILTER'),
                'value' => $this->getState('filter.search'),
            ),
            'limitBox' => $this->getPagination()->getLimitBox(),
            'listDirn' => $this->getState('list.direction', 'desc'),
            'listOrder' => $this->getState('list.ordering', 'date'),
            'sortFields' => $sortFields,
            'rightItems' => array(
                array(
                    'input' => '<select name="filter_published" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_PUBLISHED') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.published'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_type" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_EMAIL_MESSAGE_TYPE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEmailMessageTypes(), 'value', 'text', $this->getState('filter.type'), true ) .
                               '</select>',
                ),
            ),
        );
            
        $bar = new RSFilterBar($options);
            
        return $bar;
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
            
        // Email message type filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_EMAIL_MESSAGE_TYPE'),
            'filter_type',
            JHtml::_( 'select.options', $this->getEmailMessageTypes(), 'value', 'text', $this->getState('filter.type') )
        );
            
        // Status filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.published'), true )
        );
            
        return RSDirectoryToolbarHelper::render();
    }
        
    /**
     * Get email message types.
     *
     * @access public
     *
     * @return array
     */
    public function getEmailMessageTypes()
    {
        return array(
            JHtml::_( 'select.option', 'submit_entry', JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_TYPES_SUBMIT_ENTRY') ),
            JHtml::_( 'select.option', 'publish_entry', JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_TYPES_PUBLISH_ENTRY') ),
            JHtml::_( 'select.option', 'unpublish_entry', JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_TYPES_UNPUBLISH_ENTRY') ),
            JHtml::_( 'select.option', 'delete_entry', JText::_('COM_RSDIRECTORY_EMAIL_MESSAGE_TYPES_DELETE_ENTRY') ),
        );
    }
        
    /**
     * Get status options.
     *
     * @access public
     *
     * @return array
     */
    public function getStatusOptions()
    {
        return array(
            JHtml::_( 'select.option', 1, JText::_('JPUBLISHED') ),
            JHtml::_( 'select.option', 0, JText::_('JUNPUBLISHED') ),
        );
    }
}