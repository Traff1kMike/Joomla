<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$transaction = $this->transaction;

?>

<div class="rsdir">
    <div class="row-fluid">
        <div class="item-page">
                
            <div class="page-header">
                <h1><?php echo JText::_('COM_RSDIRECTORY_VIEW_TRANSACTION'); ?></h1>
            </div>
                
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th width="160"><?php echo JText::_('COM_RSDIRECTORY_CREDIT_PACKAGE_TITLE'); ?></th>
                        <td><?php echo $this->escape($transaction->credit_title); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_PRICE'); ?></th>
                        <td><?php echo $this->escape($transaction->price); ?> <?php echo $this->escape($transaction->currency); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_TAX'); ?></th>
                        <td><?php echo $this->escape($transaction->tax); ?> <?php echo $this->escape($transaction->currency); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_TOTAL'); ?></th>
                        <td><?php echo $this->escape($transaction->total); ?> <?php echo $this->escape($transaction->currency); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?></th>
                        <td><?php echo $transaction->credits ? $this->escape($transaction->credits) : JText::_('COM_RSDIRECTORY_UNLIMITED'); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY'); ?></th>
                        <td><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_GATEWAY_' . strtoupper($transaction->gateway) ); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY_ORDER_NUMBER'); ?></th>
                        <td><?php echo $this->escape($transaction->gateway_order_number); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY_ORDER_TYPE'); ?></th>
                        <td><?php echo $this->escape($transaction->gateway_order_type); ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('JSTATUS'); ?></th>
                        <td>
                            <?php if ($transaction->status == 'finalized') { ?>
                            <span class="label label-success"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_STATUS_' . strtoupper($transaction->status) ); ?></span>
                            <?php } else { ?>
                            <span class="label"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_STATUS_' . strtoupper($transaction->status) ); ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_DATE_CREATED'); ?></th>
                        <td><?php echo $transaction->date_created; ?></td>
                    </tr>
                        
                    <tr>
                        <th><?php echo JText::_('COM_RSDIRECTORY_DATE_FINALIZED'); ?></th>
                        <td><?php echo $transaction->date_finalized == '0000-00-00 00:00:00' ? '-' : $transaction->date_finalized; ?></td>
                    </tr>
                </tbody>
            </table>
                
        </div><!-- .item-page -->
    </div><!-- .row-fluid -->
</div><!-- .rsdir -->