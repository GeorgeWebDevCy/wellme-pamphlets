<?php
/**
 * Slide 1: WELLME Landing.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$wellme_logo      = get_field( 'wellme_logo', 'option' );
$eu_logo          = get_field( 'eu_logo', 'option' );
$project_title    = get_field( 'project_title', 'option' ) ?: __( 'Wellme-Wellbeing Hubs: Building a sustainable learning environment for Youth in Local communities', 'wellme-pamphlets' );
$landing_subtitle = get_field( 'landing_subtitle', 'option' ) ?: __( 'Wellness Starts From Within', 'wellme-pamphlets' );
$eu_funding_text  = get_field( 'eu_funding_text', 'option' );
$landing_image    = get_field( 'landing_hero_image', 'option' );
$overview_image   = get_field( 'overview_image', 'option' );

$wellme_logo_url    = $wellme_logo['url'] ?? '';
$eu_logo_url        = $eu_logo['url'] ?? '';
$landing_image_url  = $landing_image['url'] ?? ( $overview_image['url'] ?? '' );

// Default hero background from main site if no custom image set
$default_hero_bg = 'https://www.wellmeproject.com/wp-content/uploads/2025/05/top-view-child-learning-how-count-home.webp';
$hero_bg_url     = $landing_image_url ?: $default_hero_bg;
$hero_headline   = $project_title;
?>
<section class="wellme-experience-slide wellme-slide-landing<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'WELLME - Landing', 'wellme-pamphlets' ); ?>">

    <div class="wellme-landing-bg" style="background-image:url('<?php echo esc_url( $hero_bg_url ); ?>');--wellme-landing-image:url('<?php echo esc_url( $hero_bg_url ); ?>');" aria-hidden="true"></div>
    <div class="wellme-landing-overlay" aria-hidden="true"></div>

    <div class="wellme-landing-scroll">
        <div class="wellme-landing-content wellme-scroll-reveal">
            <div class="wellme-landing-copy">
                <?php if ( $wellme_logo_url ) : ?>
                <div class="wellme-landing-logos">
                    <img src="<?php echo esc_url( $wellme_logo_url ); ?>"
                         alt="<?php esc_attr_e( 'WELLME Project Logo', 'wellme-pamphlets' ); ?>"
                         class="wellme-landing-logo">
                </div>
                <?php endif; ?>

                <h1 class="wellme-landing-title"><?php echo esc_html( $hero_headline ); ?></h1>

                <p class="wellme-landing-subtitle"><?php echo esc_html( $landing_subtitle ); ?></p>

                <button type="button" class="wellme-landing-continue" data-experience-goto="1">
                    <?php esc_html_e( 'Continue', 'wellme-pamphlets' ); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- EU Funding Footer -->
    <div class="wellme-landing-footer">
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
</section>
