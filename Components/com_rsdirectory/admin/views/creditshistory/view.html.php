<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Credits History view.
 */
class RSDirectoryViewCreditsHistory extends JViewLegacy
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_CREDITS_HISTORY'), 'rsdirectory' );
            
        JToolBarHelper::publishList( 'creditshistory.markAsPaid', JText::_('COM_RSDIRECTORY_MARK_AS_PAID') );
        JToolBarHelper::unpublishList( 'creditshistory.markAsUnpaid', JText::_('COM_RSDIRECTORY_MARK_AS_UNPAID') );
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'creditshistory.delete' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('creditshistory');
    }
        
    /**
	 * Returns a mark as paid/unpaid buttons group.
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
	public function markPaid($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
	{
		if ( is_array($prefix) )
		{
			$options = $prefix;
			$enabled = empty($options['enabled']) ? $enabled : $options['enabled'];
			$checkbox = empty($options['checkbox']) ? $checkbox : $options['checkbox'];
			$prefix = empty($options['prefix']) ? '' : $options['prefix'];
		}
            
		$states = array(
            1 => array('markAsUnpaid', 'COM_RSDIRECTORY_PAID', 'COM_RSDIRECTORY_MARK_AS_UNPAID', 'COM_RSDIRECTORY_PAID', true, 'publish', 'publish'),
			0 => array('markAsPaid', 'COM_RSDIRECTORY_UNPAID', 'COM_RSDIRECTORY_MARK_AS_PAID', 'COM_RSDIRECTORY_UNPAID', true, 'unpublish', 'unpublish'),
        );
            
		if ( !class_exists('JHtmlJGrid') )
		{
			require_once(JPATH_LIBRARIES . '/joomla/html/html/jgrid.php');
		}
			
		return JHtmlJGrid::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
	}
}