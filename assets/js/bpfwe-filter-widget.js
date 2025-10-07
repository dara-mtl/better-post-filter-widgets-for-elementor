(function ($) {
	"use strict";
	$(window).on('elementor/frontend/init', function() {
		var originalStates = {};
		var postsPerPageCache = {};
		const performanceSettingsCache = {};
		const filterSettingsCache = {};

		let dynamic_handler = '';
		if ($('.elementor-widget-filter-widget').length) {
			dynamic_handler = 'filter-widget';
		} else if ($('.elementor-widget-search-bar-widget').length) {
			dynamic_handler = 'search-bar-widget';
		} else {
			dynamic_handler = 'sorting-widget';
		}

		// Link filter/search/sort widgets to their target post widgets via data-filters-list.
		function linkFilterWidgets() {
			$('.elementor-widget-filter-widget, .elementor-widget-search-bar-widget, .elementor-widget-sorting-widget').each(function() {
				var $widget = $(this);
				var widgetId = $widget.data('id');
				var settings = $widget.data('settings');
				var targetSelector = settings?.target_selector;

				if (targetSelector && $(targetSelector).length) {
					var $target = $(targetSelector);
					var filtersList = $target.data('filters-list') ? $target.data('filters-list').split(',') : [];

					if (!filtersList.includes(widgetId)) {
						filtersList.push(widgetId);
						$target.data('filters-list', filtersList.join(','));
						$target.attr('data-filters-list', filtersList.join(','));
					}

					var widgetID = $target.data('id');

					if (!originalStates[ widgetID ]) {
						originalStates[ widgetID ] = $target.html();
					}

					if (!postsPerPageCache[ widgetID ]) {
						const postWidgetSetting = $target.data('settings');
						let postsPerPage = postWidgetSetting?.posts_per_page ? parseInt(postWidgetSetting.posts_per_page) : null;
						if (!postsPerPage) {
							let postWrapper = $target.find('.elementor-posts, .grid, .columns, .elementor-grid').first();
							if (postWrapper.length) postsPerPage = postWrapper.children('article, .post, .item, .entry').length;
						}
						if (!postsPerPage) {
							let postWrapper = $target.find('.swiper-wrapper').first();
							if (postWrapper.length) postsPerPage = postWrapper.children('.swiper-slide').length;
						}
						if (!postsPerPage) {
							let postWrapper = $target.find('ul.products').first();
							if (postWrapper.length) postsPerPage = postWrapper.children('li').length;
						}
						if (!postsPerPage) {
							let postWrapper = $target.find('ul').first();
							if (postWrapper.length) postsPerPage = postWrapper.children('li').length;
						}
						if (!postsPerPage) {
							let postWrapper = $target.find('div').first();
							if (postWrapper.length) postsPerPage = postWrapper.children('div').length;
						}
						postsPerPageCache[ widgetID ] = postsPerPage > 0 ? postsPerPage : 50;
					}

					// Handle filter widget's settings.
					if ($widget.hasClass('elementor-widget-filter-widget')) {

						// General settings.
						if (!filterSettingsCache[ widgetID ]) {
							filterSettingsCache[ widgetID ] = [];
						}

						const filterSettings = {
							widgetId: widgetId,
							groupLogic: settings?.group_logic ?? '',
							dynamicFiltering: settings?.dynamic_filtering ?? '',
							scrollToTop: settings?.scroll_to_top ?? '',
							displaySelectedBefore: settings?.display_selected_before ?? '',
							nothingFoundMessage: settings?.nothing_found_message ?? 'It seems we can’t find what you’re looking for.',
							enableQueryDebug: settings?.enable_query_debug ?? '',
							queryID: settings?.filter_query_id ?? ''
						};

						filterSettingsCache[widgetID] = filterSettings;

						$target.data('filter-settings', filterSettings);
						$target.attr('data-filter-settings', JSON.stringify(filterSettings));

						// Performance settings.
						if (!performanceSettingsCache[ widgetID ]) {
							performanceSettingsCache[ widgetID ] = [];
						}

						const performanceSettings = {
							widgetId: widgetId,
							optimize_query: settings?.optimize_query === 'yes',
							no_found_rows: settings?.no_found_rows === 'yes',
							suppress_filters: settings?.suppress_filters === 'yes',
							posts_per_page: parseInt(settings?.posts_per_page) || -1
						};

						performanceSettingsCache[ widgetID ].push(performanceSettings);

						const mergedSettings = performanceSettingsCache[ widgetID ].reduce((merged, current) => {
							return {
								optimize_query: merged.optimize_query || current.optimize_query,
								no_found_rows: merged.no_found_rows || current.no_found_rows,
								suppress_filters: merged.suppress_filters || current.suppress_filters,
								posts_per_page: Math.min(merged.posts_per_page === -1 ? Infinity : merged.posts_per_page, current.posts_per_page === -1 ? Infinity : current.posts_per_page)
							};
						}, {
							optimize_query: false,
							no_found_rows: false,
							suppress_filters: false,
							posts_per_page: -1
						});

						mergedSettings.posts_per_page = mergedSettings.posts_per_page === Infinity ? -1 : mergedSettings.posts_per_page;

						$target.data('performance-settings', mergedSettings);
						$target.attr('data-performance-settings', JSON.stringify(mergedSettings));
					}
				}
			});
		}

		// Add the filter attributes to the targeted post widget on page load.
		linkFilterWidgets();

		// Handle Filter toggle.
		$(document).on('click', '.filter-title.collapsible', function() {
			const $title = $(this);
			let $content = $title.next('.bpfwe-taxonomy-wrapper, .bpfwe-custom-field-wrapper, .bpfwe-numeric-wrapper');

			if (!$content.length) {
				$content = $title.siblings('.bpfwe-taxonomy-wrapper, .bpfwe-custom-field-wrapper, .bpfwe-numeric-wrapper');
			}

			if (!$content.is(':visible')) {
				$content.css('display', 'flex').hide();
			}

			$title.toggleClass('collapsed');
			$content.stop(true, true).slideToggle(300, function() {
				if ($content.is(':visible')) {
					$content.css('display', 'flex');
				}
			});
		});

		const FilterWidgetHandler = elementorModules.frontend.handlers.Base.extend({
			bindEvents() {
				this.getAjaxFilter();
			},

			getAjaxFilter() {
				let ajaxInProgress = false;
				const filterWidget = this.$element.find('.filter-container');

				// Initialize single-select dropdowns with Select2.
				filterWidget.find('.bpfwe-select2 select').each(function (index) {
					const $select = $(this);
					const parentElement = $select.closest('.bpfwe-select2');
					const uniqueId = 'bpfwe-select2-' + index;
					$select.attr('id', uniqueId).prop('multiple', false).select2({
						dropdownParent: parentElement,
					});
					parentElement.css({
						"visibility": "visible",
						"opacity": "1",
						"transition": "opacity 0.3s ease-in-out"
					});
				});

				// When "Select All" span is clicked (scoped inside the widget)
				filterWidget.find('.bpfwe-select-all').on('click', function() {
					const $selectAll = $(this);
					const taxonomy = $selectAll.data('taxonomy');
					const isChecked = !$selectAll.hasClass('checked');

					$selectAll.toggleClass('checked', isChecked);

					// Limit scope to current filterWidget only.
					const $relatedCheckboxes = filterWidget
						.find('input.bpfwe-filter-item')
						.filter('[data-taxonomy="' + taxonomy + '"]');

					$relatedCheckboxes.prop('checked', isChecked).trigger('change');
				});

				filterWidget.find('input.bpfwe-filter-item').on('change', function() {
					const $changed = $(this);
					const taxonomy = $changed.data('taxonomy');

					const $groupCheckboxes = filterWidget
						.find('input.bpfwe-filter-item')
						.filter('[data-taxonomy="' + taxonomy + '"]');

					const $selectAll = filterWidget
						.find('.bpfwe-select-all[data-taxonomy="' + taxonomy + '"]');

					const allChecked = $groupCheckboxes.length === $groupCheckboxes.filter(':checked').length;

					$selectAll.toggleClass('checked', allChecked);
				});

				// Initialize multi-select dropdowns with Select2 and plus symbol logic.
				filterWidget.find('.bpfwe-multi-select2 select').each((index, el) => {
					const $select = $(el);
					const parentElement = $select.closest('.bpfwe-multi-select2');
					const uniqueId = `bpfwe-multi-select2-${index}`;
					$select.attr('id', uniqueId).prop('multiple', true).select2({
						dropdownParent: parentElement,
					});
					$select.val(null).trigger('change');
					parentElement.css({ visibility: 'visible', opacity: '1', transition: 'opacity 0.3s ease-in-out' });

					const updatePlusSymbol = () => {
						const $rendered = parentElement.find('.select2-selection__rendered');
						$rendered.find('.select2-selection__e-plus-button').remove();
						if ($select.val().length === 0) {
							$rendered.prepend('<span class="select2-selection__choice select2-selection__e-plus-button">+</span>');
						}
					};
					updatePlusSymbol();
					$select.on('change', updatePlusSymbol);
				});

				// Toggle visibility of taxonomy filter items.
				filterWidget.on('click', 'li.more', function() {
					$(this).closest('ul.taxonomy-filter').toggleClass('show-toggle');
				});

				// Toggle low-level terms group visibility with +/- indicator.
				filterWidget.on('click', '.low-group-trigger', function() {
					var $trigger     = $(this);
					var $parentLi    = $trigger.closest('li');
					var $lowTermsGrp = $parentLi.children('.low-terms-group, .child-terms');

					$lowTermsGrp.toggle();
					var isExpanded = $lowTermsGrp.is(':visible');

					$trigger.text(isExpanded ? '-' : '+');
					$trigger.attr('aria-expanded', isExpanded);
				});

				// Allow radio button deselection (with labels)
				$(document).off('mousedown', 'input[type="radio"]').on('mousedown', 'input[type="radio"]', function (e) {
					$(this).data('wasChecked', $(this).prop('checked'));
				});

				$(document).off('click', 'input[type="radio"]').on('click', 'input[type="radio"]', function (e) {
					var $radio = $(this);
					if ($radio.data('wasChecked')) {
						// user clicked an already-checked radio - deselect it
						$radio.prop('checked', false).trigger('change');
					}
					$radio.removeData('wasChecked');
				});

				// Label handlers - handle visual-range labels, but ignore clicks that actually targeted the input itself.
				$(document).off('mousedown', 'label.bpfwe-visual-range-option').on('mousedown', 'label.bpfwe-visual-range-option', function (e) {
					if ($(e.target).is('input[type="radio"]') || $(e.target).closest('input[type="radio"]').length) {
						return;
					}

					var $label = $(this);
					var radioId = $label.attr('for');
					var $radio = $();

					if (radioId) {
						var el = document.getElementById(radioId);
						if (el) { $radio = $(el); }
					} else {
						$radio = $label.find('input[type="radio"]').first();
					}

					if (!$radio.length) { return; }
					$radio.data('wasChecked', $radio.prop('checked'));
				});

				$(document).off('click', 'label.bpfwe-visual-range-option').on('click', 'label.bpfwe-visual-range-option', function (e) {
					if ($(e.target).is('input[type="radio"]') || $(e.target).closest('input[type="radio"]').length) {
						return;
					}

					var $label = $(this);
					var radioId = $label.attr('for');
					var $radio = $();

					if (radioId) {
						var el = document.getElementById(radioId);
						if (el) { $radio = $(el); }
					} else {
						$radio = $label.find('input[type="radio"]').first();
					}

					if (!$radio.length) { return; }

					if ($radio.data('wasChecked')) {
						e.preventDefault();
						$radio.prop('checked', false).trigger('change');
					} else {
						e.preventDefault();
						if (!$radio.prop('checked')) {
							$radio.prop('checked', true).trigger('change');
						}
					}

					$radio.removeData('wasChecked');
				});

				$(document).on('keydown', 'form.form-tax input', function (e) {
					if (e.which === 13) {
						e.preventDefault();
					}
				});

				$(document).on('submit', 'form.form-tax', function (e) {
					e.preventDefault();
				});

				const currentUrl = window.location.href;
				const filterSetting = this.$element.data('settings');

				if (!filterSetting) return;

				let targetPostWidget = filterSetting?.target_selector ?? '';
				if (!targetPostWidget || !$(targetPostWidget).length) {
					let $closestWidget = $('.elementor-widget-loop-carousel, .elementor-widget-loop-grid, .elementor-widget-post-widget, .elementor-widget-posts').first();
					if ($closestWidget.length) {
						let widgetClass = $closestWidget.attr('class')?.split(' ').find(cls => cls.startsWith('elementor-widget-')) || '';
						targetPostWidget = $closestWidget.attr('id') ? `#${$closestWidget.attr('id')}` : widgetClass ? `.${widgetClass}` : '';
					}
				}
				if (!targetPostWidget || targetPostWidget === '.') return;

				let currentPage = 1,
					paginationType = '';

				let targetSelector = $(targetPostWidget),
					widgetID = targetSelector.data('id'),
					loader = targetSelector.find('.loader'),
					pagination = targetSelector.find('.pagination');

				var maxPage = pagination.data('max-page'),
					filterWidgetObservers = {};

				const postWidgetSetting = targetSelector.data('settings');

				if (postWidgetSetting && (postWidgetSetting.pagination || postWidgetSetting.pagination_type)) {
					paginationType = postWidgetSetting.pagination || postWidgetSetting.pagination_type;
					var size = postWidgetSetting.scroll_threshold?.size || 0;
					var unit = postWidgetSetting.scroll_threshold?.unit || 'px';
					var infinite_threshold = size + unit;
				} else {
					var infinite_threshold = '0px';
				}

				let pageID = window.elementorFrontendConfig.post.id;

				if (!pageID) {
					if (!widgetID) return;
					var $outermost = $('[data-id="' + widgetID + '"]').parents('[data-elementor-id]').last();
					if ($outermost.length) pageID = $outermost.data('elementor-id');
				}

				// ===== Debounce the interactions =====
				function debounce (func, delay) {
					let timeoutId;
					return function() {
						const context = this,
							args = arguments;
						clearTimeout(timeoutId);
						timeoutId = setTimeout(() => func.apply(context, args), delay);
					};
				}

				let isInteracting = false,
					interactionTimeout;

				filterWidget.on('mousedown keydown touchstart', 'form.form-tax', function() {
					isInteracting = true;
					clearTimeout(interactionTimeout);
				});

				filterWidget.on('mouseup keyup touchend', 'form.form-tax', function() {
					interactionTimeout = setTimeout(() => isInteracting = false, 700);
				});

				// ===== Filter widgets: inputs =====
				$(document).off('change keydown input', 'form.form-tax, .bpfwe-numeric-wrapper input').on(
					'change keydown input',
					'form.form-tax, .bpfwe-numeric-wrapper input',
					debounce(function (e) {
						var $widget = $(this).closest('.elementor-widget-filter-widget');
						var widgetInteractionID = $widget.data('id');
						if (!widgetInteractionID) return;

						const isSubmitPresent = $widget.find('.submit-form').length > 0;

						if (!isSubmitPresent) {
							if (e.type === 'change' || (e.type === 'keydown' && e.key === 'Enter')) {
								get_form_values(widgetInteractionID);
								return;
							}
							if (!isInteracting) {
								get_form_values(widgetInteractionID);
							}
						}
					}, 700)
				);

				// ===== Filter widgets: submit button =====
				$(document).off('click', '.submit-form').on('click', '.submit-form', function() {
					var $widget = $(this).closest('.elementor-widget-filter-widget');
					var widgetInteractionID = $widget.data('id');
					if (!widgetInteractionID) return;
					get_form_values(widgetInteractionID);
					return false;
				});

				// ===== Sorting widgets =====
				$(document).off('change', 'form.form-order-by').on('change', 'form.form-order-by', function() {
					var $widget = $(this).closest('.elementor-widget-sorting-widget');
					var widgetInteractionID = $widget.data('id');
					if (!widgetInteractionID) return;
					get_form_values(widgetInteractionID);
				});

				// ===== Search bar widgets =====
				$(document).off('submit', 'form.search-post').on('submit', 'form.search-post', function() {
					var $widget = $(this).closest('.elementor-widget-search-bar-widget');
					var widgetInteractionID = $widget.data('id');
					if (!widgetInteractionID) return;
					get_form_values(widgetInteractionID);
					if ($(this).hasClass('no-redirect')) {
						return false;
					}
				});

				if (currentUrl.includes('?search=')) get_form_values();

				// ===== Utility Functions =====
				function getPageNumber(url) {
					const parsedUrl = new URL(url, window.location.origin);

					const pageNum = parsedUrl.searchParams.get('page_num');
					if (pageNum && !isNaN(pageNum)) {
						return parseInt(pageNum, 10);
					}

					const paged = parsedUrl.searchParams.get('paged');
					if (paged && !isNaN(paged)) {
						return parseInt(paged, 10);
					}

					const page = parsedUrl.searchParams.get('page');
					if (page && !isNaN(page)) {
						return parseInt(page, 10);
					}

					const match = url.match(/\/page\/(\d+)(\/|$)/);
					if (match && match[1]) {
						return parseInt(match[1], 10);
					}

					return 1;
				}

				// ===== Pagination: numbers and load more =====
				$(document).off('click', '.pagination-filter a').on('click', '.pagination-filter a', function (e) {
					var postWidgetID = $(this).closest('[data-id]').data('id');
					e.preventDefault();
					var url = $(this).attr('href');
					var paged = getPageNumber(url);
					get_form_values(null, paged, postWidgetID);
				});

				$(document).off('click', '.load-more-filter').on('click', '.load-more-filter', function (e) {
					e.preventDefault();

					var $widget = $(this).closest('[data-id]');
					var postWidgetID = $widget.data('id');
					var url = $widget.find('.e-load-more-anchor').data('next-page');

					if (url) {
						var paged = getPageNumber(url);
						get_form_values(null, paged, postWidgetID);
					} else {
						var nextPageLink = $widget.find('.pagination-filter a.next');

						if (nextPageLink.length) {
							nextPageLink.trigger('click');
							currentPage++;
							$widget.data('current-page', currentPage);
							var loadMoreButton = $widget.find('.load-more');
							loadMoreButton.text('Loading...').prop('disabled', true);
						}
					}
				});

				function post_count ($target) {
					let postCount = $target.find('.post-container').data('total-post') || 0;
					postCount = Number(postCount);
					$('.filter-post-count .number').text(postCount);
				}

				// ===== Retrieve form values, process filters, and make AJAX request for filtered posts =====
				function get_form_values(widgetInteractionID, paged, postWidgetID) {
					if ($(document).find('div[data-filters-list*="' + widgetInteractionID + '"]').length === 0) {
						linkFilterWidgets();
					}
					
					let localWidgetID = widgetInteractionID ? $('div[data-filters-list*="' + widgetInteractionID + '"]').data('id') : widgetID;
					if (!localWidgetID) return;

					// Check if the function was trigerred by a filter or a post widget.
					if (postWidgetID) {
						localWidgetID = postWidgetID;
					}

					let localTargetSelector = $('div[data-id="' + localWidgetID + '"]');
					if (!localTargetSelector.length) return;

					let originalState = originalStates[ localWidgetID ];
					let postsPerPage = postsPerPageCache[ localWidgetID ];

					let filtersList = localTargetSelector.data('filters-list') ? localTargetSelector.data('filters-list').split(',') : [];
					let postContainer = localTargetSelector.find('.post-container'),
						order = '',
						order_by = '',
						order_by_meta = '',
						searchQuery = '',
						post_type = '',
						dateQuery = '';

					// Disconnect observer to halt infinite scroll pagination during updates.
					let paginationType = localTargetSelector.data('settings')?.pagination || localTargetSelector.data('settings')?.pagination_type || '';

					if ((paginationType === 'cwm_infinite' || paginationType === 'infinite') && filterWidgetObservers[ localWidgetID ]) {
						filterWidgetObservers[ localWidgetID ].disconnect();
						filterWidgetObservers[ localWidgetID ] = null;
					}

					let isSorting = $('.elementor-widget-sorting-widget[data-id="' + widgetInteractionID + '"]').length > 0;
					let isSearch = $('.elementor-widget-search-bar-widget[data-id="' + widgetInteractionID + '"]').length > 0;
					let isFiltering = $('.elementor-widget-filter-widget[data-id="' + widgetInteractionID + '"]').length > 0;

					// Remove 'filter-active' only if sorting, searching, or filtering occurs (ensuring new stack instead of stacking posts).
					if (isSorting || isSearch || isFiltering) {
						localTargetSelector.removeClass('filter-active');
						localTargetSelector.data('current-page', 1);
					}

					// Get the current settings
					const filterSettings = filterSettingsCache[ localWidgetID ];
					let nothingFoundMessage = filterSettings?.nothingFoundMessage;
					let scrollToTop = filterSettings?.scrollToTop;
					let displaySelectedBefore = filterSettings?.displaySelectedBefore;

					let hasValues = false;
					
					// Nuke duplicated content in sticky column
					$('.elementor-sticky__spacer').empty();

					filtersList.forEach(function (filterWidgetId) {
						// Lowest priority: Sorting widget.
						if (!post_type) {
							var $sortingWidget = $('.elementor-widget-sorting-widget[data-id="' + filterWidgetId + '"]');
							if ($sortingWidget.length) {
								post_type = $sortingWidget.data('settings')?.filter_post_type || '';
							}
						}

						// Medium priority: Search widget.
						if (!post_type) {
							var $searchWidget = $('.elementor-widget-search-bar-widget[data-id="' + filterWidgetId + '"]');
							if ($searchWidget.length) {
								post_type = $searchWidget.data('settings')?.filter_post_type || '';
							}
						}

						// Highest priority: Filter widget.
						var $filterWidget = $('.elementor-widget-filter-widget[data-id="' + filterWidgetId + '"]');
						if ($filterWidget.length) {
							post_type = $filterWidget.data('settings')?.filter_post_type || '';
						}
					});

					if (post_type === 'targeted_widget') {
						let resolvedPostType = '';

						// Try Elementor Pro loop grid (look for `class*="type-"`).
						const $loopItem = localTargetSelector.find('[class*="type-"]').first();
						if ($loopItem.length) {
							const typeClass = $loopItem.attr('class').split(/\s+/).find(c => c.indexOf('type-') === 0);
							if (typeClass) {
								resolvedPostType = typeClass.replace('type-', '');
							}
						}

						// Try BPFWE post widget (read from data-settings).
						if (!resolvedPostType) {
							const settings = localTargetSelector.data('settings') || {};
							if (settings.post_type && Array.isArray(settings.post_type) && settings.post_type.length > 0) {
								resolvedPostType = settings.post_type[0];
							}
						}

						post_type = resolvedPostType || 'any';
					}

					var category = [],
						custom_field = [],
						custom_field_like = [],
						numeric_field = [];

					filtersList.forEach(function (filterWidgetId) {
						var $searchWidget = $('.elementor-widget-search-bar-widget[data-id="' + filterWidgetId + '"]');
						if ($searchWidget.length) {
							searchQuery = $searchWidget.find('form.search-post input').val() || '';
							if (searchQuery) hasValues = true;
						}

						var $sortingWidget = $('.elementor-widget-sorting-widget[data-id="' + filterWidgetId + '"]');
						if ($sortingWidget.length) {
							var $select = $sortingWidget.find('.form-order-by select'),
								$defaultOption = $select.find('option:first-child'),
								$selectedOption = $select.find('option:selected'),
								defaultVal = $select.find('option:first-child').val();

							$selectedOption.each(function() {
								var self = $(this);
								order = self.data('order');
								order_by_meta = self.data('meta');
								order_by = self.val();

								if (self.val() !== defaultVal || order_by !== '') {
									hasValues = true;
								}
							});
						}

						var $filterWidget = $('.elementor-widget-filter-widget[data-id="' + filterWidgetId + '"]');
						if ($filterWidget.length) {
							$filterWidget.find('.bpfwe-taxonomy-wrapper input:checked, .bpfwe-custom-field-wrapper input:checked').each(function() {
								var self = $(this);
								var targetArray = self.closest('.bpfwe-taxonomy-wrapper').length ? category : custom_field;
								targetArray.push({
									taxonomy: self.data('taxonomy'),
									terms: self.val(),
									logic: self.closest('div').data('logic')
								});
								hasValues = true;
							});

							$filterWidget.find('.bpfwe-custom-field-wrapper input.input-text').each(function() {
								var self = $(this);
								if (self.val()) {
									custom_field_like.push({
										taxonomy: self.data('taxonomy'),
										terms: self.val(),
										logic: self.closest('div').data('logic')
									});
									hasValues = true;
								}
							});

							$filterWidget.find('.bpfwe-taxonomy-wrapper select option:selected, .bpfwe-custom-field-wrapper select option:selected').each(function() {
								var self = $(this);
								if (self.val()) {
									var targetArray = self.closest('.bpfwe-taxonomy-wrapper').length ? category : custom_field;
									targetArray.push({
										taxonomy: self.data('taxonomy'),
										terms: self.val(),
										logic: self.closest('div').data('logic')
									});
									hasValues = true;
								}
							});

							$filterWidget.find('.bpfwe-numeric-wrapper input').each(function() {
								var self = $(this);
								var initial_val = self.data('base-value');
								if (self.val() === '' || self.val() != initial_val) {
									if (self.val() === '') self.val(initial_val);
									var _class = self.attr("class").split(' ')[ 0 ];
									$filterWidget.find('.bpfwe-numeric-wrapper input').each(function() {
										var _this = $(this);
										if (_this.hasClass(_class)) {
											numeric_field.push({
												taxonomy: _this.data('taxonomy'),
												terms: _this.val(),
												logic: _this.closest('div').data('logic')
											});
											hasValues = true;
										}
									});
								}
							});

							$filterWidget.find('.bpfwe-visual-range-wrapper input[type="radio"]:checked').each(function() {
								var self = $(this);
								var taxonomy = self.closest('.bpfwe-visual-range-wrapper').data('taxonomy');
								var logic = self.closest('.bpfwe-visual-range-wrapper').data('logic');
								var minVal = parseFloat(self.data('min'));
								var maxVal = parseFloat(self.data('max'));

								if (!isNaN(minVal) && !isNaN(maxVal)) {
									numeric_field.push({
										taxonomy: taxonomy,
										terms: minVal,
										logic: logic
									});
									numeric_field.push({
										taxonomy: taxonomy,
										terms: maxVal,
										logic: logic
									});

									hasValues = true;
								}
							});

							dateQuery = $filterWidget.find('.bpfwe-filter-item[data-taxonomy="post_date"]').map(function() { return this.value; }).get().join(',');
						}
					});

					var urlParams = new URLSearchParams(window.location.search);
					if (urlParams.has('search')) {
						searchQuery = urlParams.get('search');
						if (searchQuery) hasValues = true;
					}

					localTargetSelector.removeClass('e-load-more-pagination-end');
					postContainer.addClass('load');
					localTargetSelector.addClass('load filter-initialized');

					if (postContainer.hasClass('shortcode') || postContainer.hasClass('template')) {
						localTargetSelector.find('.loader').fadeIn();
					}

					function reduceFields(fields) {
						return fields.reduce((o, cur) => {
							const occurs = o.reduce((n, item, i) => (item.taxonomy === cur.taxonomy ? i : n), -1);
							if (occurs >= 0) {
								o[occurs].terms = o[occurs].terms.concat(cur.terms);
							} else {
								o.push({ taxonomy: cur.taxonomy, terms: [cur.terms], logic: cur.logic });
							}
							return o;
						}, []);
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

						$(document).trigger('elementor/lazyload/observe');
					}
					
					updateSelectedTermsDisplay(widgetInteractionID, displaySelectedBefore);

					var taxonomy_output = reduceFields(category),
						custom_field_output = reduceFields(custom_field),
						custom_field_like_output = reduceFields(custom_field_like),
						numeric_output = reduceFields(numeric_field);

					$.ajax({
						type: 'POST',
						url: ajax_var.url,
						async: true,
						data: {
							action: 'post_filter_results',
							widget_id: localWidgetID,
							page_id: pageID,
							group_logic: filterSettings?.groupLogic || '',
							search_query: searchQuery,
							date_query: dateQuery,
							taxonomy_output: taxonomy_output,
							dynamic_filtering: filterSettings?.dynamicFiltering,
							custom_field_output: custom_field_output,
							custom_field_like_output: custom_field_like_output,
							numeric_output: numeric_output,
							post_type: post_type,
							posts_per_page: postsPerPage,
							order: order,
							order_by: order_by,
							order_by_meta: order_by_meta,
							paged: paged,
							archive_type: $('[name="archive_type"]').val(),
							archive_post_type: $('[name="archive_post_type"]').val(),
							archive_taxonomy: $('[name="archive_taxonomy"]').val(),
							archive_id: $('[name="archive_id"]').val(),
							nonce: ajax_var.nonce,
							performance_settings: JSON.stringify(getPerformanceSettings(localWidgetID)),
							enable_query_debug: filterSettings?.enableQueryDebug || '',
							query_id: filterSettings?.queryID || '',
						},
						success: function (data) {
							var response = JSON.parse(data);
							var content = response.html;

							if (response.query && ajax_var.isUserLoggedIn) {
								const debugHtml = '<div class="query-debug-frame" style="background:#f5f5f5; border:1px solid #ccc; padding:10px; margin:15px 0; font-family: monospace; white-space: pre-wrap;">' + 
									response.query + 
									'</div>';

								const $debugFrame = $(document).find('.query-debug-frame');
								if ($debugFrame.length) {
									$debugFrame.replaceWith(debugHtml);
								} else {
									filterWidget.append(debugHtml);
								}
							}

							let originalState = originalStates[ localWidgetID ];
							if (data === '0' || !hasValues) {
								localTargetSelector.html(originalState).fadeIn().removeClass('load filter-active');
								var currentSettings = localTargetSelector.data('settings');
								if (currentSettings?.pagination_type === 'cwm_infinite') {
									currentSettings.pagination_type = 'load_more_infinite_scroll';
									localTargetSelector.data('settings', currentSettings);
								}
								if (currentSettings?.pagination_load_type === 'cwm_ajax') {
									currentSettings.pagination_load_type = 'ajax';
									localTargetSelector.data('settings', currentSettings);
								}
								post_count(localTargetSelector);
							} else {
								if ([ 'infinite', 'load_more', 'load_more_on_click', 'load_more_infinite_scroll', 'cwm_infinite' ].includes(paginationType)) {
									if (localTargetSelector.hasClass('filter-active')) {
										var existingContent = localTargetSelector.find('.elementor-grid').children();
										localTargetSelector.hide().empty().append($(content));
										localTargetSelector.find('.elementor-grid').prepend(existingContent);
										localTargetSelector.removeClass('e-load-more-pagination-loading');
										localTargetSelector[ localTargetSelector.hasClass('elementor-widget-posts') ? 'fadeIn' : 'show' ]();
										localTargetSelector.removeClass('load');
									} else {
										localTargetSelector.html(content).fadeIn().removeClass('load');
									}
								} else {
									localTargetSelector.html(content).fadeIn().removeClass('load');
								}

								localTargetSelector.find('.loader').fadeOut();

								if (localTargetSelector.find('.no-post').length || localTargetSelector.find('.e-loop-nothing-found-message').length) {
									if ( nothingFoundMessage && nothingFoundMessage.trim() ) {
										const safeMessage = nothingFoundMessage.replace(/</g, '&lt;').replace(/>/g, '&gt;');
										localTargetSelector.html(`<div class="no-post e-loop-nothing-found-message">${safeMessage}</div>`);
									}
								} else {
									var pagination = localTargetSelector.find('nav[aria-label="Pagination"], nav[aria-label="Product Pagination"]');
									pagination.addClass('pagination-filter');

									var scrollAnchor = localTargetSelector.find('.e-load-more-anchor'),
										next_page = scrollAnchor.data('next-page');

									var loadMoreButton = localTargetSelector.find('.load-more'),
										elementorLoadMoreButton = localTargetSelector.find('.e-load-more-anchor').nextAll().find('.elementor-button-link.elementor-button');

									loadMoreButton.addClass('load-more-filter');
									elementorLoadMoreButton.addClass('load-more-filter');
									localTargetSelector.addClass('filter-active');

									var currentSettings = localTargetSelector.data('settings');
									if (currentSettings?.pagination_type === 'load_more_infinite_scroll') {
										currentSettings.pagination_type = 'cwm_infinite';
										localTargetSelector.data('settings', currentSettings);
									}
									if (currentSettings?.pagination_load_type === 'ajax') {
										currentSettings.pagination_load_type = 'cwm_ajax';
										localTargetSelector.data('settings', currentSettings);
									}

									post_count(localTargetSelector);
								}
							}
							localTargetSelector.removeClass('filter-initialized');
						},
						complete: function() {
							var loadMoreButton = localTargetSelector.find('.load-more-filter'),
								maxPage;

							paginationType = localTargetSelector.data('settings')?.pagination || localTargetSelector.data('settings')?.pagination_type || '';

							var scrollAnchor = localTargetSelector.find('.e-load-more-anchor');
							if (scrollAnchor.length) {
								var currentPage = scrollAnchor.data('page');
								maxPage = scrollAnchor.data('max-page') - 1;
							} else {
								var currentPage = localTargetSelector.find('.pagination').data('page'),
									maxPage = localTargetSelector.find('.pagination').data('max-page') - 1;
							}

							if (scrollToTop === 'yes') {
								window.scrollTo({
									top: localTargetSelector.offset().top - 150,
									behavior: 'smooth'
								});
							}

							if (currentPage > maxPage) {
								localTargetSelector.addClass('e-load-more-pagination-end');
								loadMoreButton.hide();
							}

							ajaxInProgress = false;

							if (localTargetSelector.hasClass('filter-active') && paginationType === 'infinite') {
								debounce(function() {
									bpfwe_infinite_scroll(localWidgetID, localTargetSelector);
								}, 800)();
							}

							if (localTargetSelector.hasClass('filter-active') && paginationType === 'cwm_infinite') {
								debounce(function() {
									elementor_infinite_scroll(localWidgetID, localTargetSelector);
								}, 800)();
							}

							localTargetSelector.find('input').val(searchQuery);

							reinitElementorContent(localTargetSelector);
						},
						error: function (xhr, status, error) {
							console.log('AJAX error:', error);
							let originalState = originalStates[ localWidgetID ];
							localTargetSelector.html(originalState).fadeIn().removeClass('load filter-active');
							var currentSettings = localTargetSelector.data('settings');
							if (currentSettings?.pagination_type === 'cwm_infinite') {
								currentSettings.pagination_type = 'load_more_infinite_scroll';
								localTargetSelector.data('settings', currentSettings);
							}
							if (currentSettings?.pagination_load_type === 'cwm_ajax') {
								currentSettings.pagination_load_type = 'ajax';
								localTargetSelector.data('settings', currentSettings);
							}
							post_count(localTargetSelector);

							reinitElementorContent(localTargetSelector);
						}
					});
				}

				function updateSelectedTermsDisplay(widgetInteractionID, displaySelectedBefore) {
					if (!$('.selected-terms-' + widgetInteractionID + ', .selected-count-' + widgetInteractionID + ', .quick-deselect-' + widgetInteractionID + ', .bpfwe-selected-terms, .bpfwe-selected-count').length) return;

					const $filterWidget = $(`.elementor-widget-filter-widget[data-id="${widgetInteractionID}"]`);
					if (!$filterWidget.length) return;

					let selectedLabels = [];
					let selectedItems = [];

					$filterWidget.find('input[type="checkbox"]:checked, input[type="radio"]:checked').each(function() {
						const labelText = $(this).closest('label').find('span').first().text().trim();
						if (labelText) selectedLabels.push(labelText);
					});

					$filterWidget.find('select option:selected').each(function() {
						const text = $(this).text().trim();
						if (text && $(this).val()) selectedLabels.push(text);
					});

					$filterWidget.find('input[type="checkbox"]:checked, input[type="radio"]:checked, select option:selected').each(function() {
						const $input = $(this);
						const value = $input.val();
						let label = $input.is('option') ? $input.text().trim() : $input.closest('label').find('span').first().text().trim();
						if (value && label) selectedItems.push({ value, label });
					});

					const termsCountText = selectedLabels.length > 0 ? `${selectedLabels.length} ${displaySelectedBefore}` : '';
					$('.selected-count-' + widgetInteractionID + ', .bpfwe-selected-count').each(function() {
						const $widget = $(this);
						const $container = $widget.find('.elementor-widget-container').first();
						const $target = $container.length ? $container.children().first() : $widget.children().first();
						if ($target.length) $target.text(termsCountText);
					});

					const termsText = selectedLabels.length > 0 ? `${displaySelectedBefore} ${selectedLabels.join(', ')}` : '';
					$('.selected-terms-' + widgetInteractionID + ', .bpfwe-selected-terms').each(function() {
						const $widget = $(this);
						const $container = $widget.find('.elementor-widget-container').first();
						const $target = $container.length ? $container.children().first() : $widget.children().first();
						if ($target.length) $target.text(termsText);
					});

					const pillsHtml = selectedItems.map(item => 
						`<span class="bpfwe-term-pill" data-term="${item.value}">
							<span class="bpfwe-term-remove" data-widget-id="${widgetInteractionID}">×</span> ${item.label}
						</span>`
					).join('');

					$('.quick-deselect-' + widgetInteractionID).each(function() {
						const $widget = $(this);
						const $container = $widget.find('.elementor-widget-container').first();
						const $target = $container.length ? $container.children().first() : $widget.children().first();
						if ($target.length) $target.html(pillsHtml);
					});

					// Bind pill removal.
					$(document).off('click', '.bpfwe-term-remove').on('click', '.bpfwe-term-remove', function() {
						const $pill = $(this).parent();
						const termValue = $pill.data('term');
						const widgetId = $(this).data('widget-id');
						const $localFilterWidget = widgetId ? $(`.elementor-widget-filter-widget[data-id="${widgetId}"]`) : $('.elementor-widget-filter-widget');

						if ($localFilterWidget.length) {
							let $input = $localFilterWidget.find(`[value="${termValue}"]`);
							if ($input.is('input[type="checkbox"], input[type="radio"]')) {
								$input.prop('checked', false).trigger('change');
							} else if ($input.is('option')) {
								$input.prop('selected', false);
								const $select = $input.closest('select');
								if (!$select.prop('multiple')) $select.prop('selectedIndex', 0);
								$select.trigger('change');
							}
						}
					});
				}

				function bpfwe_infinite_scroll (widgetID, targetSelector) {
					var scrollAnchor = targetSelector.find('.e-load-more-anchor'),
						$paginationNext = targetSelector.find('.pagination-filter a.next');

					if (!$paginationNext.length) {
						if (filterWidgetObservers[ widgetID ]) {
							filterWidgetObservers[ widgetID ].disconnect();
							filterWidgetObservers[ widgetID ] = null;
						}
						return;
					}

					if ($paginationNext.length && scrollAnchor.length) {
						if (!filterWidgetObservers[ widgetID ]) {
							filterWidgetObservers[ widgetID ] = new IntersectionObserver(function (entries) {
								entries.forEach(function (entry) {
									if (entry.isIntersecting) {
										var $nextLink = targetSelector.find('.pagination-filter a.next');
										if (!ajaxInProgress && $nextLink.length && targetSelector.hasClass('filter-active')) {
											ajaxInProgress = true;
											var url = $nextLink.attr('href');
											var paged = getPageNumber(url);
											get_form_values(null, paged, widgetID);
										}
									}
								});
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							});
						}
						filterWidgetObservers[ widgetID ].observe(scrollAnchor.get(0));
					}
				}

				function elementor_infinite_scroll (widgetID, targetSelector) {
					var scrollAnchor = targetSelector.find('.e-load-more-anchor'),
						currentPage = targetSelector.data('current-page') || 1,
						maxPage = scrollAnchor.data('max-page');

					if (currentPage >= maxPage) {
						if (filterWidgetObservers[ widgetID ]) {
							filterWidgetObservers[ widgetID ].disconnect();
							filterWidgetObservers[ widgetID ] = null;
						}
						return;
					}

					if (scrollAnchor.length && currentPage < maxPage) {
						if (!filterWidgetObservers[ widgetID ]) {
							filterWidgetObservers[ widgetID ] = new IntersectionObserver(function (entries) {
								entries.forEach(function (entry) {
									if (entry.isIntersecting) {
										if (!ajaxInProgress && targetSelector.hasClass('filter-active')) {
											ajaxInProgress = true;
											currentPage++;
											targetSelector.data('current-page', currentPage);
											get_form_values(null, currentPage, widgetID);
										}
									}
								});
							}, {
								root: null,
								rootMargin: infinite_threshold,
								threshold: 0
							});
						}
						filterWidgetObservers[ widgetID ].observe(scrollAnchor.get(0));
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
				filterWidget.on('click', '.reset-form', function() {
					var $resetWidget = $(this).closest('.elementor-widget-filter-widget');
					var resetWidgetID = $resetWidget.data('id');
					var $targetPostWidget = $('div[data-filters-list*="' + resetWidgetID + '"]');
					var localWidgetID = $targetPostWidget.data('id');

					if (!localWidgetID || !$targetPostWidget.length) return;

					var filtersList = $targetPostWidget.data('filters-list') ? $targetPostWidget.data('filters-list').split(',') : [];

					// Reset all linked widgets.
					filtersList.forEach(function (widgetId) {
						var $filterWidget = $('.elementor-widget-filter-widget[data-id="' + widgetId + '"]');
						if ($filterWidget.length) {
							$filterWidget.find('input:checked').prop('checked', false);
							$filterWidget.find('select').each(function() {
								$(this).val($(this).find('option:first').val()).trigger('change');
							});
							$filterWidget.find('.bpfwe-numeric-wrapper input').each(function() {
								var initialVal = $(this).data('base-value');
								$(this).val(initialVal).trigger('input');
							});
							$filterWidget.find('input.input-text').val('').trigger('input');
						}

						var $sortingWidget = $('.elementor-widget-sorting-widget[data-id="' + widgetId + '"]');
						if ($sortingWidget.length) {
							$sortingWidget.find('form.form-order-by select').prop('selectedIndex', 0).trigger('change');
						}

						var $searchWidget = $('.elementor-widget-search-bar-widget[data-id="' + widgetId + '"]');
						if ($searchWidget.length) {
							$searchWidget.find('form.search-post input[name="s"]').val('').trigger('input');
						}
					});

					$targetPostWidget.addClass('filter-initialized');
					$targetPostWidget.removeClass('filter-active');
					$targetPostWidget.data('current-page', 1);
					get_form_values(resetWidgetID);
				});

				post_count(targetSelector);
			},
		});

		if (!elementorFrontend.isEditMode()) {
			elementorFrontend.elementsHandler.attachHandler(dynamic_handler, FilterWidgetHandler);
		} else {
			elementorFrontend.elementsHandler.attachHandler('filter-widget', FilterWidgetHandler);
		}
	});
})(jQuery);