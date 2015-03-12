<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Tools view.
 */
class RSDirectoryViewTools extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * @param mixed $tpl
     */
    public function display($tpl = null)
    {
        // Add the toolbar.
        $this->addToolbar();
            
        $this->sidebar = $this->get('Sidebar');
        $this->rstabs = $this->get('RSTabs');
        $this->rsfieldset = $this->get('RSFieldset');
        $this->form = $this->get('Form');
		$this->data = JFactory::getApplication()->input->get( 'jform', array(), 'array' );
			
		$this->backup_errors = $this->get('BackupErrors');
		$this->backup_cached_files = $this->get('BackupCachedFiles');
			
		$this->restore_hidden_fields = $this->get('RestoreHiddenFields');
		$this->restore_errors = $this->get('RestoreErrors');
			
		$this->import_options = $this->get('ImportOptions');
			
		$this->isJ30 = RSDirectoryHelper::isJ30();
			
        JText::script('COM_RSDIRECTORY_REGENERATE_TITLES_SELECTION_ERROR');
		JText::script('COM_RSDIRECTORY_BACKUP_DELETE_SELECTION_ERROR');
		JText::script('COM_RSDIRECTORY_RESTORE_UPLOAD_AND_RESTORE_BUTTON');
		JText::script('COM_RSDIRECTORY_RESTORE_BUTTON');
		JText::script('COM_RSDIRECTORY_RESTORE_UPLOADED_ARCHIVE_ERROR');
		JText::script('COM_RSDIRECTORY_RESTORE_LOCAL_ARCHIVE_ERROR');
		JText::script('COM_RSDIRECTORY_RESTORE_URL_ERROR');
		JText::script('COM_RSDIRECTORY_RESTORE_ABORTED');
		JText::script('COM_RSDIRECTORY_RESTORE_ABORTED_ON_USER_REQUEST');
		JText::script('COM_RSDIRECTORY_RESTORE_VERIFYING_DATA');
		JText::script('COM_RSDIRECTORY_RESTORE_MEASURING_DATA');
		JText::script('COM_RSDIRECTORY_RESTORING_DATA');
		JText::script('COM_RSDIRECTORY_RESTORE_SUCCESSFUL');
		JText::script('COM_RSDIRECTORY_IMPORT_UPLOAD_AND_IMPORT_BUTTON');
		JText::script('COM_RSDIRECTORY_IMPORT_BUTTON');
		JText::script('COM_RSDIRECTORY_IMPORT_SELECT_OPTION_ERROR');
		JText::script('COM_RSDIRECTORY_IMPORT_RUNNING_VERIFICATION');
		JText::script('COM_RSDIRECTORY_IMPORT_ABORTED');
		JText::script('COM_RSDIRECTORY_IMPORT_ABORTED_ON_USER_REQUEST');
		JText::script('COM_RSDIRECTORY_IMPORT_MEASURING_DATA');
		JText::script('COM_RSDIRECTORY_IMPORT_SUCCESSFUL');
            
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
		JToolBarHelper::title( JText::_('COM_RSDIRECTORY_PAGE_TOOLS'), 'rsdirectory' );
			
        // Add toolbar buttons.
        JToolBarHelper::cancel('tools.cancel', 'JTOOLBAR_CLOSE');
            
        require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/toolbar.php');
        RSDirectoryToolbarHelper::addToolbar('tools');
    }
}