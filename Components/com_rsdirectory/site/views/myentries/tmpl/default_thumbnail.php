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

if ($form->listing_row_show_thumbnails || $form->listing_row_show_ratings || $form->listing_row_show_price)
{
    ?>
        
    <div class="span3">
		<div class="rsdir-listing-thumb-wrapper">
			<?php
				
			if ($form->listing_row_show_ratings)
			{
				?>
					
				<div class="rsdir-listing-rating-wrap">
					<div class="rsdir-listing-rating" data-rating="<?php echo $item->avg_rating; ?>"></div>
					(<a href="<?php echo $this->item_url; ?>#reviews"><?php echo $item->ratings_count; ?></a>)
				</div>
					
				<?php
			}
				
			if ($form->listing_row_show_thumbnails)
			{
				if ( $images_field = RSDirectoryHelper::findFormField('images', $form_fields) )
				{
					// Get the images count.
					$image_count = empty($images_field->files) ? 0 : count($images_field->files);
						
					?>
						
					<div class="thumbnail" style="max-width: <?php echo $this->escape($this->width); ?>px;">
						<a href="<?php echo $this->item_url; ?>" title="<?php echo $this->title_str; ?>">
						<?php
						if ( empty($images_field->files) )
						{
							?>
							<i class="rsdir-no-image" style="height: <?php echo $this->escape($this->height); ?>px;"></i>    
							<?php
						}
						else
						{
							$src = RSDirectoryHelper::getImageURL($images_field->files[0]->hash, 'small');
								
							?>
							<img src="<?php echo $src; ?>" alt="" width="<?php echo $this->escape($this->width); ?>" height="<?php echo $this->escape($this->height); ?>" />
							<?php
						}
						?>
						</a>
						<?php if ($form->listing_row_show_images_number) { ?>
						<div class="rsdir-listing-images-count" title="<?php echo JText::plural('COM_RSDIRECTORY_NUMBER_OF_IMAGES', $image_count); ?>">
							<?php echo empty($images_field->files) ? 0 : $image_count; ?>
						</div>
						<?php } ?>
					</div>
						
					<?php
				}
			}
				
			if ($form->listing_row_show_price)
			{
				if ( $price_field = RSDirectoryHelper::findFormField('price', $form_fields) )
				{
					?>
						
					<div class="rsdir-listing-price">
						<span class="rsdir-listing-price-label hidden-phone hidden-tablet">
							<?php echo JText::_('COM_RSDIRECTORY_LISTING_PRICE_LABEL'); ?>
						</span>
						<?php echo $this->escape( RSDirectoryHelper::formatPrice($item->price) ); ?>
					</div>
						
					<?php
				}
			}
				
			?>
				
		</div>
    </div>
        
<?php
}