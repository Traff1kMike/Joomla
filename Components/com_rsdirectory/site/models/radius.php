<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Radius Search model.
 */
class RSDirectoryModelRadius extends JModelList
{
	/**
     * Method to auto-populate the model state.
     *
     * @param string $ordering An optional ordering field.
	 * @param string $direction An optional direction (asc|desc).
     * 
     * @access protected
     */
    protected function populateState($ordering = null, $direction = null)
    {
		// Get mainframe.
		$app = JFactory::getApplication();
			
		$params = $app->getParams();
		$this->setState('params', $params);
			
		parent::populateState($ordering, $direction);
    }
		
	/**
	 * Method to get an array of data items.
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params = $app->getParams();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
			
		// Get the map fields.
		$map_fields_ids = array();
			
		foreach ( $params->get('fields') as $map_field_id )
		{
			// Reset the map fields array if the field is 0 or '' ("All map categories" is selected ), because we will fetch them all later if the array is empty.
			if (!$map_field_id)
			{
				$map_fields_ids = array();
				break;
			}
				
			$map_fields_ids[] = $map_field_id;
		}
			
		$map_fields_ids = RSDirectoryHelper::arrayInt($map_fields_ids);
		$map_fields = RSDirectoryHelper::getFormFields(null, 1, 1, 'map');
		$map_fields_ids_aux = array();
			
		if ($map_fields)
		{
			if ( empty($map_fields_ids) || in_array(0, $map_fields_ids) )
			{
				foreach ($map_fields as $map_field)
				{
					$map_fields_ids_aux[] = $map_field->id;
				}
			}
			else
			{
				foreach ($map_fields as $map_field)
				{
					if ( in_array($map_field->id, $map_fields_ids) )
					{
						$map_fields_ids_aux[] = $map_field->id;
					}
				}
			}
		}
			
		$map_fields_ids = $map_fields_ids_aux;
			
		// Exit the function if there are no map fields.
		if ( empty($map_fields_ids) )
			return;
			
		// Get the featured categories.
		$featured_categories = array();
			
		if ( is_array( $params->get('featured_categories') ) )
		{
			foreach ( $params->get('featured_categories') as $category_id )
			{
				// Reset the featured categories array if the category is 0 or '' ("All categories" is selected ), because we will fetch them all later if the array is empty.
				if (!$category_id)
				{
					$featured_categories = array();
					break;
				}
					
				$featured_categories[] = $category_id;
			}
		}
			
		$featured_categories = RSDirectoryHelper::arrayInt($featured_categories);
			
		if ( empty($featured_categories) || in_array(0, $featured_categories) )
		{
			$categories_query = $db->getQuery(true)
							  ->select( $db->qn('id') )
							  ->from( $db->qn('#__categories') )
							  ->where( $db->qn('published') . ' = ' . $db->q(1) )
							  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' );
								
			$db->setQuery($categories_query);
				
			$categories_ids = $db->loadColumn();
		}
		else
		{
			$categories_query = $db->getQuery(true)
							  ->select( $db->qn('lft') . ', ' . $db->qn('rgt') )
							  ->from( $db->qn('#__categories') )
							  ->where( $db->qn('id') . ' IN (' . implode(',', $featured_categories) . ')' )
							  ->where( $db->qn('published') . ' = ' . $db->q(1) )
							  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' );
								
			$db->setQuery($categories_query);
			$categories = $db->loadObjectList();
				
			if ($categories)
			{
				$categories_query = $db->getQuery(true)
								  ->select( $db->qn('id') )
								  ->from( $db->qn('#__categories') )
								  ->where( '(' . $db->qn('extension') . ' = ' . $db->q('com_rsdirectory') . ' OR ' . $db->qn('extension') . ' = ' . $db->q('system') . ')' )
								  ->where( $db->qn('published') . ' = ' . $db->q(1) );
								  
				$or = array();
									
				foreach ($categories as $category)
				{
					$or[] = '(' . $db->qn('lft') . ' >= ' . $db->q($category->lft) . ' AND ' . $db->qn('rgt') . ' <= ' . $db->q($category->rgt) . ')';
				}
					
				$categories_query->where( '(' . implode(' OR ', $or) . ')' );
					
				$db->setQuery($categories_query);
					
				$categories_ids = $db->loadColumn();
			}
		}
			
		if ( empty($categories_ids) )
		{
			$categories_ids = array(0);
		}
			
		// Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
			
		$select = array(
			$db->qn('e') . '.*',
			$db->qn('ec') . '.*',
			$db->qn('c.title', 'category_title'),
			$db->qn('c.path', 'category_path'),
			$db->qn('f.entry_id', 'faved'),
			$db->qn("u.$author", 'author'),
		);
			
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries', 'e') )
			   ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') .  ' = ' . $db->qn('u.id') )
			   ->leftJoin( $db->qn('#__rsdirectory_favorites', 'f') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('f.entry_id') . ' AND ' . $db->qn('f.user_id') .  ' = ' . $db->q($user->id) )
			   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
			   ->where( $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) )
			   ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' >= ' . $db->q( JFactory::getDate()->toSql() ) . ')' )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->where( $db->qn('e.category_id') . ' IN (' . implode( ',', array_unique($categories_ids) ) . ')' )
			   ->group( $db->qn('e.id') );
			   
		foreach ($map_fields_ids as $map_field_id)
		{
			$query->where( $db->qn("f_{$map_field_id}_lat") . ' != ' . $db->q(0) . ' AND ' . $db->qn("f_{$map_field_id}_lng") . ' != ' . $db->q(0) );
		}
			
		$filters = $jinput->get( 'filters', array(), 'array' );
			
		// Get the filter fields for the selected categories.
		$fields = RSDirectoryHelper::getFilterFields($featured_categories);
			
		if ($fields)
		{
			foreach ($fields as $field)
			{
				// Convert the range values to a format understood by the RSDirectoryHelper::buildEntriesFilteringQuery function.
				if ( $field->properties->get('searchable_advanced') == 'range' && !empty($filters[$field->form_field_name]) )
				{
					$values = $filters[$field->form_field_name];
					$new_values = array();
						
					if ( !empty($values['ranges']) )
					{
						$ranges = $values['ranges'];
							
						if ( !is_array($ranges) )
						{
							$ranges = (array)$ranges;
						}
							
						foreach ($ranges as &$range)
						{
							$range = trim($range);
								
							// Do a few validations.
							if ( strpos($range, 'lt-') !== false )
							{
								$aux_value = trim( str_replace('lt-', '', $range) );
									
								if ( !is_numeric($aux_value) )
									continue;
									
								$new_values[] = "lt-$aux_value";
							}
							else if ( strpos($range, 'gt-') !== false )
							{
								$aux_value = trim( str_replace('gt-', '', $range) );
									
								if ( !is_numeric($aux_value) )
									continue;
									
								$new_values[] = "gt-$aux_value";
							}
							else
							{
								$arr = explode('-', $range);
									
								foreach ($arr as &$v)
								{
									$v = trim($v);
								}
									
								if ( isset($arr[2]) || !is_numeric($arr[0]) || !is_numeric($arr[1]) )
									continue;
									
								$new_values[] = $arr[0] < $arr[1] ? $arr[0] . '-' . $arr[1] : $arr[1] . '-' . $arr[0];
							}
						}
					}
						
					// Process custom range.
					if ( !empty($values['custom']) && ( isset($values['from']) || isset($values['to']) ) )
					{
						unset($from);
						unset($to);
							
						if ( isset($values['from']) && is_numeric($values['from']) )
						{
							$from = $values['from'];
						}
							
						if ( isset($values['to']) && is_numeric($values['to']) )
						{
							$to = $values['to'];
						}
							
						if ( isset($from, $to) && $from > $to )
						{
							$aux = $from;
							$from = $to;
							$to = $aux;
						}
							
						if ( isset($from) )
						{
							$new_values[] = "from-$from";
						}
							
						if ( isset($to) )
						{
							$new_values[] = "to-$to";
						}
					}
						
					$filters[$field->form_field_name] = $new_values;
				}
			}
		}
			
		RSDirectoryHelper::buildEntriesFilteringQuery($fields, $filters, $query);
			
		// Get the entries.
		$db->setQuery($query);
		$entries = $db->loadObjectList();
			
		// Initialize the results array.
		$results = array();
			
		if ($entries)
		{
			$entries = RSDirectoryHelper::getEntriesData($entries);
				
			foreach ($entries as $entry)
			{
				$coords = array();
					
				foreach ($map_fields_ids as $map_field_id)
				{
					$lat_column_name = "f_{$map_field_id}_lat";
					$lng_column_name = "f_{$map_field_id}_lng";
						
					$coords = array(
						'lat' => $entry->{$lat_column_name},
						'lng' => $entry->{$lng_column_name},
					);
				}
					
				$results[] = array(
					'id' => $entry->id,
					'coords' => $coords,
				);
			}
		}
			
		return $results;
	}
		
	/**
	 * Generate info window.
	 *
	 * @acces public
	 *
	 * @return string
	 */
	public function getInfoWindow()
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$user = JFactory::getUser();
			
		// Get entry author display info (name, username, email..).
		$author = RSDirectoryConfig::getInstance()->get('entry_author', 'name');
			
		$select = array(
			$db->qn('e') . '.*',
			$db->qn('ec') . '.*',
			$db->qn('c.title', 'category_title'),
			$db->qn('c.path', 'category_path'),
			$db->qn('f.entry_id', 'faved'),
			$db->qn("u.$author", 'author'),
		);
			
		$query = $db->getQuery(true)
			   ->select($select)
			   ->from( $db->qn('#__rsdirectory_entries', 'e') )
			   ->innerJoin( $db->qn('#__rsdirectory_entries_custom', 'ec') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('ec.entry_id') )
			   ->innerJoin( $db->qn('#__categories', 'c') . ' ON ' . $db->qn('e.category_id') . ' = ' . $db->qn('c.id') )
			   ->innerJoin( $db->qn('#__users', 'u') . ' ON ' . $db->qn('e.user_id') .  ' = ' . $db->qn('u.id') )
			   ->leftJoin( $db->qn('#__rsdirectory_favorites', 'f') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('f.entry_id') . ' AND ' . $db->qn('f.user_id') .  ' = ' . $db->q($user->id) )
			   ->where( $db->qn('e.published') . ' = ' . $db->q(1) )
			   ->where( $db->qn('e.published_time') . ' <= ' . $db->q( JFactory::getDate()->toSql() ) )
			   ->where( '(' . $db->qn('e.expiry_time') . ' = ' . $db->q('0000-00-00 00:00:00') . ' OR ' . $db->qn('e.expiry_time') . ' >= ' . $db->q( JFactory::getDate()->toSql() ) . ')' )
			   ->where( $db->qn('u.block') . ' = ' . $db->q(0) )
			   ->where( $db->qn('e.id') . ' = ' . $db->q( $app->input->getInt('id') ) );
			   
		$db->setQuery($query);
		$entry = $db->loadObject();
			
		$entry = RSDirectoryHelper::getEntryData($entry);
			
		$width = $params->get('info_window_width', 300);
		$height = $params->get('info_window_height', 120);
			
		$url = RSDirectoryRoute::getEntryURL($entry->id, $entry->title);
			
		$str = '<div>';
			
		// Google Maps would override these attributes if we didn't place the 1st div before this one.
		$str .= '<div class="rsdir-info-window media" style="width: ' . $width . 'px; height: ' . $height . 'px;">';
			
		if ( $params->get('show_thumbnails') )
		{
			$thumbnails_width = $params->get('thumbnails_width');
			$thumbnails_height = $params->get('thumbnails_height');
				
			$images_field = RSDirectoryHelper::findFormField('images', $entry->form->fields);
				
			$str .= '<a class="thumbnail pull-left" href="' . $url . '">';
			
			if ( empty($images_field) || empty($images_field->files) )
			{
				$str .= '<i class="rsdir-no-image" style="width: ' . $thumbnails_width . 'px; height: ' . $thumbnails_height . 'px;"></i>';
			}
			else
			{
				$src = RSDirectoryHelper::getImageURL($images_field->files[0]->hash, 'small');
					
				$str .= '<img class="media-object" src="' . $src . '" alt="" width="' . $thumbnails_width . '" height="' . $thumbnails_height . '" />';
			}
				
			$str .= '</a>';
		}
			
		$str .= '<div class="media-body">';
			
		if ( $params->get('show_price') )
		{
			$str .= '<div class="label label-success">' . RSDirectoryHelper::formatPrice($entry->price) . '</div> ';
		}
			
		if ( $params->get('show_title') )
		{
			$str .= '<div><a href="' . $url . '">' . RSDirectoryHelper::escapeHTML($entry->title) . '</a></div>';	
		}
			
		if ( $params->get('show_ratings') )
		{
			$str .= '<div class="rating" data-rating="' . $entry->avg_rating .  '"></div>';
		}
			
		if ( $params->get('show_favorites_button') )
		{
			$str .= '<div class="clearfix"><a class="fav' . ($entry->faved ? ' rsdir-entry-faved' : '') . ' pull-right btn" data-entry-id="' . $entry->id . '" title="' . JText::_($entry->faved ? 'COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES' : 'COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES') . '"><i class="' . ($entry->faved ? 'icon-star' : 'icon-star-empty') . '"></i></a></div>';	
		}
			
		$str .= '</div>'; // .media-body
			
		$str .= '</div>'; // .rsdir-info-window
			
		$str .= '</div>';
			
		return $str;
	}
}