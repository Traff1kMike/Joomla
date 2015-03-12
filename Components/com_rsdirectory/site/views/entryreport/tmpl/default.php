<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$config = $this->config;

?>

<div class="rsdir">
	<div class="row-fluid">
		<form action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
				
			<?php
			foreach ( $this->form->getFieldset('general') as $field )
			{
				if ( in_array($field->fieldname, $this->skipped_fields) )
					continue;
					
				$label = $field->label;
					
				// Add a mark to the message label if it is required.
				if ( $field->fieldname == 'message' && in_array('reason', $this->skipped_fields) )
				{
					$label = str_replace( JText::_('COM_RSDIRECTORY_MESSAGE'), JText::_('COM_RSDIRECTORY_MESSAGE') . '<span class="star">&nbsp;*</span>', $label );
				}
					
				?>
				<div class="control-group">
					<?php echo $label; ?>
					<?php echo $field->input; ?>
				</div>
				<?php
			}
			?>
				
			<div>
				<input type="hidden" name="jform[entry_id]" value="<?php echo $this->entry_id; ?>" />
				<?php echo JHTML::_('form.token') . "\n"; ?>
				<input type="hidden" name="task" value="entryreport.save" />
			</div>
				
		</form>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->