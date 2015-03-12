<?php
/**
 * @version		$Id: mod_visiafolio.php 2.1.0
 * @based on mod_latestnews
 * @package		Joomla 3.1.x
 * @subpackage	mod_visiafolio
 * @copyright	Copyright (C) 2005 - 20132 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

jimport('joomla.application.component.model');


abstract class modVisiaFolioHelper 
{	

	public static function getAjax() {
		// Get module parameters
		jimport('joomla.application.module.helper');
		$input  = JFactory::getApplication()->input;
		$module = JModuleHelper::getModule('visiafolio');
		$params = new JRegistry();
		$params->loadString($module->params);
		$node        = $params->get('node', 'data');
		$format     = $params->get('format', 'raw');

	}

	public static function getList(&$params, $modid)
	{
		// Get the dbo
		$db = JFactory::getDbo();
		$modulebase = ''.JURI::base(true).'/modules/mod_visiafolio/';
		
		JLoader::import( 'joomla.version' );
		$version = new JVersion();
		if (version_compare( $version->RELEASE, '2.5', '<=')) 
		{
			JModel::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
		} else {
			JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
		}
		
		// Get an instance of the generic articles model
		if (version_compare( $version->RELEASE, '2.5', '<=')) 
		{
			$articles = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		} else {
			$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		}
		
		
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$articles->setState('params', $appParams);
		
		//Get Parameters in module params
		$count	= (int) $params->get('count', 6);
		$catids = $params->get('catid');
		$show_featured	= $params->get('show_featured', 1);
		$show_introtext	= $params->get( 'show_introtext', 0 );
		$introtext_limit = $params->get('introtext_limit', 100);
		$show_date_type	= $params->get( 'show_date_type', 0 );
		
		//Get the time offset from config
		$config = JFactory::getConfig();
		$offset = $config->get('config.offset');

		// Set the filters based on the module params
		$articles->setState('list.start', 0);
		$articles->setState('list.limit', $count);
		$articles->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$articles->setState('filter.access', $access);

		// Category filter
		$articles->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));
		// Category filter
		if ($catids) {
			if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				//$categories = JModel::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categories = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				
				$categories->setState('params', $appParams);
				$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$categories->setState('filter.access', $access);
				$additional_catids = array();

				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);

					if ($items)
					{
						foreach($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
							if ($condition) {
								$additional_catids[] = $category->id;
							}
							
							$html = $category->description;
							$html .= "alt='...' title='...' />";
							$pattern = '/<img[^>]+src[\\s=\'"]';
							$pattern .= '+([^"\'>\\s]+)/is';
				
							if(preg_match( $pattern, $html, $match)) { 
								$category->firstImage = "$match[1]";
							}
						
						}
					}
				}

				$catids = array_unique(array_merge($catids, $additional_catids));
			}

			$articles->setState('filter.category_id', $catids);
		}

		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$articles->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$articles->setState('filter.author_id', $userId);
				$articles->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$articles->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}

		

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$articles->setState('filter.featured', 'only');
				break;
			case '0':
				$articles->setState('filter.featured', 'hide');
				break;
			default:
				$articles->setState('filter.featured', 'show');
				break;
		}

		// ordering
		$articles->setState('list.ordering', $params->get('article_ordering', 'a.ordering'));
		$articles->setState('list.direction', $params->get('article_ordering_direction', 'DESC'));

		// New Parameters
		
		$articles->setState('filter.featured', $params->get('show_front', 'show'));
		$articles->setState('filter.author_id', $params->get('created_by', ""));
		$articles->setState('filter.author_id.include', $params->get('author_filtering_type', 1));
		$articles->setState('filter.author_alias', $params->get('created_by_alias', ""));
		$articles->setState('filter.author_alias.include', $params->get('author_alias_filtering_type', 1));
		$excluded_articles = $params->get('excluded_articles', '');

		if ($excluded_articles) {
			$excluded_articles = explode("\r\n", $excluded_articles);
			$articles->setState('filter.article_id', $excluded_articles);
			$articles->setState('filter.article_id.include', false); // Exclude
		}

		$date_filtering = $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off') {
			$articles->setState('filter.date_filtering', $date_filtering);
			$articles->setState('filter.date_field', $params->get('date_field', 'a.created'));
			$articles->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$articles->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
			$articles->setState('filter.relative_date', $params->get('relative_date', 30));
		}
		
		// Filter by language
		$articles->setState('filter.language', $app->getLanguageFilter());

		$items = $articles->getItems();

		foreach ($items as &$item) 
		{
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;
			
			if ($access || in_array($item->access, $authorised)) {
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
				// Let's just make the Category title linked to Category Blog Layout as category list link will result 404 page not found - schro
				$item->CategoryBlogLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug));
			} else {
				// Angie Fixed Routing
				$app	= JFactory::getApplication();
				$menu	= $app->getMenu();
				$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
						
				if(isset($menuitems[0])) {
					$Itemid = $menuitems[0]->id;
				} else if (JRequest::getInt('Itemid') > 0) { 
					//use Itemid from requesting page only if there is no existing menu
					$Itemid = JRequest::getInt('Itemid');
				}
				$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
			}

			
			$item->title = htmlspecialchars( $item->title );
			$item->title_alias = self::slug($item->title);
			$item->tag 	= $item->category_title;
			$item->tag_alias = $item->category_alias;
			
			/* The code lines bellow are added to support intro and fulltext image in article parameter - schro added 13/11/2012 */
			
			$item->image = ""; $img = ""; $imgtitle = ""; $imgalt = "";
			$images = json_decode($item->images);
			if ( isset($images->image_intro) and !empty($images->image_intro) )   {
				$img = htmlspecialchars($images->image_intro);
				$imgtitle = ($images->image_intro_alt) ? htmlspecialchars($images->image_intro_alt):$item->title;
				
			}
			elseif ( isset($images->image_fulltext) and !empty($images->image_fulltext) ) {
				$img = htmlspecialchars($images->image_fulltext);
				$imgtitle = ($images->image_fulltext_alt) ? htmlspecialchars($images->image_fulltext_alt) : $item->title;
				
			}
			else {
						
				/* If still no image in fulltext parameter can be found, then find out image inserted inside text editor */
				/* end added - schro */
						
				$html = $item->introtext;
				$html .= "alt='...' title='...' />";
				$pattern = '/<img[^>]+src[\\s=\'"]';
				$pattern .= '+([^"\'>\\s]+)/is';
				if ( preg_match($pattern, $html, $match) ) {
					$img = "$match[1]";
					$imgalt = $imgtitle = JText::_('img alt');
				} else {
					$img = $modulebase .'assets/noimage.jpg';
					$imgalt = $imgtitle = JText::_('img alt');
				}
						
			}
			
			if ($img) {
				$attribs = array();
				$makethumb = $params->get('make_thumb', 1);
				if ( $makethumb ) {
					
					$img = str_replace(JURI::base(false),'',$img);
					$attribs['alt'] = ($imgtitle) ? $imgtitle:JText::_('image alt');
					$attribs['title'] = ($imgtitle) ? $imgtitle:JText::_('image title');
					$item->image = modVisiaFolioHelper::getThumbnail($img,$params,$modid,$attribs);
					
				} else {
					$width  = $params->get('thumb_width', 90);
					$height = $params->get('thumb_height', 90);
					if ( $width ) {
						$attribs['width'] = $width;
					}
					if ( $height ) {
						$attribs['height'] = $height;
					}
					$attribs['alt'] = ($imgtitle) ? $imgtitle:'image alt';
					$attribs['title'] = ($imgtitle) ? $imgtitle:'image title';
					$item->image  = JHTML::_('image', $img, '', $attribs);
				}
			}
			// end of thumbnail processing

			
			$item->introtext = JHtml::_('content.prepare', $item->introtext);
			if ($introtext_limit) {
				$item->displayIntrotext = preg_replace("/{[^}]*}/","", $item->introtext);
				$item->displayIntrotext = self::cleanIntrotext($item->introtext);
				$item->displayIntrotext = self::truncate($item->displayIntrotext, $introtext_limit);
			} else {
				$item->displayIntrotext = null;
			}

			$item->created = self::getFormattedDate($item->created, $offset, $show_date_type);
			
		
		}

		return $items;
	}

	// Get Category titles
	public static function getCategoryTitles($catids)
	{
		//$categories = $params->get('catid', array());
		$items = array();
		
		if (implode(",",$catids)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select("id, title, alias")
				->from('#__categories')
				->where("id IN (".implode(",",$catids).")")
				->where("published = 1")
				->order("title ASC");
			$db->setQuery($query);
			$results = $db->loadObjectList();
			
			foreach ($results as $result) {
				$items[$result->id] = $result->alias; // $result->title
			}
		}
		
		return $items;
	}

	
	public static function getThumbnail($img,&$params,$modid, $attribs) 
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$width      = $params->get('thumb_width',90);
		$height     = $params->get('thumb_height',90);
		$thumb_option = $params->get('thumb_option','bestfit');
		//$img_type   = $params->get('thumb_type','');
		//$bgcolor    = hexdec($params->get('thumb_bg','#FFFFFF'));
		
		$img_name   = pathinfo($img, PATHINFO_FILENAME);
		$img_ext    = pathinfo($img, PATHINFO_EXTENSION);
		$img_path   = JPATH_BASE  . '/' . $img;
		$size 	    = @getimagesize($img_path);
		
		$errors = array();
		
		if(!$size) 
		{	
			$errors[] = 'There was a problem loading image ' . $img_name . '.' . $img_ext;
		
		} else {
					
			
	
			$origw = $size[0];
			$origh = $size[1];
			if( ($origw<$width && $origh<$height)) {
				$width = $origw;
				$height = $origh;
			}
			
			$prefix = substr($thumb_option,0,1) . "_".$width."x".$height."_";
	
			$thumb_file =  $prefix . str_replace(array( JPATH_ROOT, ':', '/', '\\', '?', '&', '%20', ' '),  '_' ,$img_name . '.' . $img_ext);
			$dir = JPATH_BASE.'/images/visiafolio/thumbs-' . $modid . '/';
			if (!JFolder::exists($dir)) JFolder::create($dir);
			$thumb_path = $dir . $thumb_file;
			
			//$attribs = array();
			
			if (JFile::exists($thumb_path))	{
				$size = @getimagesize($thumb_path);
				if($size) {
					//$attribs['width']  = $size[0];
					//$attribs['height'] = $size[1];
				}
			} else {
		
				modVisiaFolioHelper::calculateSize($origw, $origh, $width, $height, $thumb_option, $newwidth, $newheight, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
	
				switch(strtolower($size['mime'])) {
					case 'image/png':
						$imagecreatefrom = "imagecreatefrompng";
						break;
					case 'image/gif':
						$imagecreatefrom = "imagecreatefromgif";
						break;
					case 'image/jpeg':
						$imagecreatefrom = "imagecreatefromjpeg";
						break;
					default:
						$errors[] = "Unsupported image type $img_name.$img_ext ".$size['mime'];
				}
	
				
				if ( !function_exists ( $imagecreatefrom ) ) {
					$errors[] = "Failed to process $img_name.$img_ext. Function $imagecreatefrom doesn't exist.";
				}
				
				$src_img = $imagecreatefrom($img_path);
				
				if (!$src_img) {
					$errors[] = "There was a problem to process image $img_name.$img_ext ".$size['mime'];
				}
				
				$dst_img = ImageCreateTrueColor($width, $height);
				
				/*$bgcolor = imagecolorallocatealpha($img, 200, 200, 200, 127);
				
				imagefill( $dst_img, 0,0, $bgcolor);
				if ( $thumb_option == 'transparent' ) {
					imagecolortransparent($dst_img, $bgcolor);
				}
				*/
				
				imagecopyresampled($dst_img,$src_img, $dst_x, $dst_y, $src_x, $src_y, $newwidth, $newheight, $src_w, $src_h);		
				
				switch(strtolower($img_ext)) {
					case 'png':
						$imagefunction = "imagepng";
						break;
					case 'gif':
						$imagefunction = "imagegif";
						break;
					default:
						$imagefunction = "imagejpeg";
				}
				
				if($imagefunction=='imagejpeg') {
					$result = @$imagefunction($dst_img, $thumb_path, 80 );
				} else {
					$result = @$imagefunction($dst_img, $thumb_path);
				}
	
				imagedestroy($src_img);
				if(!$result) {				
					if(!$disablepermissionwarning) {
					$errors[] = 'Could not create image:<br />' . $thumb_path . '.<br /> Check if the folder exists and if you have write permissions:<br /> ' . JURI::base(false).'images/visiafolio//thumbs-' . $modid.'/';
					}
					$disablepermissionwarning = true;
				} else {
					imagedestroy($dst_img);
				}
			}
		}
		
		if (count($errors)) {
			JError::raiseWarning(404, implode("\n", $errors));
			return false;
		}
		
		$image = JURI::base(false)."images/visiafolio/thumbs-$modid/" . basename($thumb_path);
		
		return  JHTML::_('image', $image, '', $attribs);
	}
	
	public static function calculateSize($origw, $origh, &$width, &$height, &$thumb_option, &$newwidth, &$newheight, &$dst_x, &$dst_y, &$src_x, &$src_y, &$src_w, &$src_h) {
		
		if(!$width ) {
			$width = $origw;
		}

		if(!$height ) {
			$height = $origh;
		}

		if ( $height > $origh ) {
			$newheight = $origh;
			$height = $origh;
		} else {
			$newheight = $height;
		}
		
		if ( $width > $origw ) {
			$newwidth = $origw;
			$width = $origw;
		} else {
			$newwidth = $width;
		}
		
		$dst_x = $dst_y = $src_x = $src_y = 0;

		switch($thumb_option) {
			case 'fill':
			case 'transparent':
				$xscale=$origw/$width;
				$yscale=$origh/$height;

				if ($yscale<$xscale){
					$newheight =  round($origh/$origw*$width);
					$dst_y = round(($height - $newheight)/2);
				} else {
					$newwidth = round($origw/$origh*$height);
					$dst_x = round(($width - $newwidth)/2);

				}

				$src_w = $origw;
				$src_h = $origh;
				break;

			case 'crop':

				$ratio_orig = $origw/$origh;
				$ratio = $width/$height;
				if ( $ratio > $ratio_orig) {
					$newheight = round($width/$ratio_orig);
					$newwidth = $width;
				} else {
					$newwidth = round($height*$ratio_orig);
					$newheight = $height;
				}
					
				$src_x = ($newwidth-$width)/2;
				$src_y = ($newheight-$height)/2;
				$src_w = $origw;
				$src_h = $origh;				
				break;
				
 			case 'only_cut':
				// }
				$src_x = round(($origw-$newwidth)/2);
				$src_y = round(($origh-$newheight)/2);
				$src_w = $newwidth;
				$src_h = $newheight;
				
				break; 
				
			case 'bestfit':
				$xscale=$origw/$width;
				$yscale=$origh/$height;

				if ($yscale<$xscale){
					$newheight = $height = round($width / ($origw / $origh));
				}
				else {
					$newwidth = $width = round($height * ($origw / $origh));
				}
				$src_w = $origw;
				$src_h = $origh;	
				
				break;
			}

	}
	
	//Create slug from title
	public static function slug($text) {
		return preg_replace('/[^a-z0-9_]/i','-', strtolower(trim($text)));
	}

	public static function getFormattedDate($date, $offset, $show_date_type) 
	{

		switch ($show_date_type) {
				case 0:
					return JHTML::_('date', htmlspecialchars( $date ), JText::_('DATE_FORMAT_LC1'), $offset); // l, d F Y H:i
					break;
				case 1:
					return JHTML::_('date', htmlspecialchars( $date ), JText::_('DATE_FORMAT_LC2'), $offset); // d F Y
					break;
				case 2:
					return JHTML::_('date', htmlspecialchars( $date ), JText::_('DATE_FORMAT_LC3'), $offset); // l, d F Y
					break;
				case 3:
					return JHTML::_('date', htmlspecialchars( $date ),'H:i', $offset); // hour:minute
					break;
				case 4:
					return JHTML::_('date', htmlspecialchars( $date ),'D, M jS Y', $offset); // Day name(short), Month(short) date th Year
					break;
				case 5:
					return JHTML::_('date', htmlspecialchars( $date ),'l, F jS Y H:i', $offset); // Day name(short), Month(short) date th Year Hour:minute
					break;
				case 6: default:
					return JHTML::_('date', htmlspecialchars( $date ),'m/d/Y', $offset); // hour:minute
					break;
		}
	}

	public static function cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<em><strong>');

		$introtext = trim($introtext);

		return $introtext;
	}
	
	public static function truncate($html, $maxLength = 0)
	{
		$baseLength = strlen($html);
		$diffLength = 0;

		// First get the plain text string. This is the rendered text we want to end up with.
		$ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);

		for ($maxLength; $maxLength < $baseLength;)
		{
			// Now get the string if we allow html.
			$htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);

			// Now get the plain text from the html string.
			$htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);

			// If the new plain text string matches the original plain text string we are done.
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}
			// Get the number of html tag characters in the first $maxlength characters
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);

			// Set new $maxlength that adjusts for the html tags
			$maxLength += $diffLength;
			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}
		return $html;
	}
}
