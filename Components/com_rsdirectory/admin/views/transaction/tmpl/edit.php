<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

$item = $this->item;

?>

<script type="text/javascript">
        
    Joomla.submitbutton = function(task)
    {
        if ( task == 'transaction.cancel' || document.formvalidator.isValid( document.id('adminForm') ) )
        {
            Joomla.submitform( task, document.getElementById('adminForm') );
        }
    }
        
</script>

<div class="rsdir">
    <form id="adminForm" name="adminForm" class="form-validate" action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
            
        <?php echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_TRANSACTION_DETAILS') ); ?>
            
        <table class="table table-striped">
                
            <tbody>
                    
                <tr>
                    <th width="160"><?php echo JText::_('COM_RSDIRECTORY_CREDIT_PACKAGE_TITLE'); ?></th>
                    <td><?php echo $this->escape($item->credit_title); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_USER'); ?></th>
                    <td>
                        <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->user_id"); ?>">
                            <?php echo $this->escape($item->user); ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_ENTRY'); ?></th>
                    <td>
                        <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>">
                            <?php echo $this->escape($item->entry_title); ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_IP'); ?></th>
                    <td><?php echo $this->escape($item->ip); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_PRICE'); ?></th>
                    <td><?php echo $this->escape($item->price); ?> <?php echo $this->escape($item->currency); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_TAX'); ?></th>
                    <td><?php echo $this->escape($item->tax); ?> <?php echo $this->escape($item->currency); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_TOTAL'); ?></th>
                    <td><?php echo $this->escape($item->total); ?> <?php echo $this->escape($item->currency); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?></th>
                    <td><?php echo $item->credits ? $this->escape($item->credits) : JText::_('COM_RSDIRECTORY_UNLIMITED'); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY'); ?></th>
                    <td><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_GATEWAY_' . strtoupper($item->gateway) ); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY_ORDER_NUMBER'); ?></th>
                    <td><?php echo $this->escape($item->gateway_order_number); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY_ORDER_TYPE'); ?></th>
                    <td><?php echo $this->escape($item->gateway_order_type); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY_PARAMS'); ?></th>
                    <td><?php echo str_replace( "\n", '<br />', $this->escape($item->gateway_params) ); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_GATEWAY_LOG'); ?></th>
                    <td><?php echo $item->gateway_log; ?></td>
                </tr>
                
                <tr>
                    <th><?php echo JText::_('JSTATUS'); ?></th>
                    <td>
                    <?php if ($item->status == 'finalized') { ?>
                    <span class="label label-success"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_STATUS_' . strtoupper($item->status) ); ?></span>
                    <?php } else { ?>
                    <span class="label"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_STATUS_' . strtoupper($item->status) ); ?></span>
                    <?php } ?>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_DATE_CREATED'); ?></th>
                    <td><?php echo $item->date_created; ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_DATE_FINALIZED'); ?></th>
                    <td><?php echo $item->date_finalized == '0000-00-00 00:00:00' ? '-' : $item->date_finalized; ?></td>
                </tr>
                    
            </tbody>
                
        </table>
            
        <?php echo $this->rsfieldset->getFieldsetEnd(); ?>
            
        <div>
            <input type="hidden" value="<?php echo $item->id; ?>" name="cid[]" />
            <input type="hidden" name="boxchecked" value="1" />
            <input type="hidden" name="task" value="" />
            <?php echo JHTML::_('form.token') . "\n"; ?>
        </div>
            
    </form>
</div><!-- .rsdir -->