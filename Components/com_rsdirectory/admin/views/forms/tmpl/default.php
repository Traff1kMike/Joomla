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

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=forms'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table id="forms-list" class="adminlist table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TITLE', 'title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center hidden-phone">
                        <?php echo JText::_('COM_RSDIRECTORY_FORM_FIELDS'); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="nowrap">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=form.edit&id=$item->id"); ?>">
                                <?php echo $this->escape($item->title); ?>
                            </a>
                        </td>
                        <td class="center hidden-phone">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=fields&filter_form=$item->id"); ?>">
                                <?php echo JText::_('COM_RSDIRECTORY_FORM_FIELDS'); ?>
                            </a>
                        </td>
                        <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
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