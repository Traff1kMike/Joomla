jQuery.noConflict();

var CSVImportFormData;

function measureCSVImport()
{
	var tools = jQuery( document.getElementById('tools') );
    var import_tab = jQuery( document.getElementById('import') );
	var import_log = document.getElementById('import-log');
    var import_start = jQuery( document.getElementById('import-start') );
    var import_stop = jQuery( document.getElementById('import-stop') );
    var jqxhr = false;
		
	addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORT_MEASURING_DATA'), 'info', 'import-msg-data-measuring' );
        
    // Set the import action.
    updateValue(CSVImportFormData, 'jform[import_action]', 'measure');
        
    jqxhr = jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&tmpl=component&random=' + Math.random(),
        data: CSVImportFormData,
        dataType: 'json',
        success: function(response)
        {
            // Add messages.
            if (response.messages != undefined)
            {
                jQuery(response.messages).each(function(index, element)
                {
                    addLog( import_log, element['message'], element['type'], element['id'] );
                });
            }
                
            if (response.action == 'selectColumns')
            {
                selectColumns();
            }
            else
            {
                abortImport( Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED') );
            }
        },
    });
        
    // Set the stop button click event.
	import_stop.off('click').on('click', function(e)
	{
		// Prevent default action.
		e.preventDefault();
            
        addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED_ON_USER_REQUEST'), 'error' );
			
		jqxhr.abort();
			
		// Hide the stop button.
		import_stop.addClass('hide');
			
		// Show the start button.
		import_start.removeClass('hide');
			
		// Unbind the click event.
		import_stop.off('click');
			
		// Enable form elements.
		import_tab.find(':input').attr('disabled', false);
	});
}

function selectColumns()
{
	var tools = jQuery( document.getElementById('tools') );
    var import_tab = jQuery( document.getElementById('import') );
	var import_log = document.getElementById('import-log');
    var import_start = jQuery( document.getElementById('import-start') );
    var import_stop = jQuery( document.getElementById('import-stop') );
	var select_columns = jQuery( Joomla.JText._('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_SELECT_COLUMNS') );
	var select_columns_btn = select_columns.find('a');
		
	// Set the import action.
    updateValue(CSVImportFormData, 'jform[import_action]', 'selectColumns');
		
	// Set the check token method to GET.
    updateValue(CSVImportFormData, 'jform[check_token_method]', 'get');
		
	href = rsdir.base + 'index.php?option=com_rsdirectory&' + jQuery.param(CSVImportFormData) + '&random=' + Math.random();
		
	// Set the check token method to POST.
    updateValue(CSVImportFormData, 'jform[check_token_method]', 'post');
		
	select_columns_btn.attr('href', href).magnificPopup(
	{
		type: 'ajax',
		callbacks: {
			ajaxContentAdded: function()
			{
				setSelectColumns(this.content);
			},
		},
	}).click();
		
	addLog(import_log, select_columns, 'info', 'import-select-columns');
		
	// Set the stop button click event.
	import_stop.off('click').on('click', function(e)
	{
		// Prevent default action.
		e.preventDefault();
            
        addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED_ON_USER_REQUEST'), 'error' );
			
		// Hide the stop button.
		import_stop.addClass('hide');
			
		// Show the start button.
		import_start.removeClass('hide');
			
		// Unbind the click event.
		import_stop.off('click');
			
		// Enable form elements.
		import_tab.find(':input').attr('disabled', false);
			
		// Disable select columns button.
		select_columns_btn.attr('disabled', true).off('click').on( 'click' ,function(e)
		{
			e.preventDefault();
		});
	});
}

function setSelectColumns(content)
{
	var import_log = document.getElementById('import-log');
		
	var content = jQuery(content);
	var select_columns_btn = content.find( document.getElementById('csv-select-columns') );
	var fields = content.find(':input[name="jform[csv][columns][]"]');
	var alert_error = content.find('.alert-error');
		
	select_columns_btn.click(function(e)
	{
		e.preventDefault();
			
		// Remove previous errors.
		content.find('.error').removeClass('error');
		alert_error.addClass('hide');
			
		// Initialize the errors array.
		var errors = []
			
		// Initialize an array of all selected values.
		var values = []
			
		// Set to true if a column is selected more than once.
		var duplicates = false;
			
		// Set to false if at least one column was selected.
		is_empty = true;
			
		fields.each(function(index, element)
		{
			element = jQuery(element);
			val = element.val();
				
			if ( val != '' )
			{
				if ( values.indexOf(val) > -1 )
				{
					element.parents('.control-group').addClass('error');
					duplicates = true;
				}
					
				is_empty = false;
			}
				
			values.push(val);
		});
			
		if (duplicates)
		{
			errors.push( Joomla.JText._('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_DUPLICATES_COLUMNS_ERROR') );
		}
			
		if (is_empty)
		{
			fields.parents('.control-group').addClass('error');
			errors.push( Joomla.JText._('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COLUMNS_SELECTION_ERROR') );
		}
			
		if (errors.length > 0)
		{
			alert_error.html( errors.join('<br />') ).removeClass('hide');
			return;
		}
			
		// Set the import action.
		updateValue(CSVImportFormData, 'jform[import_action]', 'import');
			
		// Set the columns values.
		updateValue(CSVImportFormData, 'jform[csv][columns]', values);
			
		// Click the close button.
		content.find('.mfp-close').click();
			
		addLog( import_log, Joomla.JText._('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_COLUMNS_SELECTED'), 'success', 'import-select-columns' );
		addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORTING_DATA') );
			
		importCSVProcess();
	});
}

/**
 * Import.
 */
function importCSVProcess(offset)
{   
    if (!offset)
    {
        offset = 0;
    }
        
    var tools = jQuery( document.getElementById('tools') );
	var import_tab = jQuery( document.getElementById('import') );
	var import_log = document.getElementById('import-log');
	var progress = import_tab.find('.progress');
	var progress_bar = progress.find('.bar');
	var progress_label = import_tab.find('.progress-label');
	var import_start = jQuery( document.getElementById('import-start') );
	var import_stop = jQuery( document.getElementById('import-stop') );
	var jqxhr = false;
        
    updateValue(CSVImportFormData, 'jform[csv][offset]', offset);
        
    jqxhr = jQuery.ajax(
    {
        type: 'POST',
        url: rsdir.base + 'index.php?option=com_rsdirectory&tmpl=component&random=' + Math.random(),
        data: CSVImportFormData,
        dataType: 'json',
        success: function(response)
        {
            // Add messages.
            if (response.messages != undefined)
            {
                jQuery(response.messages).each(function(index, element)
                {
                    addLog( import_log, element['message'], element['type'], element['id'] );
                });
            }
                
            if (response.action == 'import')
            {
                importCSVProcess(response.progress);
            }
            else if (response.action == 'done')
            {
                import_stop.click();
                    
                progress.removeClass('progress-striped active');
                progress_bar.addClass('bar-success');
                    
                // Enable form elements.
                import_tab.find(':input').attr('disabled', false);
                    
                addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORT_SUCCESSFUL'), 'success' );
            }
            else
            {
                abortImport( Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED') );
                return;
            }
                
            progress_bar.css('width', response.completition + '%');
            progress_label.html(response.completition + '% ' + response.progress + '/' + response.total);
        },
    });
        
    // Set the stop button click event.
    import_stop.off('click').on('click', function(e)
    {
        // Prevent default action.
        e.preventDefault();	
            
        jqxhr.abort();
            
        // Hide the stop button.
        import_stop.addClass('hide');
            
        // Show the start button.
        import_start.removeClass('hide');
            
        progress.removeClass('progress-striped active');
        progress_bar.addClass('bar-danger');
            
        // Unbind the click event.
        import_stop.off('click');
            
        // Enable form elements.
        import_tab.find(':input').attr('disabled', false);
    });
}

jQuery(function($)
{
	var tools = $( document.getElementById('tools') );
		
	var import_tab = $( document.getElementById('import') );
    var import_from = import_tab.find(':input[name="jform[import_from]"]');
    var import_start = $( document.getElementById('import-start') );
    var import_stop = $( document.getElementById('import-stop') );
    var import_log = document.getElementById('import-log');
		
	var csv_fieldset = $( document.getElementById('csv') );
	var from = $( document.getElementById('csv_from') );
		
	import_tab.find('[name="jform[import_from]"][value="csv"]').change(function()
	{
		if ( from.val() == 'uploaded_file' )
		{
			import_start.html( Joomla.JText._('COM_RSDIRECTORY_IMPORT_UPLOAD_AND_IMPORT_BUTTON') );
		}
	});
		
	from.change(function()
	{
        from.find('option').each(function(index, option)
        {
            option = $(option);
                
            if ( option.prop('selected') )
            {
                $( document.getElementById( 'csv_' + option.val() ) ).parents('.control-group').removeClass('hide');
            }
            else
            {
                $( document.getElementById( 'csv_' + option.val() ) ).parents('.control-group').addClass('hide');
            }
        });
			
		if ( from.val() == 'uploaded_file' )
		{
			import_start.html( Joomla.JText._('COM_RSDIRECTORY_IMPORT_UPLOAD_AND_IMPORT_BUTTON') );
		}
		else
		{
			import_start.html( Joomla.JText._('COM_RSDIRECTORY_IMPORT_BUTTON') );
		}
	});
		
	import_start.click( function(e)
    {
        e.preventDefault();
			
		if ( import_from.filter(':checked').val() != 'csv' )
			return;
			
		req_error = false;
			
		switch ( from.val() )
		{
			case 'uploaded_file':
					
				uploaded_file = $( document.getElementById('csv_uploaded_file') );
					
				if ( !uploaded_file.val() )
				{
					uploaded_file.parents('.control-group').addClass('error');
					req_error = true;
				}
					
				break;
					
			case 'local_file':
					
				local_file = $( document.getElementById('csv_local_file') );
					
				if ( !$.trim( local_file.val() ) )
				{
					local_file.parents('.control-group').addClass('error');
					req_error = true;
				}
					
				break;
					
			case 'url':
					
				url = $( document.getElementById('csv_url') );
					
				if ( !$.trim( url.val() ) )
				{
					url.parents('.control-group').addClass('error');
					req_error = true;
				}
					
				break;
		}
			
		category_id = $( document.getElementById('csv_category_id') );
			
		if ( category_id.val() == '' )
		{
			category_id.parents('.control-group').addClass('error');
			req_error = true;
		}
			
		ignore_first_line = csv_fieldset.find('[name="jform[csv][ignore_first_line]"]');
			
		if ( ignore_first_line.filter(':checked').length == 0 )
		{
			ignore_first_line.parents('.control-group').addClass('error');
			req_error = true;
		}
		
		regenerate_titles = csv_fieldset.find('[name="jform[csv][regenerate_titles]"]');
		
		if ( regenerate_titles.filter(':checked').length == 0 )
		{
			regenerate_titles.parents('.control-group').addClass('error');
			req_error = true;
		}
			
		csv_delimiter = $( document.getElementById('csv_delimiter') );
			
		if ( csv_delimiter.val() == '' )
		{
			csv_delimiter.parents('.control-group').addClass('error');
			req_error = true;
		}
			
		csv_enclosure = $( document.getElementById('csv_enclosure') );
			
		if ( csv_enclosure.val() == '' )
		{
			csv_enclosure.parents('.control-group').addClass('error');
			req_error = true;
		}
			
		csv_escape = $( document.getElementById('csv_escape') );
			
		if ( csv_escape.val() == '' )
		{
			csv_escape.parents('.control-group').addClass('error');
			req_error = true;
		}
			
		if (req_error)
		{
			alert( Joomla.JText._('PLG_SYSTEM_RSDIRECTORYIMPORTCSV_REQUIRED_FIELDS_ERROR') );
			return false;
		}
			
		// Get the progress bar wrapper.
        var progress = import_tab.find('.progress');
            
        progress.addClass('progress-striped active');
            
        // Get the progress bar.    
        var progress_bar = import_tab.find('.bar');
            
        progress_bar.removeClass('bar-success bar-danger');
        progress_bar.css('width', 0);
            
        // Get the progress label.
        var progress_label = import_tab.find('.progress-label');
			
		// Clear import log.
		clearLog(import_log);
			
		// Hide the start button.
		import_start.addClass('hide');
			
		// Show the stop button.
		import_stop.removeClass('hide');
			
		// Get the tools hidden input.
		var task = tools.find('input[name="task"]')
			
		// Set the task.
		task.val('tools.import');
			
		// Get form data.
		CSVImportFormData = tools.serializeArray();
			
		if ( from.val() == 'uploaded_file' )
		{
			// Set the form target to the iframe.
			tools.attr('target', 'csv_upload_target');
				
			// Submit the form.
			tools[0].submit();
				
			// Disable form elements.
			import_tab.find(':input').not(import_stop).attr('disabled', true);
				
			// Reset the target attribute.
			tools.attr('target', null);
				
			// Reset task.
			task.val('');
				
			// Remove and set a new stop button click event.
			import_stop.off('click').on('click', function(e)
			{
				e.preventDefault();
					
				addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED_ON_USER_REQUEST'), 'error' );
					
				document.getElementById('csv_upload_target').src = '';
					
				// Show the start button.
				import_start.removeClass('hide');
					
				// Hide the stop button.
				$(this).addClass('hide');
				
				// Enable form elements.
				import_tab.find(':input').attr('disabled', false);
			});
		}
		else
		{
			var jqxhr = $.ajax(
            {
                type: 'POST',
                dataType: 'JSON',
                url: rsdir.base + 'index.php?option=com_rsdirectory&random=' + Math.random(),
                data: CSVImportFormData,
				beforeSend: function(xhr)
				{
                    // Disable form elements.
					import_tab.find(':input').not(import_stop).attr('disabled', true);
				},
                success: function(json)
                {
					if (json.messages != undefined)
					{
						$(json.messages).each(function(index, element)
						{
							addLog( import_log, element['message'], element['type'] );
						});
					}
                        
                    if (json.action == 'measure')
                    {
                        measureCSVImport(CSVImportFormData);
                    }
                    else
                    {
                        abortImport( Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED') );
                    }
				}
			});
				
			// Reset task.
			task.val('');
				
			import_stop.off('click').on('click', function(e)
			{
				e.preventDefault();
                    
                addLog( import_log, Joomla.JText._('COM_RSDIRECTORY_IMPORT_ABORTED_ON_USER_REQUEST'), 'error' );
					
				jqxhr.abort();
					
				// Show the start button.
				import_start.removeClass('hide');
					
				// Hide the stop button.
				$(this).addClass('hide');
					
				// Enable form elements.
				import_tab.find(':input').attr('disabled', false);
			});
		}
	});
});