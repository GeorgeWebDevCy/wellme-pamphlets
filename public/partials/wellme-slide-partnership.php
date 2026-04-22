<?php
/**
 * Slide 2: Partnership.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$partners      = get_field( 'partners', 'option' ) ?: [];
$partner_count = is_array( $partners ) ? count( $partners ) : 0;
?>
<section class="wellme-experience-slide wellme-slide-partnership<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'Partnership', 'wellme-pamphlets' ); ?>">

    <div class="wellme-partnership-bg" aria-hidden="true"></div>
    <div class="wellme-partnership-overlay" aria-hidden="true"></div>

    <div class="wellme-partnership-content">
        <nav class="wellme-mazda-page-tabs" aria-label="<?php esc_attr_e( 'Presentation page tabs', 'wellme-pamphlets' ); ?>">
            <button type="button" class="wellme-mazda-page-tab is-active" data-experience-goto="1"><?php esc_html_e( 'Partnership', 'wellme-pamphlets' ); ?></button>
            <button type="button" class="wellme-mazda-page-tab" data-experience-goto="2"><?php esc_html_e( 'Overview', 'wellme-pamphlets' ); ?></button>
            <button type="button" class="wellme-mazda-page-tab" data-experience-goto="3"><?php esc_html_e( 'Modules', 'wellme-pamphlets' ); ?></button>
            <button type="button" class="wellme-mazda-page-tab" data-experience-goto="4"><?php esc_html_e( 'Sum-Up', 'wellme-pamphlets' ); ?></button>
        </nav>

        <div class="wellme-partnership-intro">
            <p class="wellme-partnership-kicker"><?php esc_html_e( 'European Partnership', 'wellme-pamphlets' ); ?></p>
            <h2 class="wellme-partnership-title"><?php esc_html_e( 'Partnership', 'wellme-pamphlets' ); ?></h2>
            <p class="wellme-partnership-lede">
                <?php esc_html_e( 'WELLME brings trainers, educators and community organisations together around a practical wellbeing programme for youth work.', 'wellme-pamphlets' ); ?>
            </p>
        </div>

        <nav class="wellme-partnership-menu" aria-label="<?php esc_attr_e( 'Presentation shortcuts', 'wellme-pamphlets' ); ?>">
            <button type="button" class="wellme-partnership-choice" data-experience-goto="2">
                <span class="wellme-partnership-choice-number">01</span>
                <span class="wellme-partnership-choice-title"><?php esc_html_e( 'WELLME Overview', 'wellme-pamphlets' ); ?></span>
                <span class="wellme-partnership-choice-text"><?php esc_html_e( 'Purpose, need and expected project results.', 'wellme-pamphlets' ); ?></span>
            </button>

            <button type="button" class="wellme-partnership-choice" data-experience-goto="3">
                <span class="wellme-partnership-choice-number">02</span>
                <span class="wellme-partnership-choice-title"><?php esc_html_e( 'Training Modules', 'wellme-pamphlets' ); ?></span>
                <span class="wellme-partnership-choice-text"><?php esc_html_e( 'Six interactive pamphlets for trainers.', 'wellme-pamphlets' ); ?></span>
            </button>

            <button type="button" class="wellme-partnership-choice" data-experience-goto="4">
                <span class="wellme-partnership-choice-number">03</span>
                <span class="wellme-partnership-choice-title"><?php esc_html_e( 'Sum-Up', 'wellme-pamphlets' ); ?></span>
                <span class="wellme-partnership-choice-text"><?php esc_html_e( 'Module mottos and final calls to action.', 'wellme-pamphlets' ); ?></span>
            </button>
        </nav>

        <?php if ( ! empty( $partners ) ) : ?>
        <div class="wellme-partner-strip">
            <p class="wellme-partner-strip-label">
                <?php
                echo esc_html(
                    sprintf(
                        /* translators: %d: partner count */
                        _n( '%d partner organisation', '%d partner organisations', $partner_count, 'wellme-pamphlets' ),
                        $partner_count
                    )
                );
                ?>
            </p>

            <div class="wellme-partners-grid">
                <?php foreach ( $partners as $p_index => $partner ) :
                    $logo_url = $partner['partner_logo']['url'] ?? '';
                    $name     = $partner['partner_name'] ?? '';
                ?>
                <button type="button"
                        class="wellme-partner-card wellme-scroll-reveal"
                        data-partner-index="<?php echo esc_attr( $p_index ); ?>"
                        aria-expanded="false"
                        aria-controls="wellme-partner-detail-<?php echo esc_attr( $p_index ); ?>">

                    <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>"
                         alt="<?php echo esc_attr( $name ); ?>"
                         class="wellme-partner-logo">
                    <?php endif; ?>

                    <span class="wellme-partner-name"><?php echo esc_html( $name ); ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

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
        <p class="wellme-no-partners"><?php esc_html_e( 'No partners added yet. Go to WELLME Modules -> Presentation to add partners.', 'wellme-pamphlets' ); ?></p>
        <?php endif; ?>
    </div>
</section>
