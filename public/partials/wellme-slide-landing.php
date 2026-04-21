<?php
/**
 * Slide 1: WELLME Landing
 *
 * Shows the WELLME logo (with rotation animation), project title,
 * EU logo, and funding acknowledgement text.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$wellme_logo = get_field( 'wellme_logo', 'option' );
$eu_logo     = get_field( 'eu_logo', 'option' );
$project_title    = get_field( 'project_title', 'option' ) ?: __( 'WellMe — Wellbeing Hubs', 'wellme-pamphlets' );
$landing_subtitle = get_field( 'landing_subtitle', 'option' );
$eu_funding_text  = get_field( 'eu_funding_text', 'option' );

$wellme_logo_url = $wellme_logo['url'] ?? '';
$eu_logo_url     = $eu_logo['url'] ?? '';
?>
<section class="wellme-experience-slide wellme-slide-landing<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'WELLME — Landing', 'wellme-pamphlets' ); ?>">

    <div class="wellme-landing-bg" aria-hidden="true"></div>
    <div class="wellme-landing-overlay" aria-hidden="true"></div>

    <div class="wellme-landing-content wellme-scroll-reveal">

        <?php if ( $wellme_logo_url ) : ?>
        <div class="wellme-landing-logos">
            <img src="<?php echo esc_url( $wellme_logo_url ); ?>"
                 alt="<?php esc_attr_e( 'WELLME Project Logo', 'wellme-pamphlets' ); ?>"
                 class="wellme-landing-logo wellme-logo-spin">
        </div>
        <?php endif; ?>

        <h1 class="wellme-landing-title"><?php echo esc_html( $project_title ); ?></h1>

        <?php if ( $landing_subtitle ) : ?>
        <p class="wellme-landing-subtitle"><?php echo esc_html( $landing_subtitle ); ?></p>
        <?php endif; ?>

        <?php if ( $eu_logo_url ) : ?>
        <div class="wellme-landing-eu">
            <img src="<?php echo esc_url( $eu_logo_url ); ?>"
                 alt="<?php esc_attr_e( 'Co-funded by the European Union', 'wellme-pamphlets' ); ?>"
                 class="wellme-landing-eu-logo">
        </div>
        <?php endif; ?>

        <?php if ( $eu_funding_text ) : ?>
        <p class="wellme-landing-eu-text"><?php echo esc_html( $eu_funding_text ); ?></p>
        <?php endif; ?>

        <div class="wellme-landing-agreement">
            <?php esc_html_e( 'Erasmus+ KA220-YOU — Cooperation partnerships in youth', 'wellme-pamphlets' ); ?>
        </div>
    </div>
</section>
