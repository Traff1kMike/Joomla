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
 * Maps options field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldMaps extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'maps';
        
    /**
     * Method to get the field options.
     *
     * @access protected
     * 
     * @return array The field option objects.
     */
    protected function getOptions()
    {
		$maps = RSDirectoryHelper::getFormFields(null, 1, 1, 'map');
			
		// Initialize the options array.
        $options = array();
			
		if ($maps)
		{
            foreach ($maps as $map)
            {
                $options[] = JHtml::_('select.option', $map->id, $map->name);
            }
		}
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        return $options;
    }
}