/**
 * Tooltip.
 */
var rs_tooltip = function()
{
    var id = 'rs_tt';
    var top = 3;
    var left = 3;
    var maxw = 400;
    var speed = 10;
    var timer = 20;
    var endalpha = 95;
    var alpha = 0;
    var tt, t, c, b, h;
    var ie = document.all ? true : false;
        
    return {
        show: function(v, w)
        {
            if (tt == null)
            {
                tt = document.createElement('div');
                tt.setAttribute('id', id);
                t = document.createElement('div');
                t.setAttribute('id', id + 'top');
                c = document.createElement('div');
                c.setAttribute('id', id + 'cont');
                b = document.createElement('div');
                b.setAttribute('id', id + 'bot');
                tt.appendChild(t);
                tt.appendChild(c);
                tt.appendChild(b);
                document.body.appendChild(tt);
                tt.style.opacity = 0;
                tt.style.filter = 'alpha(opacity=0)';
                document.onmousemove = this.pos;
            }
                
            tt.style.display = 'block';
            c.innerHTML = document.getElementById(v).innerHTML;
            tt.style.width = w ? w + 'px' : 'auto';
                
            if (!w && ie)
            {
                t.style.display = 'none';
                b.style.display = 'none';
                tt.style.width = tt.offsetWidth;
                t.style.display = 'block';
                b.style.display = 'block';
            }
                
            if (tt.offsetWidth > maxw)
            {
                tt.style.width = maxw + 'px'
            }
                
            h = parseInt(tt.offsetHeight) + top;
            clearInterval(tt.timer);
            tt.timer = setInterval( function(){rs_tooltip.fade(1);}, timer );
        },
        pos: function(e)
        {
            var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
            var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
            tt.style.top = (u - h) + 'px';
            tt.style.left = (l + left) + 'px';
        },
        fade: function(d)
        {
            var a = alpha;
            if ( (a != endalpha && d == 1) || (a != 0 && d == -1) )
            {
                var i = speed;
                if (endalpha - a < speed && d == 1)
                {
                    i = endalpha - a;
                }
                else if (alpha < speed && d == -1)
                {
                    i = a;
                }
                alpha = a + (i * d);
                tt.style.opacity = alpha * .01;
                tt.style.filter = 'alpha(opacity=' + alpha + ')';
            }
            else
            {
                clearInterval(tt.timer);
                
                if (d == -1)
                {
                    tt.style.display = 'none';
                }
            }
        },
        hide: function()
        {
            clearInterval(tt.timer);
            tt.timer = setInterval( function(){rs_tooltip.fade(-1);}, timer );
        }
    };
}();

/**
 * Update owner reply.
 */
window.updateOwnerReply = function(review_id, owner_reply)
{
    // Get the review element.
    review = jQuery('.rsdir-review[data-review-id="' + review_id +'"]');
        
    // Remove the old owner reply.
    review.find('.rsdir-owner-reply').remove();
        
    text = Joomla.JText._('COM_RSDIRECTORY_EDIT_OWNER_REPLY');
        
    if (owner_reply)
    {
        // Add the new owner reply.
        review.find('.rsdir-review-body').after(owner_reply);
    }
    else
    {
        text = Joomla.JText._('COM_RSDIRECTORY_ADD_OWNER_REPLY');
    }
        
    review.find('.rsdir-edit-owner-reply').html(text);
    jQuery( document.getElementById('rsdir-owner-reply-modal') ).modal('toggle');
}

// Proceed if jQuery is defined.
if (typeof jQuery != 'undefined')
{
    jQuery.noConflict();
        
    var total_credits = 0;
    var form_fields = {};
    var last_form_fields = {};
        
    /**
     * Update total credits.
     */
    function updateTotalCreditsCost()
    {
        jQuery( document.getElementById('adminForm') ).find('.rsdir-total-credits').html(total_credits);
    }
        
    /**
     * Update the total credits after the user modified a text input, text area or calendar
     */
    function updateTextCreditsCost(elem, val)
    {
        field = jQuery(elem);
            
        // Get the form field id.
        form_field_id = field.parents('.rsdir-field-wrapper').attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
            
        if (typeof form_fields[form_field_id] == 'undefined')
            return;
            
        form_field = form_fields[form_field_id];
        val = jQuery.trim(val);
            
        if ( val && !form_field.filled_in )
        {
            total_credits += form_field.credits;
            form_field.filled_in = 1;
        }
        else if ( !val && form_field.filled_in )
        {
            total_credits -= form_field.credits;
            form_field.filled_in = 0;
        }
            
        updateTotalCreditsCost();
    }
        
    /**
     * Method to clear form field cost
     */
    function clearFormFieldCost(form_field_id)
    {
        if (typeof form_fields[form_field_id] == 'undefined')
            return;
            
        form_field = form_fields[form_field_id];
            
        if (form_field.filled_in)
        {
            total_credits -= form_field.credits;
            form_field.filled_in = 0;
                
            updateTotalCreditsCost();
        }
    }
        
    function addToFavorites(button)
    {
        data = {
            id: button.attr('data-entry-id'),
        };
        data[rsdir.token] = 1;
            
        jQuery.ajax(
        {
            type: 'POST',
            dataType: 'JSON',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=entry.addToFavoritesAjax&random=' + Math.random(),
            data: data,
            success: function(json)
            {
                if (json.ok != undefined)
                {
                    if ( button.hasClass('rsdir-entry-faved') )
                    {
                        button.removeClass('rsdir-entry-faved');
                        button.find('i').removeClass('icon-star').addClass('icon-star-empty');
                        button.attr( 'title', Joomla.JText._('COM_RSDIRECTORY_ENTRY_ADD_TO_FAVORITES') );
                    }
                    else
                    {
                        button.addClass('rsdir-entry-faved');
                        button.find('i').removeClass('icon-star-empty').addClass('icon-star');
                        button.attr( 'title', Joomla.JText._('COM_RSDIRECTORY_ENTRY_REMOVE_FROM_FAVORITES') );
                    }
                }
                else if (json.error)
                {
                    // Output the error.
                    alert(json.error);
                }
            },
        });
    }
        
    /**
     * Document on ready.
     */
    jQuery(function($)
    {
        /**
         * Set the publishing interval change event.
         */
        $('input.rsdir-publishing-period,select.rsdir-publishing-period').change( function(e)
        {
            field = $(this);
                
            // Get the form field id.
            form_field_id = field.parents('.rsdir-field-wrapper').attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                
            if (typeof form_fields[form_field_id] == 'undefined')
                return;
                
            form_field = form_fields[form_field_id];
                
            // Get the selected value.
            value = field.val();
                
            if (form_field.current_value != value)
            {
                if (form_field.last_value && form_field.current_value != form_field.last_value)
                {
                    total_credits -= form_field.credits[form_field.last_value];
                }
                    
                total_credits += form_field.credits[value];
            }
            else if (form_field.current_value != form_field.last_value)
            {
                total_credits -= form_field.credits[form_field.last_value];
            }
                
            form_field.last_value = value;
                
            updateTotalCreditsCost();
        });
            
        var paid_fields = $('.rsdir-paid-field');
            
        paid_fields.on( 'change', '.rsdir-dropdown', function()
        {
            field = $(this);
                
            // Get the form field id.
            form_field_id = field.parents('.rsdir-field-wrapper').attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                
            if (typeof form_fields[form_field_id] == 'undefined')
                return;
                
            form_field = form_fields[form_field_id];
            val = $.trim( field.val() );
                
            if (val && !form_field.filled_in)
            {
                total_credits += form_field.credits;
                form_field.filled_in = 1;
            }
            else if (!val && form_field.filled_in)
            {
                total_credits -= form_field.credits;
                form_field.filled_in = 0;
            }
                
            updateTotalCreditsCost();
        });
            
        paid_fields.on( 'change', '.rsdir-checkbox,.rsdir-radio,.rsdir-promoted-entry-checkbox', function()
        {
            field = $(this);
                
            // Get the field wrapper element.
            field_wrapper = field.parents('.rsdir-field-wrapper');
                
            // Get the form field id.
            form_field_id = field_wrapper.attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                
            if (typeof form_fields[form_field_id] == 'undefined')
                return;
                
            form_field = form_fields[form_field_id];
                
            // Is at least one checkbox checked?
            checked = field_wrapper.find('input:checked').length;
                
            if (checked && !form_field.filled_in)
            {
                total_credits += form_field.credits;
                form_field.filled_in = 1;
            }
            else if (!checked && form_field.filled_in)
            {
                total_credits -= form_field.credits;
                form_field.filled_in = 0;
            }
                
            updateTotalCreditsCost();
        });
            
        paid_fields.on( 'keyup change', '.rsdir-textbox,.rsdir-textarea,.rsdir-calendar-input', function()
        {
            updateTextCreditsCost( this, $(this).val() );
        });
            
        paid_fields.on( 'change', '.rsdir-year-dropdown,.rsdir-month-dropdown,.rsdir-day-dropdown', function()
        {
            var parent_element = $(this).parents('.rsdir-field-wrapper');
                
            // Get the form field id.
            form_field_id = parent_element.attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                
            if (typeof form_fields[form_field_id] == 'undefined')
                return;
                
            form_field = form_fields[form_field_id];
                
            fields = parent_element.find('select');
                
            length = fields.length;
                
            selected = 0;
                
            $(fields).each(function()
            {
                if ( $(this).val() != 0 )
                {
                   selected++; 
                }
            });
                
            if (length == selected && !form_field.filled_in)
            {
                total_credits += form_field.credits;
                form_field.filled_in = 1;
            }
            else if (length != selected && form_field.filled_in)
            {
                total_credits -= form_field.credits;
                form_field.filled_in = 0;
            }
                
            updateTotalCreditsCost();
        });
            
        paid_fields.on( 'change', 'input[type="file"]', function()
        {
            field = $(this);
                
            if ( field.val() )
            {
                parent_element = field.parents('.rsdir-field-wrapper');
                form_field_id = parent_element.attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                    
                if ( typeof form_fields[form_field_id] == 'undefined' || field.data('charged') )
                    return;
                    
                field.data('charged', 1);
                    
                form_field = form_fields[form_field_id];
                    
                files_count = 0;
                    
                parent_element.find('input[type="file"]').each(function(index, element)
                {
                    if ( jQuery(element).val() )
                    {
                        files_count++
                    }
                });
                    
                if (files_count < 2)
                {
                    total_credits += form_field.credits;
                }
                    
                total_credits += form_field.credits_per_file;
                    
                updateTotalCreditsCost();
            }
        });
            
        $('input.rsdir-publishing-period:checked,select.rsdir-publishing-period option:selected').each(function()
        {
            if ( !$.trim( $(this).val() ) )
                return;
                
            // Get the form field id.
            form_field_id = $(this).parents('.rsdir-field-wrapper').attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                
            form_field = form_fields[form_field_id];
                
            if (form_field.last_value && form_field.last_value != form_field.current_value)
            {
                total_credits += form_field.credits[form_field.last_value];
            }
        });
            
        // Update total credits cost.
        for (i in form_fields)
        {
            form_field = form_fields[i];
                
            if (form_field.filled_in)
            {
                total_credits += form_field.credits;
            }
        }
            
        updateTotalCreditsCost();
            
        listing_rating = $('.rsdir-listing-rating');
            
        if (listing_rating.length > 0 && $.fn.raty)
        {
            listing_rating.raty(
            {
                path: rsdir.root + 'media/com_rsdirectory/images/raty/',
                readOnly: true,
                score: function()
                {
                    return $(this).attr('data-rating');
                }
            });        
        }
            
        detail_rating = $('.rsdir-detail-rating');
            
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
            
        rate_entry = $('.rsdir-rate-entry');
            
        if (rate_entry.length > 0 && $.fn.raty)
        {
            rate_entry.raty(
            {
                path: rsdir.root + 'media/com_rsdirectory/images/raty/',
                scoreName: 'jform[score]',
            });        
        }
            
        // Get the review form.
        review_form = $( document.getElementById('rsdir-review-form') );
            
        /**
         * Set the review form submit event.
         */
        review_form.submit(function(e)
        {
            // Prevent the default action.
            e.preventDefault();
                
            if (submitted)
                return false;
                
            // Get the form object.
            var form = $(this);
                
            var name = form.find('input[name="jform[name]"]');
            var email = form.find('input[name="jform[email]"]');
            var subject = form.find('input[name="jform[subject]"]');
            var review = form.find('textarea[name="jform[review]"]');
            var score = form.find('input[name="jform[score]"]');
                
            // Remove previous errors.
            form.find('.control-group').removeClass('error');
            form.find('.alert-error').remove();
                
            // Initialize the error messages array.
            errors = [];
                
            if ( name.length && !$.trim( name.val() ) )
            {
                name.parent().addClass('error');
                errors.push( Joomla.JText._('COM_RSDIRECTORY_NAME_REQUIRED') );
            }
            
            if ( email.length && !$.trim( email.val() ) )
            {
                email.parent().addClass('error');
                errors.push( Joomla.JText._('COM_RSDIRECTORY_EMAIL_REQUIRED') );
            }
                
            if ( subject.length && !$.trim( subject.val() ) )
            {
                subject.parent().addClass('error');
                errors.push( Joomla.JText._('COM_RSDIRECTORY_SUBJECT_REQUIRED') );
            }
                
            if ( review.length && !$.trim( review.val() ) )
            {
                review.parent().addClass('error');
                errors.push( Joomla.JText._('COM_RSDIRECTORY_REVIEW_REQUIRED') );
            }
                
            if ( score.length && !$.trim( score.val() ) )
            {
                score.parents('.control-group').addClass('error');
                errors.push( Joomla.JText._('COM_RSDIRECTORY_SCORE_REQUIRED') );
            }
                
            if (errors.length)
            {
                form.prepend( '<div class="alert alert-error">' + errors.join("<br />" ) + '</div>');
                return false;
            }
                
            var submitted = true;
                
            // Get the loader image.
            var loader = $( document.getElementById('rsdir-review-loader') );
                
            // Get the submit button.
            var submit = $( document.getElementById('rsdir-review-submit') );
                
            // Show the loader image.
            loader.removeClass('hide');
                
            // Hide the submit button.
            submit.addClass('hide');
                
            $.ajax(
            {
                type: 'POST',
                dataType: 'JSON',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=rating.postReviewAjax&random=' + Math.random(),
                data: form.serializeArray(),
                success: function(json)
                {
                    if (json.ok != undefined)
                    {
                        message = '<div class="alert alert-success">' + json.ok + '</div>';
                            
                        if (json.rating != undefined)
                        {
                            rating = $(json.rating);
                                
                            // Initialize the rating.
                            rating.find('.rsdir-detail-rating').raty(
                            {
                                path: rsdir.root + 'media/com_rsdirectory/images/raty/',
                                readOnly: true,
                                score: function()
                                {
                                    return $(this).attr('data-rating');
                                }
                            });
                                
                            $('.rsdir-entry-rating').replaceWith(rating);
                        }
                            
                        if (json.review != undefined)
                        {
                            review = $(json.review);
                                
                            // Initialize the rating.
                            review.find('.rsdir-detail-rating').raty(
                            {
                                path: rsdir.root + 'media/com_rsdirectory/images/raty/',
                                readOnly: true,
                                score: function()
                                {
                                    return $(this).attr('data-rating');
                                }
                            });
                                
                            $( document.getElementById('no-reviews') ).remove();
                            reviews_list = $( document.getElementById('reviews-list') ).prepend(review).prepend(message);
                            form.remove();
                            $('html, body').animate({
                                scrollTop: $( document.getElementById('reviews') ).offset().top
                            }, 500);
                        }
                        else
                        {
                            // Replace the review form with a success message.
                            form.replaceWith(message);
                        }
                    }
                    else
                    {
                        if (json.error_messages != undefined)
                        {
                            var errors = [];
                                
                            $.each(json.error_messages, function(index, message)
                            {
                                errors.push(message);
                            });
                                
                            form.prepend( '<div class="alert alert-error">' + errors.join("<br />") + '</div>' );
                        }
                            
                        if (json.error_fields != undefined)
                        {
                            $.each(json.error_fields, function(index, field_name)
                            {
                                form.find('[name="jform[' + field_name + ']"]').parents('.control-group').addClass('error');
                            });
                        }
                            
                        submitted = false;
                            
                        // Hide the loader image.
                        loader.addClass('hide');
                            
                        // Show the submit button.
                        submit.removeClass('hide');
                    }
                },
            });
        });
            
        review_form.filter('.rsdir-rating-form').find('.rsdir-rate-entry').on('click', 'img', function()
        {
            review_form.submit();
        });
            
        $('.rsdir-listing-delete').click(function(e)
        {
            e.preventDefault();
                
            if ( !confirm( Joomla.JText._('COM_RSDIRECTORY_ENTRY_DELETION_CONFIRMATION') ) )
                return false;
                
            parent_element = $(this).parents('.rsdir-listing');
                
            data = {
                id: $(this).attr('data-entry-id'),
            };
            data[rsdir.token] = 1;
                
            $.ajax(
            {
                type: 'POST',
                dataType: 'JSON',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=entry.deleteAjax&random=' + Math.random(),
                data: data,
                success: function(json)
                {
                    if (json.ok != undefined)
                    {
                        parent_element.fadeOut(1000, function()
                        {
                            parent_element.remove();
                        });
                    }
                    else if (json.error)
                    {
                        // Output the error.
                        alert(json.error);
                    }
                },
            });
        });
            
        $('.rsdir-entry-fav').click(function(e)
        {
            addToFavorites( $(this) );
        });
            
        // Get the big thumbs wrapper.
        big_thumbs = $( document.getElementById('rsdir-big-thumbs') );
            
        // Get the small thumbs wrapper.
        small_thumbs = $( document.getElementById('rsdir-small-thumbs') );
            
        small_thumbs.find('.thumbnail').click(function()
        {
            index = small_thumbs.find('.thumbnail').index(this);
                
            big_thumbs.find('.thumbnail').addClass('hidden-desktop hidden-tablet');
                
            big_thumbs.find('.thumbnail').eq(index).removeClass('hidden-desktop hidden-tablet');
        });
            
        $( document.getElementById('rsdir-print-entry') ).click(function()
        {  
            url = window.location.href.replace('#', '');
                
            url += ( url.indexOf('?') == -1 ? '?' : '&' ) + 'tmpl=component&print=1';
                
            window.open(
                url,
                'print-entry',
                'width=770,height=800,scrollbars=yes'
            );
        });
            
        $( document.getElementById('rsdir-detail-delete-entry') ).click(function(e)
        {
            e.preventDefault();
                
            if ( !confirm( Joomla.JText._('COM_RSDIRECTORY_ENTRY_DELETION_CONFIRMATION') ) )
                return false;
                
            parent = $(this).parents('.rsdir-listing');
                
            data = {
                id: $(this).attr('data-entry-id'),
            };
            data[rsdir.token] = 1;
                
            $.ajax(
            {
                type: 'POST',
                dataType: 'JSON',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=entry.deleteAjax&message=1&random=' + Math.random(),
                data: data,
                success: function(json)
                {
                    if (json.ok != undefined)
                    {
                        $('.item-page ').replaceWith('<div class="alert alert-success">' + json.message + '</div>');
                    }
                    else if (json.error)
                    {
                        // Output the error.
                        alert(json.error);
                    }
                },
            });
        });
            
        $('.media-more').click(function(e)
        {
            // Prevent default action.
            e.preventDefault();
                
            // Get the parent element.
            parent_element = $(this).parent('.media-group-wrapper');
                
            // Hide this button.
            $(this).addClass('hide');
                
            // Show the "less" button.
            parent_element.find('> .media-less').removeClass('hide');
                
            // Show the hidden elements.
            parent_element.find('> .media-group').removeClass('hide');
        });
            
        $('.media-less').click(function(e)
        {
            // Prevent default action.
            e.preventDefault();
                
            // Get the parent element.
            parent_element = $(this).parent('.media-group-wrapper');
                
            // Hide this button.
            $(this).addClass('hide');
                
            // Show the "more" button.
            parent_element.find('> .media-more').removeClass('hide');
                
            // Hide the media group.
            parent_element.find('> .media-group').addClass('hide');
        });
            
        $('.rsdir-filter').on( 'click', '.rsdir-filter-more', function(e)
        {
            e.preventDefault();
                
            btn = $(this);
            parent_element = btn.parents('.rsdir-filter');
            toggle = parent_element.find('.rsdir-filter-toggle');
                
            if ( toggle.hasClass('hide') )
            {
                toggle.removeClass('hide');
                btn.html( Joomla.JText._('COM_RSDIRECTORY_SHOW_LESS') );
            }
            else
            {
                toggle.addClass('hide');
                btn.html( Joomla.JText._('COM_RSDIRECTORY_SHOW_MORE') );
            }
        });
            
        $('.rsdir-iframe-modal').find('.btn-primary').click(function()
        {
            $(this).parents('.rsdir-iframe-modal').find('iframe').contents().find('form').submit();
        });
            
        $( document.getElementById('reviews-list') ).on( 'click', '.rsdir-edit-owner-reply', function()
        {
            btn = $(this);
            modal = $( document.getElementById('rsdir-owner-reply-modal') );
                
            $( document.getElementById('rsdir-owner-reply-header') ).html( btn.html() );
            modal.find('iframe').attr( 'src', btn.data('iframe-src') );
                
            modal.modal();
        });
            
        $( document.getElementById('rsdir-check-credits') ).on( 'click.checkCredits', function(e)
        {
            e.preventDefault();
                
            var btn = $(this);
            var form = btn.parents('form');
            var loader = form.find('.rsdir-form-loader');
                
            loader.removeClass('hide');
                
            data = {
                cost: total_credits,
            };
            data[rsdir.token] = 1;
                
            $.ajax(
            {
                type: 'POST',
                dataType: 'JSON',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=entry.checkCreditsAjax&random=' + Math.random(),
                data: data,
                success: function(json)
                {
                    loader.addClass('hide');
                        
                    if (json.ok == 0)
                    {
                        form.find('.insufficient-credits').removeClass('hide');
                    }
                    else
                    {
                        btn.unbind('click.checkCredits');
                            
                        confirm_label = $( document.getElementById('finalize-confirm-label') )
                            
                        if (total_credits > 0 && confirm_label.length > 0)
                        {
                            confirm_label.removeClass('hide');
                        }
                        else
                        {
                            Joomla.submitbutton('entry.save');
                        }
                    }
                },
            });
                
            btn.unbind('click.checkCredits').click(function()
            {
                Joomla.submitbutton('entry.save');
            });
        });
            
        var buyCreditsForm = $( document.getElementById('buyCreditsForm') );
            
        buyCreditsForm.find(':radio').on( 'change', function()
        {
            credit_package = buyCreditsForm.find(':radio[name="jform[credit_package]"]:checked');
            payment_method = buyCreditsForm.find(':radio[name="jform[payment_method]"]:checked');
            entry_id = buyCreditsForm.find(':input[name="jform[entry_id]"]');
                
            if (credit_package.length > 0 && payment_method.length > 0)
            {
                data = {
                    credit_package: credit_package.val(),
                    payment_method: payment_method.val(),
                    entry_id: entry_id.val(),
                };
                data[rsdir.token] = 1;
                    
                $.ajax(
                {
                    type: 'POST',
                    dataType: 'JSON',
                    url: rsdir.base + 'index.php?option=com_rsdirectory&task=credits.calculatePriceAjax&random=' + Math.random(),
                    data: data,
                    success: function(json)
                    {
                        buyCreditsForm.find('.buy-credits-price').html(json.price);
                        buyCreditsForm.find('.buy-credits-tax').html(json.tax);
                        buyCreditsForm.find('.buy-credits-total').html(json.total);
                    },
                });
            }
        });
            
        $( document.getElementById('finalize-confirm') ).on( 'change', function()
        {
            $( document.getElementById('finalize-confirm-btn') ).attr( 'disabled', !$(this).prop('checked') );
        });
            
        buyCreditsForm.find(':radio[name="jform[credit_package]"]').change(function()
        {
            buyCreditsForm.find('.rsdir-credit-package.rsdir-selected').removeClass('rsdir-selected');
            $(this).parents('.rsdir-credit-package').addClass('rsdir-selected');
        });
            
        $('.rsdir-filter.dependency-parent').on( 'change', ':input', function()
        {
            var field = $(this);
            var dependency_parent = field.parents('.dependency-parent');
            var loader = field.parents('.control-group').find('.rsdir-loader');
                
            // Initialize the options object.
            var options = {};
                
            field.parents('form').find('.options').each(function(index, element)
            {
                options[$(element).attr('name')] = $(element).val();
            });
                
            data = {
                parent_id: dependency_parent.data('id'),
                value: field.val(),
                filters: 1,
                options: options,
            };
            data[rsdir.token] = 1;
                
            // Show the loader.
            loader.removeClass('hide');
                
            // Send the AJAX request.
            $.ajax(
            {
                type: 'POST',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=field.getDependencyOptionsAjax&random=' + Math.random(),
                data: data,
                dataType: 'json',
                success: function(response)
                {
                    $.each(response, function(field_id, field_data)
                    {
                        if (typeof clearFormFieldCost != 'undefined')
                        {
                            clearFormFieldCost(field_id);   
                        }
                            
                        if (field_data.items_group != undefined)
                        {
                            // Get the child elements.
                            items_group = $('.rsdir-filter-' + field_id).find('.rsdir-items-group');
                                
                            // Add the new items.
                            items_group.html(field_data.items_group);
                        }
                        else if (field_data.options != undefined)
                        {
                            // Get child element.
                            element = $( document.getElementById('fields' + field_id) );
                                
                            // Remove all options but the 1st.
                            element.find('*:gt(0)').remove();
                                
                            // Append the new options.
                            element.append(field_data.options);
                                
                            // Trigger chosen update.
                            element.trigger('liszt:updated');
                        }
                    });
                        
                    // Show the loader.
                    loader.addClass('hide');
                }
            });
        });
         
    }); // On ready.
}