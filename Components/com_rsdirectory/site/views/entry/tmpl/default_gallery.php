<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$images_field = RSDirectoryHelper::findFormField('images', $this->form_fields);

if ( empty($this->images) || !$images_field )
	return;

$config = $this->config;

?>

<div id="rsdir-gallery" class="span5">
    <div class="thumbnails" style="max-width: <?php echo $this->escape( $config->get('big_thumbnail_width') ); ?>px;">
            
		<div id="rsdir-big-thumbs">
		<?php
		// Display the big thumbs.
		foreach ($this->images as $i => $image)
		{
			$src = RSDirectoryHelper::getImageURL($image->hash, 'big');
				
			?>    
				
			<div class="thumbnail<?php echo $i ? ' hidden-desktop hidden-tablet' : ''; ?>">
				<a class="rsdir-img" href="<?php echo RSDirectoryHelper::getImageURL($image->hash); ?>">
					<img src="<?php echo $src; ?>" alt="<?php echo $this->escape($image->original_file_name); ?>" />
				</a>
			</div>
				
			<?php
		}
		?>
		</div><!-- #rsdir-big-thumbs -->
			
		<?php
		// Display the small thumbs only if there are at least 2.
		if ( isset($this->images[1]) )
		{
		?>
		<div id="rsdir-small-thumbs">
			<div class="row-fluid hidden-phone">
			<?php
			foreach ($this->images as $i => $image)
			{
				if ($i % 4 == 0)
				{
					?>
					</div>
					<div class="row-fluid hidden-phone">
					<?php
				}
					
				$src = RSDirectoryHelper::getImageURL($image->hash, 'small');
					
				?>
					
				<div class="thumbnail span3" style="max-width: <?php echo $this->escape( $config->get('small_thumbnail_width') ); ?>px;">
					<img src="<?php echo $src; ?>" alt="<?php echo $this->escape($image->original_file_name); ?>" />
				</div>
					
				<?php
			}
			?>
			</div>
		</div><!-- #rsdir-small-thumbs -->
		<?php } ?>
            
    </div><!-- .thumbnails -->
		
	<?php if ( empty($this->print) && !empty($this->contact) ) { ?>
	<div class="<?php echo $config->get('images_detail_position') == 'right' ? 'text-right' : 'text-left'; ?>">
		<?php echo $this->contact; ?>
	</div>
	<?php } ?>
</div><!-- #rsdir-gallery -->