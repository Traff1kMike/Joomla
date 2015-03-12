<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

if ( RSDirectoryHelper::isJoomlaCompatible('3.2.3') )
{
    JHtml::_('formbehavior.chosen', 'select');
}

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if ( task == 'tools.cancel' || document.formvalidator.isValid( document.id('tools') ) )
        {
            Joomla.submitform( task, document.getElementById('tools') );
        }
    }
</script>

<div class="rsdir">
    <div class="row-fluid">
        <?php if ($this->isJ30) { ?>
        <div class="span2">
            <?php echo $this->sidebar; ?>
        </div><!-- .span2 -->
        <div class="span10">
        <?php } ?>
			<form id="tools" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_rsdirectory'); ?>" method="post" enctype="multipart/form-data">
					
				<?php
					
				// IMPORT
				$this->rstabs->addTitle('COM_RSDIRECTORY_IMPORT', 'import');
				$this->rstabs->addContent( $this->loadTemplate('import') );
					
				// REGENERATE TITLES
				$this->rstabs->addTitle('COM_RSDIRECTORY_REGENERATE_TITLES', 'regenerate-titles');
				$this->rstabs->addContent( $this->loadTemplate('regenerate_titles') );
					
				// BACKUP
				$this->rstabs->addTitle('COM_RSDIRECTORY_BACKUP', 'backup');
				$this->rstabs->addContent( $this->loadTemplate('backup') );
					
				// RESTORE
				$this->rstabs->addTitle('COM_RSDIRECTORY_RESTORE', 'restore');
				$this->rstabs->addContent( $this->loadTemplate('restore') );
					
				// Render the tabs.
				$this->rstabs->render();
					
				?>
					
				<input type="hidden" name="task" value="" />
				<?php echo JHTML::_('form.token') . "\n"; ?>
					
			</form><!-- #tools -->
		<?php if ($this->isJ30) { ?>
        </div><!-- .span10 -->
        <?php } ?>
    </div><!-- .row-fluid -->
</div><!-- .rsdir -->