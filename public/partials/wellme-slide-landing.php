<?php
/**
 * Slide 1: WELLME Landing
 *
 * Shows the WELLME logo (with rotation animation), project title,
 * EU logo, funding acknowledgement text, AND partner cards with logos
 * that are clickable to reveal contact info.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$wellme_logo = get_field( 'wellme_logo', 'option' );
$eu_logo     = get_field( 'eu_logo', 'option' );
$project_title    = get_field( 'project_title', 'option' ) ?: __( 'WellMe — Wellbeing Hubs', 'wellme-pamphlets' );
$landing_subtitle = get_field( 'landing_subtitle', 'option' );
$eu_funding_text  = get_field( 'eu_funding_text', 'option' );
$partners         = get_field( 'partners', 'option' ) ?: [];
$landing_image    = get_field( 'landing_hero_image', 'option' );
$overview_image   = get_field( 'overview_image', 'option' );

$wellme_logo_url = $wellme_logo['url'] ?? '';
$eu_logo_url     = $eu_logo['url'] ?? '';
$landing_image_url = $landing_image['url'] ?? ( $overview_image['url'] ?? '' );

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
         aria-label="<?php esc_attr_e( 'WELLME — Landing', 'wellme-pamphlets' ); ?>">

    <div class="wellme-landing-bg" aria-hidden="true"></div>
    <div class="wellme-landing-overlay" aria-hidden="true"></div>

    <div class="wellme-landing-scroll">

        <div class="wellme-landing-content wellme-scroll-reveal">
            <?php if ( $landing_image_url ) : ?>
            <div class="wellme-landing-hero-media" aria-hidden="true">
                <img src="<?php echo esc_url( $landing_image_url ); ?>" alt="">
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

            <?php if ( empty( $hide_partners ) ) : ?>
            <div class="wellme-landing-scroll-hint" aria-hidden="true">
                <span><?php esc_html_e( 'Our Partners', 'wellme-pamphlets' ); ?></span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <?php endif; ?>
        </div>

        <?php /* ── Partners section (scrollable, below hero) ────────── */ ?>
        <?php if ( empty( $hide_partners ) && ! empty( $partners ) ) : ?>
        <div class="wellme-landing-partners">
            <h2 class="wellme-landing-partners-title"><?php esc_html_e( 'Partnership and Click', 'wellme-pamphlets' ); ?></h2>

            <div class="wellme-partners-grid wellme-partners-grid--landing">
                <?php foreach ( $partners as $p_index => $partner ) :
                    $logo_url = $partner['partner_logo']['url'] ?? '';
                    $name     = $partner['partner_name'] ?? '';
                ?>
                <button class="wellme-partner-card wellme-partner-card--landing wellme-scroll-reveal"
                        data-partner-index="<?php echo esc_attr( $p_index ); ?>"
                        aria-expanded="false"
                        aria-controls="wellme-partner-detail-landing-<?php echo esc_attr( $p_index ); ?>">

                    <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>"
                         alt="<?php echo esc_attr( $name ); ?>"
                         class="wellme-partner-logo wellme-partner-logo--landing">
                    <?php endif; ?>

                    <span class="wellme-partner-name wellme-partner-name--landing"><?php echo esc_html( $name ); ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <?php /* Detail panels */ ?>
            <div class="wellme-partner-details">
                <?php foreach ( $partners as $p_index => $partner ) :
                    $name     = $partner['partner_name'] ?? '';
                    $desc     = $partner['partner_description'] ?? '';
                    $email    = $partner['partner_email'] ?? '';
                    $address  = $partner['partner_address'] ?? '';
                    $website  = $partner['partner_website'] ?? '';
                    $logo_url = $partner['partner_logo']['url'] ?? '';
                ?>
                <div class="wellme-partner-detail"
                     id="wellme-partner-detail-landing-<?php echo esc_attr( $p_index ); ?>"
                     hidden>
                    <button class="wellme-partner-detail-close"
                            aria-label="<?php esc_attr_e( 'Close', 'wellme-pamphlets' ); ?>">&times;</button>

                    <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>"
                         alt="<?php echo esc_attr( $name ); ?>"
                         class="wellme-partner-detail-logo">
                    <?php endif; ?>

                    <h3 class="wellme-partner-detail-name"><?php echo esc_html( $name ); ?></h3>

                    <?php if ( $desc ) : ?>
                    <p class="wellme-partner-detail-desc"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>

                    <div class="wellme-partner-detail-contacts">
                        <?php if ( $email ) : ?>
                        <div class="wellme-partner-contact">
                            <span class="wellme-partner-contact-label"><?php esc_html_e( 'Email:', 'wellme-pamphlets' ); ?></span>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                        </div>
                        <?php endif; ?>

                        <?php if ( $address ) : ?>
                        <div class="wellme-partner-contact">
                            <span class="wellme-partner-contact-label"><?php esc_html_e( 'Address:', 'wellme-pamphlets' ); ?></span>
                            <span><?php echo esc_html( $address ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $website ) : ?>
                        <div class="wellme-partner-contact">
                            <span class="wellme-partner-contact-label"><?php esc_html_e( 'Website:', 'wellme-pamphlets' ); ?></span>
                            <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $website ); ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else : ?>
        <p class="wellme-no-partners"><?php esc_html_e( 'No partners added yet. Go to WELLME Modules → Presentation to add partners.', 'wellme-pamphlets' ); ?></p>
        <?php endif; ?>

    </div><!-- /.wellme-landing-scroll -->
</section>
