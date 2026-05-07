# WellMe Pamphlets — Redesign & Content Fix Design Spec

**Date:** 2026-05-07  
**Live URL:** https://www.wellmeproject.com/wellme-pamphlets/  
**Brand reference:** https://www.wellmeproject.com/  
**Status:** Approved

---

## Goal

Make the WellMe Pamphlets plugin look and feel like the rest of wellmeproject.com, and verify all 6 module ACF fields match the canonical data in `PAMPHLETS_DATA_SHEET.xlsx`.

---

## Part 1 — Design

### Brand tokens (already in CSS, confirmed correct)

| Token | Value |
|---|---|
| `--wellme-primary` | `#26aafc` |
| `--wellme-secondary` | `#ff54b0` |
| `--wellme-success` | `#138a52` |
| `--wellme-danger` | `#c43d57` |
| `--wellme-radius` | `12px` |

Module colour rotation for 6 cards (fallback when `module_color` not set):

| Module | Color |
|---|---|
| 1 | `#26aafc` (brand blue) |
| 2 | `#ff54b0` (brand pink) |
| 3 | `#138a52` (green) |
| 4 | `#e67e22` (orange) |
| 5 | `#8e44ad` (purple) |
| 6 | `#c43d57` (red) |

### Slide 1 — Landing

**Before:** solid `#e5e5e5` grey background, spinning logo, `letter-spacing:.15em; text-transform:uppercase` heading.

**After:**
- Background: `linear-gradient(135deg, #26aafc 0%, #1a7bbe 100%)`
- All text: white
- Title: `font-weight:700`, no uppercase, no extreme letter-spacing
- CTA button: white pill `border-radius:24px`, `color:#26aafc`
- Wave divider at bottom: white `clip-path:ellipse(55% 100% at 50% 100%)`
- Logo: centred, no spin animation (remove `wellme-logo-spin` class from landing)
- EU funding text: small, `rgba(255,255,255,.7)`

**Files:** `public/css/wellme-pamphlets-public.css` (`.wellme-slide-landing`, `.wellme-landing-bg`, `.wellme-landing-title`, `.wellme-landing-continue`, `.wellme-logo-spin`)

### Slide 2 — Partnership

**Before:** grey `#e5e5e5` background, dark `#666` left card, list-style partner rows.

**After:**
- Background: white
- Left panel: `linear-gradient(160deg, #1a1a2e 0%, rgba(38,170,252,.13) 100%)`, white text, blue kicker label
- Right panel: partner grid cards — white `border:1px solid #eee`, `border-radius:8px`, logo + name
- Partner detail panel (expanded): white, blue accent border-left `4px solid #26aafc`
- Navigation choice buttons: white cards with number in `#26aafc`, hover border `#26aafc`

**Files:** `public/css/wellme-pamphlets-public.css` (`.wellme-slide-partnership`, `.wellme-partnership-content`, `.wellme-partner-card`, `.wellme-partner-detail`, `.wellme-partnership-choice`)

### Slide 3 — Overview

**Before:** light grey bg, random tab button colors (pink `#c6548f`, blue `#1e88c8`, green `#27ae60`), uppercase spaced title.

**After:**
- Background: white
- Title: normal weight, no uppercase, `color:#1a1a2e`
- Tab buttons (`.wellme-mazda-page-tab`): active → `background:#26aafc; color:white; border-radius:20px`; inactive → `background:#f0f0f0; color:#555; border-radius:20px`
- Section label/kicker: `color:#26aafc; text-transform:uppercase; font-size:.7rem; letter-spacing:.08em`
- Overview selector buttons: all use `#26aafc` regardless of per-item color (remove random `--overview-color` from active state border/background, keep for accent dot only)
- Image frame: `border-radius:10px; overflow:hidden`

**Files:** `public/css/wellme-pamphlets-public.css` (`.wellme-slide-overview`, `.wellme-mazda-page-tab`, `.wellme-overview-selector`, `.wellme-overview-section`)

### Slide 4 — Modules

**Before:** dark `#1a1a2e` background, dark gradient cards (teal/purple/navy per module).

**After:**
- Background: white
- Module card top band: `linear-gradient(135deg, var(--module-color), color-mix(in srgb, var(--module-color) 70%, black 30%))`, height `80px`
- Card body: white, `border:1px solid #eee`, `border-radius:10px`
- Card number label: `color: var(--module-color)`, small uppercase
- Card title: `color:#1a1a2e`, `font-weight:700`
- "View" CTA button: pill, `background: var(--module-color)`, white text
- Module tab nav (`.wellme-mazda-page-tab` in modules): same brand blue style as overview tabs
- Section heading: `color:#1a1a2e`, normal case

**Files:** `public/css/wellme-pamphlets-public.css` (`.wellme-slide-modules`, `.wellme-modules-slide-bg`, `.wellme-modules-slide-content`, `.wellme-module-inline-card`, `.wellme-module-inline-media`)

### Slide 5 — Sum-Up (Flip Cards)

**Before:** white bg, flip card fronts show full cover photo with module label at bottom only; no brand treatment.

**After:**
- Card front: `linear-gradient(135deg, var(--module-color), color-mix(in srgb, var(--module-color) 70%, black 30%))`, module number + title in white, cover image as subtle background overlay (`opacity:.25`)
- Card back: white background, `color:#1a1a2e`, motto text in `font-style:italic; font-size:1rem`, module number label in `var(--module-color)`
- Card border-radius: `12px`
- Section title: `color:#1a1a2e`, normal case

**Files:** `public/css/wellme-pamphlets-public.css` (`.wellme-flipcard-front`, `.wellme-flipcard-back`, `.wellme-flipcard-number`, `.wellme-flipcard-motto`)

### Module Popup (Pamphlet)

**Before:** left panel white with cover image overlay, title in giant `font-size:2.5rem+ text-transform:uppercase font-weight:900`; right panel grey `#e5e5e5`.

**After:**
- Left (content) panel: white background
- Right (nav/info) panel: `#f8fafc` (very light blue-grey)
- Module label: `color: var(--module-color); font-size:.7rem; text-transform:uppercase; letter-spacing:.1em; font-weight:700`
- Title (`h2.wellme-popup-title`): `font-size:1.3rem; font-weight:700; color:#1a1a2e; text-transform:none; letter-spacing:normal`
- Back button: `border:1px solid #eee; border-radius:20px; color:#555; font-size:.75rem`
- Close button: `color:#555`
- Section headings inside pamphlet: `font-weight:700; color:#1a1a2e; font-size:1rem`
- Section label accent: `color: var(--module-color); font-size:.7rem; text-transform:uppercase`
- TOC box: `background:#f8fafc; border-radius:8px; border-left:3px solid var(--module-color)`
- Hotspot dots: `background: var(--module-color)` (already using CSS var, keep)

**Files:** `public/css/wellme-pamphlets-public.css` (`.wellme-popup-right`, `.wellme-popup-left`, `.wellme-popup-title`, `.wellme-popup-module-label`, `.wellme-cover-title`, `.wellme-cover-number`)

### Typography

The plugin should inherit whatever the theme sets for body font. No override needed — wellmeproject.com uses Poppins loaded via the theme. The plugin must NOT hardcode a font-family that conflicts.

Remove or reduce any explicit `letter-spacing` and `text-transform:uppercase` from headings inside the plugin. Keep uppercase only for kicker labels (`.wellme-card-number`, `.wellme-partnership-kicker`, module label spans) at `font-size ≤ .75rem`.

---

## Part 2 — Content Fix

### Source of truth

`WP 3.1. Creating Hands On Training/PAMPHLETS_DATA_SHEET.xlsx`

### ACF fields to verify per module (7 field types × 6 modules = 42 checks)

| Field | ACF key | Excel column/row |
|---|---|---|
| Module motto | `module_motto` | "Module Motto" row |
| EU funding text | `module_eu_funding_text` | "EU Funding Acknowledgement" row |
| Table of contents | `module_table_of_contents` | "Table of Contents" row |
| Introduction items (3) | `module_introduction_items` repeater | "Introduction Title" + "Introduction Detail" rows (3 sets per module) |
| Conclusion | `module_conclusion` | "Conclusion" row |
| Reflection questions | `module_reflection_questions` repeater | "Reflection Questions" rows |
| Activity (learning outcomes) | `module_learning_outcomes` repeater | "Activity Title" + "Activity Detail" rows |

### Known data from Excel (mottos — critical for flip cards)

| Module | Motto |
|---|---|
| 1 | "Happiness is not something ready-made. It comes from your own actions." — Dalai Lama |
| 2 | "It is not the thing or the situation that disturbs, but the opinion about the thing or the situation" — Epictetus |
| 3 | "Your worth is not determined by your weight, your diet, or your feed – it is determined by who you are." |
| 4 | "Step by step, side by side: movement makes space for words the heart struggles to share sitting still." |
| 5 | "If You Can't Find the Right Place — Then you Should Make One" |
| 6 | "Strong transitions grow where young people, relationships and community resources meet." |

### Update method

For each module: navigate to WP admin edit page → verify each ACF field → update if different from Excel canonical value. Use the WP admin UI (not REST API) since ACF repeaters are complex to update programmatically.

---

## Architecture — No structural changes

All changes are CSS-only for design (no PHP template changes). Content changes are WP admin ACF field updates only. No new files added. No shortcode signatures change.

---

## Verification checklist

- [ ] All 5 slides have white (or blue gradient for slide 1) background — no grey `#e5e5e5`
- [ ] Module cards in slide 4 have brand-coloured top bands
- [ ] Flip card fronts show gradient not just photo
- [ ] Popup title is normal-weight, not uppercase/heavy
- [ ] Overview tabs are brand blue when active
- [ ] All 6 module mottos match Excel values
- [ ] Introduction items populated for all 6 modules (3 items each)
- [ ] Conclusion text populated for all 6 modules
- [ ] Reflection questions populated for all 6 modules
