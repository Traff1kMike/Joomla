<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ( empty($this->entry) )
    return;

$form = $this->form;
$form_fields = $this->form_fields;
$entry = $this->entry;
$files_list = $this->files_list;
$config = $this->config;

?>

<div class="rsdir">
	<div class="row-fluid">
		<div class="item-page <?php echo !empty($this->pageclass_sfx) ? htmlspecialchars($this->pageclass_sfx) : ''; ?>">
				
			<?php if ( !empty($this->params) && $this->params->get('show_page_heading') ) { ?>
			<div class="page-header">
				<h1><?php echo $this->escape( $this->params->get('page_heading') ); ?></h1>
			</div>
			<?php } ?>
				
			<?php if ($form->listing_detail_show_breadcrumb) { ?>
				<ul class="rsdir-breadcrumb breadcrumb">
				<?php foreach ($this->breadcrumbs as $item) { ?>
					<li<?php echo $item == end($this->breadcrumbs) ? ' class="active"' : ''; ?>>
					<?php if ( isset($item->url) ) { ?>
						<a href="<?php echo $item->url; ?>" title="<?php echo $this->escape($item->text); ?>"><?php echo $this->escape($item->text); ?></a>
					<?php } else { ?>
						<?php echo $this->escape($item->text); ?>
					<?php } ?>
					<?php echo $item != end($this->breadcrumbs) ? ' <span class="divider">/</span>' : ''; ?>
					</li>
				<?php } ?>
				</ul>
			<?php } ?>
				
			<div class="row-fluid">
				<?php
				if ( $config->get('images_detail_position') == 'right' )
				{
					echo $this->loadTemplate('details');
					echo $this->loadTemplate('gallery');
				}
				else
				{
					echo $this->loadTemplate('gallery');
					echo $this->loadTemplate('details');
				}
				?>
			</div>
				
			<div class="row-fluid">
			<?php
			if ($form->listing_detail_show_description)
			{
				if ( $description_field = RSDirectoryHelper::findFormField('description', $form_fields) )
				{
					$description = RSDirectoryHelper::cleanText( $this->entry->description, true, $description_field->properties->get('clean_links') );
						
					if ($description)
					{
						?>
							
						<h4 class="rsdir-detail-section-title"><?php echo $this->escape( $description_field->properties->get('listing_caption') ); ?></h4>
							
						<?php
							
						$description = $description_field->properties->get('allow_html') ? $description : nl2br( strip_tags($description) );
							
						if ( $description_field->properties->get('prepare_content') )
						{
							$description = JHtml::_('content.prepare', $description);
						}
							
						echo $description;
					}
				}
			}
			?>
			</div>
				
			<?php
			if ($this->custom_form_fields_ids)
			{	
				$table = RSDirectoryHelper::getTableStructure($this->custom_form_fields_ids);
					
				?>
					
				<div class="row-fluid">
						
					<h4 class="rsdir-detail-section-title"><?php echo JText::_('COM_RSDIRECTORY_ENTRY_DETAILS_CAPTION'); ?></h4>
						
					<table class="table table-striped table-hover">
						<?php foreach ($table as $row) { ?>
						<tr>
						<?php
						foreach ($row as $form_field_id)
						{
							?>
							<td class="span4">
							<?php
							if ($form_field_id)
							{
								$form_field = RSDirectoryHelper::findFormField( array('id' => $form_field_id), $form_fields );
									
								if ($form_field)
								{
									$field_caption = $form_field->properties->get('listing_caption');
									$field_value = RSDirectoryField::getInstance($form_field, $entry)->generate();
										
									if ($field_value == '0000-00-00 00:00:00')
									{
										$field_value = '';
									}
									else if ( !$form_field->properties->get('allow_html') )
									{
										$field_value = nl2br( $this->escape($field_value) );
									}
										
									echo '<span class="rsdir-detail-caption">' . $this->escape($field_caption) . '</span> ';
										
									echo '<div class="rsdir-detail-value">' . $field_value . '</div>';
								}
							}
							?>
							</td>
							<?php
						}
						?>
						</tr>
						<?php } ?>
					</table>
				</div>
					
				<?php
			}
				
			if ($this->image_uploads)
			{
				foreach ($this->image_uploads as $image_upload)
				{
					if ( !$image_upload->properties->get('display_on_listing_detail', 1) )
						continue;
						
					$files = RSDirectoryHelper::findElements( array('field_id' => $image_upload->id), $files_list, false );
						
					if ($files)
					{
						?>
							
						<h4 class="rsdir-detail-section-title"><?php echo $this->escape( $image_upload->properties->get('listing_caption') ); ?></h4>
							
						<?php
							
						echo RSDirectoryFormField::getImagesList($files, 0, 0);	
					}
				}
			}
				
			if ($this->fileuploads)
			{
				foreach ($this->fileuploads as $fileupload)
				{
					if ( !$fileupload->properties->get('display_on_listing_detail', 1) )
						continue;
						
					$files = RSDirectoryHelper::findElements( array('field_id' => $fileupload->id), $files_list, false );
						
					if ($files)
					{
						$table = RSDirectoryHelper::getTableStructure($files);
							
						?>
							
						<h4 class="rsdir-detail-section-title"><?php echo $this->escape( $fileupload->properties->get('listing_caption') ); ?></h4>
							
						<table class="rsdir-files-table table table-striped table-hover">
							<?php foreach ($table as $row) { ?>
							<tr>
							<?php foreach ($row as $file) { ?>
								 <td class="span4">
									<?php
									if ($file)
									{
										$href = JRoute::_("index.php?option=com_rsdirectory&task=file.download&hash=$file->hash");
											
										?>
											
										<a href="<?php echo $href; ?>"><?php echo $this->escape($file->original_file_name); ?></a>
											
										<?php
									}
									?>
								 </td>
							<?php } ?>
							</tr>
							<?php } ?>
						</table>
							
						<?php
					}
				}
			}
				
			if ($this->maps)
			{
				foreach ($this->maps as $map)
				{
					if ( !$map->properties->get('display_on_listing_detail', 1) )
						continue;
						
					$style = array();
						
					$width = $map->properties->get('width', '100%');
						
					if ( is_numeric($width) )
					{
						$width .= 'px';
					}
						
					$style[] = "width: $width;";
						
					$style[] = 'height: ' . $this->escape( $map->properties->get('height') ) . 'px;';
						
					$address_column_name = "{$map->column_name}_address";
						
					?>
						
					<h4 class="rsdir-detail-section-title"><?php echo $this->escape( $map->properties->get('listing_caption') ); ?></h4>
						
					<?php if ( trim($entry->$address_column_name) ) { ?>
					<div class="control-group">
						<?php echo $this->escape($entry->$address_column_name); ?>
					</div>
					<?php } ?>
						
					<div class="control-group">
						<div id="rsdir-map-canvas-<?php echo $map->id; ?>" class="rsdir-map-canvas" style="<?php echo implode(' ', $style); ?>"></div>
					</div>
						
					<?php
				}
			}
				
			if ($this->youtube)
			{
				foreach ($this->youtube as $youtube)
				{
					if ( empty($entry->{$youtube->column_name}) || !$youtube->properties->get('display_on_listing_detail', 1) )
						continue;
						
					$embed = new RSDirectoryYoutube($youtube->properties, $entry->{$youtube->column_name});
						
					?>
						
					<h4 class="rsdir-detail-section-title"><?php echo $this->escape( $youtube->properties->get('listing_caption') ); ?></h4>
						
					<div class="rsdir-youtube-video"><?php echo $embed->generate(); ?></div>
						
					<?php
						
				}
			}
				
			if ( ( $form->listing_detail_show_print || $form->listing_detail_show_report || $form->listing_detail_show_favorites_button ) && !$this->print )
			{
				?>
					
				<div class="control-group clearfix">
						
					<?php if ($form->listing_detail_show_print || $form->listing_detail_show_report || $form->listing_detail_show_favorites_button) { ?>
					<div class="pull-right">
							
						<?php if ($form->listing_detail_show_favorites_button) { ?>
						<a class="rsdir-entry-fav<?php echo $this->is_favorite ? ' rsdir-entry-faved' : ''; ?> btn" data-entry-id="<?php echo $entry->id; ?>" title="<?php echo JText::_($this->is_favorite ? 'COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES' : 'COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES'); ?>"><i class="<?php echo $this->is_favorite ? 'icon-star' : 'icon-star-empty'; ?>"></i></a>
						<?php } ?>
							
						<?php if ($form->listing_detail_show_print) { ?>
						<button id="rsdir-print-entry" class="btn"><i class="icon-print"></i> <?php echo JText::_('COM_RSDIRECTORY_PRINT'); ?></button>
						<?php } ?>
							
						<?php
							
						if ($form->listing_detail_show_report)
						{
							echo $this->loadTemplate('report');	
						}
							
						?>
					</div>
					<?php } ?>
				</div>
					
				<?php
			}
				
			echo $this->loadTemplate('reviews');
				
			echo $this->loadTemplate('comments');
				
			?>
				
		</div><!-- .item-page -->
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->