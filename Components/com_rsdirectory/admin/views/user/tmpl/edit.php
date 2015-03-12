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

$item = $this->item;

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if ( task == 'user.cancel' || document.formvalidator.isValid( document.id('user') ) )
        {
            Joomla.submitform( task, document.getElementById('user') );
        }
    }
        
    jQuery(function($)
    {
        var unlimited_credits = $( document.getElementById('jform_unlimited_credits') );
        var credits = $( document.getElementById('jform_credits') );
            
        if ( unlimited_credits.find(':checked').val() == 1 )
        {
            credits.parents('.control-group').addClass('hide');
        }
            
        unlimited_credits.find('input').click(function()
        {
            if ( $(this).val() == 1 )
            {
                credits.parents('.control-group').addClass('hide');
            }
            else
            {
                credits.parents('.control-group').removeClass('hide');
            }
        });
    });
</script>

<div class="rsdir">
    <form id="user" class="form-validate" action="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=user&layout=edit&id=$this->id"); ?>" method="post">
            
        <?php echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_USER_DETAILS_LABEL') ); ?>
            
        <table class="table table-striped">
                
            <tbody>
                    
                <tr>
                    <th width="130"><?php echo JText::_('COM_RSDIRECTORY_NAME'); ?></th>
                    <td>
                        <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JText::_("index.php?option=com_users&task=user.edit&id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_EDIT_USER', $this->escape($item->name) ) ); ?>">
                            <?php echo $this->escape($item->name); ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_USERNAME'); ?></th>
                    <td><?php echo $this->escape($item->username); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_AVAILABLE_CREDITS'); ?></th>
                    <td>
                    <?php
                    if ($item->unlimited_credits)
                    {
                        echo JText::_('COM_RSDIRECTORY_UNLIMITED');
                    }
                    else
                    {
                        echo $item->credits ? $this->escape($item->credits) : 0;
                    }
                    ?>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_SPENT_CREDITS'); ?></th>
                    <td><?php echo $this->escape($item->spent_credits); ?></td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'); ?></th>
                    <td>
                        <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=creditshistory&filter_user_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_CREDITS_HISTORY_FOR', $this->escape($item->name) ) ); ?>">
                            <?php echo JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'); ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_TRANSACTIONS_COUNT'); ?></th>
                    <td>
                        <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=transactions&filter_user_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_TRANSACTIONS_MADE_BY', $this->escape($item->name) ) ); ?>">
                            <?php echo empty($item->transactions_count) ? 0 : $item->transactions_count; ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_ENTRIES_COUNT'); ?></th>
                    <td>
                        <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=entries&filter_author_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_ENTRIES_POSTED_BY', $this->escape($item->name) ) ); ?>">
                            <?php echo empty($item->entries_count) ? 0 : $item->entries_count; ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_REVIEWS_COUNT'); ?></th>
                    <td>
                        <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=ratings&filter_reviews_author_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_REVIEWS_POSTED_BY', $this->escape($item->name) ) ); ?>">
                            <?php echo empty($item->reviews_count) ? 0 : $item->reviews_count; ?>
                        </a>
                    </td>
                </tr>
                    
                <tr>
                    <th><?php echo JText::_('COM_RSDIRECTORY_REPORTS_COUNT'); ?></th>
                    <td>
                        <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=reportedentries&filter_reports_author_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_REPORTS_POSTED_BY', $this->escape($item->name) ) ); ?>">
                            <?php echo empty($item->reports_count) ? 0 : $item->reports_count; ?>        
                        </a>
                    </td>
                </tr>
                    
            </tbody>
                
        </table>
            
        <?php
            
        echo $this->rsfieldset->getFieldsetEnd();
            
        echo $this->rsfieldset->getFieldsetStart( JText::_('COM_RSDIRECTORY_EDIT_USER_LABEL') );
            
        foreach ($this->form->getFieldset('general') as $field)
        {
            echo $this->rsfieldset->getField($field->label, $field->input);
        }
            
        echo $this->rsfieldset->getFieldsetEnd();
            
        ?>
            
        <div>
            <input type="hidden" name="task" value="" />
            <?php echo JHTML::_('form.token') . "\n"; ?>
        </div>
            
    </form><!-- #user -->
</div><!-- .rsdir -->