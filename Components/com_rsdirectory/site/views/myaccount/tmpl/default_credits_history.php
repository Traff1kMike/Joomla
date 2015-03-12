<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$custom_itemid = $this->params->get('itemid');

?>

<table class="table table-striped">
    <thead>
		<tr>
			<th><?php echo JText::_('COM_RSDIRECTORY_ENTRY'); ?></th>
			<th class="center" width="160"><?php echo JText::_('COM_RSDIRECTORY_TYPE'); ?></th>
			<th class="center" width="80"><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?></th>
			<th class="center" width="40"><?php echo JText::_('COM_RSDIRECTORY_FREE'); ?></th>
			<th class="center" width="40"><?php echo JText::_('COM_RSDIRECTORY_PAID'); ?></th>
			<th class="center hidden-phone" width="100"><?php echo JText::_('JDATE'); ?></th>
		</tr>
    </thead>
    <?php if ($this->credits_history) { ?>
    <tbody>
		<?php foreach ($this->credits_history as $item) { ?>
		<tr>
			<td>
			<?php
			if ( is_null($item->entry_title) )
			{
				echo JText::_('COM_RSDIRECTORY_ENTRY_REMOVED');
			}
			else
			{
				?>
				<a href="<?php echo RSDirectoryRoute::getEntryURL($item->entry_id, '', '', $custom_itemid); ?>"><?php echo $this->escape($item->entry_title); ?></a></td>
				<?php
			}
			?>
			<td class="center">
			<?php
			echo JText::_( 'COM_RSDIRECTORY_CREDIT_OBJECT_TYPE_' . strtoupper($item->object_type) );
			echo $this->escape($item->field_name ? " ($item->field_name)" : '');
			?>
			</td>
			<td class="center"><?php echo $this->escape($item->credits); ?></td>
			<td class="center"><?php echo JText::_($item->free ? 'JYES' : 'JNO'); ?></td>
			<td class="center"><?php echo JText::_($item->paid ? 'JYES' : 'JNO'); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'date', $item->created_time, JText::_('DATE_FORMAT_LC4') ); ?></td>
		</tr>
		<?php } ?>
    </tbody>
    <?php } ?>
</table>

<?php $pagesTotal = isset($this->pagination->pagesTotal) ? $this->pagination->pagesTotal : $this->pagination->get('pages.total'); ?>
<?php if ( $this->params->def('show_pagination', 2) == 1  || ( $this->params->get('show_pagination') == 2 && $pagesTotal > 1 ) ) { ?>
<div class="pagination">
		
    <?php if ( $this->params->def('show_pagination_results', 1) ) { ?>
    <p class="counter<?php echo RSDirectoryHelper::isJ30() ? ' pull-right' : ''; ?>">
        <?php echo $this->pagination->getPagesCounter(); ?>
    </p>
    <?php } ?>
        
    <?php echo $this->pagination->getPagesLinks(); ?>
</div>
<?php } ?>