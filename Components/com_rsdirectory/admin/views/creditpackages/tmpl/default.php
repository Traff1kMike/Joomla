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
$saveOrder = $listOrder == 'ordering';
$ordering = $listOrder == 'ordering';

if ($saveOrder && $this->isJ30)
{
    $saveOrderingUrl = 'index.php?option=com_rsdirectory&task=creditpackages.saveOrderAjax&tmpl=component';
    JHtml::_( 'sortablelist.sortable', 'credit-packages-list', 'adminForm', strtolower($listDirn), $saveOrderingUrl );
}

?>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=creditpackages'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table id="credit-packages-list" class="adminlist table table-striped">
            <thead>
                <tr>
                    <?php if ($this->isJ30) { ?>
                    <th width="1%" class="nowrap center hidden-phone">
                    <?php } else { ?>
                    <th width="100">
                    <?php } ?>
                    <?php if ($this->isJ30) { ?>
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    <?php } else { ?>
                        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
                        <?php if ($saveOrder) { ?>
                            <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'creditpackages.saveorder'); ?>
                        <?php } ?>
                    <?php } ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TITLE', 'title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_PRICE', 'price', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="6%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CREDITS', 'credits', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'f.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="order nowrap center hidden-phone">
                            <?php if ($this->isJ30) { ?>
                                <?php
                                $disableClassName = '';
                                $disabledLabel = '';
                                    
                                if (!$saveOrder)
                                {
                                    $disabledLabel = JText::_('JORDERINGDISABLED');
                                    $disableClassName = 'inactive tip-top';
                                }
                                ?>
                                <span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
                                    <i class="icon-menu"></i>
                                </span>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
                            <?php } else { ?>
                                <?php if ($saveOrder) { ?>
                                    <?php if ($listDirn == 'asc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, true, 'creditpackages.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'creditpackages.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } elseif ($listDirn == 'desc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, true, 'creditpackages.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'creditpackages.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } ?>
                                <?php } ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                            <?php } ?>
                        </td>
                        <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'creditpackages.'); ?></td>
                        <td class="nowrap"><a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=creditpackage.edit&id=$item->id"); ?>"><?php echo $this->escape($item->title); ?></a></td>
                        <td class="center"><?php echo RSDirectoryHelper::formatPrice($item->price); ?></td>
                        <td class="center"><?php echo $item->credits ? $this->escape($item->credits) : JText::_('COM_RSDIRECTORY_UNLIMITED'); ?></td>
                        <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="7">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
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