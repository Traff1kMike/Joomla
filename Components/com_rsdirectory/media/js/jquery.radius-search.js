;(function($)
{
    $.rsRadiusSearch = function(el, options)
	{
        var base = this;
			
        base.$el = $(el);
        base.el = el;
			
        base.$el.data("rsRadiusSearch", base);
			
        base.init = function()
		{
            base.options = $.extend({},$.rsRadiusSearch.defaultOptions, options);
				
			// Initialize the Geocoder.
			base.geocoder = new google.maps.Geocoder();
				
			base.form = $(base.options.form);
			base.location = base.form.find('[name="location"]');
			base.radius = base.form.find('[name="radius"]');
			base.unit = base.form.find('[name="unit"]');
			base.loader = base.form.find('.loader');
				
			base.circle = null;
			base.markers = [];
			base.cache = [];
				
			base.initMap();
			base.setPos();
			base.locationOnKeyUp();
			base.formOnSubmit();
        };
			
		// Initialize map.
		base.initMap = function()
		{
			base.map = new google.maps.Map(el,
			{
				zoom: base.options.zoom,
				mapTypeId: base.options.mapType,
				streetViewControl: base.options.streetViewControl,
				scrollwheel: base.options.scrollwheel,
				zoomControl: base.options.zoomControl,
			});
		}
			
		// Set the map center and marker position.
		base.setPos = function()
		{
			if (base.options.address)
			{
				base.geocoder.geocode( {address: base.options.address}, function(results, status) 
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						base.map.setCenter(results[0].geometry.location);
					}
				});
			}
		};
			
		// Add a key up event to the address input.	
		base.locationOnKeyUp = function()
		{
			base.location.bind('keyup', function()
			{
				base.location.parent().find('.' + base.options.placeholdersWrapperClass).remove();
					
				if ( $.trim( base.location.val() ) )
				{	
					base.geocoder.geocode( {address: base.location.val()}, function(results, status)
					{
						if (status == google.maps.GeocoderStatus.OK)
						{
							results_wrapper = $('<div class="' + base.options.placeholdersWrapperClass + '"><ul class="' + base.options.placeholdersClass + '"></ul></div>');
							base.location.after(results_wrapper);
								
							$(results).each(function(index, item)
							{
								li = $('<li>' + item.formatted_address + '</li>').click(function()
								{
									base.location.val(item.formatted_address);
									base.map.setCenter(item.geometry.location);
										
									results_wrapper.remove();
								});
									
								results_wrapper.find('ul').append(li);  
							});
								
							$(document).click( function(event)
							{ 
								if( $(event.target).parents().index(results_wrapper) == -1 )
								{
									results_wrapper.remove();
								}
							});
						}
					});
				}
			});
		}
			
		// Set the form on submit event.
		base.formOnSubmit = function()
		{	
			base.form.submit(function(e)
			{
				e.preventDefault();
					
				errors = false;
					
				// Remove errors.
				base.form.find('.error').removeClass('error');
					
				// Validate location.
				if ( !$.trim( base.location.val() ) )
				{
					base.location.parents('.control-group').addClass('error');
					errors = true;
				}
					
				// Validate radius.
				if ( !/^\d+$/.test( base.radius.val() ) || parseInt( base.radius.val(), 10 ) <= 0 )
				{
					base.radius.parents('.control-group').addClass('error');
					errors = true;
				}
					
				// Stop the execution of the function if there are errors.
				if (errors)
				{
					$('html,body').animate({scrollTop: base.form.offset().top});
					return;
				}
					
				base.loader.removeClass('hide');
					
				base.clearMarkers();
					
				$.ajax(
				{
					type: "POST",
					dataType: "json",
					url: base.form.attr('action'),
					data: base.form.serialize(),
					success: function(json)
					{
						try
						{
							// json is valid if json.length does not throw an error.
							json.length;
								
							base.initInfoWindow();
								
							// Create the markers.
							$(json).each(function(i, element)
							{
								$(element.coords).each(function(j, coords)
								{
									base.createMarker(element.id, coords);
								});
							});
						}
						catch(e)
						{	
						}
							
						base.process();
							
						base.loader.addClass('hide');
					}
				});
			});
		}
			
		// Initialize the info window.	
		base.initInfoWindow = function()
		{
			base.infoWindow = new google.maps.InfoWindow();	
		}
			
		// Create marker.	
		base.createMarker = function(id, coords)
		{
			var marker = new google.maps.Marker(
			{
				position: new google.maps.LatLng(coords.lat, coords.lng),
				map: base.map,
			});
				
			google.maps.event.addListener(marker, 'click', function()
			{
				if (base.cache[id] == undefined)
				{
					infoWindow = $('<div class="' + base.options.infoWindowClass + '" style=" width: ' + base.options.infoWindowWidth + 'px; height: ' + base.options.infoWindowHeight + 'px;">' + Joomla.JText._('COM_RSDIRECTORY_LOADING') + '</div>')
						
					base.infoWindow.setContent(infoWindow[0]);
					base.infoWindow.open(base.map, marker);
						
					// Serialize the form data.
					data = base.form.serializeArray();
						
					// Add the entry id.
					data.push({name: 'id', value: id});
						
					// Change the task.
					for (i in data)
					{
						if (data[i].name == 'task')
						{
							data[i].value = 'radius.getInfoWindow'
						}
					}
						
					$.ajax(
					{
						type: "POST",
						url: base.form.attr('action'),
						data: data,
						success: function(response)
						{
							infoWindow = $(response);
								
							rating = infoWindow.find('.rating');
								
							if (rating.length > 0 && $.fn.raty)
							{
								rating.raty(
								{
									path: rsdir.root + 'media/com_rsdirectory/images/raty/',
									readOnly: true,
									score: function()
									{
										return $(this).attr('data-rating');
									},
								});
							}
								
							eventName = 'click.radiusSearchFav';
								
							infoWindow.find('.fav').off(eventName).on(eventName, function()
							{
								addToFavorites( $(this) );
							});
								
							base.cache[id] = infoWindow[0];
								
							base.infoWindow.setContent(infoWindow[0]);
						}
					});
				}
				else
				{
					base.infoWindow.setContent(base.cache[id]);
					base.infoWindow.open(base.map, marker);	
				}
			});
				
			base.markers.push(marker);
		}
			
		// Clear markers.
		base.clearMarkers = function()
		{
			for (var i = 0; i < base.markers.length; i++)
			{
				base.markers[i].setMap(null);
			}
				
			base.markers = [];
		}
			
		base.process = function()
		{
			var unit_value = base.unit.val() == 'miles' ? 1609.34 : 1000;
				
			var radius = parseInt( base.radius.val(), 10) * unit_value;
				
			base.geocoder.geocode( {'address': base.location.val()}, function(results, status)
			{
				if (status == google.maps.GeocoderStatus.OK)
				{
					base.map.setCenter(results[0].geometry.location);
					var searchCenter = results[0].geometry.location;
						
					if (base.circle)
					{
						base.circle.setMap(null);
					}
						
					base.circle = new google.maps.Circle(
					{
						center:searchCenter,
						radius: radius,
						fillOpacity: 0.35,
						fillColor: "#FF0000",
						map: base.map
					});
						
					var bounds = new google.maps.LatLngBounds();
					var foundMarkers = 0;
						
					for (var i = 0; i < base.markers.length; i++)
					{
						if ( google.maps.geometry.spherical.computeDistanceBetween( base.markers[i].getPosition(), searchCenter ) < radius )
						{
							bounds.extend( base.markers[i].getPosition() );
							base.markers[i].setMap(base.map);
							foundMarkers++;
						}
						else
						{
							base.markers[i].setMap(null);
						}
					}
						
					if (foundMarkers > 0)
					{
						if ( bounds.getNorthEast().equals( bounds.getSouthWest() ) )
						{
							var extendPoint1 = new google.maps.LatLng( bounds.getNorthEast().lat() + 0.001, bounds.getNorthEast().lng() + 0.001 );
							var extendPoint2 = new google.maps.LatLng( bounds.getNorthEast().lat() - 0.001, bounds.getNorthEast().lng() - 0.001 );
							bounds.extend(extendPoint1);
							bounds.extend(extendPoint2);
						}
					 
						base.map.fitBounds(bounds);
					}
					else
					{
						base.map.fitBounds( base.circle.getBounds() );
					}
				}
			});
		}
			
        base.init();
    };
		
    $.rsRadiusSearch.defaultOptions = {
		form: null,
		address: 'Statue of Liberty National Monument, New York, NY 10004, United States',
		zoom: 5,
		mapType: google.maps.MapTypeId.ROADMAP, // See: https://developers.google.com/maps/documentation/javascript/maptypes#BasicMapTypes
		streetViewControl: false,
		scrollwheel: false,
		zoomControl: true,
		placeholdersWrapperClass: 'rsdir-placeholders-wrapper',
		placeholdersClass: 'rsdir-placeholders',
		infoWindowClass: 'rsdir-info-window media',
		infoWindowWidth: 300,
		infoWindowHeight: 120,
    };
		
    $.fn.rsRadiusSearch = function(options)
	{
        return this.each(function()
		{
            (new $.rsRadiusSearch(this, options));
        });
    };
		
})(jQuery);