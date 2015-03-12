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

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=emailmessages'); ?>" method="post">        
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
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'em.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_DESCRIPTION', 'em.description', $listDirn, $listOrder); ?>
                    </th>
                    <th class="hidden-phone" width="20%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_SUBJECT', 'em.subject', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_TYPE', 'em.type', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="20%">
                        <?php echo JHtml::_('grid.sort', 'COM_RSDIRECTORY_CATEGORY', 'c.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'em.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
                
            <?php if ($this->items) { ?>
            <tbody>
            <?php foreach ($this->items as $i => $item) { ?>
                <tr>
                    <td class="center hidden-phone"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
                    <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'emailmessages.'); ?></td>
                    <td class="nowrap"><a href="<?php echo JRoute::_("index.php?option=com_rsdirectory&task=emailmessage.edit&id=$item->id"); ?>"><?php echo $this->escape($item->description); ?></a></td>
                    <td class="nowrap hidden-phone"><?php echo $this->escape($item->subject); ?></td>
                    <td class="center"><?php echo $this->escape( JText::_( 'COM_RSDIRECTORY_EMAIL_MESSAGE_TYPES_' . strtoupper($item->type) ) ); ?></td>
                    <td class="center"><?php echo $this->escape( $item->title ? $item->title : JText::_('JALL') ); ?></td>
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