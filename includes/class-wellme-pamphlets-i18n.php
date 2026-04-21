<?php
/**
 * Define the internationalisation functionality.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_i18n {

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wellme-pamphlets',
            false,
            dirname( plugin_basename( WELLME_PAMPHLETS_PLUGIN_FILE ) ) . '/languages/'
        );
    }
}
