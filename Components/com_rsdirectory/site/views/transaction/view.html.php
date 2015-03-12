<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transaction view.
 */
class RSDirectoryViewTransaction extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
        // Get the transaction.
        $transaction = $this->get('Item');
            
        // Raise an error if no entry was found with that id.
        if (!$transaction)
        {
            JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
        }
			
		$app = JFactory::getApplication();
			
		// Build breadcrumb.
		$pathway = $app->getPathway();
			
		if ( !$app->input->get('Itemid') )
		{
			// Add breadcrumb item.
			$pathway->addItem(
				JText::_('COM_RSDIRECTORY_YOUR_ACCOUNT'),
				JRoute::_('index.php?option=com_rsdirectory&view=myaccount')
			);
		}
			
		$pathway->addItem( JText::_('COM_RSDIRECTORY_VIEW_TRANSACTION') );
            
        $this->transaction = $transaction;
            
        parent::display($tpl);
    }
}