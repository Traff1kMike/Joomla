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
 * Category Select options field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldCategorySelect extends JFormFieldList
{
    /**
     * A flexible category list that respects access controls
     *
     * @var string
     */
    public $type = 'categoryselect';
        
    /**
     * Method to get a list of categories that respects access controls and can be used for
     * either category assignment or parent category assignment in edit screens.
     * Use the parent element to indicate that the field will be used for assigning parent categories.
     *
     * @return array The field option objects.
     */
    protected function getOptions()
    {
        // Initialize the options array.
        $options = array();
            
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('a.id', 'value') . ', ' . $db->qn('a.title', 'text') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.published') )
               ->from( $db->qn('#__categories', 'a') )
               ->leftJoin( $db->qn('#__categories', 'b') . ' ON ' . $db->qn('a.lft') .  ' > ' . $db->qn('b.lft') . ' AND ' . $db->qn('a.rgt') . ' < ' . $db->qn('b.rgt') )
               ->where( '(' . $db->qn('a.extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('a.parent_id') . ' = ' . $db->q(0) . ')' )
               ->group( $db->qn('a.id') . ', ' . $db->qn('a.title') . ', ' . $db->qn('a.level') . ', ' . $db->qn('a.lft') . ', ' . $db->qn('a.rgt') . ', ' . $db->qn('a.extension') . ', ' . $db->qn('a.parent_id') . ', ' . $db->qn('a.published') )
               ->order( $db->qn('a.lft') . ' ASC' );
            
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
            
        foreach ($options as $option)
        {
            // Translate ROOT.
            if ($option->level == 0)
            {
                $option->value = 0;
                $option->text = JText::_('COM_RSDIRECTORY_ALL_CATEGORIES_OPTION');
            }
               
            // Pad the option text with spaces using depth level as a multiplier. 
            $option->text = str_repeat('- ', $option->level) . ($option->published == 1 ? $option->text : '[' . $option->text . ']') ;
        }
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        return $options;
    }
}