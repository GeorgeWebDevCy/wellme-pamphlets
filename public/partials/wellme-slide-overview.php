<?php
/**
 * Slide 3: WELLME Overview.
 *
 * @since 1.0.8
 */

defined( 'ABSPATH' ) || exit;

$purpose       = get_field( 'overview_purpose', 'option' );
$need          = get_field( 'overview_need', 'option' );
$results       = get_field( 'overview_results', 'option' );
$image         = get_field( 'overview_image', 'option' );
$landing_image = get_field( 'landing_hero_image', 'option' );
$image_url     = $image['url'] ?? ( $landing_image['url'] ?? '' );

if ( ! $image_url && ! empty( $modules ) && is_array( $modules ) ) {
    foreach ( $modules as $overview_module ) {
        $module_cover = get_field( 'module_cover_image', $overview_module->ID );
        if ( ! empty( $module_cover['url'] ) ) {
            $image_url = $module_cover['url'];
            break;
        }
    }
}

$overview_items = array_values(
    array_filter(
        [
            [
                'key'   => 'purpose',
                'label' => __( 'Purpose', 'wellme-pamphlets' ),
                'title' => __( 'Why WELLME exists', 'wellme-pamphlets' ),
                'body'  => $purpose,
                'color' => '#27ae60',
            ],
            [
                'key'   => 'need',
                'label' => __( 'Need', 'wellme-pamphlets' ),
                'title' => __( 'The challenge for youth trainers', 'wellme-pamphlets' ),
                'body'  => $need,
                'color' => '#1e88c8',
            ],
            [
                'key'   => 'results',
                'label' => __( 'Results', 'wellme-pamphlets' ),
                'title' => __( 'Expected results', 'wellme-pamphlets' ),
                'body'  => $results,
                'color' => '#c6548f',
            ],
        ],
        static function ( $item ) {
            return ! empty( $item['body'] );
        }
    )
);
?>
<section class="wellme-experience-slide wellme-slide-overview<?php echo $is_first ? ' is-active' : ''; ?>"
         data-index="<?php echo esc_attr( $index ); ?>"
         aria-label="<?php esc_attr_e( 'WELLME Overview', 'wellme-pamphlets' ); ?>">

    <div class="wellme-overview-bg" aria-hidden="true"></div>
    <div class="wellme-overview-overlay" aria-hidden="true"></div>

    <div class="wellme-overview-content">
        <h2 class="wellme-overview-main-title"><?php esc_html_e( 'WELLME Overview', 'wellme-pamphlets' ); ?></h2>

        <nav class="wellme-mazda-page-tabs" aria-label="<?php esc_attr_e( 'Overview page tabs', 'wellme-pamphlets' ); ?>">
            <?php foreach ( $overview_items as $item_index => $item ) : ?>
            <button type="button"
                    class="wellme-mazda-page-tab<?php echo 0 === $item_index ? ' is-active' : ''; ?>"
                    data-overview-target="wellme-overview-panel-<?php echo esc_attr( $item['key'] ); ?>"
                    data-overview-label="<?php echo esc_attr( $item['label'] ); ?>"
                    data-overview-index="<?php echo esc_attr( $item_index + 1 ); ?>"
                    data-overview-total="<?php echo esc_attr( count( $overview_items ) ); ?>">
                <?php echo esc_html( $item['label'] ); ?>
            </button>
            <?php endforeach; ?>
        </nav>

        <div class="wellme-overview-visual">
            <?php if ( $image_url ) : ?>
            <div class="wellme-overview-image">
                <img src="<?php echo esc_url( $image_url ); ?>"
                     alt="<?php esc_attr_e( 'WELLME Project Overview', 'wellme-pamphlets' ); ?>">
            </div>
            <?php endif; ?>

            <div class="wellme-overview-state">
                <span class="wellme-overview-state-kicker"><?php esc_html_e( 'WELLME Model', 'wellme-pamphlets' ); ?></span>
                <strong class="wellme-overview-active-label">
                    <?php echo esc_html( $overview_items[0]['label'] ?? __( 'Overview', 'wellme-pamphlets' ) ); ?>
                </strong>
                <?php if ( ! empty( $overview_items ) ) : ?>
                <span class="wellme-overview-state-count">
                    <?php echo esc_html( '1 / ' . count( $overview_items ) ); ?>
                </span>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $overview_items ) ) : ?>
            <div class="wellme-overview-selectors" role="tablist" aria-label="<?php esc_attr_e( 'Overview sections', 'wellme-pamphlets' ); ?>">
                <?php foreach ( $overview_items as $item_index => $item ) : ?>
                <button type="button"
                        class="wellme-overview-selector<?php echo 0 === $item_index ? ' is-active' : ''; ?>"
                        style="--overview-color: <?php echo esc_attr( $item['color'] ); ?>;"
                        data-overview-target="wellme-overview-panel-<?php echo esc_attr( $item['key'] ); ?>"
                        data-overview-label="<?php echo esc_attr( $item['label'] ); ?>"
                        data-overview-index="<?php echo esc_attr( $item_index + 1 ); ?>"
                        data-overview-total="<?php echo esc_attr( count( $overview_items ) ); ?>"
                        role="tab"
                        aria-selected="<?php echo 0 === $item_index ? 'true' : 'false'; ?>"
                        aria-controls="wellme-overview-panel-<?php echo esc_attr( $item['key'] ); ?>">
                    <span class="wellme-sr-only"><?php echo esc_html( $item['label'] ); ?></span>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="wellme-overview-copy">
            <p class="wellme-overview-kicker"><?php esc_html_e( 'Project Overview', 'wellme-pamphlets' ); ?></p>
            <h2 class="wellme-overview-title"><?php esc_html_e( 'WELLME Overview', 'wellme-pamphlets' ); ?></h2>

            <?php if ( ! empty( $overview_items ) ) : ?>
            <div class="wellme-overview-sections">
                <?php foreach ( $overview_items as $item_index => $item ) : ?>
                <section class="wellme-overview-section wellme-scroll-reveal"
                         id="wellme-overview-panel-<?php echo esc_attr( $item['key'] ); ?>"
                         role="tabpanel"
                         <?php echo 0 === $item_index ? '' : 'hidden'; ?>>
                    <span class="wellme-overview-section-label"><?php echo esc_html( $item['label'] ); ?></span>
                    <h3><?php echo esc_html( $item['title'] ); ?></h3>
                    <div class="wellme-overview-section-body"><?php echo wp_kses_post( $item['body'] ); ?></div>
                </section>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
            <p class="wellme-no-overview"><?php esc_html_e( 'Overview content not set yet. Go to WELLME Modules -> Presentation to add overview details.', 'wellme-pamphlets' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
