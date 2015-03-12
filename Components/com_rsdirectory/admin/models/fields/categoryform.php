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
 * Category Form options field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldCategoryForm extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'categoryform';
        
    /**
     * Method to get the field options.
     *
     * @access protected
     * 
     * @return array The field option objects.
     */
    protected function getOptions()
    {
        // Get the forms object list.
        $forms = RSDirectoryHelper::getForms();
            
        // Get the category id.
        $id = JFactory::getApplication()->input->getInt('id');
            
        if ($id)
        {
            $form = RSDirectoryHelper::getCategoryInheritedForm($id);
                
            $inherited_text = " ($form->title)";
        }
        else
        {
            $inherited_text = '';
        }
            
        // Initialize the options array.
        $options = array(
            JHtml::_( 'select.option', 0, JText::sprintf('COM_RSDIRECTORY_INHERITED_OPTION', $inherited_text) ),
        );
            
            
        if ($forms)
        {
            foreach ($forms as $form)
            {
                $options[] = JHtml::_('select.option', $form->id, $form->title);
            }
        }
            
        // Merge any additional options in the XML definition.
        $options = array_merge( parent::getOptions(), $options );
            
        return $options;
    }
}