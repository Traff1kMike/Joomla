<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/formfield.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsdirectory/helpers/field.php';

/**
 * Placeholders processing class.
 */
class RSDirectoryPlaceholders
{
    /**
     * The string to be processed.
     *
     * @var string
     * 
     * @access protected
     */
    protected $str;
        
    /**
     * Form fields.
     *
     * @var array
     * 
     * @access protected
     */
    protected $form_fields;
        
    /**
     * Entry data.
     *
     * @var object
     * 
     * @access protected
     */
    protected $entry;
        
    /**
     * Parent form.
     *
     * @var object
     *
     * @access protected
     */
    protected $form;
        
    /**
     * A list of all default placeholders.
     *
     * @var array
     * 
     * @access protected
     *
     * @static
     */
    protected static $placeholders = array(
		'site.name',
		'site.url',
        'username',
        'name',
        'email',
        'userid',
        'credits-remaining',
        'credits-spent',
        'id',
        'url',
        'category',
        'category-path',
        'images',
        'small-thumb',
        'big-thumb',
        'normal-thumb',
        'publishing-date',
        'expiry-date',
        'title',
        'big-subtitle',
        'small-subtitle',
        'price',
        'description',
    );
        
    /**
     * Excluded field types.
     *
     * @var array
     * 
     * @access protected
     */
    protected $excluded_field_types = array();
        
    /**
     * Excluded placeholders.
     *
     * @var array
     * 
     * @access protected
     */
    protected $excluded_placeholders = array();
        
    /**
     * Process placeholders (e.g.: add excluded field types, add excluded placeholders) based on these params.
     *
     * @var array
     *
     * @access protected
     */
    protected $params = array();
		
	/**
	 * Placeholders => values map.
	 *
	 * @var array
	 *
	 * @access protected
	 */
	protected $map = array();
        
    /**
     * The class constructor.
     *
     * @access public
     * 
     * @param string $str
     * @param array $form_fields
     * @param object $entry
     * @param mixed $form
     */
    public function __construct($str, $form_fields, $entry, $form = null)
    {
        $this->str = $str;
        $this->form_fields = $form_fields;
        $this->entry = $entry;
        $this->form = $form;
    }
        
    /**
     * Get RSDirectoryPlaceholders instance.
     *
     * @access public
     * 
     * @static
     * 
     * @param string $str
     * @param array $form_fields
     * @param object $entry
     * @param mixed $form
     * 
     * @return RSDirectoryPlaceholders
     */
    public static function getInstance($str, $form_fields, $entry, $form = null)
    {
        $rsdirectoryplaceholders = new RSDirectoryPlaceholders($str, $form_fields, $entry, $form);
            
        return $rsdirectoryplaceholders;
    }
        
    /**
     * Set excluded field types.
     *
     * @access public
     * 
     * @param mixed $excluded_field_types
     * 
     * @return RSDirectoryPlaceholders
     */
    public function addExcludedFieldTypes($excluded_field_types)
    {
        if ( is_array($excluded_field_types) )
        {
            $this->excluded_field_types = array_merge($this->excluded_field_types, $excluded_field_types);
        }
        else
        {
            $this->excluded_field_types[] = $excluded_field_types;
        }
            
        return $this;
    }
        
    /**
     * Remove field types from the excluded field types array.
     *
     * @access public
     * 
     * @param mixed $field_types
     * 
     * @return RSDirectoryPlaceholders
     */
    public function removeExcludedFieldTypes($field_types)
    {
        if ( is_array($field_types) )
        {
            $this->excluded_field_types = array_diff($this->excluded_field_types, $field_types);
        }
        else
        {
            $this->excluded_field_types = array_diff( $this->excluded_field_types, array($field_types) );
        }
            
        return $this;
    }
        
    /**
     * Set excluded placeholders.
     *
     * @access public
     * 
     * @param mixed $excluded_placeholders
     * 
     * @return RSDirectoryPlaceholders
     */
    public function addExcludedPlaceholders($excluded_placeholders)
    {
        if ( is_array($excluded_placeholders) )
        {
            $this->excluded_placeholders = array_merge($this->excluded_placeholders, $excluded_placeholders);
        }
        else
        {
            $this->excluded_placeholders[] = $excluded_placeholders;
        }
            
        return $this;
    }
        
    /**
     * Remove placeholders from the excluded placeholders array.
     *
     * @access public
     * 
     * @param mixed $placeholders
     * 
     * @return RSDirectoryPlaceholders
     */
    public function removeExcludedPlaceholders($placeholders)
    {
        if ( is_array($placeholders) )
        {
            $this->excluded_placeholders = array_diff($this->excluded_placeholders, $placeholders);
        }
        else
        {
            $this->excluded_placeholders = array_diff( $this->excluded_placeholders, array($placeholders) );
        }
            
        return $this;
    }
        
    /**
     * Set the string.
     *
     * @access public
     * 
     * @param string $str
     * 
     * @return RSDirectoryPlaceholders
     */
    public function setStr($str)
    {
        $this->str = $str;
            
        return $this;
    }
        
    /**
     * Set params.
     *
     * @access public
     *
     * @param mixed $params
     *
     * @return RSDirectoryPlaceholders
     */
    public function setParams($params)
    {
        if ( !is_array($params) )
        {
            $params = (array)$params;
        }
            
        foreach ($params as $param)
        {
            if ( !in_array($param, $this->params) )
            {
                if ($param == 'title')
                {
                    $this->addExcludedFieldTypes('fileupload', 'image_upload');
                    $this->addExcludedPlaceholders( array('credits-remaining', 'credits-spent', 'images', 'small-thumb', 'big-thumb', 'normal-thumb', 'title') );
                }
                    
                $this->params[] = $param;
            }
        }
            
        return $this;
    }
		
    /**
     * Remove params.
     *
     * @access public
     *
     * @return RSDirectoryPlaceholders
     */
    public function removeParams($params)
    {
        if ( !is_array($params) )
        {
            $params = (array)$params;
        }
            
        foreach ($params as $param)
        {
            $index = array_search($param, $this->params);
            
            if ($index !== false)
            {
                if ($param == 'title')
                {
                    $this->removeExcludedFieldTypes('fileupload', 'image_upload');
                    $this->removeExcludedPlaceholders( array('credits-remaining', 'credits-spent', 'images', 'small-thumb', 'big-thumb', 'normal-thumb', 'title') );
                }
                    
                unset($this->params[$index]);
            }
        }
            
        return $this;
    }
		
	/**
	 * Method to initialize the required resources.
	 *
	 * @access protected
	 */	
	protected function init()
	{
		$str = $this->str;
        $form_fields = $this->form_fields;
        $entry = $this->entry;
        $form = $this->form;
        $excluded_field_types = $this->excluded_field_types;
        $excluded_placeholders = $this->excluded_placeholders;
        $params = $this->params;
			
		// Get an instance of the RSDirectoryConfig object.
        $config = RSDirectoryConfig::getInstance();
			
		// Get the Joomla! config object.
		$jconfig = JFactory::getConfig();
            
        // Get the user object.
        $user = JFactory::getUser($entry->user_id);
			
		// Initialize the placeholders => values array.
		$map = array();
			
		foreach (self::$placeholders as $placeholder)
        {
            // Skip this placeholder because it is excluded.
            if ( in_array($placeholder, $excluded_placeholders) )
                continue;
                
            // Skip this placeholder because it was not found in the string.
            if ( strpos($str, '{' . $placeholder . '}') === false && strpos($str, '{if ' . $placeholder . '}') === false )
                continue;
                
            // Initialize the placeholder value.
            $placeholder_value = '';
				
			if ( empty($images) && in_array( $placeholder, array('images', 'small-thumb', 'big-thumb', 'normal-thumb') ) )
			{
                // Get the images field.
				$images = RSDirectoryHelper::findFormField('images', $form_fields);
					
				// Get the images list.
				if ($images)
				{
					$images_list = RSDirectoryHelper::getFilesObjectList($images->id, $entry->id);
				}
			}
				
            switch ($placeholder)
            {
				case 'site.name':
						
					$placeholder_value = $jconfig->get('sitename');
					break;
						
				case 'site.url':
						
					$placeholder_value = JURI::root();
					break;
						
                case 'username':
                        
                    $placeholder_value = $user->username;
                    break;
                        
                case 'name':
                        
                    $placeholder_value = $user->name;
                    break;
                        
                case 'email':
                        
                    $placeholder_value = $user->email;
                    break;
                        
                case 'userid':
                        
                    $placeholder_value = $user->id;
                    break;
                        
                case 'credits-remaining':
                        
                    $credits = RSDirectoryCredits::getUserCredits($user->id);
						
					$placeholder_value = $credits === 'unlimited' ? JText::_('COM_RSDIRECTORY_UNLIMITED') : $credits;
						
                    break;
                        
                case 'credits-spent':
                        
                    $placeholder_value = RSDirectoryCredits::getUserSpentCredits($user->id);
                    break;
                        
                case 'id':
                        
                    $placeholder_value = $entry->id;
                    break;
                        
                case 'url':
                        
                    $placeholder_value = RSDirectoryRoute::getEntryURL($entry->id, $entry->title, '', 0, true);
                    break;
                        
                case 'category':
                        
                    if ( isset($entry->category_title) )
                    {
                        $placeholder_value = $entry->category_title;
                    }
                    else
                    {
                        $category = RSDirectoryHelper::getCategory($entry->category_id);
                            
                        $placeholder_value = $category->title;
                    }
                        
                    break;
                        
                case 'category-path':
						
					static $categories;
						
					if ( empty($categories) )
					{
                        $categories = RSDirectoryHelper::getCategories($categories);
					}
						
					$hierarchy = RSDirectoryHelper::getCategoryHierarchy($entry->category_id, $categories);
						
					if ($hierarchy)
					{
						$path = array();
							
						foreach ($hierarchy as $category)
						{
							$path[] = $category->title;
						}
							
						if ( in_array('database', $params) )
						{
							$placeholder_value = implode(' » ', $path);
						}
						else
						{
							$placeholder_value = implode(' &raquo; ', $path);
						}
					}
                        
                    break;
                        
                case 'images':
                        
                    if ( !empty($images_list) )
                    {
                        if ( in_array('email', $params) )
                        {
                            $placeholder_value = '<table cellpadding="0" cellspacing="5" border="0">';
                            $placeholder_value .= '<tr>';
                                
                            foreach ($images_list as $image)
                            {
                                $placeholder_value .= '<td>';
                                
                                // Set the src.
                                $src = RSDirectoryHelper::getImageURL($image->hash, 'small');
                                    
                                // Set the alt.
                                $alt = JFile::stripExt($image->original_file_name);
                                    
                                $placeholder_value .= '<img src="' . $src . '" alt="' . $alt . '" />';
                                    
                                $placeholder_value .= '</td>';
                            }
                                
                            $placeholder_value .= '</tr>';        
                            $placeholder_value .= '</table>';
                        }
                        else
                        {
                            $placeholder_value = RSDirectoryFormField::getImagesList($images_list, 0, 0);
                        }
                    }
                        
                    break;
                        
                case 'small-thumb':
                        
                    if ( !empty($images_list) )
                    {
                        $src = RSDirectoryHelper::getImageURL($images_list[0]->hash, 'small');
                            
                        // Get the width of the small thumbnail.
                        $width = $config->get('small_thumbnail_width');
                            
                        // Get the height of the small thumbnail.
                        $height = $config->get('small_thumbnail_height');
                            
                        $placeholder_value = '<img src="' . $src . '" alt="" width="' . $width . '" height="' . $height . '" />';
                    }
                        
                    break;
                        
                case 'big-thumb':
                        
                    if ( !empty($images_list) )
                    {
                        $src = RSDirectoryHelper::getImageURL($images_list[0]->hash, 'big');
                            
                        // Get the width of the small thumbnail.
                        $width = $config->get('big_thumbnail_width');
                            
                        // Get the height of the small thumbnail.
                        $height = $config->get('big_thumbnail_height');
                            
                        $placeholder_value = '<img src="' . $src . '" alt="" width="' . $width . '" height="' . $height . '" />';
                    }
                        
                    break;
                        
                case 'normal-thumb':
                        
                    if ( !empty($images_list) )
                    {
                        $src = RSDirectoryHelper::getImageURL($images_list[0]->hash);
                            
                        $placeholder_value = '<img src="' . $src . '" alt="" />';
                    }
                        
                    break;
                        
                case 'publishing-date':
                        
                    if ($entry->published_time == '0000-00-00 00:00:00')
                    {
                        $placeholder_value = JText::_('COM_RSDIRECTORY_ENTRY_NOT_YET_PUBLISHED');
                    }
                    else
                    {
                        $placeholder_value = RSDirectoryHelper::formatDate($entry->published_time);
                            
                        $placeholder_value = $placeholder_value ? $placeholder_value : JText::_('COM_RSDIRECTORY_JUST_PUBLISHED');
                    }
                        
                    break;
                        
                case 'expiry-date':
                        
                    if ($entry->expiry_time == '0000-00-00 00:00:00')
                    {
                        $placeholder_value = JText::_('COM_RSDIRECTORY_NO_EXPIRY');
                    }
                    else
                    {
                        // Entry not yet expired.
                        if ( JFactory::getDate($entry->expiry_time)->toUnix() > JFactory::getDate()->toUnix() )
                        {
                            $placeholder_value = RSDirectoryHelper::formatDate($entry->expiry_time);    
                        }
                        else
                        {
                            $placeholder_value = JText::_('COM_RSDIRECTORY_EXPIRED');
                        }
                    }
                        
                    break;
                        
                case 'title':
                        
                    $placeholder_value = $entry->title;
                    break;
                        
                case 'big-subtitle':
                        
                    $placeholder_value = $entry->big_subtitle;
                    break;
                
                case 'small-subtitle':
                        
                    $placeholder_value = $entry->small_subtitle;
                    break;
                        
                case 'price':
                        
                    $placeholder_value = RSDirectoryHelper::formatPrice($entry->price);
                    break;
                        
                case 'description':
                        
                    $description = RSDirectoryHelper::findFormField('description', $form_fields);
					$placeholder_value = RSDirectoryField::getInstance($description, $entry)->generate();
						
                    break;
            }
				
			$map[$placeholder] = $placeholder_value;
        }
			
		foreach ($form_fields as $form_field)
        {
			// Skip core placeholders.
			if ( in_array($form_field->name, self::$placeholders) )
				continue;
				
            // Skip the form field if it's in the excluded field types array.
            if ( in_array($form_field->field_type, $excluded_field_types) )
                continue;
                
            // Skip this placeholder because it is excluded.
            if ( in_array($form_field->name, $excluded_placeholders) )
                continue;
                
            // Skip this placeholder because it was not found in the string.
            if ( strpos($str, '{' . $form_field->name . '}') === false && strpos($str, '{if ' . $form_field->name . '}') === false )
                continue;
                
            $properties = isset($form_field->properties) ? $form_field->properties : null;
                
            // Initialize the placeholder value.
            $placeholder_value = '';
                
            // Get a new RSDirectoryField instance.
            $rsdirfield = RSDirectoryField::getInstance($form_field, $entry);
                
            if ( in_array( $form_field->field_type, array('fileupload', 'image_upload') ) )
            {
                // Get the files list.
                $files_list = RSDirectoryHelper::getFilesObjectList($form_field->id, $entry->id);
                    
                $rsdirfield->setFilesList($files_list);
            }
                
			if ( in_array('description', $params) && $form_field->field_type == 'textarea' && !$form_field->properties->get('allow_html')  )
			{
                if ( isset($entry->{$form_field->column_name}) )
				{
					$placeholder_value = nl2br($entry->{$form_field->column_name});
				}
			}
			else
			{
				// Get the field value.
				$placeholder_value = $rsdirfield->generate();
			}
                
			$map[$form_field->name] = $placeholder_value;
        }
			
		$this->map = $map;
	}
		
	/**
	 * Method to process conditional placeholders.
	 *
	 * E.g.: {if field_name}this text gets displayed only if the placeholder has a value{/if}
	 *
	 * @access protected
	 *
	 * @param string $string
	 *
	 * @return $string
	 */
	protected function processConditional($string)
	{
		$condition 	= '[a-z0-9\-]+';
		$inner = '((?:(?!{/?if).)*?)';
		$pattern = '#{if\s?(' . $condition . ')}' . $inner . '{/if}#is';
			
		while ( preg_match($pattern, $string, $match) )
		{
			$field_name = $match[1];
			$content = $match[2];
				
			$form_field = RSDirectoryHelper::findElements( array('name' => $field_name), $this->form_fields );
				
			$replace = empty($form_field) || !isset($this->map[$field_name]) || $this->map[$field_name] == '' ? '' : addcslashes($content, '$');
				
			// If empty value remove whole line else show line but remove pseudo-code.
			$string = preg_replace($pattern, $replace, $string, 1);
		}
			
		return $string;
	}
        
    /**
     * Process placeholders and return the new string.
     *
     * @access public
     * 
     * @return string
     */
    public function process()
    {
        if (!$this->str || !$this->form_fields || !$this->entry)
            return;
			
		// Initialize the required resources.
		$this->init();
			
		if ( strpos($this->str, '{/if}') !== false )
		{
			$this->str = $this->processConditional($this->str);
		}
            
        // Remove the excluded placeholders from the string.
        if ($this->excluded_placeholders)
        {
            foreach ($this->excluded_placeholders as $placeholder)
            {
                $this->str = str_replace('{' . $placeholder . '}', '', $this->str);
            }
        }
			
		// Replace placeholders with their values.
		if ($this->map)
		{
			foreach ($this->map as $placeholder => $value)
			{
				$this->str = str_replace('{' . $placeholder . '}', $value, $this->str);
			}
		}
			
        return (string)$this->str;
    }
}