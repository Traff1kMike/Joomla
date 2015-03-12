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

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=reportedentries'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table class="adminlist table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'r.published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JText::_('COM_RSDIRECTORY_VIEW'); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY', 'e.title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY_AUTHOR', 'entry_author', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="20%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_REASON', 'r.reason', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_REPORT_AUTHOR', 'report_author', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_NAME', 'r.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_EMAIL', 'r.email', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'JGLOBAL_CREATED', 'r.created_time', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet center hidden-phone" width="1%">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'r.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="center"><?php echo $this->markRead($item->published, $i, 'reportedentries.'); ?></td>
                        <td class="center">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=reportedentry.edit&id=$item->id"); ?>">
                                <?php echo JText::_('COM_RSDIRECTORY_VIEW'); ?>
                            </a>
                        </td>
                        <td class="nowrap">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>">
                                <?php echo $this->escape($item->title); ?>
                            </a>
                        </td>
                        <td class="center small nowrap hidden-tablet hidden-phone">
                            <?php if ($item->entry_author_id) { ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->entry_author_id"); ?>">
                                <?php echo $this->escape($item->entry_author); ?>
                            </a>
                            <?php } else { ?>
                            -
                            <?php } ?>
                        </td>
                        <td class="small nowrap hidden-phone"><?php echo $this->escape($item->reason); ?></td>
                        <td class="center small nowrap hidden-tablet hidden-phone">
                            <?php if ($item->entry_author_id) { ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->report_author_id"); ?>">
                                <?php echo $this->escape($item->report_author); ?>
                            </a>
                            <?php } else { ?>
                            -
                            <?php } ?>
                        </td>
                        <td class="center small nowrap hidden-tablet hidden-phone"><?php echo $this->escape($item->name); ?></td>
                        <td class="center small nowrap hidden-tablet hidden-phone"><?php echo $this->escape($item->email); ?></td>
                        <td class="center small nowrap hidden-tablet hidden-phone"><?php echo JHtml::_( 'date', $item->created_time, JText::_('DATE_FORMAT_LC4') ); ?></td>
                        <td class="center hidden-tablet hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
            
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="task" value="" />
        <?php if (!$this->isJ30) { ?>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php } ?>
            
    </div><!-- .span10 -->
</form>