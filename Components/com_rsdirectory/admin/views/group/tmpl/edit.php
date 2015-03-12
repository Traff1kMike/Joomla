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

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if ( task == 'group.cancel' || document.formvalidator.isValid( document.id('group') ) )
        {
            Joomla.submitform( task, document.getElementById('group') );
        }
    }
</script>

<div class="rsdir">
    <form id="group" class="form-validate" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=group&layout=edit&id=' . (int)$this->id ); ?>" method="post">
            
        <?php
        echo $this->rsfieldset->getFieldsetStart();
            
        foreach ($this->form->getFieldset('group') as $field)
        {
            echo $this->rsfieldset->getField($field->label, $field->input);
        }
            
        echo $this->rsfieldset->getFieldsetEnd();
        ?>
            
        <input type="hidden" name="task" value="" />
        <?php echo JHTML::_('form.token') . "\n"; ?>
            
    </form><!-- #group -->
</div>