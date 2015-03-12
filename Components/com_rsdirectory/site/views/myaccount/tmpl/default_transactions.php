<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<table class="table table-striped">
    <thead>
		<tr>
			<th><?php echo JText::_('COM_RSDIRECTORY_VIEW'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('COM_RSDIRECTORY_CREDIT_PACKAGE'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('COM_RSDIRECTORY_GATEWAY'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('COM_RSDIRECTORY_ORDER_NUMBER'); ?></th>
			<th><?php echo JText::_('COM_RSDIRECTORY_TOTAL'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?></th>
			<th class="hidden-phone"><?php echo JText::_('JSTATUS'); ?></th>
		</tr>
    </thead>
    <?php if ($this->transactions) { ?>
    <tbody>
		<?php foreach ($this->transactions as $transaction) { ?>
		<tr>
			<td><a href="<?php echo RSDirectoryRoute::getURL('transaction', '', "id=$transaction->id"); ?>"><?php echo JText::_('COM_RSDIRECTORY_VIEW'); ?></a></td>
			<td class="hidden-phone"><?php echo $this->escape($transaction->credit_title); ?></td>
			<td class="hidden-phone"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_GATEWAY_' . strtoupper($transaction->gateway) ); ?></td>
			<td class="hidden-phone"><?php echo $transaction->gateway_order_number; ?></td>
			<td><?php echo $this->escape($transaction->currency) . ' ' . $this->escape($transaction->total); ?></td>
			<td class="hidden-phone"><?php echo $transaction->credits ? $this->escape($transaction->credits) : JText::_('COM_RSDIRECTORY_UNLIMITED'); ?></td>
			<td class="hidden-phone">
				<?php if ($transaction->status == 'finalized') { ?>
				<span class="label label-success"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_STATUS_' . strtoupper($transaction->status) ); ?></span>
				<?php } else { ?>
				<span class="label"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_STATUS_' . strtoupper($transaction->status) ); ?></span>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
    </tbody>
    <?php } ?>
</table>