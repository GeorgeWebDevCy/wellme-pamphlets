<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_Activator {

    public static function activate() {
        // Flush rewrite rules so any custom post types / endpoints register cleanly.
        flush_rewrite_rules();
    }
}
