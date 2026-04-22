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
?>
<div
    class="wellme-popup-overlay"
    id="wellme-popup-overlay"
    role="dialog"
    aria-modal="true"
    aria-label="<?php esc_attr_e( 'Module Pamphlet', 'wellme-pamphlets' ); ?>"
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
