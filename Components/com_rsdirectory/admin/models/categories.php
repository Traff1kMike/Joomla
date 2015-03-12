<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_categories/models/categories.php';

if ( !class_exists('RSDirectoryCategories') )
{
    $jversion = new JVersion();
        
    if ( $jversion->isCompatible('3.0') )
    {
        class RSDirectoryCategories extends CategoriesModelCategories
        {
            /**
             * Method to get a object for retrieving the data set from a database.
             *
             * @access protected
             *
             * @return  object
             */
            protected function getListQuery()
            {
                // Get the query from the parent class.
                $query = parent::getListQuery();
                    
                // Get DBO.
                $db = JFactory::getDbo();
                    
                $query->select( $db->qn('a.params') );
                    
                return $query;
            }
        }
    }
    else if ( $jversion->isCompatible('2.5') )
    {
        class RSDirectoryCategories extends CategoriesModelCategories
        {
            /**
             * Method to get a object for retrieving the data set from a database.
             *
             * @access public
             *
             * @return  object
             */
            public function getListQuery()
            {
                // Get the query from the parent class.
                $query = parent::getListQuery();
                    
                // Get DBO.
                $db = JFactory::getDbo();
                    
                $query->select( $db->qn('a.params') );
                    
                return $query;
            }
        }
    }    
}


/**
 * Categories model.
 */
class RSDirectoryModelCategories extends RSDirectoryCategories
{
    /**
    * Constructor.
    *
    * @param array An optional associative array of configuration settings.
    */
    public function __construct( $config = array() )
    {
        if ( empty($config['filter_fields']) )
        {
            $config['filter_fields'] = array(
                'a.ordering',
                'a.lft',
                'a.published',
                'a.title',
                'a.access',
                'language',
                'a.id',
            );
        }
            
        parent::__construct($config);
    }
    
    /**
     * Method to get a list of categories.
     *
     * @access public
     *
     * @return mixed An array of objects on success, false on failure.
     */
    public function getItems()
    {
        $items = parent::getItems();
            
        if (!$items)
            return $items;
            
        // Get the forms.
        $forms = RSDirectoryHelper::getForms();
            
        $forms_aux = array();
           
        // Group the forms by id. 
        foreach($forms as $form)
        {
            $forms_aux[$form->id] = $form;
        }
            
        // Get all the categories that belong to RSDirectory!
        $categories = RSDirectoryHelper::getCategories();
            
        foreach ($items as &$item)
        {
            // Get the category params.
            $params = new JRegistry($item->params);
                
            // Get the form id.
            $form_id = $params->get('form_id');
                
            // We have a form assigned to the category.
            if ( isset($forms_aux[$form_id]) )
            {
                $item->form = $forms_aux[$form_id];
            }
            // Inherited form.
            else
            {
                $form_id = RSDirectoryHelper::getCategoryInheritedFormId($item->id, $categories);
                    
                // Found the inherited form.
                if ( isset($forms_aux[$form_id]) )
                {
                    $item->form = clone $forms_aux[$form_id];
                    $item->form->title = JText::sprintf('COM_RSDIRECTORY_INHERITED', " ({$item->form->title})");
                }
                // No form found.
                else
                {
                    $item->form = (object)array(
                        'title' => JText::sprintf( 'COM_RSDIRECTORY_INHERITED', ' (' . JText::_('JNONE') . ')' ),
                    );
                }
            }
        }
            
        return $items;
    }
        
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string An optional ordering field.
     * @param string An optional direction (asc|desc).
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // List state information.
        parent::populateState('a.lft', 'asc');
            
        $this->setState('filter.extension', 'com_rsdirectory');
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
            JHtml::_( 'select.option', 'a.lft', JText::_('JGRID_HEADING_ORDERING') ),
            JHtml::_( 'select.option', 'a.published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'a.title', JText::_('COM_RSDIRECTORY_TITLE') ),
            JHtml::_( 'select.option', 'a.access', JText::_('JGRID_HEADING_ACCESS') ),
            JHtml::_( 'select.option', 'language', JText::_('JGRID_HEADING_LANGUAGE') ),
            JHtml::_( 'select.option', 'a.id', JText::_('JGRID_HEADING_ID') ),
        );
            
        // Initialize the options array.
        $options = array(
            'search' => array(
                'label' => JText::_('JSEARCH_FILTER'),
                'title' => JText::_('JSEARCH_FILTER'),
                'placeholder' => JText::_('JSEARCH_FILTER'),
                'label' => JText::_('JSEARCH_FILTER'),
                'value' => $this->getState('filter.search'),
            ),
            'limitBox' => $this->getPagination()->getLimitBox(),
            'listDirn' => $this->getState('list.direction', 'desc'),
            'listOrder' => $this->getState('list.ordering', 'date'),
            'sortFields' => $sortFields,
            'rightItems' => array(
                array(
                    'input' => '<select name="filter_language" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_LANGUAGE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getLanguageOptions(), 'value', 'text', $this->getState('filter.language'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_access" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_ACCESS') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getAccessOptions(), 'value', 'text', $this->getState('filter.access'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_level" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_MAX_LEVELS') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getLevelsOptions(), 'value', 'text', $this->getState('filter.level'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_published" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_PUBLISHED') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.published'), true ) .
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
     * @return string
     */
    public function getSideBar()
    { 
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
            
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.published'), true )
        );  
            
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_MAX_LEVELS'),
            'filter_level',
            JHtml::_( 'select.options', $this->getLevelsOptions(), 'value', 'text', $this->getState('filter.level') )
        );
            
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_ACCESS'),
            'filter_access',
            JHtml::_( 'select.options', $this->getAccessOptions(), 'value', 'text', $this->getState('filter.access') )
        );
            
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_LANGUAGE'),
            'filter_language',
            JHtml::_( 'select.options', $this->getLanguageOptions(), 'value', 'text', $this->getState('filter.language') )
        );
            
        return RSDirectoryToolbarHelper::render();
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
        
    /**
     * Get levels options.
     *
     * @access public
     *
     * @return array
     */
    public function getLevelsOptions()
    {
        return array(
            JHtml::_( 'select.option', 1, JText::_('J1') ),
            JHtml::_( 'select.option', 2, JText::_('J2') ),
            JHtml::_( 'select.option', 3, JText::_('J3') ),
            JHtml::_( 'select.option', 4, JText::_('J4') ),
            JHtml::_( 'select.option', 5, JText::_('J5') ),
            JHtml::_( 'select.option', 6, JText::_('J6') ),
            JHtml::_( 'select.option', 7, JText::_('J7') ),
            JHtml::_( 'select.option', 8, JText::_('J8') ),
            JHtml::_( 'select.option', 9, JText::_('J9') ),
            JHtml::_( 'select.option', 10, JText::_('J10') ),
        );
    }
        
    /**
     * Get access options.
     *
     * @access public
     *
     * @return array
     */
    public function getAccessOptions()
    {
        $db = JFactory::getDbo();
		$query = $db->getQuery(true)
			  ->select( $db->qn('a.id', 'value') . ', ' . $db->qn('a.title', 'text') )
			  ->from( $db->qn('#__viewlevels', 'a') )
			  ->group( $db->qn( array( 'a.id', 'a.title', 'a.ordering') ) )
			  ->order( $db->qn('a.ordering') . ' ASC')
			  ->order($db->qn('title') . ' ASC');
                
		// Get the options.
		$db->setQuery($query);
		return $db->loadObjectList();
    }
        
    /**
     * Get language options.
     *
     * @access public
     *
     * @return array
     */
    public function getLanguageOptions()
    {
        return array_merge(
            array( JHtml::_( 'select.option', '*', JText::_('JALL') ) ),
            JHtml::_('contentlanguage.existing')
        );
    }
}