<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); 

if ($display_thumbs)
{
	// Get the images field.
	$images_field = RSDirectoryHelper::findFormField('images', $entry->form->fields);
		
	$thumb_class = '';
		
	if ($thumb_position == 'left')
	{
		$thumb_class = ' pull-left';
	}
	else if ($thumb_position == 'right')
	{
		$thumb_class = ' pull-right';
	}
		
	?>
		
	<div class="thumbnail<?php echo $thumb_class; ?>" style="max-width: <?php echo $thumb_max_width; ?>px;">
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