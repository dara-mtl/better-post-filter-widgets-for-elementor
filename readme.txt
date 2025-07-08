=== Better Post & Filter Widgets for Elementor ===
Contributors: nomade123456
Donate link: https://wpsmartwidgets.com/donate/
Tags: elementor, woocommerce, product filter, post filter, ajax filter
Stable tag: 1.4.91
Tested up to: 6.8
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Effortlessly filter any post type with Better Post & Filter Widgets for Elementor. Compatible with ACF, WooCommerce, WPML, and various other plugins.

== Description ==

Exclusively designed for Elementor, this versatile filtering plugin lets you take full control of your WordPress and WooCommerce content effortlessly. Filter any post type based on any criteria, seamlessly integrating with Elementor.

### Filter Widget Key Features:
- Compatible with Elementor Pro post widget, ACF, WooCommerce and most translation plugins.
- Versatile filtering for any post type.
- Customizable filter items list with easy re-ordering options.
- Filter anything using taxonomies, custom field/ACF, and numeric fields.
- Keyword search support for custom field/ACF.
- Various filter types catered to diverse use-cases: checkboxes, radio buttons, label list, dropdown, select2 (single & multiple select).
- Fine-tune the filter with the choice of relation (AND or OR) between terms.
- User-friendly more/less and toggle options, ideal for managing extensive lists.
- Select your preferred filtering experience by choosing between 'Auto-submission' and 'Submit button' modes.

[Filter Widget Demo](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/filter-post-widgets-demo/) – See the filter in action. 

### Post Widget Key Features:
- Query multiple post types at once.
- Do more with less – display posts, users, and taxonomies using a single widget.
- Effortlessly switch between a dynamic carousel or grid layout at different breakpoints.
- Make the most of Swiper API with advanced features like carousel synching, parallax effects, and more.
- Multiple layout options, including classic, on the side, banner, template grid (loop grid), and custom HTML.
- Possibility to create your own custom skin, with any dynamic tags and Elementor widgets.
- Flexible post content options: title, content, excerpt, custom field/ACF, taxonomy, HTML, post meta, read more, bookmark, edit options, product price, product rating, buy now, and product badge.
- Flexible query system with AJAX pagination.
- Customize widget content and style like native Elementor widgets.

[Post Slider/Carousel Demo](https://wpsmartwidgets.com/doc/post-slider-demo-2/) – Check out the post widget possibilities.

### Dynamic Tags:
- Exclusive Repeater Field Tag: Unlock the ability to directly output ACF repeater fields in the Elementor frontend, with the flexibility to wrap each part in different HTML tags.
- Includes a series of dynamic tags, allowing users to fully utilize template grids with the free version.

### Create Loop Grids for Free:
Unlock the power of dynamic loops in Elementor, without needing the Pro version! Effortlessly showcase custom content in a loop format and experience the full potential of Elementor at no extra cost.

### Crafted for Seamless Elementor Integration:
- Blends seamlessly with Elementor's native interface, leveraging its resources for a consistent and unobtrusive user experience.
- No disruptive branding — Enjoy a clean, streamlined interface without unnecessary distractions.
- Minimalist approach, ensuring your focus remains on creating content without unnecessary notifications or distractions.
- Lightweight design, utilizing Elementor's resources to minimize external dependencies.

### Developer-Friendly:
Tailor the filter to your needs with ease with the help of a dedicated hook.

== Frequently Asked Questions ==

= How do I use the Filter Widget? =

If you're having trouble getting the Filter Widget to work properly, follow these steps to ensure it's set up correctly:
1. Ensure that you have set a unique ID or class for the target post widget. Without it, the widget will not know where to apply the filters.
2. Verify that you have selected the correct post type to filter.

= The post meta are not displaying =

The list of available meta fields will only display for the selected post type.

For more details, check out this [article](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/filter-widget/#filter-configuration).

= Why isn’t AJAX working on my site? =

AJAX should work automatically on most WordPress themes built according to standards. However, if AJAX isn't working, here are a few things to check:

1. **Check if WordPress logging is enabled.** Sometimes, when WordPress logging is enabled, it can interfere with AJAX functionality. Try disabling logging to see if that resolves the issue.
2. **Ensure your theme supports AJAX.** Some themes, especially older or custom-built ones, might not properly support AJAX. Make sure your theme is up to date and follows WordPress best practices.
3. **Check for JavaScript errors.** JavaScript errors on the page can prevent AJAX from working. Open your browser’s developer tools and check the console for any errors that could be affecting AJAX functionality.
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

= 1.5.0 – 2025-07-08 =

* New: Added toggle mode to Filter widget group titles.
* New: Introduced "Select All" option per taxonomy/meta group, configurable under Advanced tab for each row.
* New: Added `.bpfwe-selected-count` class to show number of selected terms.
* New: Added style controls for the toggle feature under Style > Group Title.
* New: Rewrote AJAX pagination logic in the Post widget to use Elementor's native rendering method, improving reliability and performance.
* New: Added "Display Mode" control to the Repeater Dynamic Tag for ul/ol, Tabs, and Toggle layouts, allows toggling between flat list (field-based) and grouped list (row-based) outputs.
* Tweak: Renamed "Group Label" to "Group Title" for clarity and consistency.
* Fix: Added missing HTML markup for meta-based filters to ensure selected terms are properly displayed via the `.bpfwe-selected-terms` class.
* Tweak: Reworked OR group logic in `tax_query` to avoid nested arrays and follow parent logic more clearly, each OR filter is now added separately.
* Tweak: Search bar border radius is now responsive.
* Compatibility: Tested up to Elementor 3.30.X.

= 1.4.1 – 2025-06-25 =

* Fix: Fixed Elementor widgets' pagination issue after filtering.
* Fix: Fixed layout issue affecting the post carousel pagination.
* Fix: Added missing **Term Order** control to the Filter widget.
* Tweak: Made **Row Span** and **Column Span** controls responsive in the Template Grid layout of the Post widget.

= 1.4.0 – 2025-06-09 =

* New: Introduced **Default Filters** feature: backend users can now predefine fixed filter selections under Content > Default Filters. This enables pre-filtered content views without user interaction.
* Fix: Resolved a layout issue in the Post widget when using the **Template Grid** layout with **User** or **Taxonomy** queries.
* Fix: Corrected the performance sanitization rules, which were previously using a default logic, leading to inconsistent filter behavior in some cases.

= 1.3.3 – 2025-05-24 =

* New: Added styling controls for checkbox and radio button labels in the Filter widget, allowing greater design flexibility.
* New: Introduced `.bpfwe-selected-terms` class to display currently selected filter terms inside any widget.
* Fix: Resolved a crash when using the Custom Field dynamic tag with array-type fields in combination with the wpautop() function.
* Fix: Prevented duplicated filter forms from interfering with each other's queries.
* Compatibility: Confirmed compatibility with Elementor version 3.29.0

= 1.3.2 – 2025-05-10 =

* Fix: Resolved issue where filters failed after the first AJAX request for custom post types registered via ACF.
* Fix: Corrected index mismatch between synchronized sliders in the Post widget.
* Fix: Prevented duplicated filter forms from interfering with each other's queries.
* Fix: Addressed inconsistent pagination when excluding terms from taxonomy queries.

= 1.3.1 – 2025-05-05 =

* New: Added Vertical Post Slider option to the Post widget. Users can now toggle between horizontal and vertical layouts.
* New: Deselectable Radio Buttons: clicking the same radio option twice will now deselect it.
* Tweak: Improved the filter's reset behavior.

For more information, see [Changelog](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/changelog/).

== Upgrade Notice ==

= 1.5.0 =

This update introduces improved pagination, new filter toggle and select options, and enhanced Repeater tag display modes.
