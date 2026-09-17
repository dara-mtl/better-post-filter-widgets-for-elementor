=== Better Post & Filter Widgets for Elementor ===
Contributors: nomade123456
Donate link: https://wpsmartwidgets.com/donate/
Tags: elementor, woocommerce, product filter, post filter, ajax filter
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.9.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The only free pro-grade Elementor filtering system for posts, taxonomies, custom fields, ACF, WooCommerce, WPML & more. Ditch paid limits!

== Description ==

The only free Elementor plugin for unlimited pro-grade filtering of all your post content. Filter by taxonomies, custom fields, ACF, relational fields, and numeric ranges – with seamless integration, no restrictions, and full customization. Get advanced filtering features without paying for limitations.

### Filter Widget Key Features:
- Compatible with Elementor Pro post widget, ACF, WooCommerce and most translation plugins.
- True faceted filtering with real-time option availability and dynamic result counts.
- Filter any post type.
- Customizable filter items list with easy re-ordering options.
- Filter anything using taxonomies, custom fields/ACF, relational and numeric fields.
- Keyword search support for custom field/ACF.
- Various filter types catered to diverse use-cases: checkboxes, radio buttons, label list, dropdown, numeric range, select2 (single & multiple select).
- Fine-tune the filter with the choice of relation (AND or OR) between terms and parents.
- User-friendly more/less and toggle options, ideal for managing extensive lists.
- Choose how filters are applied: Auto-submission or Submit button mode.

[Filter Widget Demo](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/filter-post-widgets-demo/) – See the filter in action. 

### Post Widget Key Features:
- Query multiple post types at once.
- Do more with less – display posts, users, and taxonomies using a single widget.
- Effortlessly switch between a dynamic carousel or grid layout at different breakpoints.
- Make the most of Swiper API with advanced features such as carousel synching, parallax effects, and more.
- Multiple layout options, including classic, on the side, banner, template grid (loop grid), and custom HTML.
- Possibility to create your own loop grid, with any dynamic tags and Elementor widgets.
- Flexible post content options: title, content, excerpt, custom field/ACF, taxonomy, HTML, post meta, read more, bookmark, edit options, product price, product rating, buy now, and product badge.
- Flexible query system with AJAX pagination.
- Customize widget content and style like native Elementor widgets.
- Feed-style layouts with taxonomy-based grouping, ideal for magazine or news-style content.

[Post Slider/Carousel Demo](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/elementor-post-slider-travel-theme-demo/) – Check out the post widget possibilities.

### Create Loop Grids for Free:
Create dynamic loop grids without Elementor Pro. Design fully custom layouts using any Elementor widget and dynamic tag, while keeping full compatibility with filtering and AJAX pagination.

### Exclusive Dynamic Tags:
- Exclusive Repeater Field Tag: Unlock the ability to directly output ACF repeater fields in the Elementor frontend, with the flexibility to wrap each part in different HTML tags.
- Includes a series of dynamic tags, allowing users to fully utilize template grids with the free version.

### Crafted for Seamless Elementor Integration:
- Blends seamlessly with Elementor's native interface.
- No disruptive branding — Enjoy a clean, streamlined interface without unnecessary distractions.
- Lightweight design, utilizing Elementor's resources to minimize external dependencies.

### Developer-Friendly:
Tailor the widgets to your needs using dedicated filters and developer hooks.

= Troubleshooting =

- Make sure the Post Widget class name and the Filter Widget target match.
- Check for incompatible plugins or theme conflicts:
  1. Temporarily switch to a default WordPress theme.
  2. Deactivate all other plugins except Elementor and Better Post & Filter Widgets.
  3. Test the filter. If it works, reactivate your plugins one by one to find the one causing the conflict.
- Disable caching or optimization plugins while testing, as they can interfere with AJAX.
- Check the browser console for JavaScript errors (press F12 and look under the Console tab) and resolve any errors that appear.

== Frequently Asked Questions ==

= Connecting the Filter Widget =

To link your Filter Widget to a Post, Loop Grid, Loop Carousel, or Posts widget:

1. Open your Filter Widget in Elementor.
2. In the Post Widget Target field, select the widget you want the filter to control.
3. Save the page. The filter will now update the selected widget.

The target picker automatically detects eligible widgets on the page, so no CSS class or ID is required.

For advanced setups, you can use Custom Target Selector instead. Enter a CSS class with a `.` prefix (for example, `.results`) or an ID with a `#` prefix (for example, `#results`). Multiple selectors can also be used when needed.

= The post meta are not displaying =

The list of available meta fields will only display for the selected post type.

For more details, check out this [article](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/filter-widget/#filter-configuration).

= Why isn't AJAX working on my site? =

AJAX should work automatically on most WordPress themes built according to standards. However, if AJAX isn't working, here are a few things to check:

1. **Check if WordPress logging is enabled.** Sometimes, when WordPress logging is enabled, it can interfere with AJAX functionality. Try disabling logging to see if that resolves the issue.
2. **Ensure your theme supports AJAX.** Some themes, especially older or custom-built ones, might not properly support AJAX. Make sure your theme is up to date and follows WordPress best practices.
3. **Check for JavaScript errors.** JavaScript errors on the page can prevent AJAX from working. Open your browser's developer tools and check the console for any errors that could be affecting AJAX functionality.
4. **Conflicting plugins.** Certain plugins, especially caching or performance optimization plugins, might conflict with AJAX. Try temporarily disabling them to see if the issue persists.
5. **Check server-side restrictions.** Some servers might have restrictions that prevent AJAX requests from functioning properly. Contact your hosting provider to ensure that AJAX requests are not being blocked by security rules or firewall settings.

If none of these steps resolve the issue, feel free to reach out on the [support forum](https://wordpress.org/support/plugin/better-post-filter-widgets-for-elementor/) for further assistance.

= Does the Filter Widget work with custom post types (CPT)? =

Yes, it does out-of-the-box, but be aware of the following:
1. The post type you choose in the filter will override the post type selected in the post widget. For example, if your post widget is showing posts, but you select products in the filter, once you interact with the filter, the post widget will display products instead of posts.
2. If you want the filter to return results based on the selected meta or taxonomy instead of the post type, you can choose the "Any" option under the post type to filter.
3. Dynamic filtering can be enabled to include the current archive context in the filter results.

= Is the Filter Widget compatible with other widgets? =

The filter widget is designed to work with most widgets that use a post query, such as post or product widgets. If a widget pulls data via a post query, the filter can potentially hook into it. However, pagination is fully supported only for widgets from Better Post & Filter Widgets for Elementor and Elementor Pro. Other widgets may lack pagination or loading animations, as these features rely on specific HTML and CSS.

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team helps validate, triage and handle any security vulnerabilities. [Report a security vulnerability.](https://patchstack.com/database/vdp/eace7b8a-24fe-4a69-b0af-ee83a3c7496a)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/better-post-and-filter-widgets` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to the Elementor editor and start using the widgets.

== Docs and Support ==

Find support for this plugin in the [documentation](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/).

== Source Code ==

This plugin includes both compressed and uncompressed versions of CSS and JavaScript files and can be found under the `/assets/` directory.

== Screenshots ==

1. Overview of the widgets on a page.
2. Backend view of the Elementor edit screen, showing the filter widget options.
3. Backend view of the Elementor edit screen, showing the post widget options.

== Changelog ==

= 1.9.0 – 2026-09-17 =

* New: Post Widget Target picker. A dropdown to choose which Post, Loop Grid, Loop Carousel or Posts widget a Filter, Search or Sorting widget controls, populated from the widgets present on the page. The previous CSS-selector field remains available as "Custom Target Selector" for advanced or multi targeting.
* Tweak: Featured images now render as real responsive <img>, with srcset, width and height, and native lazy loading. This improves SEO, reduces layout shift, and allows Google Images to index the thumbnails. The Aspect Ratio control now applies through CSS.
* Fix: Relational fields displayed as a Select or Select2 now apply when filtering. Previously only the checkbox style worked, as the selected value was missing from the request.
* Fix: Full post content shown through the Post Content dynamic tag is no longer stripped of embedded media such as oEmbed iframes.
* Fix: Removed generic placeholder alt text from featured images. The image's real alt text is used, or none when the image is decorative.
* Dev: New filter `bpfwe/target_widget_classes` to control which widget classes are eligible for target auto-detection and the target picker.
* Dev: New filter `bpfwe_custom_html_image` to override the `#IMAGE#` markup in the Custom HTML skin.

= 1.8.9 – 2026-08-31 =

* Tweak: Confirmed compatibility with WordPress 7.1 and the latest Elementor releases.
* Fix: Filter query arguments no longer leak into unrelated queries rendered during the same request.
* Fix: Post types requested through the filter endpoint are now validated, so only publicly viewable types, or the type the filter widget is configured with, can be queried.
* Fix: Fixed a fatal error when the filter endpoint received a page ID that does not resolve to an Elementor document.
* Fix: Corrected the text domain on two Post Widget controls so they can be translated.

= 1.8.8 – 2026-06-24 =

* New: Added style controls for quick deselect pills and selected terms shortcodes.
* Fix: Fixed filter REST API pagination issue when used with the Elementor Pro Loop Grid widget.
* Fix: Fixed compatibility issue with Elementor Pro load more pagination when using BPFWE filtering.
* Fix: Added plugin-specific prefix to key Post Widget controls to prevent conflicts with other third-party plugins.
* Tweak: Dynamic background handler is now disabled by default. Sites that rely on dynamic background image resolution can re-enable it via the "Refresh Background Images" control or the `bpfwe_enable_background_image_resolution` filter.
* Tweak: "Scroll to top" behavior is now disabled by default in both Post and Filter widgets.

= 1.8.7 – 2026-05-29 =

* New: Added mobile mode shortcode to dynamically reposition filter widgets at specific breakpoints.
* New: Added boolean support for custom meta fields.
* New: Added numeric format support for checkboxes, radio buttons, and label lists.
* New: Added step size control for numeric ranges.
* New: Added "after" suffix field for numeric inputs to support trailing currency symbols and European formats.
* Fix: Fixed masonry layout flickering with infinite/load more pagination.
* Fix: Fixed post duplication in feed layout with infinite/load more.
* Fix: Resolved permission issues with caching plugins.
* Fix: Improved numeric sliders (decimal support, reset behavior, dynamic step calculation).
* Tweak: Converted filter utility classes (e.g. selectable pills) into shortcodes with backward compatibility.
* Tweak: Added missing dynamic CSS classes and attributes to post widget.
* Dev: Added global loop attribute filters for post rendering: bpfwe/post_wrapper_attr/loop, bpfwe/post_wrapper_inner_attr/loop, bpfwe/post_attr/loop
* Dev: Migrated plugin-wide AJAX to REST API.

For full changelog, see [Changelog](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/changelog/).

== Upgrade Notice ==

= 1.9.0 =

New: Automatically target Post, Loop Grid, Loop Carousel and Posts widgets with the new Target picker.
