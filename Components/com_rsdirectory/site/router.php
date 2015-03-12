<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

// Load the language files.
$lang = JFactory::getLanguage();
    
$lang->load('com_rsdirectory', JPATH_SITE, 'en-GB', true);
$lang->load( 'com_rsdirectory', JPATH_SITE, $lang->getDefault(), true );
$lang->load('com_rsdirectory', JPATH_SITE, null, true);

function RSDirectoryBuildRoute(&$query)
{
    $segments = array();
        
    // Get a menu item based on Itemid or currently active.
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
        
    // We need a menu item. Either the one specified in the query, or the current active one if none specified.
    if ( empty($query['Itemid']) )
    {
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
			
		// This is to build the links correctly when a menu item is set as homepage.
		if ( isset($menuItem->id) && $menuItem->home == 1 )
		{
			$query['Itemid'] = $menuItem->id;
			$menuItemGiven = true;
		}
    }
    else
    {
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
    }
        
    // Check again.
    if ( $menuItemGiven && isset($menuItem) && $menuItem->component != 'com_rsdirectory' )
    {
		$menuItemGiven = false;
		unset($query['Itemid']);
    }
        
    if ( isset($query['view']) )
    {
		$view = $query['view'];
		unset($query['view']);
    }
        
    if ( isset($query['layout']) )
    {
		$layout = $query['layout'];
		unset($query['layout']);
    }
    else
    {
        $layout = 'default';
    }
		
    if ( isset($menuItem, $view) && $menuItem->component == 'com_rsdirectory' && $menuItem->query['view'] == $view )
    {
        $hasView = true;
    }
        
    if ( isset($query['task']) )
    {
		switch ($query['task'])
		{
			case 'entry.finalize':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY');
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_FINALIZE');
				$segments[] = $query['id'];
					
				unset($query['id']);
					
				break;
					
			case 'field.captcha':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_FIELD');
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_FIELD_CAPTCHA');
					
				break;
					
			case 'file.download':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_FILE');
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_FILE_DOWNLOAD');
				$segments[] = $query['hash'];
					
				unset($query['hash']);
					
				break;
				 
			case 'filters.process':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_PROCESS_FILTERS');
					
				break;
					
			case 'image.view':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_IMAGE');
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_IMAGE_VIEW');
				$segments[] = $query['size'];
				$segments[] = $query['hash'];
					
				unset($query['size']);
				unset($query['hash']);
					
				break;
		}
			
		unset($query['task']);
    }
    else if ( isset($view) )
    {    
		switch ($view)
		{
			case 'categories':
					
				if ( empty($hasView) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_CATEGORIES');    
				}
					
				break;
					
			case 'contact':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_CONTACT');
				$segments[] = $query['entry_id'];
					
				unset($query['entry_id']);
					
				break;
					
			case 'credits':
					
				if ( empty($hasView) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_CREDITS');
				}
					
				if ( !empty($query['wiretransfer']) && !empty($query['id']) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_CREDITS_WIRE_TRANSFER');
					$segments[] = $query['id'];
					unset($query['wiretransfer'], $query['id']);
				}
					
				break;
				 
			case 'entries':
					
				if ( empty($hasView) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRIES');    
				}
					
				if ( isset($query['category']) )
				{
					list($id, $title) = explode(':', $query['category']);
						
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_CATEGORY');
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_CATEGORY_VIEW');
					$segments[] = "$id-$title";
						
					unset($query['category']);
				}
				else if ( isset($query['user']) )
				{
					list($id, $title) = explode(':', $query['user']);
						
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_USER');
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_USER_VIEW');
					$segments[] = "$id-$title";
						
					unset($query['user']);
				}
				else if ( isset($query['filter']) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_FILTER');
						
					unset($query['filter']);
				}
					
				break;
					
			case 'entry':
					
				if ( empty($hasView) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY');
				}
					
				if ($layout == 'edit')
				{
					if ( isset($query['id']) )
					{
						// Edit.
						$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_EDIT');
						$segments[] = $query['id'];
					}
					else
					{
						// Add.
						$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_ADD');
					}
				}
				else if ($layout == 'finalize_confirm')
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_FINALIZE');
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_FINALIZE_CONFIRM');
					$segments[] = $query['id'];
				}
				else if ($layout == 'thank_you')
				{
					// Thank you.
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_THANK_YOU');
				}
				else
				{
					// Default.
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_VIEW');
						
					if ( isset($query['id']) )
					{
						$segments[] = $query['id'];	
					}
				}
					
				unset($query['id']);
					
				break;
				
			case 'entryreport':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_ENTRY_REPORT');
				$segments[] = $query['entry_id'];
					
				unset($query['entry_id']);
					
				break;
				 
			case 'myaccount':
					
				if ( empty($hasView) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT');    
				}
					
				if ( isset($query['tab']) )
				{
					if ($query['tab'] == 'transactions')
					{
						$segments[] = JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT_TRANSACTIONS');
					}
					else if ($query['tab'] == 'credits-history')
					{
						$segments[] = JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT_CREDITS_HISTORY');
					}
						
					unset($query['tab']);
				}
					
				break;
					
			case 'myentries':
					
				if ( empty($hasView) )
				{
					$segments[] = JText::_('COM_RSDIRECTORY_SEF_MY_ENTRIES');    
				}
					
				break;
				
			case 'ownerreply':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_OWNER_REPLY');
				$segments[] = $query['review_id'];
					
				unset($query['review_id']);
					
				break;
					
			case 'transaction':
					
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_TRANSACTION');
				$segments[] = JText::_('COM_RSDIRECTORY_SEF_TRANSACTION_VIEW');
				$segments[] = $query['id'];
					
				unset($query['id']);
					
				break;
		}
    }
       
    return $segments;
}

function RSDirectoryParseRoute($segments)
{
    //Get the active menu item.
    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();
        
    $query = array();
        
    foreach ($segments as $i => $segment)
    {
        $segments[$i] = str_replace(':', '-', $segment);
    }
        
    if ( $item && $item->component == 'com_rsdirectory' && isset($item->query['view']) )
    {
		switch ($item->query['view'])
		{
			case 'credits':
					
				if ( $segments[0] == JText::_('COM_RSDIRECTORY_SEF_CREDITS_WIRE_TRANSFER') )
				{
					array_unshift( $segments, JText::_('COM_RSDIRECTORY_SEF_CREDITS') );
				}
					
				break;
					
			case 'myaccount':
					
				if ( in_array( $segments[0], array( JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT_TRANSACTIONS'), JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT_CREDITS_HISTORY') ) ) )
				{
					array_unshift( $segments, JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT') );
				}
					
				break;
					
			case 'entry':
					
				if ( in_array( $segments[0], array( JText::_('COM_RSDIRECTORY_SEF_ENTRY_ADD'), JText::_('COM_RSDIRECTORY_SEF_ENTRY_EDIT'), JText::_('COM_RSDIRECTORY_SEF_ENTRY_VIEW'), JText::_('COM_RSDIRECTORY_SEF_ENTRY_THANK_YOU'), JText::_('COM_RSDIRECTORY_SEF_ENTRY_FINALIZE') ) ) )
				{
					array_unshift( $segments, JText::_('COM_RSDIRECTORY_SEF_ENTRY') );
				}
					
				break;
					
			case 'entries':
					
				if ( in_array( $segments[0], array( JText::_('COM_RSDIRECTORY_SEF_CATEGORY'), JText::_('COM_RSDIRECTORY_SEF_USER'), JText::_('COM_RSDIRECTORY_SEF_FILTER') ) ) )
				{
					array_unshift( $segments, JText::_('COM_RSDIRECTORY_SEF_ENTRIES') );
				}
					
				break;
        }
    }
        
    switch ($segments[0])
    {
		case JText::_('COM_RSDIRECTORY_SEF_CATEGORIES'):
				
			$query['view'] = 'categories';
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_CONTACT'):
				
			$query['view'] = 'contact';
				
			if ( isset($segments[1]) )
			{
				$query['entry_id'] = (int)$segments[1];	
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_CREDITS'):
				
			$query['view'] = 'credits';
				
			if ( isset($segments[2]) && $segments[1] == JText::_('COM_RSDIRECTORY_SEF_CREDITS_WIRE_TRANSFER') )
			{
				$query['wiretransfer'] = 1;
				$query['id'] = $segments[2];
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_ENTRIES'):
				
			$query['view'] = 'entries';
				
			if ( isset($segments[3]) && $segments[1] == JText::_('COM_RSDIRECTORY_SEF_CATEGORY') )
			{
				$query['categories'] = (int)$segments[3];
			}
			if ( isset($segments[3]) && $segments[1] == JText::_('COM_RSDIRECTORY_SEF_USER') )
			{
				$query['users'] = (int)$segments[3];
			}
			else if ( isset($segments[1]) && $segments[1] == JText::_('COM_RSDIRECTORY_SEF_FILTER') )
			{
				$query['filter'] = 1;
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_ENTRY'):
				
			$query['view'] = 'entry';
				
			if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_ENTRY_EDIT') )
			{
				$query['layout'] = 'edit';
					
				if ( isset($segments[2]) )
				{
					$query['id'] = (int)$segments[2];	
				}
			}
			else if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_ENTRY_FINALIZE') )
			{
				if ( $segments[2] == JText::_('COM_RSDIRECTORY_SEF_ENTRY_FINALIZE_CONFIRM') )
				{
					$query['layout'] = 'finalize_confirm';
						
					if ( isset($segments[3]) )
					{
						$query['id'] = (int)$segments[3];
					}
				}
				else
				{
					$query['task'] = 'entry.finalize';
						
					if ( isset($segments[2]) )
					{
						$query['id'] = (int)$segments[2];	
					}
				}
			}
			else if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_ENTRY_THANK_YOU') )
			{
				$query['layout'] = 'thank_you';
			}
			else
			{
				$query['layout'] = 'default';
					
				if ( isset($segments[2]) )
				{
					$query['id'] = (int)$segments[2];	
				}
			}
				
			break;
			
		case JText::_('COM_RSDIRECTORY_SEF_ENTRY_REPORT'):
				
			$query['view'] = 'entryreport';
				
			if ( isset($segments[1]) )
			{
				$query['entry_id'] = (int)$segments[1];	
			}
				
			break;
			
		case JText::_('COM_RSDIRECTORY_SEF_OWNER_REPLY'):
				
			$query['view'] = 'ownerreply';
				
			if ( isset($segments[1]) )
			{
				$query['review_id'] = (int)$segments[1];	
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_TRANSACTION'):
				
			$query['view'] = 'transaction';
			 
			if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_TRANSACTION_VIEW') )
			{
				$query['layout'] = 'view';
				$query['id'] = (int) $segments[2];
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_PROCESS_FILTERS'):
				
			$query['task'] = 'filters.process';
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_FILE'):
				
			if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_FILE_DOWNLOAD') )
			{
				$query['task'] = 'file.download';
				$query['hash'] = $segments[2];
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_FIELD'):
				
			if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_FIELD_CAPTCHA') )
			{
				$query['task'] = 'field.captcha';
			}
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_IMAGE'):
				
			if ( $segments[1] == JText::_('COM_RSDIRECTORY_SEF_IMAGE_VIEW') )
			{
				$query['task'] = 'image.view';
				$query['size'] = $segments[2];
				$query['hash'] = $segments[3];
			}
				
			break;
			
		case JText::_('COM_RSDIRECTORY_SEF_FAVORITES'):
				
			$query['view'] = 'favorites';
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_MY_ENTRIES'):
				
			$query['view'] = 'myentries';
				
			break;
				
		case JText::_('COM_RSDIRECTORY_SEF_MY_ACCOUNT'):
				
			$query['view'] = 'myaccount';
				
			if ( isset($segments[1]) )
			{
				$query['tab'] = $segments[1];
			}
				
			break;
    }
		
    return $query;
}