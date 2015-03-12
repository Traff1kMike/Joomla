<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class for modal items list.
 */
class RSModalItems
{
    /**
     * The options array.
     *
     * @var array
     * 
     * @access public
     */
    private $options = array();
        
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }
        
    /**
     * Get a new class instance.
     *
     * @access public
     *
     * @static
     * 
     * @param array $options
     * 
     * @return RSModalItems
     */
    public static function getInstance($options)
    {
        return new RSModalItems($options);
    }
        
    /**
     * Render the items based on the installed Joomla! version.
     *
     * @access public
     * 
     * @return string
     */
    public function render()
    {
        $jversion = new JVersion();
            
        if ( $jversion->isCompatible('3.0') )
        {
            return $this->render30();
        }
        else if ( $jversion->isCompatible('2.5') )
        {
            return $this->render25();
        }
    }
        
    /**
     * Render the items for Joomla! 3.0.
     *
     * @access public
     * 
     * @return string
     */
    public function render30()
    {
        // Get the options array.
        $options = $this->options;
            
        // Initialize the content string.
        $str = '';
            
        if ( isset($options['title']) )
        {
            $str .= '<h3>' . RSDirectoryHelper::escapeHTML($options['title']) . '</h3>';
        }
            
        if ( isset($options['groups']) )
        {
            if ( isset($options['accordion']) )
            {
                $str .= '<div id="collapseTypes" class="accordion">';
            }
                
            foreach ($options['groups'] as $i => $group)
            {
                $accordion = isset($group['accordion']) && !$group['accordion'] ? false : true;
                    
                if ($accordion)
                {
                    $str .= '<div class="accordion-group">';
                }
                    
                if ( isset($group['title']) )
                {
                    $str .= '<div class="accordion-heading">';
                    $str .= '<strong>';
                        
                    if ($accordion)
                    {
                        $str .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTypes" href="#collapse' . $i . '">' . RSDirectoryHelper::escapeHTML($group['title']) . '</a>';
                    }
                    else
                    {
                        $str .= '<span class="accordion-toggle" style="curson: default;">' . RSDirectoryHelper::escapeHTML($group['title']) . '</span>';
                    }
                        
                    $str .= '</strong>';
                    $str .= '</div>';
                }
                    
                if ( isset($group['items']) )
                {
                    if ($accordion)
                    {
                        $str .= '<div id="collapse' . $i . '" class="accordion-body">';
                    }
                        
                    $str .= '<div class="accordion-inner">';
                        
                    $str .= '<ul class="nav nav-tabs nav-stacked">';
                        
                    foreach ($group['items'] as $item)
                    {
                        $str .= '<li>';
                        
                        // Initialize the a attributes array.
                        $attrs = array();
                            
                        if ( isset($item['class']) )
                        {
                            $attrs[] = 'class="' . $item['class'] . '"';
                        }
                            
                        if ( isset($item['onclick']) )
                        {
                            $attrs[] = 'onclick="' . $item['onclick'] . '"';
                        }
                            
                        $str .= '<a' . ( $attrs ? ' ' . implode(' ', $attrs) : '' ) . ' href="#">';
                            
                        $str .= RSDirectoryHelper::escapeHTML($item['text']);
                            
                        if ( isset($item['description']) )
                        {
                            $str .= '<small class="muted">' . RSDirectoryHelper::escapeHTML($item['description']) . '</small>';
                        }
                            
                        $str .= '</a>';
                            
                        $str .= '</li>';
                    }
                        
                    $str .= '</ul>';        
                        
                    $str .= '</div>';
                        
                    if ($accordion)
                    {
                        $str .= '</div>';
                    }
                }
                    
                if ($accordion)
                {
                    $str .= '</div>';
                }
            }
                
            if ( isset($options['accordion']) )
            {
                $str .= '</div>';
            }
        }
            
        return $str;
    }
        
    /**
     * Render the items for Joomla! 2.5.
     *
     * @access public
     * 
     * @return string
     */
    public function render25()
    {
        // Get the options array.
        $options = $this->options;
            
        // Initialize the content string.
        $str = '';
            
        if ( isset($options['title']) )
        {
            $str .= '<h3>' . RSDirectoryHelper::escapeHTML($options['title']) . '</h3>';
        }
            
        if ( isset($options['groups']) )
        {
            $str .= '<ul class="menu_types">';
            
            foreach ($options['groups'] as $i => $group)
            {
                $str .= '<li>';
                    
                $str .= '<dl class="menu_type">';
                    
                if ( isset($group['title']) )
                {
                    $str .= '<dt>' . RSDirectoryHelper::escapeHTML($group['title']) . '</dt>';
                }
                    
                if ( isset($group['items']) )
                {
                    $str .= '<dd>';
                        
                    $str .= '<ul>';
                        
                    foreach ($group['items'] as $item)
                    {
                        $str .= '<li>';
                        
                        // Initialize the a attributes array.
                        $attrs = array();
                            
                        if ( isset($item['class']) )
                        {
                            $attrs[] = 'class="' . $item['class'] . '"';
                        }
                            
                        if ( isset($item['onclick']) )
                        {
                            $attrs[] = 'onclick="' . $item['onclick'] . '"';
                        }
                            
                        $str .= '<a' . ( $attrs ? ' ' . implode(' ', $attrs) : '' ) . ' href="#">';
                            
                        $str .= RSDirectoryHelper::escapeHTML($item['text']);
                            
                        $str .= '</a>';
                            
                        $str .= '</li>';
                    }
                        
                    $str .= '</ul>';
                        
                    $str .= '</dd>';
                }
                    
                $str .= '</dl>';
                    
                $str .= '</li>';
            }
                
            $str .= '</ul>';
        }
            
        return $str;
    }
}