<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * The Fields view.
 */
class RSDirectoryViewFields extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
            
        // Get the layout.
        $layout = $this->getLayout();
            
        if ($layout == 'modal')
        {
            $this->status_options = array(
                JHtml::_( 'select.option', 1, JText::_('JPUBLISHED') ),
                JHtml::_( 'select.option', 0, JText::_('JUNPUBLISHED') ),
            );
                
            // Get an instance of the Forms model.
            $forms_model = RSDirectoryModel::getInstance('Forms');
                
            $this->form_options = $forms_model->getFormsOptions();
        }
        else
        {
            $this->addToolbar();
            $this->sidebar = $this->get('Sidebar');
            $this->filterbar = $this->get('FilterBar');
            $this->isJ30 = RSDirectoryHelper::isJ30();
        }
            
        $this->config = RSDirectoryConfig::getInstance();
            
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_FIELDS'), 'rsdirectory' );
            
        JToolbarHelper::addNew('field.add');
        JToolbarHelper::editList('field.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('fields.publish');
        JToolBarHelper::unpublishList('fields.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'fields.delete' );
        JToolBarHelper::divider();
            
        $title = JText::_('COM_RSDIRECTORY_ASSIGN_TO_FORM');
            
        if ( RSDirectoryHelper::isJ30() )
        {
            $dhtml = '<button class="btn btn-small" onclick="if(document.adminForm.boxchecked.value==0){alert(\'' . JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST') . '\');}else{ Joomla.submitbutton(\'fields.assign2form\')};"><i class="icon-checkbox-partial" title="' . $title . '"></i> ' . $title . '</button>';    
        }
        else
        {
            $dhtml = '<a class="toolbar" onclick="if(document.adminForm.boxchecked.value==0){alert(\'' . JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST') . '\');}else{ Joomla.submitbutton(\'fields.assign2form\')};" href="#"><span style="background: url(\'' . JURI::root(true) .  '/media/com_rsdirectory/images/icon-32-plus.png\');"></span>' . $title . '</a>';
        }
            
        JToolBar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'assign2form');
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('fields');
    }
}