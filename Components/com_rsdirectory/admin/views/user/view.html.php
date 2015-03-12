<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * User view.
 */
class RSDirectoryViewUser extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the user data.
        if ( !$this->item = $this->get('Item') )
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
            
            
        $this->addToolBar();
        $this->id = JFactory::getApplication()->input->getInt('id');
        $this->rstabs = $this->get('RSTabs');
        $this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
        $this->fieldsets = $this->form->getFieldsets();
            
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_USER'), 'rsdirectory' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('users');
            
        JToolBarHelper::apply('user.apply');
        JToolBarHelper::save('user.save');
        JToolBarHelper::cancel('user.cancel');
    }
}