<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Updates view.
 */
class RSDirectoryViewUpdates extends JViewLegacy
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
        $this->addToolBar();
		$this->hash = $this->get('hash');
		$this->jversion = $this->get('joomlaVersion');
		$this->sidebar = $this->get('Sidebar');
			
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
		JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_UPDATES'), 'rsdirectory' );
			
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('updates');
    }
}