<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$params = $this->params;
$parent = $this->parent;

?>

<div class="rsdir">
	<div class="row-fluid">
		<div class="rsdir-categories<?php echo $this->pageclass_sfx;?>">
				
			<?php if ( $params->get('show_page_heading') ) { ?>
				<div class="page-header">
					<h1><?php echo $this->escape( $params->get('page_heading') ); ?></h1>
				</div>
			<?php } ?>
				
			<?php if ( !JFactory::getApplication()->getMenu()->getActive() ) { ?>
				<div class="page-header">
					<h1><?php echo JText::_('COM_RSDIRECTORY_CATEGORIES'); ?></h1>
				</div>
			<?php } ?>
				
			<?php if ( $params->get('show_base_title') || $params->get('show_base_description') || $params->get('show_base_thumbnail') ) { ?>
			<div class="media">
				<?php
					
				if ( $params->get('show_base_thumbnail') )
				{
					if ( $parent && $file = RSDirectoryHelper::getCategoryThumbObject(0, $parent->id) )
					{
						?>
							
						<div class="thumbnail <?php echo $params->get('base_thumbnail_position', 'right') == 'left' ? 'pull-left' : 'pull-right'; ?>">
							<img src="<?php echo RSDirectoryHelper::getImageURL($file->hash, 'small'); ?>" alt="<?php echo $this->escape($parent->title); ?>" width="<?php echo $this->escape($this->small_thumbnail_width); ?>" height="<?php echo $this->escape($this->small_thumbnail_height); ?>" />
						</div>
							
						<?php
					}
				}
					
				?>
					
				<div class="media-body">
						
					<?php if ( $parent && $params->get('show_base_title') ) { ?>
					<h4 class="media-heading"><?php echo $this->escape($parent->title); ?></h4>
					<?php } ?>
						
					<?php
						
					if ( $params->get('show_base_description') )
					{
						// If there is a description in the menu parameters use that.
						if ( $this->params->get('categories_description' ) )
						{
							echo  JHtml::_('content.prepare', $params->get('categories_description'), '', 'com_rsdirectory.categories');
						}
						else if ($parent && $parent->description)
						{
							echo JHtml::_('content.prepare', $parent->description, '', 'com_rsdirectory.categories');
						}
					}
						
					?>
				</div>
			</div>
			<?php } ?>
				
			<?php
				
			if ($this->items)
			{
				$span = 'span' . ( 12 / $params->get('num_columns', 3) );
					
				// Get the subcategories max level.
				$maxLevelcat = $params->get('maxLevelcat', 2);
					
				// Get the description character limit.
				$subcat_desc_limit = $params->get('subcat_desc_limit', 50);
					
				?>
					
				<div class="rsdir-categories-list">
					
				<?php foreach ($this->items as $row) { ?>
						
					<div class="row-fluid">
						
					<?php foreach ($row as $item) { ?>
							
						<div class="<?php echo $span; ?>">
								
							<div class="media">
									
								<?php if ( $params->get('show_subcategories_thumbnails', 1) && !empty($item->thumbnail_url) ) { ?>
								<img class="pull-left" src="<?php echo $item->thumbnail_url; ?>" alt="" width="<?php echo $this->escape($this->width); ?>" height="<?php echo $this->escape($this->height); ?>" />
								<?php } ?>
									
								<div class="media-body">
									<h5 class="media-heading">
										<a href="<?php echo RSDirectoryRoute::getCategoryEntriesURL($item->id, $item->title, $this->Itemid); ?>"><?php echo $this->escape($item->title); ?></a>
										<?php echo $params->get('show_cat_num_articles_cat') ? ' (' . $item->getNumItems(true) . ')' : ''; ?>
									</h5>
										
									<?php
										
									if ( $params->get('show_subcat_desc_cat') )
									{
										if ($subcat_desc_limit)
										{
											$description = $this->escape( RSDirectoryHelper::cut( strip_tags($item->description), $subcat_desc_limit ) );
										}
										else
										{
											$description = $item->description;
										}
											
										if ($description)
										{
											echo "<p>$description</p>";	
										}
									}
										
									if ( ($maxLevelcat > 1 || $maxLevelcat == -1) && $children = $item->getChildren() )
									{
										RSDirectoryHelper::outputSubcategoriesHTML($children, $params);
									}
										
									?>
								</div>
							</div>
								
						</div>
							
					<?php } ?>
						
					</div>
						
				<?php } ?>
					
				</div><!-- .rsdir-categories-list -->
					
			<?php } ?>
				
		</div><!-- .rsdir-categories -->
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->