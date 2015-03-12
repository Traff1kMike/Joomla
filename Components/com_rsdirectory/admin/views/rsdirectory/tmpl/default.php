<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access'); ?>

<div class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div class="span10">
    <div id="rsdir-dashboard-left">
        <?php echo $this->loadTemplate('buttons'); ?>
    </div>
    <div id="rsdir-dashboard-right" class="hidden-phone hidden-tablet">
        <?php echo $this->loadTemplate('version'); ?>
    </div>
</div>