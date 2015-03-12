(function($)
{
	$.rsMap = function(el, options)
	{
		var base = this;
			
		base.$el = $(el);
		base.el = el;
			
		base.$el.data("rsMap", base);
			
		base.init = function()
		{
			base.options = $.extend({},$.rsMap.defaultOptions, options);
				
			// Initialize the Geocoder.
			base.geocoder = new google.maps.Geocoder();
				
			base.inputAddress = $(base.options.inputAddress);
			base.inputLat = $(base.options.inputLat);
			base.inputLng = $(base.options.inputLng);
				
			base.initMap();
			base.initMarker();
			base.initPos();
			base.setMarkerOnDragEnd();
			base.inputAddressOnKeyUp();
			base.inputLatLngOnChange();
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
			
		// Initialize marker.
		base.initMarker = function()
		{
			if (!base.options.markerDisplay)
				return;
				
			base.marker = new google.maps.Marker(
			{
				map: base.map,
				draggable: base.options.markerDraggable,
			});
		}
			
		// Initialize the map and marker position.
		base.initPos = function()
		{
			// 1st priority: the lat and lng options.
			if ( parseFloat(base.options.lat) || parseFloat(base.options.lng) )
			{
				base.setPos( new google.maps.LatLng(base.options.lat, base.options.lng) );
			}
			// 2nd priority: the address option.
			else if (base.options.address)
			{
				base.geocoder.geocode( {address: base.options.address}, function(results, status) 
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						base.setPos(results[0].geometry.location);
					}
				});
			}
			// 3rd priority: the lat and lng input values.
			else if ( parseFloat( base.inputLat.val() ) || parseFloat( base.inputLng.val() ) )
			{
				base.setPos( new google.maps.LatLng( base.inputLat.val(), base.inputLng.val() ) );
			}
			// 4th priority: the address input value.
			else if ( base.inputAddress.val() )
			{
				base.geocoder.geocode( {address: base.inputAddress.val()}, function(results, status) 
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						base.setPos(results[0].geometry.location);
					}
				});
			}
			else
			{
				base.setPos( new google.maps.LatLng(0, 0) );
			}
		};
			
		// Set the map and marker positon.
		base.setPos = function(latLng)
		{
			base.map.setCenter(latLng);
				
			if (base.options.markerDisplay)
			{
				base.marker.setPosition(latLng);
			}
		}
			
		// Add a on drag end event to the marker.
		base.setMarkerOnDragEnd = function()
		{
			if (!base.options.markerDisplay)
				return;
				
			google.maps.event.addListener(base.marker, 'dragend', function() 
			{
				base.inputLat.val( base.marker.getPosition().lat() );
				base.inputLng.val( base.marker.getPosition().lng() );
					
				base.geocoder.geocode({latLng: base.marker.getPosition()}, function(results, status) 
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						base.inputAddress.val(results[0].formatted_address);
							
						if (typeof base.options.markerOnDragEnd != 'function')
							return;
							
						// Call the user defined on drag event.
						base.options.markerOnDragEnd(results[0]);
					}
					else
					{
						base.inputAddress.val('');
					}
				});
			});
		}
			
		// Add a on key up event to the address input.
		base.inputAddressOnKeyUp = function()
		{
			base.inputAddress.bind('keyup', function()
			{
				base.inputAddress.parent().find('.' + base.options.placeholdersWrapperClass).remove();
					
				if ( $.trim( base.inputAddress.val() ) )
				{	
					base.geocoder.geocode( {address: base.inputAddress.val()}, function(results, status)
					{
						if (status == google.maps.GeocoderStatus.OK)
						{
							results_wrapper = $('<div class="' + base.options.placeholdersWrapperClass + '"><ul class="' + base.options.placeholdersClass + '"></ul></div>');
							base.inputAddress.after(results_wrapper);
								
							$(results).each(function(index, item)
							{
								li = $('<li>' + item.formatted_address + '</li>').click(function()
								{
									base.inputAddress.val(item.formatted_address);
									base.inputLat.val( item.geometry.location.lat() );
									base.inputLng.val( item.geometry.location.lng() );
										
									base.setPos(item.geometry.location);
										
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
				else
				{
					base.inputLat.val(0);
					base.inputLng.val(0);
						
					base.setPos( new google.maps.LatLng(0, 0) );
				}
			});
		}
			
		// 	Add a on change event to the lat and lng inputs.
		base.inputLatLngOnChange = function()
		{
			inputLatLngOnChange = function()
			{
				base.geocoder.geocode({latLng: base.marker.getPosition()}, function(results, status) 
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						base.inputAddress.val(results[0].formatted_address);
					}
					else
					{
						base.inputAddress.val('');
					}
				});
					
				base.setPos( new google.maps.LatLng( base.inputLat.val(), base.inputLng.val() ) );
			}
				
			base.inputLat.bind('keyup change', inputLatLngOnChange);
			base.inputLng.bind('keyup change', inputLatLngOnChange);
		}
			
		base.init();
	};
		
	$.rsMap.defaultOptions = {
		address: '',
		lat: null,
		lng: null,
		zoom: 5,
		mapType: google.maps.MapTypeId.ROADMAP, // See: https://developers.google.com/maps/documentation/javascript/maptypes#BasicMapTypes
		streetViewControl: false,
		scrollwheel: false,
		zoomControl: true,
		inputAddress: null,
		inputLat: null,
		inputLng: null,
		markerDisplay: true,
		markerDraggable: false,
		markerOnDragEnd: null,
		placeholdersWrapperClass: 'rsdir-placeholders-wrapper',
		placeholdersClass: 'rsdir-placeholders',
	};
		
	$.fn.rsMap = function(options)
	{
		return this.each(function()
		{
			(new $.rsMap(this, options));
		});
	};
})(jQuery);