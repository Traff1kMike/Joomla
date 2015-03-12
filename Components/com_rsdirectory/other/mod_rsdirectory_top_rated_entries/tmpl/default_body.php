<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 

if ($display_titles || $display_prices || $display_ratings)
{
	$body_class = $thumb_position == 'bottom' && $display_thumbs ? ' body-top' : '';
		
	?>
		
	<div class="rsdir-top-rated-entry-body<?php echo $body_class; ?>">
		<?php if ($display_prices) { ?>
		<span class="rsdir-top-rated-entry-price label label-success">
		<?php echo RSDirectoryHelper::formatPrice($entry->price); ?>
		</span>
		<?php } ?>
			
		<?php if ($display_titles) { ?>
		<strong class="rsdir-top-rated-entry-title">
			<a href="<?php echo $url; ?>">
				<?php echo $title; ?>
			</a>
		</strong>
		<?php } ?>
			
		<?php if ($display_ratings) { ?>
		<div class="rsdir-top-rated-entry-rating" data-rating="<?php echo $entry->avg_rating; ?>"></div>
		<?php } ?>
	</div>
		
	<?php
}