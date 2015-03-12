<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Configuration view.
 */
class RSDirectoryViewConfiguration extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        // Add the toolbar.
        $this->addToolbar();
            
        $this->sidebar = $this->get('Sidebar');
        $this->rstabs = $this->get('RSTabs');
        $this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
		$this->isJ30 = RSDirectoryHelper::isJ30();
            
        parent::display($tpl);
    }
        
    /**
     * Add toolbar.
     *
     * @access private
     */
    private function addToolbar()
    {
		// Set title.
		JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_CONFIGURATION'), 'rsdirectory' );
			
        // Add toolbar buttons.
        JToolBarHelper::apply('configuration.apply');
        JToolBarHelper::save('configuration.save');
        JToolBarHelper::cancel('configuration.cancel');
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('configuration');
    }
}