jQuery.noConflict();

/**
 * Calculate the cells width for a table.
 */
function calculate_cells_width(table)
{    
    table.find('td').each(function(index, element)
    {
        jQuery(element).css( 'width', jQuery(element).width() );
    });
}

function removeRow(btn)
{
    jQuery(btn).parents('tr').remove();
}

function removeCategoryImage(btn, category_id)
{
    if ( !confirm( Joomla.JText._('COM_RSDIRECTORY_CATEGORY_REMOVE_THUMBNAIL_CONFIRM') ) )
        return false;
        
    data = {
        category_id: category_id,
    };
    data[rsdir.token] = 1;
        
    jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&task=category.removeImageAjax&tmpl=component&random=' + Math.random(),
        data: data,
        success: function(response)
        {
            if (response == '1')
            {
                jQuery(btn).parents('.rsdir-thumb-wrapper').remove();
            }
        },
    });
        
    return false;
}

function getCustomFieldsPlaceholders(category_id, form_id)
{
    data = {
        category_id: typeof category_id != 'undefined' ? category_id : 0,
        form_id: typeof form_id != 'undefined' ? form_id : 0,
    };
    data[rsdir.token] = 1;
        
    jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&task=fields.getCustomFieldsPlaceholdersAjax&tmpl=component&random=' + Math.random(),
        data: data,
        success: function(response)
        {
            jQuery( document.getElementById('custom-fields-placeholders') ).html(response);
        },
    });
}

function populateDependencyValue()
{
    // Get the parent field id.
    dependency = jQuery( document.getElementById('jform_dependency') ).val();
        
    // Get the dependecy value element.
    dependency_value = jQuery( document.getElementById('jform_dependency_value') );
		
	var items = jQuery('textarea[name^="jform[items]"]:gt(0)');
        
	// Parent field was selected.
    if (dependency)
    {
		// Disable dependency items.
		items.attr('disabled', false);
			
        data = {
            cid: dependency,
        };
        data[rsdir.token] = 1;
            
        jQuery.ajax(
        {
            type: 'POST',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=field.getItemsOptionsAjax&tmpl=component&random=' + Math.random(),
            data: data,
            success: function(response)
            {
                dependency_value.find('*:gt(0)').remove();
                    
                if (response)
                {
					// Display loader.
					jQuery( document.getElementById('jform_dependency') ).parents('.control-group').find('.rsdir-loader').addClass('hide');
						
					// Add values to the dependency value field.
                    dependency_value.append(response);
                }
                    
                dependency_value.parents('.control-group').removeClass('hide');
            },
        });
			
		// Hide the regular items element parent.
		jQuery( document.getElementById('jform_items') ).parents('.control-group').addClass('hide');
    }
	// No parent field selected.
    else
    {
		// Hide dependency items elements parents.
		items.parents('.control-group').addClass('hide');
			
		// Disable dependency items.
		items.attr('disabled', true);
			
		// Hide the dependency value element.
        dependency_value.parents('.control-group').addClass('hide');
			
		// Show the regular items element parent.
		jQuery( document.getElementById('jform_items') ).parents('.control-group').removeClass('hide');
			
		// Hide loader.
		jQuery( document.getElementById('jform_dependency') ).parents('.control-group').find('.rsdir-loader').addClass('hide');
    }
}

/**
 * Abort the restore process.
 */
function abortRestore(message)
{
    if (message)
    {
        addLog( document.getElementById('restore-log'), message, 'error' );
    }
        
    // Show the start button.
    jQuery( document.getElementById('restore-start') ).removeClass('hide');
        
    // Hide the stop button.
    jQuery( document.getElementById('restore-stop') ).addClass('hide');
        
    // Enable form elements.
    jQuery( document.getElementById('restore') ).find(':input').attr('disabled', false);
}

/**
 * Verify restore data.
 */
function verifyRestore(offset)
{
    if (!offset)
    {
        offset = 0;
        addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_VERIFYING_DATA') );
    }
        
    var tools = jQuery( document.getElementById('tools') );
    var task = tools.find('input[name="task"]');
    var restore_tab = jQuery( document.getElementById('restore') );
    var restore_start = jQuery( document.getElementById('restore-start') );
    var restore_stop = jQuery( document.getElementById('restore-stop') );
    var jqxhr = false;
        
    task.val('tools.restoreVerify');
		
    data = tools.serializeArray();
    data.push(
    {
        name: 'offset',
        value: offset,
    });
        
    // Reset the task input.
    task.val('');
        
    jqxhr = jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&tmpl=component&random=' + Math.random(),
        data: data,
        dataType: 'json',
        success: function(response)
        {
            // Add messages.
            if (response.messages != undefined)
            {
                jQuery(response.messages).each(function(index, element)
                {
                    addLog( document.getElementById('restore-log'), element['message'], element['type'] );
                });
            }
                
            if (response.action == 'verify')
            {
                verifyRestore(response.offset);
            }
            else if (response.action == 'measure')
            {
                measureRestore();
            }
            else
            {
                abortRestore( Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED') );
            }
        },
    });
        
    // Set the stop button click event.
	restore_stop.off('click').on('click', function(e)
	{
		// Prevent default action.
		e.preventDefault();
            
        addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED_ON_USER_REQUEST'), 'error' );
			
		jqxhr.abort();
			
		// Hide the stop button.
		restore_stop.addClass('hide');
			
		// Show the start button.
		restore_start.removeClass('hide');
			
		// Unbind the click event.
		restore_stop.off('click');
			
		// Enable form elements.
		restore_tab.find(':input').attr('disabled', false);
	});
}

/**
 * Measure restore data.
 */
function measureRestore()
{
    addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_MEASURING_DATA'), 'info', 'restore-msg-data-measuring' );
        
	var tools = jQuery( document.getElementById('tools') );
    var task = tools.find('input[name="task"]');
    var restore_tab = jQuery( document.getElementById('restore') );
    var restore_start = jQuery( document.getElementById('restore-start') );
    var restore_stop = jQuery( document.getElementById('restore-stop') );
    var jqxhr = false;
        
    task.val('tools.restoreMeasure');
        
    jqxhr = jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&tmpl=component&random=' + Math.random(),
        data: tools.serializeArray(),
        dataType: 'json',
        success: function(response)
        {
            // Add messages.
            if (response.messages != undefined)
            {
                jQuery(response.messages).each(function(index, element)
                {
                    addLog( document.getElementById('restore-log'), element['message'], element['type'], element['id'] );
                });
            }
                
            if (response.action == 'restore')
            {
                restore(response.table);
            }
            else
            {
                abortRestore( Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED') );
            }
        },
    });
        
    // Reset the task input.
    task.val('');
        
    // Set the stop button click event.
	restore_stop.off('click').on('click', function(e)
	{
		// Prevent default action.
		e.preventDefault();
            
        addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED_ON_USER_REQUEST'), 'error' );
			
		jqxhr.abort();
			
		// Hide the stop button.
		restore_stop.addClass('hide');
			
		// Show the start button.
		restore_start.removeClass('hide');
			
		// Unbind the click event.
		restore_stop.off('click');
			
		// Enable form elements.
		restore_tab.find(':input').attr('disabled', false);
	});
}
    
/**
 * Restore backup.
 */
function restore(table, table_offset, offset)
{
    if (!table_offset)
    {
        table_offset = 0;
    }
        
    if (!offset)
    {
        offset = 0;
    }
        
    var tools = jQuery( document.getElementById('tools') );
	var task = tools.find('input[name="task"]');
	var restore_tab = jQuery( document.getElementById('restore') );
	var progress = restore_tab.find('.progress');
	var progress_bar = progress.find('.bar');
	var progress_label = restore_tab.find('.progress-label');
	var restore_start = jQuery( document.getElementById('restore-start') );
	var restore_stop = jQuery( document.getElementById('restore-stop') );
	var jqxhr = false;
        
    task.val('tools.restore');
        
    data = tools.serializeArray();
    data.push(
    {
        name: 'table',
        value: table,
    });
    data.push(
    {
        name: 'table_offset',
        value: table_offset,
    });
    data.push(
    {
        name: 'offset',
        value: offset,
    });
        
    // Reset the task input.
    task.val('');
        
    jqxhr = jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&tmpl=component&random=' + Math.random(),
        data: data,
        dataType: 'json',
        success: function(response)
        {
            // Add messages.
            if (response.messages != undefined)
            {
                jQuery(response.messages).each(function(index, element)
                {
                    addLog( document.getElementById('restore-log'), element['message'], element['type'], element['id'] );
                });
            }
                
            if (response.action == 'restore')
            {
                restore(response.table, response.table_progress, response.progress);
            }
            else if (response.action == 'done')
            {
                restore_stop.click();
                    
                progress.removeClass('progress-striped active');
                progress_bar.addClass('bar-success');
                    
                // Enable form elements.
                restore_tab.find(':input').attr('disabled', false);
                    
                addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_SUCCESSFUL'), 'success' );
            }
            else
            {
                abortRestore( Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED') );
                return;
            }
                
            progress_bar.css('width', response.completition + '%');
            progress_label.html(response.completition + '% ' + response.progress + '/' + response.total);
        },
    });
        
    // Set the stop button click event.
    restore_stop.off('click').on('click', function(e)
    {
        // Prevent default action.
        e.preventDefault();	
            
        jqxhr.abort();
            
        // Hide the stop button.
        restore_stop.addClass('hide');
            
        // Show the start button.
        restore_start.removeClass('hide');
            
        progress.removeClass('progress-striped active');
        progress_bar.addClass('bar-danger');
            
        // Unbind the click event.
        restore_stop.off('click');
            
        // Enable form elements.
        restore_tab.find(':input').attr('disabled', false);
    });
}

/**
 * Abort the import process.
 */
function abortImport(message)
{
    if (message)
    {
        addLog( document.getElementById('import-log'), message, 'error' );
    }
        
    // Show the start button.
    jQuery( document.getElementById('import-start') ).removeClass('hide');
        
    // Hide the stop button.
    jQuery( document.getElementById('import-stop') ).addClass('hide');
        
    // Enable form elements.
    jQuery( document.getElementById('import') ).find(':input').attr('disabled', false);
}
    
/**
 * Clear restore log.
 */
function clearLog(table)
{
    table = jQuery(table).find('table');
    table.find('tbody').remove();
    table.find('tfoot').removeClass('hide');
}
    
/**
 * Add log.
 */
function addLog(table, message, type, id)
{
    // Get the log table.
    table = jQuery(table).find('table');
        
    if (!type)
    {
        type = 'info';
    }
        
    // Hide the table footer.
    table.find('tfoot').addClass('hide');
        
    tbody = table.find('tbody');
        
    // Create the tbody tag element if it doesn't exist.
    if (tbody.length == 0)
    {
        tbody = jQuery('<tbody>');
        table.prepend(tbody);
    }
        
    tr = jQuery('<tr class="' + type + '"><td></td></tr>');
	tr.find('td').append(message);
        
    if (id)
    {
        tr.attr('id', id);
        existing = jQuery( document.getElementById(id) );
            
        if (existing.length > 0)
        {
            existing.replaceWith(tr);
        }
        else
        {
            tbody.append(tr);
        }
    }
    else
    {
        tbody.append(tr);   
    }
}

/**
 * Document ready.
 */
jQuery(function($)
{
    var tools = $( document.getElementById('tools') );
        
    var regenerate_titles = $( document.getElementById('regenerate-titles') );
    var regenerate_titles_forms = regenerate_titles.find('input[name="jform[regenerate_titles_forms][]"]');
    var regenerate_titles_elements = regenerate_titles.find('input[name="jform[regenerate_titles_elements][]"]');
        
    initCheckAll( document.getElementById('jform_checkall_regenerate_titles_forms0'), regenerate_titles_forms );
    initCheckAll( document.getElementById('jform_checkall_regenerate_titles_elements0'), regenerate_titles_elements );
        
    $( document.getElementById('regenerate-titles-start') ).click(function(e)
    {
        // Prevent default action.
        e.preventDefault();
            
        // Get the checked forms.
        forms_checked = regenerate_titles_forms.filter(':checked');
            
        // Get the checked elements.
        elements_checked = regenerate_titles_elements.filter(':checked');
        
        regenerate_titles_forms.parents('.control-group').removeClass('error');
        regenerate_titles_elements.parents('.control-group').removeClass('error');
            
        if (forms_checked.length == 0 || elements_checked.length == 0)
        {
            if (forms_checked.length == 0)
            {
                regenerate_titles_forms.parents('.control-group').addClass('error');
            }
                
            if (elements_checked.length == 0)
            {
                regenerate_titles_elements.parents('.control-group').addClass('error');
            }
                
            alert( Joomla.JText._('COM_RSDIRECTORY_REGENERATE_TITLES_SELECTION_ERROR') );
            return;
        }
            
        // Get the progress bar wrapper.
        var progress = regenerate_titles.find('.progress');
            
        progress.addClass('progress-striped active');
            
        // Get the progress bar.    
        var progress_bar = regenerate_titles.find('.bar');
            
        progress_bar.removeClass('bar-success bar-danger');
        progress_bar.css('width', 0);
            
        // Get the progress label.
        var progress_label = regenerate_titles.find('.progress-label');
            
        // Get the start button.
        var start_btn = $(this);
            
        // Disable checkboxes.
        regenerate_titles.find(':checkbox').attr('disabled', true);
            
        // Hide the start button.
        start_btn.addClass('rsdir-hide');
            
        // Get the stop button.
        stop_btn = $( document.getElementById('regenerate-titles-stop') );
            
        // Show the stop button.
        stop_btn.removeClass('rsdir-hide');
          
        // Get the checked forms ids.
        forms_ids = [];
            
        for (i = 0; i < forms_checked.length; i++)
        {
            forms_ids.push( forms_checked[i].value );
        }  
            
        // Get the checked elements.
        elements_values = [];
            
        for (i = 0; i < elements_checked.length; i++)
        {
            elements_values.push( elements_checked[i].value );
        }
            
        var jqxhr = false;
            
        /**
         * Define the regenerate titles recursive AJAX function.
         */
        function regen(forms_ids, elements_values, offset)
        {
            data = {
                forms_ids: forms_ids,
                elements: elements_values,
                offset: offset,
            };
            data[rsdir.token] = 1;
                
            jqxhr = $.ajax(
            {
                type: 'POST',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=tools.regenerateTitlesAjax&tmpl=component&random=' + Math.random(),
                data: data,
                dataType: 'json',
                success: function(response)
                {
                    progress_bar.css('width', response.completition + '%');
                    progress_label.html(response.completition + '% ' + response.progress + '/' + response.total);
                        
                    if ( parseFloat(response.completition) < 100)
                    {  
                        regen(forms_ids, elements_values, response.progress);
                    }
                    else
                    {
                        stop_btn.click();
                            
                        progress.removeClass('progress-striped active');
                        progress_bar.addClass('bar-success');
                    }
                },
            });
        }
            
        // Call the regenerate titles function.
        regen(forms_ids, elements_values, 0);
            
        // Set the stop button click event.
        stop_btn.click(function(e)
        {
            // Prevent default action.
            e.preventDefault();
                
            jqxhr.abort();
                
            // Enable checkboxes.
            regenerate_titles.find('input[type="checkbox"]').attr('disabled', false);
                
            // Hide the stop button.
            $(this).addClass('rsdir-hide');
                
            // Show the start button.
            start_btn.removeClass('rsdir-hide');
                
            progress.removeClass('progress-striped active');
            progress_bar.addClass('bar-danger');
                
            // Unbind the click event.
            $(this).unbind('click');
        });
    });
        
		
    var backup_tab = $( document.getElementById('backup') );
    var backup_start = $( document.getElementById('backup-start') );
    var backup_stop = $( document.getElementById('backup-stop') );
        
    initCheckAll( document.getElementById('checkall-cached-files'), tools.find('.cached-file') );
        
    backup_start.click(function(e)
    {
        // Prevent default action.
        e.preventDefault();
            
        // Get the progress bar wrapper.
        var progress = backup_tab.find('.progress');
            
        progress.addClass('progress-striped active');
            
        // Get the progress bar.    
        var progress_bar = backup_tab.find('.bar');
            
        progress_bar.removeClass('bar-success bar-danger');
        progress_bar.css('width', 0);
            
        // Get the progress label.
        var progress_label = backup_tab.find('.progress-label');
            
        // Hide the start button.
        backup_start.addClass('rsdir-hide');
            
        // Show the stop button.
        backup_stop.removeClass('rsdir-hide');
            
        var jqxhr = false;
            
        /**
         * Define the backup recursive AJAX function.
         */
        function backupAjax(offset)
        {
            data = {
                offset: offset,
            };
            data[rsdir.token] = 1;
                
            jqxhr = $.ajax(
            {
                type: 'POST',
                url: rsdir.base + 'index.php?option=com_rsdirectory&task=tools.backupAjax&tmpl=component&random=' + Math.random(),
                data: data,
                dataType: 'json',
                success: function(response)
                {
                    progress_bar.css('width', response.completition + '%'); 
                    progress_label.html(response.completition + '% ' + response.progress + '/' + response.total);
                        
                    if ( parseFloat(response.completition) < 100)
                    {  
                        backupAjax(response.progress);
                    }
                    else
                    {
                        backup_stop.click();
                            
                        progress.removeClass('progress-striped active');
                        progress_bar.addClass('bar-success');
                            
                        if (response.archive_html)
                        {
                            cached_files = $( document.getElementById('cached-files') );
                                
                            tbody = cached_files.find('tbody');
                                
                            // Create a tbody element if it doesn't exist.
                            if ( tbody.length == 0 )
                            {
                                tbody = $('<tbody>');
                                cached_files.append(tbody);
                            }
                                
                            archive_html = $(response.archive_html).addClass('success');
                                
                            // Unhighlight previous archives.
                            tbody.find('.success').removeClass('success');
                                
                            // Add the new archive to the cached files table.
                            tbody.prepend(archive_html);
                                
                            // Show the delete file(s) button.
                            $( document.getElementById('backup-delete-files') ).removeClass('hide');
                                
                            // Modify the row numbers.
                            tbody.find('tr').each(function(index, element)
                            {
                                $(element).find('td').eq(1).html(index + 1);
                            });
                                
                            // Hide the tfoot.
                            cached_files.find('tfoot').addClass('hide');
                                
                            // PUSH the file tfor download.
                            window.location = archive_html.find('a').attr('href');
                                
                            // Reinitialize the check all functionality.
                            initCheckAll( $( document.getElementById('checkall-cached-files') ), tbody.find('.cached-file') );
                        }
                    }
                },
            });
        }
            
        // Call the backup function.
        backupAjax(0);
            
        // Set the stop button click event.
        backup_stop.click(function(e)
        {
            // Prevent default action.
            e.preventDefault();
                
            jqxhr.abort();
                
            // Hide the stop button.
            $(this).addClass('rsdir-hide');
                
            // Show the start button.
            backup_start.removeClass('rsdir-hide');
                
            progress.removeClass('progress-striped active');
            progress_bar.addClass('bar-danger');
                
            // Unbind the click event.
            $(this).unbind('click');
        });
    });
        
    $( document.getElementById('backup-delete-files') ).click(function(e)
    {
        // Prevent default action.
        e.preventDefault();
            
        // Get the checked files.
        var files_checked = backup_tab.find('.cached-file').filter(':checked');
            
        if (files_checked.length == 0)
        {
            alert( Joomla.JText._('COM_RSDIRECTORY_BACKUP_DELETE_SELECTION_ERROR') );
            return;
        }
            
        var button = $(this);
        var loader = $( document.getElementById('backup-delete-files-loader') );
            
        button.attr('disabled', true);
        loader.removeClass('hide');
        
        // Get the checked files.
        files_values = [];
            
        for (i = 0; i < files_checked.length; i++)
        {
            files_values.push( files_checked[i].value );
        }
            
        data = {
            files: files_values,
        };
        data[rsdir.token] = 1;
            
        $.ajax(
        {
            type: 'POST',
            url: rsdir.base + 'index.php?option=com_rsdirectory&task=tools.deleteBackupFiles&tmpl=component&random=' + Math.random(),
            data: data,
            success: function(response)
            {
                if (response == 1)
                {
                    cached_files = $( document.getElementById('cached-files') );
                        
                    files_checked.parents('tr').remove();
                        
                    if ( cached_files.find('tbody tr').length == 0 )
                    {
                        cached_files.find('tfoot').removeClass('hide');
                        button.addClass('hide');
                    }
                    else
                    {
                        cached_files.find('tbody tr').each(function(index, element)
                        {
                            $(element).find('td:eq(1)').html(index + 1);
                        });
                    }
                }
                    
                button.attr('disabled', false);
                loader.addClass('hide');
                $( document.getElementById('checkall-cached-files') ).prop('checked', false);
            },
        });
    });
        
		
    // Get the restore tab.
    var restore_tab = $( document.getElementById('restore') );
        
    var restore_from = $( document.getElementById('jform_restore_from') );
    var restore_confirm = $( document.getElementById('jform_restore_confirm0') );
    var restore_start = $( document.getElementById('restore-start') );
    var restore_stop = $( document.getElementById('restore-stop') );
        
    backup_tab.on('click', '.restore-cache', function(e)
	{
		e.preventDefault();
			
		// Trigger a click on the restore tab.
		tools.find('a[href="#restore"]').click();
			
		// Deselect everything.
		restore_from.find('option').prop('selected', false);
			
		// Select the local archive option.
		restore_from.find('option[value="local_archive"]').prop('selected', true);
			
		// Trigger a change event on the restore select.
		restore_from.trigger('change');
			
		$( document.getElementById('jform_restore_local_archive') ).val( $(this).data('path') );
	});
        
    restore_from.change(function()
	{
        restore_from.find('option').each(function(index, option)
        {
            option = $(option);
                
            if ( option.prop('selected') )
            {
                $( document.getElementById( 'jform_restore_' + option.val() ) ).parents('.control-group').removeClass('hide');
            }
            else
            {
                $( document.getElementById( 'jform_restore_' + option.val() ) ).parents('.control-group').addClass('hide');
            }
        });
			
		if ( restore_from.val() == 'uploaded_archive' )
		{
			restore_start.html( Joomla.JText._('COM_RSDIRECTORY_RESTORE_UPLOAD_AND_RESTORE_BUTTON') );
		}
		else
		{
			restore_start.html( Joomla.JText._('COM_RSDIRECTORY_RESTORE_BUTTON') );
		}
	});
        
    if ( restore_confirm.prop('checked') )
    {
        restore_start.attr('disabled', false);
    }
    else
    {
        restore_start.attr('disabled', true);
    }
        
    restore_confirm.change(function()
    {
        if ( $(this).prop('checked') )
        {
            restore_start.attr('disabled', false);
        }
        else
        {
            restore_start.attr('disabled', true);
        }
    });
        
    restore_start.click(function(e)
	{
		e.preventDefault();
			
		restore_tab.find('.control-group').removeClass('error');
			
		// Initialize the errors array.
		errors = [];
			
		switch ( restore_from.val() )
		{
			case 'uploaded_archive':
					
				uploaded_archive = $( document.getElementById('jform_restore_uploaded_archive') );
					
				if ( !uploaded_archive.val() )
				{
					uploaded_archive.parents('.control-group').addClass('error');
					errors.push( Joomla.JText._('COM_RSDIRECTORY_RESTORE_UPLOADED_ARCHIVE_ERROR') );
				}
					
				break;
					
			case 'local_archive':
					
				local_archive = $( document.getElementById('jform_restore_local_archive') );
					
				if ( !$.trim( local_archive.val() ) )
				{
					local_archive.parents('.control-group').addClass('error');
					errors.push( Joomla.JText._('COM_RSDIRECTORY_RESTORE_LOCAL_ARCHIVE_ERROR') );
				}
					
				break;
					
			case 'url':
					
				url = $( document.getElementById('jform_restore_url') );
					
				if ( !$.trim( url.val() ) )
				{
					url.parents('.control-group').addClass('error');
					errors.push( Joomla.JText._('COM_RSDIRECTORY_RESTORE_URL_ERROR') );
				}
					
				break;
		}
			
		if (errors.length > 0)
		{
			alert( errors.join("\n") );
			return false;
		}
			
        restore_tab.find('.progress').addClass('progress-striped active');
            
        // Get the progress bar.    
        var progress_bar = restore_tab.find('.bar');
            
        progress_bar.removeClass('bar-success bar-danger');
        progress_bar.css('width', 0);
            
        // Get the progress label.
        var progress_label = restore_tab.find('.progress-label');
			
		// Clear restore log.
		clearLog( document.getElementById('restore-log') );
			
		// Hide the start button.
		restore_start.addClass('hide');
			
		// Show the stop button.
		restore_stop.removeClass('hide');
			
		// Get the tools hidden input.
		var task = tools.find('input[name="task"]')
			
		if ( restore_from.val() == 'uploaded_archive' )
		{
			// Set the form target to the iframe.
			tools.attr('target', 'restore_upload_target');
				console.log(tools);
			// Set the task.
			task.val('tools.restoreInitUploadedArchive');
				
			// Submit the form.
			tools.submit();
				
			// Disable form elements.
			restore_tab.find(':input').not(restore_stop).attr('disabled', true);
				
			// Reset the target attribute.
			tools.attr('target', null);
				
			// Reset task.
			task.val('');
				
			// Remove and set a new stop button click event.
			restore_stop.off('click').on('click', function(e)
			{
				e.preventDefault();
					
				addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED_ON_USER_REQUEST'), 'error' );
					
				document.getElementById('restore_upload_target').src = '';
					
				// Show the start button.
				restore_start.removeClass('hide');
					
				// Hide the stop button.
				$(this).addClass('hide');
				
				// Enable form elements.
				restore_tab.find(':input').attr('disabled', false);
			});
		}
		else
		{
			// Set the task.
			task.val('tools.restoreInit');
				
			var jqxhr = $.ajax(
            {
                type: 'POST',
                dataType: 'JSON',
                url: rsdir.base + 'index.php?option=com_rsdirectory&random=' + Math.random(),
                data: tools.serializeArray(),
				beforeSend: function(xhr)
				{
                    // Disable form elements.
					restore_tab.find(':input').not(restore_stop).attr('disabled', true);
				},
                success: function(json)
                {
					if (json.messages != undefined)
					{
						$(json.messages).each(function(index, element)
						{
							addLog( document.getElementById('restore-log'), element['message'], element['type'] );
						});
					}
                        
                    if (json.action == 'verify')
                    {
                        verifyRestore();
                    }
                    else
                    {
                        abortRestore( Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED') );
                    }
				}
			});
				
			// Reset task.
			task.val('');
				
			restore_stop.off('click').on('click', function(e)
			{
				e.preventDefault();
                    
                addLog( document.getElementById('restore-log'), Joomla.JText._('COM_RSDIRECTORY_RESTORE_ABORTED_ON_USER_REQUEST'), 'error' );
					
				jqxhr.abort();
					
				// Show the start button.
				restore_start.removeClass('hide');
					
				// Hide the stop button.
				$(this).addClass('hide');
					
				// Enable form elements.
				restore_tab.find(':input').attr('disabled', false);
			});
		}
	});
        
        
    var import_tab = $( document.getElementById('import') );
    var import_from = import_tab.find(':input[name="jform[import_from]"]');
    var import_start = $( document.getElementById('import-start') );
        
    import_from.change(function()
    {
        tools.find('.import-fieldset').addClass('hide');
        $( document.getElementById( $(this).val() ) ).removeClass('hide');
		import_start.html( Joomla.JText._('COM_RSDIRECTORY_IMPORT_BUTTON') );
    });
		
	import_start.click( function(e)
    {
        e.preventDefault();
			
		import_tab.find('.error').removeClass('error');
			
		if ( import_from.filter(':checked').length == 0 )
        {
            import_from.parents('.control-group').addClass('error');
            alert( Joomla.JText._('COM_RSDIRECTORY_IMPORT_SELECT_OPTION_ERROR') );
        }
	});
        
		
    var dependency = $( document.getElementById('jform_dependency') );
        
    if (dependency.length > 0)
    {
        populateDependencyValue();
            
        dependency.change(function()
        {
			dependency.parents('.control-group').find('.rsdir-loader').removeClass('hide');
            populateDependencyValue();
        });
    }
        
    $( document.getElementById('jform_dependency_value') ).change(function()
    {
        dependency = $( document.getElementById('jform_dependency') ).val();
            
        base64 = Base64.encode( $(this).val() );
            
        // Sanitize the dependency value.
        dependency_value = base64.toLowerCase();
        dependency_value = dependency_value.replace(/^[^a-z0-9]+/, '').replace(/[^a-z0-9]+$/, '').replace(/[^a-z0-9\-]+/, '');
            
        // Get all the items elements.
        items = $( document.getElementById('general') ).find('textarea[name^="jform[items]"]');
            
		// Hide all items elements parents.
        items.parents('.control-group').addClass('hide');
            
        if (dependency_value)
        {
            // Build the element id.
            id = 'jform_items_' + dependency + '_' + dependency_value;
                
            if ( $( document.getElementById(id) ).length == 0 )
            {
                // Get a clone of the items' parent.
                clone = $( document.getElementById('jform_items') ).parents('.control-group').clone();
                    
				// Unhide cloned element.
                clone.removeClass('hide');
                    
				// Set textarea attributes.
                clone.find('textarea').attr(
                {
                    id: id,
                    name: 'jform[items][' + dependency + '][' + base64 + ']',
                }).val(null);
                    
				// Set label attributes.
                clone.find('label').attr('id', id + '-lbl').attr('for', id);
                    
                // Place the clone after the last items element's parent.
                items.filter(':last').parents('.control-group').after(clone);
            }
            else
            {
				// Unhide the existing dependency items element.
                $( document.getElementById(id) ).parents('.control-group').removeClass('hide');
            }
        }
    });
		
	var batch_author = $( document.getElementById('batch_author') );
	var batch_user_id_id = $( document.getElementById('batch_user_id_id') );
	var batch_user_id_wrap = batch_user_id_id.parents('.control-group');
	var batch_category = $( document.getElementById('batch_category') );
	var batch_category_id = $( document.getElementById('batch_category_id') );
	var batch_category_id_wrap = batch_category_id.parents('.control-group');
		
	if ( batch_author.val() == 'new' )
	{
		batch_user_id_wrap.removeClass('hide');
	}
	else
	{
		batch_user_id_wrap.addClass('hide');
	}
		
	batch_author.change(function()
	{
		if ( $(this).val() == 'new' )
		{
			batch_user_id_wrap.removeClass('hide');
		}
		else
		{
			batch_user_id_wrap.addClass('hide');
		}
	});
		
	if ( batch_category.val() == 'new' )
	{
		batch_category_id_wrap.removeClass('hide');
	}
	else
	{
		batch_category_id_wrap.addClass('hide');
	}
		
	batch_category.change(function()
	{
		if ( $(this).val() == 'new' )
		{
			batch_category_id_wrap.removeClass('hide');
		}
		else
		{
			batch_category_id_wrap.addClass('hide');
		}
	});
		
	$( document.getElementById('batchSubmit') ).click(function(e)
	{
		e.preventDefault();
			
		// Initialize the errors array.
		errors = [];
			
		// Required error?
		req_error = false;
			
		$(this).parents('.batch').find('.error').removeClass('error');
			
		if (document.adminForm.boxchecked.value == 0)
		{
			errors.push( Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST') );
		}
			
		if ( batch_author.val() == 'new' && $( document.getElementById('batch_user_id_id') ).val() == false )
		{
			batch_user_id_wrap.addClass('error');
			req_error = true;
		}
			
		if ( batch_category.val() == 'new' && $( document.getElementById('batch_category_id') ).val() == false )
		{
			batch_category_id_wrap.addClass('error');
			req_error = true;
		}
			
		if (req_error)
		{
			errors.push( Joomla.JText._('COM_RSDIRECTORY_FILL_IN_REQUIRED_FIELDS') );
		}
			
		if (errors.length > 0)
		{
			alert( errors.join("\n") );
			return false;
		}
			
		Joomla.submitbutton('entries.batch');
	});
		
	$( document.getElementById('batchClear') ).click(function(e)
	{
		e.preventDefault();
			
		batch_author.val('keep').trigger('change');
		batch_user_id_wrap.find(':input[type="text"]').val('');
		batch_user_id_id.val(0);
		batch_category.val('keep').trigger('change');
		batch_category_id.val(0);
	});
});