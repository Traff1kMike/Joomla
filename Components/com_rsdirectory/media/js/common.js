jQuery.noConflict();

/*
* $Id: base64.js,v 2.12 2013/05/06 07:54:20 dankogai Exp dankogai $
*
* Licensed under the MIT license.
* http://opensource.org/licenses/mit-license
*
* References:
* http://en.wikipedia.org/wiki/Base64
*/
(function(global) {
    'use strict';
    if (global.Base64) return;
    var version = "2.1.2";
    // if node.js, we use Buffer
    var buffer;
    if (typeof module !== 'undefined' && module.exports) {
        buffer = require('buffer').Buffer;
    }
    // constants
    var b64chars
        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    var b64tab = function(bin) {
        var t = {};
        for (var i = 0, l = bin.length; i < l; i++) t[bin.charAt(i)] = i;
        return t;
    }(b64chars);
    var fromCharCode = String.fromCharCode;
    // encoder stuff
    var cb_utob = function(c) {
        if (c.length < 2) {
            var cc = c.charCodeAt(0);
            return cc < 0x80 ? c
                : cc < 0x800 ? (fromCharCode(0xc0 | (cc >>> 6))
                                + fromCharCode(0x80 | (cc & 0x3f)))
                : (fromCharCode(0xe0 | ((cc >>> 12) & 0x0f))
                   + fromCharCode(0x80 | ((cc >>> 6) & 0x3f))
                   + fromCharCode(0x80 | ( cc & 0x3f)));
        } else {
            var cc = 0x10000
                + (c.charCodeAt(0) - 0xD800) * 0x400
                + (c.charCodeAt(1) - 0xDC00);
            return (fromCharCode(0xf0 | ((cc >>> 18) & 0x07))
                    + fromCharCode(0x80 | ((cc >>> 12) & 0x3f))
                    + fromCharCode(0x80 | ((cc >>> 6) & 0x3f))
                    + fromCharCode(0x80 | ( cc & 0x3f)));
        }
    };
    var re_utob = /[\uD800-\uDBFF][\uDC00-\uDFFFF]|[^\x00-\x7F]/g;
    var utob = function(u) {
        return u.replace(re_utob, cb_utob);
    };
    var cb_encode = function(ccc) {
        var padlen = [0, 2, 1][ccc.length % 3],
        ord = ccc.charCodeAt(0) << 16
            | ((ccc.length > 1 ? ccc.charCodeAt(1) : 0) << 8)
            | ((ccc.length > 2 ? ccc.charCodeAt(2) : 0)),
        chars = [
            b64chars.charAt( ord >>> 18),
            b64chars.charAt((ord >>> 12) & 63),
            padlen >= 2 ? '=' : b64chars.charAt((ord >>> 6) & 63),
            padlen >= 1 ? '=' : b64chars.charAt(ord & 63)
        ];
        return chars.join('');
    };
    var btoa = global.btoa || function(b) {
        return b.replace(/[\s\S]{1,3}/g, cb_encode);
    };
    var _encode = buffer
        ? function (u) { return (new buffer(u)).toString('base64') }
    : function (u) { return btoa(utob(u)) }
    ;
    var encode = function(u, urisafe) {
        return !urisafe
            ? _encode(u)
            : _encode(u).replace(/[+\/]/g, function(m0) {
                return m0 == '+' ? '-' : '_';
            }).replace(/=/g, '');
    };
    var encodeURI = function(u) { return encode(u, true) };
    // decoder stuff
    var re_btou = new RegExp([
        '[\xC0-\xDF][\x80-\xBF]',
        '[\xE0-\xEF][\x80-\xBF]{2}',
        '[\xF0-\xF7][\x80-\xBF]{3}'
    ].join('|'), 'g');
    var cb_btou = function(cccc) {
        switch(cccc.length) {
        case 4:
            var cp = ((0x07 & cccc.charCodeAt(0)) << 18)
                | ((0x3f & cccc.charCodeAt(1)) << 12)
                | ((0x3f & cccc.charCodeAt(2)) << 6)
                | (0x3f & cccc.charCodeAt(3)),
            offset = cp - 0x10000;
            return (fromCharCode((offset >>> 10) + 0xD800)
                    + fromCharCode((offset & 0x3FF) + 0xDC00));
        case 3:
            return fromCharCode(
                ((0x0f & cccc.charCodeAt(0)) << 12)
                    | ((0x3f & cccc.charCodeAt(1)) << 6)
                    | (0x3f & cccc.charCodeAt(2))
            );
        default:
            return fromCharCode(
                ((0x1f & cccc.charCodeAt(0)) << 6)
                    | (0x3f & cccc.charCodeAt(1))
            );
        }
    };
    var btou = function(b) {
        return b.replace(re_btou, cb_btou);
    };
    var cb_decode = function(cccc) {
        var len = cccc.length,
        padlen = len % 4,
        n = (len > 0 ? b64tab[cccc.charAt(0)] << 18 : 0)
            | (len > 1 ? b64tab[cccc.charAt(1)] << 12 : 0)
            | (len > 2 ? b64tab[cccc.charAt(2)] << 6 : 0)
            | (len > 3 ? b64tab[cccc.charAt(3)] : 0),
        chars = [
            fromCharCode( n >>> 16),
            fromCharCode((n >>> 8) & 0xff),
            fromCharCode( n & 0xff)
        ];
        chars.length -= [0, 0, 2, 1][padlen];
        return chars.join('');
    };
    var atob = global.atob || function(a){
        return a.replace(/[\s\S]{1,4}/g, cb_decode);
    };
    var _decode = buffer
        ? function(a) { return (new buffer(a, 'base64')).toString() }
    : function(a) { return btou(atob(a)) };
    var decode = function(a){
        return _decode(
            a.replace(/[-_]/g, function(m0) { return m0 == '-' ? '+' : '/' })
                .replace(/[^A-Za-z0-9\+\/]/g, '')
        );
    };
    // export Base64
    global.Base64 = {
        VERSION: version,
        atob: atob,
        btoa: btoa,
        fromBase64: decode,
        toBase64: encode,
        utob: utob,
        encode: encode,
        encodeURI: encodeURI,
        btou: btou,
        decode: decode
    };
    // if ES5 is available, make Base64.extendString() available
    if (typeof Object.defineProperty === 'function') {
        var noEnum = function(v){
            return {value:v,enumerable:false,writable:true,configurable:true};
        };
        global.Base64.extendString = function () {
            Object.defineProperty(
                String.prototype, 'fromBase64', noEnum(function () {
                    return decode(this)
                }));
            Object.defineProperty(
                String.prototype, 'toBase64', noEnum(function (urisafe) {
                    return encode(this, urisafe)
                }));
            Object.defineProperty(
                String.prototype, 'toBase64URI', noEnum(function () {
                    return encode(this, true)
                }));
        };
    }
    // that's it!
})(this);

/**
 * Reset form element.
 */
function resetFormElement(e)
{
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}

/**
 * Day select event function.
 */ 
function calendarSelect(dateText, inst)
{
    jQuery( jQuery(this).datepicker('option', 'altField') ).trigger('change');
}

/**
 * Escape HTML.
 */ 
function escapeHTML(str)
{
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/**
 * Calculate the cells width for a table.
 */
function calculate_cells_width(table)
{    
    // Proceed only if the table is visible.
    if ( table.is(':visible') )
    {
        table.find('td').each(function(index, element)
        {
            jQuery(element).css( 'width', jQuery(element).width() );
        });
    }
}

/**
 * Category select change event.
 */
function categorySelect(elem)
{
    var select = jQuery(elem);
        
    index = jQuery('.rsdir-category-select').index(elem);
        
    // Remove subcategories.
    jQuery('.rsdir-category-select:gt(' + index + ')').parent().remove();
        
    category_id = select.val();
        
    if ( select.find(':selected').data('children') == 1 )
    {
        jQuery( document.getElementById('rsdir-submit-category') ).attr('disabled', true);
        
        var loader = select.parent().find('.rsdir-loader');
            
        // Show the loader.
        loader.removeClass('hide');
            
        data = {
            category_id: category_id,
        };
        data[rsdir.token] = 1;
            
        // Send the AJAX request.
        jQuery.ajax(
        {
            type: 'POST',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=categories.getCategoriesSelectAjax&random=' + Math.random(),
            data: data,
            success: function(response)
            {
                elem = jQuery(response);
                    
                elem.find('select').change(function()
                {
                    categorySelect(this);
                });
                    
                select.parent().after(elem);
                loader.addClass('hide');
            }
        });
    }
    else
    {
        jQuery( document.getElementById('rsdir-submit-category') ).attr('disabled', category_id == 0);
            
        jQuery('input[name="fields[category_id]"]').val(category_id);
    }
}

/**
 * Clear file upload.
 */
function clearUpload(elem)
{
    // Get the file input wrapper.
    parent_element = jQuery(elem).parent();
        
    // Get the file input.
    field = parent_element.find('input[type="file"]');
        
    field.data('charged', 0);
        
    // Clone the file input wrapper to clear the selected file.
    file_upload = parent_element.clone(true, true);
        
    // Hide the file input wrapper.
    file_upload.addClass('hide');
        
    // Clear the selected file.
    resetFormElement(file_upload);
        
    // Add the new cloned file input wrapper after the old file input wrapper.
    parent_element.after(file_upload);
        
    // Get the field wrapper.
    field_wrapper = parent_element.parents('.rsdir-field-wrapper');
        
    // Show the "Add file upload" button.
    field_wrapper.find('.rsdir-add-file-upload').removeClass('hidden');
        
    // Get the form field id.
    form_field_id = field_wrapper.attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
        
    // Remove the old file input wrapper.
    parent_element.remove();
        
    if ( !field.val() || typeof form_fields[form_field_id] == 'undefined' )
        return;
        
    // Get the form field data.
    form_field = form_fields[form_field_id];
        
    // Calculate the files count.
    files_count = 0;
        
    field_wrapper.find('input[type="file"]').each(function(index, element)
    {
        if ( jQuery(element).val() )
        {
            files_count++
        }
    });
        
    // Update the total credits cost.
    if (!files_count)
    {
        total_credits -= form_field.credits;
    }
        
    total_credits -= form_field.credits_per_file;
        
    updateTotalCreditsCost();
}

/**
 * Create a check all functionality.
 */
function initCheckAll(main, checkboxes)
{
    main = jQuery(main);
    checkboxes = jQuery(checkboxes);
        
    main.on('click', function()
    {
        checkboxes.prop( 'checked', main.prop('checked') );
    });
        
    checkboxes.on('click', function()
    {
        main.prop( 'checked', checkboxes.filter(':checked').length == checkboxes.length );
    });
}

/**
 * Method to update the value of an object from an array.
 */
function updateValue(list, name, value, add)
{
    for (var i in list)
    {
        if (list[i].name == name)
        {
            list[i].value = value;
            return;
        }
    }
        
    add = typeof add !== 'undefined' ? add : true;
        
    // Add the object to the list if it was not found.
    if (add)
    {
        list.push({
            name: name,
            value: value,
        });
    }
}

/**
 * Document ready.
 */
jQuery(function($)
{
    var captcha_refresh_timeout;
        
    /**
     * Set the captcha refresh click event.
     */
    $( document.getElementById('rsdir-captcha-refresh') ).click(function()
    {
        clearTimeout(captcha_refresh_timeout);
            
        // Get the refresh button element.
        var button = $(this);
            
        // Get the image element.
        var captcha = $( document.getElementById('rsdir-captcha') );
            
        // Get the loader element.
        var loader = $( document.getElementById('rsdir-captcha-loader') );
            
        // Hide the refresh button.
        button.addClass('rsdir-hide');
            
        // Show the loader.
        loader.removeClass('rsdir-hide');
            
        // Create a timer.
        captcha_refresh_timeout = setTimeout(function()
        {
            // Reset the value entered in the input field.
            $( document.getElementById('rsdir-captcha-input') ).attr('value', '');
                
            // Get the image src and remove the random parameter.
            src = captcha.attr('src').replace(/&random=\d+/, '');
                
            // Set a new image src using a new random parameter.
            captcha.attr( 'src', src + '&random=' + Math.random() );
                
            // Fire after the image has finished loading.
            captcha.load(function()
            {
                // Show the refresh button.
                button.removeClass('rsdir-hide');
                    
                // Hide the loader.
                loader.addClass('rsdir-hide');
            });
        }, 250);
    });
        
    $('input.rsdir-submit-form').click(function()
    {
        $(this).parents('form').submit();
    });
        
    $('select.rsdir-submit-form').change(function()
    {
        $(this).parents('form').submit();
    });
        
    /**
     * Sortable files list.
     */
    $('.rsdir-order-files').sortable(
    {
        helper : 'clone',
        update: function(event, ui)
        {
            var cids = [];
                
            var files = $(this);
                
            files.find('.rsdir-file-pk').each(function(index, element)
            {
                cids.push( $(element).val() );
            });
                
            data = {
                cids: cids,
            };
            data[rsdir.token] = 1;
                
            $.ajax(
            {
                type: 'POST',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=field.orderFilesAjax&random=' + Math.random(),
                data: data,
            });
        },
        create: function(event, ui)
        {
            element = $(this);
                
            if ( element.is('tbody') )
            {
                calculate_cells_width(element);
                    
                // The files list items can be dragged only vertically.
                element.sortable('option', 'axis', 'y');
            }
        },
    });
        
    /**
     * Add file upload.
     */
    $('.rsdir-add-file-upload').click(function()
    {
        btn = $(this);
            
        file_upload = btn.prev('.rsdir-file-upload');
        
        if ( file_upload.hasClass('hide') )
        {
            file_upload.removeClass('hide');
        }
        else
        {
            file_upload = file_upload.clone(true, true);
            file_upload.removeClass('hide');
            file_upload.find('input[type="file"]').data('charged', null);
                
            resetFormElement(file_upload);
                
            max_files_number = btn.data('max-files-number');
                
            btn.before(file_upload);
        }
            
        parent_element = btn.parents('.rsdir-field-wrapper');
            
        // Get the form field id.
        form_field_id = parent_element.attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
            
        max_files_number = rsdir.files_limit[form_field_id];
            
        current_files_number = parent_element.find('.rsdir-file-upload:not(.hide)').length + parent_element.find('.rsdir-file').length;
            
        if ( max_files_number != 0 && current_files_number >= max_files_number )
        {
            btn.addClass('hidden');
        }
    });
        
    /**
     * Delete file.
     */
    $('.rsdir-delete-file').click(function()
    {
        if ( !confirm( Joomla.JText._('COM_RSDIRECTORY_FILE_DELETION_CONFIRMATION') ) )
            return;
            
        var btn = $(this);
        var parent_element = btn.parents('.rsdir-field-wrapper');
          
        var file = btn.parents('.rsdir-file');
        var files = btn.parents('.rsdir-files');
            
        // Hide the delete button.
        btn.addClass('hide');
            
        // Display the loader.
        file.find('.rsdir-image-loader').removeClass('hide');
            
        data = {
            cid: file.find('.rsdir-file-pk').val(),
        };
        data[rsdir.token] = 1;
            
        // Send the AJAX request.
        $.ajax(
        {
            type: 'POST',
            dataType: 'JSON',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=field.deleteFileAjax&random=' + Math.random(),
            data: data,
            success: function(json)
            {
                if (json.ok != undefined)
                {
                    // Remove the parent li.
                    file.remove();
                        
                    // Remove the parent ul if there are no list items left.
                    if ( !files.find('.rsdir-file').length )
                    {
                        files.remove();
                    }
                        
                    // Get the form field id.
                    form_field_id = parent_element.attr('class').match(/rsdir-field-wrapper-(\d+)/)[1];
                        
                    max_files_number = rsdir.files_limit[form_field_id];
                        
                    current_files_number = parent_element.find('.rsdir-file-upload').length + parent_element.find('.rsdir-file').length;
                        
                    parent_element.find('.rsdir-file-upload').removeClass('hide');
                        
                    if (max_files_number > current_files_number)
                    {
                        parent_element.find('.rsdir-add-file-upload').removeClass('hidden');
                    }
                }
                else if (json.errors != undefined)
                {
                    alert(json.errors);
                        
                    // Unhide delete button (on hover).
                    btn.removeClass('hide');
                        
                    // Hide the loader.
                    file.find('.rsdir-image-loader').addClass('hide');
                }
            }
        });
    });
        
    $('.rsdir-field-wrapper.dependency-parent').on( 'change', ':input', function()
    {
        var dependency_parent = $(this).parents('.dependency-parent');
        var loader = $(this).parents('.control-group').find('.rsdir-loader');
            
        data = {
            parent_id: dependency_parent.data('id'),
            value: $(this).val(),
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
                        items_group = $('.rsdir-field-wrapper-' + field_id).find('.rsdir-items-group');
                            
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
        
    $('.rsdir-toggle-help').popover();
        
    $('.rsdir-category-select').change(function()
    {
        categorySelect(this);
    });
        
    $( document.getElementById('rsdir-start-over') ).click(function()
    {
        jQuery('.rsdir-category-select:gt(0)').parent().remove();
        jQuery('.rsdir-category-select').val(0);
    });
        
    $('.rsdir-clear-upload').click(function()
    {
        clearUpload(this);
    });
        
    var category = $( document.getElementById('fields_category_id') );
    var category_change = $( document.getElementById('fields_category_id_change') );
    var category_loader = $( document.getElementById('fields_category_id_loader') );
    var current_category_id = category.data('current-category-id');
        
    category.change(function()
    {
        category_change.prop( 'disabled', current_category_id == category.val() );
    });
        
    category_change.click(function(e)
    {
        // Prevent default action.
        e.preventDefault();
            
        data = {
            current_category_id: current_category_id,
            new_category_id: category.val(),
        };
        data[rsdir.token] = 1;
            
        // Hide the change button.
        category_change.addClass('hide');
            
        // Show the loader.
        category_loader.removeClass('hide');
            
        // Send the AJAX request.
        $.ajax(
        {
            type: 'POST',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=entry.checkCategoryAjax&random=' + Math.random(),
            data: data,
            dataType: 'json',
            success: function(response)
            {
                if (response.changed)
                {
                    if ( confirm(response.message) )
                    {
                        var form = category_change.parents('form');
                            
                        // Set task.
                        form.find(':input[name="task"]').val('entry.changeCategory');
                            
                        // Submit form.
                        form.submit();
                    }
                }
                else
                {
                    var message_container = $( document.getElementById('system-message-container') );
                        
                    message_container.find('.category-change-notice').remove();
                    message_container.append('<div class="alert alert-info category-change-notice">' + response.message + '</div>');
                }
                    
                // Show the change button.
                category_change.removeClass('hide');
                    
                // Hide the loader.
                category_loader.addClass('hide');
            }
        });
    });
        
    // Process image galleries.
    var galleries = $('[id|="rsdir-gallery"]');
        
    if (galleries.length > 0)
    {
        // Load CSS.
        $('<link/>', {rel: 'stylesheet', type: 'text/css', href: rsdir.root + 'media/com_rsdirectory/css/magnific-popup.css?v=' + rsdir.version}).appendTo('head');
            
        // Load JS and initialize popup.
        $.ajax(
        {
            url: rsdir.root + 'media/com_rsdirectory/js/jquery.magnific-popup.min.js?v=' + rsdir.version,
            dataType: "script",
            cache: true,
        }).done(function()
        {
            $(galleries).each(function(index, element)
            {
                $(element).magnificPopup(
                {
                    delegate: ".rsdir-img",
                    type: "image",
                    gallery: {enabled: true},
                    callbacks:
                    {
                        buildControls: function()
                        {
                            try
                            {
                                this.contentContainer.append( this.arrowLeft.add(this.arrowRight) );	
                            }
                            catch(e)
                            {
                            }
                        }
                    },
                });
            });
        });
    }
});