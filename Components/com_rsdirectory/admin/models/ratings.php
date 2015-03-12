<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Ratings model.
 */
class RSDirectoryModelRatings extends JModelList
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
                'r.published',
                'e.title',
                'entry_author',
                'r.score',
                'r.subject',
                'review_author',
                'r.name',
                'r.created_time',
                'r.id',
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
            
        // Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
            
        $select = array(
            $db->qn('r') . '.*',
            $db->qn('e.title'),
            $db->qn('u.id', 'entry_author_id'),
            $db->qn("u.$author", 'entry_author'),
            $db->qn('ru.id', 'review_author_id'),
            $db->qn("ru.$author", 'review_author'),
        );
            
        $query = $db->getQuery(true)
               ->select($select)
               ->from( $db->qn('#__rsdirectory_reviews', 'r') )
               ->leftJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('r.entry_id') . ' = ' . $db->qn('e.id') )
               ->leftJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->leftJoin( $db->qn('#__users', 'ru') . ' ON ' . $db->qn('r.user_id') . ' = ' . $db->qn('ru.id') );
               
        // Filter by status.  
        $published = $this->getState('filter.published');
            
        if ($published !== '')
        {
            $query->where( $db->qn('r.published') . ' = ' . $db->q($published) );
        }
            
        // Filter by entry.  
        $entry_id = $this->getState('filter.entry_id');
            
        if ($entry_id !== '')
        {
            $query->where( $db->qn('r.entry_id') . ' = ' . $db->q($entry_id) );
        }
            
        // Filter by entries author.  
        $entries_author_id = $this->getState('filter.entries_author_id');
            
        if ($entries_author_id !== '')
        {
            $query->where( $db->qn('e.user_id') . ' = ' . $db->q($entries_author_id) );
        }
        
        // Filter by rating.  
        $rating = $this->getState('filter.rating');
            
        if ($rating !== '')
        {
            $query->where( $db->qn('r.score') . ' = ' . $db->q($rating) );
        }
            
        // Filter by reviews author.  
        $reviews_author_id = $this->getState('filter.reviews_author_id');
            
        if ($reviews_author_id !== '')
        {
            $query->where( $db->qn('r.user_id') . ' = ' . $db->q($reviews_author_id) );
        }
            
        // Filter by reviews author email.  
        $reviews_author_email = $this->getState('filter.reviews_author_email');
            
        if ($reviews_author_email !== '')
        {
            $query->where( $db->qn('r.email') . ' = ' . $db->q($reviews_author_email) );
        }
            
        // Filter by search.
        $search = $this->getState('filter.search');
            
        if ($search)
        {
            $search = $db->q( '%' . str_replace( ' ', '%', $db->escape($search, true) ) . '%', false );
                
            $condition = array(
                $db->qn('e.title') . " LIKE $search",
                $db->qn('r.subject') . " LIKE $search",
                $db->qn('r.review') . " LIKE $search",
            );
                
            $query->where( '(' . implode(' OR ', $condition) . ')' );
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
     * 
     * @param string $ordering
     * @param string $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest("$this->context.filter.search", 'filter_search');
        $this->setState('filter.search', $search);
            
        $published = $this->getUserStateFromRequest("$this->context.filter.published", 'filter_published', '');
        $this->setState('filter.published', $published);
            
        $entry_id = $this->getUserStateFromRequest("$this->context.filter.entry_id", 'filter_entry_id', '');
        $this->setState('filter.entry_id', $entry_id);
            
        $entries_author_id = $this->getUserStateFromRequest("$this->context.filter.entries_author_id", 'filter_entries_author_id', '');
        $this->setState('filter.entries_author_id', $entries_author_id);
            
        $rating = $this->getUserStateFromRequest("$this->context.filter.rating", 'filter_rating', '');
        $this->setState('filter.rating', $rating);
            
        $reviews_author_id = $this->getUserStateFromRequest("$this->context.filter.reviews_author_id", 'filter_reviews_author_id', '');
        $this->setState('filter.reviews_author_id', $reviews_author_id);
            
        $reviews_author_email = $this->getUserStateFromRequest("$this->context.filter.reviews_author_email", 'filter_reviews_author_email', '');
        $this->setState('filter.reviews_author_email', $reviews_author_email);
            
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
        require_once JPATH_COMPONENT . '/helpers/adapters/filterbar.php';
            
        $sortFields = array(
            JHtml::_( 'select.option', 'r.published', JText::_('JSTATUS') ),
            JHtml::_( 'select.option', 'e.title', JText::_('COM_RSDIRECTORY_ENTRY') ),
            JHtml::_( 'select.option', 'entry_author', JText::_('COM_RSDIRECTORY_ENTRY_AUTHOR') ),
            JHtml::_( 'select.option', 'r.score', JText::_('COM_RSDIRECTORY_RATING') ),
            JHtml::_( 'select.option', 'r.subject', JText::_('COM_RSDIRECTORY_SUBJECT') ),
            JHtml::_( 'select.option', 'review_author', JText::_('COM_RSDIRECTORY_REVIEW_AUTHOR') ),
            JHtml::_( 'select.option', 'r.name', JText::_('COM_RSDIRECTORY_NAME') ),
            JHtml::_( 'select.option', 'r.created_time', JText::_('JGLOBAL_CREATED') ),
            JHtml::_( 'select.option', 'r.id', JText::_('JGRID_HEADING_ID') ),
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
                    'input' => '<select name="filter_reviews_author_email" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_REVIEWS_AUTHOR_EMAIL') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getReviewsAuthorsEmails(), 'value', 'text', $this->getState('filter.reviews_author_email'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_reviews_author_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_REVIEWS_AUTHOR') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getReviewsAuthors(), 'value', 'text', $this->getState('filter.reviews_author_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_rating" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_RATING') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getRatings(), 'value', 'text', $this->getState('filter.rating'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_entries_author_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_ENTRIES_AUTHOR') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEntriesAuthors(), 'value', 'text', $this->getState('filter.entries_author_id'), true ) .
                               '</select>',
                ),
                array(
                    'input' => '<select name="filter_entry_id" class="inputbox" onchange="this.form.submit()">' . "\n" .
                               '<option value="">' . JText::_('COM_RSDIRECTORY_SELECT_ENTRY') . '</option>' . "\n" .
                               JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id'), true ) .
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
            
        // Entries filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_ENTRY'),
            'filter_entry_id',
            JHtml::_( 'select.options', $this->getEntries(), 'value', 'text', $this->getState('filter.entry_id') )
        );
            
        // Entries authors filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_ENTRIES_AUTHOR'),
            'filter_entries_author_id',
            JHtml::_( 'select.options', $this->getEntriesAuthors(), 'value', 'text', $this->getState('filter.entries_author_id') )
        );
            
        // Ratings filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_RATING'),
            'filter_rating',
            JHtml::_( 'select.options', $this->getRatings(), 'value', 'text', $this->getState('filter.rating') )
        );
            
        // Reviews authors filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_REVIEWS_AUTHOR'),
            'filter_reviews_author_id',
            JHtml::_( 'select.options', $this->getReviewsAuthors(), 'value', 'text', $this->getState('filter.reviews_author_id') )
        );
            
        // Reviews authors emails filter.
        RSDirectoryToolbarHelper::addFilter(
            JText::_('COM_RSDIRECTORY_SELECT_REVIEWS_AUTHOR_EMAIL'),
            'filter_reviews_author_email',
            JHtml::_( 'select.options', $this->getReviewsAuthorsEmails(), 'value', 'text', $this->getState('filter.reviews_author_email') )
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
     * Build a list of entries.
     *
     * @access public
     *
     * @return mixed
     */
    public function getEntries()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('e.id', 'value') . ', ' . $db->qn('e.title', 'text') )
               ->from( $db->qn('#__rsdirectory_entries', 'e') )
               ->innerJoin( $db->qn('#__rsdirectory_reviews', 'r') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('r.entry_id') )
               ->group( $db->qn('e.id') )
               ->order( $db->qn('e.title') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of entries authors.
     *
     * @access public
     *
     * @return mixed
     */
    public function getEntriesAuthors()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('u.id', 'value') . ', ' . $db->qn('u.name', 'text') )
               ->from( $db->qn('#__users', 'u') )
               ->innerJoin( $db->qn('#__rsdirectory_entries', 'e') . ' ON ' . $db->qn('e.user_id') . ' = ' . $db->qn('u.id') )
               ->innerJoin( $db->qn('#__rsdirectory_reviews', 'r') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('r.entry_id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of ratings.
     *
     * @access public
     *
     * @return mixed
     */
    public function getRatings()
    {
        return array(
            (object)array(
                'value' => 0,
                'text' => 0,
            ),
            (object)array(
                'value' => 1,
                'text' => 1,
            ),
            (object)array(
                'value' => 2,
                'text' => 2,
            ),
            (object)array(
                'value' => 3,
                'text' => 3,
            ),
            (object)array(
                'value' => 4,
                'text' => 4,
            ),
            (object)array(
                'value' => 5,
                'text' => 5,
            ),
        );
    }
        
    /**
     * Build a list of reviews authors.
     *
     * @access public
     *
     * @return mixed
     */
    public function getReviewsAuthors()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('u.id', 'value') . ', ' . $db->qn('u.name', 'text') )
               ->from( $db->qn('#__users', 'u') )
               ->innerJoin( $db->qn('#__rsdirectory_reviews', 'r') . ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('r.user_id') )
               ->group( $db->qn('u.id') )
               ->order( $db->qn('u.name') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
        
    /**
     * Build a list of reviews authors emails.
     *
     * @access public
     *
     * @return mixed
     */
    public function getReviewsAuthorsEmails()
    {
        // Get DBO.
        $db = $this->getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('email', 'value') . ', ' . $db->qn('email', 'text') )
               ->from( $db->qn('#__rsdirectory_reviews') )
               ->where( $db->qn('email') . ' != ' . $db->q('') )
               ->group( $db->qn('email') )
               ->order( $db->qn('email') );
                
        $db->setQuery($query);
            
        return $db->loadObjectList();
    }
}