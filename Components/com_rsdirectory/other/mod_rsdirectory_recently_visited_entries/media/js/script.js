jQuery.noConflict();

jQuery(function($)
{
	if ( $('.rsdir-recently-visited-entry-rating').length > 0 && $.fn.raty )
	{
		$('.rsdir-recently-visited-entry-rating').raty(
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
