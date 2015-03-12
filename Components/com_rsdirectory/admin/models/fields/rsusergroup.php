<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Supports a nested check box field listing user groups.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldRSUsergroup extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 *
	 * @access protected
	 */
	protected $type = 'rsusergroup';
		
	/**
	 * Method to get the user group field input markup.
	 *
	 * @access protected
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		$options = array();
		$attr = '';
			
		// Initialize some field attributes.
		$attr .= empty($this->class) ? '' : ' class="' . $this->class . '"';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= $this->size ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
			
		// Initialize JavaScript field attributes.
		$attr .= empty($this->onchange) ? '' : ' onchange="' . $this->onchange . '"';
		$attr .= empty($this->onclick) ? '' : ' onclick="' . $this->onclick . '"';
			
		// Iterate through the children and build an array of options.
		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
				continue;
				
			$disabled = (string)$option['disabled'];
			$disabled = $disabled == 'true' || $disabled == 'disabled' || $disabled == '1';
				
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_( 'select.option', (string)$option['value'], trim( (string)$option ), 'value', 'text', $disabled );
				
			// Set some option attributes.
			$tmp->class = (string)$option['class'];
				
			// Set some JavaScript option attributes.
			$tmp->onclick = (string)$option['onclick'];
				
			// Add the option object to the result set.
			$options[] = $tmp;
		}
			
		$db = JFactory::getDbo();
			
		$query = $db->getQuery(true)
			->select( array( $db->qn('a.id', 'value'), $db->qn('a.title', 'text'), 'COUNT(DISTINCT ' . $db->qn('b.id') . ') AS ' . $db->qn('level') ) )
			->from( $db->qn('#__usergroups', 'a') )
			->leftJoin( $db->qn('#__usergroups', 'b') . ' ON ' . $db->qn('a.lft') . ' > ' . $db->qn('b.lft') . ' AND ' . $db->qn('a.rgt') . ' < ' . $db->qn('b.rgt') )
			->group( $db->qn( array('a.id', 'a.title', 'a.lft', 'a.rgt') ) )
			->order( $db->qn('a.lft') . ' ASC' );
			
		$db->setQuery($query);
			
		if ( $jgroups = $db->loadObjectList() )
		{	
			$query = $db->getQuery(true)
				   ->select( $db->qn('jgroup_id') )
				   ->from( $db->qn('#__rsdirectory_groups_relations') );
				   
			// Exclude the jgroups found in the currently edited group.
			if ( $id = JFactory::getApplication()->input->getInt('id') )
			{
				$query->where( $db->qn('group_id') . ' != ' . $db->q($id) );
			}
				
			$db->setQuery($query);
			$jgroups_ids = $db->loadColumn();
				
			foreach ($jgroups as $jgroup)
			{
				$text = str_repeat('- ', $jgroup->level) . $jgroup->text;
				$opts = array( 'disable' => $jgroups_ids && in_array($jgroup->value, $jgroups_ids) );
					
				$options[] = JHtml::_('select.option', $jgroup->value, $text, $opts);
			}
		}
			
		return JHtml::_( 'select.genericlist', $options, $this->name, array('list.attr' => $attr, 'list.select' => $this->value) );
	}
}
