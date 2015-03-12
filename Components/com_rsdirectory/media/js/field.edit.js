jQuery.noConflict();

jQuery(function($)
{
    // Get the form element.
    var adminForm = $( document.getElementById('adminForm') );
        
    // Get the searchable advanced fields parents.
    var searchable_advanced_fields_parents = adminForm.find('.searchable-advanced-field').parents('.control-group');
        
    // Get the searchable advanced items element.
    var searchable_advanced_items = $( document.getElementById('jform_searchable_advanced_items') );
        
    // Get the searchable advanced element.
    var searchable_advanced = $( document.getElementById('jform_searchable_advanced') ).filter(':input');
    
    // Get the use dependency elements.
    var use_dependency = $( document.getElementById('jform_use_dependency') ).find('input[type="radio"]');
        
    // Get the use field items elements.
    var use_field_items = $( document.getElementById('jform_use_field_items') ).find('input[type="radio"]');
        
    // Get the condition type element.
    var condition_type = $( document.getElementById('jform_searchable_advanced_condition_type') );
        
    if (searchable_advanced.length > 0)
    {
        switch ( searchable_advanced.val() )
        {
            case '':
                    
                searchable_advanced_fields_parents.addClass('hide');
                    
                break;
                    
            case 'textbox':
                    
                use_dependency.parents('.control-group').addClass('hide');
                use_field_items.parents('.control-group').addClass('hide');
                searchable_advanced_items.parents('.control-group').addClass('hide');
                    
                break;
                    
            case 'range':
                    
                use_dependency.parents('.control-group').addClass('hide');
                condition_type.parents('.control-group').addClass('hide');
                    
                break;
                    
            case 'date_range':
                    
                use_dependency.parents('.control-group').addClass('hide');
                use_field_items.parents('.control-group').addClass('hide');
                condition_type.parents('.control-group').addClass('hide');
                searchable_advanced_items.parents('.control-group').addClass('hide');
                    
                break;
                    
            case 'dropdown':
            case 'checkboxgroup':
            case 'radiogroup':
                    
                if ( use_dependency.filter(':checked').val() == 1)
                {
                    use_field_items.parents('.control-group').addClass('hide');
                    searchable_advanced_items.parents('.control-group').addClass('hide');
                }
                    
                break;
        }
            
        searchable_advanced.change(function()
        {
            // Get the value of the field.
            val = $(this).val();
                
            if (val)
            {
                searchable_advanced_fields_parents.removeClass('hide');
                    
                if (val == 'textbox')
                {
                    use_dependency.parents('.control-group').addClass('hide');
                    use_field_items.parents('.control-group').addClass('hide');
                    searchable_advanced_items.parents('.control-group').addClass('hide');
                }
                else if (val == 'range')
                {
                    use_dependency.parents('.control-group').addClass('hide');
                    condition_type.parents('.control-group').addClass('hide');
                        
                    if ( use_field_items.filter(':checked').val() == 1 )
                    {
                        searchable_advanced_items.parents('.control-group').addClass('hide');
                    }
                }
                else if (val == 'date_range')
                {
                    use_dependency.parents('.control-group').addClass('hide');
                    use_field_items.parents('.control-group').addClass('hide');
                    condition_type.parents('.control-group').addClass('hide');
                    searchable_advanced_items.parents('.control-group').addClass('hide');
                }
                else if (val == 'dropdown' || val == 'checkboxgroup' || val == 'radiogroup')
                {
                    if ( use_dependency.filter(':checked').val() == 1 )
                    {
                        use_field_items.parents('.control-group').addClass('hide');
                        searchable_advanced_items.parents('.control-group').addClass('hide');
                    }
                    else
                    {
                        if ( use_field_items.filter(':checked').val() == 1 )
                        {
                            searchable_advanced_items.parents('.control-group').addClass('hide');
                        }
                    }
                }
            }
            else
            {
                searchable_advanced_fields_parents.addClass('hide');
            }
        });
    }
    else
    {
        // Get the searchable advanced element.
        var searchable_advanced = adminForm.find('.searchable-advanced input[type="radio"]');
            
        // Hide the searchable advanced fields.
        if ( searchable_advanced.filter(':checked').val() == 0 )
        {
            searchable_advanced_fields_parents.addClass('hide');
        }
            
        searchable_advanced.click(function()
        {
            if ( $(this).val() == 1 )
            {
                searchable_advanced_fields_parents.removeClass('hide');
            }
            else
            {
                searchable_advanced_fields_parents.addClass('hide');
            }
        });
    }
        
    use_dependency.click(function()
    {
        if ( $(this).val() == 0 )
        {
            use_field_items.parents('.control-group').removeClass('hide');
                
            if ( use_field_items.filter(':checked').val() == 0 )
            {
                searchable_advanced_items.parents('.control-group').removeClass('hide');
            }
        }
        else
        {
            use_field_items.parents('.control-group').addClass('hide');
            searchable_advanced_items.parents('.control-group').addClass('hide');
        }
    });
        
    if ( use_field_items.filter(':checked').val() == 1 )
    {
        searchable_advanced_items.parents('.control-group').addClass('hide');
    }
        
    use_field_items.click(function()
    {
        if ( $(this).val() == 0 )
        {
            searchable_advanced_items.parents('.control-group').removeClass('hide');
        }
        else
        {
            searchable_advanced_items.parents('.control-group').addClass('hide');
        }
    });
        
    has_extra = ['alpha', 'numeric', 'alphanumeric', 'custom'];
        
    // Get the default validation rule element.
    var default_validation_rule = adminForm.find('.default-validation-rule');
        
    // Get the extra accepted chars field.
    var extra_accepted_chars = adminForm.find('.extra-accepted-chars');
        
    // Get the regex syntax field.
    var regex_syntax = adminForm.find('.regex-syntax');
        
    // Get the custom validation rule field.
    var custom_validation_rule = adminForm.find('.custom-validation-rule');
        
    // Hide the extra accepted chars field.
    if ( has_extra.indexOf( default_validation_rule.val() ) == -1 )
    {
        extra_accepted_chars.parents('.control-group').addClass('hide');
    }
        
    // Hide the regex syntax field.
    if ( default_validation_rule.val() != 'regex' )
    {
        regex_syntax.parents('.control-group').addClass('hide');
    }
        
    // Hide the custom validation rule field.
    if ( default_validation_rule.val() != 'custom_php' && default_validation_rule.val() != 'custom_characters' )
    {
        custom_validation_rule.parents('.control-group').addClass('hide');
    }
        
    default_validation_rule.change(function()
    {
        value = $(this).val();
            
        extra_accepted_chars.parents('.control-group').addClass('hide');
        regex_syntax.parents('.control-group').addClass('hide');
        custom_validation_rule.parents('.control-group').addClass('hide');
            
        if ( has_extra.indexOf(value) != -1 )
        {
            extra_accepted_chars.parents('.control-group').removeClass('hide');
        }
        else if (value == 'regex')
        {
            regex_syntax.parents('.control-group').removeClass('hide');
        }
        else if ( default_validation_rule.val() == 'custom_php' || default_validation_rule.val() == 'custom_characters' )
        {
            custom_validation_rule.parents('.control-group').removeClass('hide');
        }
    });
        
    // Get the until current year element.
    var until_current_year = adminForm.find('.until-current-year input');
        
    // Get the end year element.
    var end_year = adminForm.find('.end-year');
        
    // Hide the end year field.
    if ( until_current_year.filter(':checked').val() == 1 )
    {
        end_year.parents('.control-group').addClass('hide');
    }
        
    until_current_year.click(function()
    {
        if ( $(this).filter(':checked').val() == 1 )
        {
            end_year.parents('.control-group').addClass('hide');
        }
        else
        {
            end_year.parents('.control-group').removeClass('hide');
        }
    });
        
    // Get the display method element.
    var display_method = adminForm.find('.display-method');
        
    // Get the flow element.
    var flow = adminForm.find('.flow');
        
    if ( display_method.val() != 'radiogroup' )
    {
        flow.parents('.control-group').addClass('hide');
    }
        
    display_method.change(function()
    {
        if ( $(this).val() == 'radiogroup' )
        {
            flow.parents('.control-group').removeClass('hide');
        }
        else
        {
            flow.parents('.control-group').addClass('hide');
        }
    });
        
    var show_help_tip = $( document.getElementById('jform_show_help_tip') ).find('input');
    var help_tip = $( document.getElementById('jform_help_tip') );
        
    if ( show_help_tip.filter(':checked').val() == 0 )
    {
        help_tip.parents('.control-group').addClass('hide');
    }
        
    show_help_tip.click(function()
    {
        if ( $(this).filter(':checked').val() == 1 )
        {
            help_tip.parents('.control-group').removeClass('hide');
        }
        else
        {
            help_tip.parents('.control-group').addClass('hide');
        }
    });
        
    var show_help_text = $( document.getElementById('jform_show_help_text') ).find('input');
    var help_text_parents = $('.help-text').parents('.control-group');
        
    if ( show_help_text.filter(':checked').val() == 0 )
    {
        help_text_parents.addClass('hide');
    }
        
    show_help_text.click(function()
    {
        if ( $(this).filter(':checked').val() == 1 )
        {
            help_text_parents.removeClass('hide');
        }
        else
        {
            help_text_parents.addClass('hide');
        }
    });
        
    var video_size = $( document.getElementById('jform_video_size') );
    var custom_video_size = $('.custom-video-size');
    var custom_video_size_parents = custom_video_size.parents('.control-group');
        
    if ( video_size.val() != 'custom' )
    {
        custom_video_size_parents.addClass('hide');
    }
        
    video_size.change(function()
    {
        if ( video_size.val() == 'custom' )
        {
            custom_video_size_parents.removeClass('hide');
        }
        else
        {
            custom_video_size_parents.addClass('hide');
        }
    });
        
    custom_video_size.keyup(function()
    {
        index = custom_video_size.index(this);
            
        custom_video_size1 = $(this);
        custom_video_size2 = custom_video_size.eq(!index);
            
        ratio = [16, 9];   
            
        custom_video_size2.val( Math.round(custom_video_size1.val() * ratio[Number(!index)] / ratio[index]) );
    });
});