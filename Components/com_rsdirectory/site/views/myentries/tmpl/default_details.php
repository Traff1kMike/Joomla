<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$item = $this->item;
$form = $item->form;
$form_fields = $form->fields;

?>

<div class="span9">
		
	<?php
		
	$buttons = array();
		
	if ($item->promoted)
	{
		$buttons[] = '<span class="rsdir-top-entry label label-info">' . JText::_('COM_RSDIRECTORY_TOP_ENTRY') . '</span>';
	}
		
	if ($this->expired)
	{
		$buttons[] = '<span class="rsdir-expired label label-warning">' . JText::_('COM_RSDIRECTORY_EXPIRED') . '</span>';
	}
	else if (!$this->published)
	{ 
		$buttons[] = '<span class="rsdir-unpublished label label-warning">' . JText::_('COM_RSDIRECTORY_UNPUBLISHED') . '</span>';
	}
		
	if ( !empty($this->can_edit_all_entries) || ( !empty($this->can_edit_own_entries) && $this->user->id == $item->user_id ) )
	{
		$buttons[] = '<a class="rsdir-edit label label-info" href="' . RSDirectoryRoute::getEntryURL($item->id, $item->title, 'edit') . '" title="' . JText::_('COM_RSDIRECTORY_EDIT_LINK_TITLE') . '">' . JText::_('COM_RSDIRECTORY_EDIT') . '</a>';
			
		if (!$this->item->paid)
		{
			$buttons[] = '<a class="rsdir-finalize label label-info" href="' . JRoute::_("index.php?option=com_rsdirectory&task=entry.finalize&id=$item->id") . '" title="' . JText::_('COM_RSDIRECTORY_FINALIZE_LINK_TITLE') . '">' . JText::_('COM_RSDIRECTORY_FINALIZE') . '</a>';
		}
	}
		
	if ( !empty($this->can_delete_all_entries) || ( !empty($this->can_delete_own_entries) && $this->user->id == $item->user_id ) )
	{
		$buttons[] = '<a class="rsdir-listing-delete label label-important" data-entry-id="' .  $item->id . '" title="' . JText::_('COM_RSDIRECTORY_DELETE_LINK_TITLE') . '">' . JText::_('COM_RSDIRECTORY_DELETE') . '</a>';
	}
		
	?>
		
	<?php if ($buttons) { ?>
	<div class="control-group clearfix<?php echo $this->images_listing_detail_position == 'left' ? ' text-right' : ''; ?>">
		<?php echo implode(' ', $buttons); ?>
	</div>
	<?php } ?>
		
	<h2 class="rsdir-listing-title">
		<a href="<?php echo $this->item_url; ?>" title="<?php echo $this->title_str; ?>"><?php echo $this->title_str; ?></a>
	</h2>
		
	<?php
	if ($form->listing_row_show_big_subtitle)
	{
		if ( $big_subtitle_field = RSDirectoryHelper::findFormField('big_subtitle', $form_fields) )
		{
			$big_subtitle = RSDirectoryHelper::cleanText( $item->big_subtitle, true, $big_subtitle_field->properties->get('clean_links') );
				
			if ($big_subtitle)
			{
				$big_subtitle = $big_subtitle_field->properties->get('allow_html') ? $big_subtitle : nl2br( strip_tags($big_subtitle) );
					
				?>
					
				<h3 class="rsdir-listing-big-subtitle"><?php echo $big_subtitle; ?></h3>
					
				<?php
			}
		}
	}
		
	if ($form->listing_row_show_small_subtitle)
	{
		if ( $small_subtitle_field = RSDirectoryHelper::findFormField('small_subtitle', $form_fields) )
		{
			$small_subtitle = RSDirectoryHelper::cleanText( $item->small_subtitle, true, $small_subtitle_field->properties->get('clean_links') );
				
			if ($small_subtitle)
			{
				$small_subtitle = $small_subtitle_field->properties->get('allow_html') ? $small_subtitle : nl2br( strip_tags($small_subtitle) );
					
				?>
					
				<div class="rsdir-listing-small-subtitle"><?php echo $small_subtitle; ?></div>
					
				<?php
			}
		}
	}
        
	if ($this->view == 'myentries')
	{
		?>
			
		<div class="rsdir-listing-expiry-date">
			<span class="rsdir-listing-expiry-date-label"><?php echo JText::_('COM_RSDIRECTORY_LISTING_PUBLISHING_DATE_LABEL'); ?></span>
			<?php echo $item->published_time == '0000-00-00 00:00:00' ? '-' : $item->published_time; ?>
				
			<br />
				
			<span class="rsdir-listing-expiry-date-label"><?php echo JText::_('COM_RSDIRECTORY_LISTING_EXPIRY_DATE_LABEL'); ?></span>
			<?php
			if ($item->expiry_time == '0000-00-00 00:00:00')
			{
				echo strtolower( JText::_('COM_RSDIRECTORY_NO_EXPIRY') );
			}
			else if ( JFactory::getDate($item->expiry_time)->toUnix() < JFactory::getDate()->toUnix() )
			{
				echo strtolower( JText::_('COM_RSDIRECTORY_EXPIRED') );
			}
			else
			{
				echo $this->escape($item->expiry_time);
			}
			?>
		</div>
			
		<?php
	}
    else if ($form->listing_row_show_expiry_time)
    {
		?>
			
		<div class="rsdir-listing-expiry-date">
			<span class="rsdir-listing-expiry-date-label"><?php echo JText::_('COM_RSDIRECTORY_LISTING_EXPIRY_DATE_LABEL'); ?></span>
			<?php
			if ($item->expiry_time == '0000-00-00 00:00:00')
			{
				echo strtolower( JText::_('COM_RSDIRECTORY_NO_EXPIRY') );
			}
			else if ( JFactory::getDate($item->expiry_time)->toUnix() < JFactory::getDate()->toUnix() )
			{
				echo strtolower( JText::_('COM_RSDIRECTORY_EXPIRED') );
			}
			else
			{
				echo $this->escape( RSDirectoryHelper::formatDate($item->expiry_time) );
			}
			?>
		</div>
			
		<?php
    }
		
	$meta = RSDirectoryHelper::getEntryMeta($item, $form);
		
	if ($meta || $form->listing_row_show_favorites_button || $form->listing_row_show_entry_details_link)
	{
		?>
			
		<div class="rsdir-listing-meta clearfix">
				
			<?php
				
			$span = 12;
				
			$span -= $form->listing_row_show_favorites_button ? 2 : 0;
			$span -= $form->listing_row_show_entry_details_link ? 3 : 0;
				
			?>
				
			<div class="span<?php echo $span; ?>">
				<?php echo $meta; ?>
			</div>
				
			<?php if ($form->listing_row_show_favorites_button || $form->listing_row_show_entry_details_link) { ?>
			<div class="pull-right">
				<?php if ($form->listing_row_show_favorites_button) { ?>
					
				<a class="rsdir-entry-fav<?php echo $item->faved ? ' rsdir-entry-faved' : ''; ?> btn" data-entry-id="<?php echo $item->id; ?>" title="<?php echo JText::_($item->faved ? 'COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES' : 'COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES'); ?>"><i class="<?php echo $item->faved ? 'icon-star' : 'icon-star-empty'; ?>"></i></a>
					
				<?php } ?>
					
				<?php if ($form->listing_row_show_entry_details_link) { ?>
					
				<a class="btn" href="<?php echo $this->item_url; ?>" title="<?php echo $this->title_str; ?>"><?php echo JText::_('COM_RSDIRECTORY_VIEW_DETAILS'); ?></a>
				<?php } ?>
			</div>
			<?php } ?>
		</div><!-- .rsdir-listing-meta -->
	<?php
	}
	?>
        
</div>