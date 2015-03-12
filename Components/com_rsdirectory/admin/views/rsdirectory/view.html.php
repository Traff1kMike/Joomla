<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * RSDirectory view.
 */
class RSDirectoryViewRSDirectory extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        // Add toolbar.
        $this->addToolbar();
            
        $this->code = RSDirectoryConfig::getInstance()->get('code');
        $this->buttons = $this->get('Buttons');
        $this->sidebar = $this->get('Sidebar');
            
        parent::display($tpl);
    }
        
    /**
     * Add toolbar.
     *
     * @access private
     */
    private function addToolbar()
    {
        // Add the options button if the user is authorized.
        if ( JFactory::getUser()->authorise('core.admin', 'com_rsdirectory') )
        {
            JToolBarHelper::preferences('com_rsdirectory');
        }
            
        // Set title.
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_DASHBOARD'), 'rsdirectory' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('dashboard');
    }
}