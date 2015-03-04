(function($) {
	'use strict';

	var listifyFacetWP = {
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
				self.sorting();
			});
		},

		sorting: function() {
			var self = this;

			this.cache.$document.on( 'facetwp-loaded facetwp-refresh', function() {
				$( 'select' ).wrap( '<span class="select"></span>' );
			});
		}
	};

	listifyFacetWP.init();

})(jQuery);