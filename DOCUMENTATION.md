# WELLME Pamphlets — Complete Plugin Documentation

## Table of Contents

1. [Overview](#1-overview)
2. [Requirements & Dependencies](#2-requirements--dependencies)
3. [Installation](#3-installation)
4. [Shortcodes](#4-shortcodes)
5. [Admin: Creating & Managing Modules](#5-admin-creating--managing-modules)
6. [Interactive Components](#6-interactive-components)
7. [AJAX System](#7-ajax-system)
8. [WPML Multilingual Support](#8-wpml-multilingual-support)
9. [Plugin Architecture](#9-plugin-architecture)
10. [Constants & Configuration](#10-constants--configuration)
11. [CSS Architecture](#11-css-architecture)
12. [JavaScript Architecture](#12-javascript-architecture)
13. [ACF Field Reference](#13-acf-field-reference)
14. [Automatic Updates](#14-automatic-updates)
15. [Internationalisation](#15-internationalisation)
16. [Accessibility](#16-accessibility)
17. [Design Reference](#17-design-reference)
18. [Troubleshooting](#18-troubleshooting)

---

## 1. Overview

**WELLME Pamphlets** is a WordPress plugin that delivers interactive digital pamphlets for the WELLME EU training project. Trainers use the pamphlets during a two-day training in Poland.

The plugin is **independent of the Divi theme** — it works via shortcodes placed on any WordPress page. It has no jQuery dependency and uses no page-builder-specific APIs.

### Project Context

The WELLME project has **6 training modules** for youth trainers:

| # | Module Title |
|---|---|
| 1 | From Strength To Strength: Positive Psychology For Youth Trainers |
| 2 | Bounce Back Stronger |
| 3 | Fuel for Flourishing: Healthy Nutrition & Lifestyle in Youth Work |
| 4 | Move to Thrive: Integrating Physical Activity into Youth Wellbeing |
| 5 | Spaces that Connect: Designing Environments for Youth Belonging |
| 6 | Bridges to Adulthood: Guiding Youth Transitions through Community Power |

---

## 2. Requirements & Dependencies

### WordPress environment

| Requirement | Minimum version |
|---|---|
| PHP | 7.4 |
| WordPress | 6.2 |
| Advanced Custom Fields PRO | 6.8 |

### Composer packages

| Package | Version | Purpose |
|---|---|---|
| `yahnis-elsts/plugin-update-checker` | ^5.3 (resolved to 5.6) | Automatic updates via GitHub releases |

ACF Pro must be **installed and activated before** this plugin. All ACF field groups are registered in PHP — no manual UI setup is required.

---

## 3. Installation

1. Copy the plugin folder to `/wp-content/plugins/wellme-pamphlets/`.
2. Install and activate **Advanced Custom Fields PRO** (copy provided in `references doc/advanced-custom-fields-pro/`).
3. Activate **WELLME Pamphlets** from the WordPress Plugins screen. Rewrite rules are flushed automatically on activation.
4. Go to **WELLME Modules → Add Module** in the admin and create the 6 modules (see [Section 5](#5-admin-creating--managing-modules)).
5. Place shortcodes on your pages (see [Section 4](#4-shortcodes)).

> The plugin registers its update checker against `https://github.com/GeorgeWebDevCy/wellme-pamphlets`. Pushing a new GitHub release/tag will surface the update in the WordPress Plugins screen automatically.

### On activation

`Wellme_Pamphlets_Activator::activate()` calls `flush_rewrite_rules()` so the `wellme_module` CPT slug (`/wellme-module/`) resolves immediately without a manual Settings → Permalinks save.

### On deactivation

`Wellme_Pamphlets_Deactivator::deactivate()` calls `flush_rewrite_rules()` to clean up.

### On uninstall

`uninstall.php` runs `delete_option('wellme_pamphlets_options')`. Module posts themselves are **not** deleted — content is preserved even if the plugin is removed.

---

## 4. Shortcodes

All shortcodes are registered by `Wellme_Pamphlets_Shortcodes::register()` on the `init` hook. Each uses `ob_start()` / `ob_get_clean()` to capture a PHP partial template and return it as a string — safe for use inside page builder text modules.

---

### `[wellme_module_grid]`

Renders the 6-card module index. Each card opens its full pamphlet in a slide-in modal via AJAX. This is the main entry point for visitors browsing all modules.

```
[wellme_module_grid]
[wellme_module_grid columns="2" order="DESC"]
```

**Attributes:**

| Attribute | Default | Description |
|---|---|---|
| `columns` | `3` | Number of grid columns (passed as `data-columns` on the grid element; CSS handles the actual layout) |
| `orderby` | `meta_value_num` | WP_Query `orderby` parameter |
| `order` | `ASC` | `ASC` or `DESC` |

**Template:** `public/partials/wellme-module-grid.php`

The template renders a `.wellme-module-grid` div containing one `.wellme-module-card` per module, followed by the `#wellme-pamphlet-modal` container (which is hidden by default and shared across all cards on the page).

Each card carries:
- `data-pamphlet-id` — the `wellme_module` post ID, used by the AJAX loader
- `role="button"` and `tabindex="0"` for keyboard accessibility
- `--module-color` CSS custom property set inline from the ACF Module Colour field

**ACF fields read:** `module_number`, `module_subtitle`, `module_description`, `module_color`, `module_icon`, `module_cover_image`

---

### `[wellme_pamphlet]`

Renders the full interactive pamphlet for a single module inline on the page. Use this to embed a specific module on its own dedicated page, without the modal/AJAX layer.

```
[wellme_pamphlet id="42"]
[wellme_pamphlet slug="module-1-from-strength-to-strength"]
```

**Attributes:**

| Attribute | Description |
|---|---|
| `id` | WordPress post ID of the `wellme_module` post |
| `slug` | Post slug (alternative to `id` — uses `get_posts()` with `name` argument) |

**Template:** `public/partials/wellme-pamphlet.php`

This is the same template used by the AJAX handler. Sections are rendered only when their data is present — a module with no chapters will not show the Chapters section, and so on.

---

### `[wellme_flipcards]`

Renders the Sum-Up slide — a grid of CSS 3D flip cards, one per module. Front shows the cover image and title; click/tap flips the card to reveal the module motto.

```
[wellme_flipcards]
```

No attributes. Always shows all published `wellme_module` posts ordered by module number.

**Template:** `public/partials/wellme-flipcards.php`

Each `.wellme-flipcard` carries `role="button"` and `tabindex="0"`. The `is-flipped` CSS class toggles the 3D rotation. If a module has no motto set, a placeholder string is shown on the back.

**ACF fields read:** `module_number`, `module_motto`, `module_color`, `module_cover_image`

---

### `[wellme_experience]`

A single shortcode that turns a blank page into a full-viewport interactive presentation. The experience uses a Mazda / Publitas-inspired reader layout: each major section appears as a centered publication page on a quiet light background, with large edge chevrons, dot navigation, and a page counter. Module cards inside the reader load the full interactive pamphlet via AJAX without leaving the page.

```
[wellme_experience]
```

No attributes. Always shows all published modules ordered by module number.

**Template:** `public/partials/wellme-experience.php`

**Recommended page setup:** use a page template that removes the theme header and footer so the experience fills the entire browser window edge-to-edge. In Divi, set **Page Attributes → Template** to **Blank Page**.

**Navigation methods:**

| Method | Behaviour |
|---|---|
| Left / Right arrow buttons | Large edge chevrons; Prev hidden on first slide, Next hidden on last |
| Dot indicators | Bottom centre; one dot per reader page; active dot scales up |
| Keyboard ← → | Navigate slides; disabled while drawer is open |
| Touch swipe | ≥50 px horizontal swipe triggers prev/next |
| Keyboard `Escape` | Closes the pamphlet drawer |

**Slide behaviour:**

- Each slide is presented as a fixed-ratio publication page with a subtle border and shadow, scaled responsively for desktop and mobile.
- Slide content fades in and rises slightly as the page becomes active; fades out on departure
- `aria-current="true"` is updated on the active dot; `aria-live="polite"` on the counter

**Drawer behaviour:**

- Enters from below (CSS `transform: translateY(100%)` → `translateY(0)`)
- Positioned `fixed` at `z-index: 10002` — sits above the experience overlay and outcome side-panels
- 78 vh height; independently scrollable with `overscroll-behavior: contain`
- Pamphlet content is cached per module in `data-loaded-id` — revisiting the same module in a session skips the AJAX request
- Closes via the × button or `Escape`; focus returns to the active slide's Explore button
- `document.body.style.overflow = 'hidden'` while open to prevent background scroll

**ACF fields read (slide level):** `module_number`, `module_color`, `module_subtitle`, `module_cover_image`

**ACF fields read (drawer):** all pamphlet fields — see [wellme_pamphlet](#wellme_pamphlet-id) above

---

## 5. Admin: Creating & Managing Modules

Go to **WELLME Modules → Add Module** in the WordPress admin. The **post title** is the module title. All other content is managed through ACF field groups organised into five tabs.

> Field groups are registered entirely in PHP inside `class-wellme-pamphlets-acf.php` using `acf_add_local_field_group()`. No manual ACF UI setup is required.

---

### Tab: Identity

| Field | ACF name | Type | Notes |
|---|---|---|---|
| Module Number | `module_number` | Number | Required. 1–6. Controls display order across all shortcodes. |
| Subtitle | `module_subtitle` | Text | Short tagline shown on the card and in the experience slider. |
| Short Description | `module_description` | Textarea (3 rows) | Shown on the module card and the pamphlet cover section. |
| Module Colour | `module_color` | Colour Picker | Accent colour; default `#005b96`. Sets `--module-color` CSS variable. |
| Module Icon | `module_icon` | Image (array) | SVG or PNG icon shown on the module card. |
| Cover / Hero Image | `module_cover_image` | Image (array) | Full-width hero for the pamphlet cover, experience slider background, and hotspot map base image. |
| Module Motto | `module_motto` | Text | Revealed on the back of the flip card in `[wellme_flipcards]`. |

---

### Tab: Learning Outcomes

A **repeater field** (`module_learning_outcomes`, max 10 rows). Each row becomes a clickable button that opens a side-panel overlay.

| Sub-field | ACF name | Type | Notes |
|---|---|---|---|
| Outcome Title | `outcome_title` | Text | Button label and panel heading. |
| Outcome Detail | `outcome_detail` | WYSIWYG (basic toolbar, no media upload) | Rich content shown inside the panel. |
| Icon (optional) | `outcome_icon` | Image (array) | Small icon shown on the outcome button. |

---

### Tab: Exercise Steps

A **repeater field** (`module_exercise_steps`, max 20 rows). Each row gets a numbered pulsing hotspot dot placed on the cover image.

| Sub-field | ACF name | Type | Notes |
|---|---|---|---|
| Step Title | `step_title` | Text | Shown in the step panel header. |
| Step Content | `step_content` | WYSIWYG (basic toolbar, no media upload) | Rich content for this step. |
| Step Image | `step_image` | Image (array) | Optional image inside the step panel. |
| Hotspot X Position (%) | `step_hotspot_x` | Number (0–100) | Horizontal position of the dot on the cover image. Default 50. |
| Hotspot Y Position (%) | `step_hotspot_y` | Number (0–100) | Vertical position of the dot on the cover image. Default 50. |

> **Positioning tip:** Preview the cover image and estimate percentages. `50 / 50` centres the dot. The dot uses `transform: translate(-50%, -50%)` so the value is the dot's centre point, not its top-left corner.

---

### Tab: Chapters

A **repeater field** (`module_chapters`, max 10 rows). Each row becomes a pill-shaped chapter navigation button.

| Sub-field | ACF name | Type | Notes |
|---|---|---|---|
| Chapter Title | `chapter_title` | Text | Button label. |
| Chapter Content | `chapter_content` | WYSIWYG (full toolbar, media upload enabled) | Rich content for this chapter. |
| Chapter Image | `chapter_image` | Image (array) | Optional image at the top of the chapter panel. |

---

### Tab: Media

| Field | ACF name | Type | Notes |
|---|---|---|---|
| Module Video URL | `module_video_url` | URL | YouTube or Vimeo — embedded via `wp_oembed_get()` in a responsive 16:9 wrapper. |
| Photo Gallery | `module_gallery` | Gallery (array, max 20) | Additional images displayed in a 3-column grid. |

---

## 6. Interactive Components

All interactive behaviour is in `public/js/wellme-pamphlets-public.js` (vanilla JS, no jQuery) and `public/css/wellme-pamphlets-public.css`.

---

### Module Grid Cards

**CSS class:** `.wellme-module-card`

- 3-column responsive CSS Grid (`repeat(3, 1fr)`) — 2-col at ≤900 px, 1-col at ≤560 px
- Each card has a hover `translateY(-6px)` lift and image zoom (`scale(1.04)`) on `.wellme-card-image`
- `box-shadow` intensifies on hover
- Clicking (or pressing `Enter`/`Space`) triggers the AJAX pamphlet loader (see [Section 7](#7-ajax-system))
- `--module-color` set inline from ACF; drives the top border and CTA text colour

---

### Pamphlet Modal

**CSS class:** `.wellme-pamphlet-modal` / `#wellme-pamphlet-modal`

- `position: fixed; inset: 0; z-index: 9999`
- Semi-transparent overlay (`rgba(0,0,0,0.6)`) behind the inner panel; clicking the overlay closes the modal
- Inner panel: `margin-left: auto; width: min(100%, 860px); height: 100%; overflow-y: auto`
- Slides in from the right via `@keyframes wellme-slide-in` (`translateX(100%)` → `translateX(0)`)
- Focus is trapped inside the modal while open (`trapFocus()` in JS)
- Closes via the × button, overlay click, or `Escape`
- `document.body.style.overflow = 'hidden'` prevents background scroll while open

---

### Chapter Navigation

**CSS classes:** `.wellme-chapter-nav`, `.wellme-chapter-btn`, `.wellme-chapter-panel`

- Pill-shaped buttons in a wrapping flex row at the top of the Chapters section
- `activate(index)` function: adds `.is-active` to the clicked button; sets `aria-expanded="true"`; shows matching `.wellme-chapter-panel[data-chapter="n"]`; hides all others
- First chapter is shown by default (`activate(0)` called on init)
- Active button fills with `--module-color` background

---

### Learning Outcomes Side Panel

**CSS classes:** `.wellme-outcome-btn`, `.wellme-outcome-panel`

Inspired by the **Partou** age-group selector brochure pattern.

- Outcome buttons in a wrapping flex list (`.wellme-outcomes-list`)
- Clicking a button opens a fixed side-panel (`position: fixed; right: 0; width: 420px; height: 100vh; z-index: 10001`)
- Only one panel open at a time — opening a second closes the first
- Close with the × button (`.wellme-outcome-panel-close`) or `Escape`
- `aria-expanded` toggled on the trigger button; focus moves into the panel on open and returns to the trigger on close

---

### Exercise Step Hotspots

**CSS classes:** `.wellme-hotspot-dot`, `.wellme-hotspot-number`, `.wellme-hotspot-pulse`, `.wellme-step-panel`

Inspired by the **Outremer 55** catamaran brochure pattern.

- Numbered dots absolutely positioned over the cover image using `left: X%; top: Y%` from the ACF `step_hotspot_x` / `step_hotspot_y` fields
- Each dot has a continuous pulse ring animation (`@keyframes wellme-pulse`: scales 1→2.2, opacity 0.7→0, 2 s loop)
- Pulse stops on hover; the dot number scales up
- Clicking a dot shows the corresponding `.wellme-step-panel` below the map; all others close
- Step panels have **Prev** / **Next** buttons that reference sibling panel IDs via `data-target` / `data-current` attributes
- Dot `aria-expanded` is synced when navigating via Prev/Next

---

### Flip Cards

**CSS classes:** `.wellme-flipcard`, `.wellme-flipcard-inner`, `.wellme-flipcard-front`, `.wellme-flipcard-back`

- 3-column grid (2-col at ≤860 px, 1-col at ≤500 px); fixed height `280px`
- CSS 3D flip: `perspective: 1200px` on `.wellme-flipcard`; `transform-style: preserve-3d` on `.wellme-flipcard-inner`
- Back face: `transform: rotateY(180deg)` and `backface-visibility: hidden`
- Toggle: JS adds `.is-flipped` to `.wellme-flipcard`; CSS rule `.wellme-flipcard.is-flipped .wellme-flipcard-inner { transform: rotateY(180deg) }` does the rest — no JS animation library needed
- Keyboard: `Enter` or `Space` triggers the same toggle

---

### Scroll Reveal

**CSS class:** `.wellme-scroll-reveal` / `.is-visible`

- Initial state: `opacity: 0; transform: translateY(24px)`
- `IntersectionObserver` (threshold 0.12) adds `.is-visible` when the element enters the viewport, removing the transform and restoring opacity
- Once visible the observer calls `unobserve()` — no repeated firings
- Fallback: if `IntersectionObserver` is not supported, `.is-visible` is added to all elements immediately
- Respects `prefers-reduced-motion`: CSS overrides set `opacity: 1; transform: none; transition: none`

---

### Full-Screen Experience Slider

**CSS class:** `.wellme-experience` / `#wellme-experience`

See [Section 4 — `[wellme_experience]`](#wellme_experience) for the complete feature breakdown. Technical notes:

- The container uses `width: 100vw; margin-left: calc(-50vw + 50%)` to break out of the WP content column
- `overflow: hidden` on the container clips the off-screen slides horizontally
- The track uses `transform: translateX(-N * 100%)` (not `scrollLeft`) so the browser does not paint a scrollbar
- The drawer uses `position: fixed` (not `absolute`) to escape the container's `overflow: hidden` clipping context
- `will-change: transform` on both track and drawer hint to the browser to promote them to GPU compositing layers

---

## 7. AJAX System

The pamphlet AJAX loader is used by both `[wellme_module_grid]` (modal) and `[wellme_experience]` (drawer).

### Server side

**Handler:** `Wellme_Pamphlets_Public::ajax_load_pamphlet()`

**WordPress hooks registered:**

```
wp_ajax_wellme_load_pamphlet         → logged-in users
wp_ajax_nopriv_wellme_load_pamphlet  → guests
```

**Request flow:**

1. `check_ajax_referer('wellme_pamphlet_nonce', 'nonce')` — aborts with a 403 if the nonce is invalid.
2. Cast and validate the incoming `id` parameter (`(int) $_POST['id']`).
3. Apply `wpml_object_id` filter to resolve the ID to the correct translated post for the active language (no-op if WPML is not active).
4. `get_post($id)` — validates post type is `wellme_module` and status is `publish`.
5. `ob_start()` → `include 'wellme-pamphlet.php'` → `ob_get_clean()`.
6. `wp_send_json_success(['html' => $html])`.

**Error responses** use `wp_send_json_error(message)` and return HTTP 200 with `success: false` in the JSON body.

### Client side

**Nonce and AJAX URL** are passed to JS via `wp_localize_script()`:

```js
window.wellmePamphlets = {
    ajaxUrl: '/wp-admin/admin-ajax.php',
    nonce:   '...',
    loading: 'Loading…',
};
```

**Fetch call:**

```js
fetch(wellmePamphlets.ajaxUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
        action: 'wellme_load_pamphlet',
        id:     moduleId,
        nonce:  wellmePamphlets.nonce,
    }),
})
```

After HTML is injected, `initPamphletInteractions(container)` and `initScrollReveal()` are called immediately to wire up chapter nav, outcome panels, and hotspot dots inside the freshly rendered HTML.

---

## 8. WPML Multilingual Support

The plugin is WPML-compatible. No extra plugins beyond **WPML Multilingual CMS** and **WPML String Translation** are required.

### How it works

| Layer | Behaviour |
|---|---|
| `wellme_module` CPT | Translatable post type. WPML lets editors create a translated copy of each module post via the Translation Editor. |
| ACF field groups | Registered in PHP via `acf_add_local_field_group()`. WPML's ACF integration discovers these automatically. |
| `get_posts()` queries | WPML filters all WP_Query calls to return posts in the active language — no extra code needed in the plugin. |
| AJAX pamphlet loader | Applies `apply_filters('wpml_object_id', $id, 'wellme_module', true)` to resolve the incoming post ID to the correct translated post before rendering. The third argument `true` means it falls back to the original language if no translation exists. |
| UI strings | All user-facing strings use `__()` / `esc_html_e()` with the `wellme-pamphlets` text domain, ready for `.pot` generation. |

### WPML setup steps

1. In **WPML → Custom Fields Translation**, locate all `module_*` ACF fields and configure them:

   | Field | Recommended setting |
   |---|---|
   | `module_number` | Copy (same number across languages) |
   | `module_color` | Copy (same colour across languages) |
   | `module_subtitle` | Translate |
   | `module_description` | Translate |
   | `module_motto` | Translate |
   | `module_icon` | Copy |
   | `module_cover_image` | Copy (or Translate if different images per language) |
   | `module_learning_outcomes` (repeater) | Translate |
   | `module_exercise_steps` (repeater) | Translate |
   | `module_chapters` (repeater) | Translate |
   | `module_video_url` | Copy or Translate as needed |
   | `module_gallery` | Copy |

2. Translate each `wellme_module` post via the WPML post translation interface (the flag icons in the post list).
3. Place shortcodes on language-specific pages as usual — WPML serves the correct posts per language automatically.

### Generating translations (.pot)

```bash
wp i18n make-pot . languages/wellme-pamphlets.pot --domain=wellme-pamphlets
```

Or use **Loco Translate** from the WordPress admin.

---

## 9. Plugin Architecture

### Bootstrap sequence

```
wellme-pamphlets.php          (plugin header, constants, autoloader, hooks)
  └─ run_wellme_pamphlets()
       └─ new Wellme_Pamphlets()
            ├─ load_dependencies()      require_once all class files
            ├─ set_locale()             plugins_loaded → load_plugin_textdomain
            ├─ define_admin_hooks()     admin_enqueue_scripts → styles + scripts
            ├─ define_public_hooks()    wp_enqueue_scripts → styles + scripts
            │                          wp_ajax_* → ajax_load_pamphlet
            ├─ define_cpt_hooks()       init → register_post_types
            ├─ define_acf_hooks()       acf/init → register_field_groups
            ├─ define_shortcode_hooks() init → register (all 4 shortcodes)
            └─ setup_update_checker()   PucFactory::buildUpdateChecker()
                 └─ $plugin->run()      add_action / add_filter for all collected hooks
```

### Hook map

| Hook | Priority | Callback | Notes |
|---|---|---|---|
| `plugins_loaded` | 10 | `Wellme_Pamphlets_i18n::load_plugin_textdomain` | Loads `.mo` files from `/languages/` |
| `admin_enqueue_scripts` | 10 | `Wellme_Pamphlets_Admin::enqueue_styles` | Admin CSS |
| `admin_enqueue_scripts` | 10 | `Wellme_Pamphlets_Admin::enqueue_scripts` | Admin JS |
| `wp_enqueue_scripts` | 10 | `Wellme_Pamphlets_Public::enqueue_styles` | Public CSS |
| `wp_enqueue_scripts` | 10 | `Wellme_Pamphlets_Public::enqueue_scripts` | Public JS + `wp_localize_script` |
| `wp_ajax_wellme_load_pamphlet` | 10 | `Wellme_Pamphlets_Public::ajax_load_pamphlet` | Logged-in |
| `wp_ajax_nopriv_wellme_load_pamphlet` | 10 | `Wellme_Pamphlets_Public::ajax_load_pamphlet` | Guests |
| `init` | 10 | `Wellme_Pamphlets_CPT::register_post_types` | Registers `wellme_module` CPT |
| `init` | 10 | `Wellme_Pamphlets_Shortcodes::register` | Registers all 4 shortcodes |
| `acf/init` | 10 | `Wellme_Pamphlets_ACF::register_field_groups` | Registers ACF field groups in PHP |

### Class responsibilities

| Class | File | Responsibility |
|---|---|---|
| `Wellme_Pamphlets` | `includes/class-wellme-pamphlets.php` | Core orchestrator; wires all hooks; configures update checker |
| `Wellme_Pamphlets_Loader` | `includes/class-wellme-pamphlets-loader.php` | Collects `add_action` / `add_filter` calls and runs them in `run()` |
| `Wellme_Pamphlets_i18n` | `includes/class-wellme-pamphlets-i18n.php` | Loads text domain |
| `Wellme_Pamphlets_Activator` | `includes/class-wellme-pamphlets-activator.php` | `flush_rewrite_rules()` on activation |
| `Wellme_Pamphlets_Deactivator` | `includes/class-wellme-pamphlets-deactivator.php` | `flush_rewrite_rules()` on deactivation |
| `Wellme_Pamphlets_CPT` | `includes/class-wellme-pamphlets-cpt.php` | Registers `wellme_module` custom post type |
| `Wellme_Pamphlets_ACF` | `includes/class-wellme-pamphlets-acf.php` | Registers all ACF field groups via `acf_add_local_field_group()` |
| `Wellme_Pamphlets_Shortcodes` | `includes/class-wellme-pamphlets-shortcodes.php` | Renders all 4 shortcodes via PHP partials |
| `Wellme_Pamphlets_Public` | `public/class-wellme-pamphlets-public.php` | Enqueues frontend assets; AJAX handler |
| `Wellme_Pamphlets_Admin` | `admin/class-wellme-pamphlets-admin.php` | Enqueues admin assets |

### Custom Post Type: `wellme_module`

| Argument | Value |
|---|---|
| `public` | `true` |
| `show_ui` | `true` |
| `show_in_menu` | `true` |
| `menu_icon` | `dashicons-book-alt` |
| `menu_position` | `20` |
| `supports` | `title`, `thumbnail`, `revisions` |
| `has_archive` | `false` |
| `rewrite` slug | `wellme-module` |
| `show_in_rest` | `true` (Gutenberg / REST API compatible) |

### File structure

```
wellme-pamphlets/
├── wellme-pamphlets.php              Plugin header, constants, autoloader, bootstrap
├── composer.json                     Declares plugin-update-checker dependency
├── composer.lock                     Locked dependency versions
├── uninstall.php                     Cleanup on plugin deletion
├── index.php                         Security silence file
│
├── includes/
│   ├── class-wellme-pamphlets.php         Core class — wires all hooks + update checker
│   ├── class-wellme-pamphlets-loader.php  Hook registration collector
│   ├── class-wellme-pamphlets-i18n.php    Text domain loader
│   ├── class-wellme-pamphlets-activator.php
│   ├── class-wellme-pamphlets-deactivator.php
│   ├── class-wellme-pamphlets-cpt.php     Registers wellme_module CPT
│   ├── class-wellme-pamphlets-acf.php     Registers all ACF field groups in PHP
│   └── class-wellme-pamphlets-shortcodes.php
│       [wellme_module_grid] [wellme_pamphlet] [wellme_flipcards] [wellme_experience]
│
├── public/
│   ├── class-wellme-pamphlets-public.php  Asset enqueue + AJAX handler
│   ├── css/
│   │   └── wellme-pamphlets-public.css    All frontend styles
│   ├── js/
│   │   └── wellme-pamphlets-public.js     Vanilla JS — no jQuery dependency
│   └── partials/
│       ├── wellme-module-grid.php         Module card grid + modal container
│       ├── wellme-pamphlet.php            Full pamphlet template (all sections)
│       ├── wellme-flipcards.php           Sum-Up flip cards
│       └── wellme-experience.php         Full-viewport experience slider
│
├── admin/
│   ├── class-wellme-pamphlets-admin.php
│   ├── css/wellme-pamphlets-admin.css
│   └── js/wellme-pamphlets-admin.js
│
├── languages/                        .pot / .po / .mo files
│
└── vendor/                           Composer packages
    └── yahnis-elsts/
        └── plugin-update-checker/
```

---

## 10. Constants & Configuration

Defined in `wellme-pamphlets.php` immediately after the plugin header:

| Constant | Value | Used for |
|---|---|---|
| `WELLME_PAMPHLETS_VERSION` | `1.1.2` | Enqueue version strings; bump on every release |
| `WELLME_PAMPHLETS_PLUGIN_DIR` | `plugin_dir_path(__FILE__)` | Absolute server path to plugin root (trailing slash included) |
| `WELLME_PAMPHLETS_PLUGIN_URL` | `plugin_dir_url(__FILE__)` | Public URL to plugin root (trailing slash included) |
| `WELLME_PAMPHLETS_PLUGIN_FILE` | `__FILE__` | Absolute path to main plugin file; used by update checker and activation hooks |

---

## 11. CSS Architecture

All frontend styles live in `public/css/wellme-pamphlets-public.css`. There is no Sass/Less build step — the file is plain CSS.

### CSS Custom Properties (`:root`)

| Property | Default | Purpose |
|---|---|---|
| `--wellme-primary` | `#005b96` | Global fallback accent colour |
| `--wellme-radius` | `12px` | Border radius for cards, panels, map |
| `--wellme-shadow` | `0 4px 24px rgba(0,0,0,.12)` | Default card shadow |
| `--wellme-shadow-hover` | `0 8px 32px rgba(0,0,0,.2)` | Hover card shadow |
| `--wellme-transition` | `0.3s ease` | General transition timing |
| `--wellme-overlay-bg` | `rgba(0,0,0,0.6)` | Modal overlay colour |
| `--wellme-panel-width` | `420px` | Outcome side-panel width |
| `--wellme-flip-duration` | `0.6s` | Flip card animation duration |

### Per-module variable

`--module-color` is set inline on each component root element:

```html
<div class="wellme-module-card" style="--module-color: #e63946;">
```

Every descendant interactive element references `var(--module-color, var(--wellme-primary))` — the fallback ensures styles hold even if the ACF field is empty.

### Component class map

| Component | Root class | Notes |
|---|---|---|
| Module grid | `.wellme-module-grid` | CSS Grid; responsive columns |
| Module card | `.wellme-module-card` | `role="button"` |
| Card image | `.wellme-card-image` | Background-image div |
| Pamphlet modal | `.wellme-pamphlet-modal` | `position:fixed; z-index:9999` |
| Modal inner | `.wellme-pamphlet-modal-inner` | Scrollable right-side panel |
| Pamphlet section | `.wellme-pamphlet-section` | Generic section wrapper |
| Chapter nav | `.wellme-chapter-nav` | Pill button row |
| Chapter button | `.wellme-chapter-btn` | `.is-active` state |
| Chapter panel | `.wellme-chapter-panel` | `hidden` attribute toggles visibility |
| Outcome button | `.wellme-outcome-btn` | `aria-expanded` tracked |
| Outcome panel | `.wellme-outcome-panel` | `position:fixed; right:0; z-index:10001` |
| Hotspot map | `.wellme-hotspot-map` | `position:relative` container |
| Hotspot dot | `.wellme-hotspot-dot` | Absolute positioned; `transform:translate(-50%,-50%)` |
| Hotspot pulse | `.wellme-hotspot-pulse` | `@keyframes wellme-pulse` ring |
| Step panel | `.wellme-step-panel` | `hidden` attribute toggles; left border accent |
| Flip card | `.wellme-flipcard` | `perspective: 1200px`; `.is-flipped` triggers rotation |
| Flipcard inner | `.wellme-flipcard-inner` | `transform-style: preserve-3d` |
| Flip front | `.wellme-flipcard-front` | `backface-visibility: hidden` |
| Flip back | `.wellme-flipcard-back` | `rotateY(180deg)` + `backface-visibility: hidden` |
| Scroll reveal | `.wellme-scroll-reveal` | `opacity:0` → `.is-visible` adds `opacity:1` |
| Experience | `.wellme-experience` | `100vw × 100vh`; breaks out of content column |
| Experience track | `.wellme-experience-track` | Horizontal flex; `translateX` driven |
| Experience slide | `.wellme-experience-slide` | `.is-active` triggers content reveal + BG zoom |
| Experience drawer | `.wellme-exp-drawer` | `position:fixed; bottom:0; z-index:10002` |

### Animations

| Keyframe | Used on | Effect |
|---|---|---|
| `wellme-slide-in` | Modal inner, outcome panel | `translateX(100%)` → `translateX(0)` |
| `wellme-fade-in` | Chapter panel, step panel | Opacity + Y shift on show |
| `wellme-pulse` | Hotspot pulse ring | Scale 1→2.2, opacity 0.7→0, 2 s loop |

### Reduced motion

`@media (prefers-reduced-motion: reduce)` overrides:
- `.wellme-scroll-reveal` — immediately visible
- `.wellme-flipcard-inner` — no transition
- `.wellme-hotspot-pulse` — animation disabled
- `.wellme-pamphlet-modal-inner`, `.wellme-outcome-panel` — no slide animation
- `.wellme-experience-track`, `.wellme-experience-bg`, `.wellme-experience-content`, `.wellme-exp-drawer` — no transitions; content immediately visible

---

## 12. JavaScript Architecture

`public/js/wellme-pamphlets-public.js` is a single IIFE (Immediately Invoked Function Expression) in `'use strict'` mode. No jQuery, no build step, no module bundler.

### Entry point

```js
document.addEventListener('DOMContentLoaded', function () {
    initScrollReveal();
    initModuleGrid();
    initFlipCards();
    initExperience();
    initPamphletInteractions(document); // standalone [wellme_pamphlet] shortcode
});
```

### Function index

| Function | Triggered by | Purpose |
|---|---|---|
| `show(el)` | Internal | Removes `hidden` attribute |
| `hide(el)` | Internal | Sets `hidden = true` |
| `trapFocus(el)` | Modal / drawer open | Cycles Tab focus within a container |
| `initScrollReveal()` | DOMContentLoaded; post-AJAX | Sets up IntersectionObserver on `.wellme-scroll-reveal` |
| `initModuleGrid()` | DOMContentLoaded | Wires card clicks → AJAX → modal; wires close triggers |
| `initPamphletInteractions(root)` | Post-AJAX; DOMContentLoaded | Calls chapter, outcome, hotspot inits scoped to `root` |
| `initChapterNav(root)` | via `initPamphletInteractions` | Activates chapter buttons; shows/hides panels |
| `initOutcomePanels(root)` | via `initPamphletInteractions` | Toggles outcome side-panels; Escape handler |
| `initHotspots(root)` | via `initPamphletInteractions` | Dot click → step panel; Prev/Next nav; sync `aria-expanded` |
| `initFlipCards()` | DOMContentLoaded | Click / Enter / Space → `.is-flipped` toggle |
| `initExperience()` | DOMContentLoaded | Full-screen slider: `goTo()`, drawer open/close, swipe, keyboard |

### `initExperience()` internals

```
goTo(index)
  ├── removes .is-active from current slide + dot
  ├── sets .is-active on new slide + dot
  ├── sets track.style.transform
  ├── updates counter text
  └── toggles prev/next button hidden state

openDrawer(moduleId, triggerBtn)
  ├── removes [hidden], adds .is-open, sets body overflow:hidden
  ├── if drawerBody.dataset.loadedId === moduleId → skip fetch, focus close btn
  └── fetch() → inject HTML → initPamphletInteractions(drawerBody) → focus close btn

closeDrawer()
  ├── removes .is-open, restores body overflow
  ├── listens for transitionend → sets [hidden]
  └── returns focus to current slide's Explore button
```

### AJAX caching

The experience drawer caches the loaded pamphlet HTML per module ID in `drawerBody.dataset.loadedId`. The grid modal clears its content on every open (`content.innerHTML = ''`), so it always re-fetches.

---

## 13. ACF Field Reference

Complete list of all fields registered in `class-wellme-pamphlets-acf.php`. The field group key is `group_wellme_module`.

| ACF key | Field name | Type | Parent |
|---|---|---|---|
| `field_wm_tab_identity` | — | Tab | Group root |
| `field_wm_number` | `module_number` | number | Identity tab |
| `field_wm_subtitle` | `module_subtitle` | text | Identity tab |
| `field_wm_description` | `module_description` | textarea | Identity tab |
| `field_wm_color` | `module_color` | color_picker | Identity tab |
| `field_wm_icon` | `module_icon` | image | Identity tab |
| `field_wm_cover_image` | `module_cover_image` | image | Identity tab |
| `field_wm_motto` | `module_motto` | text | Identity tab |
| `field_wm_tab_outcomes` | — | Tab | Group root |
| `field_wm_learning_outcomes` | `module_learning_outcomes` | repeater | Outcomes tab |
| `field_wm_outcome_title` | `outcome_title` | text | Outcomes repeater |
| `field_wm_outcome_detail` | `outcome_detail` | wysiwyg | Outcomes repeater |
| `field_wm_outcome_icon` | `outcome_icon` | image | Outcomes repeater |
| `field_wm_tab_steps` | — | Tab | Group root |
| `field_wm_exercise_steps` | `module_exercise_steps` | repeater | Steps tab |
| `field_wm_step_title` | `step_title` | text | Steps repeater |
| `field_wm_step_content` | `step_content` | wysiwyg | Steps repeater |
| `field_wm_step_image` | `step_image` | image | Steps repeater |
| `field_wm_step_hotspot_x` | `step_hotspot_x` | number | Steps repeater |
| `field_wm_step_hotspot_y` | `step_hotspot_y` | number | Steps repeater |
| `field_wm_tab_chapters` | — | Tab | Group root |
| `field_wm_chapters` | `module_chapters` | repeater | Chapters tab |
| `field_wm_chapter_title` | `chapter_title` | text | Chapters repeater |
| `field_wm_chapter_content` | `chapter_content` | wysiwyg | Chapters repeater |
| `field_wm_chapter_image` | `chapter_image` | image | Chapters repeater |
| `field_wm_tab_media` | — | Tab | Group root |
| `field_wm_video_url` | `module_video_url` | url | Media tab |
| `field_wm_gallery` | `module_gallery` | gallery | Media tab |

All image fields use `'return_format' => 'array'` so templates access `$field['url']`, `$field['sizes']['medium']`, `$field['alt']` etc.

---

## 14. Automatic Updates

The plugin uses [YahnisElsts/plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) (v5.6, installed via Composer) pointed at the GitHub repository.

The update checker is configured in `Wellme_Pamphlets::setup_update_checker()`:

```php
$update_checker = PucFactory::buildUpdateChecker(
    'https://github.com/GeorgeWebDevCy/wellme-pamphlets/',
    WELLME_PAMPHLETS_PLUGIN_FILE,
    'wellme-pamphlets'
);
$update_checker->getVcsApi()->enableReleaseAssets( '/^wellme-pamphlets\.zip$/i' );
```

`enableReleaseAssets()` is restricted to `wellme-pamphlets.zip`, so WordPress updates use the packaged plugin archive instead of an arbitrary release asset.

### Releasing an update

1. Make and commit all changes.
2. Bump `WELLME_PAMPHLETS_VERSION` in `wellme-pamphlets.php`.
3. Commit the version bump and push to `main`.
4. Build the plugin ZIP with:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/build-plugin.ps1
```

5. On GitHub, create a new **Release** with a version tag matching the pattern `v1.x.x` (e.g. `v1.1.0`).
6. Attach `dist/wellme-pamphlets.zip` as the release asset.

Do not use GitHub's auto-generated source ZIP for manual installs or release assets. The packaged ZIP contains the plugin files at the archive root so it can be extracted directly into the existing `wellme-pamphlets` plugin directory without creating a nested folder.

WordPress installations with the plugin active will see the update notification in **Plugins → WELLME Pamphlets** within the next WordPress update check cycle (default: 12 hours).

---

## 15. Internationalisation

- **Text domain:** `wellme-pamphlets`
- **Domain path:** `/languages/`
- Loaded on `plugins_loaded` via `load_plugin_textdomain()`
- All user-facing PHP strings use `__()`, `_e()`, `esc_html__()`, or `esc_html_e()`
- The JS `loading` string is passed from PHP via `wp_localize_script()` so it is also translatable

### Generating the .pot file

```bash
# WP-CLI (recommended)
wp i18n make-pot . languages/wellme-pamphlets.pot --domain=wellme-pamphlets

# Or use Loco Translate plugin from the WordPress admin
```

Place compiled `.mo` files in `/languages/wellme-pamphlets-{locale}.mo` (e.g. `wellme-pamphlets-el_GR.mo` for Greek).

---

## 16. Accessibility

| Feature | Implementation |
|---|---|
| Keyboard navigation | All interactive elements are reachable via `Tab`; cards and flip cards have `role="button"` and `tabindex="0"` with `Enter`/`Space` handlers |
| ARIA roles | `role="dialog"` on modals and drawers; `role="region"` on outcome panels and the experience container |
| ARIA states | `aria-expanded` on all toggle triggers; `aria-modal="true"` on modal/drawer; `aria-current` on active experience dot; `aria-live="polite"` on experience counter |
| ARIA labels | `aria-label` on all icon-only buttons (close ×, arrows, dots); descriptive labels on cards and flip cards |
| Focus trapping | `trapFocus()` called when modal opens; cycles between first and last focusable element |
| Focus return | Focus returns to the trigger element when modal, drawer, or side-panel closes |
| Reduced motion | `@media (prefers-reduced-motion: reduce)` disables all CSS animations and transitions |
| Screen readers | `aria-hidden="true"` on decorative images, background divs, and SVG icons |
| Scroll reveal fallback | Elements shown immediately if `IntersectionObserver` is unavailable |

---

## 17. Design Reference

The interactive patterns were derived from Maglr digital brochure examples referenced in the project brief. Each brochure was scraped with Playwright (headless Chromium) to analyse the DOM structure, CSS classes, and interaction model before implementation.

| Pattern | Source brochure | Used for |
|---|---|---|
| Chapter nav buttons + content panels | Partou (pedagogiek.partou.nl) | Chapter navigation inside each pamphlet |
| Clickable outcome buttons → slide-in side panel | Partou age-group selector | Learning outcomes |
| Numbered pulsing hotspot dots on image | Outremer 55 (catamaran-outremer.maglr.com) | Exercise steps |
| CSS 3D flip cards (click to reveal) | Specified in project brief | Sum-Up slide mottos (`[wellme_flipcards]`) |
| 6-card column grid | Mazda MX-5 (prijzen.mazda.nl) | Module index grid (`[wellme_module_grid]`) |
| Publication reader carousel | Mazda MX-5 / Publitas reader (prijzen.mazda.nl) | `[wellme_experience]` |

---

## 18. Troubleshooting

### ACF fields are not showing on the module edit screen

**Cause:** ACF Pro is not active, or it was activated after this plugin.
**Fix:** Ensure ACF Pro is installed and activated. Deactivate and reactivate WELLME Pamphlets. The `acf/init` hook fires after `init`, so ACF must be loaded first.

### Shortcode outputs nothing / "No modules found"

**Cause:** No published `wellme_module` posts exist, or WPML is filtering them to a language with no translations.
**Fix:** Go to **WELLME Modules** and confirm posts exist with **Published** status. If using WPML, create module translations for the active language.

### AJAX returns "Invalid module ID" or fails silently

**Cause:** Nonce mismatch, or the page is served from a full-page cache that baked in a stale nonce.
**Fix:** Exclude pages with the `[wellme_module_grid]` or `[wellme_experience]` shortcode from full-page caching (WP Rocket, W3 Total Cache, etc.), or configure the cache plugin to regenerate nonces on each request.

### Pamphlet modal slides in but content area is blank

**Cause:** The AJAX request succeeded but `initPamphletInteractions()` encountered an error (e.g. duplicate panel IDs from two `[wellme_module_grid]` shortcodes on the same page).
**Fix:** Use only one `[wellme_module_grid]` per page. Check the browser console for JS errors.

### Hotspot dots appear in the wrong position

**Cause:** The ACF `step_hotspot_x` / `step_hotspot_y` values were set based on a different image crop or aspect ratio.
**Fix:** The dots use `left: X%; top: Y%` on the `.wellme-hotspot-map` container, which is sized to the cover image. Adjust the percentage values in the ACF fields to match the actual rendered position. `50 / 50` centres the dot.

### The `[wellme_experience]` does not fill the full screen

**Cause:** The WordPress theme is constraining the page content width/height.
**Fix:** Assign the page a blank / full-width canvas template that removes the theme header and footer. In Divi, use **Page Attributes → Template → Blank Page**. The shortcode uses `width: 100vw; margin-left: calc(-50vw + 50%)` to break out of the content column, but the theme must not apply `overflow: hidden` to an ancestor element.

### Flip cards don't flip in Safari

**Cause:** Older Safari versions require `-webkit-backface-visibility` (already set in the CSS) and may have issues with `transform-style: preserve-3d` inside certain stacking contexts.
**Fix:** Ensure no ancestor of `.wellme-flipcard` has `transform`, `will-change`, or `filter` CSS applied by the theme. If the issue persists, add `transform: translateZ(0)` to `.wellme-flipcard`.

### Updates are not appearing in the Plugins screen

**Cause:** WordPress caches update checks for 12 hours. Or the GitHub release was not tagged correctly.
**Fix:** In the WordPress admin, go to **Dashboard → Updates** and click **Check Again**. Ensure the GitHub release tag matches the version format (e.g. `v1.1.0`) and that `WELLME_PAMPHLETS_VERSION` in `wellme-pamphlets.php` was bumped to match.
