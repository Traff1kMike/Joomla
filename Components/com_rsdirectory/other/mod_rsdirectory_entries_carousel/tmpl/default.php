<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<div class="rsdir">
	<div id="<?php echo $carousel_id; ?>" class="carousel slide">
	<?php if ($slides) { ?>
		<?php if ($display_indicators) { ?>
		<ol class="carousel-indicators">
			<?php foreach ($slides as $i => $slide) { ?>
			<li<?php echo $i ? '' : ' class="active"'; ?> data-target="#<?php echo $carousel_id; ?>" data-slide-to="<?php echo $i; ?>"></li>
			<?php } ?>
		</ol>
		<?php } ?>
			
		<!-- Carousel items -->
		<div class="carousel-inner">
		<?php
			
		foreach ($slides as $i => $slide)
		{
			?>
				
			<div class="item<?php echo $i ? '' : ' active'; ?>">
				<div class="row-fluid carousel-entries">
					
				<?php
					
				foreach ($slide as $entry)
				{
					$url = RSDirectoryRoute::getEntryURL($entry->id, $entry->title, '', $itemid);
						
					$title = RSDirectoryHelper::escapeHTML($entry->title);
						
					?>
						
					<div class="carousel-entry span<?php echo $span; ?>">
						
					<?php
						
					if ($display_thumbs)
					{
						// Get the images field.
						$images_field = RSDirectoryHelper::findFormField('images', $entry->form->fields);
							
						?>
							
						<div class="thumbnail" style="max-width: <?php echo $thumb_max_width; ?>px;">
							<a href="<?php echo $url; ?>" title="<?php echo $title; ?>">
							<?php
							if ( empty($images_field->files) )
							{
								?>
								<i class="rsdir-no-image" style="width: <?php echo $thumb_max_width; ?>px; height: <?php echo $thumb_max_width; ?>px; background-image: url(<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/no-image.png);"></i>    
								<?php
							}
							else
							{
								$src = RSDirectoryHelper::getImageURL($images_field->files[0]->hash, 'small');
									
								?>
								<img src="<?php echo $src; ?>" alt="" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
								<?php
							}
							?>
							</a>
						</div>
							
						<?php
					}
						
					?>
						
					<?php if ($display_titles) { ?>
					<strong class="carousel-entry-title">
						<a href="<?php echo $url; ?>">
							<?php echo $title; ?>
						</a>
					</strong>
					<?php } ?>
						
					<?php if ($display_prices) { ?>
					<span class="carousel-entry-price label label-success">
					<?php echo RSDirectoryHelper::formatPrice($entry->price); ?>
					</span>
					<?php } ?>
						
					<?php if ($display_ratings) { ?>
					<div>
						<div class="carousel-entry-rating" data-rating="<?php echo $entry->avg_rating; ?>"></div>
						<span class="carousel-entry-votes"><?php echo JText::plural('MOD_RSDIRECTORY_ENTRIES_CAROUSEL_VOTES', $entry->ratings_count); ?></span>
					</div>
					<?php } ?>
						
					</div><!-- .carousel-entry -->
						
					<?php } ?>
						
				</div><!-- .carousel-entries -->
			</div><!-- .item -->
				
		<?php } ?>
		</div><!-- . carousel-inner -->
			
		<?php if ($display_nav) { ?>
		<a class="carousel-control left" href="#<?php echo $carousel_id; ?>" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#<?php echo $carousel_id; ?>" data-slide="next">&rsaquo;</a>
		<?php } ?>
	<?php } ?>
	</div>
</div>
