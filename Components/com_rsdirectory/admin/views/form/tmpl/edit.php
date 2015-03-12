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
        if ( task == 'form.cancel' || document.formvalidator.isValid( document.id('form') ) )
        {
            Joomla.submitform( task, document.getElementById('form') );
        }
    }
        
    jQuery(function($)
    {
        var entry_titles_placeholders = $( document.getElementById('entry-titles-placeholders') ).parents('.control-group');
            
        var layouts = $( document.getElementById('layouts') );
            
        var hide_entry_titles_placeholders = true;
            
        layouts.find('.use-template-checkbox input:checked').each(function(index, element)
        {
            if (element.value == 1)
            {
                hide_entry_titles_placeholders = false;
            }
            else
            {
                $(element).parents('.control-group').next().addClass('rsdir-hide');
            }
        });
            
        if (hide_entry_titles_placeholders)
        {
            entry_titles_placeholders.addClass('rsdir-hide');
        }
            
        layouts.find('.use-template-checkbox input').click(function()
        {
            if (this.value == 1)
            {
                $(this).parents('.control-group').next().removeClass('rsdir-hide');
                entry_titles_placeholders.removeClass('rsdir-hide');
            }
            else
            {
                $(this).parents('.control-group').next().addClass('rsdir-hide');
                    
                if ( layouts.find('.use-template-checkbox input:checked[value=1]').length == 0 )
                {
                    entry_titles_placeholders.addClass('rsdir-hide');
                }
            }
        });
            
        listing_row_show_thumbnails = layouts.find('.listing-row-show-thumbnails input');
        listing_row_show_images_number = layouts.find('.listing-row-show-images-number input');
            
        if ( listing_row_show_thumbnails.filter(':checked').val() == 0 )
        {
            listing_row_show_images_number.parents('.control-group').addClass('rsdir-hide');
        }
            
        listing_row_show_thumbnails.click(function()
        {
            if ( $(this).val() == 0 )
            {
                listing_row_show_images_number.parents('.control-group').addClass('rsdir-hide');
            }
            else
            {
                listing_row_show_images_number.parents('.control-group').removeClass('rsdir-hide');
            }
        });
    });
</script>

<div class="rsdir">
    <div class="row-fluid">
        <form id="form" class="form-validate" action="<?php echo JRoute::_( 'index.php?option=com_rsdirectory&view=form&layout=edit&id=' . ( (int)$this->id ) . ($this->category_id ? "&category_id=$this->category_id" : '' )); ?>" method="post">
                
            <?php
            // GENERAL.
            $this->rstabs->addTitle('COM_RSDIRECTORY_GENERAL', 'general');
            $this->rstabs->addContent( $this->loadTemplate('general') );
                
            // FIELDS.
            $this->rstabs->addTitle('COM_RSDIRECTORY_FORM_FIELDS', 'form-fields');
            $this->rstabs->addContent( $this->loadTemplate('form_fields') );
                
            // LAYOUTS.
            $this->rstabs->addTitle('COM_RSDIRECTORY_LAYOUTS', 'layouts');
            $this->rstabs->addContent( $this->loadTemplate('layouts') );
                
            // MESSAGES.
            $this->rstabs->addTitle('COM_RSDIRECTORY_MESSAGES', 'messages');
            $this->rstabs->addContent( $this->loadTemplate('messages') );
                
            // CONTACT.
            $this->rstabs->addTitle('COM_RSDIRECTORY_CONTACT', 'contact');
            $this->rstabs->addContent( $this->loadTemplate('contact') );
                
            // Render the tabs.
            $this->rstabs->render();
            ?>
                
            <input type="hidden" name="task" value="" />
            <?php echo JHTML::_('form.token') . "\n"; ?>
                
        </form><!-- #form -->
    </div><!-- .row-fluid -->
</div><!-- .rsdir -->