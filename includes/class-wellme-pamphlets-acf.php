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

    public function register_field_groups() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

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

                // ── Exercise Steps ─────────────────────────────────────────────

                [
                    'key'       => 'field_wm_tab_steps',
                    'label'     => 'Exercise Steps',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_exercise_steps',
                    'label'        => 'Exercise Steps',
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
                    'label'     => 'Chapters',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_wm_chapters',
                    'label'        => 'Chapters',
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

            ], // end fields
        ] ); // end acf_add_local_field_group
    }
}
