<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Filter generator class.
 */
class RSDirectoryFilter
{
	/**
     * The field object.
     *
     * @var object
     * 
     * @access protected
     */
    protected $field;
		
	/**
	 * Field options.
	 *
	 * @var array
	 *
	 * @access protected
	 */
	protected $options;
		
	/**
     * The class constructor.
     *
     * @access public
     * 
     * @param object $field
     * @param array $options
     */
    public function __construct( $field, $options = array() )
    {
        $this->field = $field;
		$this->options = $options;
    }
		
	/**
     * Get RSDirectoryFilter instance.
     *
     * @access public
     * 
     * @static
     * 
     * @param object $field
     * 
     * @return RSDirectoryFilter
     */
    public static function getInstance( $field, $options = array() )
    {
        $rsdirectoryfield = new RSDirectoryFilter($field, $options);
            
        return $rsdirectoryfield;
    }
		
	/**
	 * Generate the filter.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function generate()
	{
		$field = $this->field;
		$options = new JRegistry($this->options);
		$more = (int)$options->get('more', 5);
			
		if ( !$field->properties->get('searchable_advanced') )
			return;
			
		$f = JFactory::getApplication()->input->get( 'f', array(), 'array' );
		$default = isset($f[$field->form_field_name]) ? $f[$field->form_field_name] : '';
			
		$properties = $field->properties;
			
		$searchable_advanced = $properties->get('searchable_advanced');
			
		// Get the field model.
		$field_model = RSDirectoryModel::getInstance('Field');
			
		// Get an array field types that can be used as dependency parents.
        $dependency_parent_compatible = $field_model->getDependencyParentCompatible();
            
        // Check if the field is a dependency parent.
        if ( in_array($field->field_type, $dependency_parent_compatible) )
        {
            $is_dependency_parent = RSDirectoryHelper::isDependencyParent($field->id);
        }
			
		// Field wrapper attributes.
        $filter_wrapper_attrs = array(
            'class' => "rsdir-filter rsdir-filter-$field->id control-group",
        );
			
			
		// Initialize the items array.
        $items = array();
			
		if ( in_array( $searchable_advanced, $field_model->getDependencyCompatible() ) && $properties->get('use_dependency') && $properties->get('dependency') )
		{
			$filter_wrapper_attrs['class'] .= ' dependency-child';
            $filter_wrapper_attrs['data-parent-id'] = $properties->get('dependency');
				
			// Get dependency items.
			if ( $dependency_options = $this->getDependencyOptions( $properties->get('dependency') ) )
			{
				$items = $dependency_options;
			}
		}
		else if ( $properties->get('use_field_items') )
		{
			$items = RSDirectoryHelper::getOptions( $properties->get('items') );
		}
		else
		{
			$items = RSDirectoryHelper::getOptions( $properties->get('searchable_advanced_items') );
		}
			
			
		$field_name = "filters[$field->form_field_name]";
			
		// Initialize the result strings.
		$field_str = '';
		$before_str = '';
        $after_str = '';
			
		switch ($searchable_advanced)
		{
			case 'textbox':
					
				$field_str .= '<input class="rsdir-textbox" type="text" name="' . $field_name . '" value="' . $default . '" />';
					
				break;
					
			case 'checkboxgroup':
			case 'range':
					
				if ( $default && !is_array($default) )
				{
					$default = (array)$default;
				}
					
				$show_more = $more && count($items) > $more;
				$toggled = false;
					
				$before_str .= '<div class="rsdir-items-group">';
					
				// Get dependency items.
                if ( $properties->get('dependency') && empty($dependency_options) )
                {
                    $field_str .= JText::_('COM_RSDIRECTORY_NOTHING_TO_SELECT');
                }
					
				$i = 0;
				foreach ($items as $item)
				{
					$value = $item->value;
					$text = $item->text;
						
					$i++;
						
					$disabled = false;
						
					if ( stripos($value, '[d]') !== false || stripos($text, '[d]') !== false )
					{
						$value = str_replace('[d]', '', $value);
						$text = str_replace('[d]', '', $text);
							
						$disabled = true;
					}
						
					if ($show_more && $i > $more && !$toggled)
					{
						$toggled = true;
						$field_str .= '<div class="rsdir-filter-toggle hide">';	
					}
						
					if ( $searchable_advanced == 'range' && ( stripos($value, '{range}') !== false || stripos($text, '{range}') !== false ) )
					{
						unset($from);
						unset($to);
							
						if ($default)
						{
							foreach ($default as $range)
							{
								if ( strpos($range, 'from-') !== false )
								{
									$range = trim( str_replace('from-', '', $range) );
										
									if ( is_numeric($range) )
									{
										$from = $range;
									}
								}
									
								if ( strpos($range, 'to-') !== false )
								{
									$range = trim( str_replace('to-', '', $range) );
										
									if ( is_numeric($range) )
									{
										$to = $range;
									}
								}
							}
						}
							
						$field_str .= '<div class="rsdir-range-filter control-group">' . 
						              '<input class="rsdir-custom-range" type="checkbox" name="' . $field_name . '[custom]" value="1"' . ( isset($from) || isset($to) ? ' checked="checked"' : '' ) . ' /> ' .
						              '<input class="rsdir-range-from span4" type="text" name="' . $field_name . '[from]" value="' . ( isset($from) ? $from : '' ) . '" placeholder="' . JText::_('COM_RSDIRECTORY_FROM') . '" /> ' .
						              '<input class="rsdir-range-to span4" type="text" name="' . $field_name . '[to]" value="' . ( isset($to) ? $to : '' ) . '" placeholder="' . JText::_('COM_RSDIRECTORY_TO') . '" /> ' .
						              '<i class="icon-search"></i>' .
						              '</div>';
					}
					else
					{
						if ($searchable_advanced == 'range')
						{
							// Sanitize the range value.
							if ( strpos($value, 'lt') !== false )
							{
								$value = trim( str_replace('lt', '', $value) );
									
								if ( !is_numeric($value) )
									continue;
									
								$value = "lt-$value";
							}
							else if ( strpos($value, 'gt') !== false )
							{
								$value = trim( str_replace('gt', '', $value) );
									
								if ( !is_numeric($value) )
									continue;
									
								$value = "gt-$value";
							}
							else
							{
								$range = explode('-', $value);
									
								foreach ($range as &$v)
								{
									$v = trim($v);
								}
									
								if ( isset($range[2]) || !isset($range[1]) || !is_numeric($range[0]) || !is_numeric($range[1]) )
									continue;
									
								$value = $range[0] < $range[1] ? $range[0] . '-' . $range[1] : $range[1] . '-' . $range[0];
							}
						}
							
						$checked = $default && in_array($value, $default);
							
						$name = $field_name . ( $properties->get('searchable_advanced') == 'range' ? '[ranges]' : '' ) . '[]';
							
						$field_str .= '<label class="rsdir-checkbox-label checkbox' . ( $properties->get('flow') == 'horizontal' ? ' inline' : '' ) . '">' .
						              '<input class="rsdir-checkbox" type="checkbox" name="' . $name . '" value="' . self::escapeHTML($value) . '"' .  ($checked ? ' checked="checked"' : '') . ($disabled ? ' disabled="disabled"' : '') . ' /> ' . self::escapeHTML($text) .
								      '</label>';
					}
				}
					
				$after_str .= '</div><!-- .rsdir-items-group -->';
					
				break;
					
			case 'date_range':
					
				// FROM.
				$field_str .= '<div class="control-group">';
				$field_str .= '<label for="' . $field->form_field_name . '-from">' . JText::_('COM_RSDIRECTORY_FROM') . '</label>';
					
				$field_str .= JHTML::_(
					'calendar',
					empty($default['from']) ? '' : $default['from'],
					$field_name . '[from]',
					$field->form_field_name . '-from',
					'%m/%d/%Y',
					array('class' => 'input-small', 'placeholder' => 'mm/dd/yyyy')
				);
					
				$field_str .= '</div>';
					
				// TO.
				$field_str .= '<div class="control-group">';
				$field_str .= '<label for="' . $field->form_field_name . '-to">' . JText::_('COM_RSDIRECTORY_TO') . '</label>';
					
				$field_str .= JHTML::_(
					'calendar',
					empty($default['to']) ? '' : $default['to'],
					$field_name . '[to]',
					$field->form_field_name . '-to',
					'%m/%d/%Y',
					array('class' => 'input-small', 'placeholder' => 'mm/dd/yyyy')
				);
					
				$field_str .= '</div>';
					
				break;
					
			case 'radiogroup':
					
				$show_more = $more && count($items) > $more;
				$toggled = false;
					
				$before_str .= '<div class="rsdir-items-group">';
					
				// Get dependency items.
                if ( $properties->get('dependency') && empty($dependency_options) )
                {
                    $field_str .= JText::_('COM_RSDIRECTORY_NOTHING_TO_SELECT');
                }
					
				$i = 0;
				foreach ($items as $item)
				{
					$value = $item->value;
					$text = $item->text;
						
					$i++;
						
					$disabled = false;
						
					if ( stripos($value, '[d]') !== false || stripos($text, '[d]') !== false )
					{
						$value = str_replace('[d]', '', $value);
						$text = str_replace('[d]', '', $text);
							
						$disabled = true;
					}
						
					$checked = isset($default) && $value == $default;
						
					if ($show_more && $i > $more && !$toggled)
					{
						$toggled = true;
						$field_str .= '<div class="rsdir-filter-toggle hide">';	
					}
						
					$field_str .= '<label class="rsdir-radio-label radio' . ( $properties->get('flow') == 'horizontal' ? ' inline' : '' ) . '">' .
					              '<input class="rsdir-radio" type="radio" name="' . $field_name . '" value="' . self::escapeHTML($value) . '"' . ($checked ? ' checked="checked"' : '') . ($disabled ? ' disabled="disabled"' : '') . ' /> ' . self::escapeHTML($text) .
							      '</label>';
				}
					
				$after_str .= '</div><!-- .rsdir-items-group -->';
					
				break;
					
			case 'dropdown':
					
				// Initialize the options array.
				$options = array(
					array(
						'items' => array(
							JHTML::_( 'select.option', '', JText::_('COM_RSDIRECTORY_PLEASE_SELECT_OPTION') )
						),
					),
				);
					
				if ($items)
				{
					$options = array_merge(
						$options,
						RSDirectoryHelper::getGroupedListOptions($items)
					);
				}
					
				// Add additional attributes.
				$attrs = ' class="rsdir-dropdown"';
					
				$field_str .= JHtml::_(
					'select.groupedlist',
					$options,
					$field_name,
					array(
						'id' => "fields$field->id",
						'list.select' => isset($default) ? $default : null,
						'list.attr' => $attrs,
					)
				);
					
				break;
					
			case 1:
					
				if ( !in_array( $field->field_type, array('images', 'image_upload', 'fileupload') ) )
					return;
					
				$field_str .= '<label class="rsdir-checkbox-label checkbox">' .
				              '<input class="rsdir-checkbox" type="checkbox" name="' . $field_name . '" value="1"' . ($default ? ' checked="checked"' : '') . ' /> ' .
						      self::escapeHTML( $properties->get('searchable_advanced_label') ) .
						      '</label>';
						
				break;
		}
			
		if ( !empty($show_more) )
		{
			$field_str .= '</div>';
			$field_str .= '<a href="#" class="rsdir-filter-more' . ( $properties->get('flow') == 'horizontal' ? ' for-inline' : '' ) . '">' . JText::_('COM_RSDIRECTORY_SHOW_MORE') . '</a>';	
		}
			
		if ( !empty($is_dependency_parent) )
        {
            $filter_wrapper_attrs['class'] .= ' dependency-parent';
        }
            
        $filter_wrapper_attrs['data-id'] = $field->id;
            
        $str = '<div';
            
        foreach ($filter_wrapper_attrs as $attr => $value)
        {
            $str .= " $attr=\"$value\"";
        }
            
        $str .= '>';
			
		if ( $properties->get('searchable_advanced_caption') )
		{
			$str .= '<div class="rsdir-filter-caption">' . $properties->get('searchable_advanced_caption') . '</div>';
		}	
			
		$str .= $before_str;
		$str .= $field_str;
		$str .= $after_str;
			
		// Add a loader gif after the field if it is a dependency parent.
        if ( !empty($is_dependency_parent) )
        {
            $str .= '<img class="rsdir-loader hide" src="' . JURI::root(true) . '/media/com_rsdirectory/images/loader.gif" alt="" width="16" height="11" />';    
        }
			
		$str .= '</div>';
			
		return $str;
	}
		
	/**
     * Method to get dependency options.
     *
     * @access public
     *
     * @param mixed $dependency
     *
     * @return mixed
     */
    public function getDependencyOptions($dependency)
    {
        if (!$dependency)
            return;
            
		// Get filters values.
        $f = JFactory::getApplication()->input->get( 'f', array(), 'array' );
			
		// Get dependency items.
		$parent = RSDirectoryHelper::getField($dependency);
			
		$parent_value = isset($f[$parent->form_field_name]) ? $f[$parent->form_field_name] : '';	
            
        if ( isset($parent_value) )
        {
            $dependencies = RSDirectoryHelper::getDependencies(0, $dependency, $parent_value);
                
            if ( isset($dependencies[0]) )
            {
                return RSDirectoryHelper::getOptions($dependencies[0]->items);
            }
        }
    }
		
	/**
     * Escape HTML.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $string
     * 
     * @return string
     */
    public static function escapeHTML($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }
}