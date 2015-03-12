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
    function display($tpl = null)
    {
		// Set the view layout.
		$this->_layout = 'default_xml';
			
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		
		// Gets and sets timezone offset from site configuration
		$this->tz = new DateTimeZone($app->getCfg('offset'));
		$now = JFactory::getDate();
		$now->setTimeZone($this->tz);
			
		$app->input->set( 'limit', $app->getCfg('feed_limit') );
		$this->items = $this->get('Items');
			
		parent::display($tpl);
    }
}