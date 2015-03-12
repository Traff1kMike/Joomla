<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Rating view.
 */
class RSDirectoryViewRating extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the review id.
        if ( $id = JFactory::getApplication()->input->getInt('id') )
        {
            // Get the review.
            if ( !$this->item = $this->get('Item') )
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
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_REVIEW'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_REVIEW'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('ratings');
            
        JToolBarHelper::apply('rating.apply');
        JToolBarHelper::save('rating.save');
        JToolBarHelper::save2new('rating.save2new');
        JToolBarHelper::save2copy('rating.save2copy');
        JToolBarHelper::cancel('rating.cancel');
    }
}