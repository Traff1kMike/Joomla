<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Transactions view.
 */
class RSDirectoryViewTransactions extends JViewLegacy
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
        $doc = JFactory::getDocument();
            
        $this->addToolbar();
        $this->sidebar = $this->get('Sidebar');
        $this->filterbar = $this->get('FilterBar');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->isJ30 = RSDirectoryHelper::isJ30();
            
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_TRANSACTIONS'), 'rsdirectory' );
            
        JToolBarHelper::publishList( 'transactions.publish', JText::_('COM_RSDIRECTORY_MARK_AS_FINALIZED') );
        JToolBarHelper::unpublishList( 'transactions.unpublish', JText::_('COM_RSDIRECTORY_MARK_AS_PENDING') );
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'transactions.delete' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('transactions');
    }
    
    /**
	 * Returns a mark as finalized/pending button.
	 *
	 * @access public
	 *
	 * @param int $value The state value.
	 * @param int $i The row index
	 * @param string|array $prefix An optional task prefix or an array of options.
	 * @param bool $enabled An optional setting for access control on the action.
	 * @param string $checkbox An optional prefix for checkboxes.
	 *
	 * @return string The Html code.
	 *
	 * @see JHtmlJGrid::state
	 */
	public function markFinalized($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
	{
		if ( is_array($prefix) )
		{
			$options = $prefix;
			$enabled = empty($options['enabled']) ? $enabled : $options['enabled'];
			$checkbox = empty($options['checkbox']) ? $checkbox : $options['checkbox'];
			$prefix = empty($options['prefix']) ? '' : $options['prefix'];
		}
            
		$states = array(
            1 => array('unpublish', 'COM_RSDIRECTORY_FINALIZED', 'COM_RSDIRECTORY_MARK_AS_PENDING', 'COM_RSDIRECTORY_FINALIZED', true, 'publish', 'publish'),
			0 => array('publish', 'COM_RSDIRECTORY_PENDING', 'COM_RSDIRECTORY_MARK_AS_FINALIZED', 'COM_RSDIRECTORY_PENDING', true, 'unpublish', 'unpublish')
        );
            
		if ( !class_exists('JHtmlJGrid') )
		{
			require_once(JPATH_LIBRARIES . '/joomla/html/html/jgrid.php');
		}
            
		return JHtmlJGrid::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
	}
}