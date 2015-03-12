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
        if ( task == 'emailmessage.cancel' || document.formvalidator.isValid( document.id('email-message') ) )
        {
            Joomla.submitform( task, document.getElementById('email-message') );
        }
    }
        
    jQuery(function($)
    {
        var category = $('.category');
        var custom_fields_placeholders = $( document.getElementById('custom-fields-placeholders') );
        var type = $( document.getElementById('jform_type') );
        var entry_expiration_period = $( document.getElementById('jform_entry_expiration_period') );
            
        if ( category.val() != 0 )
        {
            getCustomFieldsPlaceholders( category.val() );
        }
        else
        {
            custom_fields_placeholders.html( '<div>' + Joomla.JText._('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY_NO_CUSTOM_FIELDS') + '</div>' );
        }
            
        category.change(function()
        {
            if ( $(this).val() != 0 )
            {
                getCustomFieldsPlaceholders( $(this).val() );
            }
            else
            {
                custom_fields_placeholders.html( '<div>' + Joomla.JText._('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY_NO_CUSTOM_FIELDS') + '</div>' );
            }
        });
            
        if ( type.val() != 'entry_expiration_notice' )
        {
            entry_expiration_period.parents('.control-group').addClass('hide');
        }
            
        type.change(function()
        {
            if ( $(this).val() == 'entry_expiration_notice' )
            {
                entry_expiration_period.parents('.control-group').removeClass('hide');
            }
            else
            {
                entry_expiration_period.parents('.control-group').addClass('hide');    
            }
        });
    });
</script>

<div class="rsdir">
    <form id="email-message" class="form-validate" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=emailmessage&layout=edit&id=' . (int)$this->id ); ?>" method="post">
        
    <?php
    echo $this->rsfieldset->getFieldsetStart();
        
    foreach ($this->form->getFieldset('emailmessage') as $field)
    {
        echo $this->rsfieldset->getField($field->label, $field->input);
    }
        
    $placeholders = RSDirectoryHelper::getGlobalPlaceholdersHTML() .
                    RSDirectoryHelper::getUserPlaceholdersHTML() .
                    RSDirectoryHelper::getCreditsPlaceholdersHTML() .
                    RSDirectoryHelper::getEntryGeneralPlacehodlersHTML() .
                        
                    '<h4>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CUSTOM_FIELDS_TITLE') . '</h4>' .
                    '<p>' . JText::_('COM_RSDIRECTORY_PLACEHOLDERS_CATEGORY_CUSTOM_FIELDS_DESC') . '</p>' .
                    '<div id="custom-fields-placeholders"></div>';
                        
    echo $this->rsfieldset->getField( JText::_('COM_RSDIRECTORY_PLACEHOLDERS'), $placeholders );
        
    echo $this->rsfieldset->getFieldsetEnd();
    ?>
        
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_('form.token') . "\n"; ?>
        
    </form><!-- #email-message -->
</div><!-- .rsdir -->