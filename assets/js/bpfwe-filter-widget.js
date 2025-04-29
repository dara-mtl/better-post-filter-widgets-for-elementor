( function ( $ ) {
	"use strict";
	$( window ).on( 'elementor/frontend/init', function () {
		var originalStates = {};
		var postsPerPageCache = {};
		const performanceSettingsCache = {};

		let dynamic_handler = '';
		if ( $( '.elementor-widget-filter-widget' ).length ) {
			dynamic_handler = 'filter-widget';
		} else if ( $( '.elementor-widget-search-bar-widget' ).length ) {
			dynamic_handler = 'search-bar-widget';
		} else {
			dynamic_handler = 'sorting-widget';
		}

		// Link filter/search/sort widgets to their target post widgets via data-filters-list.
		$( '.elementor-widget-filter-widget, .elementor-widget-search-bar-widget, .elementor-widget-sorting-widget' ).each( function () {
			var $widget = $( this );
			var widgetId = $widget.data( 'id' );
			var settings = $widget.data( 'settings' );
			var targetSelector = settings?.target_selector;

			if ( targetSelector && $( targetSelector ).length ) {
				var $target = $( targetSelector );
				var filtersList = $target.data( 'filters-list' ) ? $target.data( 'filters-list' ).split( ',' ) : [];

				if ( !filtersList.includes( widgetId ) ) {
					filtersList.push( widgetId );
					$target.data( 'filters-list', filtersList.join( ',' ) );
					$target.attr( 'data-filters-list', filtersList.join( ',' ) );
				}

				var widgetID = $target.data( 'id' );

				if ( !originalStates[ widgetID ] ) {
					originalStates[ widgetID ] = $target.html();
				}

				if ( !postsPerPageCache[ widgetID ] ) {
					const postWidgetSetting = $target.data( 'settings' );
					let postsPerPage = postWidgetSetting?.posts_per_page ? parseInt( postWidgetSetting.posts_per_page ) : null;
					if ( !postsPerPage ) {
						let postWrapper = $target.find( '.elementor-posts, .grid, .columns, .elementor-grid' ).first();
						if ( postWrapper.length ) postsPerPage = postWrapper.children( 'article, .post, .item, .entry' ).length;
					}
					if ( !postsPerPage ) {
						let postWrapper = $target.find( '.swiper-wrapper' ).first();
						if ( postWrapper.length ) postsPerPage = postWrapper.children( '.swiper-slide' ).length;
					}
					if ( !postsPerPage ) {
						let postWrapper = $target.find( 'ul.products' ).first();
						if ( postWrapper.length ) postsPerPage = postWrapper.children( 'li' ).length;
					}
					if ( !postsPerPage ) {
						let postWrapper = $target.find( 'ul' ).first();
						if ( postWrapper.length ) postsPerPage = postWrapper.children( 'li' ).length;
					}
					if ( !postsPerPage ) {
						let postWrapper = $target.find( 'div' ).first();
						if ( postWrapper.length ) postsPerPage = postWrapper.children( 'div' ).length;
					}
					postsPerPageCache[ widgetID ] = postsPerPage > 0 ? postsPerPage : 50;
				}

				// Handle performance settings.
				if ( $widget.hasClass( 'elementor-widget-filter-widget' ) ) {
					if ( !performanceSettingsCache[ widgetID ] ) {
						performanceSettingsCache[ widgetID ] = [];
					}

					const performanceSettings = {
						widgetId: widgetId,
						optimize_query: settings?.optimize_query === 'yes',
						no_found_rows: settings?.no_found_rows === 'yes',
						suppress_filters: settings?.suppress_filters === 'yes',
						posts_per_page: parseInt( settings?.posts_per_page ) || -1
					};

					performanceSettingsCache[ widgetID ].push( performanceSettings );

					const mergedSettings = performanceSettingsCache[ widgetID ].reduce( ( merged, current ) => {
						return {
							optimize_query: merged.optimize_query || current.optimize_query,
							no_found_rows: merged.no_found_rows || current.no_found_rows,
							suppress_filters: merged.suppress_filters || current.suppress_filters,
							posts_per_page: Math.min( merged.posts_per_page === -1 ? Infinity : merged.posts_per_page, current.posts_per_page === -1 ? Infinity : current.posts_per_page )
						};
					}, {
						optimize_query: false,
						no_found_rows: false,
						suppress_filters: false,
						posts_per_page: -1
					});

					mergedSettings.posts_per_page = mergedSettings.posts_per_page === Infinity ? -1 : mergedSettings.posts_per_page;

					$target.data( 'performance-settings', mergedSettings );
					$target.attr( 'data-performance-settings', JSON.stringify( mergedSettings ) );
				}
			}
		} );

		const FilterWidgetHandler = elementorModules.frontend.handlers.Base.extend( {
			bindEvents () {
				this.getAjaxFilter();
			},

			getAjaxFilter () {
				let ajaxInProgress = false;
				const filterWidget = this.$element.find( '.filter-container' );

				// Initialize single-select dropdowns with Select2.
				filterWidget.find( '.bpfwe-select2 select' ).each( function ( index ) {
					const $select = $( this );
					const parentElement = $select.closest( '.bpfwe-select2' );
					const uniqueId = 'bpfwe-select2-' + index;
					$select.attr( 'id', uniqueId ).prop( 'multiple', false ).select2( {
						dropdownParent: parentElement,
					} );
					parentElement.css( {
						"visibility": "visible",
						"opacity": "1",
						"transition": "opacity 0.3s ease-in-out"
					} );
				} );

				// Initialize multi-select dropdowns with Select2 and plus symbol logic.
				filterWidget.find( '.bpfwe-multi-select2 select' ).each( function ( index ) {
					const $select = $( this );
					const parentElement = $select.closest( '.bpfwe-multi-select2' );
					const uniqueId = 'bpfwe-multi-select2-' + index;
					$select.attr( 'id', uniqueId ).prop( 'multiple', true ).select2( {
						dropdownParent: parentElement,
					} );
					$select.val( null ).trigger( 'change' );
					parentElement.css( {
						"visibility": "visible",
						"opacity": "1",
						"transition": "opacity 0.3s ease-in-out"
					} );

					function updatePlusSymbol () {
						var $rendered = parentElement.find( '.select2-selection__rendered' );
						$rendered.find( '.select2-selection__e-plus-button' ).remove();
						if ( $select.val().length === 0 ) {
							$rendered.prepend( '<span class="select2-selection__choice select2-selection__e-plus-button">+</span>' );
						}
					}
					updatePlusSymbol();
					$select.on( 'change', updatePlusSymbol );
				} );

				// Toggle visibility of taxonomy filter items.
				filterWidget.on( 'click', 'li.more', function () {
					var taxonomyFilter = $( this ).closest( 'ul.taxonomy-filter' );
					taxonomyFilter.toggleClass( 'show-toggle' );
				} );

				// Toggle low-level terms group visibility with +/- indicator.
				filterWidget.on( 'click', '.low-group-trigger', function () {
					var $trigger = $( this );
					var $parentLi = $trigger.closest( 'li' );
					var $lowTermsGroup = $parentLi.hasClass( 'child-term' ) ? $parentLi.children( '.low-terms-group' ) : $parentLi.next( '.low-terms-group' );
					$lowTermsGroup.toggle();
					var isExpanded = $lowTermsGroup.is( ':visible' );
					$trigger.text( isExpanded ? '-' : '+' );
					$trigger.attr( 'aria-expanded', isExpanded );
				} );

				$( document ).off( 'keydown', 'form.form-tax input' ).on( 'keydown', 'form.form-tax input', function ( e ) {
					if ( e.which === 13 ) e.preventDefault();
				} );

				const currentUrl = window.location.href;
				const filterSetting = this.$element.data( 'settings' );

				if ( !filterSetting ) return;

				let targetPostWidget = filterSetting?.target_selector ?? '';
				if ( !targetPostWidget || !$( targetPostWidget ).length ) {
					let $closestWidget = $( '.elementor-widget-loop-carousel, .elementor-widget-loop-grid, .elementor-widget-post-widget, .elementor-widget-posts' ).first();
					if ( $closestWidget.length ) {
						let widgetClass = $closestWidget.attr( 'class' )?.split( ' ' ).find( cls => cls.startsWith( 'elementor-widget-' ) ) || '';
						targetPostWidget = $closestWidget.attr( 'id' ) ? `#${$closestWidget.attr('id')}` : widgetClass ? `.${widgetClass}` : '';
					}
				}
				if ( !targetPostWidget || targetPostWidget === '.' ) return;

				let groupLogic = filterSetting?.group_logic ?? '',
					dynamicFiltering = filterSetting?.dynamic_filtering ?? '',
					scrollToTop = filterSetting?.scroll_to_top ?? '',
					//post_type = filterSetting?.filter_post_type ?? '',
					nothing_found_message = filterSetting?.nothing_found_message ?? 'It seems we can’t find what you’re looking for.',
					currentPage = 1,
					paginationType = '';

				let targetSelector = $( targetPostWidget ),
					widgetID = targetSelector.data( 'id' ),
					//originalFormState = filterWidget.find( 'form' ).serialize(),
					loader = targetSelector.find( '.loader' ),
					pagination = targetSelector.find( '.pagination' );

				var maxPage = pagination.data( 'max-page' ),
					filterWidgetObservers = {};

				const postWidgetSetting = targetSelector.data( 'settings' );

				if ( postWidgetSetting && ( postWidgetSetting.pagination || postWidgetSetting.pagination_type ) ) {
					paginationType = postWidgetSetting.pagination || postWidgetSetting.pagination_type;
					var size = postWidgetSetting.scroll_threshold?.size || 0;
					var unit = postWidgetSetting.scroll_threshold?.unit || 'px';
					var infinite_threshold = size + unit;
				} else {
					var infinite_threshold = '0px';
				}

				let pageID = window.elementorFrontendConfig.post.id;

				if ( !pageID ) {
					if ( !widgetID ) return;
					var $outermost = $( '[data-id="' + widgetID + '"]' ).parents( '[data-elementor-id]' ).last();
					if ( $outermost.length ) pageID = $outermost.data( 'elementor-id' );
				}

				// ===== Debounce the interactions =====
				function debounce ( func, delay ) {
					let timeoutId;
					return function () {
						const context = this,
							args = arguments;
						clearTimeout( timeoutId );
						timeoutId = setTimeout( () => func.apply( context, args ), delay );
					};
				}

				let isInteracting = false,
					interactionTimeout;

				filterWidget.on( 'mousedown keydown touchstart', 'form.form-tax', function () {
					isInteracting = true;
					clearTimeout( interactionTimeout );
				} );

				filterWidget.on( 'mouseup keyup touchend', 'form.form-tax', function () {
					interactionTimeout = setTimeout( () => isInteracting = false, 700 );
				} );

				// ===== Filter widgets: inputs =====
				$( document ).off( 'change keydown input', 'form.form-tax, .bpfwe-numeric-wrapper input' ).on(
					'change keydown input',
					'form.form-tax, .bpfwe-numeric-wrapper input',
					debounce( function ( e ) {
						var $widget = $( this ).closest( '.elementor-widget-filter-widget' );
						var widgetInteractionID = $widget.data( 'id' );
						if ( !widgetInteractionID ) return;

						const isSubmitPresent = $widget.find('.submit-form').length > 0;

						if ( !isSubmitPresent ) {
							if ( e.type === 'change' || ( e.type === 'keydown' && e.key === 'Enter' ) ) {
								resetURL();
								get_form_values( widgetInteractionID );
								return;
							}
							if ( !isInteracting ) {
								resetURL();
								get_form_values( widgetInteractionID );
							}
						}
					}, 700 )
				);

				// ===== Filter widgets: submit button =====
				$( document ).off( 'click', '.submit-form' ).on( 'click', '.submit-form', function () {
					var $widget = $( this ).closest( '.elementor-widget-filter-widget' );
					var widgetInteractionID = $widget.data( 'id' );
					if ( !widgetInteractionID ) return;
					resetURL();
					get_form_values( widgetInteractionID );
					return false;
				} );

				// ===== Sorting widgets =====
				$( document ).off( 'change', 'form.form-order-by' ).on( 'change', 'form.form-order-by', function () {
					var $widget = $( this ).closest( '.elementor-widget-sorting-widget' );
					var widgetInteractionID = $widget.data( 'id' );
					if ( !widgetInteractionID ) return;
					resetURL();
					get_form_values( widgetInteractionID );
				} );

				// ===== Search bar widgets =====
				$( document ).off( 'submit', 'form.search-post' ).on( 'submit', 'form.search-post', function () {
					var $widget = $( this ).closest( '.elementor-widget-search-bar-widget' );
					var widgetInteractionID = $widget.data( 'id' );
					if ( !widgetInteractionID ) return;
					resetURL();
					get_form_values( widgetInteractionID );
					if ($( this ).hasClass( 'no-redirect' )) {
						return false;
					}
				} );

				if ( currentUrl.includes( '?search=' ) ) get_form_values();

				// ===== Utility Functions =====
				function getPageNumber ( url ) {
					var match;
					if ( url.includes( "?page=" ) ) match = url.match( /\?page=(\d+)/ );
					else if ( url.includes( "?paged=" ) ) match = url.match( /\?paged=(\d+)/ );
					else if ( url.match( /\/(\d+)(\/|$)/ ) ) match = url.match( /\/(\d+)(\/|$)/ );
					else match = url.match( /[?&](\w+)=\d+/ );
					if ( !match ) match = url.match( /(\d+)(\/|$)/ );
					return match ? match[ 1 ] : null;
				}

				function resetURL () {
					let baseURL = window.location.origin + window.location.pathname;
					baseURL = baseURL.replace( /\/page\/\d+\/?$/, '' );
					history.replaceState( null, '', baseURL );
				}

				// ===== Pagination: numbers and load more =====
				$( document ).off( 'click', '.pagination-filter a' ).on( 'click', '.pagination-filter a', function ( e ) {
					var postWidgetID = $( this ).closest( '[data-id]' ).data( 'id' );
					e.preventDefault();
					var url = $( this ).attr( 'href' );
					var paged = getPageNumber( url );
					get_form_values( null, paged, postWidgetID );
				} );

				$( document ).off( 'click', '.load-more-filter' ).on( 'click', '.load-more-filter', function ( e ) {
					e.preventDefault();

					var $widget = $( this ).closest( '[data-id]' );
					var postWidgetID = $widget.data( 'id' );
					var url = $widget.find( '.e-load-more-anchor' ).data( 'next-page' );

					if ( url ) {
						var paged = getPageNumber( url );
						get_form_values( null, paged, postWidgetID );
					} else {
						var nextPageLink = $widget.find( '.pagination-filter a.next' );

						if ( nextPageLink.length ) {
							nextPageLink.trigger( 'click' );
							currentPage++;
							$widget.data( 'current-page', currentPage );
							var loadMoreButton = $widget.find( '.load-more' );
							loadMoreButton.text( 'Loading...' ).prop( 'disabled', true );
						}
					}
				} );

				function post_count ( $target ) {
					let postCount = $target.find( '.post-container' ).data( 'total-post' ) || 0;
					postCount = Number( postCount );
					$( '.filter-post-count .number' ).text( postCount );
				}

				// ===== Retrieve form values, process filters, and make AJAX request for filtered posts =====
				function get_form_values ( widgetInteractionID, paged, postWidgetID ) {
					let localWidgetID = widgetInteractionID ? $( 'div[data-filters-list*="' + widgetInteractionID + '"]' ).data( 'id' ) : widgetID;
					if ( !localWidgetID ) return;

					// Check if the function was trigerred by a filter or a post widget.
					if ( postWidgetID ) {
						localWidgetID = postWidgetID;
					}

					let localTargetSelector = $( 'div[data-id="' + localWidgetID + '"]' );
					if ( !localTargetSelector.length ) return;

					let originalState = originalStates[ localWidgetID ];
					let postsPerPage = postsPerPageCache[ localWidgetID ];

					let filtersList = localTargetSelector.data( 'filters-list' ) ? localTargetSelector.data( 'filters-list' ).split( ',' ) : [];
					let postContainer = localTargetSelector.find( '.post-container' ),
						order = '',
						order_by = '',
						order_by_meta = '',
						searchQuery = '',
						post_type = '';

					// Disconnect observer to halt infinite scroll pagination during updates.
					//const paginationCheck = localTargetSelector.data( 'settings' )?.pagination_type || localTargetSelector.data( 'settings' )?.pagination || '';
					let paginationType = localTargetSelector.data( 'settings' )?.pagination || localTargetSelector.data( 'settings' )?.pagination_type || '';

					if ( ( paginationType === 'cwm_infinite' || paginationType === 'infinite' ) && filterWidgetObservers[ localWidgetID ] ) {
						filterWidgetObservers[ localWidgetID ].disconnect();
						filterWidgetObservers[ localWidgetID ] = null;
					}

					let isSorting = $( '.elementor-widget-sorting-widget[data-id="' + widgetInteractionID + '"]' ).length > 0;
					let isSearch = $( '.elementor-widget-search-bar-widget[data-id="' + widgetInteractionID + '"]' ).length > 0;
					let isFiltering = $( '.elementor-widget-filter-widget[data-id="' + widgetInteractionID + '"]' ).length > 0;

					// Remove 'filter-active' only if sorting, searching, or filtering occurs (ensuring new stack instead of stacking posts).
					if ( isSorting || isSearch || isFiltering ) {
						localTargetSelector.removeClass( 'filter-active' );
						localTargetSelector.data( 'current-page', 1 );
					}

					let hasValues = false;

					filtersList.forEach( function ( filterWidgetId ) {
						// Lowest priority: Sorting widget.
						if ( !post_type ) {
							var $sortingWidget = $( '.elementor-widget-sorting-widget[data-id="' + filterWidgetId + '"]' );
							if ( $sortingWidget.length ) {
								post_type = $sortingWidget.data( 'settings' )?.filter_post_type || '';
							}
						}

						// Medium priority: Search widget.
						if ( !post_type ) {
							var $searchWidget = $( '.elementor-widget-search-bar-widget[data-id="' + filterWidgetId + '"]' );
							if ( $searchWidget.length ) {
								post_type = $searchWidget.data( 'settings' )?.filter_post_type || '';
							}
						}

						// Highest priority: Filter widget.
						var $filterWidget = $( '.elementor-widget-filter-widget[data-id="' + filterWidgetId + '"]' );
						if ( $filterWidget.length ) {
							post_type = $filterWidget.data( 'settings' )?.filter_post_type || '';
						}
					} );

					filtersList.forEach( function ( filterWidgetId ) {
						var $searchWidget = $( '.elementor-widget-search-bar-widget[data-id="' + filterWidgetId + '"]' );
						if ( $searchWidget.length ) {
							searchQuery = $searchWidget.find( 'form.search-post input' ).val() || '';
							if ( searchQuery ) hasValues = true;
						}

						var $sortingWidget = $( '.elementor-widget-sorting-widget[data-id="' + filterWidgetId + '"]' );
						if ( $sortingWidget.length ) {
							var $select = $sortingWidget.find( '.form-order-by select' );
							var $defaultOption = $select.find( 'option:first-child' );
							var $selectedOption = $select.find( 'option:selected' );

							$selectedOption.each( function () {
								var self = $( this );
								order = self.data( 'order' );
								order_by_meta = self.data( 'meta' );
								order_by = self.val();

								if ( self.val() !== $defaultOption.val() ) {
									hasValues = true;
								}
							} );
						}
					} );

					var urlParams = new URLSearchParams( window.location.search );
					if ( urlParams.has( 'search' ) ) {
						searchQuery = urlParams.get( 'search' );
						if ( searchQuery ) hasValues = true;
					}

					localTargetSelector.removeClass( 'e-load-more-pagination-end' );
					postContainer.addClass( 'load' );
					localTargetSelector.addClass( 'load' );
					localTargetSelector.addClass( 'filter-initialized' );

					if ( postContainer.hasClass( 'shortcode' ) || postContainer.hasClass( 'template' ) ) {
						localTargetSelector.find( '.loader' ).fadeIn();
					}

					var category = [],
						custom_field = [],
						custom_field_like = [],
						numeric_field = [];

					filtersList.forEach( function ( filterWidgetId ) {
						var $filterWidget = $( '.elementor-widget-filter-widget[data-id="' + filterWidgetId + '"]' );
						if ( $filterWidget.length ) {
							$filterWidget.find( '.bpfwe-taxonomy-wrapper input:checked, .bpfwe-custom-field-wrapper input:checked' ).each( function () {
								var self = $( this );
								var targetArray = self.closest( '.bpfwe-taxonomy-wrapper' ).length ? category : custom_field;
								targetArray.push( {
									'taxonomy': self.data( 'taxonomy' ),
									'terms': self.val(),
									'logic': self.closest( 'div' ).data( 'logic' )
								} );
								hasValues = true;
							} );

							$filterWidget.find( '.bpfwe-custom-field-wrapper input.input-text' ).each( function () {
								var self = $( this );
								if ( self.val() ) {
									custom_field_like.push( {
										'taxonomy': self.data( 'taxonomy' ),
										'terms': self.val(),
										'logic': self.closest( 'div' ).data( 'logic' )
									} );
									hasValues = true;
								}
							} );

							$filterWidget.find( '.bpfwe-taxonomy-wrapper select option:selected, .bpfwe-custom-field-wrapper select option:selected' ).each( function () {
								var self = $( this );
								if ( self.val() ) {
									var targetArray = self.closest( '.bpfwe-taxonomy-wrapper' ).length ? category : custom_field;
									targetArray.push( {
										'taxonomy': self.data( 'taxonomy' ),
										'terms': self.val(),
										'logic': self.closest( 'div' ).data( 'logic' )
									} );
									hasValues = true;
								}
							} );

							$filterWidget.find( '.bpfwe-numeric-wrapper input' ).each( function () {
								var self = $( this );
								var initial_val = self.data( 'base-value' );
								if ( self.val() === '' || self.val() != initial_val ) {
									if ( self.val() === '' ) self.val( initial_val );
									var _class = self.attr( "class" ).split( ' ' )[ 0 ];
									$filterWidget.find( '.bpfwe-numeric-wrapper input' ).each( function () {
										var _this = $( this );
										if ( _this.hasClass( _class ) ) {
											numeric_field.push( {
												'taxonomy': _this.data( 'taxonomy' ),
												'terms': _this.val(),
												'logic': _this.closest( 'div' ).data( 'logic' )
											} );
											hasValues = true;
										}
									} );
								}
							} );
						}
					} );

					function reduceFields ( fields ) {
						return fields.reduce( function ( o, cur ) {
							var occurs = o.reduce( function ( n, item, i ) {
								return ( item.taxonomy === cur.taxonomy ) ? i : n;
							}, -1 );
							if ( occurs >= 0 ) o[ occurs ].terms = o[ occurs ].terms.concat( cur.terms );
							else o = o.concat( [ {
								taxonomy: cur.taxonomy,
								terms: [ cur.terms ],
								logic: cur.logic
							} ] );
							return o;
						}, [] );
					}

					var taxonomy_output = reduceFields( category ),
						custom_field_output = reduceFields( custom_field ),
						custom_field_like_output = reduceFields( custom_field_like ),
						numeric_output = reduceFields( numeric_field );

					$.ajax( {
						type: 'POST',
						url: ajax_var.url,
						async: true,
						data: {
							action: 'post_filter_results',
							widget_id: localWidgetID,
							page_id: pageID,
							group_logic: groupLogic,
							search_query: searchQuery,
							taxonomy_output: taxonomy_output,
							dynamic_filtering: dynamicFiltering,
							custom_field_output: custom_field_output,
							custom_field_like_output: custom_field_like_output,
							numeric_output: numeric_output,
							post_type: post_type,
							posts_per_page: postsPerPage,
							order: order,
							order_by: order_by,
							order_by_meta: order_by_meta,
							paged: paged,
							archive_type: $( '[name="archive_type"]' ).val(),
							archive_post_type: $( '[name="archive_post_type"]' ).val(),
							archive_taxonomy: $( '[name="archive_taxonomy"]' ).val(),
							archive_id: $( '[name="archive_id"]' ).val(),
							nonce: ajax_var.nonce,
							performance_settings: JSON.stringify(getPerformanceSettings(localWidgetID))
						},
						success: function ( data ) {
							var response = JSON.parse( data );
							var content = response.html,
								//base = currentUrl,
								base = currentUrl
								.replace( /\/page\/\d+\/?($|\?)/, '/$1' )
								.replace( /[?&]e-page-[a-z0-9]+=\d+/i, '' )
								.replace( /[?&]product-page=\d+/i, '' )
								.replace( /[?&]paged=\d+/i, '' )
								.replace( /\?&+/, '?' )
								.replace( /\?$/, '' ),
								currentFormState = filterWidget.find( 'form' ).serialize();

							if ( data === '0' || !hasValues ) {
								//localTargetSelector.off();
								localTargetSelector.html( originalState ).fadeIn().removeClass( 'load filter-active' );
								var currentSettings = localTargetSelector.data( 'settings' );
								if ( currentSettings?.pagination_type === 'cwm_infinite' ) {
									currentSettings.pagination_type = 'load_more_infinite_scroll';
									localTargetSelector.data( 'settings', currentSettings );
								}
								if ( currentSettings?.pagination_load_type === 'cwm_ajax' ) {
									currentSettings.pagination_load_type = 'ajax';
									localTargetSelector.data( 'settings', currentSettings );
								}
								post_count( localTargetSelector );
							} else {
								if ( [ 'infinite', 'load_more', 'load_more_on_click', 'load_more_infinite_scroll', 'cwm_infinite' ].includes( paginationType ) ) {
									if ( localTargetSelector.hasClass( 'filter-active' ) ) {
										var existingContent = localTargetSelector.find( '.elementor-grid' ).children();
										localTargetSelector.hide().empty().append( $( content ) );
										localTargetSelector.find( '.elementor-grid' ).prepend( existingContent );
										localTargetSelector.removeClass( 'e-load-more-pagination-loading' );
										localTargetSelector[ localTargetSelector.hasClass( 'elementor-widget-posts' ) ? 'fadeIn' : 'show' ]();
										localTargetSelector.removeClass( 'load' );
									} else {
										localTargetSelector.html( content ).fadeIn().removeClass( 'load' );
									}
								} else {
									localTargetSelector.html( content ).fadeIn().removeClass( 'load' );
								}

								localTargetSelector.find( '.loader' ).fadeOut();

								if ( !$( content ).text().trim() ) {
									nothing_found_message = nothing_found_message.replace( /</g, '<' ).replace( />/g, '>' );
									if ( nothing_found_message.trim() ) {
										localTargetSelector.html( '<div class="no-post">' + nothing_found_message + '</div>' );
									}
								} else {
									var pagination = localTargetSelector.find( 'nav[aria-label="Pagination"], nav[aria-label="Product Pagination"]' );
									pagination.addClass( 'pagination-filter' );
									pagination.find( 'a.page-numbers' ).each( function () {
										var href = $( this ).attr( 'href' );
										var regex = /.*wp-admin\/admin-ajax\.php/;
										if ( base.endsWith( '/' ) ) base = base.slice( 0, -1 );
										$( this ).attr( 'href', href.replace( regex, base ) );
									} );

									var scrollAnchor = localTargetSelector.find( '.e-load-more-anchor' ),
										next_page = scrollAnchor.data( 'next-page' );

									var loadMoreButton = localTargetSelector.find( '.load-more' ),
										elementorLoadMoreButton = localTargetSelector.find( '.e-load-more-anchor' ).nextAll().find( '.elementor-button-link.elementor-button' );

									loadMoreButton.addClass( 'load-more-filter' );
									elementorLoadMoreButton.addClass( 'load-more-filter' );
									localTargetSelector.addClass( 'filter-active' );

									if ( next_page ) {
										var regex = /.*wp-admin\/admin-ajax\.php/;
										if ( base.endsWith( '/' ) ) base = base.slice( 0, -1 );
										scrollAnchor.attr( 'data-next-page', next_page.replace( regex, base ) );
									}

									var currentSettings = localTargetSelector.data( 'settings' );
									if ( currentSettings?.pagination_type === 'load_more_infinite_scroll' ) {
										currentSettings.pagination_type = 'cwm_infinite';
										localTargetSelector.data( 'settings', currentSettings );
									}
									if ( currentSettings?.pagination_load_type === 'ajax' ) {
										currentSettings.pagination_load_type = 'cwm_ajax';
										localTargetSelector.data( 'settings', currentSettings );
									}

									post_count( localTargetSelector );
								}
							}
							localTargetSelector.removeClass( 'filter-initialized' );
						},
						complete: function () {
							var loadMoreButton = localTargetSelector.find( '.load-more-filter' ),
								maxPage;

							paginationType = localTargetSelector.data( 'settings' )?.pagination || localTargetSelector.data( 'settings' )?.pagination_type || '';

							var scrollAnchor = localTargetSelector.find( '.e-load-more-anchor' );
							if ( scrollAnchor.length ) {
								var currentPage = scrollAnchor.data( 'page' );
								maxPage = scrollAnchor.data( 'max-page' ) - 1;
							} else {
								var currentPage = localTargetSelector.find( '.pagination' ).data( 'page' ),
									maxPage = localTargetSelector.find( '.pagination' ).data( 'max-page' ) - 1;
							}

							if ( scrollToTop === 'yes' ) {
								window.scrollTo( {
									top: localTargetSelector.offset().top - 150,
									behavior: 'smooth'
								} );
							}

							if ( currentPage > maxPage ) {
								localTargetSelector.addClass( 'e-load-more-pagination-end' );
								loadMoreButton.hide();
							}

							ajaxInProgress = false;

							if ( localTargetSelector.hasClass( 'filter-active' ) && paginationType === 'infinite' ) {
								debounce( function () {
									bpfwe_infinite_scroll( localWidgetID, localTargetSelector );
								}, 800 )();
							}

							if ( localTargetSelector.hasClass( 'filter-active' ) && paginationType === 'cwm_infinite' ) {
								debounce( function () {
									elementor_infinite_scroll( localWidgetID, localTargetSelector );
								}, 800 )();
							}

							localTargetSelector.find( 'input' ).val( searchQuery );

							elementorFrontend.elementsHandler.runReadyTrigger( localTargetSelector );
							if ( elementorFrontend.config.experimentalFeatures.e_lazyload ) {
								document.dispatchEvent( new Event( 'elementor/lazyload/observe' ) );
							}
						},
						error: function ( xhr, status, error ) {
							console.log( 'AJAX error:', error );
							localTargetSelector.html( originalState ).fadeIn().removeClass( 'load filter-active' );
							var currentSettings = localTargetSelector.data( 'settings' );
							if ( currentSettings?.pagination_type === 'cwm_infinite' ) {
								currentSettings.pagination_type = 'load_more_infinite_scroll';
								localTargetSelector.data( 'settings', currentSettings );
							}
							if ( currentSettings?.pagination_load_type === 'cwm_ajax' ) {
								currentSettings.pagination_load_type = 'ajax';
								localTargetSelector.data( 'settings', currentSettings );
							}
							post_count( localTargetSelector );
							elementorFrontend.elementsHandler.runReadyTrigger( localTargetSelector );
							if ( elementorFrontend.config.experimentalFeatures.e_lazyload ) {
								document.dispatchEvent( new Event( 'elementor/lazyload/observe' ) );
							}
						}
					} );
				}

				function bpfwe_infinite_scroll ( widgetID, targetSelector ) {
					var scrollAnchor = targetSelector.find( '.e-load-more-anchor' ),
						$paginationNext = targetSelector.find( '.pagination-filter a.next' );

					if ( !$paginationNext.length ) {
						if ( filterWidgetObservers[ widgetID ] ) {
							filterWidgetObservers[ widgetID ].disconnect();
							filterWidgetObservers[ widgetID ] = null;
						}
						return;
					}

					if ( $paginationNext.length && scrollAnchor.length ) {
						if ( !filterWidgetObservers[ widgetID ] ) {
							filterWidgetObservers[ widgetID ] = new IntersectionObserver( function ( entries ) {
								entries.forEach( function ( entry ) {
									if ( entry.isIntersecting ) {
										var $nextLink = targetSelector.find( '.pagination-filter a.next' );
										if ( !ajaxInProgress && $nextLink.length && targetSelector.hasClass( 'filter-active' ) ) {
											ajaxInProgress = true;
											var url = $nextLink.attr( 'href' );
											var paged = getPageNumber( url );
											get_form_values( null, paged, widgetID );
										}
									}
								} );
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							} );
						}
						filterWidgetObservers[ widgetID ].observe( scrollAnchor.get( 0 ) );
					}
				}

				function elementor_infinite_scroll ( widgetID, targetSelector ) {
					var scrollAnchor = targetSelector.find( '.e-load-more-anchor' ),
						currentPage = targetSelector.data( 'current-page' ) || 1,
						maxPage = scrollAnchor.data( 'max-page' );

					if ( currentPage >= maxPage ) {
						if ( filterWidgetObservers[ widgetID ] ) {
							filterWidgetObservers[ widgetID ].disconnect();
							filterWidgetObservers[ widgetID ] = null;
						}
						return;
					}

					if ( scrollAnchor.length && currentPage < maxPage ) {
						if ( !filterWidgetObservers[ widgetID ] ) {
							filterWidgetObservers[ widgetID ] = new IntersectionObserver( function ( entries ) {
								entries.forEach( function ( entry ) {
									if ( entry.isIntersecting ) {
										if ( !ajaxInProgress && targetSelector.hasClass( 'filter-active' ) ) {
											ajaxInProgress = true;
											currentPage++;
											targetSelector.data( 'current-page', currentPage );
											get_form_values( null, currentPage, widgetID );
										}
									}
								} );
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							} );
						}
						filterWidgetObservers[ widgetID ].observe( scrollAnchor.get( 0 ) );
					}
				}

				// Fetch the performance settings based on the widget.
				function getPerformanceSettings(widgetId) {
					const $target = $(`.elementor-element[data-id="${widgetId}"]`);
					let performanceSettings = $target.data('performance-settings');

					if (!performanceSettings) {
						performanceSettings = {
							optimize_query: false,
							no_found_rows: false,
							suppress_filters: false,
							cache_results: true,
							posts_per_page: -1
						};
					}

					performanceSettings.posts_per_page = parseInt(performanceSettings.posts_per_page) || -1;

					return performanceSettings;
				}

				// Handle reset button click to clear filters, sorting, and search for the target widget.
				filterWidget.on( 'click', '.reset-form', function () {
					var $resetWidget = $( this ).closest( '.elementor-widget-filter-widget' );
					var resetWidgetID = $resetWidget.data( 'id' );
					var $targetPostWidget = $( 'div[data-filters-list*="' + resetWidgetID + '"]' );
					var localWidgetID = $targetPostWidget.data( 'id' );

					if ( !localWidgetID || !$targetPostWidget.length ) return;

					var filtersList = $targetPostWidget.data( 'filters-list' ) ? $targetPostWidget.data( 'filters-list' ).split( ',' ) : [];

					// Reset all linked widgets.
					filtersList.forEach( function ( widgetId ) {
						var $filterWidget = $( '.elementor-widget-filter-widget[data-id="' + widgetId + '"]' );
						if ( $filterWidget.length ) {
							$filterWidget.find( 'input:checked' ).prop( 'checked', false );
							$filterWidget.find( 'select' ).each( function () {
								$( this ).val( $( this ).find( 'option:first' ).val() ).trigger( 'change' );
							} );
							$filterWidget.find( '.bpfwe-numeric-wrapper input' ).each( function () {
								var initialVal = $( this ).data( 'base-value' );
								$( this ).val( initialVal ).trigger( 'input' );
							} );
							$filterWidget.find( 'input.input-text' ).val( '' ).trigger( 'input' );
						}

						var $sortingWidget = $( '.elementor-widget-sorting-widget[data-id="' + widgetId + '"]' );
						if ( $sortingWidget.length ) {
							$sortingWidget.find( 'form.form-order-by select' ).prop( 'selectedIndex', 0 ).trigger( 'change' );
						}

						var $searchWidget = $( '.elementor-widget-search-bar-widget[data-id="' + widgetId + '"]' );
						if ( $searchWidget.length ) {
							$searchWidget.find( 'form.search-post input[name="s"]' ).val( '' ).trigger( 'input' );
						}
					} );

					resetURL();
					$targetPostWidget.addClass( 'filter-initialized' );
					$targetPostWidget.removeClass( 'filter-active' );
					$targetPostWidget.data( 'current-page', 1 );
					get_form_values( resetWidgetID );
				} );

				post_count( targetSelector );
			},
		} );

		if ( !elementorFrontend.isEditMode() ) {
			elementorFrontend.elementsHandler.attachHandler( dynamic_handler, FilterWidgetHandler );
		} else {
			elementorFrontend.elementsHandler.attachHandler( 'filter-widget', FilterWidgetHandler );
		}
	} );
} )( jQuery );