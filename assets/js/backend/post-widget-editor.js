jQuery( window ).on(
	'elementor:init',
	function () {
		function updateControlVisibility($panelElement, $closestElement) {
			$panelElement.attr( 'class', '' ); // Clear all classes

			const elementsMap = {
				'post-pin': 'show-bookmark',
				'post-title': 'show-title',
				'post-taxonomy': 'show-taxonomy',
				'post-content': 'show-content',
				'post-excerpt': 'show-content',
				'post-custom-field': 'show-custom-field',
				'post-read-more': 'show-read-more',
				'post-meta': 'show-meta',
				'edit-options': 'show-edit',
				'product-price': 'show-price',
				'product-rating': 'show-rating',
				'product-buy-now': 'show-buy',
				'product-badge': 'show-badge'
			};

			Object.entries( elementsMap ).forEach(
				([selector, className]) => {
					const $element = $closestElement.find( `.${selector}` );
					if ($element.length > 0) {
						$panelElement.addClass( className );
					}
				}
			);
		}

		window.elementor.hooks.addAction(
			'panel/open_editor/widget/post-widget',
			function (panel, model, view) {
				const $panelElement   = jQuery( panel.$el );
				const $closestElement = view.$el.closest( '.elementor-widget-post-widget' );
				updateControlVisibility( $panelElement, $closestElement );

				const observer = new MutationObserver(
					function (mutations) {
						updateControlVisibility( $panelElement, $closestElement );
					}
				);

				const observerOptions = {
					subtree: true,
					childList: true,
				};

				observer.observe( $closestElement[0], observerOptions );
			}
		);
	}
);