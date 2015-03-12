<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

?>

<div class="progress">
    <div class="bar" style="width: 0%;">
        <div class="pull-right progress-label">0% 0/0</div>
    </div>
</div>

<?php echo $this->rsfieldset->getFieldsetStart(); ?>

<?php

echo $this->rsfieldset->getField(
    $this->form->getLabel('regenerate_titles_forms'),
    $this->form->getInput('checkall_regenerate_titles_forms') . $this->form->getInput('regenerate_titles_forms')
);

echo $this->rsfieldset->getField(
    $this->form->getLabel('regenerate_titles_elements'),
    $this->form->getInput('checkall_regenerate_titles_elements') . $this->form->getInput('regenerate_titles_elements')
);

?>

<?php echo $this->rsfieldset->getFieldsetEnd(); ?>

<button id="regenerate-titles-start" class="btn btn-primary"><?php echo JText::_('COM_RSDIRECTORY_REGENERATE_TITLES_START_BUTTON'); ?></button>
<button id="regenerate-titles-stop" class="btn btn-danger rsdir-hide"><?php echo JText::_('COM_RSDIRECTORY_REGENERATE_TITLES_STOP_BUTTON'); ?></button>