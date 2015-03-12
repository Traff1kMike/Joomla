<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entries view.
 */
class RSDirectoryViewEntries extends JViewLegacy
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
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
            
        // Check for errors.
        if ( $errors = $this->get('Errors') )
        {
            JError::raiseError( 500, implode("\n", $errors) );
            return false;
        }
            
        // Get the layout.
        $layout = $this->getLayout();
            
        if ($layout == 'modal')
        {
            $this->status_options = array(
                JHtml::_( 'select.option', 1, JText::_('JPUBLISHED') ),
                JHtml::_( 'select.option', 0, JText::_('JUNPUBLISHED') ),
            );
        }
        else
        {
            $this->addToolbar();
            $this->sidebar = $this->get('Sidebar');
            $this->filterbar = $this->get('FilterBar');
            $this->isJ30 = RSDirectoryHelper::isJ30();
            $this->rsfieldset = $this->get('RSFieldset');
            $this->batch_form = $this->get('BatchForm');
				
			JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
			JText::script('COM_RSDIRECTORY_FILL_IN_REQUIRED_FIELDS');
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
        JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_ENTRIES'), 'rsdirectory' );
            
        JToolbarHelper::addNew('entry.add');
        JToolbarHelper::editList('entry.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList('entries.publish');
        JToolBarHelper::unpublishList('entries.unpublish');
        JToolBarHelper::divider();
        JToolBarHelper::deleteList( JText::_('COM_RSDIRECTORY_CONFIRM_DELETE'), 'entries.delete' );
        JToolBarHelper::divider();
            
        $title = JText::_('COM_RSDIRECTORY_BATCH');
            
        if ( RSDirectoryHelper::isJ30() )
        {
            $dhtml = '<button class="btn btn-small" onclick="if(document.adminForm.boxchecked.value==0){alert(\'' . JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST') . '\');}else{ jQuery( document.getElementById(\'batchModal\') ).modal();};"><i class="icon-checkbox-partial" title="' . $title . '"></i> ' . $title . '</button>';
				
			JToolBar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');
        }
            
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php';
        RSDirectoryToolbarHelper::addToolbar('entries');
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