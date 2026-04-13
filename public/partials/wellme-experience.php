<?php
/**
 * Template: Full-screen experience slider.
 *
 * Renders all modules as a horizontal full-viewport slider. Each slide
 * shows the module cover, number, title and subtitle. An "Explore" button
 * pulls the full pamphlet into a bottom drawer via AJAX.
 *
 * Variables available:
 *   $modules  array of WP_Post
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$total = count( $modules );
?>
<div
    class="wellme-experience"
    id="wellme-experience"
    role="region"
    aria-label="<?php esc_attr_e( 'WELLME Modules Experience', 'wellme-pamphlets' ); ?>"
>

    <?php /* ── Slides track ─────────────────────────────────────── */ ?>
    <div class="wellme-experience-track" id="wellme-experience-track">

        <?php foreach ( $modules as $index => $module ) :
            $color    = get_field( 'module_color',       $module->ID ) ?: '#005b96';
            $number   = get_field( 'module_number',      $module->ID );
            $subtitle = get_field( 'module_subtitle',    $module->ID );
            $cover    = get_field( 'module_cover_image', $module->ID );
            $cover_url = $cover ? esc_url( $cover['url'] ) : '';
        ?>
        <section
            class="wellme-experience-slide<?php echo $index === 0 ? ' is-active' : ''; ?>"
            data-module-id="<?php echo esc_attr( $module->ID ); ?>"
            data-index="<?php echo $index; ?>"
            style="--module-color: <?php echo esc_attr( $color ); ?>;"
            aria-label="<?php echo esc_attr( sprintf(
                /* translators: 1: module number 2: module title */
                __( 'Module %1$s: %2$s', 'wellme-pamphlets' ),
                $number,
                $module->post_title
            ) ); ?>"
        >
            <?php if ( $cover_url ) : ?>
            <div
                class="wellme-experience-bg"
                style="background-image: url('<?php echo $cover_url; ?>');"
                role="img"
                aria-hidden="true"
            ></div>
            <?php endif; ?>

            <div class="wellme-experience-overlay" aria-hidden="true"></div>

            <div class="wellme-experience-content">
                <?php if ( $number ) : ?>
                <span class="wellme-exp-number">
                    <?php echo esc_html( sprintf(
                        /* translators: %s: module number */
                        __( 'Module %02d', 'wellme-pamphlets' ),
                        $number
                    ) ); ?>
                </span>
                <?php endif; ?>

                <h2 class="wellme-exp-title"><?php echo esc_html( $module->post_title ); ?></h2>

                <?php if ( $subtitle ) : ?>
                <p class="wellme-exp-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>

                <button
                    class="wellme-exp-explore-btn"
                    data-module-id="<?php echo esc_attr( $module->ID ); ?>"
                    aria-expanded="false"
                    aria-controls="wellme-exp-drawer"
                >
                    <?php esc_html_e( 'Explore Module', 'wellme-pamphlets' ); ?>
                </button>
            </div>
        </section>
        <?php endforeach; ?>

    </div><!-- /.wellme-experience-track -->

    <?php /* ── Prev / Next arrows ──────────────────────────────── */ ?>
    <button
        class="wellme-exp-arrow wellme-exp-arrow--prev"
        aria-label="<?php esc_attr_e( 'Previous module', 'wellme-pamphlets' ); ?>"
        hidden
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>

    <button
        class="wellme-exp-arrow wellme-exp-arrow--next"
        aria-label="<?php esc_attr_e( 'Next module', 'wellme-pamphlets' ); ?>"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="9 18 15 12 9 6"/>
        </svg>
    </button>

    <?php /* ── Dot navigation ──────────────────────────────────── */ ?>
    <nav
        class="wellme-exp-dots"
        aria-label="<?php esc_attr_e( 'Module navigation', 'wellme-pamphlets' ); ?>"
    >
        <?php foreach ( $modules as $index => $module ) :
            $num = get_field( 'module_number', $module->ID ) ?: ( $index + 1 );
        ?>
        <button
            class="wellme-exp-dot<?php echo $index === 0 ? ' is-active' : ''; ?>"
            data-index="<?php echo $index; ?>"
            aria-label="<?php echo esc_attr( sprintf(
                /* translators: %s: module number */
                __( 'Go to Module %s', 'wellme-pamphlets' ),
                $num
            ) ); ?>"
            aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>"
        ></button>
        <?php endforeach; ?>
    </nav>

    <?php /* ── Slide counter ─────────────────────────────────────── */ ?>
    <div class="wellme-exp-counter" aria-live="polite" aria-atomic="true">
        <span class="wellme-exp-counter-current">1</span>
        <span aria-hidden="true"> / </span>
        <span class="wellme-exp-counter-total"><?php echo $total; ?></span>
    </div>

</div><!-- /.wellme-experience -->

<?php /* ── Pamphlet drawer (fixed, outside the clipping container) ── */ ?>
<div
    class="wellme-exp-drawer"
    id="wellme-exp-drawer"
    role="dialog"
    aria-modal="true"
    aria-label="<?php esc_attr_e( 'Module details', 'wellme-pamphlets' ); ?>"
    hidden
>
    <div class="wellme-exp-drawer-handle" aria-hidden="true"></div>

    <div class="wellme-exp-drawer-header">
        <button
            class="wellme-exp-drawer-close"
            data-close-drawer
            aria-label="<?php esc_attr_e( 'Close module details', 'wellme-pamphlets' ); ?>"
        >&#10005;</button>
    </div>

    <div class="wellme-exp-drawer-body" id="wellme-exp-drawer-body">
        <?php /* Pamphlet content injected here via AJAX */ ?>
    </div>
</div>
