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
 * Comments options field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldComments extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'comments';
        
    /**
     * Method to get the field options.
     *
     * @access protected
     * 
     * @return array The field option objects.
     */
    protected function getOptions()
    {
        // Get the installed commenting systems.
        $installed = RSDirectoryHelper::get_comment_systems();
            
        // Initialize the options array.
        $options = array();
            
        if ($installed)
        {
            foreach ($installed as $value => $text)
            {
                $options[] = JHtml::_('select.option', $value, $text);
            }
        }
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        return $options;
    }
}