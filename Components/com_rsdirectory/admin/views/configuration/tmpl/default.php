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
        if ( task == 'configuration.cancel' || document.formvalidator.isValid( document.id('configuration') ) )
        {
            Joomla.submitform( task, document.getElementById('configuration') );
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
            <form id="configuration" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=configuration'); ?>" method="post">
                    
                <?php
                // GENERAL.
                $this->rstabs->addTitle('COM_RSDIRECTORY_GENERAL', 'general');
                $this->rstabs->addContent( $this->loadTemplate('general') );
                    
                // EMAILS.
                $this->rstabs->addTitle('COM_RSDIRECTORY_EMAILS', 'emails');
                $this->rstabs->addContent( $this->loadTemplate('emails') );
                    
                // PAYMENTS.
                $this->rstabs->addTitle('COM_RSDIRECTORY_PAYMENTS', 'payments');
                $this->rstabs->addContent( $this->loadTemplate('payments') );
                    
                // IMAGES.
                $this->rstabs->addTitle('COM_RSDIRECTORY_IMAGES', 'images');
                $this->rstabs->addContent( $this->loadTemplate('images') );
                    
                // CAPTCHA.
                $this->rstabs->addTitle('COM_RSDIRECTORY_CAPTCHA', 'captcha');
                $this->rstabs->addContent( $this->loadTemplate('captcha') );
                    
                // COMMENTS.
                $this->rstabs->addTitle('COM_RSDIRECTORY_COMMENTS', 'comments');
                $this->rstabs->addContent( $this->loadTemplate('comments') );
                    
                // RATINGS.
                $this->rstabs->addTitle('COM_RSDIRECTORY_RATINGS', 'ratings');
                $this->rstabs->addContent( $this->loadTemplate('ratings') );
                    
                // REPORTING.
                $this->rstabs->addTitle('COM_RSDIRECTORY_REPORTING', 'reporting');
                $this->rstabs->addContent( $this->loadTemplate('reporting') );
                    
                // UPDATE.
                $this->rstabs->addTitle('COM_RSDIRECTORY_UPDATE', 'update');
                $this->rstabs->addContent( $this->loadTemplate('update') );
                    
                // Render the tabs.
                $this->rstabs->render();
                ?>
                    
                <input type="hidden" name="task" value="" />
                <?php echo JHTML::_('form.token') . "\n"; ?>
                    
            </form><!-- #configuration -->
        <?php if ($this->isJ30) { ?>
        </div><!-- .span10 -->
        <?php } ?>
    </div><!-- .row-fluid -->
</div><!-- .rsdir -->