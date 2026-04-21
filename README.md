# WELLME Pamphlets

A WordPress plugin that delivers interactive digital pamphlets for the **WELLME EU training project**. Trainers use the pamphlets during a two-day training in Poland. The plugin is independent of the Divi theme and works via shortcodes placed on any page.

---

## Project Context

The WELLME project has **6 training modules** for youth trainers:

| # | Module Title |
|---|---|
| 1 | From Strength To Strength: Positive Psychology For Youth Trainers |
| 2 | Bounce Back Stronger |
| 3 | Fuel for Flourishing: Healthy Nutrition & Lifestyle in Youth Work |
| 4 | Move to Thrive: Integrating Physical Activity into Youth Wellbeing |
| 5 | Spaces that Connect: Designing Environments for Youth Belonging |
| 6 | Bridges to Adulthood: Guiding Youth Transitions through Community Power |

Each module is a fully interactive digital pamphlet with chapter navigation, learning outcome panels, hotspot exercise steps, and a flip-card summary slide.

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | 7.4+ |
| WordPress | 6.2+ |
| Advanced Custom Fields PRO | 6.8+ |

ACF Pro must be installed and activated **before** this plugin. All ACF field groups are registered in PHP — no manual setup in the ACF UI is needed.

---

## Installation

1. Copy the plugin folder to `/wp-content/plugins/wellme-pamphlets/`.
2. Install and activate **Advanced Custom Fields PRO**.
3. Activate **WELLME Pamphlets** from the WordPress Plugins screen.
4. Go to **WELLME Modules** in the admin menu and create the 6 modules.
5. Place shortcodes on your pages (see below).

---
push and commit
## Module Import Workflow

The plugin can now import the six WELLME learning modules from the local source documents.

### 1. Build an import package locally

From this repository, run:

```powershell
python scripts/build-import-package.py --source-root "C:\Users\georg\Downloads\WellMe-20260413T092223Z-3-001\WellMe" --output "dist\wellme-import-package.zip"
```

This reads the WP2.4 learning module `.docx` files, extracts structured content plus embedded images, and builds a ZIP package with a `manifest.json` file for WordPress import.

### 2. Upload the package in WordPress

In the WordPress admin, go to **WELLME Modules -> Import Modules** and upload the generated ZIP.

The importer will create or update `wellme_module` posts by module number and populate:

- module title and slug
- subtitle, description, color, and motto defaults
- learning outcomes
- chapters
- exercise steps
- cover image and gallery images

### Notes

- The current importer uses `WP2.4_Design_Learning_Modules` as the primary source of truth.
- Hotspot coordinates, icons, and some cover-image choices may still need manual review after import.
- ACF Pro must be active before running the importer.

---

## Shortcodes

### `[wellme_module_grid]`

Renders the 6-card module index. Each card opens its full pamphlet in a slide-in modal via AJAX.

```
[wellme_module_grid]
[wellme_module_grid columns="2"]
```

| Attribute | Default | Description |
|---|---|---|
| `columns` | `3` | Number of grid columns |
| `orderby` | `meta_value_num` | WP_Query orderby |
| `order` | `ASC` | `ASC` or `DESC` |

### `[wellme_pamphlet]`

Renders the full interactive pamphlet for a single module. Use this to embed a specific module on its own dedicated page.

```
[wellme_pamphlet id="42"]
[wellme_pamphlet slug="module-1-from-strength-to-strength"]
```

| Attribute | Description |
|---|---|
| `id` | WordPress post ID of the module |
| `slug` | Post slug (alternative to `id`) |

### `[wellme_flipcards]`

Renders the Sum-Up slide — 6 CSS 3D flip cards. Front shows the module cover image and title; click/tap to reveal the module motto on the back.

```
[wellme_flipcards]
```

No attributes. Always shows all published modules ordered by module number.

### `[wellme_experience]`

Full-viewport interactive experience with a Mazda / Publitas-inspired reader layout. Each major section appears as a centered publication page with edge chevrons, dot navigation, and a page counter. Module cards inside the reader load the full interactive pamphlet without leaving the page.

```
[wellme_experience]
```

No attributes. Always shows all published modules in order.

**Recommended page setup:** use a page template that removes the theme header and footer (a "blank" or "full-width canvas" template) so the experience fills the entire browser window edge-to-edge. In Divi, set the page to use the *Blank Page* template under Page Attributes.

**Navigation:**
- Large left / right edge chevrons
- Dot indicators (bottom centre)
- Keyboard arrow keys (← →)
- Touch swipe on mobile

---

## Interactive Features

### Module Grid Cards

- 3-column responsive grid (2-col tablet, 1-col mobile)
- Hover lift and image zoom effect
- Click to load the full pamphlet via AJAX into a slide-in modal
- Modal traps keyboard focus; close with ×, overlay click, or `Escape`

### Chapter Navigation

Pill-shaped chapter buttons at the top of each pamphlet. Clicking a button shows that chapter's content panel and hides the rest. First chapter is open by default. Active state highlighted in the module's accent colour.

### Learning Outcomes Side Panel

Outcome buttons open a fixed side-panel that slides in from the right. Only one panel is open at a time — opening another closes the previous. Closeable with × or `Escape`.

### Exercise Step Hotspots

Numbered pulsing dots are positioned over the cover image at coordinates set in the ACF fields. Clicking a dot opens a step panel below the map with Prev / Next navigation.

### Flip Cards (Sum-Up Slide)

CSS 3D flip on click, `Enter`, or `Space`. Front: cover image + module title. Back: module accent colour + module motto.

### Scroll Reveal

All major elements animate in as they enter the viewport via `IntersectionObserver`. Respects `prefers-reduced-motion` — animations are fully disabled for users who opt out.

---

## Accent Colours

Each module card and pamphlet sets a `--module-color` CSS custom property from the **Module Colour** ACF field. All interactive elements (hotspot dots, buttons, chapter pills, flip card backs) inherit this value automatically.

```css
style="--module-color: #e63946;"
```

---

## Automatic Updates

The plugin uses [YahnisElsts/plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) pointed at this GitHub repository. WordPress will surface new releases in the Plugins screen automatically.

**To release an update:**

1. Bump `WELLME_PAMPHLETS_VERSION` in `wellme-pamphlets.php`.
2. Commit and push.
3. Build the release ZIP:

   ```powershell
   powershell -ExecutionPolicy Bypass -File scripts/build-plugin.ps1
   ```

4. Create a new GitHub release with a version tag (e.g. `v1.1.0`).
5. Attach `dist/wellme-pamphlets.zip` to that release, or upload the same ZIP directly in WordPress via **Plugins -> Add New Plugin -> Upload Plugin**.

Do not use GitHub's auto-generated source ZIP for manual installs. Use `dist/wellme-pamphlets.zip`, which preserves the plugin root folder that WordPress expects.

---

## File Structure

```
wellme-pamphlets/
├── wellme-pamphlets.php              Main plugin file
├── composer.json
├── composer.lock
├── uninstall.php
│
├── includes/
│   ├── class-wellme-pamphlets.php
│   ├── class-wellme-pamphlets-loader.php
│   ├── class-wellme-pamphlets-i18n.php
│   ├── class-wellme-pamphlets-activator.php
│   ├── class-wellme-pamphlets-deactivator.php
│   ├── class-wellme-pamphlets-cpt.php         Registers wellme_module CPT
│   ├── class-wellme-pamphlets-acf.php         Registers all ACF field groups
│   └── class-wellme-pamphlets-shortcodes.php
│
├── public/
│   ├── class-wellme-pamphlets-public.php      Asset enqueue + AJAX handler
│   ├── css/wellme-pamphlets-public.css
│   ├── js/wellme-pamphlets-public.js          Vanilla JS — no jQuery
│   └── partials/
│       ├── wellme-module-grid.php
│       ├── wellme-pamphlet.php
│       └── wellme-flipcards.php
│
├── admin/
│   ├── class-wellme-pamphlets-admin.php
│   ├── css/wellme-pamphlets-admin.css
│   └── js/wellme-pamphlets-admin.js
│
└── vendor/                                    Composer packages
    └── yahnis-elsts/plugin-update-checker/
```

---

## WPML Multilingual Support

The plugin is WPML-compatible out of the box:

- **Post type** — `wellme_module` is translatable. Translators can create a translated copy of each module post via the WPML Translation Editor.
- **ACF fields** — all fields are registered in code via `acf_add_local_field_group()`. WPML's **String Translation** and **ACF + WPML** integration surface these fields for translation automatically once the field group is configured in **WPML → Custom Fields Translation**.
- **Queries** — all `get_posts()` calls respect the active WPML language filter automatically.
- **AJAX pamphlet loader** — resolves incoming post IDs to the correct translated post for the current language via the `wpml_object_id` filter before rendering.
- **Strings** — all user-facing strings are wrapped in `__()` / `esc_html_e()` with the `wellme-pamphlets` text domain, ready for `.pot` generation.

> In the WPML admin, go to **WPML → Custom Fields Translation** and set all `module_*` fields to **Copy** or **Translate** as appropriate for your workflow.

---

## Full Documentation

See [DOCUMENTATION.md](DOCUMENTATION.md) for the complete reference including all ACF field definitions, accessibility notes, and design pattern sources.

---

## License

GPL-2.0-or-later
