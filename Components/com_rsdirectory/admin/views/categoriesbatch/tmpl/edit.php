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
        if ( task == 'categoriesbatch.cancel' || document.formvalidator.isValid( document.id('item-form') ) )
        {
            Joomla.submitform( task, document.getElementById('item-form') );
        }
    }
</script>

<div class="rsdir">
    <form id="item-form" name="adminForm" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=categoriesbatch&layout=edit'); ?>" method="post">
        
    <?php
    echo $this->rsfieldset->getFieldsetStart();
        
    foreach ($this->form->getFieldset('category_details') as $field)
    {
        $input = $field->input;
            
        if ($field->fieldname == 'categories')
        {
            $input .= '<div class="help-block">' . JText::_('COM_RSDIRECTORY_CATEGORIES_BATCH_CATEGORIES_DESC') . '</div>';
        }
            
        echo $this->rsfieldset->getField($field->label, $input);
    }
        
    echo $this->rsfieldset->getFieldsetEnd();
    ?>
        
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_('form.token') . "\n"; ?>
        
    </form><!-- #item-form -->
</div><!-- .rsdir -->