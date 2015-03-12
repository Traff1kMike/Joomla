<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * The Forms view.
 */
class RSDirectoryViewForms extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
        $layout = $this->getLayout();
            
        if ($layout == 'default')
        {
            $this->addToolbar();
            $this->sidebar = $this->get('Sidebar');
            $this->filterbar = $this->get('FilterBar');
            $this->items = $this->get('Items');
            $this->pagination = $this->get('Pagination');
            $this->state = $this->get('State');
            $this->isJ30 = RSDirectoryHelper::isJ30();
        }
        else if ($layout == 'modal')
        {
            require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/modalitems.php';
                
            // Get the forms.
            $forms = RSDirectoryHelper::getForms();
                
            // Initialize the items array.
            $items = array();
                
            foreach ($forms as $form)
            {
                $items[] = array(
                    'text' => $form->title,
                    'onclick' => "window.parent.assign2Form($form->id)",
                );
            }
                
            $options = array(
                'title' => JText::_('COM_RSDIRECTORY_ASSIGN_TO_FORM'),
                'accordion' => 0,
                'groups' => array(
                    array(
                        'accordion' => 0,
                        'items' => $items,
                    ),
                ),
            );
                
            $this->rsmodalitems = RSModalItems::getInstance($options); 
        }
            
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_FORMS'), 'rsdirectory' );
            
        JToolbarHelper::addNew('form.add');
        JToolbarHelper::editList('form.edit');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'forms.delete' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('forms');
    }
}