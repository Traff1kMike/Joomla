jQuery.noConflict();

jQuery(function($)
{
	if ( $('.rsdir-top-rated-entry-rating').length > 0 && $.fn.raty )
	{
		$('.rsdir-top-rated-entry-rating').raty(
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
