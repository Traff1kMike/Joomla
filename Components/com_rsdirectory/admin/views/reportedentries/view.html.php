<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Reported Entries view.
 */
class RSDirectoryViewReportedEntries extends JViewLegacy
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
     * @access private
     */
    private function addToolbar()
    {
        // Set title.
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_REPORTED_ENTRIES'), 'rsdirectory' );
            
        JToolBarHelper::publishList( 'reportedentries.publish', JText::_('COM_RSDIRECTORY_MARK_AS_READ') );
        JToolBarHelper::unpublishList( 'reportedentries.unpublish', JText::_('COM_RSDIRECTORY_MARK_AS_UNREAD') );
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'reportedentries.delete' );
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('reportedentries');
    }
        
    /**
	 * Returns a mark as read/unread button.
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
	public function markRead($value, $i, $prefix = '', $enabled = true, $checkbox = 'cb')
	{
		if ( is_array($prefix) )
		{
			$options = $prefix;
			$enabled = empty($options['enabled']) ? $enabled : $options['enabled'];
			$checkbox = empty($options['checkbox']) ? $checkbox : $options['checkbox'];
			$prefix = empty($options['prefix']) ? '' : $options['prefix'];
		}
            
		$states = array(
            1 => array('unpublish', 'COM_RSDIRECTORY_READ', 'COM_RSDIRECTORY_MARK_AS_UNREAD', 'COM_RSDIRECTORY_READ', true, 'publish', 'publish'),
			0 => array('publish', 'COM_RSDIRECTORY_UNREAD', 'COM_RSDIRECTORY_MARK_AS_READ', 'COM_RSDIRECTORY_UNREAD', true, 'unpublish', 'unpublish')
        );
            
		if ( !class_exists('JHtmlJGrid') )
		{
			require_once(JPATH_LIBRARIES . '/joomla/html/html/jgrid.php');
		}
			
		return JHtmlJGrid::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
	}
}