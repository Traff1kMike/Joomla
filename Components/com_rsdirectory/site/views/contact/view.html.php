<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Contact view.
 */
class RSDirectoryViewContact extends JViewLegacy
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
		$this->contact_captcha = RSDirectoryHelper::checkUserPermission('contact_captcha');
		$this->config = RSDirectoryConfig::getInstance();
        $this->form = $this->get('Form');
		$this->skipped_fields = $this->get('SkippedFields');
			
		JFactory::getDocument()->addStyleSheet( JURI::root(true) . '/media/com_rsdirectory/css/iframe.css?v=' . RSDirectoryVersion::$version );
			
		parent::display($tpl);
    }
}