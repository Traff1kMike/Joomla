jQuery.noConflict();

jQuery(function($)
{
	// MooTools slider conflict fix.
	$('.rsdir .carousel').each(function(index, element)
	{
		$(this)[index].slide = null;
	});
		
	if ( $('.carousel-entry-rating').length > 0 && $.fn.raty )
	{
		$('.carousel-entry-rating').raty(
		{
			path: rsdir.root + 'media/com_rsdirectory/images/raty/',
			readOnly: true,
			score: function()
			{
				return $(this).attr('data-rating');
			}
		});        
	}
});
