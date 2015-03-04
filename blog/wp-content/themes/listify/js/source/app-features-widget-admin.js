window.cFeatures = window.cFeatures || {};

(function( window, $, _, wp, wpWidgets, undefined ) {

	var features = {
		added: []
	};

	var _template = $( '#widget-area-home #featureTemplate' ).html();

	cFeatures.Panel = function() {
		this.setFeatures(cFeaturesExisting);

		this.bindEvents();
	}

	cFeatures.Panel.prototype.setFeatures = function(toAdd) {
		features.added = [];
		$( '#widget-area-home #feature-list' ).html( '' );

		$.each(toAdd, function(i, data) {
			new cFeatures.Feature(i, data);
		});

		$( '#widget-area-home #feature-list' ).sortable();
	}

	cFeatures.Panel.prototype.bindEvents = function() {
		$( '#widget-area-home' ).on( 'click', '.button-add-feature', function(e) {
			e.preventDefault();

			new cFeatures.Feature(features.added.length + 1, {});
		});
	}

	cFeatures.Feature = function(i, data) {
		this.data = $.extend( {}, {
			'i': i,
			'title': '',
			'media': '',
			'description': ''
		}, data );

		this.set();
		this.add(this.data.i);

		return features.added.push(this.data);
	}

	cFeatures.Feature.prototype.set = function() {
		var _template = $( '#widget-area-home #featureTemplate' ).html();
		var widgetNumber = $( '#widget-area-home #featureTemplate' ).parent().parent().find('input.multi_number').val();

		_template = _template.replace( /__i__/g, widgetNumber );

		var template = _.template( _template );

		this.html = template( this.data );
	}

	cFeatures.Feature.prototype.add = function() {
		var $stage = $( '#widget-area-home #feature-list' );

		$stage.append( this.html );

		this.bindEvents();
	}

	cFeatures.Feature.prototype.bindEvents = function() {
		var self = this;

		$( '#feature-' + this.data.i ).on( 'click', '.button-remove-feature', function(e) {
			e.preventDefault();

			$(this).parent().remove();
		});

		new cImageWidget.MediaManager({
			target: $( '#feature-' + this.data.i ).data( 'key' ) + '-' + this.data.i + '-media'
		});
	}

	$(document).on( 'ready', function() {
		features.panel = new cFeatures.Panel();
	});

	$(document).on( 'widget-updated widget-added', function(widget) {
		features.panel.setFeatures(cFeaturesExisting);
	});

})( this, jQuery, _, wp, wpWidgets );
