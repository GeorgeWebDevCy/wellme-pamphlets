<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/admin
 */
class Wellme_Pamphlets_Admin {

    /**
     * @var string
     */
    private $plugin_name;

    /**
     * @var string
     */
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    public function enqueue_styles( $hook_suffix = '' ) {
        wp_enqueue_style(
            $this->plugin_name,
            WELLME_PAMPHLETS_PLUGIN_URL . 'admin/css/wellme-pamphlets-admin.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts( $hook_suffix = '' ) {
        wp_enqueue_script(
            $this->plugin_name,
            WELLME_PAMPHLETS_PLUGIN_URL . 'admin/js/wellme-pamphlets-admin.js',
            [ 'jquery' ],
            $this->version,
            true
        );
    }
}
