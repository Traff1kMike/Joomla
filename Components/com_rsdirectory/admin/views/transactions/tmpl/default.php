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

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=transactions'); ?>" method="post">        
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
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 't.published', $listDirn, $listOrder); ?>
                    </th>
					<th class="nowrap">
						<?php echo JText::_('COM_RSDIRECTORY_TRANSACTION_DETAILS'); ?>
					</th>
                    <th class="nowrap hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CREDIT_PACKAGE_TITLE', 't.credit_title', $listDirn, $listOrder); ?>
                    </th>
					<th class="nowrap hidden-phone center" width="6%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CREDITS', 't.credits', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_USER', 'user', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="20%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY', 'entry_title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY_PAID', 'entry_paid', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_GATEWAY', 't.gateway', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CURRENCY', 't.currency', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_PRICE', 't.price', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TAX', 't.tax', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TOTAL', 't.total', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_DATE_CREATED', 't.date_created', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_DATE_FINALIZED', 't.date_finalized', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet center hidden-phone" width="1%">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 't.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
                <tbody>
                <?php foreach ($this->items as $i => $item) {; ?>
                    <tr>
                        <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                        <td class="center"><?php echo $this->markFinalized($item->status == 'finalized' ? 1 : 0, $i, 'transactions.'); ?></td>
						<td class="nowrap">
							<a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=transaction.edit&id=$item->id"); ?>" class="btn btn-small">
								<?php echo JText::_('COM_RSDIRECTORY_DETAILS'); ?>
							</a>
						</td>
                        <td class="nowrap hidden-phone">
                            <?php echo $this->escape($item->credit_title); ?>
                        </td>
						<td class="center nowrap hidden-phone"><?php echo $item->credits ? $this->escape($item->credits) : JText::_('COM_RSDIRECTORY_UNLIMITED'); ?></td>
                        <td class="center nowrap hidden-tablet hidden-phone">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=user.edit&id=$item->user_id"); ?>">
                                <?php echo $this->escape($item->user); ?>        
                            </a>
                        </td>
                        <td class="center nowrap hidden-tablet hidden-phone">
                            <?php if ( empty($item->entry_id) ) { ?>
                            -
                            <?php } else { ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>">
                                <?php echo $this->escape($item->entry_title); ?>        
                            </a>
                            <?php } ?>
                        </td>
                        <td class="center small nowrap hidden-tablet hidden-phone">
                            <?php echo JText::_($item->entry_paid ? 'JYES' : 'JNO'); ?>
                        </td>
                        <td class="center nowrap hidden-phone"><?php echo JText::_( 'COM_RSDIRECTORY_TRANSACTION_GATEWAY_' . strtoupper($item->gateway) ); ?></td>
                        <td class="center nowrap"><?php echo $this->escape($item->currency); ?></td>
                        <td class="center nowrap"><?php echo $this->escape($item->price); ?></td>
                        <td class="center nowrap"><?php echo $this->escape($item->tax); ?></td>
                        <td class="center nowrap"><?php echo $this->escape($item->total); ?></td>
                        <td class="center nowrap hidden-tablet hidden-phone"><?php echo JHtml::_( 'date', $item->date_created, JText::_('DATE_FORMAT_LC4') ); ?></td>
                        <td class="center nowrap hidden-tablet hidden-phone">
                        <?php echo $item->date_finalized == '0000-00-00 00:00:00' ? '-' : JHtml::_( 'date', $item->date_finalized, JText::_('DATE_FORMAT_LC4') ); ?>
                        </td>
                        <td class="center hidden-tablet hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="16"><?php echo $this->pagination->getListFooter(); ?></td>
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