<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Form view.
 */
class RSDirectoryViewForm extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the JInput object.
        $jinput = JFactory::getApplication()->input;
            
        // Get the form id.
        $id = $jinput->getInt('id');
            
        if ($id)
        {
            // Get the form.
            $form = JTable::getInstance('Form', 'RSDirectoryTable');
            $form->load($id);
                
            if (!$form->id)
            {
                JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
            }
        }
            
            
        $this->addToolBar();
        $this->id = $id;
        $this->category_id = $jinput->getInt('category_id');
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
        if ( JFactory::getApplication()->input->getInt('id') )
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_FORM'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_FORM'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('forms');
            
        JToolBarHelper::apply('form.apply');
        JToolBarHelper::save('form.save');
        JToolBarHelper::save2new('form.save2new');
        JToolBarHelper::save2copy('form.save2copy');
        JToolBarHelper::cancel('form.cancel');
    }
}