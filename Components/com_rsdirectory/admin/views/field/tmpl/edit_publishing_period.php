<?php
/**
 * @package RSDirectory!
 * @copyright (C) 2013-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

// Get items.
if ( is_array($this->field->value) )
{
    $items = array();
        
    foreach ($this->field->value['periods'] as $i => $period)
    {
        $items[] = (object)array(
            'period' => $period,
            'credits' => $this->field->value['credits'][$i],
        );
    }
}
else
{
    @$items = unserialize($this->field->value);
}

?>

<script type="text/javascript">
jQuery( function($)
{
    Joomla.submitbutton = function(task)
    {
        var isValid = document.formvalidator.isValid( document.id('adminForm') );
            
        inputs = $( document.getElementById('publishing-periods') ).find('input[name*="jform[items]"]');
            
        // Validate the values of the publishing periods and credits.
        inputs.each( function(i, elem)
        {
            elem = $(elem);
                
            if ( $.isNumeric( elem.val() ) )
            {
                elem.removeClass('invalid');
                elem.attr('aria-invalid', false);
            }
            else
            {
                isValid = false;
                elem.addClass('invalid');
                elem.attr('aria-invalid', true);
            }
        });
            
        if (task == 'field.cancel' || isValid)
        {
            Joomla.submitform( task, document.getElementById('adminForm') );
        }
    }
        
    // Get the publishing periods tbody element.
    var publishing_periods = $( document.getElementById('publishing-periods') ).find('tbody');
        
    $( document.getElementById('new-publishing-period') ).click(function(e)
    {
        e.preventDefault();
            
        row = '<tr><td><input type="text" name="jform[items][periods][]" /></td><td>' +
              '<input type="text" name="jform[items][credits][]" />' +
              '</td>' + 
              '<td style="vertical-align: middle;">' +
              '<a href="#" class="remove-publishing-period">' +
              '<i class="rsdir-icon-remove"></i>' +
              '</a>' +
              '</td>' +
              '</tr>';
              
        // Add a new row to the publishing periods tbody.
        publishing_periods.append(row);
            
        // Calculate cells width.
        calculate_cells_width(publishing_periods);
    });
        
    publishing_periods.on('click', '.remove-publishing-period', function(e)
    {
        e.preventDefault();
            
        removeRow(this);
    });
        
    if (typeof $.fn.sortable == 'function')
    {
        publishing_periods.sortable(
        {
            axis: 'y',
        });
    }
});
</script>

<table id="publishing-periods" class="adminlist table table-striped" style="width: 34%;">
        
    <thead>
        <tr>
            <th class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::_('COM_RSDIRECTORY_PERIOD_TITLE') ); ?>"><?php echo JText::_('COM_RSDIRECTORY_PERIOD'); ?></th>
            <th class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" title="<?php echo RSDirectoryHelper::getTooltipText( JText::_('COM_RSDIRECTORY_CREDITS_TITLE') ); ?>"><?php echo JText::_('COM_RSDIRECTORY_CREDITS'); ?></th>
            <th width="8%"></th>
        </tr>
    </thead>
        
    <tbody>
    <?php if ($items) { ?>
        <?php foreach ($items as $item) { ?>
            <tr>
                <td>
                    <input type="text" name="jform[items][periods][]" value="<?php echo $this->escape($item->period); ?>" />
                </td>
                <td>
                    <input type="text" name="jform[items][credits][]" value="<?php echo $this->escape($item->credits); ?>" />
                </td>
                <td style="vertical-align: middle;">
                    <a href="#" onclick="removeRow(this);">
                        <i class="rsdir-icon-remove"></i>
                    </a>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
        
</table>

<strong>
    <a id="new-publishing-period" class="<?php echo RSDirectoryHelper::getTooltipClass(); ?>" href="#" title="<?php echo RSDirectoryHelper::getTooltipText( JText::_('COM_RSDIRECTORY_ADD_NEW_PUBLISHING_PERIOD_TITLE') ); ?>">
        <i class="rsdir-icon-new"></i>
        <?php echo JText::_('COM_RSDIRECTORY_ADD_NEW'); ?>
    </a>
</strong>