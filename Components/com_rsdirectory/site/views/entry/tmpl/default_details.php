<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$config = $this->config;
$form = $this->form;
$form_fields = $this->form_fields;
$entry = $this->entry;

?>

<div class="span<?php echo empty($this->images) ? 12 : 7; ?>">

<?php
		
	$buttons = array();
		
	if ($entry->promoted)
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
		
	if ( empty($this->print) )
	{
		if ($this->can_edit_entry)
		{
			$buttons[] = '<a class="rsdir-edit label label-info" href="' . RSDirectoryRoute::getEntryURL($entry->id, $entry->title, 'edit') . '" title="' . JText::_('COM_RSDIRECTORY_EDIT_LINK_TITLE') . '">' . JText::_('COM_RSDIRECTORY_EDIT') . '</a>';
				
			if (!$entry->paid)
			{
				$buttons[] = '<a class="rsdir-finalize label label-info" href="' . JRoute::_("index.php?option=com_rsdirectory&task=entry.finalize&id=$entry->id") . '" title="' . JText::_('COM_RSDIRECTORY_FINALIZE_LINK_TITLE') . '">' . JText::_('COM_RSDIRECTORY_FINALIZE') . '</a>';
			}
		}
			
		if ($this->can_delete_entry)
		{
			$buttons[] = '<a id="rsdir-detail-delete-entry" class="label label-important" data-entry-id="' .  $entry->id . '" title="' . JText::_('COM_RSDIRECTORY_DELETE_LINK_TITLE') . '">' . JText::_('COM_RSDIRECTORY_DELETE') . '</a>';
		}
	}
		
	?>
		
	<?php if ($buttons) { ?>
	<div class="control-group clearfix">
		<?php echo implode(' ', $buttons); ?>
	</div>
	<?php } ?>
        
    <?php if ($form->listing_detail_show_title) { ?>
    <div class="page-header">
        <h2 class="rsdir-detail-title"><?php echo $this->escape($entry->title); ?></h2>
    </div>
    <?php } ?>
        
	<?php
	if ($form->listing_detail_show_big_subtitle)
	{
		if ( $big_subtitle_field = RSDirectoryHelper::findFormField('big_subtitle', $form_fields) )
		{
			$big_subtitle = RSDirectoryHelper::cleanText( $this->entry->big_subtitle, true, $big_subtitle_field->properties->get('clean_links') );
				
			if ($big_subtitle)
			{
				$big_subtitle = $big_subtitle_field->properties->get('allow_html') ? $big_subtitle : nl2br( strip_tags($big_subtitle) );
					
				?>
					
				<h3 class="rsdir-detail-big-subtitle rsdir-detail-section"><?php echo $big_subtitle; ?></h3>
					
				<?php
			}
		}
	}
		
	if ($form->listing_detail_show_small_subtitle)
	{
		if ( $small_subtitle_field = RSDirectoryHelper::findFormField('small_subtitle', $form_fields) )
		{
			$small_subtitle = RSDirectoryHelper::cleanText( $this->entry->small_subtitle, true, $small_subtitle_field->properties->get('clean_links') );
				
			if ($small_subtitle)
			{
				$small_subtitle = $small_subtitle_field->properties->get('allow_html') ? $small_subtitle : nl2br( strip_tags($small_subtitle) );
					
				?>
					
				<div class="rsdir-detail-small-subtitle rsdir-detail-section"><?php echo $small_subtitle; ?></div>
					
				<?php
			}
		}
	}
		
	$price_field = RSDirectoryHelper::findFormField('price', $form_fields)
		
	?>
        
    <?php if ( ($form->listing_detail_show_price && $price_field) || $form->listing_detail_show_expiry_time ) { ?>
    <div class="rsdir-price-wrapper clearfix">
            
        <div class="span7">
        <?php
        if ($form->listing_detail_show_expiry_time)
        {
            ?>
                
            <div class="rsdir-detail-expiry-date">
                <span class="rsdir-listing-expiry-date-label"><?php echo JText::_('COM_RSDIRECTORY_LISTING_EXPIRY_DATE_LABEL'); ?></span>
                <?php
                if ($entry->expiry_time == '0000-00-00 00:00:00')
                {
                    echo strtolower( JText::_('COM_RSDIRECTORY_NO_EXPIRY') );
                }
                else if ( JFactory::getDate($entry->expiry_time)->toUnix() < JFactory::getDate()->toUnix() )
                {
                    echo JText::_('COM_RSDIRECTORY_ENTRY_EXPIRED');
                }
                else
                {
                    echo RSDirectoryHelper::formatDate($entry->expiry_time);
                }
                ?>
            </div>
                
            <?php
        }
        ?>
        </div>
            
        <?php if ($form->listing_detail_show_price && $price_field) { ?>
		<div class="rsdir-detail-price span5">
			<span class="rsdir-detail-price-label hidden-tablet">
				<?php echo JText::_('COM_RSDIRECTORY_LISTING_PRICE_LABEL'); ?>
			</span>
			<?php echo $this->escape( RSDirectoryHelper::formatPrice($entry->price) ); ?>
		</div>
		<?php } ?>
            
    </div>
    <?php } ?>
        
    <?php if ( $entry_meta = RSDirectoryHelper::getEntryMeta($entry, $form, 'listing_detail') ) { ?>
	<div class="rsdir-detail-meta rsdir-detail-section clearfix">
		<?php echo $entry_meta; ?>
	</div>
    <?php } ?>
        
    <?php if ($form->listing_detail_show_hits || $form->listing_detail_show_recommend) { ?>
    <div id="rsdir-detail-social" class="rsdir-detail-section row-fluid">
        <?php if ($form->listing_detail_show_hits) { ?>
        <div class="span3">
            <i class="icon-signal"></i> <?php echo JText::sprintf( 'COM_RSDIRECTORY_HITS_NUMBER', $entry->hits ); ?>
        </div>
        <?php } ?>
        <?php if ($form->listing_detail_show_recommend && !$this->print) { ?>
        <div class="span9">
            <ul id="rsdir-recommend">
                <li>
                    <?php echo JText::_('COM_RSDIRECTORY_RECOMMEND'); ?>
                </li>
                <li>
                    <a class="rsdir-recommend-facebook" href="#" onclick="
                    window.open(
                        'https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( JUri::getInstance()->toString() ); ?>', 
                        'facebook-share-dialog', 
                        'width=626,height=436'
                    ); 
                    return false;"></a>
                </li>
                <li>
                    <a class="rsdir-recommend-twitter" href="#" onclick="
                    window.open(
                        'https://twitter.com/share?url=<?php echo urlencode( JUri::getInstance()->toString() ); ?>',
                        'tweet-dialog',
                        'width=626,height=436'
                    );
                    return false;"></a>
                </li>
            </ul>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
        
    <?php
    if ($form->listing_detail_show_ratings)
    {
        echo RSDirectoryHelper::getRatingHTML($entry->avg_rating, $entry->ratings_count);
    }
    ?>
		
	<?php if ( empty($this->print) && !empty($this->contact) && ( empty($this->images) || !RSDirectoryHelper::findFormField('images', $form_fields) ) ) { ?>
	<div class="text-right">
		<?php echo $this->contact; ?>
	</div>
	<?php } ?>
</div>