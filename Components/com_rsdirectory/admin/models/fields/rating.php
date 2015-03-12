<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Rating field.
 *
 * @package RSDirectory!
 * @subpackage com_rsdirectory
 */
class JFormFieldRating extends JFormField
{
    /**
     * The form field type.
     *
     * @var string
     */
    protected $type = 'rating';
        
    /**
     * Method to get the field input markup.
     *
     * @access protected
     *
     * @return string The field input markup.
     */
    protected function getInput()
    {
        $doc = JFactory::getDocument();
            
        $doc->addScript( JURI::root(true) . '/media/com_rsdirectory/js/jquery.raty.min.js?v=' . RSDirectoryVersion::$version );
            
        $script = ' jQuery(function($)
                    {
                        $( document.getElementById("' . $this->id . '") ).raty(
                        {
                            path: "' . JUri::root(true) . '/media/com_rsdirectory/images/raty/",
                            scoreName: "' . $this->name . '",
                            score: function()
                            {
                                return $(this).attr("data-rating");
                            }
                        });
                    });';
                    
        $doc->addScriptDeclaration($script);
            
        // Initialize field attributes.
        $attrs = " id=\"$this->id\"";
        $attrs .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
            
        // Initialize the result string.
        $str = "<div$attrs data-rating=\"" . ( (int) $this->value ) . "\">";
            
        $str .= '</div>';
            
            
        return $str;
    }
}