<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); ?>

<p><?php echo JText::_('COM_RSDIRECTORY_ADD_ENTRY_CATEGORY_SELECT_DESC'); ?></p>
    
<div class="control-group">
    <?php echo $this->categories_select; ?>
</div>

<input type="hidden" name="task" value="entry.selectCategory" />
<input type="hidden" name="fields[category_id]" value="0" />

<button id="rsdir-submit-category" class="btn btn-primary btn" type="submit" disabled="disabled"><?php echo JText::_('JNEXT'); ?></button>
<a id="rsdir-start-over" class="btn btn-link" href="javascript: void(0);"><?php echo JText::_('COM_RSDIRECTORY_START_OVER'); ?></a>