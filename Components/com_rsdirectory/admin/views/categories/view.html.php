<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories view.
 */
class RSDirectoryViewCategories extends JViewLegacy
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
        // Get the layout.
        $layout = $this->getLayout();
            
        if ($layout == 'modal')
        {
            $categories = RSDirectoryHelper::getSubcategories(0);
                
            // Initialize the options array.    
            $options = array(
                JHtml::_( 'select.option', 0, JText::_('JOPTION_SELECT_CATEGORY') ),
            );
                
            $this->categories = $categories;
            $this->categories_select = RSDirectoryHelper::getCategoriesSelect($categories);
        }
        else
        {
            $this->addToolbar();
            $this->sidebar = $this->get('Sidebar');
            $this->filterbar = $this->get('FilterBar');
            $this->pagination = $this->get('Pagination');
            $this->state = $this->get('State');
            $this->isJ30 = RSDirectoryHelper::isJ30();
                
            $items = $this->get('Items');
                
            $this->items = $items;
                
            // Preprocess the list of items to find ordering divisions.
            foreach ($items as &$item)
            {
                $this->ordering[$item->parent_id][] = $item->id;
            }
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_CATEGORIES'), 'rsdirectory' );
            
        JToolbarHelper::addNew('category.add');
        JToolbarHelper::editList('category.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('categories.publish');
        JToolBarHelper::unpublishList('categories.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'categories.delete' );
        JToolBarHelper::divider();
        JToolbarHelper::custom('categories.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
            
        $title = JText::_('COM_RSDIRECTORY_BATCH_ADD');
            
        if ( RSDirectoryHelper::isJ30() )
        {
            $dhtml = '<button class="btn btn-small" onclick="Joomla.submitbutton(\'categoriesbatch.add\');"><i class="icon-checkbox-partial" title="' . $title . '"></i> ' . $title . '</button>';    
        }
        else
        {
            $dhtml = '<a class="toolbar" onclick="Joomla.submitbutton(\'categoriesbatch.add\');" href="#"><span style="background: url(\'' . JURI::root(true) .  '/media/com_rsdirectory/images/icon-32-plus.png\');"></span>' . $title . '</a>';
        }
            
        JToolBar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('categories');
    }
}