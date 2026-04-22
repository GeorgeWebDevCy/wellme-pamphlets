<?php
/**
 * The plugin bootstrap file.
 *
 * @link              https://github.com/GeorgeWebDevCy/wellme-pamphlets
 * @since             1.0.0
 * @package           Wellme_Pamphlets
 *
 * @wordpress-plugin
 * Plugin Name:       WELLME Pamphlets
 * Plugin URI:        https://github.com/GeorgeWebDevCy/wellme-pamphlets
 * Description:       Interactive digital pamphlets for the WELLME EU project — 6 training modules for youth trainers.
 * Version:           1.2.4
 * Author:            George Nicolaou
 * Author URI:        https://github.com/GeorgeWebDevCy
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wellme-pamphlets
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WELLME_PAMPHLETS_VERSION', '1.2.4' );
define( 'WELLME_PAMPHLETS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WELLME_PAMPHLETS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WELLME_PAMPHLETS_PLUGIN_FILE', __FILE__ );

// TEMP: Include fix-modules (DELETE after running once)
if ( file_exists( WELLME_PAMPHLETS_PLUGIN_DIR . 'admin/class-wellme-pamphlets-fix-modules.php' ) ) {
    require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'admin/class-wellme-pamphlets-fix-modules.php';
}

/** Composer autoloader — loads plugin-update-checker and any future dependencies.
 */
if ( file_exists( WELLME_PAMPHLETS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * The code that runs during plugin activation.
 */
function activate_wellme_pamphlets() {
    require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'includes/class-wellme-pamphlets-activator.php';
    Wellme_Pamphlets_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wellme_pamphlets() {
    require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'includes/class-wellme-pamphlets-deactivator.php';
    Wellme_Pamphlets_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wellme_pamphlets' );
register_deactivation_hook( __FILE__, 'deactivate_wellme_pamphlets' );

/**
 * Core plugin class.
 */
require_once WELLME_PAMPHLETS_PLUGIN_DIR . 'includes/class-wellme-pamphlets.php';

/**
 * Begins execution of the plugin.
 */
function run_wellme_pamphlets() {
    $plugin = new Wellme_Pamphlets();
    $plugin->run();
}
run_wellme_pamphlets();
