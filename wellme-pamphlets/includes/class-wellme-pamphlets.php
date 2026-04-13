<?php
/**
 * The core plugin class.
 *
 * Maintains the unique identifier, current version, defines the loader,
 * and sets up the update checker.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Wellme_Pamphlets {

    /**
     * @var Wellme_Pamphlets_Loader
     */
    protected $loader;

    /**
     * @var string The unique identifier of this plugin.
     */
    protected $plugin_name = 'wellme-pamphlets';

    /**
     * @var string The current version of the plugin.
     */
    protected $version;

    public function __construct() {
        $this->version = defined( 'WELLME_PAMPHLETS_VERSION' ) ? WELLME_PAMPHLETS_VERSION : '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->setup_update_checker();
    }

    private function load_dependencies() {
        require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'includes/class-wellme-pamphlets-loader.php';
        require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'includes/class-wellme-pamphlets-i18n.php';
        require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'admin/class-wellme-pamphlets-admin.php';
        require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'public/class-wellme-pamphlets-public.php';

        $this->loader = new Wellme_Pamphlets_Loader();
    }

    private function set_locale() {
        $plugin_i18n = new Wellme_Pamphlets_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    private function define_admin_hooks() {
        $plugin_admin = new Wellme_Pamphlets_Admin( $this->plugin_name, $this->version );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    }

    private function define_public_hooks() {
        $plugin_public = new Wellme_Pamphlets_Public( $this->plugin_name, $this->version );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }

    /**
     * Configure the plugin update checker to pull releases from GitHub.
     *
     * Point this at the GitHub repository that hosts the plugin.
     * When a new release/tag is pushed, WordPress will surface the update
     * in the Plugins screen automatically.
     */
    private function setup_update_checker() {
        if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
            return;
        }

        $update_checker = PucFactory::buildUpdateChecker(
            'https://github.com/GeorgeWebDevCy/wellme-pamphlets/',
            WELLME_PAMPHLETS_PLUGIN_FILE,
            $this->plugin_name
        );

        // Use GitHub releases (tags) as the update source.
        $update_checker->getVcsApi()->enableReleaseAssets();
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
}
