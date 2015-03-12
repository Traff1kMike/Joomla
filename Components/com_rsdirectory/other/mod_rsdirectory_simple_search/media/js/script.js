// Proceed if jQuery is defined.
if (typeof jQuery != 'undefined')
{
    jQuery.noConflict();
        
    jQuery(function($)
    {
        form = $('.rsdir-mod-simple-search');
            
        form.find('.dropdown-menu a').click(function()
        {
            btn = $(this);
                
            parent_element = btn.parents('.btn-group');
                
            parent_element.find('.dropdown-toggle').html( btn.data('text') + ' <span class="caret"></span>' );
            parent_element.find('input[name="categories[]"]').val( btn.data('value') );
        });
    });
}