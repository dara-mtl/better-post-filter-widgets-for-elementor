(function ($) {
	"use strict";
	$(window).on('elementor/frontend/init', function() {
		const $document = $(document);
		const originalStates        = {};
		const postsPerPageCache     = {};

		let dynamicHandler          = '';
		let globalEventsBound       = false;
		let ajaxInProgress          = false;

		if ($('.elementor-widget-filter-widget').length) {
			dynamicHandler = 'filter-widget';
		} else if ($('.elementor-widget-search-bar-widget').length) {
			dynamicHandler = 'search-bar-widget';
		} else {
			dynamicHandler = 'sorting-widget';
		}

		// Debounce the interactions
		function debounce (func, delay) {
			let timeoutId;
			return function() {
				const context = this,
				args = arguments;
				clearTimeout(timeoutId);
				timeoutId = setTimeout(() => func.apply(context, args), delay);
			};
		}

		function reduceFields(fields) {
		  const grouped = {};
		  fields.forEach((cur) => {
			const { taxonomy, terms, logic } = cur;
			if (!grouped[taxonomy]) {
			  grouped[taxonomy] = { taxonomy, terms: [], logic };
			}
			grouped[taxonomy].terms = grouped[taxonomy].terms.concat(terms);
		  });
		  return Object.values(grouped);
		}

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

		// Link filter/search/sort widgets to their target post widgets via data-filters-list.
		function linkFilterWidgets() {
			$('.elementor-widget-filter-widget, .elementor-widget-search-bar-widget, .elementor-widget-sorting-widget').each(function() {
				var $widget = $(this);
				var interactionWidgetID = $widget.data('id');
				var settings = $widget.data('settings');
				var targetSelector = settings?.target_selector;

				// Skip widgets with no valid target selector or non-existent target.
				if (!targetSelector || !$(targetSelector).length) {
					return;
				}

				var $target = $(targetSelector);
				var filtersList = $target.data('filters-list') ? $target.data('filters-list').split(',') : [];

				// Avoid duplicate widget IDs in filters-list.
				if (!filtersList.includes(interactionWidgetID)) {
					filtersList.push(interactionWidgetID);
					$target.data('filters-list', filtersList.join(','));
					$target.attr('data-filters-list', filtersList.join(','));
				}

				var targetWidgetID = $target.data('id');

				// Initialize original state only if not already set.
				if (!originalStates[targetWidgetID]) {
					originalStates[targetWidgetID] = $target.html();
				}

				// Initialize posts per page cache only if not already set.
				if (!postsPerPageCache[targetWidgetID]) {
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
					postsPerPageCache[targetWidgetID] = postsPerPage > 0 ? postsPerPage : 50;
				}
			});
		}
		linkFilterWidgets();

		// Add Support for URL.
		function bpfweInitUrlFilters() {
			const params = new URLSearchParams(window.location.search);
			const formId = params.get('results');
			if (!formId) return;
			const $form = $('#' + formId);
			if (!$form.length) return;
			let hasPrefill = false;
			function getSection($input) {
				const classes = ($input.closest('.flex-wrapper').attr('class') || '').split(/\s+/);
				for (let i = 0; i < classes.length; i++) {
					if (classes[i] && classes[i] !== 'flex-wrapper') return classes[i];
				}
				return null;
			}
			let urlChanged = false;
			['min__regular_price', 'max__regular_price'].forEach(function(oldKey) {
				if (params.has(oldKey)) {
					const newKey = oldKey.replace('__', '_');
					params.set(newKey, params.get(oldKey));
					params.delete(oldKey);
					urlChanged = true;
				}
			});
			if (urlChanged) {
				const newUrl = window.location.pathname + '?' + params.toString();
				window.history.replaceState({}, '', newUrl);
			}
			// Build URL query from current form state.
			function updateUrl() {
				const query = new URLSearchParams();
				query.set('results', formId);
				// checkboxes & radios - NO section suffix.
				$form.find(':checkbox:checked, :radio:checked').each(function() {
					const $input = $(this);
					const name = $input.attr('name');
					if (!name) return;
					const val = $input.val();
					const existing = query.get(name);
					query.set(name, existing ? existing + ',' + val : val);
				});
				// text & number inputs.
				$form.find('input[type="text"], input[type="number"]').each(function() {
					const $input = $(this);
					const name = $input.attr('name');
					const val = $input.val();
					if (!name || !val) return;
					if (name.startsWith('min_') || name.startsWith('max_')) {
						query.set(name, val);
						return;
					}
					const section = getSection($input);
					const key = section ? name + '_' + section : name;
					query.set(key, val);
				});
				// selects.
				$form.find('select').each(function() {
					const $input = $(this);
					const name = $input.attr('name');
					if (!name) return;
					let val = $input.val();
					if (val === null || val === '') return;
					if (Array.isArray(val)) {
						val = val.join(',');
					} 
					const existing = query.get(name);
					query.set(name, existing ? existing + ',' + val : val);
				});
				const newUrl = window.location.pathname + '?' + query.toString();
				window.history.replaceState({}, '', newUrl);
			}
			const sectionClasses = (function() {
				const classes = {};
				$form.find('.flex-wrapper').each(function() {
					const cls = ($(this).attr('class') || '').split(/\s+/);
					for (let i = 0; i < cls.length; i++) {
						if (cls[i] && cls[i] !== 'flex-wrapper') classes[cls[i]] = true;
					}
				});
				return Object.keys(classes).sort(function(a, b) {
					return b.length - a.length;
				});
			})();
			// Prefill from URL.
			params.forEach(function(value, key) {
				if (key === 'results') return;
				if (key.startsWith('min_') || key.startsWith('max_')) {
					let $inputs = $form.find('[name="' + key + '"]');
					if (!$inputs.length) {
						const alt = key.replace('_', '__');
						$inputs = $form.find('[name="' + alt + '"]');
					}
					if ($inputs.length) {
						$inputs.val(value);
						hasPrefill = true;
					}
					return;
				}
				let matched = false;
				for (let i = 0; i < sectionClasses.length; i++) {
					const section = sectionClasses[i];
					const suffix = '_' + section;
					if (key.endsWith(suffix)) {
						const baseKey = key.slice(0, -suffix.length);
						const $scope = $form.find('.flex-wrapper.' + section);
						const $inputs = $scope.find('[name="' + baseKey + '"]');
						if ($inputs.length) {
							if ($inputs.is('input[type="text"], input[type="number"]')) {
								$inputs.val(value);
								hasPrefill = true;
								matched = true;
								break;
							} else if ($inputs.is(':checkbox, :radio')) {
								const values = value.split(',');
								$inputs.each(function() {
									if (values.includes($(this).val())) {
										$(this).prop('checked', true);
										hasPrefill = true;
									}
								});
								matched = true;
								break;
							} else {
								const values = $inputs.prop('multiple') ? value.split(',') : value;
								hasPrefill = true;
								matched = true;
								break;
							}
						}
					}
				}
				if (matched) return;
				const $inputsDirect = $form.find('[name="' + key + '"]');
				if (!$inputsDirect.length) return;
				if ($inputsDirect.is(':checkbox, :radio')) {
					const values = value.split(',');
					$inputsDirect.each(function() {
						if (values.includes($(this).val())) {
							$(this).prop('checked', true);
							hasPrefill = true;
						}
					});
					return;
				}
				if ($inputsDirect.is('input[type="text"], input[type="number"]')) {
						$inputsDirect.val(value);
						hasPrefill = true;
					} else if ($inputsDirect.is('select')) {
						const values = $inputsDirect.prop('multiple') ? value.split(',') : value;
					
					if ($inputsDirect.prop('multiple')) {
						setTimeout(function() {
							$inputsDirect.val(values).trigger('change');
						}, 100);
					} else {
						$inputsDirect.val(values).trigger('change');
					}
					
					hasPrefill = true;
				}
				
			});
			// Trigger prefill filter logic.
			if (hasPrefill) {
				setTimeout(function() {
					$form.find('select[multiple]').each(function() {
						const $select = $(this);
						const val = $select.val();
						if (!val || val.length === 0) {
							const paramName = $select.attr('name');
							const paramVal = params.get(paramName);
							if (paramVal) {
								const values = paramVal.split(',');
								$select.val(values).trigger('change');
							}
						}
					});
					
					updateUrl();
					
					$form.find(':checked, input[type="text"][value!=""], input[type="number"][value!=""]').trigger('change');
					$form.trigger('change');
				}, 300);
			}
			
			// Update URL dynamically.
			const delegatedSelector = '.bpfwe-filter-item, .input-text, [class^="bpfwe-filter-range-"], input[type="text"], input[type="number"], :checkbox, :radio, select';
			$form.on('change input', delegatedSelector, function() {
				updateUrl();
			});
			$form.data('bpfweUpdateUrl', updateUrl);
		}
		bpfweInitUrlFilters();

		// Handle Filter toggle.
		$document.on('click', '.filter-title.collapsible', function() {
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
				const filterWidget = this.$element.find('.filter-container');

				// Initialize single-select dropdowns with Select2.
				filterWidget.find('.bpfwe-select2 select').each(function (index) {
					const $select = $(this);
					const parentElement = $select.closest('.bpfwe-select2');
					const uniqueId = 'bpfwe-select2-' + Math.floor(Math.random() * 1000);
					$select.attr('id', uniqueId).prop('multiple', false).select2({
						dropdownParent: parentElement,
					});
					parentElement.css({
						"visibility": "visible",
						"opacity": "1",
						"transition": "opacity 0.3s ease-in-out"
					});
				});

				// Initialize multi-select dropdowns with Select2 and plus symbol logic.
				filterWidget.find('.bpfwe-multi-select2 select').each((index, el) => {
					const $select = $(el);
					const parentElement = $select.closest('.bpfwe-multi-select2');
					const uniqueId = 'bpfwe-multi-select2-' + Math.floor(Math.random() * 1000);
					$select.attr('id', uniqueId).prop('multiple', true).select2({
						dropdownParent: parentElement,
					});
					$select.val(null).trigger('change.select2');
					parentElement.css({
						visibility: 'visible',
						opacity: '1',
						transition: 'opacity 0.3s ease-in-out'
					});

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

				// When "Select All" span is clicked.
				filterWidget.find('.bpfwe-select-all').on('click', function() {
					const $selectAll = $(this);
					const taxonomy = $selectAll.data('taxonomy');
					const isChecked = !$selectAll.hasClass('checked');

					$selectAll.toggleClass('checked', isChecked);

					// Limit scope to current filterWidget only, skip inputs inside disabled labels.
					const $relatedCheckboxes = filterWidget.find('input.bpfwe-filter-item').filter('[data-taxonomy="' + taxonomy + '"]').filter(function() {
						return !$(this).closest('label').hasClass('bpfwe-option-disabled');
					});

					$relatedCheckboxes.prop('checked', isChecked).trigger('change');
				});

				// When "Select All" span is clicked again.
				filterWidget.find('input.bpfwe-filter-item').on('change', function() {
					const $changed = $(this);
					const taxonomy = $changed.data('taxonomy');

					const $groupCheckboxes = filterWidget.find('input.bpfwe-filter-item').filter('[data-taxonomy="' + taxonomy + '"]').filter(function() {
						return !$(this).closest('label').hasClass('bpfwe-option-disabled');
					});

					const $selectAll = filterWidget.find('.bpfwe-select-all[data-taxonomy="' + taxonomy + '"]');
					const allChecked = $groupCheckboxes.length === $groupCheckboxes.filter(':checked').length;

					$selectAll.toggleClass('checked', allChecked);
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

				if (!globalEventsBound) {
					globalEventsBound = true;

					// Radio input: store checked state on mousedown to allow deselection.
					$document.off('mousedown.bpfwe-filter', 'form.form-tax input[type="radio"]').on('mousedown.bpfwe-filter', 'form.form-tax input[type="radio"]', function (e) {
						$(this).data('wasChecked', $(this).prop('checked'));
					});

					// Radio input: deselect when clicking an already-checked radio.
					$document.off('click.bpfwe-filter', 'form.form-tax input[type="radio"]').on('click.bpfwe-filter', 'form.form-tax input[type="radio"]', function (e) {
						var $radio = $(this);
						if ($radio.data('wasChecked')) {
							// user clicked an already-checked radio - deselect it.
							$radio.prop('checked', false).trigger('change');
						}
						$radio.removeData('wasChecked');
					});

					// Visual range label: capture radio checked state before click (ignore direct input clicks).
					$document.off('mousedown.bpfwe-filter', 'form.form-tax label.bpfwe-visual-range-option').on('mousedown.bpfwe-filter', 'form.form-tax label.bpfwe-visual-range-option', function (e) {
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

					// Visual range label: toggle radio selection and support deselect behavior.
					$document.off('click.bpfwe-filter', 'form.form-tax label.bpfwe-visual-range-option').on('click.bpfwe-filter', 'form.form-tax label.bpfwe-visual-range-option', function (e) {
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

					// Filter form: prevent Enter key from submitting inside inputs.
					$document.on('keydown.bpfwe-filter', 'form.form-tax input', function (e) {
						if (e.which === 13) {
							e.preventDefault();
						}
					});

					// Filter form: disable native form submission.
					$document.on('submit.bpfwe-filter', 'form.form-tax', function (e) {
						e.preventDefault();
					});

					// Filter form: auto-apply filters on input change (debounced).
					$document.off('change.bpfwe-filter input.bpfwe-filter', 'form.form-tax').on('change.bpfwe-filter input.bpfwe-filter','form.form-tax', debounce(function (e) {
						var $target = $(e.target);

						if ($target.is('.bpfwe-numeric-wrapper input.input-val')) {
							return;
						}

						// Ignore Select2 search field typing.
						if ($target.is('.select2-search__field') || $target.closest('.select2-container').length) {
							return;
						}

						var isTouchDevice = ('ontouchstart' in window || navigator.maxTouchPoints > 0 || window.matchMedia("(pointer: coarse)").matches);

						//if (isTouchDevice && $target.is('.bpfwe-numeric-wrapper input') && $target.is(':focus') && $target.val() === '') {
						if (isTouchDevice && $target.is('.bpfwe-numeric-wrapper input') && ($target.is(':focus') || $target.val() === '')) {
							return;
						}

						var $widget = $(this).closest('.elementor-widget-filter-widget');
						if ($target.is('.bpfwe-numeric-wrapper input')) {
							var $activeWrapper = $target.closest('.bpfwe-numeric-wrapper');
							snapshotNumericFacet($widget, $activeWrapper);
						} else {
							$(this).find('.bpfwe-numeric-wrapper[data-faceted-range]').removeAttr('data-faceted-range');
						}

						var widgetInteractionID = $widget.data('id');
						if (!widgetInteractionID) return;

						const isSubmitPresent = $widget.find('.submit-form').length > 0;

						if (!isSubmitPresent) {
							getFormValues(widgetInteractionID);
						}

					}, 700));

					// Numeric range: apply filter only on valid complete range or Enter key.
					$document.off('change.bpfwe-filter input.bpfwe-filter', 'form.form-tax .bpfwe-numeric-wrapper input.input-val').on('change.bpfwe-filter input.bpfwe-filter', 'form.form-tax .bpfwe-numeric-wrapper input.input-val', debounce(function (e) {

						const $wrapper = $(this).closest('.bpfwe-numeric-wrapper');
						if (!$wrapper.length) return;

						const $widget = $wrapper.closest('.elementor-widget-filter-widget');
						const widgetInteractionID = $widget.data('id');
						if (!widgetInteractionID) return;

						const isSubmitPresent = $widget.find('.submit-form').length > 0;

						const $min = $wrapper.find('input[name^="min_"]');
						const $max = $wrapper.find('input[name^="max_"]');
						if (!$min.length || !$max.length) return;

						const minVal = ($min.val() || '').trim();
						const maxVal = ($max.val() || '').trim();

						const isTouchDevice = ('ontouchstart' in window || navigator.maxTouchPoints > 0 || window.matchMedia("(pointer: coarse)").matches);

						// Skip incomplete inputs on touch devices.
						if (isTouchDevice && $(this).is('.bpfwe-numeric-wrapper input.input-val') && ($min.is(':focus') || $max.is(':focus') || minVal === '' || maxVal === '')) {
							return;
						}

						// Do not trigger on partial ranges.
						if (minVal === '' || maxVal === '') return;
						if (isNaN(minVal) || isNaN(maxVal)) return;
						if (Number(minVal) >= Number(maxVal)) return;

						if (!isSubmitPresent) {
							getFormValues(widgetInteractionID);
						}

					}, 700));

					// Filter submit button: manually trigger filter evaluation.
					$document.off('click.bpfwe-filter', 'form.form-tax .submit-form').on('click.bpfwe-filter', 'form.form-tax .submit-form', function() {
						var $widget = $(this).closest('.elementor-widget-filter-widget');
						var widgetInteractionID = $widget.data('id');
						if (!widgetInteractionID) return;
						getFormValues(widgetInteractionID);
						return false;
					});

					// Sorting widget: apply ordering change immediately.
					$document.off('change.bpfwe-filter', 'form.form-order-by').on('change.bpfwe-filter', 'form.form-order-by', function() {
						var $widget = $(this).closest('.elementor-widget-sorting-widget');
						var widgetInteractionID = $widget.data('id');
						if (!widgetInteractionID) return;
						getFormValues(widgetInteractionID);
					});

					// Search widget: submit search and optionally prevent redirect.
					$document.off('submit.bpfwe-filter', 'form.search-post').on('submit.bpfwe-filter', 'form.search-post', function() {
						var $widget = $(this).closest('.elementor-widget-search-bar-widget');
						var widgetInteractionID = $widget.data('id');
						if (!widgetInteractionID) return;
						getFormValues(widgetInteractionID);
						if ($(this).hasClass('no-redirect')) {
							return false;
						}
					});

					// Pagination links: load specific page via AJAX.
					$document.off('click.bpfwe-filter', '.pagination-filter a').on('click.bpfwe-filter', '.pagination-filter a', function (e) {
						var postWidgetID = $(this).closest('[data-id]').data('id');
						e.preventDefault();
						var url = $(this).attr('href');
						var paged = getPageNumber(url);
						getFormValues(null, paged, postWidgetID);
					});

					// Load more button: fetch next page or fallback to pagination link.
					$document.off('click.bpfwe-filter', '.load-more-filter').on('click.bpfwe-filter', '.load-more-filter', function (e) {
						e.preventDefault();

						var $widget = $(this).closest('[data-id]');
						var postWidgetID = $widget.data('id');
						var url = $widget.find('.e-load-more-anchor').data('next-page');

						if (url) {
							var paged = getPageNumber(url);
							getFormValues(null, paged, postWidgetID);
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
				}

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

				let globalTemplateID = null;

				// Scan all filter widgets on the page.
				$('.elementor-widget-filter-widget, .elementor-widget-search-bar-widget, .elementor-widget-sorting-widget').each(function() {
					const settings = $(this).data('settings');
					if (settings && settings.elementor_template_id) {
						globalTemplateID = settings.elementor_template_id;
						return false;
					}
				});

				// If a global template ID exists, use it directly.
				let pageID = globalTemplateID;

				if (!pageID) {
					// fallback logic only runs if no override.
					pageID = window.elementorFrontendConfig.post.id || null;
					if (!pageID) {
						if (!widgetID) return;
						var $outermost = $('[data-id="' + widgetID + '"]').parents('[data-elementor-id]').last();
						if ($outermost.length) pageID = $outermost.data('elementor-id');
					} else {
						if (!widgetID) return;
						var $outermost = $('[data-id="' + widgetID + '"]').parents('[data-elementor-id]').last();
						if ($outermost.length) {
							var isTemplate = $outermost.data('elementor-post-type') === 'elementor_library';
							var typeAttr = $outermost.data('elementor-type');
							if (isTemplate && typeAttr && typeAttr.indexOf('single') !== -1) {
								pageID = $outermost.data('elementor-id');
							}
						}
					}
				}

				filterWidget.on('click', '.reset-form', function () {
					var resetWidgetID = $(this).closest('.elementor-widget-filter-widget').data('id');

					if (!resetWidgetID) {
						return;
					}

					bpfweResetLinkedWidgets(resetWidgetID, "full");
				});

				if (currentUrl.includes('?search=')) {
					getFormValues();
				}

				function postCount($target) {
					let postCount = $target.find('.post-container').data('total-post') || 0;
					postCount = Number(postCount);
					$('.filter-post-count .number').text(postCount);
				}

				// Retrieve form values, process filters, and make AJAX request for filtered posts.
				function getFormValues(widgetInteractionID, paged, postWidgetID) {
					if ($document.find('div[data-filters-list*="' + widgetInteractionID + '"]').length === 0) {
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

					let hasValues = false;

					let nothingFoundMessage = '';
					let isFacetted = false;
					let FacetWidgetId = '';
					let customAjax = false;
					let scrollToTop = '';
					let displaySelectedBefore = '';
					let enableQueryDebug = false;
					let injectID = false;
					let queryID = '';
					let dynamicFiltering = false;
					let groupLogic = 'AND';

					let performanceSettings = {
						optimize_query: false,
						no_found_rows: false,
						suppress_filters: false,
						posts_per_page: -1
					};
					
					// Nuke duplicated content in sticky column.
					$('.elementor-sticky__spacer').empty();

					let resolvedFilterWidgetId = isFiltering ? widgetInteractionID : null;

					if (!resolvedFilterWidgetId) {
						let $postWidget = $('div[data-id="' + localWidgetID + '"]');
						let closestDist = Infinity;
						filtersList.forEach(id => {
							let $filter = $('.elementor-widget-filter-widget[data-id="' + id + '"]');
							if ($filter.length) {
								let dist = Math.abs($postWidget.offset().top - $filter.offset().top); // Vertical distance as proxy
								if (dist < closestDist) {
									closestDist = dist;
									resolvedFilterWidgetId = id;
								}
							}
						});
					}

					if (!resolvedFilterWidgetId) {
						// Fallback: Find last visible filter widget on the page.
						const $allVisibleFilters = $('.elementor-widget-filter-widget:visible');

						if ($allVisibleFilters.length > 0) {
							const $lastOne = $allVisibleFilters.last();
							resolvedFilterWidgetId = $lastOne.data('id');
						}
					}
					
					let $loadingWidget = isFiltering ? $('.elementor-widget-filter-widget[data-id="' + widgetInteractionID + '"]') : $('.elementor-widget-filter-widget[data-id="' + resolvedFilterWidgetId + '"]');

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
						// Get the current filter settings.
						const currentfilterWidgetId = resolvedFilterWidgetId || filterWidgetId;

						var $filterWidget = $('.elementor-widget-filter-widget[data-id="' + currentfilterWidgetId + '"]');
						if ($filterWidget.length) {
							var filterSettings = $filterWidget.data('settings') || {};
							post_type = filterSettings.filter_post_type || '';
							nothingFoundMessage = filterSettings.nothing_found_message || '';
							isFacetted = filterSettings.is_facetted || false;
							FacetWidgetId = currentfilterWidgetId;
							customAjax = filterSettings.filter_custom_handler || false;
							scrollToTop = filterSettings.scroll_to_top || '';
							displaySelectedBefore = filterSettings.display_selected_before || '';
							enableQueryDebug = filterSettings.enable_query_debug || false;
							injectID = filterSettings.inject_query_id || false;
							queryID = filterSettings.filter_query_id || '';
							dynamicFiltering = filterSettings.dynamic_filtering || false;
							groupLogic = filterSettings.group_logic || 'AND';

							performanceSettings = {
								optimize_query: filterSettings.optimize_query === 'yes',
								no_found_rows: filterSettings.no_found_rows === 'yes',
								suppress_filters: filterSettings.suppress_filters === 'yes',
								posts_per_page: parseInt(filterSettings.posts_per_page, 10) || -1
							};
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
						custom_field_relational = [],
						custom_field_like = [],
						numeric_field = [];

					filtersList.forEach(function (filterWidgetId) {
						var $searchWidgets = $('.elementor-widget-search-bar-widget[data-id="' + filterWidgetId + '"]');

						$searchWidgets.each(function () {
							var val = $(this).find('form.search-post input').val();
							if (val && val.trim().length > 0) {
								searchQuery = val.trim();
								return false;
							}
						});

						if (searchQuery !== '') {
							hasValues = true;
						}

						var $sortingWidget = $('.elementor-widget-sorting-widget[data-id="' + filterWidgetId + '"]');
						if ($sortingWidget.length) {
							var $select = $sortingWidget.find('.form-order-by select'),
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

							$filterWidget.find('.bpfwe-custom-field-relational-wrapper input:checked').each(function() {
								var self = $(this);
								custom_field_relational.push({
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

							// Fixed range numeric fields.
							$filterWidget.find('.bpfwe-numeric-wrapper').each(function () {
								var $wrapper = $(this);
								var snapshot = $wrapper.attr('data-faceted-range');

								$wrapper.find('input:not(.input-val)').each(function () {
									var self = $(this);
									var initialVal = self.attr('data-base-value');

									if (self.val() === '' || self.val() != initialVal || snapshot) {

										if (self.val() === '') {
											self.val(initialVal);
										}

										var fieldClass = self.attr('class').split(' ')[0];

										$wrapper.find('input').each(function () {
											var _this = $(this);

											if (_this.hasClass(fieldClass)) {
												var terms = snapshot !== undefined ? snapshot.split('|') : _this.val();

												numeric_field.push({
													taxonomy: _this.data('taxonomy'),
													terms: terms,
													logic: $wrapper.data('logic')
												});

												hasValues = true;
											}
										});
									}
								});
							});

							// Free-range numeric fields.
							$filterWidget.find('.bpfwe-numeric-wrapper').has('input.input-val').each(function() {
								const $wrapper = $(this);
								const $min = $wrapper.find('input.input-val').first();
								const $max = $wrapper.find('input.input-val').last();

								const minVal = ($min.val() || '').trim();
								const maxVal = ($max.val() || '').trim();

								if (minVal === '' && maxVal === '') {
									return;
								}

								[$min, $max].forEach($el => {
									const currentVal = ($el.val() || '').trim();

									numeric_field.push({
										taxonomy: $el.data('taxonomy'),
										terms: currentVal,
										logic: $wrapper.data('logic') || $el.closest('[data-logic]').data('logic')
									});
								});

								hasValues = true;
							});

							// Visual range fields.
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

					// Facetted part for a single select, not modified from within it's group but influenced by the other selections.
					$document.on('change', '.flex-wrapper input[type="radio"], .flex-wrapper select:not([multiple])', function () {
						$('.flex-wrapper').removeClass('bpfwe-skip-update');
						$(this).closest('.flex-wrapper').addClass('bpfwe-skip-update');
					});

					$document.on('change', '.flex-wrapper input[type="checkbox"], .flex-wrapper select[multiple]', function () {
						$('.flex-wrapper').removeClass('bpfwe-skip-update');
						setTimeout(function () {
							$('.flex-wrapper').removeClass('bpfwe-skip-update');
						}, 800);
					});

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

						$document.trigger('elementor/lazyload/observe');
					}
			
					updateSelectedTermsDisplay(widgetInteractionID, displaySelectedBefore);

					var taxonomy_output = reduceFields(category),
						custom_field_output = reduceFields(custom_field),
						custom_field_relational_output = reduceFields(custom_field_relational),
						custom_field_like_output = reduceFields(custom_field_like),
						numeric_output = reduceFields(numeric_field);
					
					const ajaxUrl = customAjax ? ajax_var.bpfwe_url : ajax_var.url;

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						async: true,
						data: {
							action: 'post_filter_results',
							widget_id: localWidgetID,
							filter_widget: isFacetted ? FacetWidgetId : '',
							page_id: pageID,
							group_logic: groupLogic,
							search_query: searchQuery,
							date_query: dateQuery,
							taxonomy_output: taxonomy_output,
							dynamic_filtering: dynamicFiltering,
							custom_field_output: custom_field_output,
							custom_field_relational_output: custom_field_relational_output,
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
							performance_settings: JSON.stringify(performanceSettings),
							enable_query_debug: enableQueryDebug,
							inject_id: injectID ,
							query_id: queryID,
						},
						beforeSend: function() {
							$loadingWidget.addClass('load');
						},
						success: function (data) {
							var response = JSON.parse(data);
							var content = response.html;
							var filters = response.filters;

							if (response.query && ajax_var.isUserLoggedIn) {
								const debugHtml = '<div class="query-debug-frame" style="background:#f5f5f5; border:1px solid #ccc; padding:10px; margin:15px 0; font-family: monospace; white-space: pre-wrap;">' + response.query + '</div>';
								const $debugFrame = $loadingWidget.find('.query-debug-frame');
								if ($debugFrame.length) {
									$debugFrame.replaceWith(debugHtml);
								}
							}
							
							bpfweSyncFacetFilters(data, hasValues, filters);

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
								postCount(localTargetSelector);
								var resetWidgetID = $loadingWidget.closest('.elementor-widget-filter-widget').data('id');
								bpfweResetLinkedWidgets(resetWidgetID, "partial");
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
									var pagination = localTargetSelector.find('.elementor-pagination, .pagination, nav[aria-label="Pagination"], nav[aria-label="Product Pagination"]');
									pagination.addClass('pagination-filter');

									var scrollAnchor = localTargetSelector.find('.e-load-more-anchor');

									var loadMoreButton = localTargetSelector.find('.load-more'),
										elementorLoadMoreButton = localTargetSelector.find('.e-load-more-anchor').nextAll().find('a.elementor-button');

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

									postCount(localTargetSelector);
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
									bpfweInfiniteScroll(localWidgetID, localTargetSelector);
								}, 800)();
							}

							if (localTargetSelector.hasClass('filter-active') && paginationType === 'cwm_infinite') {
								debounce(function() {
									elementorInfiniteScroll(localWidgetID, localTargetSelector);
								}, 800)();
							}

							localTargetSelector.find('input').val(searchQuery);

							reinitElementorContent(localTargetSelector);
							$loadingWidget.removeClass('load');
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

							postCount(localTargetSelector);
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
					// Checkboxes & radios
					$filterWidget.find('input[type="checkbox"]:checked, input[type="radio"]:checked').each(function() {
						let labelText = $(this).closest('label').find('span').first().text().trim();
						labelText = labelText.replace(/\s*\(\d+\)\s*$/, '').replace(/\s*\(\–\)\s*$/, '');
						if (labelText) selectedLabels.push(labelText);
					});
					// Selects
					$filterWidget.find('select option:selected').each(function() {
						let text = $(this).text().trim();
						text = text.replace(/\s*\(\d+\)\s*$/, '').replace(/\s*\(\–\)\s*$/, '');
						if (text && $(this).val()) selectedLabels.push(text);
					});
					// Build selectedItems
					$filterWidget.find('input[type="checkbox"]:checked, input[type="radio"]:checked, select option:selected').each(function() {
						const $input = $(this);
						const value = $input.val();
						let label = $input.is('option') ? $input.text().trim() : $input.closest('label').find('span').first().text().trim();
						label = label.replace(/\s*\(\d+\)\s*$/, '').replace(/\s*\(\–\)\s*$/, '');
						if (value && label) selectedItems.push({ value, label });
					});
					// Numeric ranges
					$filterWidget.find('.bpfwe-numeric-wrapper').each(function() {
						const $wrapper = $(this);
						const $min = $wrapper.find('input[name^="min_"]');
						const $max = $wrapper.find('input[name^="max_"]');
						if ($min.hasClass('input-val') || $max.hasClass('input-val')) return;
						if ($min.length && $max.length) {
							const minVal = $min.val();
							const maxVal = $max.val();
							const baseMin = $min.attr('data-base-value');
							const baseMax = $max.attr('data-base-value');
							// Skip if empty
							if (minVal === '' && maxVal === '') return;
							if (minVal != baseMin || maxVal != baseMax) {
								const label = `${minVal || ''} - ${maxVal || ''}`;
								selectedLabels.push(label);
								selectedItems.push({
									value: `${minVal || ''}-${maxVal || ''}`,
									label: label,
									type: 'range',
									minInput: $min,
									maxInput: $max
								});
							}
						}
					});
					const termsCountText = selectedLabels.length > 0 ? `${selectedLabels.length} ${displaySelectedBefore}` : '';
					$('.selected-count-' + widgetInteractionID + ', .bpfwe-selected-count').each(function() {
						const $widget = $(this);
						const $container = $widget.find('.elementor-widget-container').first();
						const $target = $container.length ? $container.children().first() : $widget.children().first();
						if ($target.length) $target.text(termsCountText);
					});
					const termsText = selectedLabels.length > 0 ? `${displaySelectedBefore || ''} ${selectedLabels.join(', ')}`.trim() : '';
					$('.selected-terms-' + widgetInteractionID + ', .bpfwe-selected-terms').each(function() {
						const $widget = $(this);
						const $container = $widget.find('.elementor-widget-container').first();
						const $target = $container.length ? $container.children().first() : $widget.children().first();
						if ($target.length) $target.text(termsText);
					});
					const pillsHtml = selectedItems.map(item => {
						if (item.type === 'range') {
							return `<span class="bpfwe-term-pill" data-range="true" data-min="${item.minInput.attr('name')}" data-max="${item.maxInput.attr('name')}" data-widget-id="${widgetInteractionID}">
							<span class="bpfwe-term-remove" data-widget-id="${widgetInteractionID}">×</span> ${item.label}
							</span>`;
						}
						return `<span class="bpfwe-term-pill" data-term="${item.value}">
						<span class="bpfwe-term-remove" data-widget-id="${widgetInteractionID}">×</span> ${item.label}
						</span>`;
					}).join('');
					$('.quick-deselect-' + widgetInteractionID).each(function() {
						const $widget = $(this);
						const $container = $widget.find('.elementor-widget-container').first();
						const $target = $container.length ? $container.children().first() : $widget.children().first();
						if ($target.length) $target.html(pillsHtml);
					});
					$document.off('click', '.bpfwe-term-remove').on('click', '.bpfwe-term-remove', function() {
						const $pill = $(this).parent();
						const widgetId = $(this).data('widget-id');
						const $localFilterWidget = widgetId ? $(`.elementor-widget-filter-widget[data-id="${widgetId}"]`) : $('.elementor-widget-filter-widget');
						if ($pill.data('range')) {
							const minName = $pill.data('min');
							const maxName = $pill.data('max');
							const $min = $localFilterWidget.find(`input[name="${minName}"]`);
							const $max = $localFilterWidget.find(`input[name="${maxName}"]`);
							if ($min.length && $max.length) {
								$min.val($min.attr('data-base-value')).trigger('change');
								$max.val($max.attr('data-base-value')).trigger('change');
							}
							} else if ($localFilterWidget.length) {
							let $input = $localFilterWidget.find(`[value="${$pill.data('term')}"]`);
							if ($input.is('input[type="checkbox"], input[type="radio"]')) {
								$input.prop('checked', false).trigger('change');
								} else if ($input.is('option')) {
								$input.prop('selected', false);
								const $select = $input.closest('select');
								if (!$select.prop('multiple')) $select.prop('selectedIndex', 0);
								$select.trigger('change');
							}
						}
						updateSelectedTermsDisplay(widgetId, displaySelectedBefore);
					});
				}

				function bpfweInfiniteScroll(widgetID, targetSelector) {
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
											getFormValues(null, paged, widgetID);
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

				function elementorInfiniteScroll(widgetID, targetSelector) {
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
											getFormValues(null, currentPage, widgetID);
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

				function snapshotNumericFacet($widget, $activeWrapper) {
					var settings = $widget.data('settings') || {};

					if (settings.is_facetted !== 'yes') {
						return;
					}

					if (!$activeWrapper || !$activeWrapper.length) {
						return;
					}

					// Clear previous numeric facet states.
					//$widget.find('.bpfwe-numeric-wrapper[data-faceted-range]').not($activeWrapper).removeAttr('data-faceted-range');

					var $inputs = $activeWrapper.find('input[type="number"]');

					if ($inputs.length < 2) {
						return;
					}

					var minVal = $inputs.eq(0).val();
					var maxVal = $inputs.eq(1).val();

					if (!minVal || !maxVal) {
						return;
					}

					$activeWrapper.attr('data-faceted-range', minVal + '|' + maxVal);
				}

				function bpfweSyncFacetFilters(data, hasValues, filters) {
					if (data === '0' || !hasValues) {
						return;
					}
					if (!filters || !Object.keys(filters).length) {
						return;
					}

					const filterWidgetId = Object.keys(filters)[0];
					const container = $('.elementor-widget-filter-widget[data-id="' + filterWidgetId + '"]');
					if (!container.length) return;
					const $incoming = $(filters[filterWidgetId]);
					// Update each non-numeric / non-range flex-wrapper.
					container.find('.flex-wrapper').each(function () {
						const $current = $(this);
						// Skip numeric / visual range.
						if ($current.find('.bpfwe-visual-range-wrapper').length) return;
						if ($current.hasClass('bpfwe-skip-update')) return;
						const className = $current.attr('class');
						if (!className) return;
						const selector = '.flex-wrapper.' + className.trim().replace(/\s+/g, '.');
						const $replacement = $incoming.find(selector);
						if (!$replacement.length) return;

						// Numeric wrapper replacement.
						if ($current.find('.bpfwe-numeric-wrapper').length) {
						  const $currentMin = $current.find('input:not(.input-val)[name^="min_"]');
						  const $currentMax = $current.find('input:not(.input-val)[name^="max_"]');
						  const $replMin = $replacement.find('input:not(.input-val)[name^="min_"]');
						  const $replMax = $replacement.find('input:not(.input-val)[name^="max_"]');
						  if ($currentMin.length && $replMin.length) {
							//$currentMin.attr('min', $replMin.attr('min'));
							$currentMin.attr('max', $replMin.attr('max'));
							$currentMin.attr('value', $replMin.attr('value'));
							$currentMin.attr('data-base-value', $replMin.attr('data-base-value'));
							$currentMin.val($replMin.val());
						  }
						  if ($currentMax.length && $replMax.length) {
							$currentMax.attr('min', $replMax.attr('min'));
							//$currentMax.attr('max', $replMax.attr('max'));
							$currentMax.attr('value', $replMax.attr('value'));
							$currentMax.attr('data-base-value', $replMax.attr('data-base-value'));
							$currentMax.val($replMax.val());
						  }
						  return;
						}

						// Reset disabled state.
						$current.find('.bpfwe-option-disabled').removeClass('bpfwe-option-disabled');
						// Checkboxes & radios.
						$current.find('input[type="checkbox"], input[type="radio"]').each(function () {
							const $input = $(this);
							const value = $input.val();
							const name = $input.attr('name');
							if (value === undefined) return;
							let matchSelector = 'input[value="' + CSS.escape(value) + '"]';
							if (name) {
								matchSelector = 'input[name="' + CSS.escape(name) + '"]' + matchSelector;
							}
							const $incomingInput = $replacement.find(matchSelector);
							const $currentLabel = $input.closest('label');
							const $currentCount = $currentLabel.find('.count');
							if (!$incomingInput.length) {
								$currentLabel.addClass('bpfwe-option-disabled');
								return;
							}
							$currentLabel.removeClass('bpfwe-option-disabled');
							const $incomingCount = $incomingInput.closest('label').find('.count');
							if ($incomingCount.length && $currentCount.length) {
								$currentCount.text($incomingCount.text());
							}
						});
						// Native selects.
						$current.find('select').each(function () {
							const $select = $(this);
							const name = $select.attr('name');
							if (!name) return;
							const $incomingSelect = $replacement.find(
								'select[name="' + CSS.escape(name) + '"]'
							);
							if (!$incomingSelect.length) return;
							// Sync options.
							$select.find('option').each(function () {
								const $option = $(this);
								const value = $option.val();
								// Skip placeholder.
								if ($option.prop('disabled') && $option.index() === 0) return;
								const $incomingOption = $incomingSelect.find(
									'option[value="' + CSS.escape(value) + '"]'
								);
								if (!$incomingOption.length) {
									$option.prop('disabled', true).addClass('bpfwe-option-disabled');
									return;
								}
								$option.prop('disabled', false).removeClass('bpfwe-option-disabled').html($incomingOption.html());
							});
							// Clean invalid selections.
							let currentValue = $select.val() || [];
							if (!Array.isArray(currentValue)) currentValue = [currentValue];
							currentValue = currentValue.filter(function (val) {
								if (!val) return false;
								const $opt = $select.find(
									'option[value="' + CSS.escape(val) + '"]'
								);
								return $opt.length && !$opt.prop('disabled');
							});
							$select.val(currentValue.length ? currentValue : '');
							// Sync Select2 UI if present.
							if ($select.hasClass('select2-hidden-accessible')) {
								const config = $select.data('select2').options.options;
								$select.select2('destroy');
								$select.select2(config);
								// Ensure plus symbol for multi-select if no values.
								if ($select.prop('multiple')) {
									const $rendered = $select.closest('.bpfwe-multi-select2').find('.select2-selection__rendered');
									$rendered.find('.select2-selection__e-plus-button').remove();
									if (($select.val() || []).length === 0) {
										$rendered.prepend(
											'<span class="select2-selection__choice select2-selection__e-plus-button">+</span>'
										);
									}
								}
							}
						});
					});
					// Post-update single-select faceting.
					const activeWrappers = [];
					container.find('.flex-wrapper').each(function () {
						const $wrapper = $(this);
						if ($wrapper.find('.bpfwe-numeric-wrapper, .bpfwe-visual-range-wrapper').length) {
							return;
						}
						let hasValue = false;
						let singleOnly = true;
						$wrapper.find('input, select').each(function () {
							const $el = $(this);
							if ($el.is('input[type="checkbox"]') && $el.prop('checked')) {
								hasValue = true;
								singleOnly = false;
							}
							if ($el.is('select[multiple]') && ($el.val() || []).length) {
								hasValue = true;
								singleOnly = false;
							}
							if ($el.is('input[type="radio"]') && $el.prop('checked')) {
								hasValue = true;
							}
							if ($el.is('select:not([multiple])') && $el.val()) {
								hasValue = true;
							}
						});
						if (hasValue) {
							activeWrappers.push({
								wrapper: $wrapper,
								singleOnly: singleOnly
							});
						}
					});
					if (activeWrappers.length === 1 && activeWrappers[0].singleOnly) {
						const $wrapper = activeWrappers[0].wrapper;
						$wrapper.find('label').removeClass('bpfwe-option-disabled');
						$wrapper.find('option.bpfwe-option-disabled').removeClass('bpfwe-option-disabled').prop('disabled', false);
						$wrapper.find('.count').each(function () {
							const reset = $(this).attr('data-reset');
							if (reset !== undefined) {
								$(this).text(reset);
							}
						});
					}
				}

				function bpfweResetLinkedWidgets(resetWidgetID, $mode="partial") {
					var $resetWidget = $('.elementor-widget-filter-widget[data-id="' + resetWidgetID + '"]');
					var $targetPostWidget = $('div[data-filters-list*="' + resetWidgetID + '"]');
					var localWidgetID = $targetPostWidget.data('id');

					if (!localWidgetID || !$targetPostWidget.length) {
						return;
					}

					var filtersList = $targetPostWidget.data('filters-list') ? $targetPostWidget.data('filters-list').split(',') : [];

					filtersList.forEach(function (widgetId) {
						var $filterWidget = $('.elementor-widget-filter-widget[data-id="' + widgetId + '"]');

						if ($filterWidget.length) {
							// Checkboxes / radios.
							$filterWidget.find('input:checked').prop('checked', false);

							// Native selects.
							$filterWidget.find('select').each(function () {
								const $select = $(this);

								$select.find('option').each(function () {
									$(this).prop('disabled', false).removeClass('bpfwe-option-disabled');
								});

								if ($select.hasClass('select2-hidden-accessible')) {
									if ($select.prop('multiple')) {
										$select.val([]);
									} else {
										$select.val(null);
									}

									// Visually refresh Select2 only
									const config = $select.data('select2').options.options;
									$select.select2('destroy');
									$select.select2(config);

									// Restore "+" symbol for multi-selects
									if ($select.prop('multiple')) {
										const $parent = $select.closest('.bpfwe-multi-select2');
										const $rendered = $parent.find('.select2-selection__rendered');

										$rendered.find('.select2-selection__e-plus-button').remove();

										const currentVal = $select.val();
										if (!Array.isArray(currentVal) || currentVal.length === 0) {
											$rendered.prepend(
												'<span class="select2-selection__choice select2-selection__e-plus-button">+</span>'
											);
										}
									}

									return;
								}

								const $firstOption = $select.find('option:first');
								if ($firstOption.length) {
									$select.val($firstOption.val());
								}
							});

							// Numeric fields (fixed and free-range).
							$filterWidget.find('.bpfwe-numeric-wrapper').each(function () {
								const $wrapper = $(this);

								// Fixed-range inputs
								$wrapper.find('input:not(.input-val)').each(function () {
									const initialVal = $(this).data('base-value');
									$(this).val(initialVal);
								});

								// Free-range inputs
								$wrapper.find('input.input-val').each(function () {
									$(this).val(''); // clear free-range values
								});

								// Remove faceted range snapshot
								$wrapper.removeAttr('data-faceted-range');
							});

							// Text inputs.
							$filterWidget.find('input.input-text').val('');
							
							// Restore facets.
							$filterWidget.find('label.bpfwe-option-disabled').removeClass('bpfwe-option-disabled');
							$filterWidget.find('.count').each(function () {
								const $count = $(this);
								const reset = $count.attr('data-reset');
								if (reset !== undefined && reset !== '') {
									$count.text(reset);
								}
							});

							$filterWidget.find('.bpfwe-numeric-wrapper').each(function () {
								$(this).removeAttr('data-faceted-range');
								const $inputs = $(this).find('input:not(.input-val)');
								if ($inputs.length < 2) return;

								const $min = $inputs.eq(0);
								const $max = $inputs.eq(1);

								const minVal  = $min.data('base-min');
								const maxVal  = $max.data('base-max');
								const minBound = $min.data('base-min')  || minVal;
								const maxBound = $max.data('base-max')  || maxVal;

								// Restore values
								if (minVal !== undefined) $min.val(minVal).attr('value', minVal).attr('data-base-value', minVal);
								if (maxVal !== undefined) $max.val(maxVal).attr('value', maxVal).attr('data-base-value', maxVal);

								// Restore bounds to both
								if (minBound !== undefined) {
									$min.attr('min', minBound);
									$max.attr('min', minBound);
								}
								if (maxBound !== undefined) {
									$min.attr('max', maxBound);
									$max.attr('max', maxBound);
								}
							});
						}

						// Sorting widget.
						var $sortingWidget = $('.elementor-widget-sorting-widget[data-id="' + widgetId + '"]');
						if ($sortingWidget.length) {
							$sortingWidget.find('form.form-order-by select').prop('selectedIndex', 0);
						}

						// Search widget.
						var $searchWidget = $('.elementor-widget-search-bar-widget[data-id="' + widgetId + '"]');
						if ($searchWidget.length) {
							$searchWidget.find('form.search-post input[name="s"]').val('');
						}
					});

					$targetPostWidget.addClass('filter-initialized');
					$targetPostWidget.data('current-page', 1);

					if($mode === "full") {
						getFormValues(resetWidgetID);
					}
					
					$targetPostWidget.removeClass('filter-active');
				}
			},
		});

		if (!elementorFrontend.isEditMode()) {
			elementorFrontend.elementsHandler.attachHandler(dynamicHandler, FilterWidgetHandler);
		} else {
			elementorFrontend.elementsHandler.attachHandler('filter-widget', FilterWidgetHandler);
		}
	});
})(jQuery);