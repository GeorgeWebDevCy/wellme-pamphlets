<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/public
 */
class Wellme_Pamphlets_Public {

    private $plugin_name;
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            WELLME_PAMPHLETS_PLUGIN_URL . 'public/css/wellme-pamphlets-public.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        // No jQuery dependency — pure vanilla JS
        wp_enqueue_script(
            $this->plugin_name,
            WELLME_PAMPHLETS_PLUGIN_URL . 'public/js/wellme-pamphlets-public.js',
            [],
            $this->version,
            true
        );

        // Pass AJAX URL, nonce and i18n strings to the script
        wp_localize_script( $this->plugin_name, 'wellmePamphlets', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'wellme_pamphlet_nonce' ),
            'loading' => __( 'Loading…', 'wellme-pamphlets' ),
        ] );
    }

    /**
     * AJAX: return the rendered pamphlet HTML for a given module ID.
     */
    public function ajax_load_pamphlet() {
        check_ajax_referer( 'wellme_pamphlet_nonce', 'nonce' );

        $id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
        if ( ! $id ) {
            wp_send_json_error( __( 'Invalid module ID.', 'wellme-pamphlets' ) );
        }

        // If WPML is active, resolve to the translated post for the current language.
        $id = (int) apply_filters( 'wpml_object_id', $id, 'wellme_module', true );

        $module = get_post( $id );
        if ( ! $module || $module->post_type !== 'wellme_module' || $module->post_status !== 'publish' ) {
            wp_send_json_error( __( 'Module not found.', 'wellme-pamphlets' ) );
        }

        ob_start();
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-pamphlet.php';
        $html = ob_get_clean();

        wp_send_json_success( [ 'html' => $html ] );
    }
}
