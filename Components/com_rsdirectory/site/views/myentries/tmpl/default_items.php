<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<div class="rsdir-listings">
    <?php
    if ($this->items)
    {
		foreach ($this->items as $i => $item)
		{
			$this->item = $item;
			$this->published = $item->published && JFactory::getDate($item->published_time)->toUnix() <= JFactory::getDate()->toUnix();
			$this->expired = $item->expiry_time != '0000-00-00 00:00:00' && JFactory::getDate($item->expiry_time)->toUnix() < JFactory::getDate()->toUnix();
				
			$listing_class = array('rsdir-listing clearfix');
				
			if ($item->promoted)
			{
				$listing_class[] = 'rsdir-listing-promoted';
			}
				
			if (!$this->published)
			{
				$listing_class[] = 'rsdir-listing-unpublished';
			}
				
			if ($this->expired)
			{
				$listing_class[] = 'rsdir-listing-expired';
			}
				
			?>
				
			<div class="row-fluid">
				<div class="<?php echo implode(' ', $listing_class); ?>">
						
					<?php
						
					// Get the listing url.
					$this->item_url = RSDirectoryRoute::getEntryURL($item->id, $item->title);
						
					// Get the title string.
					$this->title_str = $this->escape($item->title);
						
					if ($this->images_listing_detail_position == 'right')
					{
						echo $this->loadTemplate('details');
						echo $this->loadTemplate('thumbnail');
					}
					else
					{
						echo $this->loadTemplate('thumbnail');
						echo $this->loadTemplate('details');
					}
						
					?>
						
				</div><!-- .rsdir-listing -->
			</div><!-- .row-fluid -->
				
			<?php 
		}
    }
    ?>
</div>

<?php $pagesTotal = isset($this->pagination->pagesTotal) ? $this->pagination->pagesTotal : $this->pagination->get('pages.total'); ?>
<?php if ( $this->params->def('show_pagination', 2) == 1  || ( $this->params->get('show_pagination') == 2 && $pagesTotal > 1 ) ) { ?>
<div class="pagination">
        
    <?php if ( $this->params->def('show_pagination_results', 1) ) { ?>
    <p class="counter<?php echo RSDirectoryHelper::isJ30() ? ' pull-right' : ''; ?>">
        <?php echo $this->pagination->getPagesCounter(); ?>
    </p>
    <?php  } ?>
        
    <?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php } ?>