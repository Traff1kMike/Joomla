<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<div class="rsdir">
	<div class="row-fluid">
			
		<?php if ( !empty($this->form) ) { ?>
		<form action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
			<?php
			foreach ( $this->form->getFieldset('general') as $field )
			{
				?>
				<div class="control-group">
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				</div>
				<?php
			}
			?>
				
			<div>
				<?php echo JHTML::_('form.token') . "\n"; ?>
				<input type="hidden" name="task" value="ownerreply.save" />
			</div>
		</form>
		<?php } ?>
			
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->