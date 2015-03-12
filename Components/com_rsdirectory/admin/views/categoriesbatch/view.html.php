<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Categories Batch view.
 */
class RSDirectoryViewCategoriesBatch extends JViewLegacy
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_CATEGORIES_BATCH'), 'rsdirectory' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('categoriesbatch');
            
        JToolBarHelper::apply('categoriesbatch.apply');
        JToolBarHelper::save('categoriesbatch.save');
        JToolBarHelper::cancel('categoriesbatch.cancel');
    }
}