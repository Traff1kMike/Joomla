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

JHtml::_('behavior.tooltip');

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'entry.add')
        {
            <?php RSDirectoryHelper::openModalWindow( JRoute::_('index.php?option=com_rsdirectory&view=categories&layout=modal&tmpl=component', false) ); ?>
                
            return false;
        }
            
        Joomla.submitform( task, document.getElementById('adminForm') );
    }
        
    function newEntry(category_id)
    {
        document.getElementById('category_id').value = category_id;
            
        Joomla.submitform( 'entry.add', document.getElementById('adminForm') );
    }
</script>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=entries'); ?>" method="post">
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
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'e.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TITLE', 'e.title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_FORM', 'form', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'JGLOBAL_CREATED', 'e.created_time', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'e.published_time', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'JGLOBAL_MODIFIED', 'e.modified_time', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_EXPIRY', 'e.expiry_time', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_RENEW', 'e.renew', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_FIELDS_PROMOTED', 'e.promoted', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="10%">
                        <?php echo JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'); ?>
                    </th>
                    <th class="nowrap hidden-phone hidden-tablet center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_PAID', 'e.paid', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'e.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                    <tr>
                        <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'entries.'); ?></td>
                        <td class="nowrap">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->id"); ?>"><?php echo $this->escape($item->title); ?></a>
                            <?php if ($item->category_title) { ?>
                            <div class="small">
                                <?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
                            </div>
                            <?php } ?>
                        </td>
                        <td class="center small nowrap">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=form.edit&id=$item->form_id"); ?>">
                                <?php echo $this->escape($item->form); ?>
                            </a>
                        </td>
                        <td class="center small nowrap hidden-phone">
                            <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->user_id"); ?>">
                                <?php echo $this->escape($item->author); ?>
                            </a>
                        </td>
                        <td class="center small nowrap hidden-tablet hidden-phone">
                        <?php echo $item->created_time == '0000-00-00 00:00:00' ? $item->created_time : JHtml::_( 'date', $item->created_time, JText::_('DATE_FORMAT_LC4') ); ?>
                        </td>
                        <td class="center small nowrap hidden-phone">
                        <?php
                        if ($item->published_time != '0000-00-00 00:00:00')
                        {
                            echo JHtml::_( 'date', $item->published_time, JText::_('DATE_FORMAT_LC4') );
                        }
                        else
                        {
                            echo '<div title="' . JText::_('COM_RSDIRECTORY_ENTRY_NOT_YET_PUBLISHED') . '">-</div>';
                        }
                        ?>
                        </td>
                        <td class="center small nowrap hidden-phone">
                            <?php echo $item->modified_time != '0000-00-00 00:00:00' ? JHtml::_( 'date', $item->modified_time, JText::_('DATE_FORMAT_LC4') ) : '-'; ?>
                        </td>
                        <td class="center small nowrap hidden-phone">
                        <?php
                        if ($item->expiry_time != '0000-00-00 00:00:00')
                        {
                            echo JHtml::_( 'date', $item->expiry_time, JText::_('DATE_FORMAT_LC4') );
                        }
                        else
                        {
                            echo '<div title="' . JText::_('COM_RSDIRECTORY_NO_EXPIRY') . '">-</div>';
                        }
                        ?>
                        </td>
                        <td class="center small nowrap hidden-phone hidden-tablet"><?php echo JText::_( empty($item->renew) ? 'JNO' : 'JYES' ); ?></td>
                        <td class="center small nowrap hidden-phone hidden-tablet"><?php echo JText::_( empty($item->promoted) ? 'JNO' : 'JYES' ); ?></td>
                        <td class="center small nowrap hidden-phone hidden-tablet">
                            <a class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="<?php echo JRoute::_("index.php?option=com_rsdirectory&view=creditshistory&filter_entry_id=$item->id"); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::sprintf( 'COM_RSDIRECTORY_CREDITS_HISTORY_FOR', $this->escape($item->title) ) ); ?>">
                                <?php echo JText::_('COM_RSDIRECTORY_CREDITS_HISTORY'); ?>
                            </a>
                        </td>
                        <td class="center small nowrap hidden-phone hidden-tablet"><?php echo $this->markPaid($item->paid, $i, 'entries.'); ?></td>
                        <td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td>
                </tr>
            </tfoot>
        </table>
            
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="task" value="" />
        <input id="category_id" type="hidden" name="category_id" value="" /><!-- Used when creating a new entry. -->
        <?php if (!$this->isJ30) { ?>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php } ?>
            
        <?php echo $this->loadTemplate($this->isJ30 ? 'batch_modal' : 'batch'); ?>
            
    </div><!-- .span10 -->
</form>