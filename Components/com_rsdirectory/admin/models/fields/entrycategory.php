<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * Entry Category Select options field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldEntryCategory extends JFormFieldList
{
    /**
     * A flexible category list that respects access controls
     *
     * @var string
     */
    public $type = 'entrycategory';

    /**
	 * Method to get the field input markup for a combo box field.
	 *
	 * @access protected
	 * 
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
        // Initialize the options array.
        $options = array();
			
		$db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
			   ->select( $db->qn('a.id', 'value') . ', ' . $db->qn('a.title', 'text') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.published') )
			   ->from( $db->qn('#__categories', 'a') )
			   ->leftJoin( $db->qn('#__categories', 'b') . ' ON ' . $db->qn('a.lft') .  ' > ' . $db->qn('b.lft') . ' AND ' . $db->qn('a.rgt') . ' < ' . $db->qn('b.rgt') )
			   ->where( $db->qn('a.extension') . ' = ' . $db->q('com_rsdirectory') )
			   ->group( $db->qn('a.id') . ', ' . $db->qn('a.title') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.lft') . ', ' . $db->qn('a.rgt') . ', ' . $db->qn('a.extension') . ', ' . $db->qn('a.parent_id') . ', ' . $db->qn('a.published') )
			   ->order( $db->qn('a.lft') . ' ASC' );
			   
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
			
		if ( $app->isSite() && $menu && !RSDirectoryHelper::checkUserPermission('can_edit_all_entries') )
		{
			$item = $menu->getActive();
				
			if ($item)
			{
				$catid = $item->params->get('catid');
					
				if ($catid)
				{
					$q = $db->getQuery(true)
				       ->select('*')
				       ->from( $db->qn('#__categories') )
					   ->where( $db->qn('id') . ' = ' . $db->q($catid) );
						
					$db->setQuery($q);
						
					$result = $db->loadObject();
						
					if ($result)
					{
						$q = $db->getQuery(true)
						   ->select('COUNT(*)')
						   ->from( $db->qn('#__categories') )
						   ->where( $db->qn('parent_id') . ' = ' . $db->q($result->id) );
						   
						$db->setQuery($q);
							
						if ( $db->loadResult() )
						{
							$query->where( $db->qn('a.lft') . ' > ' . $db->q($result->lft) )
						          ->where( $db->qn('a.rgt') . ' < ' . $db->q($result->rgt) );
						}
						else
						{
							$query->where( $db->qn('a.lft') . ' >= ' . $db->q($result->lft) )
						          ->where( $db->qn('a.rgt') . ' <= ' . $db->q($result->rgt) );
						}
					}
				}
				
			}
		}
			
        // Get the options.
        $db->setQuery($query);
            
        try
        {
            $options = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            JError::raiseWarning(500, $e->getMessage);
        }
            
		foreach ($options as $i => $option)
		{
			if ($option->value == $this->value)
			{
				$option->text = '[ ' . $option->text . ' ]';
			}
				
			// Pad the option text with spaces using depth level as a multiplier.
            $option->text = str_repeat('- ', $option->level - 1) . ($option->published == 1 ? $option->text : '[' . $option->text . ']');
				
			if ( isset($options[$i + 1]) && $options[$i + 1]->level > $option->level )
			{
				$option->disable = true;
			}
		}
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        // Initialize variables.
		$html = array();
		$attr = '';
			
		// Initialize some field attributes.
		$attr .= empty($this->class) ? '' : ' class="' . $this->class . '"';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= $this->size ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
		$attr .= ' data-current-category-id="' . $this->value . '"';
			
		// Initialize JavaScript field attributes.
		$attr .= empty($this->onchange) ? '' : ' onchange="' . $this->onchange . '"';
		$attr .= empty($this->onclick) ? '' : ' onclick="' . $this->onclick . '"';
			
		// Add the select field.	
		$html[] = JHtml::_( 'select.genericlist', $options, $this->name, array('list.attr' => $attr, 'list.select' => $this->value, 'id' => $this->id) );
			
		// Add the button.
		if ( !isset($this->element['change-button']) || $this->element['change-button'] == 'true' )
		{
			$html[] = '&nbsp;<button id="' . $this->id . '_change" class="' . RSDirectoryHelper::getTooltipClass() . ' btn" title="' . RSDirectoryHelper::getTooltipText( JText::_('COM_RSDIRECTORY_CHANGE_CATEGORY_DESC') ) . '" disabled="disabled">' . JText::_('COM_RSDIRECTORY_CHANGE_CATEGORY_LABEL') . '</button>';	
		}
			
		// Add loader.
		$html[] = '&nbsp;<img id="' . $this->id . '_loader" class="hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" alt="" width="16" height="16" />';
			
		return implode($html);
    }
}