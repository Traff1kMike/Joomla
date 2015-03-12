<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Category view.
 */
class RSDirectoryViewCategory extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get mainframe.
        $app = JFactory::getApplication();
            
        // Get the JInput object.
        $jinput = $app->input;
        
        // Get the category id.
        $id = $jinput->getInt('id');
            
        if ($id)
        {
            // Get the category.
            $item = $this->get('Item');
                
            if (!$item->id)
            {
                JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
            }
                
            if ( !RSDirectoryHelper::getCategoryInheritedFormId($id) )
            {    
                if ( RSDirectoryHelper::getFormsCount() )
                {
                    $msg = JText::sprintf( 'COM_RSDIRECTORY_CURRENT_CATEGORY_NO_FORM_CONFIGURED_CHOOSE_OR_ADD', JRoute::_("index.php?option=com_rsdirectory&task=form.add&category_id=$id") );
                }
                else
                {
                    $msg = JText::sprintf( 'COM_RSDIRECTORY_CURRENT_CATEGORY_NO_FORM_CONFIGURED_ADD', JRoute::_("index.php?option=com_rsdirectory&task=form.add&category_id=$id") );    
                }
                    
                $app->enqueueMessage($msg, 'warning');
            }
        }   
            
        // Set the extension.
        $jinput->set('extension', 'com_rsdirectory');
            
        $this->addToolBar();
        $this->id = $id;
        $this->rstabs = $this->get('RSTabs');
        $this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
            
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
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_CATEGORY'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_CATEGORY'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('categories');
            
        JToolBarHelper::apply('category.apply');
        JToolBarHelper::save('category.save');
        JToolBarHelper::save2new('category.save2new');
        JToolBarHelper::save2copy('category.save2copy');
        JToolBarHelper::cancel('category.cancel');
    }
}