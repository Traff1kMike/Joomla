<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

echo $this->rsfieldset->getFieldsetStart();

?>

<?php if ( !empty($this->restore_errors) ) { ?>
<div class="alert alert-error">
    <?php echo implode('<br />', $this->restore_errors); ?>
</div>
<?php } ?>

<div class="progress">
    <div class="bar" style="width: 0%;">
        <div class="pull-right progress-label">0% 0/0</div>
    </div>
</div>

<div class="row-fluid">
	<div class="span6">
			
		<?php
			
		foreach ( $this->form->getFieldset('restore') as $field )
		{
			if ( in_array($field->fieldname, $this->restore_hidden_fields) )
			{
				echo $this->rsfieldset->getField( $field->label, $field->input, array('hide' => true) );
			}
			else
			{
				if ($field->fieldname == 'restore_confirm')
				{
					echo '<div class="alert alert-error"><p>' . JText::_('COM_RSDIRECTORY_RESTORE_WARNING') . "</p>$field->input</div>";
				}
				else
				{
					echo $this->rsfieldset->getField($field->label, $field->input);	
				}
			}
		}
			
		?>
			
		<button id="restore-start" class="btn btn-primary" disabled="disabled"><?php echo JText::_('COM_RSDIRECTORY_RESTORE_UPLOAD_AND_RESTORE_BUTTON'); ?></button>
		<button id="restore-stop" class="btn btn-danger hide"><?php echo JText::_('COM_RSDIRECTORY_RESTORE_STOP_BUTTON'); ?></button>
			
		<iframe id="restore_upload_target" name="restore_upload_target" src="#" style="width: 0; height: 0; border: none;"></iframe>
			
	</div>
	<div id="restore-log" class="span6">
			
		<h3><?php echo JText::_('COM_RSDIRECTORY_RESTORE_LOG'); ?></h3>	
			
		<table class="table">
			<tfoot>
				<tr>
					<td><?php echo JText::_('COM_RSDIRECTORY_RESTORE_LOG_EMPTY'); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<?php

echo $this->rsfieldset->getFieldsetEnd();