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

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    { 
        Joomla.submitform( task, document.getElementById('adminForm') );
    }
</script>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=creditshistory'); ?>" method="post">        
    <div class="span2">
        <?php echo $this->sidebar; ?>
    </div><!-- .span2 -->
    <div class="span10">
            
        <?php $this->filterbar->show(); ?>
            
        <table id="credit-packages-list" class="adminlist table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY', 'e.title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_USER', 'user', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TYPE', 'ec.object_type', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CREDITS', 'ec.credits', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_FREE', 'ec.free', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_PAID', 'ec.paid', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'JGLOBAL_CREATED', 'ec.created_time', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'ec.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="nowrap">
                            <?php if ($item->title) { ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>"><?php echo $this->escape($item->title); ?></a>
                            <?php } else { ?>
                            <?php echo JText::_('COM_RSDIRECTORY_ENTRY_REMOVED'); ?>
                            <?php } ?>
                        </td>
                        <td class="center"><a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=user.edit&id=$item->user_id"); ?>"><?php echo $this->escape($item->user); ?></a></td>
                        <td class="center small nowrap">
                        <?php
                        echo JText::_( 'COM_RSDIRECTORY_CREDIT_OBJECT_TYPE_' . strtoupper($item->object_type) );
                        echo $item->field_name ? " ($item->field_name)" : '';
                        ?>
                        </td>
                        <td class="center small"><?php echo $item->credits; ?></td>
                        <td class="center small"><?php echo JText::_($item->free ? 'JYES' : 'JNO'); ?></td>
                        <td class="center small nowrap hidden-phone hidden-tablet"><?php echo $this->markPaid($item->paid, $i, 'creditshistory.'); ?></td>
                        <td class="center small nowrap hidden-tablet hidden-phone"><?php echo JHtml::_( 'date', $item->created_time, JText::_('DATE_FORMAT_LC4') ); ?></td>
                        <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="9">
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