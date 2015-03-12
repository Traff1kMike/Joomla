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
        if ( task == 'creditpackage.cancel' || document.formvalidator.isValid( document.id('credit-package') ) )
        {
            Joomla.submitform( task, document.getElementById('credit-package') );
        }
    }
</script>

<div class="rsdir">
    <form id="credit-package" class="form-validate" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=creditpackage&layout=edit&id=' . (int)$this->id ); ?>" method="post">
        
    <?php
    echo $this->rsfieldset->getFieldsetStart();
        
    foreach ($this->form->getFieldset('creditpackage') as $field)
    {
        echo $this->rsfieldset->getField($field->label, $field->input);
    }
        
    echo $this->rsfieldset->getFieldsetEnd();
    ?>
        
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_('form.token') . "\n"; ?>
        
    </form><!-- #credit-package -->
</div>