<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_Deactivator {

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
