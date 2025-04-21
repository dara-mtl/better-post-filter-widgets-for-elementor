jQuery(document).ready(function ($) {
    // Initialize color pickers on specified fields.
    function initializeColorPickers() {
        var $colorFields = $('.bpfwe-swatches-color');
        if ($colorFields.length && typeof $.wp.wpColorPicker === 'function') {
            $colorFields.wpColorPicker();
        }
    }

    // Open media uploader and set selected image URL.
    function openMediaUploader($button) {
        var $input = $button.siblings('.bpfwe-swatches-image');
        if (typeof wp.media === 'undefined') {
            return;
        }

        var frame = wp.media({
            title: 'Select Swatch Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.url);
        });

        frame.open();
    }

    // Handle swatch type change and render new fields.
    function handleSwatchTypeChange($select) {
        var swatchType = $select.val();
        var $formField = $select.closest('.form-field, tr.form-field');
        var $groupSeparator = $formField.next('.bpfwe-group-separator-wrap');
        var $existingFields = $groupSeparator.nextAll('.bpfwe-swatch-field, .form-field:has([name^="bpfwe_swatches_"])');
        $existingFields.remove();

        var isEditForm = $formField.is('tr');
        var html = '';
        var data = window.bpfweSwatchesData || {};
        var safeColor = $('<div/>').text(data.color || '#000000').html();
        var safeImage = $('<div/>').text(data.image || '').html();
        var safeButtonText = $('<div/>').text(data.buttonText || '').html();

        if (swatchType === 'color') {
            html = isEditForm ?
                '<tr class="form-field bpfwe-swatch-field"><th scope="row"><label for="bpfwe_swatches_color">Swatch Color</label></th><td><input type="text" name="bpfwe_swatches_color" value="' + safeColor + '" class="bpfwe-swatches-color" /></td></tr>' :
                '<div class="form-field bpfwe-swatch-field"><label for="bpfwe_swatches_color">Swatch Color</label><input type="text" name="bpfwe_swatches_color" value="' + safeColor + '" class="bpfwe-swatches-color" /></div>';
        } else if (swatchType === 'image') {
            html = isEditForm ?
                '<tr class="form-field bpfwe-swatch-field"><th scope="row"><label for="bpfwe_swatches_image">Swatch Image</label></th><td><input type="text" style="margin-bottom: 0.9rem;" name="bpfwe_swatches_image" value="' + safeImage + '" class="bpfwe-swatches-image" /><button type="button" class="button bpfwe-swatches-upload-button">Upload/Add image</button></td></tr>' :
                '<div class="form-field bpfwe-swatch-field"><label for="bpfwe_swatches_image">Swatch Image</label><input type="text" style="margin-bottom: 0.9rem;" name="bpfwe_swatches_image" value="' + safeImage + '" class="bpfwe-swatches-image" /><button type="button" class="button bpfwe-swatches-upload-button">Upload/Add image</button></div>';
        } else if (swatchType === 'button') {
            html = isEditForm ?
                '<tr class="form-field bpfwe-swatch-field"><th scope="row"><label for="bpfwe_swatches_button_text">Swatch Button Text</label></th><td><input type="text" name="bpfwe_swatches_button_text" value="' + safeButtonText + '" /></td></tr>' :
                '<div class="form-field bpfwe-swatch-field"><label for="bpfwe_swatches_button_text">Swatch Button Text</label><input type="text" name="bpfwe_swatches_button_text" value="' + safeButtonText + '" /></div>';
        }

        if (html) {
            $groupSeparator.after(html);
            if (swatchType === 'color') {
                initializeColorPickers();
            }
        }

        // Toggle group separator visibility
        toggleGroupSeparator(swatchType);
    }

    // Toggle group separator field visibility
    function toggleGroupSeparator(swatchType) {
        if (swatchType === 'none') {
            $('.bpfwe-group-separator-wrap').hide();
        } else {
            $('.bpfwe-group-separator-wrap').show();
        }
    }

    // Event listeners
    $('form').on('click', '.bpfwe-swatches-upload-button', function (e) {
        e.preventDefault();
        openMediaUploader($(this));
    });

    $('#bpfwe_swatches_type').on('change', function () {
        handleSwatchTypeChange($(this));
    });

    // Initialize on page load
    initializeColorPickers();
    var $swatchTypeSelect = $('#bpfwe_swatches_type');
    if ($swatchTypeSelect.length) {
        var initialSwatchType = $swatchTypeSelect.val();
        toggleGroupSeparator(initialSwatchType);
    }
});