<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ( RSDirectoryHelper::isJoomlaCompatible('3.2.3') )
{
    JHtml::_('formbehavior.chosen', '#batchModal select');
}

?>

<div id="batchModal" class="batch modal hide fade" tabindex="-1" role="dialog" aria-labelledby="batchModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="batchModalLabel"><?php echo JText::_('COM_RSDIRECTORY_BATCH_PROCESS_ENTRIES'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_RSDIRECTORY_ENTRIES_BATCH_TIP'); ?></p>
		<?php
		foreach ( $this->batch_form->getFieldset('general') as $field )
		{
			echo $this->rsfieldset->getField($field->label, $field->input);
		}
		?>
			
		<hr />	
			
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('JCANCEL'); ?></button>
		<button id="batchSubmit" class="btn btn-primary" type="submit"><?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?></button>
	</div>
</div>