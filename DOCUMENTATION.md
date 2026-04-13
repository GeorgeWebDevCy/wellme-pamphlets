# WELLME Pamphlets — Plugin Documentation

## Overview

A WordPress plugin that delivers interactive digital pamphlets for the WELLME EU training project. Trainers use the pamphlets during a two-day training in Poland. The plugin is independent of the Divi theme and works via shortcodes placed on any page.

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

## Requirements

| Requirement | Version |
|---|---|
| PHP | 7.4+ |
| WordPress | 6.2+ |
| Advanced Custom Fields PRO | 6.8+ |

ACF Pro must be installed and activated **before** this plugin. The field groups are registered entirely in PHP — no manual ACF UI setup is needed.

---

## Installation

1. Copy the `wellme-pamphlets/` folder to `/wp-content/plugins/`.
2. Install and activate **Advanced Custom Fields PRO** (provided in `refrences doc/advanced-custom-fields-pro/`).
3. Activate **WELLME Pamphlets** from the WordPress Plugins screen.
4. Go to **WELLME Modules** in the admin menu and create the 6 modules.
5. Place shortcodes on your pages (see below).

> The plugin auto-registers its update checker against `https://github.com/GeorgeWebDevCy/wellme-pamphlets`. Pushing a new GitHub release/tag will surface the update in the WordPress Plugins screen.

---

## Shortcodes

### `[wellme_module_grid]`

Renders the 6-card module index — the main entry point for visitors. Each card opens its full pamphlet in a slide-in modal via AJAX.

```
[wellme_module_grid]
```

**Optional attributes:**

| Attribute | Default | Description |
|---|---|---|
| `columns` | `3` | Number of grid columns |
| `orderby` | `meta_value_num` | WP_Query orderby — orders by module number |
| `order` | `ASC` | `ASC` or `DESC` |

---

### `[wellme_pamphlet id=X]`

Renders the full interactive pamphlet for a single module. Use this to embed a specific module on its own page.

```
[wellme_pamphlet id="42"]
[wellme_pamphlet slug="module-1-from-strength-to-strength"]
```

**Attributes:**

| Attribute | Description |
|---|---|
| `id` | WordPress post ID of the `wellme_module` post |
| `slug` | Post slug (alternative to `id`) |

---

### `[wellme_flipcards]`

Renders the Sum-Up slide — 6 flip cards. Front shows the module cover image and title; click/tap to flip and reveal the module motto on the back.

```
[wellme_flipcards]
```

No attributes. Always shows all published modules ordered by module number.

---

## Admin: Creating a Module

Go to **WELLME Modules → Add Module** in the WordPress admin. The post title is the module title. All other content is managed through ACF field groups organised into tabs:

### Tab: Identity

| Field | Type | Description |
|---|---|---|
| Module Number | Number (1–6) | Controls display order |
| Subtitle | Text | Short tagline shown on the card |
| Short Description | Textarea | Shown on the card and pamphlet intro |
| Module Colour | Colour Picker | Accent colour for cards, hotspot dots and buttons |
| Module Icon | Image | SVG or PNG icon on the module card |
| Cover / Hero Image | Image | Full-width hero used on the cover slide and as the hotspot map background |
| Module Motto | Text | Revealed on the back of the flip card in the Sum-Up slide |

### Tab: Learning Outcomes

A **repeater field** — each row becomes a clickable button that opens a side-panel overlay (inspired by the Partou brochure pattern).

| Sub-field | Type | Description |
|---|---|---|
| Outcome Title | Text | Button label and panel heading |
| Outcome Detail | WYSIWYG | Rich content shown inside the panel |
| Icon (optional) | Image | Small icon shown on the outcome button |

### Tab: Exercise Steps

A **repeater field** — each row gets a numbered pulsing hotspot dot placed on the cover image. Clicking the dot opens a step panel below the map (inspired by the Outremer brochure pattern).

| Sub-field | Type | Description |
|---|---|---|
| Step Title | Text | Shown in the panel header |
| Step Content | WYSIWYG | Rich content for this step |
| Step Image | Image | Optional image inside the step panel |
| Hotspot X Position (%) | Number (0–100) | Horizontal position of the dot on the cover image |
| Hotspot Y Position (%) | Number (0–100) | Vertical position of the dot on the cover image |

> **Tip:** Set hotspot positions by previewing the cover image and estimating percentages. `50 / 50` places the dot in the centre.

### Tab: Chapters

A **repeater field** — each row becomes a pill-shaped chapter navigation button. Clicking a button shows that chapter's content panel below the nav (inspired by the Partou chapter-nav pattern).

| Sub-field | Type | Description |
|---|---|---|
| Chapter Title | Text | Button label |
| Chapter Content | WYSIWYG | Rich content for this chapter |
| Chapter Image | Image | Optional image at the top of the chapter panel |

### Tab: Media

| Field | Type | Description |
|---|---|---|
| Module Video URL | URL | YouTube or Vimeo — auto-embedded via `wp_oembed_get()` |
| Photo Gallery | Gallery | Additional images displayed in a 3-column grid |

---

## Interactive Components

### Module Grid Cards

- 3-column responsive grid (2-col on tablet, 1-col on mobile)
- Each card has a hover lift + image zoom effect
- Clicking a card fetches the pamphlet HTML via AJAX and opens it in a full-height slide-in modal
- Modal traps keyboard focus for accessibility
- Close with the × button, overlay click, or `Escape`

### Pamphlet Modal

- Slides in from the right (860px max-width)
- Contains the full pamphlet with all sections below

### Chapter Navigation (Partou pattern)

- Pill-shaped buttons at the top of the Chapters section
- Clicking a button shows that chapter's content panel and hides the others
- First chapter is open by default
- Active state highlighted in the module accent colour

### Learning Outcomes Side Panel (Partou pattern)

- Outcome buttons in a wrapping flex list
- Clicking opens a fixed side-panel that slides in from the right
- Only one panel open at a time — opening another closes the previous
- Close with the × button or `Escape`
- `aria-expanded` toggled for screen readers

### Exercise Step Hotspots (Outremer pattern)

- Numbered pulsing dots positioned absolutely over the cover image
- Each dot has a continuous pulse ring animation in the module accent colour
- Clicking a dot opens the step panel below the map
- Step panels have Prev / Next navigation to move through steps sequentially
- Only one step panel open at a time
- `aria-expanded` toggled for screen readers

### Flip Cards (Sum-Up slide)

- 3-column grid of cards (2-col tablet, 1-col mobile)
- CSS 3D flip on click or `Enter`/`Space` keypress
- Front: cover image + module number + title
- Back: module colour background + motto text
- `is-flipped` class toggled for state; no JS animation library required

### Scroll Reveal

- All major elements have the `wellme-scroll-reveal` class
- `IntersectionObserver` adds `is-visible` when the element enters the viewport
- Falls back gracefully (shows immediately) in browsers without `IntersectionObserver`
- Respects `prefers-reduced-motion` — animations disabled for users who opt out

---

## File Structure

```
wellme-pamphlets/
├── wellme-pamphlets.php              Main plugin file (plugin header, constants, bootstrap)
├── composer.json                     Declares plugin-update-checker dependency
├── composer.lock
├── uninstall.php                     Cleanup on plugin deletion
├── index.php                         Security silence file
│
├── includes/
│   ├── class-wellme-pamphlets.php         Core class — wires all hooks
│   ├── class-wellme-pamphlets-loader.php  Hook registration collector
│   ├── class-wellme-pamphlets-i18n.php    Text domain loader
│   ├── class-wellme-pamphlets-activator.php
│   ├── class-wellme-pamphlets-deactivator.php
│   ├── class-wellme-pamphlets-cpt.php     Registers wellme_module CPT
│   ├── class-wellme-pamphlets-acf.php     Registers all ACF field groups in PHP
│   └── class-wellme-pamphlets-shortcodes.php  [wellme_module_grid], [wellme_pamphlet], [wellme_flipcards]
│
├── public/
│   ├── class-wellme-pamphlets-public.php  Enqueues assets, AJAX handler
│   ├── css/
│   │   └── wellme-pamphlets-public.css    All interactive styles
│   ├── js/
│   │   └── wellme-pamphlets-public.js     Vanilla JS — no jQuery dependency
│   └── partials/
│       ├── wellme-module-grid.php         Module card grid + modal container
│       ├── wellme-pamphlet.php            Full pamphlet template (all sections)
│       └── wellme-flipcards.php           Sum-Up flip cards
│
├── admin/
│   ├── class-wellme-pamphlets-admin.php
│   ├── css/wellme-pamphlets-admin.css
│   └── js/wellme-pamphlets-admin.js
│
├── languages/                        .pot files go here
│
└── vendor/                           Composer packages (plugin-update-checker)
    └── yahnis-elsts/plugin-update-checker/
```

---

## Automatic Updates

The plugin uses [YahnisElsts/plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) (v5.6, installed via Composer) pointed at the GitHub repository.

**To release an update:**

1. Bump `WELLME_PAMPHLETS_VERSION` in `wellme-pamphlets.php`.
2. Commit and push.
3. Create a new GitHub release with a version tag (e.g. `v1.1.0`).

WordPress will detect the new release and surface the update in the Plugins screen automatically.

---

## Design Reference

The interactive patterns were derived by scraping the Maglr digital brochure examples referenced in the project brief:

| Pattern | Source brochure | Used for |
|---|---|---|
| Chapter nav buttons + content panels | Partou (pedagogiek.partou.nl) | Chapter navigation inside each pamphlet |
| Clickable outcome buttons → side panel | Partou age-group selector | Learning outcomes |
| Numbered pulsing hotspot dots on image | Outremer 55 (catamaran-outremer.maglr.com) | Exercise steps |
| CSS 3D flip cards | Specified in project brief | Sum-Up slide mottos |
| 6-card column grid | Mazda MX-5 (prijzen.mazda.nl) | Module index grid |

---

## CSS Custom Properties

Each module card and pamphlet sets `--module-color` from the **Module Colour** ACF field. All interactive elements (hotspot dots, buttons, chapter pills, step number circles, flip card backs) inherit this value, so each module has a consistent accent colour throughout.

```css
/* Applied inline on each component root */
style="--module-color: #e63946;"
```

Global fallback defined in `:root`:

```css
:root {
    --wellme-primary: #005b96;
}
```

---

## Accessibility

- All interactive elements are keyboard-navigable (`tabindex="0"`, `Enter`/`Space` handlers)
- `aria-expanded`, `aria-controls`, `aria-label` set throughout
- Focus is trapped inside open modals and panels
- `prefers-reduced-motion` disables all CSS animations and transitions
- `IntersectionObserver` scroll reveal degrades gracefully
