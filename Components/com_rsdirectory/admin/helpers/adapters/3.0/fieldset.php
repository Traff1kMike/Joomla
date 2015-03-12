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
    public function getFieldsetStart($legend = '', $class = 'adminform form-horizontal')
    {
        $str = "<fieldset class=\"$class\">";
            
        if ($legend)
        {
            $str .= "<legend>$legend</legend>";
        }
            
        return $str;
    }
        
    /**
     * Get field.
     *
     * @access public
     * 
     * @param string $label
     * @param string $input
     * @param array $options
     * 
     * @return string
     */
    public function getField( $label, $input, $options = array() )
    {
        $str = '<div class="control-group' . ( empty($options['hide']) ? '' : ' hide' ) . '">';
            
        if ($label)
        {
            $str .= "<div class=\"control-label\">$label</div>";
        }
            
        $str .= '<div' . ($label ? ' class="controls"' : '') . ">$input</div>";
            
        $str .= '</div>';
            
        return $str;
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
        return '</fieldset>';
    }
}