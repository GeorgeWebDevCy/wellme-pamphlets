<?php
/**
 * Sum-Up flip cards template.
 *
 * 6 cards — front shows module cover image + number/title,
 * back shows module motto. Click/hover triggers CSS 3D flip.
 *
 * Variables available:
 *   $modules  WP_Post[]
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wellme-flipcards-grid">
    <?php foreach ( $modules as $module ) :
        $number    = (int) get_field( 'module_number',       $module->ID );
        $motto     = get_field( 'module_motto',        $module->ID );
        $color     = get_field( 'module_color',        $module->ID ) ?: '#005b96';
        $cover     = get_field( 'module_cover_image',  $module->ID );
        $cover_url = $cover['sizes']['medium'] ?? ( $cover['url'] ?? '' );
    ?>
    <div class="wellme-flipcard wellme-scroll-reveal"
         style="--module-color: <?php echo esc_attr( $color ); ?>;"
         role="button"
         tabindex="0"
         aria-label="<?php echo esc_attr( sprintf( __( 'Module %d: %s — click to reveal motto', 'wellme-pamphlets' ), $number, get_the_title( $module ) ) ); ?>">

        <div class="wellme-flipcard-inner">

            <?php /* Front */ ?>
            <div class="wellme-flipcard-front">
                <?php if ( $cover_url ) : ?>
                <div class="wellme-flipcard-image" style="background-image: url('<?php echo esc_url( $cover_url ); ?>');"></div>
                <?php endif; ?>
                <div class="wellme-flipcard-front-body">
                    <span class="wellme-flipcard-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
                    <h3 class="wellme-flipcard-title"><?php echo esc_html( get_the_title( $module ) ); ?></h3>
                </div>
            </div>

            <?php /* Back */ ?>
            <div class="wellme-flipcard-back">
                <span class="wellme-flipcard-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
                <?php if ( $motto ) : ?>
                <p class="wellme-flipcard-motto">&ldquo;<?php echo esc_html( $motto ); ?>&rdquo;</p>
                <?php else : ?>
                <p class="wellme-flipcard-motto wellme-placeholder"><?php esc_html_e( 'Motto coming soon.', 'wellme-pamphlets' ); ?></p>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <?php endforeach; ?>
</div>
