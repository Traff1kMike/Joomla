<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Entries view.
 */
class RSDirectoryViewEntries extends JViewLegacy
{
    /**
     * The display function.
     *
     * @access public
     * 
     * @param mixed $tpl
     */
    function display($tpl = null)
    {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$config = RSDirectoryConfig::getInstance();
			
		$width = $config->get('small_thumbnail_width');
		$height = $config->get('small_thumbnail_height');
			
		$app->input->set( 'limit', $app->getCfg('feed_limit') );
		$rows = $this->get('Items');
			
		foreach ($rows as $row)
		{
			// Strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
				
			$item = new JFeedItem;
			$item->title = $title;
			$item->link = RSDirectoryRoute::getEntryURL($row->id, $row->title);
			$item->date = JFactory::getDate($row->published_time)->format('r');
			$item->category = $row->category_title;
			$item->author = $row->author;
				
			$description = '<div class="feed-description">';
				
			if ( !empty($row->form->fields) )
			{
				$images = RSDirectoryHelper::findFormField('images', $row->form->fields);
					
				if ( $images && !empty($images->files[0]->hash) )
				{
					$src = RSDirectoryHelper::getImageURL($images->files[0]->hash, 'small');
					$description .= '<img src="' . $src . '" alt="" width="' . $width . '" height="' . $height . '" />';
				}
			}
				
			$description .= $row->description;
			$description .= '</div>';
			
			$item->description = $description;
			
				
			// Loads item info into rss array
			$doc->addItem($item);
		}
    }
}