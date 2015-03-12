<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

class RSFieldset
{
    /**
     * Get fieldset start.
     *
     * @access public
     * 
     * @param string $legend
     * @param string $class
     * 
     * @return string
     */
    public function getFieldsetStart($legend = '', $class = 'adminform')
    {
        $str = "<fieldset class=\"$class\">";
            
        if ($legend)
        {
            $str .= "<legend>$legend</legend>";
        }
            
        $str .= '<ul class="config-option-list">';
            
        return $str;
    }
        
    /**
     * Get field.
     *
     * @access public
     * 
     * @param string $label
     * @param string $input
     * 
     * @return string
     */
    public function getField($label, $input)
    {
        return "<li class=\"control-group" . ( !empty($options['hide']) ? ' hide' : '' ) . "\">$label $input</li>";
    }
        
    /**
     * Get fieldset end.
     *
     * @access public
     * 
     * @return string
     */
    public function getFieldsetEnd()
    {
        return '</ul></fieldset><div class="clr"></div>';
    }
}