<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/public
 */
class Wellme_Pamphlets_Public {

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
        wp_enqueue_script(
            $this->plugin_name,
            WELLME_PAMPHLETS_PLUGIN_URL . 'public/js/wellme-pamphlets-public.js',
            [ 'jquery' ],
            $this->version,
            true
        );
    }
}
