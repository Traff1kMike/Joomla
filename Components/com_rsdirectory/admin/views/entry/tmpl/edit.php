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
JHtml::_('behavior.modal');

if ( RSDirectoryHelper::isJoomlaCompatible('3.2.3') )
{
    JHtml::_('formbehavior.chosen', 'select');
}

$rsfieldset = $this->rsfieldset;
$form = $this->form;
$jform = $this->jform;
$entry = empty($this->entry) ? null : $this->entry;

$action = 'index.php?option=com_rsdirectory&view=entry&layout=edit&id=' . (int)$this->id;

if ($this->category_id)
{
    $action .= "&category_id=$this->category_id";
}

?>

<script type="text/javascript">
        
    Joomla.submitbutton = function(task)
    {
        if ( task == 'entry.cancel' || document.formvalidator.isValid( document.id('form') ) )
        {
            Joomla.submitform( task, document.getElementById('form') );
        }
    }
        
</script>

<div class="rsdir">
    <div class="row-fluid">
        <form id="form" class="rsdir-add-entry form-validate" action="<?php echo JRoute::_($action); ?>" method="post" enctype="multipart/form-data">
                
            <?php echo $rsfieldset->getFieldsetStart(); ?>
                
            <div class="control-group">
                <?php echo $jform->getLabel('category_id'); ?>
                <?php echo $jform->getInput('category_id'); ?>
            </div>
                
            <div class="control-group">
                <label><?php echo $jform->getLabel('user_id'); ?></label>
                <?php echo $jform->getInput('user_id'); ?>
            </div>
                
            <?php
                
            if ($form)
            {
                if ( !empty($this->form_fields) )
                {
                    foreach ($this->form_fields as $form_field)
                    {
                        if ($form_field->field_type == 'section_break')
                            continue;
                            
                        echo RSDirectoryFormField::getInstance($form_field, $entry)->generate();
                    }
                }
            }
            else
            {
                ?>
                    
                <p><?php echo JText::_('COM_RSDIRECTORY_NO_FORM_ASSOCIATED_ERROR'); ?></p>
                    
                <?php
            }
                
            ?>
                
            <div class="control-group">
                <?php echo $jform->getLabel('published_time'); ?>
                <?php echo $jform->getInput('published_time'); ?>
            </div>
                
            <div class="control-group">
                <?php echo $jform->getLabel('published'); ?>
                <?php echo $jform->getInput('published'); ?>
            </div>
                
            <div class="control-group">
                <?php echo $jform->getLabel('paid'); ?>
                <?php echo $jform->getInput('paid'); ?>
            </div>
                
            <?php echo $rsfieldset->getFieldsetEnd(); ?>
                
            <div>
                <input type="hidden" name="fields[id]" value="<?php echo !empty($entry) ? $entry->id : 0; ?>" />
                <input type="hidden" name="task" value="" />
                <?php echo JHTML::_('form.token') . "\n"; ?>
            </div>
                
        </form><!-- #form -->
    </div><!-- .row-fluid -->
</div><!-- .rsdir -->