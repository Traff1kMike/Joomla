<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entry Report view.
 */
class RSDirectoryViewEntryReport extends JViewLegacy
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
		$config = RSDirectoryConfig::getInstance();
		$this->config = $config;
		$this->form = $this->get('Form');
		$this->skipped_fields = $this->get('SkippedFields');
		$this->entry_id = JFactory::getApplication()->input->getInt('entry_id');
			
		JFactory::getDocument()->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/iframe.css?v=' . RSDirectoryVersion::$version );
			
		parent::display($tpl);
    }
}