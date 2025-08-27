=== Better Post & Filter Widgets for Elementor ===
Contributors: nomade123456
Donate link: https://wpsmartwidgets.com/donate/
Tags: elementor, woocommerce, product filter, post filter, ajax filter
Requires at least: 5.9
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The only free pro-grade Elementor filter for posts, taxonomies, custom fields, ACF, WooCommerce, WPML & more. Ditch paid limits!

== Description ==

The only free Elementor plugin for unlimited pro-grade filtering of all your post content. Filter by taxonomies, custom fields, ACF, meta fields, and numeric ranges – with seamless integration, no restrictions, and full customization. Get advanced filtering features without paying for limitations.

### Filter Widget Key Features:
- Compatible with Elementor Pro post widget, ACF, WooCommerce and most translation plugins.
- Filter any post type.
- Customizable filter items list with easy re-ordering options.
- Filter anything using taxonomies, custom field/ACF, and numeric fields.
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

[Post Slider/Carousel Demo](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/elementor-post-slider-travel-theme-demo/) – Check out the post widget possibilities.

### Create Loop Grids for Free:
Unlock the power of dynamic loops in Elementor, without needing the Pro version! Effortlessly showcase custom content in a loop format and experience the full potential of Elementor at no extra cost.

### Exclusive Dynamic Tags:
- Exclusive Repeater Field Tag: Unlock the ability to directly output ACF repeater fields in the Elementor frontend, with the flexibility to wrap each part in different HTML tags.
- Includes a series of dynamic tags, allowing users to fully utilize template grids with the free version.

### Crafted for Seamless Elementor Integration:
- Blends seamlessly with Elementor's native interface.
- No disruptive branding — Enjoy a clean, streamlined interface without unnecessary distractions.
- Minimalist approach, ensuring your focus remains on creating content without unnecessary notifications or distractions.
- Lightweight design, utilizing Elementor's resources to minimize external dependencies.

### Developer-Friendly:
Tailor the widget to your needs with the help of a dedicated filters.

== Frequently Asked Questions ==

= How do I use the Filter Widget? =

If you're having trouble getting the Filter Widget to work properly, follow these steps to ensure it's set up correctly:
1. Ensure that you have set a unique ID or class for the target post widget. Without it, the widget will not know where to apply the filters.
2. Verify that you have selected the correct post type to filter.

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

= 1.6.0 – 2025-08-26 =

* New: Added visual range option to transform numeric ranges into a 1–10 scale, ideal for star ratings or scoring systems.
* New: Added label formatting for custom fields.
* New: Introduced `.quick-deselect-FILTERID` class to create quick-remove "pills" that deselect associated filter terms when clicked.
* New: Added post wrapper alignment controls for post widget content.
* Dev: Added filters to extend queries and widget attributes for both the post and filter widgets. See [docs](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/filter-widget/custom-filter-for-term-queries/).
  * `bpfwe/filter_query_args/{query_id}` – Modify filter's query arguments before execution.
  * `bpfwe/get_terms_args/{query_id}` – Modify arguments passed to the filter's get_terms().
  * `bpfwe/post_wrapper_attr/{query_id}` – Add or modify attributes for the post wrapper container.
  * `bpfwe/post_wrapper_inner_attr/{query_id}` – Add or modify attributes for the inner post wrapper container.
  * `bpfwe/post_attr/{query_id}` – Add or modify attributes for each post item.
* Fix: Resolved issue where post content dynamic tag did not display after AJAX re-render.
* Fix: Masonry layout flickering on mobile devices.
* Tweak: Improved Filter widget UI.
* Tweak: More robust post and filter widgets pagination.

= 1.5.3 – 2025-08-07 =

* New: Added a "None" option to the Filter widget's term order setting.
* New: Added a Gallery Mode to the Image Custom Field dynamic tag.
* Fix: Pagination in the Post widget returned incorrect total pages when using the main query. The plugin now explicitly sets 'post_status' to 'publish' for main queries.
* Core: Refactored part of the plugin's query filtering logic to improve inner template rendering in Elementor. Query arguments are now injected using widget-specific $query_id logic, resolving conflicts and ensuring correct post context for dynamic tags and nested templates.

= 1.5.2 – 2025-07-29 =

* Tweak: Simplified the meta query logic by removing unnecessary nesting and the AND/OR group logic option. Meta queries now use a leaner structure, reflecting WordPress's actual behavior.
* Fix: Standalone Sorting widget no longer resets its value unexpectedly after AJAX interactions.
* Fix: Suppressed PHP warning triggered by the Post widget's pagination.
* Fix: Resolved background image rendering issues after AJAX pagination and filtering. To benefit from this fix, users must use the plugin's Image Custom Field dynamic tag for background images:
  * For featured images, use the meta key _thumbnail_id
  * For ACF fields or other custom meta, use the appropriate custom key.
* Tweak: Improved debug data output for the Filter widget. Display is now more accurately reflects the executed query.

= 1.5.1 – 2025-07-19 =

* New: Added a query debug option in the Filter widget's Additional Options to allow backend users visualize the query.
* Fix: Resolves cases where filters became non-functional after first interacting Elementor Pro's AJAX pagination.
* Fix: Updated internal JavaScript logic to ensure widgets inside the Post widget (e.g., off-canvas panels) are properly re-initialized after AJAX interactions. This addresses broken behavior in Loop Grid using interactive widgets.
* Tweak: Updated the "Group Label" text in the Filter widget to "Group Title" for consistency across controls.

= 1.5.0 – 2025-07-11 =

* New: Added toggle mode to Filter widget group titles.
* New: Introduced "Select All" option per taxonomy/meta group, configurable under the Advanced tab of each group.
* New: Added `.bpfwe-selected-count` class to show number of selected terms.
* New: Added style controls for the toggle feature under Style > Group Title.
* New: Rewrote AJAX pagination logic in the Post widget to use Elementor's native rendering method, improving reliability and performance. Legacy pagination will remain available for now.
* New: Added "Display Mode" control to the Repeater Dynamic Tag for ul/ol, Tabs, and Toggle layouts, allows toggling between flat list (field-based) and grouped list (row-based) outputs.
* Fix: Added missing HTML markup for meta-based filters to ensure selected terms are properly displayed via the `.bpfwe-selected-terms` class.
* Fix: Off-canvas widgets are now properly re-initialized after AJAX pagination and filtering.
* Tweak: Renamed "Group Label" to "Group Title" for clarity and consistency.
* Tweak: Reworked OR group logic in `tax_query` to avoid nested arrays and follow parent logic more closely, each OR filter is now added separately.
* Tweak: Search bar border radius is now responsive.
* Compatibility: Tested up to Elementor 3.30.X.

= 1.4.1 – 2025-06-25 =

* Fix: Fixed Elementor widgets' pagination issue after filtering.
* Fix: Fixed layout issue affecting the post carousel pagination.
* Fix: Added missing **Term Order** control to the Filter widget.
* Tweak: Made **Row Span** and **Column Span** controls responsive in the Template Grid layout of the Post widget.

= 1.4.0 – 2025-06-09 =

* New: Introduced **Default Filters** feature: Backend users can now define fixed taxonomy, meta, or date queries to include in the filter logic, under Content > Default Filters.
* Fix: Resolved a layout issue in the Post widget when using the **Template Grid** layout with **User** or **Taxonomy** queries.
* Fix: Corrected the performance sanitization rules, which were previously using a default logic, leading to inconsistent filter behavior in some cases.

= 1.3.3 – 2025-05-24 =

* New: Added styling controls for checkbox and radio button labels in the Filter widget, allowing greater design flexibility.
* New: Introduced `.bpfwe-selected-terms` class to display currently selected filter terms inside any widget.
* Fix: Resolved a crash when using the Custom Field dynamic tag with array-type fields in combination with the wpautop() function.
* Fix: Prevented duplicated filter forms from interfering with each other's queries.
* Compatibility: Confirmed compatibility with Elementor version 3.29.0

For more information, see [Changelog](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/changelog/).

== Upgrade Notice ==

= 1.6.0 =

This update adds visual range scaling, custom label formatting, quick-deselect pills, post alignment controls, dev filters, and various fixes/improvements.
