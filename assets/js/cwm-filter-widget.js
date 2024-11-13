(function ($) {
	"use strict";
	$(window).on('elementor/frontend/init', function () {
		const FilterWidgetHandler = elementorModules.frontend.handlers.Base.extend({
			
			bindEvents() {
				this.getAjaxFilter();
			},
			
			getAjaxFilter() {
				let ajaxInProgress = false;
				
				if ($(".cwm-select2")[0]) {
					var parentElement = $(".cwm-select2");
					$(".cwm-select2 select").prop('multiple', false).select2({
						dropdownParent: parentElement,
					});
					
					$(".cwm-select2").css({
						"visibility": "visible",
						"opacity": "1",
						"transition": "opacity 0.3s ease-in-out"
					});
				}
				
				if ($(".cwm-multi-select2")[0]) {
					var parentElement = $(".cwm-multi-select2");

					$(".cwm-multi-select2 select").prop('multiple', true).select2({
						dropdownParent: parentElement,
					});

					$(".cwm-multi-select2 select").val(null).trigger('change');

					$(".cwm-multi-select2").css({
						"visibility": "visible",
						"opacity": "1",
						"transition": "opacity 0.3s ease-in-out"
					});

					function updatePlusSymbol() {
						var $rendered = $(".cwm-multi-select2 .select2-selection__rendered");

						$rendered.find(".select2-selection__e-plus-button").remove();

						if ($(".cwm-multi-select2 select").val().length === 0) {
							$rendered.prepend('<span class="select2-selection__choice select2-selection__e-plus-button">+</span>');
						}
					}
					
					updatePlusSymbol();
					$(".cwm-multi-select2 select").on('change', updatePlusSymbol);
				}
				
				const widgetContainer = this.$element.find('.elementor-widget-container');
				let pageID = window.elementorFrontendConfig.post.id;
				
				const filterSetting = this.$element.data('settings');
				
				//Fix for backend
				if (!filterSetting) {
					return;
				}
				
				const targetPostWidget = filterSetting.target_selector;
				
				if (!targetPostWidget.length) {
					return;
				}
				
				let groupLogic = filterSetting.group_logic,
					dynamicFiltering = filterSetting.dynamic_filtering,
					scrollToTop = filterSetting.scroll_to_top;
					
				//let postStatus = filterSetting.post_status;
				
				const targetSelector = $(targetPostWidget),
					  widgetID = targetSelector.data('id'),
					  originalState = targetSelector.html(),
					  loader = targetSelector.find('.loader'),
					  pagination = targetSelector.find('.pagination');
					  
				var maxPage = pagination.data('max-page');
				
				let paginationType = '';
				
				var paginationNext = '';
				var filterWidgetObservers = filterWidgetObservers || {};
				
				const postWidgetSetting = targetSelector.data('settings');
				if (postWidgetSetting && (postWidgetSetting.pagination || postWidgetSetting.pagination_type)) {
					paginationType = postWidgetSetting.pagination || postWidgetSetting.pagination_type;
					var size = postWidgetSetting.scroll_threshold && postWidgetSetting.scroll_threshold.size ? postWidgetSetting.scroll_threshold.size : 0;
					var unit = postWidgetSetting.scroll_threshold && postWidgetSetting.scroll_threshold.unit ? postWidgetSetting.scroll_threshold.unit : 'px';
					var infinite_threshold = size + unit;
				} else {
					var infinite_threshold = '0px';
				}
				
				const displayLoading = filterSetting && filterSetting.display_animation;
				
				let currentPage = 1;
				
				if (pageID === 0 || pageID === undefined) {
					pageID = $('header').next('div').data('elementor-id');
					if (pageID === 0 || pageID === undefined) {
						pageID = $('main div:first').data('elementor-id');
					}
				}
				
				function debounce(func, delay) {
					let timeoutId;
					return function() {
						const context = this;
						const args = arguments;
						clearTimeout(timeoutId);
						timeoutId = setTimeout(() => {
							func.apply(context, args);
						}, delay);
					};
				}
				
				const isSubmitPresent = widgetContainer.find('.submit-form').length > 0;
				
				widgetContainer.on('change keypress', 'form.form-tax', debounce(function(e) {
					if (!isSubmitPresent && (e.type !== 'keypress' || e.which == 13)) {
						resetURL();
						targetSelector.addClass('filter-initialized');
						targetSelector.removeClass('filter-active');
						get_form_values();
					}
				}, 300));
				
				widgetContainer.on('submit', 'form', function() {
					resetURL();
					targetSelector.addClass('filter-initialized');
					targetSelector.removeClass('filter-active');
					get_form_values();
					return false;
				});
				
				$(document).on('change', 'form.form-order-by', function() {
					targetSelector.addClass('filter-initialized');
					targetSelector.removeClass('filter-active');
					get_form_values();
				});
				
				$(document).on('submit', '.search-container form', function() {
					resetURL();
					targetSelector.addClass('filter-initialized');
					targetSelector.removeClass('filter-active');
					get_form_values();
					return false;
				});
				
				function getPageNumber(url) {
					var match;
					if (url.includes("?page=")) {
						match = url.match(/\?page=(\d+)/);
					} else if (url.includes("?paged=")) {
						match = url.match(/\?paged=(\d+)/);
					} else if (url.match(/\/(\d+)(\/|$)/)) {
						match = url.match(/\/(\d+)(\/|$)/);
					} else {
						match = url.match(/[?&](\w+)=\d+/);
					}
					if (!match) {
						match = url.match(/(\d+)(\/|$)/);
					}
					return match ? match[1] : null;
				}
				
				function resetURL() {
					let originalURL = window.location.origin + window.location.pathname;
					history.replaceState(null, '', originalURL);
				}
				
				$(document).on('click', targetPostWidget + ' .pagination-filter a', function(e) {
					e.preventDefault();
					var url = $(this).attr('href');
					var paged = getPageNumber(url);
					get_form_values(paged);
				});
				
				$(document).on('click', targetPostWidget + ' .load-more-filter', function(e) {
					e.preventDefault();
					var url = targetSelector.find('.e-load-more-anchor').data('next-page');
					if (url) {
						var paged = getPageNumber(url);
						paged = match ? match[1] : null;
						get_form_values(paged);
					} else {
						$(document).find(targetPostWidget + ' .pagination-filter a.next').click();
						currentPage = currentPage + 1;
						
						var loadMoreButton = targetSelector.find('.load-more');
							loadMoreButton.text('Loading...');
							loadMoreButton.prop('disabled', true);
					}
				});
				
				function post_count() {
					let postCount = targetSelector.find('.post-container').data('total-post');
					
					if (postCount === undefined) {
						postCount = 0;
					}
					
					postCount = Number(postCount);
					
					if (postCount > '1') {
						$('.filter-post-count').html(postCount + ' results found');
						} else {
						$('.filter-post-count').html(postCount + ' result found');
					}
				}
				
				function get_form_values(paged) {
					var postContainer = targetSelector.find('.post-container'),
					post_type = $('form.form-tax').data('post-type'),
					order = '',
					order_by = '',
					order_by_meta = '';
					
					var search_input = $('.search-container form').find('input[name="s"]'),
					query = search_input.val();
					
					$('.form-order-by select option:selected').each(function() {
						var self = $(this);
						order = self.data('order'),
						order_by_meta = self.data('meta'),
						order_by = self.val();
					});
					
					if (displayLoading === 'yes' && !targetSelector.hasClass('elementor-widget-post-widget')) {
						targetSelector.addClass('filter-load');
					}
					
					targetSelector.removeClass('e-load-more-pagination-end');
					
					postContainer.addClass('load');
					
					if (postContainer.hasClass('shortcode') || postContainer.hasClass('template')) {
						loader.fadeIn();
					}
					
					var category = [];
					var custom_field = [];
					var custom_field_like = [];
					var numeric_field = [];
					
					$('.cwm-taxonomy-wrapper input:checked, .cwm-custom-field-wrapper input:checked').each(function() {
						var self = $(this);
						var targetArray = self.closest('.cwm-taxonomy-wrapper').length ? category : custom_field;
						targetArray.push({
							'taxonomy': self.data('taxonomy'),
							'terms': self.val(),
							'logic': self.closest('div').data('logic')
						});
					});
					
					$('.cwm-custom-field-wrapper input.input-text').each(function() {
						var self = $(this);
						if ( self.val() ) {
							custom_field_like.push({
								'taxonomy': self.data('taxonomy'),
								'terms': self.val(),
								'logic': self.closest('div').data('logic')
							});
						}
					});
					
					$('.cwm-taxonomy-wrapper select option:selected, .cwm-custom-field-wrapper select option:selected').each(function() {
						var self = $(this);
						if ( self.val() ) {
							var targetArray = self.closest('.cwm-taxonomy-wrapper').length ? category : custom_field;
							targetArray.push({
								'taxonomy': self.data('taxonomy'),
								'terms': self.val(),
								'logic': self.closest('div').data('logic')
							});
						}
					});
					
					$('.cwm-numeric-wrapper input').each(function() {
						var self = $(this);
						var initial_val = self.data('base-value');
						
						if (self.val() === '' || self.val() != initial_val) {
							if (self.val() === '') {
								self.val(initial_val);
							}
							
							var _class = self.attr("class").split(' ')[0];
							
							$('.cwm-numeric-wrapper').find('input').each(function() {
								var _this = $(this);
								if (_this.hasClass(_class)) {
									numeric_field.push({
										'taxonomy': _this.data('taxonomy'),
										'terms': _this.val(),
										'logic': _this.closest('div').data('logic')
									});
								}
							});
						}
					});
					
					function reduceFields(fields) {
						return fields.reduce(function(o, cur) {
							var occurs = o.reduce(function(n, item, i) {
								return (item.taxonomy === cur.taxonomy) ? i : n;
							}, -1);
							
							if (occurs >= 0) {
								o[occurs].terms = o[occurs].terms.concat(cur.terms);
								} else {
								var obj = {
									taxonomy: cur.taxonomy,
									terms: [cur.terms],
									logic: cur.logic
								};
								o = o.concat([obj]);
							}
							return o;
						}, []);
					}
					
					var taxonomy_output = reduceFields(category);
					var custom_field_output = reduceFields(custom_field);
					var custom_field_like_output = reduceFields(custom_field_like);
					var numeric_output = reduceFields(numeric_field);
					
					$.ajax({
						type: "POST",
						url : ajax_var.url,
						async: true,
						data: {
							action: 'post_filter_results',
							widget_id: widgetID,
							page_id: pageID,
							group_logic: groupLogic,
							search_query: query,
							taxonomy_output: taxonomy_output,
							dynamic_filtering: dynamicFiltering,
							custom_field_output: custom_field_output,
							custom_field_like_output: custom_field_like_output,
							numeric_output: numeric_output,
							//post_status: postStatus,
							post_type: post_type,
							order: order,
							order_by: order_by,
							order_by_meta: order_by_meta,
							paged: paged,
							archive_type: $('[name="archive_type"]').val(),
							archive_post_type: $('[name="archive_post_type"]').val(),
							archive_taxonomy: $('[name="archive_taxonomy"]').val(),
							archive_id: $('[name="archive_id"]').val(),
							nonce: ajax_var.nonce,
						},
						success: function (data) {
							var response = JSON.parse(data);
							
							var content = response.html,
							base = window.location.href;
							
							if (data === '0') {
								targetSelector.off();
								
								targetSelector.html(originalState).fadeIn().removeClass('load');
								targetSelector.removeClass('filter-active').removeClass('filter-load');
								
								var currentSettings = targetSelector.data('settings');
								if (currentSettings.pagination_type === 'cwm_infinite') {
									currentSettings.pagination_type = 'load_more_infinite_scroll';
									targetSelector.data('settings', currentSettings);
								}
								if (currentSettings.pagination_load_type === 'cwm_ajax') {
									currentSettings.pagination_load_type = 'ajax';
									targetSelector.data('settings', currentSettings);
								}								
								post_count();
								
							} else {
								//Load More & Infinite Paginations
								if (paginationType == 'infinite' || paginationType == 'load_more' || paginationType == 'load_more_on_click' || paginationType == 'load_more_infinite_scroll' || paginationType == 'cwm_infinite') {
									if (targetSelector.hasClass('filter-active')) {
										var specificPart = targetSelector.find('.elementor-grid').children();
										targetSelector.html(content).fadeIn();
										targetSelector.find('.elementor-grid').prepend(specificPart).fadeIn().removeClass('load');
									} else {
										targetSelector.html(content).fadeIn().removeClass('load');
									}
								//Number & Next/Prev Paginations
								} else {
									targetSelector.html(content).fadeIn().removeClass('load');
								}
								loader.fadeOut();
								targetSelector.removeClass('filter-load');
								
								if (!$(content).text().trim()) {
									var no_post = $('.no-post-message[data-target-post-widget="' + targetPostWidget + '"]').text();
									if (no_post.length) {
										targetSelector.html('<div class="no-post">' + no_post + '</div>');
									}
								} else {
									var pagination = targetSelector.find('nav[aria-label="Pagination"]');
									pagination.addClass('pagination-filter');
									pagination.find('a.page-numbers').each(function () {
										var href = $(this).attr('href');
										var regex = /.*wp-admin\/admin-ajax\.php/;
										if (base.charAt(base.length - 1) === '/') {
											base = base.slice(0, -1);
										}
										var newHref = href.replace(regex, base);
										$(this).attr('href', newHref);
									});
									
									var scrollAnchor = targetSelector.find('.e-load-more-anchor'),
										next_page = scrollAnchor.data('next-page');
									
									var loadMoreButton = targetSelector.find('.load-more'),
										elementorLoadMoreButton = targetSelector.find('.elementor-button-link.elementor-button');
									
										loadMoreButton.addClass('load-more-filter');
										elementorLoadMoreButton.addClass('load-more-filter');
										targetSelector.addClass('filter-active');
									
									if (next_page !== undefined) {
										var regex = /.*wp-admin\/admin-ajax\.php/;
										if (base.charAt(base.length - 1) === '/') {
											base = base.slice(0, -1);
										}
										var newHref = next_page.replace(regex, base);
										scrollAnchor.attr('data-next-page', newHref);
									}
									
									var currentSettings = targetSelector.data('settings');
									if (currentSettings.pagination_type === 'load_more_infinite_scroll') {
										currentSettings.pagination_type = 'cwm_infinite';
										targetSelector.data('settings', currentSettings);
									}
									if (currentSettings.pagination_load_type === 'ajax') {
										currentSettings.pagination_load_type = 'cwm_ajax';
										targetSelector.data('settings', currentSettings);
									}									
									post_count();
									
								}
								
							}
						},
						complete: function () {
							var loadMoreButton = targetSelector.find('.load-more-filter'),
								maxPage,
								paginationType = targetSelector.data('settings').pagination || targetSelector.data('settings').pagination_type;
							
							// Check for maxPage in the usual place or the Elementor Pro widget
							var scrollAnchor = $('.e-load-more-anchor');
							if (scrollAnchor.length) {
								var currentPage = scrollAnchor.data('page');
									maxPage = scrollAnchor.data('max-page') - 1;
							} else {
								var currentPage = targetSelector.find('.pagination').data('page'),
									maxPage = targetSelector.find('.pagination').data('max-page') - 1;
							}
							
							if (scrollToTop == 'yes') {
								window.scrollTo({
									top: targetSelector.offset().top - 150,
									behavior: 'smooth'
								});
							}

							if (currentPage > maxPage) {
								loadMoreButton.hide();
							}
							
							ajaxInProgress = false;
							
							if (targetSelector.hasClass('filter-active') && paginationType == 'infinite') {
								debounce(function(e) {
									bpf_infinite_scroll(widgetID, targetSelector);
								}, 800)();	
							}
							
							if (targetSelector.hasClass('filter-active') && paginationType == 'cwm_infinite') {
								debounce(function(e) {
									elementor_infinite_scroll(widgetID, targetSelector);
								}, 800)();
							}
							
							elementorFrontend.elementsHandler.runReadyTrigger($(targetPostWidget));
							if (elementorFrontend.config.experimentalFeatures.e_lazyload) {
								document.dispatchEvent(new Event('elementor/lazyload/observe'));
							}
							
							targetSelector.removeClass('filter-initialized');
						},
						error: function(xhr, status, error) {
							console.log('AJAX error: ', error);
						}
					});
				}
				
				function bpf_infinite_scroll(widgetID, targetSelector) {
					var scrollAnchor = targetSelector.find('.e-load-more-anchor'),
						paginationNext = $(document).find(targetPostWidget + ' .pagination-filter a.next');
						
					if (!paginationNext.length) {
						if (filterWidgetObservers[widgetID]) {
							filterWidgetObservers[widgetID].disconnect();
							filterWidgetObservers[widgetID] = null;
						}
						return;
					}

					if (paginationNext.length && scrollAnchor.length) {
						if (!filterWidgetObservers[widgetID]) {
							filterWidgetObservers[widgetID] = new IntersectionObserver(function(entries) {
								entries.forEach(function(entry) {
									if (entry.isIntersecting) {
										var paginationNext = $(document).find(targetPostWidget + ' .pagination-filter a.next');
										
										if (!ajaxInProgress && paginationNext.length && targetSelector.hasClass('filter-active')) {
											ajaxInProgress = true;
											paginationNext.click();
										}
									}
								});
								
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							});
						}
						
						filterWidgetObservers[widgetID].observe(scrollAnchor.get(0));	
						//$(window).on('resize', function() {
						//	if (scrollAnchor.length && !ajaxInProgress && currentPage <= maxPage) {
						//		filterWidgetObservers[widgetID].observe(scrollAnchor.get(0));
						//	}
						//});
					}
				}
				
				function elementor_infinite_scroll(widgetID, targetSelector) {
					var scrollAnchor = targetSelector.find('.e-load-more-anchor'),
						currentPage = scrollAnchor.data('page'),
						maxPage = scrollAnchor.data('max-page');

					if (currentPage === maxPage) {
						if (filterWidgetObservers[widgetID]) {
							filterWidgetObservers[widgetID].disconnect();
							filterWidgetObservers[widgetID] = null;
						}
						return;
					}
	
					if (scrollAnchor.length && currentPage < maxPage) {
						if (!filterWidgetObservers[widgetID]) {
							filterWidgetObservers[widgetID] = new IntersectionObserver(function(entries) {
								entries.forEach(function(entry) {
									if (entry.isIntersecting) {
										if (!ajaxInProgress && targetSelector.hasClass('filter-active')) {
											ajaxInProgress = true;
											currentPage++;
											get_form_values(currentPage);
										}
									}
								});
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							});
						}

						filterWidgetObservers[widgetID].observe(scrollAnchor.get(0));
						
						//$(window).on('resize', function() {
						//	if (scrollAnchor.length && !ajaxInProgress && currentPage <= maxPage) {
						//		filterWidgetObservers[widgetID].observe(scrollAnchor.get(0));
						//	}
						//});
					}
				}
				
				//Add more/less
				widgetContainer.on('click', 'li.more', function () {
					var taxonomyFilter = $(this).closest('ul.taxonomy-filter');
					
					if (taxonomyFilter.hasClass('yes')) {
						taxonomyFilter.removeClass('yes');
						} else {
						taxonomyFilter.addClass('yes');
					}
					
					$(this).text(function(i, text){
						return text === "More..." ? "Less..." : "More...";
					});
				});
				
				//Child term toggle
				$('.cwm-filter-item').click(function() {
					var $lowTermsGroup = $(this).closest('li').next('.low-terms-group');

					if ($(this).is(':checked')) {
						$lowTermsGroup.show();
					} else {
						$lowTermsGroup.hide();
					}
				});
				
				// Disable keyboard enter key for input fields
				$('form.form-tax input').on('keypress', function(e) {
					if (e.which === 13) {
						e.preventDefault();
					}
				});
				
				//Add reset
				widgetContainer.on( 'click', '.reset-form', function() {
					get_form_values();
				});
				
				post_count();
			},
			
		});
		
		elementorFrontend.elementsHandler.attachHandler('filter-widget', FilterWidgetHandler);
	});
})(jQuery);