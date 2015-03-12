<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// No direct access.
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

if ( $app->isSite() )
{
    JSession::checkToken('get') or die( JText::_('JINVALID_TOKEN') );
}

$function = $app->input->getCmd('function', 'jSelectEntry');

?>

<script type="text/javascript">
jQuery(function($)
{
    $( document.getElementById('adminForm') ).submit(function(e)
    {
        // Prevent default action.
        e.preventDefault();
            
        window.parent.newEntry( $('input[name="fields[category_id]"]').val() );
    });
        
    $( document.getElementById('new-category') ).click(function()
    {
        window.parent.location = $(this).attr('href');
    });
});
</script>

<div class="rsdir">
    <form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_rsdirectory&view=categories&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>" method="post">
        
    <h1><?php echo JText::_('COM_RSDIRECTORY_ADD_ENTRY'); ?></h1>
        
    <?php if ($this->categories) { ?>
        
    <p><?php echo JText::_('COM_RSDIRECTORY_ADD_ENTRY_CATEGORY_SELECT_DESC'); ?></p>
        
    <div class="control-group">
        <?php echo $this->categories_select; ?>
    </div>
        
    <input type="hidden" name="fields[category_id]" value="0" />
        
    <button id="rsdir-submit-category" class="btn btn-primary btn" type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
    <a id="rsdir-start-over" class="btn btn-link" href="javascript: void(0);"><?php echo JText::_('COM_RSDIRECTORY_START_OVER'); ?></a>
        
    <?php } else { ?>
        
    <div class="alert alert-block"><?php echo JText::sprintf( 'COM_RSDIRECTORY_NO_CATEGORY_CONFIGURED' , JRoute::_('index.php?option=com_rsdirectory&view=categories') ); ?></div>
        
    <?php } ?>
        
    </form>
</div>