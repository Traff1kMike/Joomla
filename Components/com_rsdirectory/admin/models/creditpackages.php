<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The Credits model.
 */
class RSDirectoryModelCreditPackages extends JModelList
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
                'ordering', 'published', 'title', 'price', 'credits', 'id',
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
            
        $query = $db->getQuery(true);
        $query->select('*')
              ->from( $db->qn('#__rsdirectory_credit_packages') );
              
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $query->where( $db->qn('title') . " LIKE $search OR " . $db->qn('description') . " LIKE $search" );
        }
            
        $ordering = $this->getState('list.ordering', 'id');
        $direction = $this->getState('list.direction', 'asc');
            
        if ($ordering == 'credits')
        {
            $query->order( $db->qn($ordering) . ' = 0 ' . $db->escape($direction) . ', ' . $db->qn($ordering) . ' ' . $db->escape($direction) );
        }
        else
        {
            $query->order( $db->qn($ordering) . ' ' . $db->escape($direction) );   
        }
            
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
            
        // List state information.
        parent::populateState('id', 'asc');
    }
        
    /**
     * Get filter bar.
     *
     * @access public
     *
     * @return RSFilterBar
     */
    public function getFilterBar()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/filterbar.php';
            
        $sortFields = array(
            JHtml::_( 'select.option', 'ordering', JText::_('JGRID_HEADING_ORDERING') ),
            JHtml::_( 'select.option', 'published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'title', JText::_('COM_RSDIRECTORY_TITLE') ),
            JHtml::_( 'select.option', 'price', JText::_('COM_RSDIRECTORY_PRICE') ),
            JHtml::_( 'select.option', 'credits', JText::_('COM_RSDIRECTORY_CREDITS') ),
            JHtml::_( 'select.option', 'id', JText::_('JGRID_HEADING_ID') ),
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
            
        return RSDirectoryToolbarHelper::render();
    }
}