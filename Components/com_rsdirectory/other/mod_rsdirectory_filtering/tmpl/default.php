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
	<div class="row-fluid">
		<form class="rsdir-filter-form" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&task=filters.process'); ?>" method="post">
				
			<?php
				
			if ( $params->get('show_categories') && $categories_list )
			{
				RSChosen::create(
					'.rsdir-filtering-categories',
					array(
						'placeholder_text_multiple' => '"' . JText::_('MOD_RSDIRECTORY_FILTERING_SELECT_CATEGORIES') . '"',
						'no_results_text' => '"' . JText::_('MOD_RSDIRECTORY_FILTERING_NO_RESULTS') . '"',
					)
				);
					
				?>
					
				<div class="rsdir-filter rsdir-filter-categories control-group">
					<div class="rsdir-filter-caption"><?php echo JText::_('MOD_RSDIRECTORY_FILTERING_CATEGORIES_CAPTION'); ?></div>
					<select class="rsdir-filtering-categories" name="categories[]" multiple="multiple">
					<?php RSDirectoryFilteringHelper::outputCategoriesOptions($categories_list); ?>
					</select>
				</div>
					
				<?php
			}
				
			$can_view_all_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_all_unpublished_entries');
			$can_view_own_unpublished_entries = RSDirectoryHelper::checkUserPermission('can_view_own_unpublished_entries');
				
			if ($can_view_all_unpublished_entries || $can_view_own_unpublished_entries)
			{
				?>
				<div class="rsdir-filter rsdir-filter-status control-group">
					<div class="rsdir-filter-caption"><?php echo JText::_('MOD_RSDIRECTORY_FILTERING_STATUS'); ?></div>
						
					<label class="rsdir-checkbox-label checkbox">
						<input class="rsdir-checkbox" type="checkbox" value="1" name="status[]"<?php echo in_array(1, $status) ? ' checked="checked"' : ''; ?> />
						<?php echo JText::_('MOD_RSDIRECTORY_FILTERING_PUBLISHED'); ?>
					</label>
						
					<label class="rsdir-checkbox-label checkbox">
						<input class="rsdir-checkbox" type="checkbox" value="0" name="status[]"<?php echo in_array(0, $status) ? ' checked="checked"' : ''; ?> />
						<?php echo JText::_('MOD_RSDIRECTORY_FILTERING_UNPUBLISHED'); ?>
					</label>
				</div>
				<?php
			}
				
			if ($fields)
			{
				foreach ($fields as $field)
				{
					echo RSDirectoryFilter::getInstance($field, $options)->generate();
				}
			}
				
			?>
				
			<button class="btn btn-primary" type="submit"><?php echo JText::_('MOD_RSDIRECTORY_FILTERING_SUBMIT'); ?></button>
			<button class="btn" type="submit" name="clear_filters" value="1"><?php echo JText::_('MOD_RSDIRECTORY_FILTERING_CLEAR_FILTERS'); ?></button>
				
			<div>
				<?php echo JHTML::_('form.token') . "\n"; ?>
				<?php echo $itemid ? '<input type="hidden" name="Itemid" value="' . $itemid . '" />' : ''; ?>
				<?php if ($options) { ?>
				<?php foreach ($options as $name => $value) { ?>
				<input class="options" type="hidden" name="<?php echo RSDirectoryHelper::escapeHTML($name); ?>" value="<?php echo RSDirectoryHelper::escapeHTML($value); ?>" />
				<?php } ?>
				<?php } ?>
			</div>
		</form>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->