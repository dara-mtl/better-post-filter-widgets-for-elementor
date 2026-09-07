jQuery(window).on('elementor:init', function () {

	const elementsMap = {
		'post-pin'            : 'show-bookmark',
		'post-title'          : 'show-title',
		'post-taxonomy'       : 'show-taxonomy',
		'post-content'        : 'show-content',
		'post-excerpt'        : 'show-content',
		'post-custom-field'   : 'show-custom-field',
		'post-read-more'      : 'show-read-more',
		'product-add-to-cart' : 'show-add-to-cart',
		'post-meta'           : 'show-meta',
		'post-html'           : 'show-html',
		'edit-options'        : 'show-edit',
		'product-price'       : 'show-price',
		'product-rating'      : 'show-rating',
		'product-buy-now'     : 'show-buy',
		'product-badge'       : 'show-badge',
	};

	function updateControlVisibility($panel, $widget) {
		$panel.removeClass(() => Object.values(elementsMap).join(' '));

		let matched = false;
		for (const [sel, cls] of Object.entries(elementsMap)) {
			if ($widget.find('.' + sel).length) {
				$panel.addClass(cls);
				matched = true;
			}
		}
		if (!matched) $panel.addClass(Object.values(elementsMap).join(' '));
	}

	elementor.hooks.addAction('panel/open_editor/widget/post-widget', (panel, model, view) => {
		const $panel   = jQuery(panel.$el);
		const $widget  = view.$el.closest('.elementor-widget-post-widget');

		updateControlVisibility($panel, $widget);

		const observer = new MutationObserver(() => updateControlVisibility($panel, $widget));
		observer.observe($widget[0], { childList: true, subtree: true });
		const widgetID = model.id;
        
        // Define all shortcode fields here (Control ID → Shortcode template)
        const shortcodeFields = {
            'feed_filter_buttons_description' : `[feed_filters id="${widgetID}"]`,
            'feed_anchor_buttons_description' : `[feed_anchor_filters id="${widgetID}"]`,
            // 'another_shortcode_control'    : `[my_shortcode id="${widgetID}"]`,
        };

        Object.entries(shortcodeFields).forEach(([controlId, shortcode]) => {
            const $input = $panel.find(`.elementor-control-${controlId} input`);
            
            if ($input.length) {
                $input.val(shortcode).attr('readonly', true);

                $input.off('click.bpfwe-shortcode').on('click.bpfwe-shortcode', function () {
                    this.select();
                    copyToClipboard(shortcode);

                    elementor.notifications.showToast({
                        message: "Copied to clipboard!",
                        type: "success"
                    });
                });
            }
        });
	});

	function initSelect2($row) {
		const $select = $row.find('select[data-setting="meta_value_relational"]');
		if (!$select.length || $select.data('select2-initialized')) return;

		const $hidden   = $row.closest('.elementor-repeater-fields').find('input[data-setting="meta_value_relational_raw"]').first();
		const postType  = $row.find('input[data-setting="filter_post_type"]').val();

		let saved = [];
		try { saved = $hidden.val() ? JSON.parse($hidden.val()) : []; }
		catch (e) { console.warn('Invalid JSON in meta_value_relational_raw', $hidden.val()); }

		$select.select2({
			allowClear: true,
			minimumInputLength: 2,
			cache: true,
			ajax: {
				url: ajax_var.rest_url + 'bpfwe/v1/search-related-items',
				dataType: 'json',
				delay: 250,
				data: params => ({
					post_type: postType,
					q: params.term || '',
					page: params.page || 1,
				}),
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', ajax_var.rest_nonce);
				},
				processResults: res => ({
					results: (res.success && res.data) ?
						res.data.map(i => ({ id: i.id, text: i.text })) : []
				}),
			},
		}).data('select2-initialized', true);

		if (Array.isArray(saved) && saved.length) {
			saved.forEach(v => {
				const opt = new Option(v.text || v.id, v.id, true, true);
				$select.append(opt);
			});
			$select.trigger('change');
		}

		$select.off('.syncHidden').on('change.syncHidden', () => {
			const data = $select.select2('data').map(i => ({ id: i.id, text: i.text }));
			$hidden.val(JSON.stringify(data)).trigger('input');
		});
	}

	function updateFacetVisibility($panel) {
		const isFacetted = $panel.find('input[data-setting="is_facetted"]').prop('checked');

		$panel.toggleClass('bpfwe-facet-disabled', !isFacetted);
	}

	const debounce = (fn, wait) => {
		let t;
		return (...args) => {
			clearTimeout(t);
			t = setTimeout(() => fn.apply(this, args), wait);
		};
	};

	function copyToClipboard(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).catch(() => {});
			return;
		}
		try {
			const el = document.createElement('textarea');
			el.value = text;
			el.setAttribute('readonly', '');
			el.style.position = 'absolute';
			el.style.left = '-9999px';
			document.body.appendChild(el);
			el.select();
			document.execCommand('copy');
			document.body.removeChild(el);
		} catch (e) {}
	}

	// Stamp the read-only shortcode / Filter ID fields with a value derived
	// strictly from THIS widget's model id. Runs on panel open and again on
	// every panel re-render so a stale value can never survive.
	function stampFilterIdFields($panel, widgetID) {
		if (!widgetID) return;

		const idFields = {
			selected_terms_shortcode : `[filter_terms id="${widgetID}"]`,
			selected_count_shortcode : `[filter_count id="${widgetID}"]`,
			quick_deselect_shortcode : `[filter_tags id="${widgetID}"]`,
			mobile_mode_shortcode    : `[filter_mobile_view id="${widgetID}"]`,
			filter_id                : `filter-${widgetID}`,
		};

		Object.entries(idFields).forEach(([controlId, value]) => {
			const $input = $panel.find(`.elementor-control-${controlId} input`);
			if (!$input.length) return;

			if ($input.val() !== value) {
				$input.val(value);
			}
			$input.attr('readonly', true);

			$input.off('click.bpfweId').on('click.bpfweId', function () {
				this.select();
				copyToClipboard(value);
				elementor.notifications.showToast({ message: 'Copied to clipboard!', type: 'success' });
			});
		});
	}

	elementor.hooks.addAction('panel/open_editor/widget/filter-widget', (panel, model) => {
		const $panel = jQuery(panel.$el);
		const widgetID = model && model.id ? model.id : null;
		$panel.find('.elementor-repeater-fields').each((_, el) => initSelect2(jQuery(el)));

		stampFilterIdFields($panel, widgetID);
		updateFacetVisibility($panel);

		$panel.off('change.bpfweFacet').on('change.bpfweFacet', 'input[data-setting="is_facetted"]', function () {
			updateFacetVisibility($panel);
		});

		const observer = new MutationObserver(debounce(mutations => {
			stampFilterIdFields($panel, widgetID);
			bpfweFillTargetWidgetOptions($panel, model);
			mutations.forEach(m => {
				if (!m.addedNodes.length) return;
				jQuery(m.addedNodes).find('.elementor-repeater-fields').addBack('.elementor-repeater-fields').each((_, el) => initSelect2(jQuery(el)));
			});
		}, 200));

		observer.observe($panel[0], { childList: true, subtree: true });

		$panel.data('bpfwe-observer', observer);
	});

	elementor.hooks.addAction('panel/close_editor/widget/filter-widget', panel => {
		const observer = jQuery(panel.$el).data('bpfwe-observer');
		if (observer) observer.disconnect();
	});

	/* ------------------------------------------------------------------
	 * Post widget picker.
	 *
	 * target_widget is a real Elementor SELECT control, so Elementor owns
	 * saving it and the conditional display that hides it against the manual
	 * target_selector field. Its options cannot be registered in PHP because
	 * they depend on what is on the page, so they are filled in here from the
	 * preview document each time the panel opens.
	 * --------------------------------------------------------------- */

	const BPFWE_POST_WIDGET_CLASSES = Array.isArray(window.ajax_var && window.ajax_var.targetWidgets) ?
		window.ajax_var.targetWidgets : [
			'elementor-widget-post-widget',
			'elementor-widget-loop-grid',
			'elementor-widget-loop-carousel',
			'elementor-widget-posts'
		];

	const BPFWE_POST_WIDGETS = BPFWE_POST_WIDGET_CLASSES.map(c => '.' + c).join(', ');

	const BPFWE_WIDGET_LABELS = {
		'elementor-widget-post-widget'   : 'Post Widget',
		'elementor-widget-loop-grid'     : 'Loop Grid',
		'elementor-widget-loop-carousel' : 'Loop Carousel',
		'elementor-widget-posts'         : 'Posts',
	};

	function bpfwePreviewDoc() {
		try {
			return elementor.$preview[0].contentDocument || null;
		} catch (e) {
			return null;
		}
	}

	// Elementor data-id is unique per page, unlike a widget class, which would
	// match every widget of that type.
	function bpfweUniqueSelector(el) {
		const widgetId = el.getAttribute('data-id');
		if (widgetId) return '[data-id="' + widgetId + '"]';
		return el.id ? '#' + el.id : '';
	}

	// Turns 'jet-listing-grid' into 'Jet Listing Grid' so widgets added through
	// the bpfwe/target_widget_classes filter still read as names, not classes.
	function bpfweDeriveLabel(widgetClass) {
		return widgetClass
			.replace(/^elementor-widget-/, '')
			.replace(/[-_]+/g, ' ')
			.replace(/\b\w/g, m => m.toUpperCase())
			.trim();
	}

	function bpfweWidgetLabel(el) {
		let label = '';

		for (const [cls, name] of Object.entries(BPFWE_WIDGET_LABELS)) {
			if (el.classList.contains(cls)) {
				label = name;
				break;
			}
		}

		if (!label) {
			const matched = BPFWE_POST_WIDGET_CLASSES.find(c => el.classList.contains(c));
			label = matched ? bpfweDeriveLabel(matched) : 'Post widget';
		}

		const widgetId = el.getAttribute('data-id');
		return widgetId ? label + ' (' + widgetId + ')' : label;
	}

	// A value saved by an earlier build can be unparseable as a selector. Treat it
	// as unset so the panel falls back to the placeholder and invites a fresh pick,
	// rather than re-offering a broken option. A valid selector that simply matches
	// nothing in this document stays usable: the target may live elsewhere.
	function bpfweIsUsableSelector(value) {
		if (!value) return false;
		try {
			document.querySelector(value);
			return true;
		} catch (e) {
			return false;
		}
	}

	// The select can only hold a value whose option exists, and the options are
	// added here rather than registered in PHP. So on the first render the DOM
	// reads empty while the setting is set: take the value from the model.
	function bpfweSavedTarget(model) {
		try {
			if (model && typeof model.getSetting === 'function') {
				return model.getSetting('target_widget') || '';
			}

			const settings = (model && typeof model.get === 'function') ? model.get('settings') : null;
			if (settings && typeof settings.get === 'function') {
				return settings.get('target_widget') || '';
			}
		} catch (e) {}

		return '';
	}

	function bpfweFillTargetWidgetOptions($panel, model) {
		const $select = $panel.find('select[data-setting="target_widget"]');
		if (!$select.length) return;

		const doc = bpfwePreviewDoc();
		const widgets = (doc && BPFWE_POST_WIDGETS) ?
			Array.prototype.slice.call(doc.querySelectorAll(BPFWE_POST_WIDGETS)) : [];

		// Keep the saved value even when its widget is not in this document, so
		// opening the panel never quietly drops a target set somewhere else.
		const select = $select[0];
		const raw = bpfweSavedTarget(model) || select.value || '';
		const current = bpfweIsUsableSelector(raw) ? raw : '';

		const entries = [ [ '', $select.data('placeholder') || 'Select a post widget' ] ];
		let hasCurrent = false;

		widgets.forEach(function (el) {
			const selector = bpfweUniqueSelector(el);
			if (!selector) return;
			if (selector === current) hasCurrent = true;
			entries.push([ selector, bpfweWidgetLabel(el) ]);
		});

		if (current !== '' && !hasCurrent) {
			entries.push([ current, current ]);
		}

		// Nothing to do when the options and the shown value are both current.
		// Elementor re-renders controls freely, so the value is checked separately:
		// the options can survive a re-render while the selection does not.
		const signature = JSON.stringify(entries);
		const optionsCurrent = $select.data('bpfwe-options') === signature;

		if (optionsCurrent && select.value === current) return;

		if (!optionsCurrent) {
			// Built through the DOM rather than an HTML string: a selector like
			// [data-id="abc"] carries double quotes, which would terminate a value
			// attribute early and store a truncated, unparseable selector.
			select.innerHTML = '';
			entries.forEach(function (entry) {
				const option = document.createElement('option');
				option.value = entry[0];
				option.textContent = entry[1];
				select.appendChild(option);
			});
			$select.data('bpfwe-options', signature);
		}

		if (select.value !== current) {
			select.value = current;
		}
	}

	['filter-widget', 'search-bar-widget', 'sorting-widget'].forEach(function (widgetType) {
		elementor.hooks.addAction('panel/open_editor/widget/' + widgetType, function (panel, model) {
			const $panel = jQuery(panel.$el);

			bpfweFillTargetWidgetOptions($panel, model);

			// Rescan the preview whenever the list is opened, so widgets added
			// since the panel opened show up.
			$panel.off('mousedown.bpfweTarget').on('mousedown.bpfweTarget', 'select[data-setting="target_widget"]', function () {
				bpfweFillTargetWidgetOptions($panel, model);
			});

			// The filter panel already re-runs this from its own observer. The other
			// two need one so the selection is restored when Elementor re-renders
			// the control, which drops any option this script added.
			if ('filter-widget' === widgetType) return;

			const observer = new MutationObserver(debounce(function () {
				bpfweFillTargetWidgetOptions($panel, model);
			}, 200));

			observer.observe($panel[0], { childList: true, subtree: true });
			$panel.data('bpfwe-target-observer', observer);
		});

		elementor.hooks.addAction('panel/close_editor/widget/' + widgetType, function (panel) {
			const observer = jQuery(panel.$el).data('bpfwe-target-observer');
			if (observer) observer.disconnect();
		});
	});

});
