<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Group view.
 */
class RSDirectoryViewGroup extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the group id.
        $id = JFactory::getApplication()->input->getInt('id');
            
        if ($id)
        {
            // Get the group.
            $group = JTable::getInstance('Group', 'RSDirectoryTable');
            $group->load($id);
                
            if (!$group->id)
            {
                JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
            }
        }
            
            
        $this->addToolBar();
        $this->id = $id;
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
        if ( JFactory::getApplication()->input->getInt('id') )
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_GROUP'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_GROUP'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('groups');
            
        JToolBarHelper::apply('group.apply');
        JToolBarHelper::save('group.save');
        JToolBarHelper::save2new('group.save2new');
        JToolBarHelper::save2copy('group.save2copy');
        JToolBarHelper::cancel('group.cancel');
    }
}