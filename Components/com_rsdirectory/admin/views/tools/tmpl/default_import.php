<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ( empty($this->import_options) )
{
	?>
		
	<div class="alert alert-warning"><?php echo JText::_('COM_RSDIRECTORY_NO_IMPORT_PLUGIN_WARNING'); ?></div>
		
	<?php
		
	return;
}

$data = $this->data;

echo $this->rsfieldset->getFieldsetStart();

?>

<div class="progress">
    <div class="bar" style="width: 0%;">
        <div class="pull-right progress-label">0% 0/0</div>
    </div>
</div>

<div class="row-fluid">
	<div class="span6">
			
		<div class="control-group">
				
			<div class="control-label">
				<label>
					<?php echo JText::_('COM_RSDIRECTORY_IMPORT_FROM'); ?>
				</label>
			</div>
				
			<div class="controls">
				<?php foreach ($this->import_options as $option) { ?>
					
				<label class="radio">
					<input type="radio" name="jform[import_from]" value="<?php echo $this->escape($option->value); ?>"<?php echo empty($data['import_from']) || $option->value != $data['import_from'] ? '' : ' checked="checked"'; ?> />
					<strong><?php echo $this->escape($option->text); ?></strong>
				</label>
					
				<?php } ?>
			</div>
		</div>
			
		<?php JFactory::getApplication()->triggerEvent('rsdirectory_displayImportFieldset'); ?>
			
		<button id="import-start" class="btn btn-primary"><?php echo JText::_('COM_RSDIRECTORY_IMPORT_BUTTON'); ?></button>
		<button id="import-stop" class="btn btn-danger hide"><?php echo JText::_('COM_RSDIRECTORY_IMPORT_STOP_BUTTON'); ?></button>
			
	</div>
	<div id="import-log" class="span6">
			
		<h3><?php echo JText::_('COM_RSDIRECTORY_IMPORT_LOG'); ?></h3>
			
		<table class="table">
			<tfoot>
				<tr>
					<td><?php echo JText::_('COM_RSDIRECTORY_IMPORT_LOG_EMPTY'); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<?php

echo $this->rsfieldset->getFieldsetEnd();