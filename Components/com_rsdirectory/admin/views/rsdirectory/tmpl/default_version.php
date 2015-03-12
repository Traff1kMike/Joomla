<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>

<div class="rsdir-dashboard-container">
    <div class="rsdir-dashboard-info">
            
        <img src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/rsdirectory.png" width="567" height="130" alt="RSDirectory!" />
            
        <table class="rsdir-dashboard-table">
            <tr>
                <td nowrap="nowrap"><strong><?php echo JText::_('COM_RSDIRECTORY_PRODUCT_VERSION') ?>: </strong></td>
                <td nowrap="nowrap">
                    <?php echo RSDirectoryVersion::$product; ?>
                    <?php echo RSDirectoryVersion::$version; ?>
                </td>
            </tr>
            <tr>
                <td nowrap="nowrap"><strong><?php echo JText::_('COM_RSDIRECTORY_COPYRIGHT_NAME') ?>: </strong></td>
                <td nowrap="nowrap">&copy; 2013 - 2014 <a href="http://www.rsjoomla.com" target="_blank">RSJoomla!</a></td>
            </tr>
            <tr>
                <td nowrap="nowrap"><strong><?php echo JText::_('COM_RSDIRECTORY_LICENSE_NAME') ?>: </strong></td>
                <td nowrap="nowrap"><a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU/GPL</a> Commercial</a></td>
            </tr>
            <tr>
                <td nowrap="nowrap"><strong><?php echo JText::_('COM_RSDIRECTORY_UPDATE_CODE') ?>: </strong></td>
                <?php if ( strlen($this->code) == 20 ) { ?>
                <td class="rsdir-correct-code" nowrap="nowrap"><?php echo $this->escape($this->code); ?></td>
                <?php } elseif ($this->code) { ?>
                <td class="rsdir-incorrect-code" nowrap="nowrap"><?php echo $this->escape($this->code); ?></td>
                <?php } else { ?>
                <td class="rsdir-missing-code" nowrap="nowrap"><a href="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=configuration'); ?>"><?php echo JText::_('COM_RSDIRECTORY_ENTER_UPDATE_CODE_IN_CONFIGURATION'); ?></a></td>
                <?php } ?>
            </tr>
        </table>
    </div><!-- .rsdir-dashboard-info -->
</div><!-- .rsdir-dashboard-container -->