<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape( $this->state->get('list.ordering') );
$listDirn = $this->escape( $this->state->get('list.direction') );

?>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=users'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table class="adminlist table table-striped">
            <thead>
                <tr>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_NAME', 'u.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_USERNAME', 'u.username', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_AVAILABLE_CREDITS', 'uc.credits', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_SPENT_CREDITS', 'ec.spent_credits', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center hidden-phone" width="10%">
                        <?php echo JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'); ?>
                    </th>
                    <th class="nowrap center hidden-phone" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TRANSACTIONS', 't.transactions_count', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center hidden-phone" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRIES', 'e.entries_count', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center hidden-tablet hidden-phone" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_REVIEWS', 'rev.reviews_count', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center hidden-tablet hidden-phone" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_REPORTS', 'rep.reports_count', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center hidden-phone" width="1%">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'u.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="nowrap">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=user.edit&id=$item->id"); ?>">
                                <?php echo $this->escape($item->name); ?>
                            </a>    
                        </td>
                        <td class="center"><?php echo $this->escape($item->username); ?></td>
                        <td class="center">
                        <?php
                        if ($item->unlimited_credits)
                        {
                            echo JText::_('COM_RSDIRECTORY_UNLIMITED');
                        }
                        else
                        {
                            echo $item->credits ? $item->credits : 0;
                        }
                        ?>
                        </td>
                        <td class="center"><?php echo $item->spent_credits ? $item->spent_credits : 0; ?></td>
                        <td class="center hidden-phone">
                            <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=creditshistory&filter_user_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_CREDITS_HISTORY_FOR', $this->escape($item->name) ) ); ?>">
                                <?php echo JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'); ?>
                            </a>
                        </td>
                        <td class="center hidden-phone">
                            <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=transactions&filter_user_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_TRANSACTIONS_MADE_BY', $this->escape($item->name) ) ); ?>">
                                <?php echo empty($item->transactions_count) ? 0 : $item->transactions_count; ?>
                            </a>
                        </td>
                        <td class="center hidden-phone">
                            <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=entries&filter_author_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_ENTRIES_POSTED_BY', $this->escape($item->name) ) ); ?>">
                                <?php echo empty($item->entries_count) ? 0 : $item->entries_count; ?>
                            </a>
                        </td>
                        <td class="center hidden-tablet hidden-phone">
                            <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=ratings&filter_reviews_author_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_REVIEWS_POSTED_BY', $this->escape($item->name) ) ); ?>">
                                <?php echo empty($item->reviews_count) ? 0 : $item->reviews_count; ?>
                            </a>
                        </td>
                        <td class="center hidden-tablet hidden-phone">
                            <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=reportedentries&filter_reports_author_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_REPORTS_POSTED_BY', $this->escape($item->name) ) ); ?>">
                                <?php echo empty($item->reports_count) ? 0 : $item->reports_count; ?>        
                            </a>
                        </td>
                        <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
            
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="task" value="" />
        <?php if (!$this->isJ30) { ?>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php } ?>
            
    </div><!-- .span10 -->
</form>