<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entries model.
 */
class RSDirectoryModelEntries extends JModelList
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
                'e.published',
                'e.title',
                'c.path',
                'form',
                'author',
                'e.category_id',
                'e.created_time',
                'e.published_time',
                'e.modified_time',
                'e.expiry_time',
                'e.renew',
                'e.promoted',
                'e.paid',
                'e.id',
            );
        }
            
        parent::__construct($config);
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
        // Adjust the context to support modal layouts.
        if ( $layout = JFactory::getApplication()->input->get('layout') )
        {
            $this->context .= ".$layout";
        }
            
        $search = $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search');
        $this->setState('filter.search', $search);
            
        $published = $this->getUserStateFromRequest("$this->context.filter.published", 'filter_published', '');
        $this->setState('filter.published', $published);
            
        $availability = $this->getUserStateFromRequest("$this->context.filter.availability", 'filter_availability', '');
        $this->setState('filter.availability', $availability);
            
        $form_id = $this->getUserStateFromRequest("$this->context.filter.form_id", 'filter_form_id', '');
        $this->setState('filter.form_id', $form_id);
            
        $category_id = $this->getUserStateFromRequest("$this->context.filter.category_id", 'filter_category_id');
        $this->setState('filter.category_id', $category_id);
            
        $level = $this->getUserStateFromRequest("$this->context.filter.level", 'filter_level');
        $this->setState('filter.level', $level);
            
        $author_id = $this->getUserStateFromRequest("$this->context.filter.author_id", 'filter_author_id');
        $this->setState('filter.author_id', $author_id);
            
        $renew = $this->getUserStateFromRequest("$this->context.filter.renew", 'filter_renew');
        $this->setState('filter.renew', $renew);
            
        $promoted = $this->getUserStateFromRequest("$this->context.filter.promoted", 'filter_promoted');
        $this->setState('filter.promoted', $promoted);
            
        $paid = $this->getUserStateFromRequest("$this->context.filter.paid", 'filter_paid');
        $this->setState('filter.paid', $paid);
            
        // List state information.
        parent::populateState('e.published_time', 'desc');
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
            
        // Get the JUser object.
        $user = JFactory::getUser();
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        $select = array(
            $db->qn('e') . '.*',
            $db->qn('ec') . '.*',
            $db->qn('c.title', 'category_title'),
            $db->qn('c.path', 'category_path'),
            $db->qn("u.$author", 'author'),
            $db->qn('f.title', 'form'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_entries', 'e') )
               ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__rsdirectory_forms', 'f') . ' ON ' . $db->qn('e.form_id') . ' = ' . $db->qn('f.id') )
               ->leftJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') );
             
        // Filter by published state.  
        $published = $this->getState('filter.published');
            
        if ($published !== '')
        {
            $query->where( $db->qn('e.published') . ' = ' . $db->q($published) );
        }
            
        // Filter by availability.
        $availability = $this->getState('filter.availability');
            
        if ($availability !== '')
        {
            if ($availability == 0)
            {
                $query->where( $db->qn('e.expiry_time') . ' != ' . $db->q('0000-00-00 00:00:00') . ' AND ' . $db->qn('e.expiry_time') . ' < ' . $db->q( JFactory::getDate()->toSql() ) );
            }
            else
            {
                $query->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' > ' . $db->q( JFactory::getDate()->toSql() ) . ')' );
            }
        }
            
        // Filter by form id.
        $form_id = $this->getState('filter.form_id');
            
        if ($form_id !== '')
        {
            $query->where( $db->qn('form_id') . ' = ' . $db->q($form_id) );
        }
            
        // Filter by category.
        $category_id = $this->getState('filter.category_id');
        $baselevel = 1;
            
        if ( is_numeric($category_id) )
        {
            $category = JTable::getInstance('Category', 'JTable');
            $category->load($category_id);
            $baselevel = (int) $category->level;
            $query->where( $db->qn('c.lft') . ' >= ' . $db->q($category->lft) );
            $query->where( $db->qn('c.rgt') . ' <= ' . $db->q($category->rgt) );
        }
            
        // Filter by level.
        if ( $level = $this->getState('filter.level') )
        {
            $query->where( $db->qn('c.level') . ' <= ' . ( (int) $level + (int) $baselevel - 1 ) );  
        }
            
        // Filter by author.
        $author_id = $this->getState('filter.author_id');
            
        if ( is_numeric($author_id) )
        {
            $query->where( $db->qn('e.user_id') . ' = ' . $db->q($author_id) );
        }
            
        // Renew Y/N ?
        $renew = $this->getState('filter.renew');
            
        if ( is_numeric($renew) )
        {
            $query->where( $db->qn('e.renew') . ' = ' . $db->q($renew) );    
        }
            
        // Promoted Y/N ?
        $promoted = $this->getState('filter.promoted');
            
        if ( is_numeric($promoted) )
        {
            $query->where( $db->qn('e.promoted') . ' = ' . $db->q($promoted) );    
        }
            
        // Paid Y/N ?
        $paid = $this->getState('filter.paid');
            
        if ( is_numeric($paid) )
        {
            $query->where( $db->qn('e.paid') . ' = ' . $db->q($paid) );    
        }
            
        // Filter by search in title.
        $search = $this->getState('filter.search');
            
        if ( !empty($search) )
        {
            if ( stripos($search, 'id:') === 0 )
            {
                $query->where( $db->qn('e.id') . ' = ' . $db->q( substr($search, 3) ) );
            }
            else if ( stripos($search, 'author:') === 0 )
            {
                $search = $db->q( '%' . $db->escape( substr($search, 7), true ) .'%' );
                $query->where( "(" . $db->qn('u.name') . " LIKE $search OR " . $db->qn('u.username') . " LIKE $search)" );
            }
            else
            {
                $search = $db->q( '%' . $db->escape($search, true) . '%' );
                $query->where( $db->qn('e.title') . " LIKE $search" );
            }
        }
            
        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'e.title');
        $orderDirn = $this->state->get('list.direction', 'asc');
            
        $query->order( $db->qn($orderCol) . ' ' . $db->escape($orderDirn) );
            
        return $query;
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
            JHtml::_( 'select.option', 'e.published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'e.title', JText::_('COM_RSDIRECTORY_TITLE') ),
            JHtml::_( 'select.option', 'c.path', JText::_('JCATEGORY') ),
            JHtml::_( 'select.option', 'form', JText::_('COM_RSDIRECTORY_FORM') ),
            JHtml::_( 'select.option', 'author', JText::_('JAUTHOR') ),
            JHtml::_( 'select.option', 'e.created_time', JText::_('COM_RSDIRECTORY_DATE_CREATED') ),
            JHtml::_( 'select.option', 'e.published_time', JText::_('COM_RSDIRECTORY_DATE_PUBLISHED') ),
            JHtml::_( 'select.option', 'e.modified_time', JText::_('COM_RSDIRECTORY_DATE_MODIFIED') ),
            JHtml::_( 'select.option', 'e.expiry_time', JText::_('COM_RSDIRECTORY_EXPIRY_DATE') ),
            JHtml::_( 'select.option', 'e.promoted', JText::_('COM_RSDIRECTORY_FIELDS_PROMOTED') ),
            JHtml::_( 'select.option', 'e.renew', JText::_('COM_RSDIRECTORY_RENEW') ),
            JHtml::_( 'select.option', 'e.paid', JText::_('COM_RSDIRECTORY_PAID') ),
            JHtml::_( 'select.option', 'e.id', JText::_('JGRID_HEADING_ID') ),
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
            'limitBox' => $this->getPagination()->getLimitBox(),
            'listDirn' => $this->getState('list.direction', 'desc'),
            'listOrder' => $this->getState('list.ordering', 'e.published_time'),
            'sortFields' => $sortFields,
            'rightItems' => array(
                array(
                    'input' => '<select name="filter_paid" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_PAID') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.paid'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_promoted" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_PROMOTED') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.promoted'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_renew" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_RENEW') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.renew'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_author_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_AUTHOR') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getAuthors(), 'value', 'text', $this->getState('filter.author_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_level" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_MAX_LEVELS') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getLevelsOptions(), 'value', 'text', $this->getState('filter.level'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('JOPTION_SELECT_CATEGORY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', JHtml::_('category.options', 'com_rsdirectory'), 'value', 'text', $this->getState('filter.category_id') ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_form_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_FORM') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $forms_model->getFormsOptions(), 'value', 'text', $this->getState('filter.form_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_availability" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_AVAILABILITY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getAvailabilityOptions(), 'value', 'text', $this->getState('filter.availability'), true ) .
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
            
        if ( RSDirectoryHelper::isJ30() )
        {
            $options['search'] = array(
                'label' => JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'),
                'title' => JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'),
                'placeholder' => JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'),
                'value' => $this->getState('filter.search'),
            );
        }
        else
        {
            $options['search'] = array(
                'label' => JText::_('JSEARCH_FILTER'),
                'title' => JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'),
                'placeholder' => JText::_('COM_RSDIRECTORY_FILTER_SEARCH_DESC'),
                'value' => $this->getState('filter.search'),
            );
        }
            
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
            
        // Status filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            JHtml::_( 'select.options', $this->getStatusOptions(), 'value', 'text', $this->getState('filter.published'), true )
        );
            
        // Availability filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_AVAILABILITY'),
            'filter_availability',
            JHtml::_( 'select.options', $this->getAvailabilityOptions(), 'value', 'text', $this->getState('filter.availability'), true )
        );
            
        // Get an instance of the Forms model.
        $forms_model = RSDirectoryModel::getInstance('Forms');
            
        // Form filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_FORM'),
            'filter_form_id',
            JHtml::_('select.options', $forms_model->getFormsOptions(), 'value', 'text', $this->getState('filter.form_id'), true)
        );
            
        // Category filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_CATEGORY'),
            'filter_category_id',
            JHtml::_( 'select.options', JHtml::_('category.options', 'com_rsdirectory'), 'value', 'text', $this->getState('filter.category_id') )
        ); 
            
        // Levels filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_MAX_LEVELS'),
            'filter_level',
            JHtml::_( 'select.options', $this->getLevelsOptions(), 'value', 'text', $this->getState('filter.level') )
        );
            
        // Author filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('JOPTION_SELECT_AUTHOR'),
            'filter_author_id',
            JHtml::_( 'select.options', $this->getAuthors(), 'value', 'text', $this->getState('filter.author_id') )
        );
            
        // Renew
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_RENEW'),
            'filter_renew',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.renew') )
        );
            
        // Promoted.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_PROMOTED'),
            'filter_promoted',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.promoted') )
        );
            
        // Paid.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_PAID'),
            'filter_paid',
            JHtml::_( 'select.options', $this->getBoolOptions(), 'value', 'text', $this->getState('filter.paid') )
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
     * Get availability options.
     *
     * @access public
     *
     * @return array
     */
    public function getAvailabilityOptions()
    {
        return array(
            JHtml::_( 'select.option', 1, JText::_('COM_RSDIRECTORY_NOT_EXPIRED') ),
            JHtml::_( 'select.option', 0, JText::_('COM_RSDIRECTORY_EXPIRED') ),
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
     * Build a list of authors.
     *
     * @access public
     *
     * @return array
     */
    public function getAuthors()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('u.id', 'value') . ', ' . $db->qn('u.name', 'text') )
               ->from( $db->qn('#__users', 'u') )
               ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Method for getting the batch form from the model.
     *
     * @access public
     * 
     * @return mixed
     */
    public function getBatchForm()
    {
        // Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
            
        $form = JForm::getInstance( 'com_rsdirectory.entries_batch', 'entries_batch', array('control' => 'batch'), false, false );
            
        if ( empty($form) )
            return false;
            
        return $form;
    }
        
    /**
     * Method to validate batch data.
     *
     * @access public
     *
     * @param array $data
     * @param array $cid
     *
     * @return bool
     */
    public function batchValidate($data, $cid)
    {
        if ( is_array($data) && is_array($cid) )
        {
            $cid = RSDirectoryHelper::arrayInt($cid, true, true);
            $cid = array_unique($cid);
                
            if (
                empty($data['author']) || !trim($data['author']) || !in_array( $data['author'], array('keep', 'new') ) ||
                empty($data['category']) || !trim($data['category']) || !in_array( $data['category'], array('keep', 'new') ) ||
                empty($data['copy_move']) || !trim($data['copy_move']) || !in_array( $data['copy_move'], array('copy', 'move') ) ||
                empty($cid) )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
                return false;
            }
            else if ( $data['author'] == 'new' && ( empty($data['user_id']) || !trim($data['user_id']) ) )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_BATCH_AUTHOR_ERROR') );
                return false;
            }
            else if ( $data['category'] == 'new' && ( empty($data['category_id']) || !trim($data['category_id']) ) )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_BATCH_CATEGORY_ERROR') );
                return false;
            }
                
            // Check if the entries exist.
            $db = JFactory::getDbo();
                
            foreach ($cid as &$entry_id)
            {
                $entry_id = $db->q($entry_id);
            }
                
            $query = $db->getQuery(true)
                   ->select('COUNT(*)')
                   ->from( $db->qn('#__rsdirectory_entries') )
                   ->where( $db->qn('id') . ' IN (' . implode(',', $cid) . ')' );
                   
            $db->setQuery($query);
                
            if ( !$db->loadResult() )
            {
                $this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
                return false;
            }
                
            return true;
        }
        else
        {
            $this->setError( JText::_('COM_RSDIRECTORY_INVALID_DATA_PROVIDED') );
        }
            
        return false;
    }
        
    /**
     * Method to process batch data.
     *
     * @access public
     *
     * @param array $data
     * @param array $cid
     *
     * @return bool
     */
    public function batchProcess($data, $cid)
    {
        $db = JFactory::getDbo();
            
        $cid = RSDirectoryHelper::arrayInt($cid, true, true);
        $cid = array_unique($cid);
            
        foreach ($cid as &$entry_id)
        {
            $entry_id = $db->q($entry_id);
        }
            
        $ids = implode(',', $cid);
            
        // Regenerate titles?
        $regenerate_titles = false;
            
        // Regenerate titles for the following entries ids.
        $entries_ids = array();
            
        // Move.
        if ($data['copy_move'] == 'move')
        {
            if ($data['author'] == 'new')
            {
                $regenerate_titles = true;
                    
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_entries') )
                       ->set( $db->qn('user_id') . ' = ' . $db->q($data['user_id']) )
                       ->where( $db->qn('id') . ' IN (' . $ids . ')' );
                       
                $db->setQuery($query);
                $db->execute();
                    
                // Assign the entries credits to the new user.
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_entries_credits') )
                       ->set( $db->qn('user_id') . ' = ' . $db->q($data['user_id']) )
                       ->where( $db->qn('entry_id') . ' IN (' . $ids . ')' );
                       
                $db->setQuery($query);
                $db->execute();
                    
                // Assign the entries files to the new user.
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_uploaded_files', 'f') )
                       ->innerJoin( $db->qn('#__rsdirectory_uploaded_files_fields_relations', 'r') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('r.file_id') )
                       ->set( $db->qn('f.user_id') . ' = ' . $db->q($data['user_id']) )
                       ->where( $db->qn('r.entry_id') . ' IN (' . $ids . ')' );
                       
                $db->setQuery($query);
                $db->execute();
            }
                
            if ($data['category'] == 'new')
            {
                $regenerate_titles = true;
                    
                $form_id = RSDirectoryHelper::getCategoryInheritedFormId($data['category_id']);
                    
                $query = $db->getQuery(true)
                       ->update( $db->qn('#__rsdirectory_entries') )
                       ->set( $db->qn('category_id') . ' = ' . $db->q($data['category_id']) )
                       ->set( $db->qn('form_id') . ' = ' . $db->q($form_id) )
                       ->where( $db->qn('id') . ' IN (' . $ids . ')' );
                       
                $db->setQuery($query);
                $db->execute();
            }
                
            $entries_ids = $ids;
        }
        // Copy.
        else
        {
            // #__rsdirectory_entries
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_entries') )
                   ->where( $db->qn('id') . ' IN (' . $ids . ')' );
                   
            $db->setQuery($query);
                
            $entries = $db->loadObjectList();
                
            if (!$entries)
                return true;
                
            if ($data['author'] == 'new')
            {
                $regenerate_titles = true;
                    
                $author = $data['user_id'];
            }
                
            if ($data['category'] == 'new')
            {
                $regenerate_titles = true;
                    
                $category_id = $data['category_id'];
                $form_id = RSDirectoryHelper::getCategoryInheritedFormId($data['category_id']);
            }
                
            $parents = array();
                
            foreach ($entries as $entry)
            {
                // Remember the old entry id.
                $old_entry_id = $entry->id;
                    
                unset($entry->id);
                    
                if ( !empty($author) )
                {
                    $entry->user_id = $author;
                }
                    
                if ( !empty($category_id) )
                {
                    $entry->category_id = $category_id;
                    $entry->form_id = $form_id;
                }
                    
                $db->insertObject('#__rsdirectory_entries', $entry, 'id');
                    
                // Remember the old - new entry id pair.
                $parents[$old_entry_id] = $entry->id;
                    
                $entries_ids[] = $entry->id;
            }
                
            // #__rsdirectory_entries_credits
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_entries_credits') )
                   ->where( $db->qn('entry_id') . ' IN (' . $ids . ')' );
                   
            $db->setQuery($query);
                
            if ( $list = $db->loadObjectList() )
            {
                $columns = $db->getTableColumns('#__rsdirectory_entries_credits');
                    
                // Unset the id column.
                unset($columns['id']);
                    
                $query = $db->getQuery(true)
                       ->insert( $db->qn('#__rsdirectory_entries_credits') )
                       ->columns( $db->qn( array_keys($columns) ) );
                    
                foreach ($list as $object)
                {
                    // Unset the id column.
                    unset($object->id);
                        
                    $object->entry_id = $parents[$object->entry_id];
                        
                    if ( !empty($author) )
                    {
                        $object->user_id = $author;
                    }
                        
                    $values = (array)$object;
                        
                    foreach ($values as &$value)
                    {
                        $value = $db->q($value);
                    }
                        
                    $query->values( implode(',', $values) );
                }
                    
                $db->setQuery($query);
                $db->execute();
            }
                
            // #__rsdirectory_entries_custom
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_entries_custom') )
                   ->where( $db->qn('entry_id') . ' IN (' . $ids . ')' );
                   
            $db->setQuery($query);
                
            if ( $list = $db->loadObjectList() )
            {
                $columns = $db->getTableColumns('#__rsdirectory_entries_custom');
                    
                $query = $db->getQuery(true)
                       ->insert( $db->qn('#__rsdirectory_entries_custom') )
                       ->columns( $db->qn( array_keys($columns) ) );
                    
                foreach ($list as $object)
                {
                    $object->entry_id = $parents[$object->entry_id];
                        
                    $values = (array)$object;
                        
                    foreach ($values as &$value)
                    {
                        $value = $db->q($value);
                    }
                      
                    $query->values( implode(',', $values) );  
                }
                       
                $db->setQuery($query);
                $db->execute();
            }
                
            // #__rsdirectory_uploaded_files_fields_relations
            $query = $db->getQuery(true)
                   ->select('*')
                   ->from( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
                   ->where( $db->qn('entry_id') . ' IN (' . $ids . ')' );
                   
            $db->setQuery($query);
                
            if ( $list = $db->loadObjectList() )
            {
                jimport('joomla.filesystem.folder');
                    
                $files_ids = array();
                    
                foreach ($list as $object)
                {
                    $files_ids[] = $db->q($object->file_id);
                }
                    
                // #__rsdirectory_uploaded_files
                $query = $db->getQuery(true)
                       ->select('*')
                       ->from( $db->qn('#__rsdirectory_uploaded_files') )
                       ->where( $db->qn('id') . ' IN (' . implode(',', $files_ids) . ')' );
                       
                $db->setQuery($query);
                    
                $files_list = $db->loadObjectList();
                    
                $values = array();
                    
                foreach ($entries as $entry)
                {
                    $path = JPATH_COMPONENT_SITE . '/files/entries/';
                        
                    // Find the old entry id.
                    $old_entry_id = array_search($entry->id, $parents);
                        
                    // Proceed if the folder exists and was copied.
                    if ( file_exists($path . $old_entry_id) && JFolder::copy($old_entry_id, $entry->id, $path, true) )
                    {
                        foreach ($list as $object)
                        {
                            if ($object->entry_id == $old_entry_id)
                            {
                                foreach ($files_list as $file)
                                {
                                    if ($object->file_id == $file->id)
                                    {
                                        $new_file = clone $file;
                                            
                                        unset($new_file->id);
                                            
                                        if ( !empty($author) )
                                        {
                                            $new_file->user_id = $author;
                                        }
                                            
                                        $db->insertObject('#__rsdirectory_uploaded_files', $new_file, 'id');
                                            
                                        $new_object = clone $object;
                                        $object->file_id = $new_file->id;
                                        $object->entry_id = $entry->id;
                                           
                                        $array = (array)$object;
                                            
                                        foreach ($array as &$value)
                                        {
                                            $value = $db->q($value);
                                        }
                                            
                                        $values[] = implode(',', $array);
                                    }
                                }
                            }
                        }
                    }
                }
                    
                if ($values)
                {
                    $columns = $db->getTableColumns('#__rsdirectory_uploaded_files_fields_relations');
                        
                    $query = $db->getQuery(true)
                       ->insert( $db->qn('#__rsdirectory_uploaded_files_fields_relations') )
                       ->columns( $db->qn( array_keys($columns) ) )
                       ->values($values);
                       
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
            
        // Regenerate titles.
        if ( $regenerate_titles && !empty($entries_ids) )
        {
            // Get entries.
            $entries = RSDirectoryHelper::getEntriesObjectListByIds($entries_ids);
                
            // Regenerate titles.
            RSDirectoryHelper::regenerateEntriesTitles($entries);
        }
            
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
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/adapters/fieldset.php');
            
        return new RSFieldset();
    }
}