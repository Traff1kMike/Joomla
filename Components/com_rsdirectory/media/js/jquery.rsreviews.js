;(function($)
{
    $.rsReviews = function(el, options)
	{
        var base = this;
			
        base.$el = $(el);
        base.el = el;
			
        base.$el.data("rsReviews", base);
			
        base.init = function()
		{
            base.options = $.extend({}, $.rsReviews.defaultOptions, options);
				
			base.initShowMore();
        };
			
		// Initialize the "Show	more" button.
		base.initShowMore = function()
		{
			base.loadMore = base.$el.find('.rsdir-load-more');
			base.loadMoreBtn = base.loadMore.find('.btn');
			base.loadMoreLoader = base.loadMore.find('.rsdir-loader');
				
			if (!base.options.entryId)
				return;
				
			base.loadMoreBtn.click(function()
			{
				base.loadMoreBtn.addClass('hide');
				base.loadMoreLoader.removeClass('hide');
					
				var excluded = [];
					
				base.$el.find('.rsdir-review').each(function(index, element)
				{
					excluded.push( $(element).data('review-id') );
				});
					
				data = {
					id: base.options.entryId,
					excluded: excluded,
				};
				data[rsdir.token] = 1;
					
				$.ajax(
				{
					dataType: 'JSON',
					url: rsdir.base + 'index.php?option=com_rsdirectory&task=ratings.showMoreAjax&random=' + Math.random(),
					data: data,
					success: function(json)
					{
						if (json.reviews != undefined)
						{
							reviews = $(json.reviews)
							detail_rating = reviews.find('.rsdir-detail-rating');
								
							if (detail_rating.length > 0 && $.fn.raty)
							{
								detail_rating.raty(
								{
									path: rsdir.root + 'media/com_rsdirectory/images/raty/',
									readOnly: true,
									score: function()
									{
										return $(this).attr('data-rating');
									},
								});        
							}
								
							reviews.hide().insertBefore(base.loadMore).fadeIn(1000);
						}
							
						base.loadMoreLoader.addClass('hide');
							
						if (json.hide == undefined)
						{
							base.loadMoreBtn.removeClass('hide');
						}
						else
						{
							base.loadMoreLoader.remove();
						}
					},
				});
			});
		}
			
        base.init();
    };
		
    $.rsReviews.defaultOptions = {
		entryId: null,
    };
		
    $.fn.rsReviews = function(options)
	{
        return this.each(function()
		{
            (new $.rsReviews(this, options));
        });
    };
		
})(jQuery);