<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Fields List view.
 */
class RSDirectoryViewList extends JViewLegacy
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
		$state = $this->get('State');
			
		// Get the page/component configuration.
		$params = $state->params;
			
		// Get the field id.
		$id =  JFactory::getApplication()->input->getInt('id');
			
		$field = RSDirectoryHelper::getField($id);
			
		if (!$field)
		{
			JError::raiseError( 404, JText::_('COM_RSDIRECTORY_PAGE_NOT_FOUND') );
		}
			
		$this->params = $params;
		$this->pageclass_sfx = htmlspecialchars( $params->get('pageclass_sfx') );
		$this->items = $this->get('Items');
		$this->Itemid = $this->params->get('itemid');
		$this->id = $id;
		$this->field = $field;
			
		parent::display($tpl);
	}
}