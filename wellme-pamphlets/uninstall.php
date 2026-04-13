<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 */

// If uninstall not called from WordPress, abort.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Remove plugin options if any were stored.
delete_option( 'wellme_pamphlets_options' );
