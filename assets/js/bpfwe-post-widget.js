(function ($) {
	"use strict";
	$(window).on(
		'elementor/frontend/init',
		function() {
			const PostWidgetHandler = elementorModules.frontend.handlers.Base.extend({

				bindEvents: function() {
					this.fetchMasonry();
					this.fetchFeedLayout();
					this.changePostStatus();
					this.getPinnedPosts();
					this.getPostsByAjax();
					this.postCarousel();
				},

				debounce: function (func, delay) {
					let timeoutId;
					return function() {
						const context = this;
						const args = arguments;
						clearTimeout(timeoutId);
						timeoutId = setTimeout(
							() => {
								func.apply(context, args);
							},
							delay
						);
					};
				},

				fetchMasonry: function() {
					var settings = this.getElementSettings(),
						$container = this.$element.find('.bpfwe-masonry');

					if ($container.length === 0) {
						return;
					}

					var $masonryElements = $container.find('.post-wrapper');

					if ($masonryElements.length === 0) {
						return;
					}

					const breakpoints = elementorFrontend.config.responsive.breakpoints;

					const getColumns =() => {
						const windowWidth = window.innerWidth;

						let columns = '';

						if (windowWidth <= breakpoints.widescreen.value && settings.nb_columns_widescreen !== undefined) {
							columns = settings.nb_columns_widescreen || 4;
						}
						if (windowWidth <= breakpoints.laptop.value && settings.nb_columns !== undefined) {
							columns = settings.nb_columns || 3;
						}
						if (windowWidth <= breakpoints.tablet_extra.value && settings.nb_columns_tablet_extra !== undefined) {
							columns = settings.nb_columns_tablet_extra || 3;
						}
						if (windowWidth <= breakpoints.tablet.value && settings.nb_columns_tablet !== undefined) {
							columns = settings.nb_columns_tablet || 2;
						}
						if (windowWidth <= breakpoints.mobile_extra.value && settings.nb_columns_mobile_extra !== undefined) {
							columns = settings.nb_columns_mobile_extra || 2;
						}
						if (windowWidth <= breakpoints.mobile.value && settings.nb_columns_mobile !== undefined) {
							columns = settings.nb_columns_mobile || 1;
						}

						if (columns === undefined || columns === '') {
							columns = settings.nb_columns || 3;
						}

						return columns;
					};

					let lastColumns = null;
					let firstRun = true;
					let lastOrientation = typeof window.orientation !== 'undefined' ? window.orientation : null;

					const createMasonryLayout =() => {
						const columns = getColumns();

						let triggerAnimation = false;

						if (columns !== lastColumns || firstRun) {
							triggerAnimation = firstRun;
							lastColumns = columns;
						}

						if (typeof window.orientation !== 'undefined' && window.orientation !== lastOrientation) {
							triggerAnimation = true;
							lastOrientation = window.orientation;
						}

						$container.removeClass(function (i, c) {
							return (c.match(/(^|\s)columns-\S+/g) || []).join(' ');
						}).addClass('elementor-grid bpfwe-masonry masonry-layout columns-' + columns);

						$container.children('.masonry-column').remove();

						for (let i = 1; i <= columns; i++) {
							const $newColumn = $('<div></div>').addClass('masonry-column masonry-column-' + i);
							$container.append($newColumn);
						}

						let countColumn = 1;

						$container.find('.post-wrapper').remove();

						$masonryElements.each((index, element) => {
							const $col = $container.find('.masonry-column-' + countColumn);

							if (triggerAnimation) {
								$(element).css({
									opacity: '0',
									transform: 'translateY(20px)',
								});
							}

							$col.append($(element));
							countColumn = countColumn < columns ? countColumn + 1 : 1;

							if (triggerAnimation) {
								setTimeout(() => {
									$(element).css({
										opacity: '1',
										transform: 'translateY(0)',
										transition: 'opacity 0.5s ease, transform 0.5s ease',
									});
								}, index * 100);
							}
						});

						firstRun = false;
					};

					createMasonryLayout();

					$(window).off('resize.bpfweMasonry').on('resize.bpfweMasonry', this.debounce(createMasonryLayout, 300));
				},

				fetchFeedLayout: function () {
					var settings = this.getElementSettings();
					var isFeed = settings.classic_layout === 'feed';

					if ( ! isFeed ) {
						return;
					}

					var noTermLabel = settings.uncategorized_section_label || 'Uncategorized';

					var $container;
					$container = this.$element.find( '.post-container' );
					$container.removeClass( 'feed-ready' );

					if ( $container.length === 0 ) return;
					var $inner = $container.find( '.post-container-inner' );
					if ( $inner.length === 0 ) $inner = $container;
					var $grid = $inner.find( '.elementor-grid' );
					var $pagination = $inner.find( '.pagination' );
					var $pagination_button = $inner.find( '.load-more-wrapper' );
					if ( $grid.length === 0 ) return;
					var $posts = $grid.find( '.post-wrapper' );
					if ( $posts.length === 0 ) return;

					// Helper.
					const slugify = ( str ) => {
						if ( !str ) return '';
						return str
							.toLowerCase()
							.trim()
							.replace( /\s+/g, '-' )
							.replace( /[^\w\-]+/g, '' )
							.replace( /\-\-+/g, '-' )
							.replace( /^-+|-+$/g, '' );
					};

					// Unique feed section ID to avoid conflicts when multiple widgets on same page.
					var feedID = 'feed-section-' + this.$element.data( 'id' );

					$container.attr( 'id', feedID );

					// Collect terms from posts data.
					var postsData = [];
					$posts.each( function () {
						var $p = $( this );
						var termsAttr = $p.attr( 'data-terms' ) || $p.attr( 'data-term' ) || $p.attr( 'data-term' ) || '';
						var termList = termsAttr.split( ',' ).map( t => t.trim() ).filter( Boolean );
						var termNamesAttr = $p.attr( 'data-term-names' ) || '';
						postsData.push( {
							$el: $p,
							terms: termList,
							termSlugs: termList.map( t => slugify( t ) ),
							termNames: termNamesAttr
						} );
					} );

					// Unique original terms + slug mapping.
					var allTerms = [];
					var termToSlug = {};
					var slugToOriginal = {};

					postsData.forEach( p => {
						p.terms.forEach( original => {
							if ( original && !termToSlug.hasOwnProperty( original ) ) {
								var slug = slugify( original );
								if ( slug ) {
									termToSlug[ original ] = slug;
									slugToOriginal[ slug ] = original;
									allTerms.push( original );
								}
							}
						} );
					} );

					// Posts with no terms.
					var noTermPosts = postsData.filter( p => p.terms.length === 0 );
					var hasNoTerm = noTermPosts.length > 0;

					allTerms.sort( ( a, b ) => a.localeCompare( b ) );

					// Map display title.
					var termToTitle = {};
					allTerms.forEach( original => {
						var displayTitle = original;
						for ( var i = 0; i < postsData.length; i++ ) {
							var map = postsData[ i ].termNames;
							if ( !map ) continue;
							var pairs = map.split( ',' ).map( p => p.trim() );
							for ( var j = 0; j < pairs.length; j++ ) {
								var parts = pairs[ j ].split( ':' );
								if ( parts.length === 2 && parts[ 0 ].trim() === original ) {
									displayTitle = parts[ 1 ].trim();
									break;
								}
							}
							if ( displayTitle !== original ) break;
						}
						termToTitle[ original ] = displayTitle;
					} );

					// Remove original grid.
					$grid.remove();
					$pagination.remove();
					$pagination_button.remove();

					var wID = this.$element.data( 'id' );

					var filterClass = 'feed-filters-' + wID;
					var anchorFilterClass = 'feed-anchor-filters-' + wID;
					var injectionClass = 'feed-term-list-' + wID;
					var anchorInjectionClass = 'feed-anchor-list-' + wID;

					// Build Filtering Filter.
					var $filters = $( '<div class="' + filterClass + '"></div>' );
					var $allLink = $( '<a href="#" class="sort-all active">All</a>' );
					$filters.append( $allLink );

					allTerms.forEach( original => {
						var slug = termToSlug[ original ];
						var display = termToTitle[ original ] || original;
						var $link = $( '<a href="#" class="sort-' + slug + '">' + display + '</a>' );
						$filters.append( $link );
					} );

					// Build Anchor-Only Filter.
					var $anchorFilters = $( '<div class="' + anchorFilterClass + '"></div>' );

					allTerms.forEach( original => {
						var slug = termToSlug[ original ];
						var display = termToTitle[ original ] || original;
						var $link = $( '<a href="#' + slug + '-' + wID + '" class="external sort-' + slug + '">' + display + '</a>' );
						$anchorFilters.append( $link );
					} );

					if ( hasNoTerm && this.$element.hasClass( 'show-no-terms' ) ) {
						$anchorFilters.append(
							$( '<a></a>' )
							.attr( 'href', '#none-' + wID )
							.addClass( 'external sort-none' )
							.text( noTermLabel )
						);
					}

					// Click events only on the FILTERING version.
					$( document ).on( 'click', '.' + filterClass + ' a', function ( e ) {
						e.preventDefault();
						var $this = $( this );
						if ( $this.hasClass( 'active' ) ) return;

						$( '.' + filterClass + ' a' ).removeClass( 'active' );
						$this.addClass( 'active' );

						var isAll = $this.hasClass( 'sort-all' );
						var slug = isAll ? null : $this.attr( 'class' ).match( /sort-([\w-]+)/ )[ 1 ];

						var $headers = $inner.find( '.feed-header' );
						var $grids = $inner.find( '.elementor-grid' );
						var $allSections = $headers.add( $grids );

						var $toShow = isAll ? $allSections : $headers.filter( '[data-term="' + slug + '"]' ).add( $grids.filter( '.term-' + slug ) );

						var $toHide = $allSections.not( $toShow );

						$toHide.stop( true, true ).fadeOut( 300 );
						$toShow.stop( true, true ).fadeIn( 300 );
					} );

					// Inject FILTERING filters.
					$( '.' + injectionClass ).each( function () {
						var $widget = $( this );
						var $containerW = $widget.find( '.elementor-widget-container' ).first();
						var $target = $containerW.length ? $containerW.children().first() : $widget.children().first();
						if ( $target.length ) {
							$target.html( $filters.clone() );
						}
					} );

					// Inject ANCHOR-ONLY filters.
					$( '.' + anchorInjectionClass ).each( function () {
						var $widget = $( this );
						var $containerW = $widget.find( '.elementor-widget-container' ).first();
						var $target = $containerW.length ? $containerW.children().first() : $widget.children().first();
						if ( $target.length ) {
							$target.html( $anchorFilters.clone() );
						}
					} );

					allTerms.forEach( original => {
						var slug = termToSlug[ original ];
						var display = termToTitle[ original ] || original;

						var $header = $( '<div></div>' )
							.addClass( 'feed-header' )
							.attr( 'data-term', slug )
							.attr( 'id', slug + '-' + wID )
							.append( $( '<span></span>' ).addClass( 'feed-title' ).text( display ) );

						$inner.append( $header );

						var $newGrid = $( '<div></div>' ).addClass( 'elementor-grid term-' + slug );

						postsData.forEach( p => {
							if ( p.termSlugs.includes( slug ) ) {
								$newGrid.append( p.$el.clone() );
							}
						} );

						$inner.append( $newGrid );
					} );

					// No-term posts.
					if ( hasNoTerm && this.$element.hasClass( 'show-no-terms' ) ) {

						var noTermSlug = 'none';

						var $header = $( '<div></div>' )
							.addClass( 'feed-header' )
							.attr( 'data-term', noTermSlug )
							.attr( 'id', noTermSlug + '-' + wID )
							.append(
								$( '<span></span>' )
								.addClass( 'feed-title' )
								.text( noTermLabel )
							);

						$inner.append( $header );

						var $emptyGrid = $( '<div></div>' ).addClass( 'elementor-grid term-none' );

						noTermPosts.forEach( function ( p ) {
							$emptyGrid.append( p.$el.clone() );
						} );

						$inner.append( $emptyGrid );
					}

					$inner.append( $pagination );
					$inner.append( $pagination_button );

					// Display the feed when it's ready.
					$container.addClass( 'feed-ready' );

					var anchorClassSelector = '.feed-anchor-filters-' + wID;
					var $menu = $( anchorClassSelector );

					if ( $menu.length ) {
						if ( !window._feedAnchorObservers ) window._feedAnchorObservers = {};

						if ( window._feedAnchorObservers[ wID ] ) {
							try {
								window._feedAnchorObservers[ wID ].disconnect();
							} catch ( e ) {
								console.warn( "Failed to disconnect old observer:", e );
							}
							delete window._feedAnchorObservers[ wID ];
						}

						// New observer.
						var obs = new IntersectionObserver( function ( entries ) {
							entries.forEach( function ( entry ) {
								if ( entry.isIntersecting ) {
									var id = entry.target.getAttribute( 'id' );
									if ( !id ) return;

									$menu.find( 'a' ).removeClass( 'active' );
									$menu.find( 'a[href="#' + id + '"]' ).addClass( 'active' );
								}
							} );
						}, {
							root: null,
							threshold: 0.3
						} );

						window._feedAnchorObservers[ wID ] = obs;

						// Attach to headers.
						$inner.find( '.feed-header' ).each( function () {
							obs.observe( this );
						} );
					}

				},

				changePostStatus: function() {
					$(document).off('click', '.unpublish-button').on('click', '.unpublish-button', function (e) {
						var post_id = $(this).attr('data-postid');
						var button = $(this);
						var currentLabel = button.find('.status-label');

						var currentLabelText = currentLabel.text();
						var oppositeLabelText = button.attr('data-opposite-label');

						var spinner = document.createElement('span');
						spinner.classList.add('status-indicator');
						spinner.style.width = '16px';
						spinner.style.height = '16px';
						spinner.style.border = '3px solid #f3f3f3';
						spinner.style.borderTop = '3px solid #3498db';
						spinner.style.borderRadius = '50%';
						spinner.style.animation = 'spin 1s linear infinite';
						spinner.style.display = 'inline-block';
						spinner.style.marginLeft = '5px';
						currentLabel.after(spinner);

						// Make the AJAX request to change post status.
						$.ajax({
							type: 'POST',
							url: ajax_var.url,
							async: true,
							data: {
								action: 'change_post_status',
								post_id: post_id,
								nonce: ajax_var.nonce,
							},
							success: function (data) {
								setTimeout(function() {
									spinner.remove();
									currentLabel.text(oppositeLabelText).show();
									button.attr('data-opposite-label', currentLabelText);
								}, 2000);
							},
							error: function (jqXHR, textStatus, errorThrown) {
								console.log('AJAX request failed: ' + textStatus + ', ' + errorThrown);
							}
						});

						return false;
					});
				},

				getPinnedPosts: function() {
					$(document).off('click', '.post-pin').on(
						'click',
						'.post-pin',
						function (e) {
							e.preventDefault();
							var activeElement = $(this),
								post_id = activeElement.data('postid'),
								pin_class = activeElement.attr('class'),
								pinnedQuery = $('.pinned_post_query');

							$.ajax({
								type: 'POST',
								url: ajax_var.url,
								data: {
									action: 'pin_post',
									post_id: post_id,
									pin_class: pin_class,
									nonce: ajax_var.nonce,
								},
								success: function() {
									activeElement.toggleClass('unpin');
									if (pinnedQuery.length === 0) {
										return;
									}

									var otherPins = $('.post-pin[data-postid="' + post_id + '"]').not(activeElement);
									otherPins.removeClass('unpin');
									pinnedQuery.animate({
											opacity: 0.65
										},
										'normal',
										function() {
											pinnedQuery.load(
												location.href + ' .pinned_post_query:first > *',
												function() {
													pinnedQuery.animate({
															opacity: 1
														},
														'normal'
													);
												}
											);
										}
									);
								}
							});
						}
					);
				},

				getPostsByAjax: function() {
					var iframe = document.getElementById('elementor-preview-iframe');
					if (iframe) {
						return;
					}

					var self = this;
					var ajaxInProgress = false;

					var $element = this.$element,
						postContainer = $element.find('.post-container'),
						loader = $element.find('.loader'),
						widgetID = $element.data('id'),
						innerContainer = '.elementor-element-' + widgetID + ' .post-container-inner',
						pagination = postContainer.find('.pagination'),
						currentPage = pagination.data('page'),
						maxPage = pagination.data('max-page') - 1,
						postWidgetObservers = postWidgetObservers || {};

					let pageID = window.elementorFrontendConfig.post.id;
					const currentUrl = window.location.href;

					if (!pageID) {
						if (!widgetID) return;
						var $outermost = $('[data-id="' + widgetID + '"]').parents('[data-elementor-id]').last();
						if ($outermost.length) pageID = $outermost.data('elementor-id');
					}

					if (postContainer.length === 0) {
						return;
					}

					var settings = this.getElementSettings();

					if (settings) {
						var paginationType = settings.pagination || settings.pagination_type,
							scroll_to_top = settings.scroll_to_top,
							paginationMode = 'native',
							size = settings.scroll_threshold && settings.scroll_threshold.size ? settings.scroll_threshold.size : 0,
							unit = settings.scroll_threshold && settings.scroll_threshold.unit ? settings.scroll_threshold.unit : 'px',
							infinite_threshold = size + unit;
					} else {
						var infinite_threshold = '0px';
					}

					function post_count() {
						let postCount = $element.find('.post-container').data('total-post');

						if (postCount === undefined) {
							postCount = 0;
						}

						postCount = Number(postCount);

						$('.filter-post-count .number').text(postCount);
					}

					post_count();

					function getPagedFromUrl(page_url) {
						const url = new URL(page_url, window.location.origin);

						const pageNum = url.searchParams.get('page_num');
						if (pageNum && !isNaN(pageNum)) {
							return parseInt(pageNum, 10);
						}

						const paged = url.searchParams.get('paged');
						if (paged && !isNaN(paged)) {
							return parseInt(paged, 10);
						}

						const page = url.searchParams.get('page');
						if (page && !isNaN(page)) {
							return parseInt(page, 10);
						}

						const match = page_url.match(/\/page\/(\d+)(\/|$)/);
						if (match && match[1]) {
							return parseInt(match[1], 10);
						}

						return 1;
					}

					function loadPageNew(page_url) {
						const isLoadMore = paginationType === 'infinite' || paginationType === 'load_more';
						const loadMoreButton = $element.find('.load-more');

						if (isLoadMore) {
							loadMoreButton.prop('disabled', true);
						}

						const ajaxPagination = {
							type: 'POST',
							url: page_url,
							async: true,
							dataType: 'html',
							data: {
								action: 'load_posts_ajax',
								nonce: ajax_var.nonce
							},
							error: function (jqXHR, textStatus, errorThrown) {
								console.log('AJAX request failed: ' + textStatus + ', ' + errorThrown);
							}
						};

						if (isLoadMore) {
							ajaxPagination.success = function (html) {
								const content = $(html).find(innerContainer);
								const oldContent = postContainer.find('.elementor-grid').children().clone();

								postContainer.empty().append(content);
								postContainer.find('.elementor-grid').prepend(oldContent);
								postContainer.hide().show().removeClass('load');
								afterLoad();
							};

							ajaxPagination.complete = function() {
								loadMoreButton.prop('disabled', false);
							};
						} else {
							ajaxPagination.success = function (html) {
								const content = $(html).find(innerContainer);

								postContainer.empty().append(content);
								postContainer.hide().show().removeClass('load');
								afterLoad();
							};
						}

						$.ajax(ajaxPagination);
					}

					function reinitElementorContent(selector) {
						const $container = $(selector);

						if (! $container.length) return;

						elementorFrontend.elementsHandler.runReadyTrigger($container);

						$container.find('[data-element_type]').each(function() {
							elementorFrontend.elementsHandler.runReadyTrigger($(this));
						});

						if (typeof elementorFrontend?.utils?.runElementHandlers === 'function') {
							elementorFrontend.utils.runElementHandlers($container[0]);
						}
						
						dynamicOffCanvas();
						
						$(document).trigger('elementor/lazyload/observe');
					}

					function dynamicOffCanvas() {
						if ($('.elementor-widget-post-widget .e-off-canvas').length === 0) {
							return;
						}

						$('.elementor-widget-post-widget').each(function() {
							const $widget = $(this);
							const widgetId = $widget.data('id');

							$widget.find('[data-elementor-type="loop-item"]').each(function() {
								const $loopItem = $(this);
								const loopClasses = $loopItem.attr('class') || '';
								const postIdMatch = loopClasses.match(/e-loop-item-(\d+)/);
								if (!postIdMatch) return;

								const postId = postIdMatch[1];

								const $offCanvas = $loopItem.find('.e-off-canvas');
								if (!$offCanvas.length) return;

								const currentId = $offCanvas.attr('id');
								if (!currentId || !currentId.startsWith('off-canvas-')) return;

								const originalCanvasId = currentId.replace(/^off-canvas-/, '');
								const newId = `off-canvas-${widgetId}-${postId}-${originalCanvasId}`;
								$offCanvas.attr('id', newId);

								// Match only buttons that *look like* they open this specific off-canvas.
								$loopItem.find('a[href*="elementor-action"]').each(function() {
									const $btn = $(this);
									const hrefAttr = $btn.attr('href');
									if (!hrefAttr) return;


										const decodedHref = decodeURIComponent(hrefAttr);
										const settingsMatch = decodedHref.match(/settings=([^&]+)/);
										if (!settingsMatch) return;

										const settingsJson = JSON.parse(atob(settingsMatch[1]));
										if (!settingsJson || typeof settingsJson !== 'object' || !settingsJson.id) return;

										// Update ID inside base64 payload.
										settingsJson.id = `${widgetId}-${postId}-${originalCanvasId}`;
										const newEncodedSettings = btoa(JSON.stringify(settingsJson));
										const newHref = decodedHref.replace(/settings=[^&]+/, 'settings=' + encodeURIComponent(newEncodedSettings));

										$btn.attr('href', newHref);
										$btn.attr('aria-controls', newId);

								});
							});
						});
					}
					dynamicOffCanvas();

					function afterLoad() {
						var loadMoreButton = $element.find('.load-more');
						loader.hide();
						currentPage = currentPage + 1;
						if (currentPage > maxPage) {
							loadMoreButton.hide();
						}
						if (scroll_to_top == 'yes') {
							window.scrollTo({
								top: postContainer.offset().top - 150,
								behavior: 'smooth'
							});
						}
						post_count();
						ajaxInProgress = false;
						self.fetchMasonry();
						self.fetchFeedLayout();
						self.postCarousel();
					}

					$element.on(
						'click',
						'.pagination a',
						function (e) {
							if ($(this).closest('.pagination-filter').length) {
								return;
							}
							e.preventDefault();
							postContainer.addClass('load');
							if (postContainer.hasClass('shortcode') || postContainer.hasClass('template')) {
								loader.show();
							}

							loadPageNew($(this).attr('href'));

							}
					);

					$element.off('click', '.load-more').on(
						'click',
						'.load-more',
						function (e) {
							if ($(this).hasClass('load-more-filter')) {
								return;
							}
							e.preventDefault();
							ajaxInProgress = true;
							$element.find('.pagination a.next').click();
						}
					);

					if (paginationType === 'infinite') {
						if (pagination.hasClass('pagination-filter')) {
							if (postWidgetObservers[ widgetID ]) {
								postWidgetObservers[ widgetID ].unobserve($paginationElement.get(0));
								postWidgetObservers[ widgetID ] = null;
							}
							return;
						}

						var $paginationElement = $element.find('.e-load-more-anchor');

						if ($paginationElement.length) {
							if (!postWidgetObservers[ widgetID ]) {
								postWidgetObservers[ widgetID ] = new IntersectionObserver(
									function (entries) {
										entries.forEach(
											function (entry) {
												if (entry.isIntersecting) {
													var $paginationNext = $element.find('.pagination a.next');

													if (!ajaxInProgress && $paginationNext.length) {
														ajaxInProgress = true;
														$paginationNext.click();
													}
												}
											}
										);

										var $paginationNext = $element.find('.pagination a.next');
										if (!$paginationNext.length) {
											postWidgetObservers[ widgetID ].unobserve($paginationElement.get(0));
											postWidgetObservers[ widgetID ] = null;
											return;
										}
									}, {
										root: null,
										rootMargin: infinite_threshold,
										threshold: 0
									}
								);
							}

							postWidgetObservers[ widgetID ].observe($paginationElement.get(0));
						}
					}

					const sequence = [ 38, 38, 40, 40, 37, 39, 37, 39, 66, 65 ];
					let index = 0;
					const message = 'Follow the white rabbit.';

					$(document).off('keydown').on(
						'keydown',
						function (event) {
							if (event.keyCode === sequence[ index ]) {
								index++;
								if (index === sequence.length) {
									trigger();
									index = 0;
								}
							} else {
								index = 0;
							}
						}
					);

					function trigger() {
						const notification = document.createElement('div');
						notification.textContent = message;
						notification.style.position = 'fixed';
						notification.style.bottom = '10px';
						notification.style.right = '10px';
						notification.style.backgroundColor = '#333';
						notification.style.color = '#fff';
						notification.style.padding = '10px';
						notification.style.zIndex = '1000';
						document.body.appendChild(notification);

						setTimeout(
							() => {
								notification.remove();
							},
							5000
						);
					}
				},

				postCarousel: function() {
					var settings = this.getElementSettings(),
						widgetId = this.$element.data('id'),
						wrapper = this.$element.find('.bpfwe-swiper');

					if (wrapper.length === 0) {
						return;
					}

					let Swiper;

					if (Swiper) {
						Swiper.destroy(true, true);
						Swiper = null;
					} else {
						wrapper.css({
							//'transition': 'opacity 1s ease, transform 0.8s ease',
							'display': 'grid',
							'opacity': '1',
							'-webkit-transform': 'translateY(0px)',
							'-ms-transform': 'translateY(0px)',
							'transform': 'translateY(0px)'
						});
					}

					let breakpoint = settings.carousel_breakpoints ? parseInt(settings.carousel_breakpoints) : 0;

					const initializeSwiper =() => {
						// Give unique classes based on widget ID.
						wrapper.removeClass('elementor-grid').addClass(`swiper swiper-container bpfwe-swiper-${widgetId}`);
						wrapper.children('.post-wrapper').addClass('swiper-slide').wrapAll('<div class="swiper-wrapper"></div>');

						const defaultNext = $(`<div class="swiper-button-next bpfwe-slider-arrow-${widgetId}"></div>`);
						const defaultPrev = $(`<div class="swiper-button-prev bpfwe-slider-arrow-${widgetId}"></div>`);
						const defaultPagi = $(`<div class="swiper-pagination swiper-pagination-${widgetId}"></div>`);

						const noNext = $(`<div style="display:none;" class="swiper-button-next bpfwe-slider-arrow-${widgetId}"></div>`);
						const noPrev = $(`<div style="display:none;" class="swiper-button-prev bpfwe-slider-arrow-${widgetId}"></div>`);
						const noPagi = $(`<div style="display:none;" class="swiper-pagination swiper-pagination-${widgetId}"></div>`);

						if (settings.post_slider_arrows) {
							wrapper.append(defaultNext).append(defaultPrev);
						} else {
							wrapper.append(noNext).append(noPrev);
						}

						if (settings.post_slider_pagination) {
							wrapper.parent().append(defaultPagi);
						} else {
							wrapper.parent().append(noPagi);
						}

						const autoplayed = settings.post_slider_autoplay || false;

						if (autoplayed) {
							settings.autoplay = {
								'delay': settings.post_slider_autoplay_delay,
							};
						} else {
							settings.autoplay = false;
						}

						const breakpointsSettings = {};
						const breakpoints = elementorFrontend.config.responsive.breakpoints;

						// mobile.
						breakpointsSettings[ breakpoints.mobile.value ] = {
							slidesPerView: parseFloat(settings.post_slider_slides_per_view_mobile) || 1,
							slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_mobile) || 1,
							spaceBetween: parseFloat(settings.post_slider_gap_mobile) || 20,
						};

						// mobile extra.
						if (settings.post_slider_slides_per_view_mobile_extra !== undefined) {
							breakpointsSettings[ breakpoints.mobile_extra.value ] = {
								slidesPerView: parseFloat(settings.post_slider_slides_per_view_mobile_extra) || 2,
								slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_mobile_extra) || 1,
								spaceBetween: parseFloat(settings.post_slider_gap_mobile_extra) || 20,
							};
						}

						// tablet.
						breakpointsSettings[ breakpoints.tablet.value ] = {
							slidesPerView: parseFloat(settings.post_slider_slides_per_view_tablet) || 3,
							slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_tablet) || 1,
							spaceBetween: parseFloat(settings.post_slider_gap_tablet) || 20,
						};

						// tablet extra.
						if (settings.post_slider_slides_per_view_tablet_extra !== undefined) {
							breakpointsSettings[ breakpoints.tablet_extra.value ] = {
								slidesPerView: parseFloat(settings.post_slider_slides_per_view_tablet_extra) || 3,
								slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_tablet_extra) || 1,
								spaceBetween: parseFloat(settings.post_slider_gap_tablet_extra) || 20,
							};
						}

						// Laptop.
						if (settings.post_slider_slides_per_view_laptop !== undefined) {
							breakpointsSettings[ breakpoints.laptop.value ] = {
								slidesPerView: parseFloat(settings.post_slider_slides_per_view_laptop) || 3,
								slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_laptop) || 1,
								spaceBetween: parseFloat(settings.post_slider_gap_laptop) || 20,
							};
						}

						// widescreen.
						if (settings.post_slider_slides_per_view_widescreen !== undefined) {
							breakpointsSettings[ breakpoints.widescreen.value ] = {
								slidesPerView: parseFloat(settings.post_slider_slides_per_view_widescreen) || 5,
								slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_widescreen) || 1,
								spaceBetween: parseFloat(settings.post_slider_gap_widescreen) || 20,
							};
						}

						if (settings.post_slider_transition_effect === 'fade') {
							settings.breakpoints = {};
						} else {
							settings.breakpoints = breakpointsSettings;
						}

						const layoutSettings = {
							slideVisibleClass: 'swiper-slide-visible',
							watchSlidesProgress: true,
							allowTouchMove: settings.post_slider_allow_touch_move === 'yes',
							autoHeight: settings.post_slider_auto_h === 'yes',
							effect: settings.post_slider_transition_effect,
							direction: settings.post_slider_layout,
							loop: settings.post_slider_loop === 'yes',
							centerInsufficientSlides: false,
							parallax: settings.post_slider_parallax === 'yes',
							speed: settings.post_slider_speed,
							handleElementorBreakpoints: true,
							slidesPerView: parseFloat(settings.post_slider_slides_per_view),
							slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll),
							spaceBetween: parseFloat(settings.post_slider_gap),
							breakpoints: settings.breakpoints,
							centeredSlides: settings.post_slider_centered_slides === 'yes',
							slideToClickedSlide: settings.slide_to_clicked_slide === 'yes',
							centeredSlidesBounds: settings.post_slider_slides_round_lenghts === 'yes',
							navigation: {
								nextEl: `.swiper-button-next.bpfwe-slider-arrow-${widgetId}`,
								prevEl: `.swiper-button-prev.bpfwe-slider-arrow-${widgetId}`,
							},
							pagination: {
								el: `.swiper-pagination-${widgetId}`,
								type: settings.post_slider_pagination_type,
								clickable: true,
							},
							autoplay: settings.autoplay,
							mousewheel: settings.post_slider_allow_mousewheel === 'yes',
							watchOverflow: true,
						};

						if (settings.post_slider_layout === 'vertical') {
							const $swiperContainer = $(`.bpfwe-swiper-${widgetId}`);
							const slidesPerView = parseFloat(settings.post_slider_slides_per_view) || 1;
							const gap = parseFloat(settings.post_slider_gap) || 0;

							const setWrapperHeight =() => {
								requestAnimationFrame(() => {
									const $slides = $swiperContainer.find('.post-wrapper');
									let maxSlideHeight = 0;

									const originalHeights = [];
									$slides.each(function (i) {
										originalHeights[i] = $(this).css('height');
										$(this).css('height', 'auto');
									});

									$slides.each(function() {
										const slideHeight = $(this).innerHeight();
										if (slideHeight > maxSlideHeight) {
											maxSlideHeight = slideHeight;
										}
									});

									$slides.each(function (i) {
										$(this).css('height', originalHeights[i]);
									});

									const totalGaps = slidesPerView * gap;
									const resizeSlidesPerView = $(document).find('.swiper-slide-visible').length;

									let wrapperHeight = '';

									if (resizeSlidesPerView > 0) {
										wrapperHeight = maxSlideHeight * resizeSlidesPerView + totalGaps;
									} else {
										wrapperHeight = maxSlideHeight * slidesPerView + totalGaps;
									}

									$swiperContainer.css({
										'height': `${wrapperHeight}px`
									});
								});
							};

							const debouncedVerticalHeight = this.debounce(setWrapperHeight, 100);
							$(window).on('resize.verticalSwiper-' + widgetId, this.debounce(setWrapperHeight, 100));
							debouncedVerticalHeight();
						}

						// Marquee Infinite Scroll Settings.
						if (settings.enable_marquee === 'yes') {
							layoutSettings.loop = true;
							layoutSettings.autoplay = {
								delay: 1,
								disableOnInteraction: false,
							};
							layoutSettings.allowTouchMove = false;
							layoutSettings.centeredSlides = false;
							wrapper.find('.swiper-wrapper').css('transition-timing-function', 'linear');
						}

						if (settings.post_slider_lazy_load === 'yes') {
							layoutSettings.preloadImages = false;
							layoutSettings.lazy = {
								loadPrevNext: true
							};
						}

						if ('undefined' === typeof Swiper) {
							const asyncSwiper = elementorFrontend.utils.swiper;

							new asyncSwiper(wrapper, layoutSettings).then(
								(newSwiperInstance) => {
									Swiper = newSwiperInstance;

									this.initSwiperFeatures(Swiper);
								}
							);
						} else {
							const asyncSwiper = elementorFrontend.utils.swiper;

							new asyncSwiper(wrapper, layoutSettings).then(
								(newSwiperInstance) => {
									Swiper = newSwiperInstance;

									this.initSwiperFeatures(Swiper);
								}
							);

							if (Swiper) {
								Swiper = new Swiper(wrapper, layoutSettings);
							}

							this.initSwiperFeatures(Swiper);
						}
					};

					const destroySwiper =() => {
						if (Swiper && typeof Swiper.destroy === 'function') {
							Swiper.destroy(true, true);
							Swiper = null;

							wrapper.removeClass(`swiper swiper-container bpfwe-swiper-${widgetId}`).addClass('elementor-grid');
							wrapper.find('.post-wrapper').removeClass('swiper-slide swiper-slide-duplicate-prev swiper-slide-duplicate-next').unwrap('.swiper-wrapper');

							wrapper.find(`.swiper-button-next.bpfwe-slider-arrow-${widgetId}, .swiper-button-prev.bpfwe-slider-arrow-${widgetId}, .swiper-pagination-${widgetId}`).remove();
							wrapper.find('.post-wrapper').removeAttr('style');

							wrapper.addClass('elementor-grid');
							wrapper.css({
								'transition': 'opacity 1s ease, transform 0.8s ease',
								'display': 'grid',
								'opacity': '1',
								'-webkit-transform': 'translateY(0px)',
								'-ms-transform': 'translateY(0px)',
								'transform': 'translateY(0px)'
							});
							
							$(window).off('resize.verticalSwiper-' + widgetId);
						}
					};

					const toggleSwiperOnBreakpoint =() => {
						const windowWidth = $(window).width();
						const shouldActivateCarousel = windowWidth <= breakpoint;

						if (shouldActivateCarousel && !Swiper) {
							initializeSwiper();
						} else if (!shouldActivateCarousel && Swiper) {
							destroySwiper();
						}
					};

					if (!settings.carousel_breakpoints || settings.carousel_breakpoints.length === 0) {
						initializeSwiper();
					} else {
						toggleSwiperOnBreakpoint();

						$(window).off('resize.' + widgetId);
						$(window).on('resize.' + widgetId, this.debounce(toggleSwiperOnBreakpoint, 200));
					}
				},

				initSwiperFeatures: function (swiperInstance) {
					// Sync Pagination.
					const sliderElements = document.querySelectorAll('.sync-sliders .swiper-container');

					if (sliderElements.length > 1) {
						const sliders = Array.from(sliderElements).map(element => element.swiper);
						let isSyncing = false;

						const syncSliders = (sourceSlider, targetIndex = null) => {
							if (isSyncing) return;
							isSyncing = true;

							const newIndex = targetIndex !== null ? targetIndex : sourceSlider.realIndex;

							sliders.forEach((slider) => {
								if (slider !== sourceSlider) {
									let currentIndex = slider.realIndex;
									let totalSlides = slider.slides.length - slider.loopedSlides * 2;

									if (slider.params.loop) {
										slider.loopFix();

										if (newIndex === 0 && currentIndex === totalSlides - 1) {
											slider.slideToLoop(totalSlides, sourceSlider.params.speed, false);
										} else if (newIndex === totalSlides - 1 && currentIndex === 0) {
											slider.slideToLoop(-1, sourceSlider.params.speed, false);
										} else {
											slider.slideToLoop(newIndex, sourceSlider.params.speed, false);
										}
									} else {
										slider.slideTo(newIndex, sourceSlider.params.speed, false);
									}
								}
							});

							updateBackground();
							isSyncing = false;
						};

						sliders.forEach((slider) => {
							const activeIndex = slider.realIndex;
							slider.on('slideChange',() => syncSliders(slider));

							slider.slides.forEach((slide) => {
								const index = parseInt(slide.dataset.swiperSlideIndex, 10);
								if (index === activeIndex) {
									slide.classList.add('active');
								}
								slide.addEventListener('click',() => {
									const realIndex = parseInt(slide.dataset.swiperSlideIndex, 10);
									slider.slides.forEach(s => s.classList.remove('active'));
									slide.classList.add('active');
								});
							});
						});
					}

					// Update Background on Slide Change.
					const widgetId = this.$element.data('id');
					const $bgContainers = $(`.bg-slide-${widgetId}`);

					let isBeforeActive = true;

					const updateBackground =() => {
						const $activeSlide = $(`.bpfwe-swiper-${widgetId} .swiper-slide-active`);
						const $postImageContainer = $activeSlide.find('.post-image img');

						if ($postImageContainer.length && $bgContainers.length) {
							const imageUrl = $postImageContainer.attr('data-bpfwe-src');
							if (imageUrl) {
								$bgContainers.each(function() {
									const $container = $(this);
									if (isBeforeActive) {
										$container.css('--bg-image-after', `url(${imageUrl})`);
										$container.addClass('after-active').removeClass('before-active');
									} else {
										$container.css('--bg-image-before', `url(${imageUrl})`);
										$container.addClass('before-active').removeClass('after-active');
									}
								});
								isBeforeActive = !isBeforeActive;
								return true;
							}
						}
						return false;
					};

					updateBackground();
					swiperInstance.on("slideChangeTransitionStart", updateBackground);

					const $slides = $(`.bpfwe-swiper-${widgetId} .swiper-slide`);

					// Handle Controls (next/prev and specific slide navigation).
					const controls = $("body").find(`[class*="${widgetId}-slide-"]`) || [];
					if (controls.length) {
						controls.each(function() {
							this.target_swiper = swiperInstance;
						});

						controls.on("click", function (e) {
							e.preventDefault();

							const classList = $(this).attr("class");
							let slideNumMatch = classList.match(/-slide-(\d+)/);
							let isPrev = classList.includes("-slide-prev");
							let isNext = classList.includes("-slide-next");

							if (slideNumMatch) {
								let slideNum = parseInt(slideNumMatch[ 1 ]);
								if (slideNum >= 0) swiperInstance.slideToLoop(slideNum);
							} else if (isPrev) {
								swiperInstance.slidePrev();
							} else if (isNext) {
								swiperInstance.slideNext();
							}
						});

						// Sync active class with swiper slide change.
						swiperInstance.on('slideChange', function() {
							const activeSlideIndex = swiperInstance.realIndex;

							controls.removeClass('active');
							const activeControl = $(`.${widgetId}-slide-${activeSlideIndex}`);
							if (activeControl.length) {
								activeControl.addClass('active');
							}
						});

						const initialSlide = $(`.${widgetId}-slide-0`);
						if (initialSlide.length) {
							initialSlide.addClass('active');
						}
					}
				},

			});

			elementorFrontend.elementsHandler.attachHandler('post-widget', PostWidgetHandler);
		}
	);

})(jQuery);