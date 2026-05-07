# WellMe Pamphlets Redesign & Content Fix — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update `wellme-pamphlets-public.css` to match wellmeproject.com's brand (blue gradient hero, white slide backgrounds, brand-coloured elements), and verify/update ACF field content for all 6 modules against `PAMPHLETS_DATA_SHEET.xlsx`.

**Architecture:** The plugin already uses a `wellme-experience--reader` class on the outer wrapper that hides dark bg overlays and sets slide content to white. Changes are additive CSS overrides appended to the existing file — no PHP template changes, no structural changes. Content fixes are done via WP Admin ACF field updates.

**Tech Stack:** WordPress plugin (PHP), CSS, ACF Pro, Playwright (for WP Admin content updates), WP site at `https://www.wellmeproject.com` (creds in `.env`)

---

## File Map

| File | What changes |
|---|---|
| `public/css/wellme-pamphlets-public.css` | Append ~200 lines of brand overrides at end of file |
| WP Admin (ACF fields) | Motto, EU text, TOC, intro items, conclusion, reflection questions for 6 modules |

---

## PART 1 — CSS DESIGN

### Task 1: Landing slide — blue gradient hero

**Files:**
- Modify: `public/css/wellme-pamphlets-public.css` (append after last line)

Context: `.wellme-landing-bg` is currently `#08111f` dark navy. `.wellme-landing-continue` is a ghost white-outline button. Logo has spin animation. Goal: blue gradient bg, solid white pill CTA, no spin.

- [ ] **Step 1: Append landing overrides**

Open `public/css/wellme-pamphlets-public.css`. Append exactly this block at the very end of the file:

```css
/* ============================================================
   BRAND REDESIGN — Landing Slide
   ============================================================ */

.wellme-landing-bg {
    background: linear-gradient(135deg, #26aafc 0%, #1a7bbe 100%) !important;
}

.wellme-landing-overlay {
    background: none !important;
}

/* Wave divider at bottom of landing slide */
.wellme-slide-landing {
    overflow: hidden;
}
.wellme-slide-landing::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 48px;
    background: #fff;
    clip-path: ellipse(55% 100% at 50% 100%);
    z-index: 3;
    pointer-events: none;
}

/* Solid white CTA pill */
.wellme-landing-continue {
    background: #fff !important;
    color: #1a7bbe !important;
    border-color: #fff !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-transform: none !important;
}
.wellme-landing-continue:hover,
.wellme-landing-continue:focus-visible {
    background: rgba(255,255,255,.9) !important;
    color: #1a7bbe !important;
}

/* No spin on landing logo */
.wellme-slide-landing .wellme-logo-spin {
    animation: none !important;
}
```

- [ ] **Step 2: Deploy plugin update to live site**

The live site auto-updates from GitHub releases. After committing, either:
- Push to `main` and create a new GitHub release (plugin update checker will pick it up), OR
- Go to `https://www.wellmeproject.com/wp-admin/plugins.php`, find "WELLME Pamphlets", click **Update** if available, OR
- Download the plugin zip from the repo and upload via `wp-admin/plugin-install.php?tab=upload`

- [ ] **Step 3: Visual verify**

Navigate to `https://www.wellmeproject.com/wellme-pamphlets/`.  
Expected: Slide 1 has bright blue gradient background, white text, white pill Continue button, logo static.

- [ ] **Step 3: Commit**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: landing slide blue gradient hero + white pill CTA"
```

---

### Task 2: Partnership + Overview + Modules slides — white backgrounds

**Files:**
- Modify: `public/css/wellme-pamphlets-public.css` (append)

Context: Slides 2/3/4 bg elements are dark `#0a0e1a`. The reader mode already hides them (`display:none`) and sets white bg on content. But headings/kickers remain white text on white bg, invisible. Need to fix text colours.

- [ ] **Step 1: Append slide colour overrides**

Append to end of `public/css/wellme-pamphlets-public.css`:

```css
/* ============================================================
   BRAND REDESIGN — Partnership / Overview / Modules text colours
   ============================================================ */

/* Partnership slide — white bg, dark text */
.wellme-partnership-title,
.wellme-partnership-kicker,
.wellme-partnership-lede,
.wellme-partnership-content {
    color: #1a1a2e;
}
.wellme-partnership-kicker {
    color: #26aafc;
}

/* Left intro panel — dark navy, white text */
.wellme-partnership-intro {
    background: linear-gradient(160deg, #1a1a2e 0%, #0d2a4a 100%);
    color: #fff;
    border-radius: var(--wellme-radius);
    padding: 28px 24px;
}
.wellme-partnership-intro .wellme-partnership-kicker {
    color: #26aafc;
}
.wellme-partnership-intro .wellme-partnership-title,
.wellme-partnership-intro .wellme-partnership-lede {
    color: #fff;
}

/* Choice nav buttons — white cards, blue number */
.wellme-partnership-choice {
    background: #fff;
    border: 1px solid #eee;
    border-radius: var(--wellme-radius);
    padding: 16px 20px;
    text-align: left;
    transition: border-color 0.2s, transform 0.2s;
}
.wellme-partnership-choice:hover,
.wellme-partnership-choice:focus-visible {
    border-color: #26aafc;
    transform: translateY(-2px);
}
.wellme-partnership-choice-number {
    color: #26aafc;
}
.wellme-partnership-choice-title {
    color: #1a1a2e;
}
.wellme-partnership-choice-text {
    color: #666;
}

/* Partner cards — white with border */
.wellme-partner-card {
    background: #fff !important;
    border: 1px solid #eee !important;
}
.wellme-partner-card:hover,
.wellme-partner-card:focus-visible {
    border-color: #26aafc !important;
}
.wellme-partner-name {
    color: #1a1a2e !important;
}

/* Overview slide — dark text */
.wellme-overview-main-title,
.wellme-overview-title,
.wellme-overview-content {
    color: #1a1a2e;
}
.wellme-overview-kicker,
.wellme-overview-section-label,
.wellme-overview-state-kicker {
    color: #26aafc;
}
.wellme-overview-section h3 {
    color: #1a1a2e;
}
.wellme-overview-section-body {
    color: #444;
}
.wellme-overview-active-label,
.wellme-overview-state-count {
    color: #1a1a2e;
}

/* Modules slide — dark text */
.wellme-modules-slide-title,
.wellme-modules-slide-content {
    color: #1a1a2e;
}
```

- [ ] **Step 2: Commit**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: fix text colours for white-background slides"
```

---

### Task 3: Mazda tab navigation — brand blue pill style

**Files:**
- Modify: `public/css/wellme-pamphlets-public.css` (append)

Context: `.wellme-mazda-page-tab` is currently transparent with grey text + underline on active. Spec: pill shape, blue active, grey inactive.

- [ ] **Step 1: Append tab overrides**

Append to end of `public/css/wellme-pamphlets-public.css`:

```css
/* ============================================================
   BRAND REDESIGN — Mazda Page Tabs (pill style)
   ============================================================ */

.wellme-mazda-page-tabs {
    border-bottom: none;
    gap: 8px;
    flex-wrap: wrap;
}

.wellme-mazda-page-tab {
    padding: 6px 16px;
    border-radius: 20px;
    background: #f0f0f0;
    color: #555;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: none;
    border: 1px solid transparent;
    transition: background 0.2s, color 0.2s;
}

.wellme-mazda-page-tab::after {
    display: none;
}

.wellme-mazda-page-tab:hover,
.wellme-mazda-page-tab:focus-visible {
    background: #e0f0ff;
    color: #26aafc;
}

.wellme-mazda-page-tab.is-active {
    background: #26aafc;
    color: #fff;
}

.wellme-mazda-page-tab.is-active::after {
    display: none;
}
```

- [ ] **Step 2: Commit**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: tab navigation pill style matching brand"
```

---

### Task 4: Module inline cards — white + colour-top-band

**Files:**
- Modify: `public/css/wellme-pamphlets-public.css` (append)

Context: `.wellme-module-inline-card` is glass-morphism dark (`rgba(255,255,255,0.06)` background). Spec: white card, colour-top-band using `--module-color`.

The card image `(.wellme-module-inline-image)` will become the coloured top band. The `.wellme-module-inline-body` already has `border-top: 3px solid var(--module-color)`.

- [ ] **Step 1: Append module card overrides**

Append to end of `public/css/wellme-pamphlets-public.css`:

```css
/* ============================================================
   BRAND REDESIGN — Module inline cards (slide 4)
   ============================================================ */

.wellme-module-inline-card {
    background: #fff !important;
    border: 1px solid #eee !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.wellme-module-inline-card:hover,
.wellme-module-inline-card:focus-visible {
    background: #fff !important;
    border-color: var(--module-color, #26aafc) !important;
    box-shadow: 0 6px 24px rgba(0,0,0,.10);
}

/* Colour-top band using module image as bg + gradient overlay */
.wellme-module-inline-image {
    height: 72px;
    background-color: var(--module-color, #26aafc) !important;
    background-blend-mode: multiply;
    position: relative;
}
.wellme-module-inline-image::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        var(--module-color, #26aafc) 0%,
        color-mix(in srgb, var(--module-color, #26aafc) 65%, #000 35%) 100%
    );
    opacity: 0.85;
}

.wellme-module-inline-number {
    color: var(--module-color, #26aafc) !important;
}

.wellme-module-inline-title {
    color: #1a1a2e !important;
}

.wellme-module-inline-desc {
    color: #666 !important;
}

/* CTA pill */
.wellme-module-inline-cta {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 14px;
    border-radius: 20px;
    background: var(--module-color, #26aafc);
    color: #fff !important;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: none;
    border-bottom: none;
    transition: opacity 0.2s;
}

.wellme-module-inline-card:hover .wellme-module-inline-cta,
.wellme-module-inline-card:focus-visible .wellme-module-inline-cta {
    opacity: 0.85 !important;
}
```

- [ ] **Step 2: Commit**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: module cards white with colour-top-band and pill CTA"
```

---

### Task 5: Flip cards — brand gradient front, white back

**Files:**
- Modify: `public/css/wellme-pamphlets-public.css` (append)

Context: `.wellme-flipcard-front` is `#1a1a2e` dark. `.wellme-flipcard-back` is brand gradient. Spec swaps these: front = module-color gradient, back = white with dark motto text.

- [ ] **Step 1: Append flipcard overrides**

Append to end of `public/css/wellme-pamphlets-public.css`:

```css
/* ============================================================
   BRAND REDESIGN — Flip cards (Sum-Up slide)
   ============================================================ */

.wellme-flipcard-front {
    background: linear-gradient(
        135deg,
        var(--module-color, #26aafc) 0%,
        color-mix(in srgb, var(--module-color, #26aafc) 65%, #000 35%) 100%
    ) !important;
}

/* Cover image as subtle overlay, not dominant */
.wellme-flipcard-image {
    opacity: 0.18 !important;
    mix-blend-mode: luminosity;
}

.wellme-flipcard-front .wellme-flipcard-number {
    color: rgba(255,255,255,.8) !important;
    opacity: 1 !important;
}

.wellme-flipcard-front .wellme-flipcard-title {
    color: #fff !important;
}

/* Back: white with dark text */
.wellme-flipcard-back {
    background: #fff !important;
    color: #1a1a2e !important;
}

.wellme-flipcard-back .wellme-flipcard-number {
    color: var(--module-color, #26aafc) !important;
    opacity: 1 !important;
}

.wellme-flipcard-motto {
    color: #1a1a2e !important;
    font-size: 0.95rem;
    font-style: italic;
    font-weight: 600;
    line-height: 1.6;
}
```

- [ ] **Step 2: Commit**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: flipcards brand gradient front, white motto back"
```

---

### Task 6: Popup — normal weight title, light right panel

**Files:**
- Modify: `public/css/wellme-pamphlets-public.css` (append)

Context: `.wellme-popup-title` is `font-weight:800`. Right panel already `background:#fff`. Spec wants 700 weight, `#f8fafc` right panel, blue label.

- [ ] **Step 1: Append popup overrides**

Append to end of `public/css/wellme-pamphlets-public.css`:

```css
/* ============================================================
   BRAND REDESIGN — Module popup
   ============================================================ */

.wellme-popup-right {
    background: #f8fafc !important;
}

.wellme-popup-title {
    font-weight: 700 !important;
    font-size: 1.15rem !important;
    text-transform: none !important;
    letter-spacing: normal !important;
}

.wellme-popup-module-label,
.wellme-popup-label {
    color: var(--module-color, #26aafc) !important;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Cover title in pamphlet (inside popup) */
.wellme-cover-title {
    font-size: clamp(1.3rem, 2.5vw, 1.8rem) !important;
    font-weight: 700 !important;
    text-transform: none !important;
    letter-spacing: normal !important;
}

.wellme-cover-number {
    color: var(--module-color, #26aafc) !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
}

/* TOC box accent */
.wellme-cover-toc {
    border-left: 3px solid var(--module-color, #26aafc) !important;
    background: #f8fafc !important;
    border-radius: 0 8px 8px 0 !important;
    padding: 14px 18px !important;
}

/* Section headings inside pamphlet */
.wellme-pamphlet .wellme-pamphlet-section h2 {
    font-weight: 700 !important;
    color: #1a1a2e !important;
    font-size: 1.05rem !important;
}

.wellme-section-cover .wellme-cover-eu-text {
    font-size: 0.72rem !important;
    color: #888 !important;
}
```

- [ ] **Step 2: Visual spot-check**

Open a module pamphlet popup on `https://www.wellmeproject.com/wellme-pamphlets/` (slide 4, click a module).  
Expected: Module label in brand colour, title in 700-weight non-uppercase, right panel light blue-grey.

- [ ] **Step 3: Commit**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: popup normal-weight title, brand label, light right panel"
```

---

### Task 7: Full visual regression — screenshot all 5 slides + popup

**Files:** None changed.

- [ ] **Step 1: Screenshot all slides via Playwright**

Use Playwright to navigate to `https://www.wellmeproject.com/wellme-pamphlets/`. Click through slides 1–5 and take a screenshot of each. Click a module on slide 4 to screenshot the popup.

- [ ] **Step 2: Verify checklist**

| Check | Pass? |
|---|---|
| Slide 1: bright blue gradient bg, white text, white pill CTA | |
| Slide 2: white bg, dark navy left intro panel, white partner cards | |
| Slide 3: white bg, blue active tab pill, dark text | |
| Slide 4: white bg, module cards with colour-top-band, white cards | |
| Slide 5: gradient front (module colour), white back | |
| Popup: normal weight title, light right panel, brand colour label | |

If any check fails, diagnose with browser dev tools (inspect computed styles) and add a more specific override.

- [ ] **Step 3: Commit any fixes**

```bash
git add public/css/wellme-pamphlets-public.css
git commit -m "style: visual fixes from regression check"
```

---

## PART 2 — CONTENT FIX

### Task 8: Read all module mottos from live ACF + fix against Excel

**Source:** `WP 3.1. Creating Hands On Training/PAMPHLETS_DATA_SHEET.xlsx` row "Module Motto"

**Canonical mottos:**
| Module | Post ID | Correct motto |
|---|---|---|
| 1 | 100510 | "Happiness is not something ready-made. It comes from your own actions." Dalai Lama |
| 2 | 100518 | "It is not the thing or the situation that disturbs, but the opinion about the thing or the situation", Epictetus |
| 3 | 100524 | "Your worth is not determined by your weight, your diet, or your feed – it is determined by who you are." |
| 4 | 100530 | "Step by step, side by side: movement makes space for words the heart struggles to share sitting still. |
| 5 | 100535 | If You Can't Find the Right Place-Then you Should Make One |
| 6 | 100541 | Strong transitions grow where young people, relationships and community resources meet. |

- [ ] **Step 1: Navigate to Module 1 edit page**

Go to `https://www.wellmeproject.com/wp-admin/post.php?post=100510&action=edit` (use creds from `.env`).  
Find the "Module Motto" ACF field. Read its current value.

- [ ] **Step 2: Update Module 1 motto if different**

If current value ≠ canonical above, clear the field and enter the exact canonical text. Click **Update**.

- [ ] **Step 3: Repeat for Modules 2–6**

Post IDs: 100518, 100524, 100530, 100535, 100541.  
Navigate to each, verify `module_motto` field, update if needed.

- [ ] **Step 4: Verify on live site**

Navigate to `https://www.wellmeproject.com/wellme-pamphlets/`, go to slide 5 (Sum-Up). Flip each card. Confirm the back of each card shows the correct motto.

- [ ] **Step 5: Commit note**

Mottos are WP DB content, not in the repo. No commit needed — just leave a note that it was done.

---

### Task 9: Introduction items — 3 items per module

**Source:** PAMPHLETS_DATA_SHEET.xlsx rows "Introduction Title" and "Introduction Detail" (3 sets per module)

Each module needs exactly 3 introduction items in the `module_introduction_items` ACF repeater:

| Item | intro_title | intro_detail field from Excel |
|---|---|---|
| 1 | "Aim of Module N" | Long intro text from "Introduction Detail" row 1 |
| 2 | "How Module N is connected with Youth work" | Text from "Introduction Detail" row 2 |
| 3 | "Module N and WELLME Goals" | Text from "Introduction Detail" row 3 |

**Module 1 exact values:**

*Item 1:*
- `intro_title`: `Aim of Module 1`
- `intro_detail`: `Positive Psychology looks at what helps people feel well, grow, and thrive. It focuses on wellbeing, personal strengths, and the conditions that support positive development in individuals and communities. In this module, you will explore several key ideas that can support your youth work: Wellbeing (PERMA)... [full text from Excel row 63-73]`

*Item 2:*
- `intro_title`: `How Module 1 is connected with Youth work`
- `intro_detail`: `Supporting young people's wellbeing is an important part of your work as a youth trainer... [full text from Excel row 92-99]`

*Item 3:*
- `intro_title`: `Module 1 and WELLME Goals`
- `intro_detail`: `The WellMe project focuses on supporting young people's wellbeing by helping them feel more confident, connected, and supported... [full text from Excel row 109-116]`

- [ ] **Step 1: Open Module 1 edit page**

Go to `https://www.wellmeproject.com/wp-admin/post.php?post=100510&action=edit`.  
Scroll to the `module_introduction_items` repeater field.

- [ ] **Step 2: Verify / add intro items for Module 1**

Check if 3 items exist. For each item compare `intro_title` and `intro_detail` against the Excel values. Read the full text from `PAMPHLETS_DATA_SHEET.xlsx` for the exact detail text.

Update any that differ. Click **Update**.

- [ ] **Step 3: Repeat for Modules 2–6**

For each module, read the 3 Introduction rows from the Excel (cols B through G) and update the repeater field. The structure is always: Aim / Youth work connection / WELLME Goals.

- [ ] **Step 4: Verify on live site**

Open a pamphlet popup (slide 4 → click a module). Check the Introduction section shows 3 clickable items with the correct titles. Click each to verify the detail text.

---

### Task 10: Module conclusion text — 6 modules

**Source:** PAMPHLETS_DATA_SHEET.xlsx row "Conclusion"

**Canonical conclusions:**
| Module | Conclusion text |
|---|---|
| 1 | `Small everyday moments often pass unnoticed... "Happiness is not something ready-made. It comes from your own actions." Dalai Lama` |
| 2 | `Ἡ ἀρετὴ ἕξις ἐστίν... "It is not the thing or the situation that disturbs, but the opinion about the thing or the situation", Epictetus` |
| 3 | `This exercise is consistently one of the most impactful... "Your worth is not determined by your weight, your diet, or your feed – it is determined by who you are."` |
| 4 | `"Step by step, side by side: movement makes space for words the heart struggles to share sitting still.` |
| 5 | `If You Can't Find the Right Place-Then you Should Make One` |
| 6 | `Strong transitions grow where young people, relationships and community resources meet.` |

- [ ] **Step 1: For each module (100510–100541), navigate to edit page**

Find the `module_conclusion` field. Compare with canonical above.  
Update if different. Click **Update**.

- [ ] **Step 2: Verify on live site**

Open each module pamphlet. Scroll to Conclusion section. Verify text matches.

---

### Task 11: Reflection questions — all 6 modules

**Source:** PAMPHLETS_DATA_SHEET.xlsx "Reflection Questions" rows

Each module has a `module_reflection_questions` repeater. Each entry has one field: `reflection_question`.

**Module 1 — 8 questions:**
1. How did reflecting on a positive moment influence your mood or perspective during the activity?
2. Did you notice any new details or feelings when you took time to think more deeply about that moment?
3. Why do you think it is sometimes difficult to notice positive moments in our daily lives?
4. How can sharing positive experiences or appreciation influence relationships and group atmosphere?
5. How does this activity relate to the ideas of Positive Psychology, such as focusing on strengths and positive emotions?
6. In what ways can practices like gratitude and reflection support wellbeing and resilience in everyday life?
7. How could activities like this help create more positive and supportive learning environments for young people?
8. What is one simple action you could try this week to notice positive moments more often or express appreciation to someone in your life?

**Module 2 — 2 questions:**
1. What will we take away from today's activity?
2. What is helpful to remember about failure?

**Module 3 — 6 questions:**
1. Which social media post analysed today had the strongest effect on you – and why?
2. What does this tell you about how diet culture operates in your everyday digital life?
3. Social Cognitive Theory tells us that we learn through observing others. How does the modelling of unrealistic body standards on social media influence what young people believe is "normal" or desirable?
4. How can youth workers use positive role models as a counter-strategy?
5. As a youth worker, what one specific action could you take in your community to help young people build a healthier relationship with social media and body image?
6. What barriers might you face, and how could you address them?

**Module 4 — 3 questions:**
1. How did walking side-by-side, instead of sitting face-to-face, change the way you spoke, listened, or felt during the conversation?
2. What did this exercise show you in practice about the link between movement, nature and emotional regulation that we discussed in the module?
3. As a youth worker, in which real situations (conflicts, check-ins, mentoring talks) could a short "walk & talk" be a safer, more accessible option than a formal sit-down talk?

**Module 5 — 4 questions:**
1. Which place made you feel the strongest sense of belonging and why?
2. How did this activity change the way you see your community?
3. With whom will you engage with to achieve more inclusive spaces?
4. What small action could you take to improve a space in your area?

**Module 6 — verify from Excel** (only "1", "2", "…" listed in spreadsheet — read full text from the DOCX source: `WP3.1.Hands on Training Module6_Autokreacja.docx`)

- [ ] **Step 1: Open Module 1 edit page, find `module_reflection_questions` repeater**

Navigate to `https://www.wellmeproject.com/wp-admin/post.php?post=100510&action=edit`.  
Check existing questions. Add/update to match 8 questions above. Click **Update**.

- [ ] **Step 2: Repeat for Modules 2–5**

Post IDs: 100518 (2Q), 100524 (6Q), 100530 (3Q), 100535 (4Q).

- [ ] **Step 3: Module 6 — read from DOCX**

Open `WP 3.1. Creating Hands On Training/WP3.1.Hands on Training Module6_Autokreacja.docx`.  
Find reflection/conclusion questions. Update `module_reflection_questions` for post 100541.

- [ ] **Step 4: Verify on live site**

Open each module pamphlet → scroll to Conclusion section → verify Reflection Questions list.

---

### Task 12: Module activity (learning outcomes) — all 6 modules

**Source:** PAMPHLETS_DATA_SHEET.xlsx "Activity Title" and "Activity Detail" rows

Each module has `module_learning_outcomes` repeater with `outcome_title` + `outcome_detail`.

**Two outcomes per module:**

| Module | Outcome 1 title | Outcome 2 title |
|---|---|---|
| 1 | `Learning outcomes of the Activity: Savor the Moment: A Gratitude Practice` | `How this Activity is connected with the Module` |
| 4 | `Learning outcomes of the Activity: Walk & Talk Pathways – Moving Conversations in Nature` | `How this Activity is connected with the Module` |
| 5 | `Learning outcomes of the Activity: My Place of Belonging – Photo & Mapping Exploration` | `How this Activity is connected with the Module` |

For detail text, read from the Excel rows "Activity Detail" for each module (columns B–G).

- [ ] **Step 1: For each module, navigate to edit page**

Check `module_learning_outcomes` repeater. Verify 2 items exist with correct titles and detail text from the Excel.

- [ ] **Step 2: Update any mismatches. Click Update.**

- [ ] **Step 3: Verify on live site**

Open each module pamphlet → "Module Activity" section → confirm clickable outcome links expand with correct detail text.

---

### Task 13: EU funding text per module

**Source:** PAMPHLETS_DATA_SHEET.xlsx "EU Funding Acknowledgement" row

| Module | `module_eu_funding_text` |
|---|---|
| 1 | `Design by GESEME` |
| 2 | `Designed by EUROPEAN PROGRESS` |
| 3 | `Designed by UNIVERSITY OF ZARAGOZA` |
| 4 | `Designed by ETAP` |
| 5 | `Designed by CENTREDOT` |
| 6 | `Designed by AUTOKREACJA` |

- [ ] **Step 1: For each module, navigate to edit page**

Check `module_eu_funding_text` field. Update to canonical above. Click Update.

- [ ] **Step 2: Verify on live site**

Open each pamphlet popup → cover section → verify EU text shows correctly.

---

### Task 14: Table of contents per module

**Source:** PAMPHLETS_DATA_SHEET.xlsx "Table of Contents" row

All 6 modules share the same TOC structure with module number substituted:

```
A. Introduction:
- Aim of Module N.
- How Module N is connected with Youth work
- Module N and WELLME Goals
B. Module N Activity:
-Learning outcomes of the Activity
-How this Activity is connected with Module
C. Activity steps-Experiential Implementation
D. Conclusions
```

- [ ] **Step 1: For each module, navigate to edit page**

Check `module_table_of_contents` textarea. Update to the structure above with correct module number. Click Update.

- [ ] **Step 2: Verify on live site**

Open each pamphlet → cover section → Contents box shows correct TOC.

---

## Verification — full spec checklist

- [ ] Slide 1: blue gradient bg, wave bottom, white pill CTA, no spin
- [ ] Slide 2: white bg, dark navy left intro, white partner cards
- [ ] Slide 3: white bg, brand-blue active tabs (pill)
- [ ] Slide 4: white bg, module cards with colour-top-band + pill CTA
- [ ] Slide 5: gradient (module colour) card fronts, white motto backs
- [ ] Popup: normal-weight title, blue module label, `#f8fafc` right panel
- [ ] All 6 mottos match Excel
- [ ] Module 1: 8 reflection questions present
- [ ] Module 2: 2 reflection questions present
- [ ] Modules 3–5: correct reflection questions per Excel
- [ ] All 6 introduction item sets (3 each) present and match Excel
- [ ] All 6 conclusions present
- [ ] EU funding text shows "Designed by [PARTNER]" on each cover
- [ ] TOC present on all 6 module covers
