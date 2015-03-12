<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access');

class RSDirectoryViewCreditPackage extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the credit package id.
        $id = JFactory::getApplication()->input->getInt('id');
            
        if ($id)
        {
            // Get the credit package.
            $credit_package = JTable::getInstance('CreditPackage', 'RSDirectoryTable');
            $credit_package->load($id);
                
            if (!$credit_package->id)
            {
                JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
            }
        }
            
            
        $this->addToolBar();
        $this->id = $id;
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
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_EDIT_CREDIT_PACKAGE'), 'rsdirectory' );
        }
        else
        {
            JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ADD_CREDIT_PACKAGE'), 'rsdirectory' );
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('creditpackages');
            
        JToolBarHelper::apply('creditpackage.apply');
        JToolBarHelper::save('creditpackage.save');
        JToolBarHelper::save2new('creditpackage.save2new');
        JToolBarHelper::save2copy('creditpackage.save2copy');
        JToolBarHelper::cancel('creditpackage.cancel');
    }
}