=== Better Post & Filter Widgets for Elementor ===
Contributors: nomade123456
Donate link: https://wpsmartwidgets.com/donate/
Tags: elementor, woocommerce, product filter, post filter, ajax filter
Requires at least: 6.2
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.8.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The only free pro-grade Elementor filter for posts, taxonomies, custom fields, ACF, WooCommerce, WPML & more. Ditch paid limits!

== Description ==

The only free Elementor plugin for unlimited pro-grade filtering of all your post content. Filter by taxonomies, custom fields, ACF, relational fields, and numeric ranges – with seamless integration, no restrictions, and full customization. Get advanced filtering features without paying for limitations.

### Filter Widget Key Features:
- Compatible with Elementor Pro post widget, ACF, WooCommerce and most translation plugins.
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

[Post Slider/Carousel Demo](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/elementor-post-slider-travel-theme-demo/) – Check out the post widget possibilities.

### Create Loop Grids for Free:
Unlock the power of dynamic loops in Elementor, without needing the Pro version! Effortlessly showcase custom content in a loop format and experience the full potential of Elementor at no extra cost.

### Exclusive Dynamic Tags:
- Exclusive Repeater Field Tag: Unlock the ability to directly output ACF repeater fields in the Elementor frontend, with the flexibility to wrap each part in different HTML tags.
- Includes a series of dynamic tags, allowing users to fully utilize template grids with the free version.

### Crafted for Seamless Elementor Integration:
- Blends seamlessly with Elementor's native interface.
- No disruptive branding — Enjoy a clean, streamlined interface without unnecessary distractions.
- Lightweight design, utilizing Elementor's resources to minimize external dependencies.

### Developer-Friendly:
Tailor the widget to your needs with the help of a dedicated filters.

== Connecting the Filter Widget ==

To link your Filter Widget to a Post or Loop Grid widget, follow these steps:

1. Open the Post Widget settings in Elementor.
Go to the Advanced tab and enter the following in the CSS Classes field:
`results`
2. Open your Filter Widget.
In the Post Widget Target field, enter:
`.results`
3. Save the page. The filter will now update the correct widget.

Note: `results` is a reference class. You can choose any class or id name you like, as long as the Filter Widget target matches the Post Widget class (with a .) or id (with a #).

= Troubleshooting =

- Make sure the Post Widget class name and the Filter Widget target match.
- Check for incompatible plugins or theme conflicts:
  1. Temporarily switch to a default WordPress theme.
  2. Deactivate all other plugins except Elementor and Better Post & Filter Widgets.
  3. Test the filter. If it works, reactivate your plugins one by one to find the one causing the conflict.
- Disable caching or optimization plugins while testing, as they can interfere with AJAX.
- Check the browser console for JavaScript errors (press F12 and look under the Console tab) and resolve any errors that appear.

== Frequently Asked Questions ==

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

= 1.8.0 – Upcoming =

* New: True faceted filtering for the Filter widget.
  * Filter options now react to each other in real time (dynamic counts & availability)
  * Unavailable options can be greyed out or hidden to prevent dead-end combinations
  * Faceted behavior is fully opt-in and can be enabled globally or per individual filter group
  * Feature will continue to be refined and expanded in future releases
* New: Feed layout for the Post widget.
  * Posts can now be grouped by taxonomy or specific terms (magazine/news feed look)
  * Added quick controls to filter/show only the selected group
* New: Free numeric input for meta-based filters.
  * Numeric filters are no longer limited to predefined ranges.
  * Users can manually enter custom minimum and maximum values.
  * Backend users can define custom placeholders for numeric inputs.
* New: Start expanded option for Filter widget toggle mode.
* New: Custom AJAX handler (experimental).
  * Optional high-performance replacement for admin-ajax.php.
  * Can be enabled per Filter widget under Performance settings.
  * Fully isolated and opt-in — safe for testing without affecting existing sites.
  * Planned to become the default in a future release.
* Fix: Corrected a typo in the Post widget where "Excluded terms" was labeled as "Included terms".
* Fix: Fixed an issue where the inner wrapper class filter was not applied correctly in the Post widget
* Fix: Potentially resolved an Elementor editor issue where container CSS could break when using the main query in the Post widget.
* Tweak: Post terms output can now be displayed as: comma-separated, ul or ol.
* Tweak: Improved widget UI consistency by replacing RAW text with notice controls where appropriate.
* Dev: Internal change to Filter widget behavior.
  * Previously, Filter widget settings (e.g. custom AJAX, faceting mode) were applied globally to all linked filters.
  * Now each Filter widget uses its own settings independently.

= 1.7.3 – 2025-12-03 =

* Fix: Search bar issue when multiple widgets share the same filter.

= 1.7.2 – 2025-12-03 =

* Fix: Corrected an issue where using ?results=filter-XXXXXXX did not reload the page with the correct filter options pre-selected.
* Fix: Resolved an issue where search queries returned empty when two or more Search widgets were linked to the same Post widget on the same page.
* New: Added a Display Format control to the Term Meta dynamic tag, allowing terms to be shown inline, as an unordered list (ul), or as an ordered list (ol).
* New: Added a style control for select fields inside the Filter widget.
* New: Added a style control for customizing the Select2 background inside the Filter widget.
* Tweak: Checked and confirmed full compatibility with WordPress 6.9.

= 1.7.1 – 2025-10-29 =

* Fix: Added "Include Loop Grid Query ID" switch under the Query section to prevent potential query ID conflicts.
  * The automatic inclusion of the Loop/Post Widget Query ID is now disabled by default for backward compatibility.
* UI/Tweak: Reorganized Filter widget UI for clarity and better grouping of query-related controls.
* Tweak: Added `$with_css = true` parameter to the Template Grid option in the Post Widget.

= 1.7.0 – 2025-10-24 =

* New: Added "Include Loop Grid Query ID" switch under the Query section to prevent potential query ID conflicts
  * Relational fields were also added to the Default Filter section, allowing pre-filtering of results based on related users or posts.
* New: Added quick deselect pill support for numeric ranges.
* New: Added URL-based filter triggering.
  * Filters can now be triggered using ?results=filter-XXXXXXX (replace XXXXXXX with the Filter ID).
  * After interacting with the widget, the URL scheme will be automatically revealed for sharing or linking.
* New: Filter query results now detect Elementor Pro and BPFWE Post Widget `query_id`, ensuring filter results do not override the Loop Grid's own query.
* New/Dev: Added a developer filter for extending relational meta-based terms:
  * `bpfwe/get_relational_terms/{query_id}`
* Fix: Prevented a potential HTTP 500 error when using a single template directly on a static page instead of assigning the template to the page itself.

For full changelog, see [Changelog](https://wpsmartwidgets.com/doc/better-post-and-filter-widgets/changelog/).

== Upgrade Notice ==

= 1.8.0 =

Version 1.8.0 introduces powerful new filtering and layout capabilities.
This release brings true faceted filtering with real-time option interaction, a new feed-style layout for Posts, free numeric inputs for meta filters, and multiple UX and performance improvements. It also includes UI refinements, bug fixes, and an experimental high-performance AJAX handler for advanced setups.
