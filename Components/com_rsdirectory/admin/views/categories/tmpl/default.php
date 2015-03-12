<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->escape ($this->state->get('list.ordering') );
$listDirn = $this->escape( $this->state->get('list.direction') );
$ordering = $listOrder == 'a.lft';
$saveOrder = $listOrder == 'a.lft' && $listDirn == 'asc';

if ($saveOrder && $this->isJ30)
{
    $saveOrderingUrl = 'index.php?option=com_rsdirectory&task=categories.saveOrderAjax&tmpl=component';
    JHtml::_( 'sortablelist.sortable', 'category-list', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true );
}

?>

<script type="text/javascript">
    Joomla.orderTable = function()
    {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>')
        {
            dirn = 'asc';
        }
        else
        {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=categories'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table id="category-list" class="adminlist table table-striped">
                
            <thead>
                <tr>
                    <?php if ($this->isJ30) { ?>
                    <th width="1%" class="nowrap center hidden-phone">
                    <?php } else { ?>
                    <th width="100">
                    <?php } ?>
                    <?php if ($this->isJ30) { ?>
                        <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    <?php } else { ?>
                        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
                        <?php if ($saveOrder) { ?>
                            <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'categories.saveorder'); ?>
                        <?php } ?>
                    <?php } ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
                    </th>
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="15%" class="center">
                        <?php echo JText::_('COM_RSDIRECTORY_FORM'); ?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php
                    
                foreach ($this->items as $i => $item)
                {
                    $orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
                        
                    // Get the parents of item for sorting.
                    if ($item->level > 1)
                    {
                        $parentsStr = '';
                        $_currentParentId = $item->parent_id;
                        $parentsStr = " $_currentParentId";
                            
                        for ($i2 = 0; $i2 < $item->level; $i2++)
                        {
                            foreach ($this->ordering as $k => $v)
                            {
                                $v = implode('-', $v);
                                $v = "-$v-";
                                    
                                if ( strpos($v, "-$_currentParentId-") !== false )
                                {
                                    $parentsStr .= " $k";
                                    $_currentParentId = $k;
                                    break;
                                }
                            }
                        }
                    }
                    else
                    {
                        $parentsStr = '';
                    }
                    
                    ?>
                    <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id;?>" item-id="<?php echo $item->id?>" parents="<?php echo $parentsStr?>" level="<?php echo $item->level?>">
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
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo  $orderkey + 1; ?>" class="width-20 text-area-order" />
                            <?php } else { ?>
                                <?php if ($saveOrder) { ?>
                                    <?php if ($listDirn == 'asc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, true, 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } elseif ($listDirn == 'desc') { ?>
                                        <span><?php echo $this->pagination->orderUpIcon($i, true, 'categories.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'categories.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                    <?php } ?>
                                <?php } ?>
                                <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                                <input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
                            <?php } ?>
                        </td>
                        <td class="nowrap center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="nowrap center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'categories.'); ?></td>
                        <td>
                            <?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1); ?>
                                
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=category.edit&id=$item->id");?>">
                                <?php echo $this->escape($item->title); ?>
                            </a>
                            <span class="small" title="<?php echo $this->escape($item->path); ?>">
                                <?php if (!$item->note) { ?>
                                    <?php echo JText::sprintf( 'JGLOBAL_LIST_ALIAS', $this->escape($item->alias) );?>
                                <?php } else { ?>
                                    <?php echo JText::sprintf( 'JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note) );?>
                                <?php } ?>
                            </span>
                        </td>
                        <td class="center nowrap small">
                            <?php echo $this->escape($item->form->title); ?>
                        </td>
                        <td class="small hidden-phone">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="small nowrap hidden-phone">
                        <?php if ($item->language == '*') { ?>
                            <?php echo JText::alt('JALL', 'language'); ?>
                        <?php } else { ?>
                            <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                        <?php } ?>
                        </td>
                        <td class="nowrap center hidden-phone">
                            <span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
                                <?php echo (int) $item->id; ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
                
        </table>
            
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="original_order_values" value="" />
            
        <?php if (!$this->isJ30) { ?>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php } ?>
            
    </div><!-- .span10 -->
</form>