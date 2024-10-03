jQuery(document).ready(function($) {
    // Create the style element once
    var style = document.createElement('style');
    document.head.appendChild(style);

    // Function to update the display properties based on the selected post_content values and widget ID
    function updateDisplay(widgetId) {
        // Reset style.innerHTML before updating
        style.innerHTML = '';

        // Hide all controls initially
        for (var control in controls) {
            style.innerHTML += '.widget-' + widgetId + ' .elementor-control-' + controls[control] + '{display:none!important;}';
        }

        // Show the controls corresponding to the selected post_content values
        $('select[data-setting="post_content"][data-widget-id="' + widgetId + '"]').each(function () {
            var selectedValue = this.value;
            if (controls[selectedValue]) {
                style.innerHTML += '.widget-' + widgetId + ' .elementor-control-' + controls[selectedValue] + '{display:initial!important;}';
            }
        });

        // Save the style.innerHTML as a cookie with widget-specific key
        document.cookie = "styleCookie_" + widgetId + "=" + encodeURIComponent(style.innerHTML);
    }

    // Apply the style on page load for each widget
    $('[data-id*="post-widget"]').each(function () {
        var widgetId = $(this).attr('data-id');
        var savedStyle = getCookie("styleCookie_" + widgetId);
        if (savedStyle) {
            style.innerHTML = decodeURIComponent(savedStyle);
        }
    });

    // Hook to update the display properties when the post_content changes for each widget
    elementor.hooks.addAction('panel/open_editor/widget/post-widget', function (panel, model, view) {
        const $this = panel.$el;
        var widgetId = $this.data('id');
        $this.on('change', 'select[data-setting="post_content"]', function () {
            updateDisplay(widgetId);
        });
    });

    // Function to get the value of a cookie by name
    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) return match[2];
    }
});
