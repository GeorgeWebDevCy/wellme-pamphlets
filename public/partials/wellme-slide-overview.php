<?php
/**
 * Slide 3: Wellme Overview
 *
 * Shows project purpose, need, and expected results.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$purpose = get_field( 'overview_purpose', 'option' );
$need    = get_field( 'overview_need', 'option' );
$results = get_field( 'overview_results', 'option' );
$image   = get_field( 'overview_image', 'option' );
$image_url = $image['url'] ?? '';
?>
<section class="wellme-experience-slide wellme-slide-overview<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'WELLME Overview', 'wellme-pamphlets' ); ?>">

    <div class="wellme-overview-bg" aria-hidden="true"></div>
    <div class="wellme-overview-overlay" aria-hidden="true"></div>

    <div class="wellme-overview-content">
        <h2 class="wellme-overview-title"><?php esc_html_e( 'WELLME Overview', 'wellme-pamphlets' ); ?></h2>

        <?php if ( $image_url ) : ?>
        <div class="wellme-overview-image">
            <img src="<?php echo esc_url( $image_url ); ?>"
                 alt="<?php esc_attr_e( 'WELLME Project Overview', 'wellme-pamphlets' ); ?>">
        </div>
        <?php endif; ?>

        <div class="wellme-overview-sections">
            <?php if ( $purpose ) : ?>
            <div class="wellme-overview-section wellme-scroll-reveal">
                <h3><?php esc_html_e( 'Purpose', 'wellme-pamphlets' ); ?></h3>
                <div class="wellme-overview-section-body"><?php echo wp_kses_post( $purpose ); ?></div>
            </div>
            <?php endif; ?>

            <?php if ( $need ) : ?>
            <div class="wellme-overview-section wellme-scroll-reveal">
                <h3><?php esc_html_e( 'Need', 'wellme-pamphlets' ); ?></h3>
                <div class="wellme-overview-section-body"><?php echo wp_kses_post( $need ); ?></div>
            </div>
            <?php endif; ?>

            <?php if ( $results ) : ?>
            <div class="wellme-overview-section wellme-scroll-reveal">
                <h3><?php esc_html_e( 'Expected Results', 'wellme-pamphlets' ); ?></h3>
                <div class="wellme-overview-section-body"><?php echo wp_kses_post( $results ); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ( ! $purpose && ! $need && ! $results ) : ?>
        <p class="wellme-no-overview"><?php esc_html_e( 'Overview content not set yet. Go to WELLME Modules → Presentation to add overview details.', 'wellme-pamphlets' ); ?></p>
        <?php endif; ?>
    </div>
</section>
