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

$rsfieldset = $this->rsfieldset;
$rstabs = $this->rstabs;
$form = $this->form;

?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if ( task == 'category.cancel' || document.formvalidator.isValid( document.id('item-form') ) )
        {
            <?php echo $this->form->getField('description')->save(); ?>
            Joomla.submitform( task, document.getElementById('item-form') );
        }
    }
</script>

<div class="rsdir">
    <form id="item-form" name="adminForm" class="form-validate" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=category&layout=edit&id=' . (int)$this->id ); ?>" method="post" enctype="multipart/form-data">
            
        <div class="form-inline form-inline-header">
            <?php echo $rsfieldset->getField( $form->getLabel('title'), $form->getInput('title') ); ?>
            <?php echo $rsfieldset->getField( $form->getLabel('alias'), $form->getInput('alias') ); ?>
        </div>
            
        <div class="form-horizontal">
            <?php
            // Category details.
            $this->rstabs->addTitle('COM_RSDIRECTORY_CATEGORY', 'category');
            $this->rstabs->addContent( $this->loadTemplate('category') );
             
            // Category metadata.
            $this->rstabs->addTitle('COM_RSDIRECTORY_METADATA', 'metadata');
            $this->rstabs->addContent( $this->loadTemplate('metadata') );
                
            // Render tabs.
            $rstabs->render();
            ?>
                
            <div>
                <?php echo $form->getInput('id'); ?>
                <input type="hidden" name="task" value="" />
                <?php echo JHTML::_('form.token') . "\n"; ?>
            </div>
        </div>
            
    </form><!-- #category -->
</div>