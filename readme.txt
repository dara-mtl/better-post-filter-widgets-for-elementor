=== Better Post & Filter Widgets for Elementor ===
Contributors: nomade123456
Donate link: https://wpsmartwidgets.com/donate/
Tags: elementor, woocommerce, product filter, post filter, ajax filter
Stable tag: 1.2.4
Tested up to: 6.7
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

= 1.2.4 - 2025-04-19 =

* Fix: Removed `'fields' => 'ids'` from the filter query causing an AJAX error in Elementor Pro widgets requiring the full post object.

= 1.2.3 - 2025-04-18 =

* Tweak: Group Separator swatch now defaults to an empty value instead of a pre-filled label, preventing accidental saving of unused separator titles.

= 1.2.2 - 2025-04-17 =

* Tweak: Post Featured Image dynamic tag can now be used on background.
* Tweak: Minor CSS adjustments to improve template grid layout consistency.
* Compatibility: Confirmed compatibility with WordPress 6.8.
* Fix: Re-applied fix for Search widget redirecting to the search results page when attached to a Post widget.

= 1.2.1 =

* Fix: Added missing files

= 1.2.0 - 2025-04-10 =

* New: Users can now assign a swatch to any taxonomy terms, with support for color, image/icon, WooCommerce category image, group separator and button.
* New: Added inline label support for filters, allowing users to toggle between vertical and horizontal layouts.
* New: Introduced an option to toggle labels and checkbox/radio inputs.
* New: Introduced new dynamic tags: taxonomy meta and image custom field, both designed to enhance template grid compatibility with user & taxonomy queries.
* Tweak: Enabled additional dynamic tags (Tax Meta, User Meta, Image Custom Field) for Elementor Pro users, alongside existing tags (Custom Field, Repeater, Post Content), to support template grid creation with taxonomy and user queries.
* Tweak: Added support for displaying taxonomy hierarchies in select dropdown (previously limited to radio and checkboxes).
* Tweak: Hierarchical taxonomy is now fully recursive, displaying all depth levels.
* Tweak: Added filter pagination support for Elementor Pro's Products widget widget.
* Tweak: Extended dynamic tag support for taxonomy and user queries, enabling dynamic retrieval of user ID or taxonomy ID in the loop.
* Fix: Addressed an issue where the sorting widget query would overwrite the one from the filter.

= 1.1.3 - 2025-04-09 =

* Fix: Corrected the sender email address to properly reflect the site/domain instead of the plugin slug.

= 1.1.2 (Love Triangle Release) - 2025-03-19 =

* Tweak: Filter, Search and Sorting widgets can now be used as standalone or combined, using a shared target selector.
* Tweak: Added a post widget target and post type controls to the sorting widget.
* Fix: Fixed a bug preventing the sorting and search bar widgets from functioning as standalone components.
* Fix: Fixed an issue where Elementor buttons would become unresponsive after filtering a loop.
* Fix: Corrected intermittent Search widget redirects to the search results page when attached to a Post widget.

= 1.1.1 - 2025-02-28 =

* Tweak: Added icon support to the before/after section within the post widget content.
* Tweak: Improved background image handling in the post carousel for more consistent fetching on page load.
* Tweak: In the taxonomy query, added support for automatically retrieving featured images for product categories.
* Tweak: In the user query, replaced the user Gravatar with a custom user meta key.
* Fix: Addressed an issue where filters failed to retrieve the document ID in certain cases.

= 1.1.0 - 2025-02-14 =

* New: Added the ability to query taxonomies using the post widget, with full support for pagination and slider conversion. Users can now query posts, users, taxonomies, and the main query.
* New: Extended post carousel functionality:
  * Users can set post images as backgrounds on any containers.
  * Introduced marquee mode.
  * Added custom pagination via classes, allowing carousel control through any element on the page.
  * Enabled synchronization of two or more post carousels using the `.sync-sliders` class.
* Fix: Resolved Select2 dropdown conflict when multiple filters are present.

= 1.0.0 =

* Initial stable release.
