<?php
/**
 * Template: Full-screen experience slider — 4-slide presentation.
 *
 * Slide 1: WELLME Landing + Partners (logo, title, EU branding, clickable partner cards)
 * Slide 2: Wellme Overview (purpose, need, results)
 * Slide 3: Modules (6 clickable module cards)
 * Slide 4: Sum-Up (6 flip cards with module mottos)
 *
 * Variables available:
 *   $modules  array of WP_Post
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$total_slides = 4; // landing+partners, overview, modules, sum-up
$experience_title = get_field( 'project_title', 'option' ) ?: __( 'WELLME', 'wellme-pamphlets' );
?>
<div
    class="wellme-experience wellme-experience--reader"
    id="wellme-experience"
    role="region"
    aria-label="<?php esc_attr_e( 'WELLME Presentation', 'wellme-pamphlets' ); ?>"
>
    <div class="wellme-reader-title" aria-hidden="true">
        <?php echo esc_html( $experience_title ); ?>
    </div>

    <?php /* ── Slides track ─────────────────────────────────────── */ ?>
    <div class="wellme-experience-track" id="wellme-experience-track">

        <?php /* ── Slide 1: Landing + Partners ──────────────────── */ ?>
        <?php
        $index = 0;
        $is_first = true;
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-slide-landing.php';
        ?>

        <?php /* ── Slide 2: Overview ─────────────────────────── */ ?>
        <?php
        $index = 1;
        $is_first = false;
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-slide-overview.php';
        ?>

        <?php /* ── Slide 3: Modules ──────────────────────────── */ ?>
        <section class="wellme-experience-slide wellme-slide-modules"
                 data-index="2"
                 aria-label="<?php esc_attr_e( 'Modules', 'wellme-pamphlets' ); ?>">

            <div class="wellme-modules-slide-bg" aria-hidden="true"></div>
            <div class="wellme-modules-slide-overlay" aria-hidden="true"></div>

            <div class="wellme-modules-slide-content">
                <h2 class="wellme-modules-slide-title"><?php esc_html_e( 'Training Modules', 'wellme-pamphlets' ); ?></h2>
                <div class="wellme-modules-grid-inline">
                    <?php foreach ( $modules as $m_index => $module ) :
                        $number    = (int) get_field( 'module_number', $module->ID );
                        $subtitle  = get_field( 'module_subtitle', $module->ID );
                        $color     = get_field( 'module_color', $module->ID ) ?: '#005b96';
                        $cover     = get_field( 'module_cover_image', $module->ID );
                        $cover_url = $cover['url'] ?? '';
                    ?>
                    <div class="wellme-module-inline-card wellme-scroll-reveal"
                         style="--module-color: <?php echo esc_attr( $color ); ?>;"
                         data-module-id="<?php echo esc_attr( $module->ID ); ?>"
                         role="button"
                         tabindex="0"
                         aria-label="<?php echo esc_attr( get_the_title( $module ) ); ?>">

                        <?php if ( $cover_url ) : ?>
                        <div class="wellme-module-inline-image" style="background-image: url('<?php echo esc_url( $cover_url ); ?>');"></div>
                        <?php endif; ?>

                        <div class="wellme-module-inline-body">
                            <span class="wellme-module-inline-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
                            <h3 class="wellme-module-inline-title"><?php echo esc_html( get_the_title( $module ) ); ?></h3>
                            <?php if ( $subtitle ) : ?>
                            <p class="wellme-module-inline-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <?php /* ── Slide 4: Sum-Up (Flip Cards) ──────────────── */ ?>
        <section class="wellme-experience-slide wellme-slide-sumup"
                 data-index="3"
                 aria-label="<?php esc_attr_e( 'Sum-Up', 'wellme-pamphlets' ); ?>">

            <div class="wellme-sumup-bg" aria-hidden="true"></div>
            <div class="wellme-sumup-overlay" aria-hidden="true"></div>

            <div class="wellme-sumup-content">
                <h2 class="wellme-sumup-title"><?php esc_html_e( 'Sum-Up', 'wellme-pamphlets' ); ?></h2>
                <p class="wellme-sumup-subtitle"><?php esc_html_e( 'Click each card to reveal the module motto.', 'wellme-pamphlets' ); ?></p>

                <div class="wellme-flipcards-grid wellme-flipcards-grid--experience">
                    <?php foreach ( $modules as $module ) :
                        $number    = (int) get_field( 'module_number',       $module->ID );
                        $motto     = get_field( 'module_motto',        $module->ID );
                        $color     = get_field( 'module_color',        $module->ID ) ?: '#005b96';
                        $cover     = get_field( 'module_cover_image',  $module->ID );
                        $cover_url = $cover['sizes']['medium'] ?? ( $cover['url'] ?? '' );
                    ?>
                    <div class="wellme-flipcard wellme-scroll-reveal"
                         style="--module-color: <?php echo esc_attr( $color ); ?>;"
                         role="button"
                         tabindex="0"
                         aria-label="<?php echo esc_attr( sprintf( __( 'Module %d: %s — click to reveal motto', 'wellme-pamphlets' ), $number, get_the_title( $module ) ) ); ?>">

                        <div class="wellme-flipcard-inner">
                            <div class="wellme-flipcard-front">
                                <?php if ( $cover_url ) : ?>
                                <div class="wellme-flipcard-image" style="background-image: url('<?php echo esc_url( $cover_url ); ?>');"></div>
                                <?php endif; ?>
                                <div class="wellme-flipcard-front-body">
                                    <span class="wellme-flipcard-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
                                    <h3 class="wellme-flipcard-title"><?php echo esc_html( get_the_title( $module ) ); ?></h3>
                                </div>
                            </div>
                            <div class="wellme-flipcard-back">
                                <span class="wellme-flipcard-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
                                <?php if ( $motto ) : ?>
                                <p class="wellme-flipcard-motto">&ldquo;<?php echo esc_html( $motto ); ?>&rdquo;</p>
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
        ></button>
        <?php endfor; ?>
    </nav>

    <?php /* ── Slide counter ─────────────────────────────────────── */ ?>
    <div class="wellme-exp-counter" aria-live="polite" aria-atomic="true">
        <span class="wellme-exp-counter-label"><?php esc_html_e( 'WELLME Presentation - page', 'wellme-pamphlets' ); ?></span>
        <span class="wellme-exp-counter-current">1</span>
        <span class="wellme-exp-counter-separator" aria-hidden="true"> / </span>
        <span class="wellme-exp-counter-total"><?php echo $total_slides; ?></span>
    </div>

    <?php /* ── Slide labels for accessibility ────────────────────── */ ?>
    <div class="wellme-exp-slide-labels" aria-hidden="true">
        <span data-label="0"><?php esc_html_e( 'WELLME', 'wellme-pamphlets' ); ?></span>
        <span data-label="1"><?php esc_html_e( 'Overview', 'wellme-pamphlets' ); ?></span>
        <span data-label="2"><?php esc_html_e( 'Modules', 'wellme-pamphlets' ); ?></span>
        <span data-label="3"><?php esc_html_e( 'Sum-Up', 'wellme-pamphlets' ); ?></span>
    </div>

    </div><!-- /.wellme-experience -->

<?php include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-popup.php'; ?>
