(function ($) {
	"use strict";
	$( window ).on(
		'elementor/frontend/init',
		function () {
			let dynamic_handler = '';

			if ($( '.elementor-widget-filter-widget' ).length) {
				dynamic_handler = 'filter-widget';
			} else {
				dynamic_handler = 'search-bar-widget';
			}

			const FilterWidgetHandler = elementorModules.frontend.handlers.Base.extend(
				{

					bindEvents() {
						this.getAjaxFilter();
					},

					getAjaxFilter() {
						let ajaxInProgress = false;

						if ($( ".cwm-select2" )[0]) {
							var parentElement = $( ".cwm-select2" );
							$( ".cwm-select2 select" ).prop( 'multiple', false ).select2(
								{
									dropdownParent: parentElement,
								}
							);

							$( ".cwm-select2" ).css(
								{
									"visibility": "visible",
									"opacity": "1",
									"transition": "opacity 0.3s ease-in-out"
								}
							);
						}

						if ($( ".cwm-multi-select2" )[0]) {
							var parentElement = $( ".cwm-multi-select2" );

							$( ".cwm-multi-select2 select" ).prop( 'multiple', true ).select2(
								{
									dropdownParent: parentElement,
								}
							);

							$( ".cwm-multi-select2 select" ).val( null ).trigger( 'change' );

							$( ".cwm-multi-select2" ).css(
								{
									"visibility": "visible",
									"opacity": "1",
									"transition": "opacity 0.3s ease-in-out"
								}
							);

							function updatePlusSymbol() {
								var $rendered = $( ".cwm-multi-select2 .select2-selection__rendered" );

								$rendered.find( ".select2-selection__e-plus-button" ).remove();

								if ($( ".cwm-multi-select2 select" ).val().length === 0) {
									$rendered.prepend( '<span class="select2-selection__choice select2-selection__e-plus-button">+</span>' );
								}
							}

							updatePlusSymbol();
							$( ".cwm-multi-select2 select" ).on( 'change', updatePlusSymbol );
						}

						const filterWidget = this.$element.find( '.filter-container' );
						let pageID         = window.elementorFrontendConfig.post.id;

						const filterSetting = this.$element.data( 'settings' );

						// Fix for backend
						if ( ! filterSetting) {
							return;
						}

						const targetPostWidget = filterSetting.target_selector;

						if ( ! targetPostWidget || ! targetPostWidget.length) {
							return;
						}

						let groupLogic   = filterSetting.group_logic,
						dynamicFiltering = filterSetting.dynamic_filtering,
						scrollToTop      = filterSetting.scroll_to_top,
						post_type        = filterSetting.filter_post_type;

						const targetSelector = $( targetPostWidget ),
						widgetID             = targetSelector.data( 'id' ),
						originalState        = targetSelector.html(),
						loader               = targetSelector.find( '.loader' ),
						pagination           = targetSelector.find( '.pagination' );

						var maxPage = pagination.data( 'max-page' );

						let paginationType = '';

						var paginationNext        = '';
						var filterWidgetObservers = filterWidgetObservers || {};

						const postWidgetSetting = targetSelector.data( 'settings' );
						if (postWidgetSetting && (postWidgetSetting.pagination || postWidgetSetting.pagination_type)) {
							paginationType         = postWidgetSetting.pagination || postWidgetSetting.pagination_type;
							var size               = postWidgetSetting.scroll_threshold && postWidgetSetting.scroll_threshold.size ? postWidgetSetting.scroll_threshold.size : 0;
							var unit               = postWidgetSetting.scroll_threshold && postWidgetSetting.scroll_threshold.unit ? postWidgetSetting.scroll_threshold.unit : 'px';
							var infinite_threshold = size + unit;
						} else {
							var infinite_threshold = '0px';
						}

						let currentPage = 1;

						if (pageID === 0 || pageID === undefined) {
							pageID = $( 'div[data-elementor-id]' ).first().data( 'elementor-id' );
							if (pageID === 0 || pageID === undefined) {
								pageID = $( 'main div:first' ).data( 'elementor-id' );
							}
						}

						function debounce(func, delay) {
							let timeoutId;
							return function () {
								const context = this;
								const args    = arguments;
								clearTimeout( timeoutId );
								timeoutId = setTimeout(
									() => {
                                    func.apply( context, args );
									},
									delay
								);
							};
						}

						const isSubmitPresent = filterWidget.find( '.submit-form' ).length > 0;

						let isInteracting = false;
						let interactionTimeout;

						// Detect interaction on both desktop and mobile
						filterWidget.on(
							'mousedown keydown touchstart',
							'form.form-tax',
							function () {
								isInteracting = true;
								clearTimeout( interactionTimeout );
							}
						);

					filterWidget.on(
						'mouseup keyup touchend',
						'form.form-tax',
						function () {
							interactionTimeout = setTimeout(
								() => {
									isInteracting  = false;
								},
								700
							);
						}
					);

					filterWidget.on(
						'change keypress',
						'form.form-tax, .cwm-numeric-wrapper input',
						debounce(
							function (e) {
								if ( ! isSubmitPresent && ! isInteracting && (e.type !== 'keypress' || e.which == 13)) {
									resetURL();
									targetSelector.addClass( 'filter-initialized' );
									targetSelector.removeClass( 'filter-active' );
									get_form_values();
								}
							},
							700
						)
					);

					// filterWidget.on('submit', 'form', function() {
					// resetURL();
					// targetSelector.addClass('filter-initialized');
					// targetSelector.removeClass('filter-active');
					// get_form_values();
					// return false;
					// });

					$( document ).on(
						'change',
						'form.form-order-by',
						function () {
							targetSelector.addClass( 'filter-initialized' );
							targetSelector.removeClass( 'filter-active' );
							get_form_values();
						}
					);

					$( document ).off( 'submit', 'form.search-post' ).on(
						'submit',
						'form.search-post',
						function () {
							const formAction = $( this ).attr( 'action' );
							const currentUrl = window.location.href;
							resetURL();
							targetSelector.addClass( 'filter-initialized' );
							targetSelector.removeClass( 'filter-active' );
							get_form_values();
							if (formAction === currentUrl) {
								return false;
							}
						}
					);

					const currentUrl = window.location.href;
					if (currentUrl.includes( '?search=' )) {
						get_form_values();
					}

					function getPageNumber(url) {
						var match;
						if (url.includes( "?page=" )) {
							match = url.match( /\?page=(\d+)/ );
						} else if (url.includes( "?paged=" )) {
							match = url.match( /\?paged=(\d+)/ );
						} else if (url.match( /\/(\d+)(\/|$)/ )) {
							match = url.match( /\/(\d+)(\/|$)/ );
						} else {
							match = url.match( /[?&](\w+)=\d+/ );
						}
						if ( ! match) {
							match = url.match( /(\d+)(\/|$)/ );
						}
						return match ? match[1] : null;
					}

					function resetURL() {
						let originalURL = window.location.origin + window.location.pathname;
						history.replaceState( null, '', originalURL );
					}

					$( document ).on(
						'click',
						targetPostWidget + ' .pagination-filter a',
						function (e) {
							e.preventDefault();
							var url   = $( this ).attr( 'href' );
							var paged = getPageNumber( url );
							get_form_values( paged );
						}
					);

					$( document ).on(
						'click',
						targetPostWidget + ' .load-more-filter',
						function (e) {
							e.preventDefault();
							var url = targetSelector.find( '.e-load-more-anchor' ).data( 'next-page' );
							if (url) {
								var paged = getPageNumber( url );
								get_form_values( paged );
							} else {
								$( document ).find( targetPostWidget + ' .pagination-filter a.next' ).click();
								currentPage = currentPage + 1;

								var loadMoreButton = targetSelector.find( '.load-more' );
								loadMoreButton.text( 'Loading...' );
								loadMoreButton.prop( 'disabled', true );
							}
						}
					);

					function post_count() {
						let postCount = targetSelector.find( '.post-container' ).data( 'total-post' );

						if (postCount === undefined) {
							postCount = 0;
						}

						postCount = Number( postCount );

						$( '.filter-post-count .number' ).text( postCount );
					}

					function get_form_values(paged) {
						var postContainer = targetSelector.find( '.post-container' ),
						order             = '',
						order_by          = '',
						order_by_meta     = '';

						var searchQuery = $( 'form.search-post' ).find( 'input' ).val();
						var urlParams   = new URLSearchParams( window.location.search );
						if (urlParams.has( 'search' )) {
							searchQuery = urlParams.get( 'search' );
						}

						$( '.form-order-by select option:selected' ).each(
							function () {
								var self      = $( this );
								order         = self.data( 'order' ),
								order_by_meta = self.data( 'meta' ),
								order_by      = self.val();
							}
						);

						targetSelector.removeClass( 'e-load-more-pagination-end' );

						postContainer.addClass( 'load' );
						targetSelector.addClass( 'load' );

						if (postContainer.hasClass( 'shortcode' ) || postContainer.hasClass( 'template' )) {
							loader.fadeIn();
						}

						var category          = [];
						var custom_field      = [];
						var custom_field_like = [];
						var numeric_field     = [];

						$( '.cwm-taxonomy-wrapper input:checked, .cwm-custom-field-wrapper input:checked' ).each(
							function () {
								var self        = $( this );
								var targetArray = self.closest( '.cwm-taxonomy-wrapper' ).length ? category : custom_field;
								targetArray.push(
									{
										'taxonomy': self.data( 'taxonomy' ),
										'terms': self.val(),
										'logic': self.closest( 'div' ).data( 'logic' )
									}
								);
							}
						);

						$( '.cwm-custom-field-wrapper input.input-text' ).each(
							function () {
								var self = $( this );
								if ( self.val() ) {
									custom_field_like.push(
										{
											'taxonomy': self.data( 'taxonomy' ),
											'terms': self.val(),
											'logic': self.closest( 'div' ).data( 'logic' )
										}
									);
								}
							}
						);

						$( '.cwm-taxonomy-wrapper select option:selected, .cwm-custom-field-wrapper select option:selected' ).each(
							function () {
								var self = $( this );
								if ( self.val() ) {
									var targetArray = self.closest( '.cwm-taxonomy-wrapper' ).length ? category : custom_field;
									targetArray.push(
										{
											'taxonomy': self.data( 'taxonomy' ),
											'terms': self.val(),
											'logic': self.closest( 'div' ).data( 'logic' )
										}
									);
								}
							}
						);

						$( '.cwm-numeric-wrapper input' ).each(
							function () {
								var self        = $( this );
								var initial_val = self.data( 'base-value' );

								if (self.val() === '' || self.val() != initial_val) {
									if (self.val() === '') {
										self.val( initial_val );
									}

									var _class = self.attr( "class" ).split( ' ' )[0];

									$( '.cwm-numeric-wrapper' ).find( 'input' ).each(
										function () {
											var _this = $( this );
											if (_this.hasClass( _class )) {
												numeric_field.push(
													{
														'taxonomy': _this.data( 'taxonomy' ),
														'terms': _this.val(),
														'logic': _this.closest( 'div' ).data( 'logic' )
													}
												);
											}
										}
									);
								}
							}
						);

						function reduceFields(fields) {
							return fields.reduce(
								function (o, cur) {
									var occurs = o.reduce(
										function (n, item, i) {
											return (item.taxonomy === cur.taxonomy) ? i : n;
										},
										-1
									);

									if (occurs >= 0) {
										o[occurs].terms = o[occurs].terms.concat( cur.terms );
									} else {
										var obj = {
											taxonomy: cur.taxonomy,
											terms: [cur.terms],
											logic: cur.logic
										};
										o       = o.concat( [obj] );
									}
									return o;
								},
								[]
							);
						}

						var taxonomy_output          = reduceFields( category );
						var custom_field_output      = reduceFields( custom_field );
						var custom_field_like_output = reduceFields( custom_field_like );
						var numeric_output           = reduceFields( numeric_field );

						$.ajax(
							{
								type: "POST",
								url : ajax_var.url,
								async: true,
								data: {
									action: 'post_filter_results',
									widget_id: widgetID,
									page_id: pageID,
									group_logic: groupLogic,
									search_query: searchQuery,
									taxonomy_output: taxonomy_output,
									dynamic_filtering: dynamicFiltering,
									custom_field_output: custom_field_output,
									custom_field_like_output: custom_field_like_output,
									numeric_output: numeric_output,
									post_type: post_type,
									order: order,
									order_by: order_by,
									order_by_meta: order_by_meta,
									paged: paged,
									archive_type: $( '[name="archive_type"]' ).val(),
									archive_post_type: $( '[name="archive_post_type"]' ).val(),
									archive_taxonomy: $( '[name="archive_taxonomy"]' ).val(),
									archive_id: $( '[name="archive_id"]' ).val(),
									nonce: ajax_var.nonce,
								},
								success: function (data) {
									var response = JSON.parse( data );

									var content = response.html,
									base        = window.location.href;

									if (data === '0') {
										targetSelector.off();

										targetSelector.html( originalState ).fadeIn().removeClass( 'load' );
										targetSelector.removeClass( 'filter-active' );

										var currentSettings = targetSelector.data( 'settings' );
										if (currentSettings.pagination_type === 'cwm_infinite') {
											currentSettings.pagination_type = 'load_more_infinite_scroll';
											targetSelector.data( 'settings', currentSettings );
										}
										if (currentSettings.pagination_load_type === 'cwm_ajax') {
											currentSettings.pagination_load_type = 'ajax';
											targetSelector.data( 'settings', currentSettings );
										}
										post_count();
									} else {
										// Load More & Infinite Paginations
										if (paginationType == 'infinite' || paginationType == 'load_more' || paginationType == 'load_more_on_click' || paginationType == 'load_more_infinite_scroll' || paginationType == 'cwm_infinite') {
											if (targetSelector.hasClass( 'filter-active' )) {
												var existingContent = targetSelector.find( '.elementor-grid' ).children();
												targetSelector.hide().empty().append( $( content ) );
												targetSelector.find( '.elementor-grid' ).prepend( existingContent );
												targetSelector.removeClass( 'e-load-more-pagination-loading' );
												if (targetSelector.hasClass( 'elementor-widget-posts' )) {
													targetSelector.fadeIn();
												} else {
													targetSelector.show();
												}
												targetSelector.removeClass( 'load' );
											} else {
												targetSelector.html( content ).fadeIn().removeClass( 'load' );
											}
											// Number & Next/Prev Paginations
										} else {
											targetSelector.html( content ).fadeIn().removeClass( 'load' );
										}

										loader.fadeOut();

										if ( ! $( content ).text().trim()) {
											var no_post = $( '.no-post-message[data-target-post-widget="' + targetPostWidget + '"]' ).text();
											if (no_post.length) {
												targetSelector.html( '<div class="no-post">' + no_post + '</div>' );
											}
										} else {
											var pagination = targetSelector.find( 'nav[aria-label="Pagination"]' );
											pagination.addClass( 'pagination-filter' );
											pagination.find( 'a.page-numbers' ).each(
												function () {
													var href  = $( this ).attr( 'href' );
													var regex = /.*wp-admin\/admin-ajax\.php/;
													if (base.charAt( base.length - 1 ) === '/') {
														base = base.slice( 0, -1 );
													}
													var newHref = href.replace( regex, base );
													$( this ).attr( 'href', newHref );
												}
											);

											var scrollAnchor = targetSelector.find( '.e-load-more-anchor' ),
											next_page        = scrollAnchor.data( 'next-page' );

											var loadMoreButton      = targetSelector.find( '.load-more' ),
											elementorLoadMoreButton = targetSelector.find( '.elementor-button-link.elementor-button' );

											loadMoreButton.addClass( 'load-more-filter' );
											elementorLoadMoreButton.addClass( 'load-more-filter' );
											targetSelector.addClass( 'filter-active' );

											if (next_page !== undefined) {
												var regex = /.*wp-admin\/admin-ajax\.php/;
												if (base.charAt( base.length - 1 ) === '/') {
													base = base.slice( 0, -1 );
												}
												var newHref = next_page.replace( regex, base );
												scrollAnchor.attr( 'data-next-page', newHref );
											}

											var currentSettings = targetSelector.data( 'settings' );
											if (currentSettings.pagination_type === 'load_more_infinite_scroll') {
												currentSettings.pagination_type = 'cwm_infinite';
												targetSelector.data( 'settings', currentSettings );
											}
											if (currentSettings.pagination_load_type === 'ajax') {
												currentSettings.pagination_load_type = 'cwm_ajax';
												targetSelector.data( 'settings', currentSettings );
											}

											post_count();
										}

									}
								},
								complete: function () {
									var loadMoreButton = targetSelector.find( '.load-more-filter' ),
									maxPage,
									paginationType     = targetSelector.data( 'settings' ).pagination || targetSelector.data( 'settings' ).pagination_type;

									// Check for maxPage in the usual place or the Elementor Pro widget
									var scrollAnchor = targetSelector.find( '.e-load-more-anchor' );
									if (scrollAnchor.length) {
										var currentPage = scrollAnchor.data( 'page' );
										maxPage         = scrollAnchor.data( 'max-page' ) - 1;
									} else {
										var currentPage = targetSelector.find( '.pagination' ).data( 'page' ),
										maxPage         = targetSelector.find( '.pagination' ).data( 'max-page' ) - 1;
									}

									if (scrollToTop == 'yes') {
										window.scrollTo(
											{
												top: targetSelector.offset().top - 150,
												behavior: 'smooth'
											}
										);
									}

									if (currentPage > maxPage) {
										targetSelector.addClass( 'e-load-more-pagination-end' );
										loadMoreButton.hide();
									}

									ajaxInProgress = false;

									if (targetSelector.hasClass( 'filter-active' ) && paginationType == 'infinite') {
										debounce(
											function (e) {
												bpf_infinite_scroll( widgetID, targetSelector );
											},
											800
										)();
									}

									if (targetSelector.hasClass( 'filter-active' ) && paginationType == 'cwm_infinite') {
										debounce(
											function (e) {
												elementor_infinite_scroll( widgetID, targetSelector );
											},
											800
										)();
									}

									targetSelector.find( 'input' ).val( searchQuery );

									elementorFrontend.elementsHandler.runReadyTrigger( $( targetPostWidget ) );
									if (elementorFrontend.config.experimentalFeatures.e_lazyload) {
										document.dispatchEvent( new Event( 'elementor/lazyload/observe' ) );
									}

									targetSelector.removeClass( 'filter-initialized' );
								},
								error: function (xhr, status, error) {
									console.log( 'AJAX error: ', error );
								}
							}
						);
					}

					function bpf_infinite_scroll(widgetID, targetSelector) {
						var scrollAnchor = targetSelector.find( '.e-load-more-anchor' ),
						paginationNext   = $( document ).find( targetPostWidget + ' .pagination-filter a.next' );

						if ( ! paginationNext.length) {
							if (filterWidgetObservers[widgetID]) {
								filterWidgetObservers[widgetID].disconnect();
								filterWidgetObservers[widgetID] = null;
							}
							return;
						}

						if (paginationNext.length && scrollAnchor.length) {
							if ( ! filterWidgetObservers[widgetID]) {
								filterWidgetObservers[widgetID] = new IntersectionObserver(
									function (entries) {
										entries.forEach(
											function (entry) {
												if (entry.isIntersecting) {
													var paginationNext = $( document ).find( targetPostWidget + ' .pagination-filter a.next' );

													if ( ! ajaxInProgress && paginationNext.length && targetSelector.hasClass( 'filter-active' )) {
														ajaxInProgress = true;
														paginationNext.click();
													}
												}
											}
										);

									},
									{
										root: null,
										rootMargin: infinite_threshold,
										threshold: 0
									}
								);
							}

							filterWidgetObservers[widgetID].observe( scrollAnchor.get( 0 ) );
						}
					}

					function elementor_infinite_scroll(widgetID, targetSelector) {
						var scrollAnchor = targetSelector.find( '.e-load-more-anchor' ),
						currentPage      = scrollAnchor.data( 'page' ),
						maxPage          = scrollAnchor.data( 'max-page' );

						if (currentPage === maxPage) {
							if (filterWidgetObservers[widgetID]) {
								filterWidgetObservers[widgetID].disconnect();
								filterWidgetObservers[widgetID] = null;
							}
							return;
						}

						if (scrollAnchor.length && currentPage < maxPage) {
							if ( ! filterWidgetObservers[widgetID]) {
								filterWidgetObservers[widgetID] = new IntersectionObserver(
									function (entries) {
										entries.forEach(
											function (entry) {
												if (entry.isIntersecting) {
													if ( ! ajaxInProgress && targetSelector.hasClass( 'filter-active' )) {
														ajaxInProgress = true;
														currentPage++;
														get_form_values( currentPage );
													}
												}
											}
										);
									},
									{
										root: null,
										rootMargin: infinite_threshold,
										threshold: 0
									}
								);
							}

							filterWidgetObservers[widgetID].observe( scrollAnchor.get( 0 ) );
						}
					}

					// Add more/less
					filterWidget.on(
						'click',
						'li.more',
						function () {
							var taxonomyFilter = $( this ).closest( 'ul.taxonomy-filter' );

							if (taxonomyFilter.hasClass( 'show-toggle' )) {
								taxonomyFilter.removeClass( 'show-toggle' );
							} else {
								taxonomyFilter.addClass( 'show-toggle' );
							}
						}
					);

					// Child term toggle
					$( '.cwm-filter-item' ).click(
						function () {
							var $lowTermsGroup = $( this ).closest( 'li' ).next( '.low-terms-group' );

							if ($( this ).is( ':checked' )) {
								$lowTermsGroup.show();
							} else {
								$lowTermsGroup.hide();
							}
						}
					);

					// Disable keyboard enter key for input fields
					$( 'form.form-tax input' ).on(
						'keypress',
						function (e) {
							if (e.which === 13) {
								e.preventDefault();
							}
						}
					);

					// Add reset
					filterWidget.on(
						'click',
						'.reset-form',
						function () {
							get_form_values();
						}
					);

					post_count();
					},

				}
			);

			elementorFrontend.elementsHandler.attachHandler( dynamic_handler, FilterWidgetHandler );
		}
	);
})( jQuery );