<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The EmailMessages view.
 */
class RSDirectoryViewEmailMessages extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        $this->addToolbar();
        $this->sidebar = $this->get('Sidebar');
        $this->filterbar = $this->get('FilterBar');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->isJ30 = RSDirectoryHelper::isJ30();
            
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EMAIL_MESSAGES'), 'rsdirectory' );
            
        JToolbarHelper::addNew('emailmessage.add');
        JToolbarHelper::editList('emailmessage.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('emailmessages.publish');
        JToolBarHelper::unpublishList('emailmessages.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'emailmessages.delete' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('emailmessages');
    }
}