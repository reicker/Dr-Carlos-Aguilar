(function($) {

	window.cGallery = window.cGallery || {};

	var gallery = gallery || {
		modal: null,
		mediaManager: null,
		archive: null
	};

	cGallery.Archive = function() {
		var self = this;

		$( '.gallery-overlay-trigger' ).magnificPopup({
			type: 'ajax',
			ajax: {
				settings: {
					type: 'GET',
					data: { 'view': 'singular' }
				}
			},
			gallery: {
				enabled: true,
				preload: [1,1]
			},
			callbacks: {
				open: function() {
					$( 'body' ).addClass( 'gallery-overlay' );
				},
				close: function() {
					$( 'body' ).removeClass( 'gallery-overlay' );
				},
				lazyLoad: function(item) {
					var $thumb = $( item.el ).data( 'src' );
				},
				parseAjax: function(mfpResponse) {
					mfpResponse.data = $(mfpResponse.data).find( '#main' );
				}
			}
		});

		if ( window.location.hash ) {
			var hash = window.location.hash.substring(1);

			if ( $( 'a[href="' + hash + '"]:first' ).length ) {
				$( $( 'a[href="' + hash + '"]:first' ) ).trigger( 'click' );
			}
		}
	}

	cGallery.MediaManager = function() {
		this.setFrame();
		this.bindEvents();

		return {
			frame: this.getFrame()
		}
	}

	cGallery.MediaManager.prototype.bindEvents = function() {
		var self = this;

		this.frame.on( 'select', function() {
			gallery.modal.attachItems();
		});
	}

	cGallery.MediaManager.prototype.setFrame = function() {
		this.frame = wp.media.frames._frame = wp.media({
			title: listifyListingGallery.gallery_title,
			button: {
				text: listifyListingGallery.gallery_button,
			},
			multiple: true
		});
	}

	cGallery.MediaManager.prototype.getFrame = function() {
		return this.frame
	}

	cGallery.Modal = function() {
		gallery.mediaManager = new cGallery.MediaManager();

		this.bindEvents();
	}

	cGallery.Modal.prototype.bindEvents = function() {
		var self = this;

		$( '.upload-images' ).click(function(e) {
			e.preventDefault();

			gallery.mediaManager.frame.open();
		});

		$( '.listify-add-to-gallery' ).on( 'submit', function(e) {
			e.preventDefault();

			self.saveItems();
		});
	}

	cGallery.Modal.prototype.attachItems = function() {
		var selection = gallery.mediaManager.frame.state().get( 'selection' );
		var ids = [];

		if ( selection.length == 0 ) {
			return;
		}

		$( '#listify-new-gallery-additions, .listify-add-to-gallery' ).fadeIn();

		selection.map( function( attachment ) {
			attachment = attachment.toJSON();

			ids.push(attachment.id);

			$( '#listify-new-gallery-additions' ).append('\
				<li class="gallery-preview-image" style="background-image: url(' + attachment.url + ');"></li>');
		});

		$( '#listify-new-gallery-images' ).val( ids.join( ',' ) );
	}

	cGallery.Modal.prototype.saveItems = function() {
		var ids = $( '#listify-new-gallery-images' ).val();

		var data = {
			_nonce: $( '#_wpnonce' ).val(),
			action: 'listify_add_to_gallery',
			ids: ids,
			post_id: $( '#post_id' ).val()
		}

		$.ajax({
			url: listifySettings.ajaxurl,
			data: data,
			dataType: 'json',
			type: 'POST'
		}).done(function(response) {
			$.magnificPopup.close();

			window.location.href = $( '#gallery-redirect' ).val();
		});
	}

	$(document).on( 'ready', function() {
		if ( $( 'body' ).hasClass( 'single-job_listing' ) && ! $( 'body' ).hasClass( 'single-job_listing-gallery' ) && listifyListingGallery.canUpload === '1' ) {
			gallery.modal = new cGallery.Modal();
		}

		gallery.archive = new cGallery.Archive();
	});

})(jQuery);