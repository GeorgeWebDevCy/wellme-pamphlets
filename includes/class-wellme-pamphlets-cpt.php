<?php
/**
 * Registers the wellme_module custom post type.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_CPT {

    public function register_post_types() {
        $labels = [
            'name'               => __( 'Modules', 'wellme-pamphlets' ),
            'singular_name'      => __( 'Module', 'wellme-pamphlets' ),
            'add_new'            => __( 'Add Module', 'wellme-pamphlets' ),
            'add_new_item'       => __( 'Add New Module', 'wellme-pamphlets' ),
            'edit_item'          => __( 'Edit Module', 'wellme-pamphlets' ),
            'new_item'           => __( 'New Module', 'wellme-pamphlets' ),
            'view_item'          => __( 'View Module', 'wellme-pamphlets' ),
            'search_items'       => __( 'Search Modules', 'wellme-pamphlets' ),
            'not_found'          => __( 'No modules found', 'wellme-pamphlets' ),
            'not_found_in_trash' => __( 'No modules found in trash', 'wellme-pamphlets' ),
            'menu_name'          => __( 'WELLME Modules', 'wellme-pamphlets' ),
        ];

        register_post_type( 'wellme_module', [
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-book-alt',
            'menu_position'      => 20,
            'supports'           => [ 'title', 'thumbnail', 'revisions' ],
            'has_archive'        => false,
            'rewrite'            => [ 'slug' => 'wellme-module' ],
            'show_in_rest'       => true,
        ] );
    }
}
