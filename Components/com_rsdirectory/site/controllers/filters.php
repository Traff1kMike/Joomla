<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Filters controller.
 */
class RSDirectoryControllerFilters extends JControllerLegacy
{
    /**
     * Process filters.
     *
     * @access public
     */
    public function process()
    {
        // Stop the script if the token is invalid.
        JSession::checkToken() or jexit('Invalid Token');
            
		// Get mainframe.
		$app = JFactory::getApplication();
			
		// Get the JInput object.
		$jinput = $app->input;
			
		if ( !$jinput->getInt('clear_filters') )
		{
			// Initialize the query array.
			$query = array();
				
			// Get the search query.
			list($q) = $jinput->get( 'q', array(''), 'array' );
				
			if ( trim($q) !== '')
			{
				$query[] = 'q=' . urlencode($q);    
			}
				
			// Process the categories. 
			if ( $categories = $jinput->get( 'categories', array(), 'array' ) )
			{
				$categories = array_unique( RSDirectoryHelper::arrayInt($categories) );
					
				if ($categories)
				{
					foreach ($categories as $category_id)
					{
						$query[] = 'categories[]=' . $category_id;
					}
				}
			}
				
			// Process the status.
			if ( $statuses = $jinput->get( 'status', array(), 'array' ) )
			{
				$statuses = array_unique( RSDirectoryHelper::arrayInt($statuses) );
					
				if ($statuses)
				{
					foreach ($statuses as $status)
					{
						if ( in_array( $status, array(0, 1) ) )
						{
							$query[] = 'status[]=' . $status;	
						}
					}
				}
			}
				
			// Get the search filters.
			$filters = $jinput->get( 'filters', array(), 'array' );
				
			if ( !empty($filters) )
			{
				// Get all the published form fields.
				$form_fields = RSDirectoryHelper::getFormFields(0, 1);
					
				foreach ($filters as $field_name => $field_value)
				{
					if ( is_string($field_value) && trim($field_value) === '' )
						continue;
						
					$form_field = RSDirectoryHelper::findElements( array('form_field_name' => $field_name), $form_fields );
						
					if ( empty($form_field) || !$form_field->properties->get('searchable_advanced') )
						continue;
						
					$values = array();
						
					$searchable_advanced = $form_field->properties->get('searchable_advanced');
						
					if ($searchable_advanced == 'range')
					{
						if ( !empty($field_value['ranges']) )
						{
							$ranges = $field_value['ranges'];
								
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
										
									$query[] = "f[$field_name][]=lt-$aux_value";
								}
								else if ( strpos($range, 'gt-') !== false )
								{
									$aux_value = trim( str_replace('gt-', '', $range) );
										
									if ( !is_numeric($aux_value) )
										continue;
										
									$query[] = "f[$field_name][]=gt-$aux_value";
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
										
									$query[] = "f[$field_name][]=" . ($arr[0] < $arr[1] ? $arr[0] . '-' . $arr[1] : $arr[1] . '-' . $arr[0]);
								}
							}
						}
							
						// Process custom range.
						if ( !empty($field_value['custom']) && ( isset($field_value['from']) || isset($field_value['to']) ) )
						{
							unset($from);
							unset($to);
								
							if ( isset($field_value['from']) && is_numeric($field_value['from']) )
							{
								$from = $field_value['from'];
							}
								
							if ( isset($field_value['to']) && is_numeric($field_value['to']) )
							{
								$to = $field_value['to'];
							}
								
							if ( isset($from, $to) && $from > $to )
							{
								$aux = $from;
								$from = $to;
								$to = $aux;
						    }
								
							if ( isset($from) )
							{
								$query[] = "f[$field_name][]=from-$from";
							}
								
							if ( isset($to) )
							{
								$query[] = "f[$field_name][]=to-$to";
							}
						}
					}
					else if ($searchable_advanced == 'date_range')
					{
						if ( !empty($field_value['from']) )
						{
							$from = JFactory::getDate($field_value['from']);
						}
							
						if ( !empty($field_value['to']) )
						{
							$to = JFactory::getDate($field_value['to']);
						}
							
						if ( isset($from, $to ) )
						{
							if ( $from->toUnix() > $to->toUnix() )
							{
								$query[] = "f[$field_name][from]=" . $field_value['to'];
								$query[] = "f[$field_name][to]=" . $field_value['from'];
							}
							else
							{
								$query[] = "f[$field_name][from]=" . $field_value['from'];
								$query[] = "f[$field_name][to]=" . $field_value['to'];
							}
						}
						else if ( isset($from) )
						{
							$query[] = "f[$field_name][from]=" . $field_value['from'];
						}
						else if ( isset($to) )
						{
							$query[] = "f[$field_name][to]=" . $field_value['to'];
						}
					}
					else
					{
						if ( is_array($field_value) )
						{
							foreach ($field_value as &$value)
							{
								$query[] = "f[$field_name][]=" . urlencode( trim($value) );
							}
						}
						else
						{
							$query[] = "f[$field_name]=" . urlencode( trim($field_value) );
						}
					}
				}
			}
		}
			
		$url = 'index.php?option=com_rsdirectory&view=entries&filter=1';
			
		// Get the Itemid.
		if ( $Itemid = $jinput->getInt('Itemid') )
		{
			$url .= ( strpos($url, '?') === false ? '?' : '&' ) . "Itemid=$Itemid";
		}
			
		$url = JRoute::_($url, false);
			
		if ( !empty($query) )
		{
			$url .= ( strpos($url, '?') === false ? '?' : '&' ) . implode('&', $query);
		}
			
		$app->redirect($url);
    }
}