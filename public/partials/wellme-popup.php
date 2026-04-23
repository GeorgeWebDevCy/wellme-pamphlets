<?php
/**
 * Maglr inspire-style popup overlay for module pamphlets.
 *
 * Layout:
 *   Left  = Pamphlet content (scrollable)
 *   Right = Title, subtitle, close, more info expandable
 *
 * @since 1.0.9
 */

defined( 'ABSPATH' ) || exit;

$popup_module_sequence = [];

if ( ! empty( $modules ) && is_array( $modules ) ) {
    foreach ( $modules as $popup_module_index => $popup_module ) {
        if ( ! $popup_module instanceof WP_Post ) {
            continue;
        }

        $popup_module_number = (int) get_field( 'module_number', $popup_module->ID );
        $popup_module_number = $popup_module_number ?: ( $popup_module_index + 1 );
        $popup_module_desc   = get_field( 'module_description', $popup_module->ID );

        $popup_module_sequence[] = [
            'id'       => (string) $popup_module->ID,
            'label'    => sprintf( __( 'Module %d', 'wellme-pamphlets' ), $popup_module_number ),
            'title'    => get_the_title( $popup_module ),
            'subtitle' => (string) get_field( 'module_subtitle', $popup_module->ID ),
            'desc'     => $popup_module_desc ? wp_trim_words( wp_strip_all_tags( $popup_module_desc ), 22 ) : '',
        ];
    }
}
?>
<div
    class="wellme-popup-overlay"
    id="wellme-popup-overlay"
    role="dialog"
    aria-modal="true"
    aria-label="<?php esc_attr_e( 'Module Pamphlet', 'wellme-pamphlets' ); ?>"
    data-module-sequence="<?php echo esc_attr( wp_json_encode( $popup_module_sequence ) ); ?>"
    hidden
>
    <div class="wellme-popup-container">
        <div class="wellme-popup-right" id="wellme-popup-right">
            <div class="wellme-popup-info">
                <div class="wellme-popup-title-block">
                    <div class="wellme-popup-module-label" id="wellme-popup-label"><?php esc_html_e( 'Module', 'wellme-pamphlets' ); ?></div>
                    <h2 class="wellme-popup-title" id="wellme-popup-title"></h2>
                    <p class="wellme-popup-subtitle" id="wellme-popup-subtitle"></p>
                </div>
                <div class="wellme-popup-actions">
                    <button type="button" class="wellme-popup-back-btn" data-close-popup>
                        <?php esc_html_e( 'Back to modules', 'wellme-pamphlets' ); ?>
                    </button>
                    <button type="button" class="wellme-popup-nav-btn wellme-popup-nav-btn--prev" data-popup-module-prev aria-label="<?php esc_attr_e( 'Previous module', 'wellme-pamphlets' ); ?>">
                        <span aria-hidden="true">&lsaquo;</span>
                    </button>
                    <button type="button" class="wellme-popup-nav-btn wellme-popup-nav-btn--next" data-popup-module-next aria-label="<?php esc_attr_e( 'Next module', 'wellme-pamphlets' ); ?>">
                        <span aria-hidden="true">&rsaquo;</span>
                    </button>
                    <button class="wellme-popup-more-btn" id="wellme-popup-more-btn"><?php esc_html_e( 'More info', 'wellme-pamphlets' ); ?></button>
                    <a href="#" class="wellme-popup-close" data-close-popup aria-label="<?php esc_attr_e( 'Close', 'wellme-pamphlets' ); ?>">&times;</a>
                </div>
            </div>

            <div class="wellme-popup-more-info" id="wellme-popup-more-info" hidden>
                <p><strong><?php esc_html_e( 'Description', 'wellme-pamphlets' ); ?></strong></p>
                <p id="wellme-popup-desc"></p>
                <p><strong><?php esc_html_e( 'Module', 'wellme-pamphlets' ); ?></strong></p>
                <p id="wellme-popup-modnum"></p>
            </div>
        </div>

        <div class="wellme-popup-left" id="wellme-popup-left">
            <div class="wellme-popup-body" id="wellme-popup-body">
                <div class="wellme-popup-loading"><?php esc_html_e( 'Loading...', 'wellme-pamphlets' ); ?></div>
            </div>
        </div>

    </div>
</div>
