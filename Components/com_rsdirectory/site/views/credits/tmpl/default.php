<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$params = $this->params;
$data = $this->data;

?>

<div class="rsdir">
	<div class="row-fluid">
		<div class="<?php echo htmlspecialchars($this->pageclass_sfx); ?>">
				
			<?php if ( $params->get('show_page_heading', 1) ) { ?>
				<div class="page-header">
					<h1><?php echo $this->escape( $params->get('page_heading') ); ?></h1>
				</div>
			<?php } ?>
				
			<?php foreach ($this->messages_list as $type => $messages) { ?>
				<?php if ($messages) { ?>
				<div class="alert alert-<?php echo $type; ?>">
					<?php echo implode('<br />', $messages); ?>
				</div>
				<?php } ?>
			<?php } ?>
				
			<?php if ( empty($this->payment_form_data) ) { ?>
				
			<?php if ( empty($this->user->id) ) { ?>
				
			<div class="alert alert-info">
				<?php echo JText::_('COM_RSDIRECTORY_ENTRY_GUEST'); ?>
			</div>
				
			<form action="<?php echo JRoute::_( 'index.php', true, false ); ?>" method="post">
				<fieldset>
					<legend><?php echo JText::_('COM_RSDIRECTORY_LOGIN_FORM'); ?></legend>
						
					<div class="control-group">
						<label class="control-label" for="rsdir-login-username"><?php echo JText::_('JGLOBAL_USERNAME'); ?><span class="star">&nbsp;*</span></label>
						<div class="controls">
							<input id="rsdir-login-username" type="text" name="username" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="rsdir-login-password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?><span class="star">&nbsp;*</span></label>
						<div class="controls">
							<input id="rsdir-login-password" type="password" name="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<label class="checkbox">
								<input type="checkbox" name="remember" value="yes"  /> <?php echo JText::_('JGLOBAL_REMEMBER_ME'); ?>
							</label>
							<button type="submit" class="btn btn-primary"><?php echo JText::_('JLOGIN'); ?></button>
						</div>
					</div>
				</fieldset>
				<div>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="<?php echo base64_encode( JUri::getInstance()->toString() );?>" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</form>
			<?php } ?>
				
			<form id="buyCreditsForm" action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
					
				<?php if ( empty($this->user->id) ) { ?>
				<fieldset>
					<legend><?php echo JText::_('COM_RSDIRECTORY_USER_DETAILS'); ?></legend>
						
					<div class="control-group<?php echo is_array($this->error_reg_fields) && in_array('name', $this->error_reg_fields) ? ' error' : ''; ?>">
						<label for="rsdir-register-name"><?php echo JText::_('COM_RSDIRECTORY_NAME'); ?><span class="star">&nbsp;*</span></label>
						<input id="rsdir-register-name" type="text" name="jform[reg][name]"<?php echo empty($data['reg']['name']) ? '' : ' value="' . $this->escape($data['reg']['name']) . '"'; ?> />
					</div>
						
					<div class="control-group<?php echo is_array($this->error_reg_fields) && in_array('email', $this->error_reg_fields) ? ' error' : ''; ?>">
						<label for="rsdir-register-email"><?php echo JText::_('COM_RSDIRECTORY_EMAIL'); ?><span class="star">&nbsp;*</span></label>
						<input id="rsdir-register-email" type="text" name="jform[reg][email]"<?php echo empty($data['reg']['email']) ? '' : ' value="' . $this->escape($data['reg']['email']) . '"'; ?> />
					</div>
				</fieldset>
				<?php } ?>
					
				<?php if ( !empty($this->entry_summary) ) { ?>
					
				<?php echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_SUMMARY') ); ?>
					
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
					
				<?php echo $this->rsfieldset->getFieldsetEnd(); ?>
					
				<?php } ?>
					
				<?php if ($this->credit_packages) { ?>
						
					<?php echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_CREDIT_PACKAGES') ); ?>
						
					<?php foreach ($this->credit_packages as $credit_package) { ?>
					<div class="rsdir-credit-package control-group<?php echo empty($credit_package->class) ? '' : " $credit_package->class"; ?>">
						<label class="radio">
							<input type="radio" name="jform[credit_package]" value="<?php echo $this->escape($credit_package->id); ?>"<?php echo !empty($data['credit_package']) && $credit_package->id == $data['credit_package'] ? ' checked="checked"' : ''; ?> />
							<strong><?php echo $this->escape($credit_package->title); ?></strong>
							<span class="label label-info"><?php
							if ($credit_package->credits)
							{
								echo JText::plural('COM_RSDIRECTORY_NUMBER_OF_CREDITS', $credit_package->credits);
							}
							else
							{
								echo JText::_('COM_RSDIRECTORY_UNLIMITED_CREDITS');
							}
							?></span>
							<span class="label label-success"><?php echo $this->escape( RSDirectoryHelper::formatPrice($credit_package->price) ); ?></span>
						</label>
						<?php echo $credit_package->description; ?>
					</div>
					<?php } ?>
						
					<?php echo $this->rsfieldset->getFieldsetEnd(); ?>
						
				<?php } ?>
					
				<?php if ($this->payment_methods) { ?>
						
					<?php echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_PAYMENT_METHOD') ); ?>
						
					<div class="control-group">
						
					<?php foreach ($this->payment_methods as $payment_method) { ?>
							
						<label class="radio">
							<input type="radio" name="jform[payment_method]" value="<?php echo $this->escape($payment_method->value); ?>"<?php echo !empty($data['payment_method']) && $payment_method->value == $data['payment_method'] ? ' checked="checked"' : ''; ?> />
							<strong><?php echo $this->escape($payment_method->text); ?></strong>
							<?php if ($payment_method->tax_text) { ?>
							<span class="label label-warning"><?php echo $this->escape( JText::sprintf('COM_RSDIRECTORY_TAX_TEXT', $payment_method->tax_text) ); ?></span>
							<?php } ?>
						</label>
							
					<?php } ?>
						
					</div>
						
					<?php echo $this->rsfieldset->getFieldsetEnd(); ?>
						
				<?php } ?>
					
				<table class="table">
					<tbody>
						<tr class="info">
							<th width="90%"><?php echo JText::_('COM_RSDIRECTORY_PRICE'); ?></th>
							<td class="buy-credits-price"><?php echo RSDirectoryHelper::formatPrice(0); ?></td>
						</tr>
						<tr class="info">
							<th><?php echo JText::_('COM_RSDIRECTORY_TAX'); ?></th>
							<td class="buy-credits-tax"><?php echo RSDirectoryHelper::formatPrice(0); ?></td>
						</tr>
						<tr class="info">
							<th><?php echo JText::_('COM_RSDIRECTORY_TOTAL'); ?></th>
							<td>
								<strong class="buy-credits-total"><?php echo RSDirectoryHelper::formatPrice(0); ?></strong>
							</td>
						</tr>
					</tbody>
				</table>
					
				<button class="btn btn-primary" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
				<div>
					<?php echo JHTML::_('form.token') . "\n"; ?>
					<input type="hidden" name="task" value="credits.purchase" />
					<?php echo empty($this->entry) ? '' : '<input type="hidden" name="jform[entry_id]" value="' . $this->entry->id . '" />'; ?>
				</div>
			</form>
			<?php } else { ?>
				<?php JFactory::getApplication()->triggerEvent('rsdirectory_showForm', $this->payment_form_data); ?>
			<?php } ?>
				
		</div>
	</div><!-- .row-fluid -->
</div><!-- .rsdir -->