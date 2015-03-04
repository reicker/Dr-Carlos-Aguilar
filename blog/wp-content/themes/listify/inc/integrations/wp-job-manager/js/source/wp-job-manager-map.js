(function($) {

	window.cLocator = window.cLocator || {};

	var map = map || {
		Map: {},
		markers: {},
		located: {},
		infoBubble: false,
		bounds: new google.maps.LatLngBounds(),
		geocoder: new google.maps.Geocoder(),
		markerClusterer: false,
		needsFit: true,
		isPanned: false,
		formIndex: 0,
		hasCanvas: true
	};

	/**
	 * Marker
	 *
	 * An individual marker. Passed either an object of arbitrary
	 * data or an existing RichMarker instance. Create the marker
	 * if needed or simply show an existing.
	 */

	cLocator.Marker = function(id, marker) {
		if ( map.markers.hasOwnProperty(id) ) {
			this.data = map.markers[id].meta;
			this.marker = map.markers[id];

			this.marker.setVisible(true);
		} else {
			this.data = marker;
			this.create();
		}

		this.setBounds();

		return {
			marker: this.marker,
			pin: this.getPinContent(),
			infoBubble: this.getInfoBubbleContent()
		}
	}

	cLocator.Marker.prototype.create = function() {
		this.marker = new RichMarker({
			position: new google.maps.LatLng( this.data.lat, this.data.lng ),
			flat: true,
			meta: this.data,
			draggable: false,
			content: this.getPinContent()
		});

		if ( this.marker.meta.title ) {
			this.setInfoBubble();
		}
	}

	cLocator.Marker.prototype.setBounds = function() {
		map.bounds.extend(this.marker.getPosition());
	}

	cLocator.Marker.prototype.setInfoBubble = function() {
		var self = this;

		google.maps.event.addListener(this.marker, 'click', function() {
			window.location.href = this.meta.link;
		});

		google.maps.event.addListener(this.marker, 'mouseover', function() {
			if ( map.infoBubble.isOpen_ && map.infoBubble.getContent() == self.getInfoBubbleContent() ) {
				return;
			}

			map.infoBubble.setContent(self.getInfoBubbleContent());
			map.infoBubble.open(map.Map, self.marker);
		});
	}

	cLocator.Marker.prototype.getInfoBubbleContent = function() {
		var content =
			'<a href="' + this.data.link + '">' +
			this.data.thumb +
			'<h1>' + this.data.title + '</h1>' +
			'<div class="rating">' +
			this.data.rating + this.data.ratingCount +
			'</div>' +
			this.data.address +
			'</a>';

		return content;
	}

	cLocator.Marker.prototype.getPinContent = function() {
		var content =
		'<div class="map-marker type-' + this.data.term + '">' +
			'<i class="' + this.data.icon + '"></i>' +
		'</div>';

		return content;
	}

	/**
	 * Markers
	 *
	 * Passed an object of jQuery objects (via cLocator.Results)
	 * Create a new marker if one does not exist for that result.
	 */

	cLocator.Markers = function() {
		if ( ! map.infoBubble ) {
			map.infoBubble = new InfoBubble({
				backgroundClassName: 'map-marker-info',
				borderRadius: 4,
				padding: 15,
				borderColor: '#ffffff',
				shadowStyle: 0,
				minHeight: 110,
				maxHeight: 130,
				minWidth: 225,
				maxWidth: 275,
				hideCloseButton: true,
				flat: true,
				anchor: RichMarkerPosition.BOTTOM
			});
		}
	}

	cLocator.Markers.prototype.hideAll = function() {
		if ( $.isEmptyObject( map.markers ) ) {
			return;
		}

		// reset bounds
		map.bounds = new google.maps.LatLngBounds();

		// any open InfoBubbles
		map.infoBubble.close();

		// any existing clusters
		if ( map.markerClusterer ) {
			map.markerClusterer.clearMarkers();
		}

		// any visible markers
		$.each( map.markers, function(i, marker) {
			if ( i != 'searched-location' ) {
				map.markers[i].setVisible(false);
			}
		});
	}

	cLocator.Markers.prototype.place = function(results) {
		this.hideAll();

		this.results = results;

		if ( $.isEmptyObject( this.results ) ) {
			return map.Stage.showDefault();
		}

		$.each(this.results, function(i, result) {
			var markerObj = new cLocator.Marker(i, result);

			map.markers[i] = markerObj.marker;

			if ( map.markers[i].getVisible() ) {
				map.markers[i].setMap(map.Map);
			} else {
				map.markers[i].setMap(null);
			}
		});

		if ( map.needsFit === true ) {
			this.fitBounds();
		}

		if ( map.Stage.settings.useClusters ) {
			this.createClusters();
		}
	}

	cLocator.Markers.prototype.fitBounds = function() {
		map.needsFit = false;

		map.Map.fitBounds( map.bounds );
		map.Map.setZoom( map.Map.getZoom() );
	}

	cLocator.Markers.prototype.createClusters = function() {
		var self = this;

		if ( typeof map.markers === 'undefined' ) {
			return;
		}

		map.markerClusterer = new MarkerClusterer(
			map.Map,
			map.markers,
			{
				ignoreHidden: true,
				maxZoom: map.Stage.settings.mapOptions.maxZoom,
				gridSize: parseInt( map.Stage.settings.gridSize )
			}
		);

		google.maps.event.addListener(map.markerClusterer, 'click', function(c) {
			self.clusterOverlay(c);
		});
	}

	cLocator.Markers.prototype.clusterOverlay = function(c) {
		var markers = c.getMarkers();
		var zoom = map.Map.getZoom();

		if ( zoom < map.Stage.settings.mapOptions.maxZoom ) {
			return;
		}

		var p = [];

		for ( i = 0; i < markers.length; i++ ) {
			if ( 'undefined' === markers[i].meta.title ) {
				return;
			}

			var marker = new cLocator.Marker(markers[i].meta.id, markers[i]);

			p.push(marker.infoBubble);
		};

		$.magnificPopup.open({
			items: {
				src: '<div class="popup"><ul class="cluster-items"><li class="map-marker-info">' +
						p.join( '</li><li class="map-marker-info">' ) +
					'</li></ul></div>',
				type: 'inline'
			}
		});
	}

	/**
	 * Results
	 *
	 * Parse a list of HTML elements and extra the necessary information
	 * we will use to help place the results.
	 *
	 * The indididual result objects are not markers at this point, just information.
	 */

	cLocator.Results = function() {

	}

	cLocator.Results.prototype.parse = function() {
		var self = this;

		var section = $( 'div.job_listings' ).eq( map.formIndex );

		this.results = {};
		this.items = section.find( 'ul.job_listings' ).find( '.type-job_listing' );

		$.each( this.items, function(i, el) {
			var $el = $(el);

			if ( ! ( $el.data( 'longitude' ) && $el.data( 'latitude' ) ) ) {
				return;
			}

			var data = {
				id:     $el.attr( 'id' ),
				lat:    $el.data( 'latitude' ),
				lng:    $el.data( 'longitude' ),
				color:  $el.data( 'color' ),
				icon:   $el.data( 'icon' ),
				term:   $el.data( 'term' ),
				title:  $el.find( '.job_listing-title' ).text().trim(),
				link:   $el.find( '.job_listing-clickbox' ).attr( 'href' ),
				thumb:  $el.find( '.job_listing-entry-thumbnail' ).html().trim(),
				rating: $el.find( '.job_listing-rating-stars' )[0].outerHTML,
				ratingCount: $el.find( '.job_listing-rating-count' ).html().trim(),
				address: $el.find( '.job_listing-location-formatted' )[0].outerHTML
			}

			self.results[data.id] = data;
		});

		map.Markers.place(this.results);
	}

	cLocator.Results.prototype.refresh = function() {
		$( 'div.job_listings' ).trigger( 'update_results', [ 1, false ] );
	}

	/**
	 * Geolocation
	 *
	 * Initialize a jQuery UI slider and GeoLocate when a user
	 * drags the maps or enters a location.
	 */

	cLocator.GeoCode = function() {
		this.$useRadius = $( '#use_search_radius' ).is( ':checked' );
		this.$submitButton = $( 'input[name=update_results]' );

		if ( listifyMapSettings.facetwp === '' ) {
			this.initPlaces();
		}

		if ( true === map.hasCanvas ) {
			this.initRadiusSlider();
		}
	}

	cLocator.GeoCode.prototype.updateLocation = function(from) {
		if ( ! from || '' === from ) {
			from = this.findSearchLocation();
		}

		if ( 0 == from ) {
			return;
		}

		this.address = this.location = false;

		if ( 'string' != typeof from ) {
			this.location = from;

			this.setCurrentLocation(this.location);
			this.setLatLng();

			if ( '' == this.findSearchLocation() ) {
				this.getAddress();
			}
		} else {
			this.address = from;

			this.getLatLng();
		}

		/*if ( this.getCurrentLocation() != this.findSearchLocation() ) {
			map.needsFit = true;
		}*/

		if ( '' != this.getCurrentLocation() && true === map.hasCanvas ) {
			this.showRadiusSlider();
			/* this.addMarker(); */
		}
	}

	cLocator.GeoCode.prototype.getLatLng = function() {
		var self = this;

		this.$submitButton.attr( 'disabled', true );

		if ( map.located.hasOwnProperty(this.address) ) {
			self.location = map.located[this.address];

			self.setLatLng();
		}

		map.geocoder.geocode({
			'address': this.address
		}, function(results, status) {
			if ( status == google.maps.GeocoderStatus.OK ) {
				self.location = results[0].geometry.location;

				self.setLatLng();
				self.setCurrentLocation(self.location);
				self.cacheAddress();
			} else {
				console.log( 'Geocode was not successful for the following reason: ' + status );
			}
		});
	}

	cLocator.GeoCode.prototype.getAddress = function() {
		var self = this;

		this.$submitButton.attr( 'disabled', true );

		map.geocoder.geocode({
			'latLng': this.location
		}, function(results, status) {
			if ( status == google.maps.GeocoderStatus.OK ) {
				self.address = results[0].formatted_address;

				$( '#search_location' ).each(function() {
					$(this).val( self.address );
				});

				self.cacheAddress();
			} else {
				console.log( 'Geocode was not successful for the following reason: ' + status );
			}
		});
	}

	cLocator.GeoCode.prototype.setCurrentLocation = function(location) {
		if ( ! location ) {
			location = this.findSearchLocation();
		}

		map.currentLocation = location;
	}

	cLocator.GeoCode.prototype.getCurrentLocation = function() {
		if ( ! map.currentLocation ) {
			this.setCurrentLocation();
		}

		return map.currentLocation;
	}

	cLocator.GeoCode.prototype.findSearchLocation = function() {
		var $location = $( '#search_location' );

		if ( ! $location.length && $( '#search_region' ) ) {
			if ( 0 == $( '#search_region option:selected' ).val() ) {
				$location = 0;
			} else {
				$location = $( '#search_region option:selected' ).text();
			}
		} else {
			$location = $location.val();
		}

		return $location;
	}

	cLocator.GeoCode.prototype.setLatLng = function() {
		$( '#search_lat' ).val( this.location.lat() );
		$( '#search_lng' ).val( this.location.lng() );
	}

	cLocator.GeoCode.prototype.clearLatLng = function() {
		$( '#search_lat' ).val( '' );
		$( '#search_lng' ).val( '' );
	}

	cLocator.GeoCode.prototype.resetLocation = function() {
		map.needsFit = true;

		this.setCurrentLocation(null);
		this.clearLatLng();
		this.hideRadiusSlider();
	}

	cLocator.GeoCode.prototype.cacheAddress = function(address) {
		map.located[this.address] = this.location;
	}

	cLocator.GeoCode.prototype.initPlaces = function() {
		var self = this;

		map.autocomplete = new google.maps.places.Autocomplete( 
			document.getElementById( 'search_location' ),
			listifyMapSettings.autoComplete
		); 

		google.maps.event.addListener(map.autocomplete, 'place_changed', function () {
			var place = map.autocomplete.getPlace();

			self.updateLocation( place.geometry.location );
		});
	}

	cLocator.GeoCode.prototype.addMarker = function() {
		if ( ! this.location ) {
			return;
		}

		var me = new cLocator.Marker( 'searched-location', {
			lat: this.location.lat(),
			lng: this.location.lng(),
			color: '',
			icon: 'ion-android-locate',
			term: ''
		});

		map.markers[ 'searched-location' ] = me.marker;
		map.markers[ 'searched-location' ].setMap(map.Map);
	}

	cLocator.GeoCode.prototype.initRadiusSlider = function() {
		this.$radiusSlider = $( '.search-radius-wrapper' );
		this.$radiusSliderToggle = $( '#use_search_radius' );

		this.setupRadiusSlider();	
		this.toggleRadiusSlider();
	}

	cLocator.GeoCode.prototype.toggleRadiusSlider = function() {
		var self = this;

		this.$radiusSliderToggle.change(function() {
			map.GeoCode.updateLocation();
			self.$radiusSlider.find( '.search-radius-slider' ).toggleClass( 'in-use' );	
		});
	}

	cLocator.GeoCode.prototype.setupRadiusSlider = function() {
	 	this.$radiusSlider.each(function() {
	 		$slider = $(this).find( '#search-radius' );
		 	$input  = $(this).find( '#search_radius' );
		 	$label  = $(this).find( '.search-radius-label .radi' );

		 	var min = parseInt( listifyMapSettings.searchRadius.min );
		 	var max = parseInt( listifyMapSettings.searchRadius.max );
			var avg = Math.round( ( min + max ) / 2 );

			$slider.slider({
				value: avg,
				min: min,
				max: max,
				step: 1,
				slide: function(event, ui) {
					$input.val( ui.value );
					$label.text( ui.value );
				},
				stop: function(event, ui) {
					map.needsFit = true;

					map.Results.refresh();
				}
			});

			$input.val( $slider.slider( 'value' ) );
			$label.text( $slider.slider( 'value' ) );
			
			if ( ! $(this).hasClass( 'in-use' ) ) {
				$(this).hide();
			}
		});
	}

	cLocator.GeoCode.prototype.showRadiusSlider = function() {
		this.$radiusSlider.show();
	}

	cLocator.GeoCode.prototype.hideRadiusSlider = function() {
		this.$radiusSlider.hide();
	}

	/**
	 * Me
	 */

	cLocator.Me = function() {
		if ( listifyMapSettings.facetwp == 1 ) {
		  return;
		}

		this.addButton();
		this.bindEvents();
	}

	cLocator.Me.prototype.addButton = function() {
		$( '.search_location' ).prepend( '<i class="locate-me"></i>' );
	}

	cLocator.Me.prototype.bindEvents = function() {
		var self = this;

		$( '.locate-me' ).on( 'click', function() {
			$(this).addClass( 'loading' );

			self.findMe();
		})
	}

	cLocator.Me.prototype.findMe = function() {
		var self = this;

		if ( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition(function(position) {
				lat = position.coords.latitude;
				lng = position.coords.longitude;

				map.myLocation = new google.maps.LatLng( lat, lng );;
				map.GeoCode.updateLocation( map.myLocation );

				/* if ( true === map.hasCanvas ) { */
				/* 	map.GeoCode.addMarker(); */
				/* } */

				$( '.locate-me' ).removeClass( 'loading' );
			}, function() {
				this.notFound();
			});
		} else {
			this.notFound();
		}
	}

	cLocator.Me.prototype.notFound = function() {
		$( '.locate-me' ).removeClass( 'loading' );
	}

	/**
	 * Stage
	 *
	 * Initialize the map and set up the rest of our functionality.
	 */

	cLocator.Stage = function(settings) {
		var defaults = {
			useClusters: true,
			mapOptions: {
				center: new google.maps.LatLng(41.850033, -87.6500523),
				zoom: 3,
				maxZoom: 17,
				scrollwheel: false,
				panControl: false,
				scaleControl: false,
				overviewMapControl: false
			}
		}

		this.settings = $.extend( true, {}, defaults, settings );
		this.canvas = settings.canvas;

		// Validate some settings
		this.settings.mapOptions.zoom = parseInt( this.settings.mapOptions.zoom );
		this.settings.mapOptions.maxZoom = parseInt( this.settings.mapOptions.maxZoom );

		this.settings.searchRadius.min = parseInt( this.settings.searchRadius.min );
		this.settings.searchRadius.max = parseInt( this.settings.searchRadius.max );

		if ( '' === this.settings.autoFit ) {
			map.needsFit = false;
		}

		$( '#search_location' ).unbind( 'change' );

		if ( typeof this.settings.mapOptions.center === 'string' ) {
			var center = this.settings.mapOptions.center.split( ',' );

			this.settings.mapOptions.center = new google.maps.LatLng( center[0].trim(), center[1].trim() );
		}

		map.GeoCode = new cLocator.GeoCode();
		map.Me      = new cLocator.Me();

		this.toggleView();
		this.bindEvents();

		if ( ! document.getElementById( this.canvas ) ) {
			map.hasCanvas = false;

			return;
		}

		google.maps.event.addDomListener( window, 'load', this.create() );

		map.currentLocation = $( '#search_location' ).val();

		map.Results = new cLocator.Results();
		map.Markers = new cLocator.Markers();
		map.Stage   = this;
	}

	cLocator.Stage.prototype.create = function() {
		map.Map = new google.maps.Map(
			document.getElementById( this.canvas ),
			this.settings.mapOptions
		);
	}

	cLocator.Stage.prototype.showDefault = function() {
		if ( '' == map.GeoCode.getCurrentLocation() ) {
			map.Map.setCenter(map.Stage.settings.mapOptions.center);
			map.Map.setZoom(map.Stage.settings.mapOptions.zoom);
		} else {
			map.Map.setCenter(map.GeoCode.getCurrentLocation());
		}
	}

	cLocator.Stage.prototype.toggleView = function() {
		var $toggle = $( '.archive-job_listing-toggle' );
		var $sections = $( '.content-area, .job_listings-map-wrapper' );

		$toggle.on( 'click', function(e) {
			e.preventDefault();

			$( 'body' ).toggleClass( 'map-toggled' );

			$toggle.removeClass( 'active' );
			$(this).addClass( 'active' );

			var $target = $(this).data( 'toggle' );

			$sections.hide().filter( $( $target ) ).show();

			$('html, body').animate({
				scrollTop: $( '.archive-job_listing-toggle-wrapper' ).offset().top
			}, 1);

			$( '.job_listings-map-wrapper' ).trigger( 'map-toggled' );
		});
	},

	cLocator.Stage.prototype.bindEvents = function() {
		var self = this;

		var $target = $( 'div.job_listings[data-location]' );

		if ( $target.length > 0 ) {

			/**
			 * When the results are updated if the location input has changed update
			 * the current location to query from
			 */
			$target.on( 'update_results', function(event) {
				map.formIndex = $( 'div.job_listings' ).index(this);

				if ( ! map.isPanned && '' != map.GeoCode.findSearchLocation() ) {
					map.isPanned = false;
					map.GeoCode.updateLocation();

					if ( false === map.GeoCode.location ) {
						map.GeoCode.updateLocation();

						return false;
					}
				} else if ( '' === map.GeoCode.findSearchLocation() ) {
					map.GeoCode.resetLocation();
				}
			});

			/** When the results have been loaded parse/place the pins */
			$target.on( 'updated_results', function(event, result) {
				var found = result.found_posts;

				if ( result.found_jobs === false ) {
					found = 0;
				}

				$( '.results-found' ).html(found);

				map.Results.parse();
			});
		}

		/* When more jobs are loaded refit the bounds */
		$( '.load_more_jobs' ).click(function(e) {
			map.needsFit = true;
		});

		/** When FacetWP results have been loaded parse/place the pins */
		$(document).on( 'facetwp-loaded', function() {
			map.needsFit = true;

			map.Results.parse();
		});

		/** When the map-only mobile view has been toggled redraw the map */
		$( '.job_listings-map-wrapper' ).on( 'map-toggled', function() {
			google.maps.event.trigger(map.Map, 'resize' );
			map.Map.fitBounds( map.bounds );
		});

		/** When the map is clicked hide all info bubbles */
		google.maps.event.addListener(map.Map, 'click', function() {
			map.action = 'click';

			map.infoBubble.close();
		});

		/** When the map is dragged query again based on map center */
		google.maps.event.addListener(map.Map, 'dragend', function() {
			if ( '' === map.GeoCode.findSearchLocation() ) {
				return;
			}

			map.needsFit = false;
			map.isPanned = true;

			map.GeoCode.updateLocation(map.Map.getCenter());
			map.Results.refresh();
		});

		/** When the map is zoomed close any infobubbles */
		google.maps.event.addListener(map.Map, 'zoom_changed', function() {
			map.infoBubble.close();
		});

		$( 'form.job_filters' ).on( 'submit', function(e) {
			e.preventDefault();

			map.needsFit = true;

			$target.trigger( 'update_results', [ 1, false ] );
		});
	}

	/**
	 *  Get the height of the anchor
	 *
	 *  This function is a hack for now and doesn't really work that good, need to
	 *  wait for pixelBounds to be correctly exposed.
	 *  @private
	 *  @return {number} The height of the anchor.
	 */
	InfoBubble.prototype.getAnchorHeight_ = function() {
	 	return 55;
	};

	$(document).on( 'ready', function() {
		new cLocator.Stage( listifyMapSettings );
	});

})(jQuery);
