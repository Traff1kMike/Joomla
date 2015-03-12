<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); ?>

<div class="rsdir">
	<div class="rsdir-recently-visited-entries<?php echo " $display"; ?> row-fluid">
	<?php
		
	foreach ($entries as $entry)
	{
		$url = RSDirectoryRoute::getEntryURL($entry->id, $entry->title, '', $itemid);
			
		$title = RSDirectoryHelper::escapeHTML($entry->title);
			
		$entry_class = $display == 'horizontal' ? " span$span" : '';
			
		?>
			
		<div class="rsdir-recently-visited-entry clearfix<?php echo $entry_class; ?>">
			
		<?php
			
		if ($thumb_position == 'bottom')
		{
			include JModuleHelper::getLayoutPath('mod_rsdirectory_recently_visited_entries', 'default_body');
			include JModuleHelper::getLayoutPath('mod_rsdirectory_recently_visited_entries', 'default_thumb');
		}
		else
		{
			include JModuleHelper::getLayoutPath('mod_rsdirectory_recently_visited_entries', 'default_thumb');
			include JModuleHelper::getLayoutPath('mod_rsdirectory_recently_visited_entries', 'default_body');
		}
			
		?>
			
		</div>
			
		<?php
	}
	?>
	</div>
</div><!-- .rsdir -->