<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<div class="page-header">
	<h1><?php echo JText::_('COM_RSDIRECTORY_FINALIZE_ENTRY_TITLE'); ?></h1>
</div>

<form id="adminForm" class="form-horizontal" action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
	<fieldset class="adminform form-horizontal">
			
		<legend><?php echo JText::_('COM_RSDIRECTORY_SUMMARY'); ?></legend>
			
		<?php if ($this->entry_summary) { ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th></th>
					<th width="80" class="center"><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->entry_summary as $item) { ?>
				<tr class="info">
					<th><?php echo $this->escape($item->text); ?></th>
					<td class="center">
						<?php if ( empty($item->is_total) ) { ?>
						<?php echo $item->credits; ?>
						<?php } else { ?>
						<strong><?php echo $this->escape($item->credits); ?></strong>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php } ?>
			
		<div class="control-group">
			<label class="checkbox">
				<input id="finalize-confirm" class="checkbox" type="checkbox" /> <?php echo JText::sprintf('COM_RSDIRECTORY_FINALIZE_CONFIRM_LABEL', $this->total); ?>
			</label>
		</div>
			
		<button id="finalize-confirm-btn" class="btn btn-primary" type="submit" name="task" value="entry.finalizeConfirm" disabled="disabled"><?php echo JText::_('COM_RSDIRECTORY_CONFIRM'); ?></button>
		<button class="btn" type="submit" name="task" value="entry.back"><?php echo JText::_('COM_RSDIRECTORY_BACK'); ?></button>
			
		<div>
			<input type="hidden" name="fields[id]" value="<?php echo $this->id; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
			
	</fieldset>
</form>