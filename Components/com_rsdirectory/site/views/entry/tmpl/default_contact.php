<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<a class="btn btn-primary btn-large" href="#rsdir-contact-modal" role="button" data-toggle="modal"><?php echo JText::_('COM_RSDIRECTORY_CONTACT_AUTHOR'); ?></a>
	
<div id="rsdir-contact-modal" class="rsdir-iframe-modal modal hide fade" tabindex="-1" role="dialog" aria-labelledby="rsdir-contact-header" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="rsdir-contact-header"><?php echo JText::_('COM_RSDIRECTORY_CONTACT_AUTHOR'); ?></h3>
	</div>
	<div class="modal-body">
		<iframe src="<?php echo RSDirectoryRoute::getURL('contact', '', "tmpl=component&entry_id={$this->entry->id}"); ?>" style="height: <?php echo $this->config->get('contact_modal_body_height', 500); ?>px;"></iframe>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSDIRECTORY_CLOSE'); ?></button>
		<button class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
	</div>
</div>