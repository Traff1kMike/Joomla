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

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=ratings'); ?>" method="post">        
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
                        <?php echo JText::_('COM_RSDIRECTORY_EDIT'); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY', 'e.title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_ENTRY_AUTHOR', 'entry_author', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="5%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_RATING', 'r.score', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-phone center" width="20%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_SUBJECT', 'r.subject', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_REVIEW_AUTHOR', 'review_author', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap hidden-tablet hidden-phone center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_NAME', 'r.name', $listDirn, $listOrder); ?>
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
                        <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'ratings.'); ?></td>
                        <td class="center">
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=rating.edit&id=$item->id"); ?>">
                                <?php echo JText::_('COM_RSDIRECTORY_EDIT'); ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=entry.edit&id=$item->entry_id"); ?>">
                                <?php echo $this->escape($item->title); ?>
                            </a>
                        </td>
                        <td class="center small nowrap hidden-tablet hidden-phone">
                            <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->entry_author_id"); ?>">
                                <?php echo $this->escape($item->entry_author); ?>        
                            </a>
                        </td>
                        <td class="center small nowrap hidden-phone">
                            <div class="rsdir-listing-rating" data-rating="<?php echo $item->score; ?>"></div>
                        </td>
                        <td class="small hidden-phone"><?php echo $this->escape($item->subject); ?></td>
                        <td class="center small nowrap hidden-tablet hidden-phone">
                            <a href="<?php echo JRoute::_("index.php?option=com_users&task=user.edit&id=$item->review_author_id"); ?>">
                                <?php echo $this->escape($item->review_author); ?>        
                            </a>
                        </td>
                        <td class="center small nowrap hidden-tablet hidden-phone"><?php echo $this->escape($item->name); ?></td>
                        <td class="center small nowrap hidden-tablet hidden-phone"><?php echo JHtml::_( 'date', $item->created_time, JText::_('DATE_FORMAT_LC4') ); ?></td>
                        <td class="center hidden-tablet hidden-phone"><?php echo (int) $item->id; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>
                
            <tfoot>
                <tr>
                    <td colspan="11">
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