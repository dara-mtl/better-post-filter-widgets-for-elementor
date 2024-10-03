(function ($) {
    $(window).on('elementor/frontend/init', function () {
        var PostWidgetHandler = elementorModules.frontend.handlers.Base.extend({

            bindEvents: function() {
				this.fetchMasonry();
				this.changePostStatus();
				this.getPinnedPosts();
				this.getPostsByAjax();
				this.getAjaxSearch();
				this.postCarousel();
            },
			
            debounce: function(func, delay) {
                let timeoutId;
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        func.apply(context, args);
                    }, delay);
                };
            },

			fetchMasonry: function() {
				var settings = this.getElementSettings(),
					$container = this.$element.find('.cwm-masonry');

				if ($container.length === 0) {
					return;
				}

				var $masonryElements = $container.find('.post-wrapper');

				if ($masonryElements.length === 0) {
					return;
				}


				const breakpoints = elementorFrontend.config.responsive.breakpoints;

				const getColumns = () => {
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

				const createMasonryLayout = () => {
					const columns = getColumns();
					$container.removeClass().addClass('elementor-grid cwm-masonry masonry-layout columns-' + columns);
					$container.children('.masonry-column').remove();

					for (let i = 1; i <= columns; i++) {
						const $newColumn = $('<div></div>').addClass('masonry-column masonry-column-' + i);
						$container.append($newColumn);
					}

					let countColumn = 1;

					$container.find('.post-wrapper').remove();

					$masonryElements.each(function(index, element) {
						const $col = $container.find('.masonry-column-' + countColumn);
						$(element).css({
							opacity: '0',
						});

						$col.append($(element));
						countColumn = countColumn < columns ? countColumn + 1 : 1;
					});

					setTimeout(function() {
						$masonryElements.css({
							opacity: '1',
						});
					}, 100);
				};

				createMasonryLayout();

				$(window).on('resize', this.debounce(createMasonryLayout, 200));
			},


			changePostStatus: function () {
				$(document).off('click', '.edit-button, .unpublish-button').on('click', '.edit-button, .unpublish-button', function(e){
					var post_id = $(this).attr('data-postid');
					var editButton = $(this); // Declare editButton

					$.ajax({
						type: "POST",
						url : ajax_var.url,
						async: true,
						data: {
							action: 'change_post_status',
							'post_id': post_id,
							nonce: ajax_var.nonce,
						},
						success: function( data ) {
							editButton.removeAttr('href').removeAttr('onclick').text('Done!');
						},
						error: function( jqXHR, textStatus, errorThrown ) { // Error handling
							console.log('AJAX request failed: ' + textStatus + ', ' + errorThrown);
						}
					});
					return false;
				});
			},
			
			getPinnedPosts: function () {
				$(document).off('click', '.post-pin').on('click', '.post-pin', function(e){
					e.preventDefault();
					var activeElement = $(this),
						post_id = activeElement.data('postid'),
						pin_class = activeElement.attr('class'),
						pinnedQuery = $('.pinned_post_query');

					$.ajax({
						type: "POST",
						url : ajax_var.url,
						data: {
							'action': 'pin_post',
							'post_id': post_id,
							'pin_class': pin_class,
							nonce: ajax_var.nonce,
						},
						success:function() {
							activeElement.toggleClass('unpin');
							if (pinnedQuery.length === 0)
							return;

							var otherPins = $('.post-pin[data-postid="'+post_id+'"]').not(activeElement);
							otherPins.removeClass('unpin');
							pinnedQuery.animate({ opacity: 0.65 }, 'normal', function(){
								pinnedQuery.load(location.href + ' .pinned_post_query:first > *', function(){
									pinnedQuery.animate({ opacity: 1 }, 'normal');
								});
							});
						}
					});
				});
			},
			
			getPostsByAjax: function () {
				var self = this;
				var ajaxInProgress = false;
				var $element = this.$element,
					postContainer = $element.find('.post-container'),
					loader = $element.find('.loader'),
					widgetID = $element.data('id'),
					innerContainer = ' .elementor-element-' + widgetID +' .post-container-inner',
					pagination = postContainer.find('.pagination'),
					currentPage = pagination.data('page'),
					maxPage = pagination.data('max-page')-1;

				if (postContainer.length === 0)
					return;

				var settings = this.getElementSettings();

				if (settings) {
					var paginationType = settings.pagination || settings.pagination_type,
						scroll_to_top = settings.scroll_to_top,
						size = settings.scroll_threshold && settings.scroll_threshold.size ? settings.scroll_threshold.size : 0,
						unit = settings.scroll_threshold && settings.scroll_threshold.unit ? settings.scroll_threshold.unit : 'px',
						infinite_threshold = size + unit;
				} else {
					var infinite_threshold = '0px';
				}
				
				function loadPage(page_url) {
					if (paginationType == 'infinite' || paginationType == 'load_more') {
						var loadMoreButton = $element.find('.load-more');
						
						loadMoreButton.text('Loading...');
						loadMoreButton.prop('disabled', true);
						
						$.get(page_url, function(data) {
							var oldContent = postContainer.find('.elementor-grid').children().clone();
							postContainer.empty().append($(data).find(innerContainer));
							postContainer.find('.elementor-grid').prepend(oldContent);
							postContainer.hide().show().removeClass('load');
							afterLoad();
						}).fail(function(jqXHR, textStatus, errorThrown) {
							console.error('An error occurred while fetching the posts: ' + textStatus);
							console.error('HTTP status: ' + jqXHR.status);
							console.error('Error thrown: ' + errorThrown);
						});
					} else {
						$.get(page_url, function(data) {
							postContainer.empty().append($(data).find(innerContainer));
							postContainer.hide().show().removeClass('load');
							afterLoad();
						}).fail(function(jqXHR, textStatus, errorThrown) {
							console.error('An error occurred while fetching the posts: ' + textStatus);
							console.error('HTTP status: ' + jqXHR.status);
							console.error('Error thrown: ' + errorThrown);
						});
					}
				}

				function afterLoad() {
					var loadMoreButton = $element.find('.load-more');
					loader.hide();
					currentPage = currentPage + 1;
					if (currentPage > maxPage) {
						loadMoreButton.hide();
					}
					if(scroll_to_top == 'yes'){
						window.scrollTo({
							top: postContainer.offset().top - 150,
							behavior: 'smooth'
						});
					}
					ajaxInProgress = false;
					self.fetchMasonry();
				}

				$element.on('click', '.pagination a', function(e) {
					if ($(this).closest('.pagination-filter').length) {
						return;
					}
					e.preventDefault();
					postContainer.addClass('load');
					if(postContainer.hasClass('shortcode') || postContainer.hasClass('template')) {
						loader.show();
					}
					loadPage($(this).attr('href'));
				});

				$element.off('click.postWidget', '.load-more').on('click', '.load-more', function(e) {
					if ($(this).hasClass('load-more-filter')) {
						return;
					}
					e.preventDefault();
					ajaxInProgress = true;
					$element.find('.pagination a.next').click();
				});

				if (paginationType === 'infinite') {
					var postWidgetObservers = postWidgetObservers || {};

					if (pagination.hasClass('pagination-filter')) {
						if (postWidgetObservers[widgetID]) {
							postWidgetObservers[widgetID].unobserve($paginationElement.get(0));
						}
						return;
					}

					var $paginationElement = $element.find('.e-load-more-anchor');

					if ($paginationElement.length) {
						if (!postWidgetObservers[widgetID]) {
							postWidgetObservers[widgetID] = new IntersectionObserver(function(entries) {
								entries.forEach(function(entry) {
									if (entry.isIntersecting) {
										var $paginationNext = $element.find('.pagination a.next');

										if (!ajaxInProgress && $paginationNext.length) {
											ajaxInProgress = true;
											$paginationNext.click();
										}
									}
								});

								var $paginationNext = $element.find('.pagination a.next');
								if (!$paginationNext.length) {
									postWidgetObservers[widgetID].unobserve($paginationElement.get(0));
								}
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							});
						}

						postWidgetObservers[widgetID].observe($paginationElement.get(0));
					}
					
					$(window).on('resize', function() {
						if ($paginationElement.length && !ajaxInProgress) {
							postWidgetObservers[widgetID].observe($paginationElement.get(0));
						}
					});
				}
			},
			
			getAjaxSearch: function() {
				//Check if the filter widget is on the same page, if yes, run the other script instead
				if ($('div.filter-container').length) {
					return;
				}
				
				var paged = '';
				
				var settings = this.getElementSettings(),
					scroll_to_top = settings.scroll_to_top,
					post_status = settings.post_status,
					post_type = settings.post_type_search;
					
				var widgetContainer = this.$element,
					postContainer = widgetContainer.find('.post-container'),
					loader = widgetContainer.find('.loader'),
					widgetID = widgetContainer.data('id'),
					pageID = window.elementorFrontendConfig.post.id,
					originalState = widgetContainer.html();
				
				if(pageID === 0 || pageID === 'undefined') {
					pageID = $(document).find('main div').data('elementor-id');
				}				

				widgetContainer.on( 'click', '.pagination-filter a', function(e) {
					var url = $(this).attr('href');
					var hashes = url.split("?")[1];
					var paged = hashes.split("=")[1];
					get_search_values(paged);
					e.preventDefault();
				});

				widgetContainer.on( 'submit', '.search-container form', function() {
					get_search_values();
					return false;
				});
					
				function get_search_values(paged) {
					postContainer.addClass('load');
					if(postContainer.hasClass('shortcode') || postContainer.hasClass('template')) {
						loader.fadeIn();
					}
					var input = $('.search-container form').find('input[name="s"]');
					var query = input.val();

					$.ajax({
						type: "POST",
						url : ajax_var.url,
						async: true,
						data: {
							action: 'post_filter_results',
							widget_id: widgetID,
							page_id: pageID,
							search_query: query,
							post_type: post_type,
							paged: paged,
							base: window.location.pathname,
							nonce: ajax_var.nonce,
						},
						success: function (data) {
							var response = JSON.parse(data),
								html = response.html,
								base = response.base;

							if (data === '0') {
								widgetContainer.off();
								widgetContainer.html(originalState).removeClass('load');
							} else {
								loader.fadeOut();
								widgetContainer.html(html).fadeIn().removeClass('load');
								var pagination = widgetContainer.find('nav[aria-label="Pagination"]');
								pagination.addClass('pagination-filter');                    
								pagination.find('a.page-numbers').each(function() {
									var href = $(this).attr('href');
									if (base.endsWith('/')) {
										base = base.slice(0, -1);
									}
									var newHref = href.replace("/wp-admin/admin-ajax.php", base);
									$(this).attr('href', newHref);
								});
							}
						},
						complete: function () {
							if (scroll_to_top == 'yes') {
								window.scrollTo({
									top: postContainer.offset().top - 150,
									behavior: 'smooth'
								});
							}
											
							elementorFrontend.elementsHandler.runReadyTrigger(widgetContainer);
							if (elementorFrontend.config.experimentalFeatures.e_lazyload) {
								document.dispatchEvent(new Event('elementor/lazyload/observe'));
							}

						}
					});
				}
			},

			postCarousel: function () {
				var settings = this.getElementSettings(),
					wrapper = this.$element.find( '.cwm-swiper' );

				if (wrapper.length === 0) {
					return;
				}

				let Swiper;

				if (Swiper) {
					Swiper.destroy(true, true);
					Swiper = null;
				}

				wrapper.addClass('swiper swiper-container');
				wrapper.children('.post-wrapper').addClass('swiper-slide').wrapAll('<div class="swiper-wrapper"></div>');

				const defaultNext = $('<div class="swiper-button-next cwm-slider-arrow"></div>');
				const defaultPrev = $('<div class="swiper-button-prev cwm-slider-arrow"></div>');
				const defaultPagi = $('<div class="swiper-pagination"></div>');

				const noNext = $('<div style="display:none;" class="swiper-button-next cwm-slider-arrow"></div>');
				const noPrev = $('<div style="display:none;" class="swiper-button-prev cwm-slider-arrow"></div>');
				const noPagi = $('<div style="display:none;" class="swiper-pagination"></div>');

				// append controls: next prev pagination
				if (settings.post_slider_arrows) {
					wrapper
						.append(defaultNext)
						.append(defaultPrev);
				} else {
					wrapper
						.append(noNext)
						.append(noPrev);
				}

				if (settings.post_slider_pagination) {
					wrapper.append(defaultPagi);
				} else {
					wrapper.append(noPagi);
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

				// mobile
				breakpointsSettings[breakpoints.mobile.value] = {
					slidesPerView: parseFloat(settings.post_slider_slides_per_view_mobile) || 1,
					slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_mobile) || 1,
					spaceBetween: parseFloat(settings.post_slider_gap_mobile) || parseFloat(settings.post_slider_gap) || 0,
				};

				// mobile extra
				if (settings.post_slider_slides_per_view_mobile_extra !== undefined) {
					breakpointsSettings[breakpoints.mobile_extra.value] = {
						slidesPerView: parseFloat(settings.post_slider_slides_per_view_mobile_extra) || 1,
						slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_mobile_extra) || 1,
						spaceBetween: parseFloat(settings.post_slider_gap_mobile_extra) || parseFloat(settings.post_slider_gap) || 0,
					};
				}

				// tablet
				breakpointsSettings[breakpoints.tablet.value] = {
					slidesPerView: parseFloat(settings.post_slider_slides_per_view_tablet) || 1,
					slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_tablet) || 1,
					spaceBetween: parseFloat(settings.post_slider_gap_tablet) || parseFloat(settings.post_slider_gap) || 0,
				};

				// tablet extra
				if (settings.post_slider_slides_per_view_tablet_extra !== undefined) {
					breakpointsSettings[breakpoints.tablet_extra.value] = {
						slidesPerView: parseFloat(settings.post_slider_slides_per_view_tablet_extra) || 1,
						slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_tablet_extra) || 1,
						spaceBetween: parseFloat(settings.post_slider_gap_tablet_extra) || parseFloat(settings.post_slider_gap) || 0,
					};
				}
			
				// Laptop
				breakpointsSettings[breakpoints.laptop.value] = {
					slidesPerView: parseFloat(settings.post_slider_slides_per_view) || 1,
					slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll) || 1,
					spaceBetween: parseFloat(settings.post_slider_gap) || 0,
				};

				// widescreen
				if (settings.post_slider_slides_per_view_widescreen !== undefined) {
					breakpointsSettings[breakpoints.widescreen.value] = {
						slidesPerView: parseFloat(settings.post_slider_slides_per_view_widescreen) || 1,
						slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll_widescreen) || 1,
						spaceBetween: parseFloat(settings.post_slider_gap_widescreen) || parseFloat(settings.post_slider_gap) || 0,
					};
				}
				
				if (settings.post_slider_transition_effect === 'fade') {
					settings.breakpoints = {}; // no breakpoints needed in this case
				} else {
					settings.breakpoints = breakpointsSettings;
				}

				const layoutSettings = {
					allowTouchMove: settings.post_slider_allow_touch_move === 'yes',
					autoHeight: settings.post_slider_auto_h === 'yes',
					effect: settings.post_slider_transition_effect,
					direction: 'horizontal',
					loop: settings.post_slider_loop === 'yes',
					centerInsufficientSlides: false,
					parallax: settings.post_slider_parallax === 'yes',
					handleElementorBreakpoints : true,
					speed: settings.post_slider_speed,
					slidesPerView: parseFloat(settings.post_slider_slides_per_view),
					slidesPerGroup: parseInt(settings.post_slider_slides_to_scroll),
					spaceBetween: parseFloat(settings.post_slider_gap),
					breakpoints: settings.breakpoints,
					centeredSlides: settings.post_slider_centered_slides === 'yes',
					centeredSlidesBounds: settings.post_slider_slides_round_lenghts === 'yes',
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					pagination: {
						el: '.swiper-pagination',
						type: settings.post_slider_pagination_type,
						clickable: true,
					},
					autoplay: settings.autoplay,
					mousewheel: settings.post_slider_allow_mousewheel === 'yes',
					watchOverflow: true,
				};
				
				if (settings.post_slider_lazy_load === 'yes') {
					layoutSettings.preloadImages = false;
					layoutSettings.lazy = {
						loadPrevNext: true
					};
				}

				if ( 'undefined' === typeof Swiper ) {
					const asyncSwiper = elementorFrontend.utils.swiper;
 
				new asyncSwiper( wrapper, layoutSettings ).then( ( newSwiperInstance ) => {
					//console.log( 'New Swiper instance is ready: ', newSwiperInstance );
					Swiper = newSwiperInstance;
					this.syncPagination();
				} );
				} else {
					//console.log( 'Swiper global variable is ready, create a new instance: ', Swiper );
					Swiper = new Swiper( wrapper, layoutSettings );
					this.syncPagination();
				}
			},
			
			syncPagination: function () {
				const slider_A_element = document.querySelector('#sync1 .swiper-container');
				const slider_B_element = document.querySelector('#sync2 .swiper-container');

				if (!slider_A_element || !slider_B_element) {
					return;
				}

				const slider_A = slider_A_element.swiper;
				const slider_B = slider_B_element.swiper;

				//slider_A.controller.control = slider_B;
				//slider_B.controller.control = slider_A;

				let isSyncing = false;

				const syncSliders = (source, target) => {
					if (!isSyncing) {
						isSyncing = true;
						let newIndex = source.realIndex;

						// Handle looped sliders
						if (source.params.loop) {
							const totalSlides = source.slides.length - source.loopedSlides * 2;
							const adjustedIndex = source.activeIndex % totalSlides;
							newIndex = adjustedIndex + source.loopedSlides;
						}

						target.slideToLoop(newIndex); // Use slideToLoop for looped swipers
						isSyncing = false;
					}
				};

				slider_A.on('slideChange', function() {
					syncSliders(slider_A, slider_B);
				});
				
				slider_B.on('slideChange', function() {
					syncSliders(slider_B, slider_A);
				});
	
			},

        });

        elementorFrontend.elementsHandler.attachHandler('post-widget', PostWidgetHandler);
    });

})(jQuery);