<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<a id="rsdir-report-button" class="btn" href="#rsdir-report-modal" role="button" data-toggle="modal">
	<i class="icon-info-sign"></i> <?php echo JText::_('COM_RSDIRECTORY_REPORT_ENTRY'); ?>
</a>
	
<div id="rsdir-report-modal" class="rsdir-iframe-modal modal hide fade" tabindex="-1" role="dialog" aria-labelledby="rsdir-report-header" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="rsdir-report-header"><?php echo JText::_('COM_RSDIRECTORY_REPORT_ENTRY'); ?></h3>
	</div>
	<div class="modal-body">
		<iframe src="<?php echo RSDirectoryRoute::getURL('entryreport', '', "entry_id={$this->entry->id}&tmpl=component"); ?>" style="height: <?php echo $this->config->get('reporting_modal_body_height', 400); ?>px;"></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSDIRECTORY_CLOSE'); ?></button>
		<button class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
	</div>
</div>