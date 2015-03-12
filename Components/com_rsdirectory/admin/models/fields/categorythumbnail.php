<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Category thumbnail field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldCategoryThumbnail extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'categorythumbnail';
        
    /**
     * Method to get the field input markup.
     *
     * @access protected
     *
     * @return string The field input markup.
     */
    protected function getInput()
    {
        // Initialize the result string.
        $str = '';
            
        // Initialize field attributes.
        $attrs = $this->element['accept'] ? ' accept="' . (string) $this->element['accept'] . '"' : '';
        $attrs .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attrs .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
        $attrs .= (string)$this->element['disabled'] == 'true' ? ' disabled="disabled"' : '';
        $attrs .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
            
        $str .= '<input type="file" name="' . $this->name . '" id="' . $this->id . '"' . ' value=""' . $attrs . ' />';
            
        // Display uploaded image.
        if ($this->value)
        {
            // Get the thumb data.
            $thumb = RSDirectoryHelper::getCategoryThumbObject($this->value);
                
            if ($thumb)
            {
                $src = RSDirectoryHelper::getImageURL($thumb->hash, 'small');
                    
                $str .= '<div class="rsdir-thumb-wrapper">';
                    
                $str .= '<br /> <img class="thumbnail" alt="" src="' . $src . '" /> <br />';
                    
                $str .= '<strong><a href="javascript: void(0);" onclick="removeCategoryImage(this, ' . $thumb->category_id . ');"><i class="rsdir-icon-x"></i>' . JText::_('COM_RSDIRECTORY_REMOVE_IMAGE') . '</a></strong>';
                    
                $str .= '</div>';
            }
        }
            
        JText::script('COM_RSDIRECTORY_CATEGORY_REMOVE_THUMBNAIL_CONFIRM');
            
        return $str;
    }
}