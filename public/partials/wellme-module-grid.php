<?php
/**
 * Module grid template — renders the 6 clickable module cards.
 *
 * Variables available from shortcode:
 *   $modules  array  WP_Post[]
 *   $atts     array  shortcode attributes
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wellme-module-grid" data-columns="<?php echo esc_attr( $atts['columns'] ); ?>">
    <?php foreach ( $modules as $module ) :
        $number      = (int) get_field( 'module_number',      $module->ID );
        $subtitle    = get_field( 'module_subtitle',    $module->ID );
        $description = get_field( 'module_description', $module->ID );
        $color       = get_field( 'module_color',       $module->ID ) ?: '#005b96';
        $icon        = get_field( 'module_icon',        $module->ID );
        $cover       = get_field( 'module_cover_image', $module->ID );
        $cover_url   = $cover['sizes']['medium'] ?? ( $cover['url'] ?? '' );
        $icon_url    = $icon['url'] ?? '';
        $pamphlet_id = $module->ID;
    ?>
    <div class="wellme-module-card wellme-scroll-reveal"
         style="--module-color: <?php echo esc_attr( $color ); ?>;"
         data-pamphlet-id="<?php echo esc_attr( $pamphlet_id ); ?>"
         role="button"
         tabindex="0"
         aria-label="<?php echo esc_attr( sprintf( __( 'Open Module %d: %s', 'wellme-pamphlets' ), $number, get_the_title( $module ) ) ); ?>">

        <?php if ( $cover_url ) : ?>
        <div class="wellme-card-image" style="background-image: url('<?php echo esc_url( $cover_url ); ?>');"></div>
        <?php endif; ?>

        <div class="wellme-card-body">
            <span class="wellme-card-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>

            <?php if ( $icon_url ) : ?>
            <img class="wellme-card-icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" aria-hidden="true">
            <?php endif; ?>

            <h3 class="wellme-card-title"><?php echo esc_html( get_the_title( $module ) ); ?></h3>

            <?php if ( $subtitle ) : ?>
            <p class="wellme-card-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>

            <?php if ( $description ) : ?>
            <p class="wellme-card-desc"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>

            <span class="wellme-card-cta"><?php esc_html_e( 'Open Pamphlet', 'wellme-pamphlets' ); ?> &rarr;</span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
// Pamphlet modal container — loaded via AJAX, rendered here as a drawer.
?>
<div class="wellme-pamphlet-modal" id="wellme-pamphlet-modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Module Pamphlet', 'wellme-pamphlets' ); ?>" hidden>
    <div class="wellme-pamphlet-modal-overlay" data-close-modal></div>
    <div class="wellme-pamphlet-modal-inner">
        <button class="wellme-pamphlet-modal-close" data-close-modal aria-label="<?php esc_attr_e( 'Close pamphlet', 'wellme-pamphlets' ); ?>">&times;</button>
        <div class="wellme-pamphlet-modal-content" id="wellme-pamphlet-modal-content">
            <div class="wellme-pamphlet-loading"><?php esc_html_e( 'Loading…', 'wellme-pamphlets' ); ?></div>
        </div>
    </div>
</div>
