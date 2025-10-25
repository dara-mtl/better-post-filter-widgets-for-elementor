jQuery(window).on('elementor:init', function () {

	const elementsMap = {
		'post-pin'          : 'show-bookmark',
		'post-title'        : 'show-title',
		'post-taxonomy'     : 'show-taxonomy',
		'post-content'      : 'show-content',
		'post-excerpt'      : 'show-content',
		'post-custom-field' : 'show-custom-field',
		'post-read-more'    : 'show-read-more',
		'post-meta'         : 'show-meta',
		'post-html'         : 'show-html',
		'edit-options'      : 'show-edit',
		'product-price'     : 'show-price',
		'product-rating'    : 'show-rating',
		'product-buy-now'   : 'show-buy',
		'product-badge'     : 'show-badge',
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
				url: ajax_var.url,
				dataType: 'json',
				delay: 250,
				data: params => ({
					action: 'bpfwe_search_related_items',
					nonce: ajax_var.nonce,
					post_type: postType,
					q: params.term || '',
					page: params.page || 1,
				}),
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

	const debounce = (fn, wait) => {
		let t;
		return (...args) => {
			clearTimeout(t);
			t = setTimeout(() => fn.apply(this, args), wait);
		};
	};

	elementor.hooks.addAction('panel/open_editor/widget/filter-widget', (panel) => {
		const $panel = jQuery(panel.$el);

		$panel.find('.elementor-repeater-fields').each((_, el) => initSelect2(jQuery(el)));

		const observer = new MutationObserver(debounce(mutations => {
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

});