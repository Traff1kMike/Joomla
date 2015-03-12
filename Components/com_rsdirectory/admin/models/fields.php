<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Fields model.
 */
class RSDirectoryModelFields extends JModelList
{
    /**
     * Class constructor.
     *
     * @access public
     * 
     * @param array $config
     */
    public function __construct( $config = array() )
    {
        if ( empty($config['filter_fields']) )
        {
            $config['filter_fields'] = array(
                'ff.ordering',
                'f.published',
                'f.name',
                'ft.type',
                'searchable_simple',
                'searchable_advanced',
                'ft.core',
                'f.required',
                'credits',
                'f.id',
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
        $form_id = $this->getState('filter.form');
            
        $select = array(
            $db->qn('f') . '.*',
            $db->qn('ft.core'),
            $db->qn('ft.type'),
            $db->qn('fpc.value', 'credits'),
            $db->qn('fpss.value', 'searchable_simple'),
            $db->qn('fpsa.value', 'searchable_advanced'),
        );
            
        if ($form_id)
        {
            $select[] = $db->qn('ff.ordering');
        }
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_fields', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_field_types', 'ft') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ft.id') )
               ->leftJoin( $db->qn('#__rsdirectory_fields_properties', 'fpc') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fpc.field_id') . ' AND ' . $db->qn('fpc.property_name') . ' = ' . $db->q('credits') )
               ->leftJoin( $db->qn('#__rsdirectory_fields_properties', 'fpss') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fpss.field_id') . ' AND ' . $db->qn('fpss.property_name') . ' = ' . $db->q('searchable_simple') )
               ->leftJoin( $db->qn('#__rsdirectory_fields_properties', 'fpsa') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fpsa.field_id') . ' AND ' . $db->qn('fpsa.property_name') . ' = ' . $db->q('searchable_advanced') );
                
        // Filter by published state.  
        $published = $this->getState('filter.published');
            
        if ($published !== '')
        {
            $query->where( $db->qn('f.published') . ' = ' . $db->q($published) );
        }
            
        // Filter by form.
        if ($form_id)
        {
            $query->innerJoin( $db->qn('#__rsdirectory_forms_fields', 'ff') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('ff.field_id') )
                  ->where( $db->qn('ff.form_id') . ' = ' . $db->q($form_id) );
        }
            
        // Filter by field type.  
        if ( $field_type = $this->getState('filter.field_type') )
        {
            $query->where( $db->qn('ft.type') . ' = ' . $db->q($field_type) );
        }
            
        // Filter by core field.
        $core_field = $this->getState('filter.core_field');
            
        if ($core_field !== '')
        {
            $query->where( $db->qn('ft.core') . ' = ' . $db->q($core_field) );
        }
            
        // Is required?
        $required = $this->getState('filter.required');
            
        if ($required !== '')
        {
            $query->where( $db->qn('f.required') . ' = ' . $db->q($required) );
        }
            
        // Filter by searchable simple.
        $searchable_simple = $this->getState('filter.searchable_simple');
            
        if ($searchable_simple !== '')
        {
            if ($searchable_simple == -1)
            {
                $query->where( $db->qn('fpss.value') . ' IS NULL' );    
            }
            else
            {
                $query->where( $db->qn('fpss.value') . ' = ' . $db->q($searchable_simple) );    
            }
        }
            
        // Filter by searchable advanced.
        $searchable_advanced = $this->getState('filter.searchable_advanced');
            
        if ($searchable_advanced !== '')
        {
            if ($searchable_advanced == -1)
            {
                $query->where( $db->qn('fpsa.value') . ' IS NULL' );
            }
            else if ($searchable_advanced == 0)
            {
                $query->where( '(' . $db->qn('fpsa.value') . ' = ' . $db->q(0) . ' OR ' . $db->qn('fpsa.value') . ' = ' . $db->q('') . ')' );
            }
            else
            {
                $query->where( $db->qn('fpsa.value') . ' = ' . $db->q($searchable_advanced) );
            }
        }
            
        // Filter by search.
        $search = $this->getState('filter.search');
            
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $query->where( $db->qn('f.name') . " LIKE $search" );
        }
            
        // If displaying the modal view, limit the results to the following field types.
        if ( JFactory::getApplication()->input->get('layout') == 'modal' )
        {
            $modal_field_types = array(
                $db->q('dropdown'),
                $db->q('checkboxgroup'),
                $db->q('radiogroup'),
                $db->q('country'),
            );
                
            $query->where( $db->qn('ft.type') . ' IN (' . implode(',', $modal_field_types) . ')' );
        }
            
        $ordering = $this->getState('list.ordering', 'f.id');
        $direction = $this->getState('list.direction', 'asc');
            
        $query->order(
            ( $ordering == 'credits' ? 'CAST(' . $db->qn($ordering) . ' AS UNSIGNED)' : $db->qn($ordering) ) . ' ' . $db->escape($direction)
        );
            
        return $query;
    }
        
    /**
     * Method to auto-populate the model state. 
     * 
     * @access protected
     * 
     * @param string $ordering
     * @param string $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Adjust the context to support modal layouts.
		if ( $layout = JFactory::getApplication()->input->get('layout') )
		{
			$this->context .= ".$layout";
		}
            
        $search = $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search');
        $this->setState('filter.search', $search);
            
        $published = $this->getUserStateFromRequest("$this->context.filter.published", 'filter_published', '');
        $this->setState('filter.published', $published);
            
        $form = $this->getUserStateFromRequest("$this->context.filter.form", 'filter_form');
        $this->setState('filter.form', $form);
          
        $field_type = $this->getUserStateFromRequest("$this->context.filter.field_type", 'filter_field_type');
        $this->setState('filter.field_type', $field_type);
            
        $core_field = $this->getUserStateFromRequest("$this->context.filter.core_field", 'filter_core_field', '');
        $this->setState('filter.core_field', $core_field);
            
        $required = $this->getUserStateFromRequest("$this->context.filter.required", 'filter_required', '');
        $this->setState('filter.required', $required);
            
        $searchable_simple = $this->getUserStateFromRequest("$this->context.filter.searchable_simple", 'filter_searchable_simple', '');
        $this->setState('filter.searchable_simple', $searchable_simple);
            
        $searchable_advanced = $this->getUserStateFromRequest("$this->context.filter.searchable_advanced", 'filter_searchable_advanced', '');
        $this->setState('filter.searchable_advanced', $searchable_advanced);  
            
        // List state information.
        parent::populateState('f.id', 'asc');
            
            
        $ordering_aux = $this->getUserStateFromRequest("$this->context.ordercol", 'filter_order', 'f.id');
            
        if ( empty($ordering_aux) || (!$form && $ordering_aux == 'ff.ordering') )
        {
            $ordering_aux = 'f.id';
        }
            
        $this->setState('list.ordering', $ordering_aux);
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
            JHtml::_( 'select.option', 'f.published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'f.name', JText::_('COM_RSDIRECTORY_NAME') ),
            JHtml::_( 'select.option', 'ft.type', JText::_('COM_RSDIRECTORY_TYPE') ),
            JHtml::_( 'select.option', 'searchable_simple', JText::_('COM_RSDIRECTORY_SEARCHABLE_SIMPLE') ),
            JHtml::_( 'select.option', 'searchable_advanced', JText::_('COM_RSDIRECTORY_SEARCHABLE_ADVANCED') ),
            JHtml::_( 'select.option', 'ft.core', JText::_('COM_RSDIRECTORY_CORE_FIELD') ),
            JHtml::_( 'select.option', 'f.required', JText::_('COM_RSDIRECTORY_REQUIRED') ),
            JHtml::_( 'select.option', 'credits', JText::_('COM_RSDIRECTORY_CREDITS') ),
            JHtml::_( 'select.option', 'f.id', JText::_('JGRID_HEADING_ID') ),
        );
            
        // Add the ordering option if a form was selected.
        if ( $this->getState('filter.form') )
        {
            array_unshift( $sortFields, JHtml::_( 'select.option', 'ff.ordering', JText::_('JGRID_HEADING_ORDERING') ) );
        }
            
        // Get an instance of the Forms model.
        $forms_model = RSDirectoryModel::getInstance('Forms');
            
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
                    'input' => '<select name="filter_searchable_advanced" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_FILTER_SEARCHABLE_ADVANCED') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getSearchableAdvancedOptions(), 'value', 'text', $this->getState('filter.searchable_advanced'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_searchable_simple" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_FILTER_SEARCHABLE_SIMPLE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getSearchableSimpleOptions(), 'value', 'text', $this->getState('filter.searchable_simple'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_required" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_FILTER_REQUIRED') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.required'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_core_field" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_FILTER_CORE_FIELD') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.core_field'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_field_type" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_FILTER_SELECT_FIELD_TYPE') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getFieldTypes(), 'value', 'text', $this->getState('filter.field_type'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_form" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_FORM') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $forms_model->getFormsOptions(), 'value', 'text', $this->getState('filter.form'), true ) .
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
        // Get an instance of the Forms model.
        $forms_model = RSDirectoryModel::getInstance('Forms');
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
            
        // Status filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.published'), true )
        );
            
        // Form filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_FORM'),
            'filter_form',
            JHtml::_('select.options', $forms_model->getFormsOptions(), 'value', 'text', $this->getState('filter.form'), true)
        );
            
        // Credit package filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_FILTER_SELECT_FIELD_TYPE'),
            'filter_field_type',
            JHtml::_( 'select.options', $this->getFieldTypes(), 'value', 'text', $this->getState('filter.field_type') )
        );
            
        // Core field filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_FILTER_CORE_FIELD'),
            'filter_core_field',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.core_field'), true )
        );
            
        // Required filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_FILTER_REQUIRED'),
            'filter_required',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.required'), true )
        );
            
        // Simple search filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_FILTER_SEARCHABLE_SIMPLE'),
            'filter_searchable_simple',
            JHtml::_( 'select.options', $this->getSearchableSimpleOptions(), 'value', 'text', $this->getState('filter.searchable_simple'), true )
        );
            
        // Advanced search filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_FILTER_SEARCHABLE_ADVANCED'),
            'filter_searchable_advanced',
            JHtml::_( 'select.options', $this->getSearchableAdvancedOptions(), 'value', 'text', $this->getState('filter.searchable_advanced'), true )
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
     * Build a list field types..
     *
     * @access public
     *
     * @return mixed
     */
    public function getFieldTypes()
    {
        $field_types_model = RSDirectoryModel::getInstance('FieldTypes');
            
        $field_types = $field_types_model->getFieldTypesObjectList();
            
        $options = array();
            
        if ($field_types)
        {
            foreach ($field_types as $field_type)
            {
                $options[] = (object)array(
                    'value' => $field_type->type,
                    'text' => $field_type->name,
                );
            }
        }
            
        return $options;
    }
        
    /**
     * Get boolean options.
     *
     * @access public
     *
     * @return array
     */
    public function getBoolOptions()
    {
        return array(
            JHtml::_( 'select.option', 1, JText::_('JYES') ),
            JHtml::_( 'select.option', 0, JText::_('JNO') ),
        );
    }
        
    /**
     * Get searchable simple options.
     *
     * @access public
     *
     * @return array
     */
    public function getSearchableSimpleOptions()
    {
        return array(
            JHtml::_( 'select.option', -1, JText::_('JNONE') ),
            JHtml::_( 'select.option', 1, JText::_('JYES') ),
            JHtml::_( 'select.option', 0, JText::_('JNO') ),
        );
    }
        
    /**
     * Get searchable advanced options.
     *
     * @access public
     *
     * @return
     */    
    public function getSearchableAdvancedOptions()
    {
        return array(
            JHtml::_( 'select.option', -1, JText::_('JNONE') ),
            JHtml::_( 'select.option', 1, JText::_('JYES') ),
            JHtml::_( 'select.option', 0, JText::_('JNO') ),
            JHtml::_( 'select.option', 'textbox', JText::_('COM_RSDIRECTORY_FIELDS_TEXTBOX') ),
            JHtml::_( 'select.option', 'range', JText::_('COM_RSDIRECTORY_FIELDS_RANGE') ),
            JHtml::_( 'select.option', 'checkboxgroup', JText::_('COM_RSDIRECTORY_FIELDS_CHECKBOXGROUP') ),
            JHtml::_( 'select.option', 'radiogroup', JText::_('COM_RSDIRECTORY_FIELDS_RADIOGROUP') ),
            JHtml::_( 'select.option', 'dropdown', JText::_('COM_RSDIRECTORY_FIELDS_DROPDOWN') ),
        );
    }
}