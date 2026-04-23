<?php
/**
 * Registers all ACF field groups for the wellme_module CPT.
 *
 * All fields are code-defined via acf_add_local_field_group() so they are
 * version-controlled and require no manual UI setup. ACF Pro must be active.
 *
 * @since      1.0.0
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */
class Wellme_Pamphlets_ACF {

    public function prepare_module_activity_label( $field ) {
        $module_number = $this->get_current_module_number();
        $field['label'] = $module_number
            ? sprintf( __( 'Module %d Activity', 'wellme-pamphlets' ), $module_number )
            : __( 'Module Activity', 'wellme-pamphlets' );

        return $field;
    }

    public function prepare_module_wellme_goals_label( $field ) {
        $module_number = $this->get_current_module_number();
        $field['label'] = $module_number
            ? sprintf( __( 'Module %d WellMe Goals', 'wellme-pamphlets' ), $module_number )
            : __( 'Module WellMe Goals', 'wellme-pamphlets' );

        return $field;
    }

    private function get_current_module_number() {
        $post_id = 0;

        if ( isset( $_GET['post'] ) ) {
            $post_id = absint( $_GET['post'] );
        } elseif ( isset( $_POST['post_ID'] ) ) {
            $post_id = absint( $_POST['post_ID'] );
        } else {
            global $post;

            if ( $post instanceof WP_Post ) {
                $post_id = (int) $post->ID;
            }
        }

        if ( ! $post_id || 'wellme_module' !== get_post_type( $post_id ) ) {
            return 0;
        }

        return (int) get_post_meta( $post_id, 'module_number', true );
    }

    public function migrate_overview_legacy_fields_to_repeater() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
            return;
        }

        $existing_blocks = get_field( 'overview_blocks', 'option' );
        if ( ! empty( $existing_blocks ) && is_array( $existing_blocks ) ) {
            return;
        }

        $legacy_items = [
            [
                'label' => __( 'Purpose', 'wellme-pamphlets' ),
                'title' => __( 'Why WELLME exists', 'wellme-pamphlets' ),
                'body'  => get_field( 'overview_purpose', 'option' ) ?: get_option( 'options_overview_purpose' ),
                'color' => '#27ae60',
            ],
            [
                'label' => __( 'Need', 'wellme-pamphlets' ),
                'title' => __( 'The challenge for youth trainers', 'wellme-pamphlets' ),
                'body'  => get_field( 'overview_need', 'option' ) ?: get_option( 'options_overview_need' ),
                'color' => '#1e88c8',
            ],
            [
                'label' => __( 'Results', 'wellme-pamphlets' ),
                'title' => __( 'Expected results', 'wellme-pamphlets' ),
                'body'  => get_field( 'overview_results', 'option' ) ?: get_option( 'options_overview_results' ),
                'color' => '#c6548f',
            ],
        ];

        $blocks = [];

        foreach ( $legacy_items as $item ) {
            if ( empty( $item['body'] ) ) {
                continue;
            }

            $blocks[] = $item;
        }

        if ( ! empty( $blocks ) ) {
            update_field( 'field_wm_pres_overview_blocks', $blocks, 'option' );
        }
    }

    public function migrate_sumup_module_fields_to_repeater() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
            return;
        }

        $existing_cards = get_field( 'sumup_cards', 'option' );
        if ( ! empty( $existing_cards ) && is_array( $existing_cards ) ) {
            return;
        }

        $modules = get_posts( [
            'post_type'      => 'wellme_module',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'module_number',
            'order'          => 'ASC',
        ] );

        if ( empty( $modules ) ) {
            return;
        }

        $cards = [];

        foreach ( $modules as $index => $module ) {
            $number = (int) get_field( 'module_number', $module->ID );
            $number = $number ?: ( $index + 1 );
            $image  = get_field( 'module_cover_image', $module->ID );

            $cards[] = [
                'card_label' => sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ),
                'card_title' => get_the_title( $module ),
                'card_motto' => get_field( 'module_motto', $module->ID ) ?: '',
                'card_image' => $image['ID'] ?? '',
                'card_color' => get_field( 'module_color', $module->ID ) ?: '#005b96',
            ];
        }

        if ( ! empty( $cards ) ) {
            update_field( 'field_wm_pres_sumup_cards', $cards, 'option' );
        }
    }

    public function register_field_groups() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        // ── Options page: Presentation Slides ────────────────────────────────

        if ( function_exists( 'acf_add_options_page' ) ) {
            acf_add_options_page( [
                'page_title' => 'Presentation Settings',
                'menu_title' => 'Presentation',
                'menu_slug'  => 'wellme-presentation',
                'capability' => 'manage_options',
                'icon_url'   => 'dashicons-slides',
                'position'   => null,
                'parent_slug' => 'edit.php?post_type=wellme_module',
                'redirect'   => true,
            ] );
        }

        acf_add_local_field_group( [
            'key'      => 'group_wellme_presentation',
            'title'    => 'Presentation Slides',
            'location' => [
                [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'wellme-presentation' ] ],
            ],
            'fields'   => [

                // ── Slide 1: Landing ─────────────────────────────────────────

                [ 'key' => 'field_wm_pres_tab_slide1', 'label' => 'Slide 1 — Landing', 'type' => 'tab', 'placement' => 'top' ],

                [
                    'key'          => 'field_wm_pres_wellme_logo',
                    'label'        => 'WELLME Logo',
                    'name'         => 'wellme_logo',
                    'type'         => 'image',
                    'return_format'=> 'array',
                    'preview_size' => 'medium',
                    'instructions' => 'Main WELLME project logo shown on the landing slide.',
                ],
                [
                    'key'          => 'field_wm_pres_eu_logo',
                    'label'        => 'EU Logo',
                    'name'         => 'eu_logo',
                    'type'         => 'image',
                    'return_format'=> 'array',
                    'preview_size' => 'medium',
                    'instructions' => 'European Union co-funded logo.',
                ],
                [
                    'key'          => 'field_wm_pres_project_title',
                    'label'        => 'Project Title',
                    'name'         => 'project_title',
                    'type'         => 'text',
                    'default_value'=> 'WellMe — Wellbeing Hubs. Building a sustainable learning environment for Youth in Local communities',
                    'instructions' => 'Full project title shown on the landing slide.',
                ],
                [
                    'key'          => 'field_wm_pres_landing_subtitle',
                    'label'        => 'Landing Subtitle',
                    'name'         => 'landing_subtitle',
                    'type'         => 'text',
                    'default_value'=> 'Creating Hands-On Training for Implementing the Five Elements of Wellbeing',
                    'instructions' => 'Subtitle or tagline below the project title.',
                ],
                [
                    'key'          => 'field_wm_pres_eu_funding_text',
                    'label'        => 'EU Funding Acknowledgement',
                    'name'         => 'eu_funding_text',
                    'type'         => 'textarea',
                    'rows'         => 3,
                    'default_value'=> 'Funded by the European Union. Views and opinions expressed are however those of the author(s) only and do not necessarily reflect those of the European Union or the European Education and Culture Executive Agency (EACEA). Neither the European Union nor the granting authority can be held responsible for them.',
                    'instructions' => 'Standard EU funding disclaimer text.',
                ],

                // ── Slide 2: Partnership ─────────────────────────────────────

                [ 'key' => 'field_wm_pres_tab_slide2', 'label' => 'Slide 2 — Partnership', 'type' => 'tab', 'placement' => 'top' ],

                [
                    'key'          => 'field_wm_pres_partners',
                    'label'        => 'Partners',
                    'name'         => 'partners',
                    'type'         => 'repeater',
                    'instructions' => 'Each partner gets a clickable card that reveals contact details.',
                    'layout'       => 'block',
                    'button_label' => 'Add Partner',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_partner_name',
                            'label' => 'Partner Name',
                            'name'  => 'partner_name',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_wm_partner_logo',
                            'label' => 'Partner Logo',
                            'name'  => 'partner_logo',
                            'type'  => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'thumbnail',
                        ],
                        [
                            'key'   => 'field_wm_partner_description',
                            'label' => 'Description',
                            'name'  => 'partner_description',
                            'type'  => 'textarea',
                            'rows'  => 3,
                        ],
                        [
                            'key'   => 'field_wm_partner_email',
                            'label' => 'Email',
                            'name'  => 'partner_email',
                            'type'  => 'email',
                        ],
                        [
                            'key'   => 'field_wm_partner_address',
                            'label' => 'Address',
                            'name'  => 'partner_address',
                            'type'  => 'textarea',
                            'rows'  => 2,
                        ],
                        [
                            'key'   => 'field_wm_partner_website',
                            'label' => 'Website',
                            'name'  => 'partner_website',
                            'type'  => 'url',
                        ],
                    ],
                ],

                // ── Slide 3: Overview ────────────────────────────────────────

                [ 'key' => 'field_wm_pres_tab_slide3', 'label' => 'Slide 3 — Overview', 'type' => 'tab', 'placement' => 'top' ],

                [
                    'key'          => 'field_wm_pres_overview_blocks',
                    'label'        => 'Overview Blocks',
                    'name'         => 'overview_blocks',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'collapsed'    => 'field_wm_pres_overview_block_title',
                    'button_label' => 'Add overview block',
                    'instructions' => 'Add the Slide 3 information blocks here. Existing legacy Purpose / Need / Results values remain as a public fallback until blocks are added.',
                    'sub_fields'   => [
                        [
                            'key'          => 'field_wm_pres_overview_block_label',
                            'label'        => 'Short Label',
                            'name'         => 'label',
                            'type'         => 'text',
                            'instructions' => 'Short selector label, for example Purpose, Need, Impact, Results.',
                        ],
                        [
                            'key'          => 'field_wm_pres_overview_block_title',
                            'label'        => 'Title',
                            'name'         => 'title',
                            'type'         => 'text',
                            'instructions' => 'Heading shown above this block of text.',
                        ],
                        [
                            'key'          => 'field_wm_pres_overview_block_body',
                            'label'        => 'Text',
                            'name'         => 'body',
                            'type'         => 'wysiwyg',
                            'tabs'         => 'all',
                            'toolbar'      => 'basic',
                            'media_upload' => 0,
                        ],
                        [
                            'key'           => 'field_wm_pres_overview_block_image',
                            'label'         => 'Block Image',
                            'name'          => 'image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                            'instructions'  => 'Optional image shown when this overview block is selected. Falls back to the global Overview Image when empty.',
                        ],
                        [
                            'key'           => 'field_wm_pres_overview_block_color',
                            'label'         => 'Accent Colour',
                            'name'          => 'color',
                            'type'          => 'color_picker',
                            'default_value' => '#1e88c8',
                        ],
                    ],
                ],
                [
                    'key'          => 'field_wm_pres_overview_image',
                    'label'        => 'Overview Image',
                    'name'         => 'overview_image',
                    'type'         => 'image',
                    'return_format'=> 'array',
                    'preview_size' => 'medium',
                    'instructions' => 'Fallback image for overview blocks that do not have their own block image.',
                ],

                // Slide 5: Sum-Up.

                [ 'key' => 'field_wm_pres_tab_slide5', 'label' => 'Slide 5 - Sum-Up', 'type' => 'tab', 'placement' => 'top' ],

                [
                    'key'           => 'field_wm_pres_sumup_nav_label',
                    'label'         => 'Navigation Label',
                    'name'          => 'sumup_nav_label',
                    'type'          => 'text',
                    'default_value' => 'Sum-Up',
                    'instructions'  => 'Short label used in the presentation navigation and slide dots.',
                ],
                [
                    'key'           => 'field_wm_pres_sumup_title',
                    'label'         => 'Slide Title',
                    'name'          => 'sumup_title',
                    'type'          => 'text',
                    'default_value' => 'Sum-Up',
                ],
                [
                    'key'           => 'field_wm_pres_sumup_subtitle',
                    'label'         => 'Slide Subtitle',
                    'name'          => 'sumup_subtitle',
                    'type'          => 'text',
                    'default_value' => 'Click each card to reveal the module motto.',
                ],
                [
                    'key'          => 'field_wm_pres_sumup_cards',
                    'label'        => 'Sum-Up Cards',
                    'name'         => 'sumup_cards',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'collapsed'    => 'field_wm_pres_sumup_card_title',
                    'button_label' => 'Add Sum-Up card',
                    'instructions' => 'Edit the cards shown on the Sum-Up slide. If this is left empty, the slide falls back to the module titles, cover images, colours, and mottos.',
                    'sub_fields'   => [
                        [
                            'key'          => 'field_wm_pres_sumup_card_label',
                            'label'        => 'Card Label',
                            'name'         => 'card_label',
                            'type'         => 'text',
                            'instructions' => 'For example: Module 1.',
                        ],
                        [
                            'key'   => 'field_wm_pres_sumup_card_title',
                            'label' => 'Card Title',
                            'name'  => 'card_title',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_wm_pres_sumup_card_motto',
                            'label' => 'Card Motto / Back Text',
                            'name'  => 'card_motto',
                            'type'  => 'textarea',
                            'rows'  => 3,
                        ],
                        [
                            'key'           => 'field_wm_pres_sumup_card_image',
                            'label'         => 'Card Image',
                            'name'          => 'card_image',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'           => 'field_wm_pres_sumup_card_color',
                            'label'         => 'Accent Colour',
                            'name'          => 'card_color',
                            'type'          => 'color_picker',
                            'default_value' => '#005b96',
                            'enable_opacity' => 0,
                            'return_format' => 'string',
                        ],
                    ],
                ],
            ],
        ] );

        acf_add_local_field_group( [
            'key'      => 'group_wellme_module',
            'title'    => 'Module Details',
            'location' => [
                [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'wellme_module' ] ],
            ],
            'menu_order'  => 0,
            'position'    => 'normal',
            'style'       => 'default',
            'label_placement' => 'top',
            'fields'     => [

                // ── Identity ──────────────────────────────────────────────────

                [
                    'key'           => 'field_wm_tab_identity',
                    'label'         => 'Identity',
                    'type'          => 'tab',
                    'placement'     => 'top',
                ],
                [
                    'key'           => 'field_wm_number',
                    'label'         => 'Module Number',
                    'name'          => 'module_number',
                    'type'          => 'number',
                    'instructions'  => '1 – 6',
                    'required'      => 1,
                    'min'           => 1,
                    'max'           => 6,
                ],
                [
                    'key'           => 'field_wm_subtitle',
                    'label'         => 'Subtitle',
                    'name'          => 'module_subtitle',
                    'type'          => 'text',
                    'instructions'  => 'Short tagline shown on the module card.',
                ],
                [
                    'key'           => 'field_wm_description',
                    'label'         => 'Short Description',
                    'name'          => 'module_description',
                    'type'          => 'textarea',
                    'rows'          => 3,
                    'instructions'  => 'Shown on the module card on hover / in the pamphlet intro.',
                ],
                [
                    'key'           => 'field_wm_color',
                    'label'         => 'Module Colour',
                    'name'          => 'module_color',
                    'type'          => 'color_picker',
                    'instructions'  => 'Accent colour for this module\'s card, hotspots and buttons.',
                    'default_value' => '#005b96',
                    'enable_opacity' => 0,
                    'return_format' => 'string',
                ],
                [
                    'key'           => 'field_wm_icon',
                    'label'         => 'Module Icon',
                    'name'          => 'module_icon',
                    'type'          => 'image',
                    'return_format' => 'array',
                    'preview_size'  => 'thumbnail',
                    'instructions'  => 'SVG or PNG icon shown on the module card.',
                ],
                [
                    'key'           => 'field_wm_cover_image',
                    'label'         => 'Cover / Hero Image',
                    'name'          => 'module_cover_image',
                    'type'          => 'image',
                    'return_format' => 'array',
                    'preview_size'  => 'medium',
                    'instructions'  => 'Full-width hero image for the pamphlet cover slide.',
                ],
                [
                    'key'           => 'field_wm_motto',
                    'label'         => 'Module Motto',
                    'name'          => 'module_motto',
                    'type'          => 'text',
                    'instructions'  => 'Shown on the back of the flip card in the Sum-Up slide.',
                ],

                // ── Learning Outcomes ──────────────────────────────────────────

                [
                    'key'       => 'field_wm_tab_outcomes',
                    'label'     => 'Learning Outcomes',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_learning_outcomes',
                    'label'        => 'Learning Outcomes',
                    'name'         => 'module_learning_outcomes',
                    'type'         => 'repeater',
                    'instructions' => 'Each outcome becomes a clickable button that opens a side-panel overlay (Partou pattern).',
                    'min'          => 0,
                    'max'          => 10,
                    'layout'       => 'block',
                    'button_label' => 'Add Outcome',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_outcome_title',
                            'label' => 'Outcome Title',
                            'name'  => 'outcome_title',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_wm_outcome_detail',
                            'label' => 'Outcome Detail',
                            'name'  => 'outcome_detail',
                            'type'  => 'wysiwyg',
                            'tabs'  => 'all',
                            'toolbar' => 'basic',
                            'media_upload' => 0,
                        ],
                        [
                            'key'   => 'field_wm_outcome_icon',
                            'label' => 'Icon (optional)',
                            'name'  => 'outcome_icon',
                            'type'  => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'thumbnail',
                        ],
                    ],
                ],

                // ── Activity Steps ─────────────────────────────────────────────

                [
                    'key'       => 'field_wm_tab_steps',
                    'label'     => 'Activity Steps',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_exercise_steps',
                    'label'        => 'Activity Steps',
                    'name'         => 'module_exercise_steps',
                    'type'         => 'repeater',
                    'instructions' => 'Each step gets a numbered pulsing hotspot dot. Clicking it opens a panel with the step content (Outremer pattern).',
                    'min'          => 0,
                    'max'          => 20,
                    'layout'       => 'block',
                    'button_label' => 'Add Step',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_step_title',
                            'label' => 'Step Title',
                            'name'  => 'step_title',
                            'type'  => 'text',
                        ],
                        [
                            'key'   => 'field_wm_step_content',
                            'label' => 'Step Content',
                            'name'  => 'step_content',
                            'type'  => 'wysiwyg',
                            'tabs'  => 'all',
                            'toolbar' => 'basic',
                            'media_upload' => 0,
                        ],
                        [
                            'key'   => 'field_wm_step_image',
                            'label' => 'Step Image',
                            'name'  => 'step_image',
                            'type'  => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                        ],
                        [
                            'key'          => 'field_wm_step_hotspot_x',
                            'label'        => 'Hotspot X Position (%)',
                            'name'         => 'step_hotspot_x',
                            'type'         => 'number',
                            'instructions' => 'Left offset of the pulsing dot on the layout image (0–100).',
                            'default_value'=> 50,
                            'min'          => 0,
                            'max'          => 100,
                        ],
                        [
                            'key'          => 'field_wm_step_hotspot_y',
                            'label'        => 'Hotspot Y Position (%)',
                            'name'         => 'step_hotspot_y',
                            'type'         => 'number',
                            'instructions' => 'Top offset of the pulsing dot on the layout image (0–100).',
                            'default_value'=> 50,
                            'min'          => 0,
                            'max'          => 100,
                        ],
                    ],
                ],

                // ── Chapters ───────────────────────────────────────────────────

                [
                    'key'       => 'field_wm_tab_chapters',
                    'label'     => 'Module Activity',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_activity_aims',
                    'label'        => 'Aims',
                    'name'         => 'module_activity_aims',
                    'type'         => 'group',
                    'instructions' => 'Content shown as three tabs at the top of the Module Activity section.',
                    'layout'       => 'block',
                    'sub_fields'   => [
                        [
                            'key'          => 'field_wm_activity_aim',
                            'label'        => 'Aim',
                            'name'         => 'activity_aim',
                            'type'         => 'wysiwyg',
                            'tabs'         => 'all',
                            'toolbar'      => 'full',
                            'media_upload' => 1,
                        ],
                        [
                            'key'          => 'field_wm_activity_youth_worker',
                            'label'        => 'Youth Worker',
                            'name'         => 'activity_youth_worker',
                            'type'         => 'wysiwyg',
                            'tabs'         => 'all',
                            'toolbar'      => 'full',
                            'media_upload' => 1,
                        ],
                        [
                            'key'          => 'field_wm_activity_wellme_goals',
                            'label'        => 'Module WellMe Goals',
                            'name'         => 'activity_wellme_goals',
                            'type'         => 'wysiwyg',
                            'tabs'         => 'all',
                            'toolbar'      => 'full',
                            'media_upload' => 1,
                        ],
                    ],
                ],
                [
                    'key'          => 'field_wm_chapters',
                    'label'        => 'Module Activity',
                    'name'         => 'module_chapters',
                    'type'         => 'repeater',
                    'instructions' => 'Each chapter becomes a navigation button inside the pamphlet (Partou chapter-nav pattern).',
                    'min'          => 0,
                    'max'          => 10,
                    'layout'       => 'block',
                    'button_label' => 'Add Chapter',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_chapter_title',
                            'label' => 'Chapter Title',
                            'name'  => 'chapter_title',
                            'type'  => 'text',
                        ],
                        [
                            'key'     => 'field_wm_chapter_content',
                            'label'   => 'Chapter Content',
                            'name'    => 'chapter_content',
                            'type'    => 'wysiwyg',
                            'tabs'    => 'all',
                            'toolbar' => 'full',
                            'media_upload' => 1,
                        ],
                        [
                            'key'   => 'field_wm_chapter_image',
                            'label' => 'Chapter Image',
                            'name'  => 'chapter_image',
                            'type'  => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'medium',
                        ],
                    ],
                ],

                // ── Media ──────────────────────────────────────────────────────

                [
                    'key'       => 'field_wm_tab_assessment',
                    'label'     => 'Assessment',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_assessment_questions',
                    'label'        => 'Assessment Questions',
                    'name'         => 'module_assessment_questions',
                    'type'         => 'repeater',
                    'instructions' => 'Multiple-choice questions shown in the interactive assessment section.',
                    'min'          => 0,
                    'max'          => 20,
                    'layout'       => 'block',
                    'button_label' => 'Add Question',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_assessment_question_prompt',
                            'label' => 'Question Prompt',
                            'name'  => 'assessment_question_prompt',
                            'type'  => 'textarea',
                            'rows'  => 2,
                        ],
                        [
                            'key'          => 'field_wm_assessment_question_options',
                            'label'        => 'Answer Options',
                            'name'         => 'assessment_question_options',
                            'type'         => 'repeater',
                            'min'          => 2,
                            'max'          => 8,
                            'layout'       => 'table',
                            'button_label' => 'Add Option',
                            'sub_fields'   => [
                                [
                                    'key'           => 'field_wm_assessment_option_key',
                                    'label'         => 'Option Key',
                                    'name'          => 'option_key',
                                    'type'          => 'text',
                                    'wrapper'       => [ 'width' => 20 ],
                                    'maxlength'     => 2,
                                    'default_value' => 'A',
                                ],
                                [
                                    'key'     => 'field_wm_assessment_option_text',
                                    'label'   => 'Option Text',
                                    'name'    => 'option_text',
                                    'type'    => 'textarea',
                                    'rows'    => 2,
                                    'wrapper' => [ 'width' => 80 ],
                                ],
                            ],
                        ],
                        [
                            'key'           => 'field_wm_assessment_question_correct_option',
                            'label'         => 'Correct Option Key',
                            'name'          => 'assessment_question_correct_option',
                            'type'          => 'text',
                            'instructions'  => 'For example: A, B, C, or D.',
                            'maxlength'     => 2,
                            'default_value' => 'A',
                        ],
                        [
                            'key'           => 'field_wm_assessment_question_explanation',
                            'label'         => 'Explanation',
                            'name'          => 'assessment_question_explanation',
                            'type'          => 'textarea',
                            'rows'          => 3,
                            'instructions'  => 'Shown after the learner checks their answers.',
                        ],
                    ],
                ],
                [
                    'key'       => 'field_wm_tab_media',
                    'label'     => 'Media',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_video_url',
                    'label'        => 'Module Video URL',
                    'name'         => 'module_video_url',
                    'type'         => 'url',
                    'instructions' => 'YouTube or Vimeo URL embedded in the pamphlet.',
                ],
                [
                    'key'          => 'field_wm_gallery',
                    'label'        => 'Photo Gallery',
                    'name'         => 'module_gallery',
                    'type'         => 'gallery',
                    'instructions' => 'Additional images shown in the pamphlet.',
                    'return_format'=> 'array',
                    'preview_size' => 'medium',
                    'insert'       => 'append',
                    'min'          => 0,
                    'max'          => 20,
                ],

                // ── EU & Structure ───────────────────────────────────────────

                [
                    'key'       => 'field_wm_tab_eu_structure',
                    'label'     => 'EU Text & Structure',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_eu_funding_text',
                    'label'        => 'EU Funding Acknowledgement',
                    'name'         => 'module_eu_funding_text',
                    'type'         => 'textarea',
                    'rows'         => 3,
                    'default_value'=> 'Funded by the European Union. Views and opinions expressed are however those of the author(s) only and do not necessarily reflect those of the European Union or the European Education and Culture Executive Agency (EACEA). Neither the European Union nor the granting authority can be held responsible for them.',
                    'instructions' => 'Shown on the module cover slide. Leave blank to use the default.',
                ],
                [
                    'key'          => 'field_wm_table_of_contents',
                    'label'        => 'Table of Contents',
                    'name'         => 'module_table_of_contents',
                    'type'         => 'textarea',
                    'rows'         => 6,
                    'instructions' => 'List the sections/topics covered in this module. One item per line.',
                ],

                // ── Introduction & Conclusion ────────────────────────────────

                [
                    'key'       => 'field_wm_tab_intro_conclusion',
                    'label'     => 'Introduction & Conclusion',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_introduction_items',
                    'label'        => 'Introduction Items',
                    'name'         => 'module_introduction_items',
                    'type'         => 'repeater',
                    'instructions' => 'Each item becomes a clickable introduction card that opens inline detail, matching the Learning Outcomes pattern.',
                    'min'          => 0,
                    'max'          => 10,
                    'layout'       => 'block',
                    'button_label' => 'Add Introduction Item',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_intro_item_title',
                            'label' => 'Introduction Title',
                            'name'  => 'intro_title',
                            'type'  => 'text',
                        ],
                        [
                            'key'          => 'field_wm_intro_item_detail',
                            'label'        => 'Introduction Detail',
                            'name'         => 'intro_detail',
                            'type'         => 'wysiwyg',
                            'tabs'         => 'all',
                            'toolbar'      => 'basic',
                            'media_upload' => 0,
                        ],
                        [
                            'key'           => 'field_wm_intro_item_icon',
                            'label'         => 'Icon (optional)',
                            'name'          => 'intro_icon',
                            'type'          => 'image',
                            'return_format' => 'array',
                            'preview_size'  => 'thumbnail',
                        ],
                    ],
                ],
                [
                    'key'          => 'field_wm_introduction',
                    'label'        => 'Introduction Fallback Content',
                    'name'         => 'module_introduction',
                    'type'         => 'wysiwyg',
                    'tabs'         => 'all',
                    'toolbar'      => 'full',
                    'media_upload' => 1,
                    'instructions' => 'Fallback content shown only when no Introduction Items are added.',
                ],
                [
                    'key'          => 'field_wm_conclusion',
                    'label'        => 'Conclusion',
                    'name'         => 'module_conclusion',
                    'type'         => 'wysiwyg',
                    'tabs'         => 'all',
                    'toolbar'      => 'full',
                    'media_upload' => 0,
                    'instructions' => 'Closing text that reinforces learning and encourages further application.',
                ],
                [
                    'key'          => 'field_wm_reflection_questions',
                    'label'        => 'Reflection Questions',
                    'name'         => 'module_reflection_questions',
                    'type'         => 'repeater',
                    'instructions' => 'Questions for participants to reflect on the exercise and its relevance.',
                    'min'          => 0,
                    'max'          => 10,
                    'layout'       => 'block',
                    'button_label' => 'Add Reflection Question',
                    'sub_fields'   => [
                        [
                            'key'   => 'field_wm_reflection_question',
                            'label' => 'Question',
                            'name'  => 'reflection_question',
                            'type'  => 'textarea',
                            'rows'  => 2,
                        ],
                    ],
                ],

            ], // end fields
        ] ); // end acf_add_local_field_group
    }
}
