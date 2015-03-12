<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transaction view.
 */
class RSDirectoryViewTransaction extends JViewLegacy
{
    public function display($tpl = null)
    {
        // Get the transaction.
        $item = $this->get('Item');
            
        if (!$item)
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
            
        $this->addToolBar();
        $this->item = $item;
        $this->rsfieldset = $this->get('RSFieldset');
            
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_TRANSACTION'), 'rsdirectory' );
            
        JToolBarHelper::publishList( 'transactions.publish', JText::_('COM_RSDIRECTORY_MARK_AS_FINALIZED') );
        JToolBarHelper::unpublishList( 'transactions.unpublish', JText::_('COM_RSDIRECTORY_MARK_AS_PENDING') );
        JToolBarHelper::cancel( 'transaction.cancel', JText::_('COM_RSDIRECTORY_CLOSE') );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('transactions');
    }
}