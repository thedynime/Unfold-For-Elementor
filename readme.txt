=== Unfold for Elementor ===
Contributors: dynime, jitkumarsaha
Donate link: https://dynime.com
Tags: elementor, show more, show less, content toggle, read more, accordion, expand collapse, unfold
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A flexible Show More and Show Less solution for Elementor. Make widgets, containers, and sections expandable and collapsible from the Advanced tab.

== Description ==

**Unfold for Elementor** provides a modern, seamless way to make any Elementor content expandable and collapsible. Instead of inserting dedicated widgets and duplicating content, Unfold integrates directly into Elementor's **Advanced tab** across all supported elements:

* **Flexbox Containers & Grid Containers**
* **Sections & Inner Sections**
* **Columns & Inner Columns**
* **Elementor Core Widgets** (Heading, Image, Text Editor, Icon Box, Button, etc.)
* **Elementor Pro Widgets** (Loop Grid, Portfolio, Posts, Products, etc.)
* **Third-Party Addon Widgets**
* **Nested Elementor Structures**

### ⚡ Key Features

* **Advanced Tab Integration:** Toggle Unfold directly under `Advanced → Unfold` on any compatible Elementor element.
* **Responsive Height Settings:** Set independent collapsed heights for Desktop, Tablet, and Mobile devices (supports `px`, `vh`, `rem`, `%`, and `0px` full hide mode).
* **Content Preview Mode:** Fold based on a custom percentage of the content height (e.g. show 35% on fold).
* **0px Full Hide / Collapse from Top:** Completely hide extra rows, card details, or specifications until expanded.
* **Dynamic Overlays:** Choose from Classic Gradient, Frosted Glass (blur & glassmorphism), Dark Card Shadow, or custom multi-stop backgrounds.
* **Custom Button Layouts:** Standard Text & Icon, Rounded Pill, or Floating Icon-Only badges (Product Card style).
* **Button Placement Options:** Standard (below content) or Overlap (floating over bottom edge/fade).
* **Smooth 180° Icon Rotation:** Fluid icon rotation during expand/collapse transitions.
* **Animation & Physics:** Smooth Accordion Slide, Slide + Content Fade, and Elastic Spring transitions.
* **Smart Auto-Hide:** Automatically hides buttons and overlays when content naturally fits within the collapsed height using ResizeObserver.
* **Accordion Mode:** Group elements by name so expanding one automatically collapses sibling items.
* **State Persistence:** Remembers expand/collapse preference across page visits via browser localStorage.
* **Scroll-to on Collapse:** Smoothly brings the viewport back to the top of the element upon collapsing.
* **Accessible & SEO Friendly:** Uses semantic `<button>` elements, standard ARIA states (`aria-expanded`), keyboard navigation (`Enter` / `Space`), and keeps all content indexed in the DOM.
* **Zero Telemetry & 100% Local:** No remote tracking, no external CDN dependencies, and no promotional banners.

== Installation ==

1. Upload the `unfold-for-elementor` folder to your `/wp-content/plugins/` directory, or install directly through the WordPress Plugins dashboard (`Plugins → Add New → Upload Plugin`).
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Open any page in the Elementor Editor.
4. Select any Container, Section, Column, or Widget.
5. Go to the **Advanced** tab and expand the **Unfold** section.
6. Toggle **Enable Unfold: Yes** and customize your settings.

== Frequently Asked Questions ==

= Does this plugin work with Elementor Containers? =
Yes. Unfold for Elementor works with both Flexbox Containers and Grid Containers, as well as legacy Sections and Columns.

= Does this plugin work with third-party Elementor addon widgets? =
Yes. Because Unfold operates at the Elementor element level, any standard third-party widget can be folded directly through its Advanced tab.

= Is content hidden from search engines? =
No. The content remains fully rendered in the DOM for search engine indexing and accessibility screen readers. It is only visually collapsed via CSS and JavaScript.

= Does this plugin require an API key or account? =
No. Unfold for Elementor is 100% free, fully local, and requires zero external API keys or registration.

= Can I completely hide content initially (0px height)? =
Yes. Set the **Collapsed Height** to `0px` and **Initially Expanded** to `No`. The content will be completely folded until the user clicks the expand button.

== Screenshots ==

1. Elementor Advanced tab Unfold controls.
2. Responsive collapsed height and fold modes.
3. Dynamic overlay styles including Frosted Glass and Classic Gradient.
4. Floating icon button and pill button layouts.
5. Front-end expand/collapse interaction.

== Requirements ==

* WordPress 5.8 or greater
* PHP 7.4 or greater
* Elementor 3.5.0 or greater

== Privacy ==

Unfold for Elementor does not collect, store, or transmit any personal data, IP addresses, or telemetry. It makes zero external HTTP requests.

== Changelog ==

= 1.0.0 =
* Initial official release for WordPress.org.
* Universal Advanced tab integration for all Elementor elements.
* Responsive collapsed height controls (Desktop, Tablet, Mobile) with 0px full collapse support.
* Dynamic overlay styles (Classic, Frosted Glass, Dark Card, Custom).
* Multiple button layouts (Standard, Pill, Floating Icon) and placement modes (Below, Overlap).
* Smooth 180° icon rotation and slide+fade animations.
* Accordion grouping and localStorage state persistence.
* Elementor live preview real-time synchronization.

== Upgrade Notice ==

= 1.0.0 =
Initial release of Unfold for Elementor.
