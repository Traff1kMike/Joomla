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
        if ( task == 'reportedentry.cancel' || document.formvalidator.isValid( document.id('adminForm') ) )
        {
            Joomla.submitform( task, document.getElementById('adminForm') );
        }
    }
        
</script>

<div class="rsdir">
    <form id="adminForm" name="adminForm" class="form-validate" action="<?php echo htmlspecialchars( JUri::getInstance()->toString() ); ?>" method="post">
            
        <?php echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_REPORT_DETAILS') ); ?>
            
        <table class="table table-striped">
                
            <tbody>
                    
                <tr>
                    <th width="100"><?php echo JText::_('COM_RSDIRECTORY_ENTRY'); ?></th>
                    <td>
                        <a href="<?php echo JText::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>">
                            <?php echo $this->escape($item->title); ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_ENTRY_AUTHOR'); ?></th>
                    <td>
                        <?php if ($item->entry_author_id) { ?>
                        <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->entry_author_id"); ?>">
                            <?php echo $this->escape($item->entry_author); ?>
                        </a>
                        <?php } else { ?>
                        -
                        <?php } ?>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_REPORT_AUTHOR'); ?></th>
                    <td>
                        <?php if ($item->entry_author_id) { ?>
                        <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->report_author_id"); ?>">
                            <?php echo $this->escape($item->report_author); ?>
                        </a>
                        <?php } else { ?>
                        -
                        <?php } ?>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_NAME'); ?></th>
                    <td><?php echo $this->escape($item->name); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_EMAIL'); ?></th>
                    <td><?php echo $this->escape($item->email); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_IP'); ?></th>
                    <td><?php echo $this->escape($item->ip); ?></td>
                </tr>
                
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_DATE_CREATED'); ?></th>
                    <td><?php echo $item->created_time; ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('JSTATUS'); ?></th>
                    <td>
                        <?php if ($item->published) { ?>
                        <span class="label label-success"><?php echo JText::_('COM_RSDIRECTORY_READ'); ?></span>
                        <?php } else { ?>
                        <span class="label label-warning"><?php echo JText::_('COM_RSDIRECTORY_UNREAD'); ?></span>
                        <?php } ?>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_REASON'); ?></th>
                    <td><?php echo $this->escape($item->reason); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_MESSAGE'); ?></th>
                    <td><?php echo $this->escape($item->message); ?></td>
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