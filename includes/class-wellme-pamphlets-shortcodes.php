<?php
/**
 * Registers all plugin shortcodes.
 *
 * [wellme_module_grid]   – 6-card module index (main entry point)
 * [wellme_pamphlet id=X] – Full interactive pamphlet for one module
 * [wellme_flipcards]     – Sum-Up flip cards (6 modules, front=image, back=motto)
 * [wellme_experience]    – Full-viewport horizontal slider (one shortcode, full-page experience)
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_Shortcodes {

    public function register() {
        add_shortcode( 'wellme_module_grid', [ $this, 'render_module_grid' ] );
        add_shortcode( 'wellme_pamphlet',    [ $this, 'render_pamphlet' ] );
        add_shortcode( 'wellme_flipcards',   [ $this, 'render_flipcards' ] );
        add_shortcode( 'wellme_experience',  [ $this, 'render_experience' ] );
    }

    // ── Module Grid ────────────────────────────────────────────────────────────

    public function render_module_grid( $atts ) {
        $atts = shortcode_atts( [
            'columns' => 3,
            'orderby' => 'meta_value_num',
            'order'   => 'ASC',
        ], $atts, 'wellme_module_grid' );

        $modules = $this->get_modules( $atts['orderby'], $atts['order'] );
        if ( empty( $modules ) ) {
            return '<p class="wellme-no-modules">' . esc_html__( 'No modules found.', 'wellme-pamphlets' ) . '</p>';
        }

        ob_start();
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-module-grid.php';
        return ob_get_clean();
    }

    // ── Individual Pamphlet ────────────────────────────────────────────────────

    public function render_pamphlet( $atts ) {
        $atts = shortcode_atts( [
            'id'   => 0,
            'slug' => '',
        ], $atts, 'wellme_pamphlet' );

        $module = null;

        if ( ! empty( $atts['id'] ) ) {
            $module = get_post( (int) $atts['id'] );
        } elseif ( ! empty( $atts['slug'] ) ) {
            $posts = get_posts( [
                'post_type'      => 'wellme_module',
                'name'           => sanitize_title( $atts['slug'] ),
                'posts_per_page' => 1,
                'post_status'    => 'publish',
            ] );
            $module = $posts[0] ?? null;
        }

        if ( ! $module || $module->post_type !== 'wellme_module' ) {
            return '<p class="wellme-error">' . esc_html__( 'Module not found.', 'wellme-pamphlets' ) . '</p>';
        }

        ob_start();
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-pamphlet.php';
        return ob_get_clean();
    }

    // ── Flip Cards (Sum-Up slide) ──────────────────────────────────────────────

    public function render_flipcards( $atts ) {
        $atts = shortcode_atts( [], $atts, 'wellme_flipcards' );

        $modules = $this->get_modules();
        if ( empty( $modules ) ) {
            return '<p class="wellme-no-modules">' . esc_html__( 'No modules found.', 'wellme-pamphlets' ) . '</p>';
        }

        ob_start();
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-flipcards.php';
        return ob_get_clean();
    }

    // ── Full-screen Experience ────────────────────────────────────────────────

    public function render_experience( $atts ) {
        $atts = shortcode_atts( [], $atts, 'wellme_experience' );

        $modules = $this->get_modules();
        if ( empty( $modules ) ) {
            return '<p class="wellme-no-modules">' . esc_html__( 'No modules found.', 'wellme-pamphlets' ) . '</p>';
        }

        ob_start();
        include WELLME_PAMPHLETS_PLUGIN_DIR . 'public/partials/wellme-experience.php';
        return ob_get_clean();
    }

    // ── Helper ─────────────────────────────────────────────────────────────────

    private function get_modules( $orderby = 'meta_value_num', $order = 'ASC' ) {
        return get_posts( [
            'post_type'      => 'wellme_module',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => $orderby,
            'meta_key'       => 'module_number',
            'order'          => $order,
        ] );
    }
}
