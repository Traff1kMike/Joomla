<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');


echo $this->rsfieldset->getFieldsetStart();

?>

<?php if ( !empty($this->backup_errors) ) { ?>
<div class="alert alert-error">
    <?php echo implode('<br />', $this->backup_errors); ?>
</div>
<?php } ?>

<div class="progress">
    <div class="bar" style="width: 0%;">
        <div class="pull-right progress-label">0% 0/0</div>
    </div>
</div>

<div class="row-fluid">
    <div class="span6">
        <div class="alert alert-info">
            <h3><?php echo JText::_('COM_RSDIRECTORY_BACKUP_NOTE'); ?></h3>
            <ul>
                <li><?php echo JText::_('COM_RSDIRECTORY_BACKUP_NOTE_GENERAL'); ?></li>
                <li><?php echo JText::_('COM_RSDIRECTORY_BACKUP_NOTE_FILES'); ?></li>
            </ul>
        </div>
            
        <button id="backup-start" class="btn btn-primary"<?php echo empty($this->backup_errors) ? '' : ' disabled="disabled"'; ?>><?php echo JText::_('COM_RSDIRECTORY_BACKUP_START_BUTTON'); ?></button>
        <button id="backup-stop" class="btn btn-danger rsdir-hide"><?php echo JText::_('COM_RSDIRECTORY_BACKUP_STOP_BUTTON'); ?></button>
    </div>
    
    <div class="span6">
            
        <h3><?php echo JText::_('COM_RSDIRECTORY_BACKUP_CACHE'); ?></h3>
            
        <div class="span8">
            <table id="cached-files" class="table table-striped">
                <thead>
                    <tr>
                        <th><input id="checkall-cached-files" type="checkbox" /></th>
                        <th width="5%">#</th>
                        <th><?php echo JText::_('COM_RSDIRECTORY_BACKUP_FILE'); ?></th>
                        <th><?php echo JText::_('JDATE'); ?></th>
                        <th><?php echo JText::_('COM_RSDIRECTORY_RESTORE'); ?></th>
                    </tr>
                </thead>
                <?php if ($this->backup_cached_files) { ?>
                <tbody>
                <?php
                foreach ($this->backup_cached_files as $i => $file)
                {
                    echo RSDirectoryHelper::getBackupCachedFileRowHTML($file, $i + 1); 
                }
                ?>
                </tbody>
                <?php } ?>
                <tfoot<?php echo $this->backup_cached_files ? ' class="hide"' : ''; ?>>
                    <tr>
                        <td colspan="5"><?php echo JText::_('COM_RSDIRECTORY_BACKUP_NO_CACHED_FILES'); ?></td>
                    </tr>
                </tfoot>
            </table>
                
            <button id="backup-delete-files" class="btn btn-danger<?php echo $this->backup_cached_files ? '' : ' hide'; ?>"><?php echo JText::_('COM_RSDIRECTORY_BACKUP_DELETE_FILES'); ?></button>
            <img id="backup-delete-files-loader" class="hide" src="<?php echo JURI::root(true); ?>/media/com_rsdirectory/images/loader.gif" width="16" height="16" alt="" />
        </div>
            
    </div>
</div>

<?php

echo $this->rsfieldset->getFieldsetEnd();