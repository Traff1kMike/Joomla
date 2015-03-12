// Proceed if jQuery is defined.
if (typeof jQuery != 'undefined')
{
    jQuery.noConflict();
        
    jQuery(function($)
    {
        $('.rsdir-range-filter .icon-search').click(function()
        {
            $(this).parent().find('.rsdir-custom-range').attr('checked', true);
            $(this).parents('form').submit();
        });
            
        $('.rsdir-filter-form').submit(function()
        {
            range_filters = $('.rsdir-range-filter');
                
            var submit = true;
                
            if (range_filters.length > 0)
            {
                $(range_filters).each(function(index, element)
                {
                    if ( $(element).find('.rsdir-custom-range').is(':checked') )
                    {
                        from = parseInt( $(element).find('.rsdir-range-from').val() ); 
                        to = parseInt( $(element).find('.rsdir-range-to').val() );
                            
                        if ( (!from && !to) || (from && to && from > to) )
                        {
                            element.addClass('error');
                            submit = false;
                        } 
                    }
                });
            }
                
            return submit;
        });
    });
}