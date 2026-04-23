<?php
/**
 * Template: Full-screen experience slider — 5-slide presentation.
 *
 * Slide 1: WELLME Landing (logo, title, EU branding)
 * Slide 2: Partnership and Click (clickable partner cards)
 * Slide 3: Wellme Overview (purpose, need, results)
 * Slide 4: Modules (6 clickable module cards → open pamphlets)
 * Slide 5: Sum-Up (6 flip cards with module mottos)
 *
 * Variables available:
 *   $modules  array of WP_Post
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$total_slides     = 5; // landing, partnership, overview, modules, sum-up
$experience_title = get_field( 'project_title', 'option' ) ?: __( 'WELLME', 'wellme-pamphlets' );
$sumup_nav_label  = get_field( 'sumup_nav_label', 'option' ) ?: __( 'Sum-Up', 'wellme-pamphlets' );
$sumup_title      = get_field( 'sumup_title', 'option' ) ?: $sumup_nav_label;
$sumup_subtitle   = get_field( 'sumup_subtitle', 'option' ) ?: __( 'Click each card to reveal the module motto.', 'wellme-pamphlets' );
$sumup_cards_raw  = get_field( 'sumup_cards', 'option' ) ?: [];
$sumup_cards      = [];
$slide_nav_items  = [
    __( 'WELLME', 'wellme-pamphlets' ),
    __( 'Partnership', 'wellme-pamphlets' ),
    __( 'Overview', 'wellme-pamphlets' ),
    __( 'Modules', 'wellme-pamphlets' ),
    $sumup_nav_label,
];

$landing_image = get_field( 'landing_hero_image', 'option' );
$overview_image = get_field( 'overview_image', 'option' );
$landing_image_url = $landing_image['sizes']['medium'] ?? ( $landing_image['url'] ?? '' );
$overview_image_url = $overview_image['sizes']['medium'] ?? ( $overview_image['url'] ?? '' );
$module_cover_urls = [];

if ( ! empty( $modules ) && is_array( $modules ) ) {
    foreach ( $modules as $nav_module ) {
        $nav_cover = get_field( 'module_cover_image', $nav_module->ID );
        if ( ! empty( $nav_cover['url'] ) ) {
            $module_cover_urls[] = $nav_cover['sizes']['medium'] ?? $nav_cover['url'];
        }
    }
}

$fallback_nav_image = $landing_image_url ?: ( $overview_image_url ?: ( $module_cover_urls[0] ?? '' ) );
$slide_nav_media = [
    $landing_image_url ?: $fallback_nav_image,
    $overview_image_url ?: $fallback_nav_image,
    $overview_image_url ?: $fallback_nav_image,
    $module_cover_urls[0] ?? $fallback_nav_image,
    $module_cover_urls[1] ?? ( $module_cover_urls[0] ?? $fallback_nav_image ),
];

if ( ! empty( $sumup_cards_raw ) && is_array( $sumup_cards_raw ) ) {
    foreach ( $sumup_cards_raw as $card_index => $card ) {
        $card_image     = $card['card_image'] ?? [];
        $card_image_url = $card_image['sizes']['medium'] ?? ( $card_image['url'] ?? '' );
        $card_label     = trim( wp_strip_all_tags( $card['card_label'] ?? '' ) );
        $card_title     = trim( wp_strip_all_tags( $card['card_title'] ?? '' ) );
        $card_motto     = trim( wp_strip_all_tags( $card['card_motto'] ?? '' ) );

        if ( '' === $card_label && '' === $card_title && '' === $card_motto && '' === $card_image_url ) {
            continue;
        }

        $card_fallback_label = $card_label ?: sprintf( __( 'Card %d', 'wellme-pamphlets' ), $card_index + 1 );

        $sumup_cards[] = [
            'label'     => $card_fallback_label,
            'title'     => $card_title ?: $card_fallback_label,
            'motto'     => $card_motto,
            'image_url' => $card_image_url,
            'color'     => ! empty( $card['card_color'] ) ? $card['card_color'] : '#005b96',
        ];
    }
}

if ( empty( $sumup_cards ) && ! empty( $modules ) && is_array( $modules ) ) {
    foreach ( $modules as $module_index => $module ) {
        $number    = (int) get_field( 'module_number', $module->ID );
        $number    = $number ?: ( $module_index + 1 );
        $cover     = get_field( 'module_cover_image', $module->ID );
        $cover_url = $cover['sizes']['medium'] ?? ( $cover['url'] ?? '' );

        $sumup_cards[] = [
            'label'     => sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ),
            'title'     => get_the_title( $module ),
            'motto'     => get_field( 'module_motto', $module->ID ) ?: '',
            'image_url' => $cover_url,
            'color'     => get_field( 'module_color', $module->ID ) ?: '#005b96',
        ];
    }
}

if ( ! empty( $sumup_cards[0]['image_url'] ) ) {
    $slide_nav_media[4] = $sumup_cards[0]['image_url'];
}
?>
<div
    class="wellme-experience wellme-experience--reader"
    id="wellme-experience"
    role="region"
    aria-label="<?php esc_attr_e( 'WELLME Presentation', 'wellme-pamphlets' ); ?>"
>

    <?php /* ── Mazda-style top navigation bar ─────────────────────── */ ?>
    <nav class="wellme-exp-topnav" aria-label="<?php esc_attr_e( 'Presentation navigation', 'wellme-pamphlets' ); ?>">
        <div class="wellme-exp-topnav-brand">
            <span class="wellme-exp-topnav-brand-name"><?php echo esc_html( $experience_title ); ?></span>
        </div>

        <div class="wellme-exp-topnav-tabs" role="tablist">
            <button class="wellme-exp-topnav-tab is-active" data-index="0"
                    role="tab" aria-selected="true" aria-controls="wellme-experience-track">
                <?php esc_html_e( 'WELLME', 'wellme-pamphlets' ); ?>
            </button>
            <button class="wellme-exp-topnav-tab" data-index="1"
                    role="tab" aria-selected="false" aria-controls="wellme-experience-track">
                <?php esc_html_e( 'Partnership', 'wellme-pamphlets' ); ?>
            </button>
            <button class="wellme-exp-topnav-tab" data-index="2"
                    role="tab" aria-selected="false" aria-controls="wellme-experience-track">
                <?php esc_html_e( 'Overview', 'wellme-pamphlets' ); ?>
            </button>
            <button class="wellme-exp-topnav-tab" data-index="3"
                    role="tab" aria-selected="false" aria-controls="wellme-experience-track">
                <?php esc_html_e( 'Modules', 'wellme-pamphlets' ); ?>
            </button>
            <button class="wellme-exp-topnav-tab" data-index="4"
                    role="tab" aria-selected="false" aria-controls="wellme-experience-track">
                <?php echo esc_html( $sumup_nav_label ); ?>
            </button>
        </div>

        <div class="wellme-exp-topnav-actions">
            <div class="wellme-exp-topnav-counter" aria-live="polite" aria-atomic="true">
                <span class="wellme-exp-topnav-counter-current">1</span>
                <span aria-hidden="true"> / </span>
                <span class="wellme-exp-topnav-counter-total"><?php echo $total_slides; ?></span>
            </div>
        </div>
    </nav>

    <?php /* ── Slides track ─────────────────────────────────────── */ ?>
    <div class="wellme-experience-track" id="wellme-experience-track">

        <?php /* ── Slide 1: Landing (branding only) ────────────── */ ?>
        <?php
        $index         = 0;
        $is_first      = true;
        $hide_partners = true;  // Partners are shown on Slide 2
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-slide-landing.php';
        unset( $hide_partners );
        ?>

        <?php /* ── Slide 2: Partnership ─────────────────────────── */ ?>
        <?php
        $index    = 1;
        $is_first = false;
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-slide-partnership.php';
        ?>

        <?php /* ── Slide 3: Overview ──────────────────────────────── */ ?>
        <?php
        $index    = 2;
        $is_first = false;
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-slide-overview.php';
        ?>

        <?php /* ── Slide 4: Modules ──────────────────────────────── */ ?>
        <section class="wellme-experience-slide wellme-slide-modules"
                 data-index="3"
                 aria-label="<?php esc_attr_e( 'Modules', 'wellme-pamphlets' ); ?>">

            <div class="wellme-modules-slide-bg" aria-hidden="true"></div>
            <div class="wellme-modules-slide-overlay" aria-hidden="true"></div>

            <div class="wellme-modules-slide-content">
                <?php if ( ! empty( $modules ) ) : ?>
                <nav class="wellme-mazda-page-tabs wellme-modules-page-tabs" aria-label="<?php esc_attr_e( 'Module page tabs', 'wellme-pamphlets' ); ?>">
                    <?php foreach ( $modules as $m_index => $module ) :
                        $number = (int) get_field( 'module_number', $module->ID );
                        $number = $number ?: ( $m_index + 1 );
                    ?>
                    <button type="button"
                            class="wellme-mazda-page-tab<?php echo 0 === $m_index ? ' is-active' : ''; ?>"
                            data-module-open="<?php echo esc_attr( $module->ID ); ?>">
                        <?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?>
                    </button>
                    <?php endforeach; ?>
                </nav>

                <h2 class="wellme-modules-slide-title"><?php esc_html_e( 'Which Module Will You Choose?', 'wellme-pamphlets' ); ?></h2>

                <div class="wellme-modules-grid-inline" aria-label="<?php esc_attr_e( 'Training modules', 'wellme-pamphlets' ); ?>">
                    <?php foreach ( $modules as $m_index => $module ) :
                        $number    = (int) get_field( 'module_number', $module->ID );
                        $number    = $number ?: ( $m_index + 1 );
                        $subtitle  = get_field( 'module_subtitle', $module->ID );
                        $desc      = get_field( 'module_description', $module->ID );
                        $icon      = get_field( 'module_icon', $module->ID );
                        $cover     = get_field( 'module_cover_image', $module->ID );
                        $color     = get_field( 'module_color', $module->ID ) ?: '#005b96';
                        $icon_url  = $icon['url'] ?? '';
                        $cover_url = $cover['sizes']['large'] ?? ( $cover['url'] ?? '' );
                    ?>
                    <article class="wellme-module-inline-card wellme-scroll-reveal"
                             style="--module-color: <?php echo esc_attr( $color ); ?>;"
                             data-module-id="<?php echo esc_attr( $module->ID ); ?>"
                             role="button"
                             tabindex="0"
                             aria-label="<?php echo esc_attr( get_the_title( $module ) ); ?>">
                        <div class="wellme-module-inline-media" aria-hidden="true">
                            <?php if ( $cover_url ) : ?>
                            <img class="wellme-module-inline-image"
                                 src="<?php echo esc_url( $cover_url ); ?>"
                                 alt="">
                            <?php endif; ?>
                            <span class="wellme-module-inline-media-fallback">
                                <?php if ( $icon_url ) : ?>
                                <img src="<?php echo esc_url( $icon_url ); ?>" alt="">
                                <?php else : ?>
                                <?php echo esc_html( (string) $number ); ?>
                                <?php endif; ?>
                            </span>
                        </div>

                        <div class="wellme-module-inline-body">
                            <span class="wellme-module-inline-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
                            <h3 class="wellme-module-inline-title"><?php echo esc_html( get_the_title( $module ) ); ?></h3>
                            <?php if ( $subtitle ) : ?>
                            <p class="wellme-module-inline-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                            <?php elseif ( $desc ) : ?>
                            <p class="wellme-module-inline-subtitle"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $desc ), 12 ) ); ?></p>
                            <?php endif; ?>
                            <?php if ( $desc ) : ?>
                            <span class="wellme-module-inline-desc" hidden><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $desc ), 22 ) ); ?></span>
                            <?php endif; ?>
                            <button type="button"
                                    class="wellme-module-inline-cta"
                                    data-module-open="<?php echo esc_attr( $module->ID ); ?>">
                                <?php esc_html_e( 'View Module', 'wellme-pamphlets' ); ?>
                            </button>
                            <span class="wellme-module-inline-badge">
                                <?php echo esc_html( sprintf( '%02d', $number ) ); ?>
                            </span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <h2 class="wellme-modules-slide-title"><?php esc_html_e( 'Training Modules', 'wellme-pamphlets' ); ?></h2>
                <p class="wellme-no-modules"><?php esc_html_e( 'No modules found.', 'wellme-pamphlets' ); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <?php /* ── Slide 5: Sum-Up (Flip Cards) ──────────────── */ ?>
        <section class="wellme-experience-slide wellme-slide-sumup"
                 data-index="4"
                 aria-label="<?php echo esc_attr( $sumup_nav_label ); ?>">

            <div class="wellme-sumup-bg" aria-hidden="true"></div>
            <div class="wellme-sumup-overlay" aria-hidden="true"></div>

            <div class="wellme-sumup-content">
                <h2 class="wellme-sumup-title"><?php echo esc_html( $sumup_title ); ?></h2>
                <?php if ( $sumup_subtitle ) : ?>
                <p class="wellme-sumup-subtitle"><?php echo esc_html( $sumup_subtitle ); ?></p>
                <?php endif; ?>

                <div class="wellme-flipcards-grid wellme-flipcards-grid--experience">
                    <?php foreach ( $sumup_cards as $card ) :
                        $card_label     = $card['label'] ?? '';
                        $card_title     = ! empty( $card['title'] ) ? $card['title'] : $card_label;
                        $card_motto     = $card['motto'] ?? '';
                        $card_color     = ! empty( $card['color'] ) ? $card['color'] : '#005b96';
                        $card_image_url = $card['image_url'] ?? '';
                    ?>
                    <div class="wellme-flipcard wellme-scroll-reveal"
                         style="--module-color: <?php echo esc_attr( $card_color ); ?>;"
                         role="button"
                         tabindex="0"
                         aria-label="<?php echo esc_attr( sprintf( __( '%1$s: %2$s - click to reveal motto', 'wellme-pamphlets' ), $card_label, $card_title ) ); ?>">

                        <div class="wellme-flipcard-inner">
                            <div class="wellme-flipcard-front">
                                <?php if ( $card_image_url ) : ?>
                                <div class="wellme-flipcard-image" style="background-image: url('<?php echo esc_url( $card_image_url ); ?>');"></div>
                                <?php endif; ?>
                                <div class="wellme-flipcard-front-body">
                                    <?php if ( $card_label ) : ?>
                                    <span class="wellme-flipcard-number"><?php echo esc_html( $card_label ); ?></span>
                                    <?php endif; ?>
                                    <h3 class="wellme-flipcard-title"><?php echo esc_html( $card_title ); ?></h3>
                                </div>
                            </div>
                            <div class="wellme-flipcard-back">
                                <?php if ( $card_label ) : ?>
                                <span class="wellme-flipcard-number"><?php echo esc_html( $card_label ); ?></span>
                                <?php endif; ?>
                                <?php if ( $card_motto ) : ?>
                                <p class="wellme-flipcard-motto">&ldquo;<?php echo esc_html( $card_motto ); ?>&rdquo;</p>
                                <?php else : ?>
                                <p class="wellme-flipcard-motto wellme-placeholder"><?php esc_html_e( 'Motto coming soon.', 'wellme-pamphlets' ); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </div><!-- /.wellme-experience-track -->

    <?php /* ── Prev / Next arrows ──────────────────────────────── */ ?>
    <button
        class="wellme-exp-arrow wellme-exp-arrow--prev"
        aria-label="<?php esc_attr_e( 'Previous slide', 'wellme-pamphlets' ); ?>"
        hidden
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>

    <button
        class="wellme-exp-arrow wellme-exp-arrow--next"
        aria-label="<?php esc_attr_e( 'Next slide', 'wellme-pamphlets' ); ?>"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>

    <?php /* ── Dot navigation ──────────────────────────────────── */ ?>
    <nav
        class="wellme-exp-dots"
        aria-label="<?php esc_attr_e( 'Slide navigation', 'wellme-pamphlets' ); ?>"
    >
        <?php for ( $d = 0; $d < $total_slides; $d++ ) : ?>
        <button
            class="wellme-exp-dot<?php echo $d === 0 ? ' is-active' : ''; ?>"
            data-index="<?php echo $d; ?>"
            aria-label="<?php echo esc_attr( sprintf(
                /* translators: %s: slide number */
                __( 'Go to slide %s', 'wellme-pamphlets' ),
                $d + 1
            ) ); ?>"
            aria-current="<?php echo $d === 0 ? 'true' : 'false'; ?>"
        >
            <?php if ( ! empty( $slide_nav_media[ $d ] ) ) : ?>
            <span class="wellme-exp-dot-thumb"
                  style="background-image: url('<?php echo esc_url( $slide_nav_media[ $d ] ); ?>');"
                  aria-hidden="true"></span>
            <?php endif; ?>
            <span class="wellme-exp-dot-number"><?php echo esc_html( (string) ( $d + 1 ) ); ?></span>
            <span class="wellme-exp-dot-label"><?php echo esc_html( $slide_nav_items[ $d ] ?? (string) ( $d + 1 ) ); ?></span>
        </button>
        <?php endfor; ?>
    </nav>

    <?php /* ── Slide counter (bottom right) ──────────────────────── */ ?>
    <div class="wellme-exp-counter" aria-hidden="true">
        <span class="wellme-exp-counter-current">1</span>
        <span aria-hidden="true"> / </span>
        <span class="wellme-exp-counter-total"><?php echo $total_slides; ?></span>
    </div>

    </div><!-- /.wellme-experience -->

<?php include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-popup.php'; ?>
