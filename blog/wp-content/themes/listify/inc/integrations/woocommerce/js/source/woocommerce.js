(function($) {
	'use strict';

	var listifyWooCommerce = {
		cache: {
			$document: $(document),
			$window: $(window)
		},

		init: function() {
			this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			this.cache.$document.on( 'ready', function() {
				self.initRatings();
				self.initSocialLogin(false);
			});

			$( 'body' ).on( 'popup-trigger-ajax', function() {
				self.initSocialLogin( $( '.popup' ) );
			});
		},

		initRatings: function() {
			$( '.comment-form-rating' ).on( 'hover click', '.stars span a', function() {
				$(this)
					.siblings()
						.removeClass( 'hover' )
						.end()
					.prevAll()
						.addClass( 'hover' );
			});
		},

		initSocialLogin: function(search) {
			if ( ! search ) {
				search = $( 'body' );
			}

			var $social = search.find( $( '.woocommerce .wc-social-login' ) );

			if ( ! $social.length ) {
				return;
			}

			var $clone = $social.clone();
			var $container = $social.parents( '.woocommerce' );

			$social.remove();
			$container.append( $clone );
		}
	};

	listifyWooCommerce.init();

})(jQuery);