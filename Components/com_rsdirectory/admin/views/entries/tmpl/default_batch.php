<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ( RSDirectoryHelper::isJoomlaCompatible('3.2.3') )
{
    JHtml::_('formbehavior.chosen', '.batch select');
}

?>

<fieldset class="adminform batch">
	<legend><?php echo JText::_('COM_RSDIRECTORY_BATCH_PROCESS_ENTRIES');?></legend>
	<p><?php echo JText::_('COM_RSDIRECTORY_ENTRIES_BATCH_TIP'); ?></p>
		
	<?php
	foreach ( $this->batch_form->getFieldset('general') as $field )
	{
		?>
			
		<div class="control-group clearfix">
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		</div>
			
		<?php
	}
	?>
		
	<button id="batchSubmit" type="submit"><?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?></button>
	<button id="batchClear" type="button"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		
</fieldset>