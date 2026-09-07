# Unfold for Elementor

<p align="center">
  <img src="unfold-for-elementor-logo.png" alt="Unfold for Elementor Logo" width="380" />
</p>

<p align="center">
  <strong>A flexible Show More and Show Less solution for Elementor.</strong><br>
  Make widgets, containers, sections, and nested Elementor content expandable and collapsible directly from the Advanced tab.
</p>

<p align="center">
  <a href="https://wordpress.org/plugins/unfold-for-elementor/"><img src="https://img.shields.io/badge/WordPress-5.8%2B-blue.svg" alt="WordPress 5.8+"></a>
  <a href="https://elementor.com/"><img src="https://img.shields.io/badge/Elementor-3.5.0%2B-red.svg" alt="Elementor 3.5.0+"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-7.4%2B-purple.svg" alt="PHP 7.4+"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-GPLv2%20or%20later-green.svg" alt="License: GPLv2"></a>
  <a href="https://dynime.com"><img src="https://img.shields.io/badge/Developed%20by-Dynime%20LLC-darkblue.svg" alt="Dynime LLC"></a>
</p>

---

## 📖 Overview

**Unfold for Elementor** brings universal, high-performance content folding to your Elementor pages. Instead of inserting dedicated widgets and duplicating content, Unfold integrates natively into **Elementor's Advanced Tab** across all element types:

- 🧱 **Flexbox Containers & Grid Containers**
- 📦 **Sections & Inner Sections**
- 🏛️ **Columns & Inner Columns**
- 🧩 **Core Elementor Widgets** (Heading, Image, Text, Icon Box, Button, etc.)
- 💎 **Elementor Pro Widgets** (Loop Grid, Portfolio, Posts, Products, etc.)
- 🔌 **Third-Party Addon Widgets**
- 🪆 **Nested Elementor Structures**

---

## ⚡ Key Features

- **🚀 Native Advanced Tab Integration:** Toggle Unfold directly under `Advanced → Unfold` on any element.
- **📏 Responsive Collapsed Height:** Independent sliders for Desktop, Tablet, and Mobile (`px`, `vh`, `rem`, `%`).
- **🫥 0px Full Collapse / Start From Top:** Set collapsed height to `0px` to completely hide extra rows, card details, or specifications until expanded.
- **🎨 Dynamic Overlays:**
  - *Classic Bottom Gradient*
  - *Frosted Glass* (`backdrop-filter: blur(8px)`)
  - *Dark Card Shadow* (Vignette for dark cards)
  - *Custom Multi-Stop Backgrounds*
- **🔘 Versatile Button Layouts:**
  - *Standard Button* (Text & Icon)
  - *Pill Button* (Rounded 9999px)
  - *Floating Circle / Square Icon Badge* (E-commerce card style)
- **📍 Smart Button Placement:** Below Content or Overlapping the bottom edge.
- **🔄 Smooth 180° Icon Rotation:** Fluid icon flipping during expand/collapse transitions.
- **✨ Physics-Based Animations:** Smooth Accordion Slide, Slide + Content Fade, and Elastic Spring transitions.
- **👁️ Smart Auto-Hide:** Automatically hides buttons and overlays if the content naturally fits inside the collapsed height using `ResizeObserver`.
- **📂 Accordion Mode:** Group elements by name so expanding one automatically collapses sibling items.
- **💾 State Persistence:** Remembers user expand/collapse preference across page visits via `localStorage`.
- **♿ Accessible & SEO Friendly:** Semantic `<button>` elements, full `aria-expanded` and `aria-controls` bindings, keyboard navigation (`Enter` / `Space`), and complete content indexing in the DOM.
- **🔒 100% Local & Privacy Compliant:** Zero external CDN dependencies, zero telemetry, and zero tracking.

---

## 🛠️ Installation

### Via WordPress Plugin Dashboard
1. Go to **Plugins → Add New → Upload Plugin**.
2. Upload `unfold-for-elementor.zip` and click **Install Now**.
3. Activate the plugin.

### Manual Installation
1. Clone or download the repository into your WordPress plugin directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/thedynime/Unfold-For-Elementor.git unfold-for-elementor
   ```
2. Activate **Unfold for Elementor** in the WordPress admin panel under **Plugins**.

---

## 🎯 Quick Start Guide

1. Open any page in the **Elementor Editor**.
2. Click to select any Container, Section, Column, or Widget.
3. In the left panel, navigate to the **Advanced** tab and open the **Unfold** section.
4. Toggle **Enable Unfold: Yes**.
5. Customize your **Collapsed Height**, **Overlay Style**, **Button Layout**, and **Animation Effect**.

---

## 📂 Project Structure

```
unfold-for-elementor/
├── unfold-for-elementor.php           # Main bootstrap & accurate header metadata
├── uninstall.php                      # Safe uninstaller (zero DB clutter)
├── readme.txt                         # WordPress.org standard documentation
├── README.md                          # GitHub repository documentation
├── LICENSE                            # GNU General Public License v2.0
├── includes/
│   ├── class-plugin.php               # Plugin singleton & environment compatibility
│   ├── class-elementor-integration.php# Elementor lifecycle initialization
│   ├── class-controls.php             # Advanced tab 'Unfold' controls & style panels
│   ├── class-renderer.php             # Frontend attribute & class renderer
│   ├── class-assets.php               # Frontend, preview, and editor asset manager
│   └── helper-functions.php           # Prefixed helper & safe icon rendering functions
├── assets/
│   ├── css/
│   │   ├── unfold-elementor.css       # Core layout, dynamic overlays, animations & button styles
│   │   └── unfold-elementor-editor.css# Elementor editor panel styles & icons
│   └── js/
│       ├── unfold-elementor.js        # Interaction engine (ResizeObserver, 0px hide, Accordion)
│       └── unfold-elementor-editor.js # Elementor editor live preview real-time synchronization
└── languages/
    └── unfold-for-elementor.pot       # Clean POT translation template
```

---

## 🛡️ License

This plugin is licensed under the **GNU General Public License v2.0 or later** ([GPLv2 or later](LICENSE)).

---

## 🏢 Developed by Dynime LLC

**Unfold for Elementor** is designed, developed, and maintained by **[Dynime LLC](https://dynime.com)**.

For questions, suggestions, or support:
- 🌐 Website: [https://dynime.com](https://dynime.com)
- 📧 Support: [support@dynime.com](mailto:support@dynime.com)
