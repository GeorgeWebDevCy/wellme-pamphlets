<?php
/**
 * Slide 1: WELLME Landing.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$wellme_logo      = get_field( 'wellme_logo', 'option' );
$eu_logo          = get_field( 'eu_logo', 'option' );
$project_title    = get_field( 'project_title', 'option' ) ?: __( 'WELLME', 'wellme-pamphlets' );
$landing_subtitle = get_field( 'landing_subtitle', 'option' ) ?: __( 'Empowering youth trainers to build resilience and wellbeing through community power.', 'wellme-pamphlets' );
$eu_funding_text  = get_field( 'eu_funding_text', 'option' );
$landing_image    = get_field( 'landing_hero_image', 'option' );
$overview_image   = get_field( 'overview_image', 'option' );

$wellme_logo_url    = $wellme_logo['url'] ?? '';
$eu_logo_url        = $eu_logo['url'] ?? '';
$landing_image_url  = $landing_image['url'] ?? ( $overview_image['url'] ?? '' );
$hero_media_url     = $wellme_logo_url ?: $landing_image_url;
$hero_media_is_logo = (bool) $wellme_logo_url;

if ( ! $landing_image_url && ! empty( $modules ) && is_array( $modules ) ) {
    foreach ( $modules as $landing_module ) {
        $module_cover = get_field( 'module_cover_image', $landing_module->ID );
        if ( ! empty( $module_cover['url'] ) ) {
            $landing_image_url = $module_cover['url'];
            break;
        }
    }
}
?>
<section class="wellme-experience-slide wellme-slide-landing<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'WELLME - Landing', 'wellme-pamphlets' ); ?>">

    <div class="wellme-landing-bg" aria-hidden="true"></div>
    <div class="wellme-landing-overlay" aria-hidden="true"></div>

    <div class="wellme-landing-scroll">
        <div class="wellme-landing-content wellme-scroll-reveal">
            <?php if ( $hero_media_url ) : ?>
            <div class="wellme-landing-hero-media<?php echo $hero_media_is_logo ? ' is-logo-hero' : ''; ?>" aria-hidden="true">
                <img src="<?php echo esc_url( $hero_media_url ); ?>"
                     alt=""
                     class="<?php echo $hero_media_is_logo ? 'wellme-landing-hero-logo wellme-logo-spin' : ''; ?>">
            </div>
            <?php endif; ?>

            <div class="wellme-landing-copy">
                <?php if ( $wellme_logo_url ) : ?>
                <div class="wellme-landing-logos">
                    <img src="<?php echo esc_url( $wellme_logo_url ); ?>"
                         alt="<?php esc_attr_e( 'WELLME Project Logo', 'wellme-pamphlets' ); ?>"
                         class="wellme-landing-logo wellme-logo-spin">
                </div>
                <?php endif; ?>

                <p class="wellme-landing-kicker"><?php esc_html_e( 'Digital Training Pamphlets', 'wellme-pamphlets' ); ?></p>

                <h1 class="wellme-landing-title"><?php echo esc_html( $project_title ); ?></h1>

                <p class="wellme-landing-subtitle"><?php echo esc_html( $landing_subtitle ); ?></p>

                <button type="button" class="wellme-landing-continue" data-experience-goto="1">
                    <?php esc_html_e( 'Continue', 'wellme-pamphlets' ); ?>
                </button>

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
                    <?php esc_html_e( 'Erasmus+ KA220-YOU - Cooperation partnerships in youth', 'wellme-pamphlets' ); ?>
                </div>
            </div>
        </div>
    </div>
</section>
