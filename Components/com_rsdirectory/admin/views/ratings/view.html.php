<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Ratings view.
 */
class RSDirectoryViewRatings extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        $doc = JFactory::getDocument();
            
        $this->addToolbar();
        $this->sidebar = $this->get('Sidebar');
        $this->filterbar = $this->get('FilterBar');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->isJ30 = RSDirectoryHelper::isJ30();
            
        $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );
        $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/script.js?v=' . RSDirectoryVersion::$version );
            
        parent::display($tpl);
    }
        
    /**
     * Add toolbar.
     *
     * @access protected
     */
    protected function addToolBar()
    {
        // Set title.
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_REVIEWS'), 'rsdirectory' );
            
        JToolbarHelper::addNew('rating.add');
        JToolbarHelper::editList('rating.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('ratings.publish');
        JToolBarHelper::unpublishList('ratings.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'ratings.delete' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('ratings');
    }
}