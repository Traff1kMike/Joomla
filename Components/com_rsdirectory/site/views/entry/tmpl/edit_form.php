<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

if ($this->form)
{
    if (!$this->user->id)
    {
		?>
			
		<fieldset>
			<legend><?php echo JText::_('COM_RSDIRECTORY_USER_DETAILS'); ?></legend>
				
			<?php RSDirectoryFormField::outputRegFields(); ?>
		</fieldset>
			
		<fieldset>
			<legend><?php echo JText::_('COM_RSDIRECTORY_ENTRY_DETAILS'); ?></legend>
			
		<?php
    }
		
	?>
		
	<div class="control-group">
		<?php echo $this->jform->getLabel('category_id'); ?>
		<?php echo $this->jform->getInput('category_id'); ?>
	</div>
		
	<?php
        
    $break_type = $this->config->get('break_type');
        
    if ($break_type == 'column_break')
    {
        echo $this->loadTemplate('column_break');
    }
    else if ($break_type == 'section_break')
    {
        echo $this->loadTemplate('section_break');
    }
    else if ($break_type == 'tab_break')
    {
        echo $this->loadTemplate('tab_break');
    }
		
	if ( empty($this->can_edit_all_entries) )
	{	
		?>
			
		<div class="rsdir-field-wrapper alert alert-info clearfix"> 
        <?php echo JText::sprintf('COM_RSDIRECTORY_TOTAL_CREDITS', '<span class="rsdir-total-credits">0</span>'); ?>
        </div>
			
		<?php if ( isset($this->entry) && empty($this->entry->paid) ) { ?>
		<div id="finalize-confirm-label" class="control-group hide">
			<label class="checkbox">
				<input type="checkbox" name="fields[finalize]" value="1" /> <?php echo JText::sprintf('COM_RSDIRECTORY_FINALIZE_CONFIRM_LABEL', '<span class="rsdir-total-credits">0</span>'); ?>
			</label> 
		</div>
		<?php } ?>
			
		<?php
	}
	else
	{
        ?>
			
		<div class="control-group">
			<?php echo $this->jform->getLabel('published_time'); ?>
			<?php echo $this->jform->getInput('published_time'); ?>
		</div>
			
		<div class="control-group">
			<?php echo $this->jform->getLabel('published'); ?>
			<?php echo $this->jform->getInput('published'); ?>
		</div>
			
		<?php
	}
		
	if ( empty($this->entry) )
	{
		echo RSDirectoryFormField::getCaptchaField();
	}
        
    ?>
		
	<div class="insufficient-credits alert alert-warning hide">
		<?php echo JText::_('COM_RSDIRECTORY_INSUFFICIENT_CREDITS_WARNING'); ?>
	</div>
		
	<br />
        
    <button<?php echo empty($this->can_edit_all_entries) ? ' id="rsdir-check-credits"' : ' onclick="Joomla.submitbutton(\'entry.save\');"'; ?> class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
	<button class="insufficient-credits btn btn-primary hide" onclick="Joomla.submitbutton('entry.saveAndBuyCredits')"><?php echo JText::_('COM_RSDIRECTORY_BUY_CREDITS'); ?></button>
		
	<?php if ( !empty($this->entry) ) { ?>
	<button class="btn" onclick="Joomla.submitbutton('entry.back')"><?php echo JText::_('COM_RSDIRECTORY_BACK'); ?></button>
    <?php } ?>
	<img class="rsdir-form-loader hide" src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/loader.gif" alt="" width="16" height="11" />
		
    <?php
    if (!$this->user->id)
    {
		?>
			
		</fieldset>
			
		<?php
    }
}
else
{
    ?>
        
    <p><?php echo JText::_('COM_RSDIRECTORY_NO_FORM_ASSOCIATED_ERROR'); ?></p>
        
    <?php
}

?>

<div>
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="fields[id]" value="<?php echo empty($this->entry) ? 0 : $this->entry->id; ?>" />
</div>