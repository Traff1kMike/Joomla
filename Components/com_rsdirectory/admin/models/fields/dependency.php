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
 * Dependency field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldDependency extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'dependency';
        
    /**
     * Method to get the field options.
     *
     * @access protected
     * 
     * @return array The field option objects.
     */
    protected function getOptions()
    {
        // Get DBO.
        $db = JFactory::getDbo();
            
        $query = $db->getQuery(true)
               ->select( $db->qn('f.id', 'value') . ', ' . $db->qn('f.name', 'text') )
               ->from( $db->qn('#__rsdirectory_fields', 'f') )
               ->innerJoin( $db->qn('#__rsdirectory_field_types', 'ff') . ' ON ' . $db->qn('f.field_type_id') . ' = ' . $db->qn('ff.id') )
               ->where( $db->qn('ff.type') . ' IN (' . RSDirectoryHelper::quoteImplode( array('dropdown', 'radiogroup') ) . ')' );
               
        if ( $form_field_id = JFactory::getApplication()->input->getInt('id') )
        {
            // Exclude itself.
            $query->where( $db->qn('f.id') . ' != ' . $db->q($form_field_id) );
        }
            
        $db->setQuery($query);
            
        $options = $db->loadObjectList();
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        return $options;
    }
}