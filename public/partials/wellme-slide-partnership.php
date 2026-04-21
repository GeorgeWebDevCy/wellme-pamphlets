<?php
/**
 * Slide 2: Partnership
 *
 * Shows clickable partner cards. Each card displays the partner logo and name.
 * Clicking a card expands to show description, email, address, and website.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$partners = get_field( 'partners', 'option' ) ?: [];
?>
<section class="wellme-experience-slide wellme-slide-partnership<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'Partnership', 'wellme-pamphlets' ); ?>">

    <div class="wellme-partnership-bg" aria-hidden="true"></div>
    <div class="wellme-partnership-overlay" aria-hidden="true"></div>

    <div class="wellme-partnership-content">
        <h2 class="wellme-partnership-title"><?php esc_html_e( 'Partnership and Click', 'wellme-pamphlets' ); ?></h2>

        <?php if ( ! empty( $partners ) ) : ?>
        <div class="wellme-partners-grid">
            <?php foreach ( $partners as $p_index => $partner ) :
                $logo_url = $partner['partner_logo']['url'] ?? '';
                $name     = $partner['partner_name'] ?? '';
            ?>
            <div class="wellme-partner-card wellme-scroll-reveal"
                 data-partner-index="<?php echo esc_attr( $p_index ); ?>"
                 role="button"
                 tabindex="0"
                 aria-expanded="false"
                 aria-controls="wellme-partner-detail-<?php echo esc_attr( $p_index ); ?>">

                <?php if ( $logo_url ) : ?>
                <img src="<?php echo esc_url( $logo_url ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>"
                     class="wellme-partner-logo">
                <?php endif; ?>

                <span class="wellme-partner-name"><?php echo esc_html( $name ); ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php /* Detail panels (hidden by default) */ ?>
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
                 id="wellme-partner-detail-<?php echo esc_attr( $p_index ); ?>"
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
        <?php else : ?>
        <p class="wellme-no-partners"><?php esc_html_e( 'No partners added yet. Go to WELLME Modules → Presentation to add partners.', 'wellme-pamphlets' ); ?></p>
        <?php endif; ?>
    </div>
</section>
