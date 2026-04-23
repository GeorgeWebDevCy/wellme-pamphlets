<?php
/**
 * Admin importer for WELLME module packages.
 *
 * @since      1.0.2
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/admin
 */

class Wellme_Pamphlets_Importer {

    const MENU_SLUG = 'wellme-pamphlets-import';
    const ACTION    = 'wellme_pamphlets_import_package';

    /**
     * @var string
     */
    private $plugin_name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var array<string,int>
     */
    private $attachment_cache = [];

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the import screen under the module CPT menu.
     */
    public function register_menu() {
        add_submenu_page(
            'edit.php?post_type=wellme_module',
            __( 'Import Modules', 'wellme-pamphlets' ),
            __( 'Import Modules', 'wellme-pamphlets' ),
            'manage_options',
            self::MENU_SLUG,
            [ $this, 'render_page' ]
        );
    }

    /**
     * Render the import page.
     */
    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $report          = $this->pull_report();
        $import_page_url = $this->get_page_url();

        include WELLME_PAMPHLETS_PLUGIN_DIR . 'admin/partials/wellme-pamphlets-import-page.php';
    }

    /**
     * Handle package upload and import.
     */
    public function handle_import() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to import WELLME modules.', 'wellme-pamphlets' ) );
        }

        check_admin_referer( self::ACTION );

        $cleanup_paths = [];

        try {
            if ( ! function_exists( 'update_field' ) ) {
                throw new RuntimeException( __( 'Advanced Custom Fields Pro must be active before importing modules.', 'wellme-pamphlets' ) );
            }

            if ( empty( $_FILES['wellme_import_package']['name'] ) ) {
                throw new RuntimeException( __( 'Upload a WELLME import package ZIP first.', 'wellme-pamphlets' ) );
            }

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $uploaded = wp_handle_upload(
                $_FILES['wellme_import_package'],
                [
                    'test_form' => false,
                    'mimes'     => [
                        'zip' => 'application/zip',
                    ],
                ]
            );

            if ( isset( $uploaded['error'] ) ) {
                throw new RuntimeException( wp_strip_all_tags( $uploaded['error'] ) );
            }

            $cleanup_paths[] = $uploaded['file'];

            $uploads     = wp_upload_dir();
            $extract_dir = trailingslashit( $uploads['basedir'] ) . 'wellme-pamphlets-imports/' . wp_generate_uuid4();

            if ( ! wp_mkdir_p( $extract_dir ) ) {
                throw new RuntimeException( __( 'Could not create a temporary extraction directory.', 'wellme-pamphlets' ) );
            }

            $cleanup_paths[] = $extract_dir;

            $unzipped = unzip_file( $uploaded['file'], $extract_dir );

            if ( is_wp_error( $unzipped ) ) {
                throw new RuntimeException( $unzipped->get_error_message() );
            }

            $manifest_path = $this->find_manifest_path( $extract_dir );
            $payload       = $this->read_manifest( $manifest_path );
            $report        = $this->import_payload( $payload, dirname( $manifest_path ) );

            $this->store_report(
                [
                    'type'    => 'success',
                    'message' => __( 'WELLME modules imported successfully.', 'wellme-pamphlets' ),
                    'report'  => $report,
                ]
            );
        } catch ( RuntimeException $e ) {
            $this->store_report(
                [
                    'type'    => 'error',
                    'message' => $e->getMessage(),
                    'report'  => [
                        'created'  => 0,
                        'updated'  => 0,
                        'modules'  => [],
                        'warnings' => [],
                    ],
                ]
            );
        }

        foreach ( $cleanup_paths as $cleanup_path ) {
            $this->cleanup_path( $cleanup_path );
        }

        wp_safe_redirect( $this->get_page_url() );
        exit;
    }

    /**
     * Import all modules from a decoded manifest payload.
     *
     * @param array  $payload      Package manifest.
     * @param string $package_root Absolute path to the extracted package root.
     *
     * @return array
     */
    private function import_payload( array $payload, $package_root ) {
        $report = [
            'created'      => 0,
            'updated'      => 0,
            'modules'      => [],
            'warnings'     => [],
            'source'       => $payload['source'] ?? [],
            'generated_at' => $payload['generated_at'] ?? '',
        ];

        $this->attachment_cache = [];

        foreach ( $payload['modules'] as $module_payload ) {
            $module_report        = $this->import_module( $module_payload, $package_root );
            $report['modules'][]  = $module_report;
            $report['warnings']   = array_merge( $report['warnings'], $module_report['warnings'] );
            $report[ $module_report['status'] ]++;
        }

        return $report;
    }

    /**
     * Import or update a single module.
     *
     * @param array  $module       Module payload.
     * @param string $package_root Package root path.
     *
     * @return array
     */
    private function import_module( array $module, $package_root ) {
        $module_number = isset( $module['module_number'] ) ? (int) $module['module_number'] : 0;
        $post_title    = sanitize_text_field( $module['post_title'] ?? '' );

        if ( $module_number < 1 || $module_number > 6 ) {
            throw new RuntimeException( __( 'Each imported module must include a module number between 1 and 6.', 'wellme-pamphlets' ) );
        }

        if ( '' === $post_title ) {
            throw new RuntimeException(
                sprintf(
                    /* translators: %d: module number */
                    __( 'Module %d is missing a title in the import package.', 'wellme-pamphlets' ),
                    $module_number
                )
            );
        }

        $post_id = $this->find_existing_module_id( $module_number, $module['post_name'] ?? '' );
        $status  = $post_id ? 'updated' : 'created';

        $postarr = [
            'post_type'   => 'wellme_module',
            'post_title'  => $post_title,
            'post_name'   => sanitize_title( $module['post_name'] ?? $post_title ),
            'post_status' => $post_id ? get_post_status( $post_id ) : 'publish',
        ];

        if ( $post_id ) {
            $postarr['ID'] = $post_id;
            $post_id       = wp_update_post( wp_slash( $postarr ), true );
        } else {
            $post_id = wp_insert_post( wp_slash( $postarr ), true );
        }

        if ( is_wp_error( $post_id ) ) {
            throw new RuntimeException( $post_id->get_error_message() );
        }

        $warnings                = [];
        $preserve_existing_media = ! empty( $module['preserve_existing_media'] );

        $cover_id = '';
        if ( array_key_exists( 'module_cover_image', $module ) ) {
            $cover_id = $this->import_attachment_reference( $module['module_cover_image'], $package_root, $post_id );
        } elseif ( $preserve_existing_media ) {
            $cover_id = get_field( 'module_cover_image', $post_id, false ) ?: '';
        }

        $icon_id = '';
        if ( array_key_exists( 'module_icon', $module ) ) {
            $icon_id = $this->import_attachment_reference( $module['module_icon'], $package_root, $post_id );
        } elseif ( $preserve_existing_media ) {
            $icon_id = get_field( 'module_icon', $post_id, false ) ?: '';
        }

        $gallery = [];
        if ( array_key_exists( 'module_gallery', $module ) ) {
            $gallery = $this->import_gallery( $module['module_gallery'], $package_root, $post_id );
        } elseif ( $preserve_existing_media ) {
            $gallery = get_field( 'module_gallery', $post_id, false ) ?: [];
        }

        $introduction_items   = $this->build_introduction_items( $module['module_introduction_items'] ?? [], $package_root, $post_id );
        $outcomes             = $this->build_outcomes( $module['module_learning_outcomes'] ?? [], $package_root, $post_id );
        $steps                = $this->build_steps( $module['module_exercise_steps'] ?? [], $package_root, $post_id );
        $chapters             = $this->build_chapters( $module['module_chapters'] ?? [], $package_root, $post_id );
        $activity_aims        = $this->build_activity_aims( $module['module_activity_aims'] ?? [] );
        $assessment_questions = $this->build_assessment_questions( $module['module_assessment_questions'] ?? [] );
        $reflection_questions = $this->build_reflection_questions( $module['module_reflection_questions'] ?? [] );
        $color                = sanitize_hex_color( $module['module_color'] ?? '' ) ?: '#005b96';
        $subtitle             = sanitize_text_field( $module['module_subtitle'] ?? '' );
        $motto                = sanitize_text_field( $module['module_motto'] ?? '' );
        $video_url            = esc_url_raw( $module['module_video_url'] ?? '' );
        $eu_text              = sanitize_textarea_field( $module['module_eu_funding_text'] ?? '' );
        $toc                  = sanitize_textarea_field( $module['module_table_of_contents'] ?? '' );
        $introduction         = wp_kses_post( $module['module_introduction'] ?? '' );
        $conclusion           = wp_kses_post( $module['module_conclusion'] ?? '' );

        if ( ! $cover_id && ! $preserve_existing_media ) {
            $warnings[] = sprintf(
                /* translators: %d: module number */
                __( 'Module %d imported without a cover image.', 'wellme-pamphlets' ),
                $module_number
            );
        }

        update_field( 'module_number', $module_number, $post_id );
        update_field( 'module_subtitle', $subtitle, $post_id );
        update_field( 'module_description', wp_strip_all_tags( $module['module_description'] ?? '' ), $post_id );
        update_field( 'module_color', $color, $post_id );
        update_field( 'module_icon', $icon_id ?: '', $post_id );
        update_field( 'module_cover_image', $cover_id ?: '', $post_id );
        update_field( 'module_motto', $motto, $post_id );
        update_field( 'module_video_url', $video_url, $post_id );
        update_field( 'module_gallery', $gallery, $post_id );
        update_field( 'module_introduction_items', $introduction_items, $post_id );
        update_field( 'module_learning_outcomes', $outcomes, $post_id );
        update_field( 'module_exercise_steps', $steps, $post_id );
        update_field( 'module_chapters', $chapters, $post_id );
        update_field( 'module_activity_aims', $activity_aims, $post_id );
        update_field( 'module_assessment_questions', $assessment_questions, $post_id );
        update_field( 'module_eu_funding_text', $eu_text, $post_id );
        update_field( 'module_table_of_contents', $toc, $post_id );
        update_field( 'module_introduction', $introduction, $post_id );
        update_field( 'module_conclusion', $conclusion, $post_id );
        update_field( 'module_reflection_questions', $reflection_questions, $post_id );

        if ( $cover_id ) {
            set_post_thumbnail( $post_id, $cover_id );
        }

        update_post_meta( $post_id, '_wellme_import_source', sanitize_text_field( $module['source_document'] ?? '' ) );

        return [
            'module_number' => $module_number,
            'post_id'       => $post_id,
            'title'         => $post_title,
            'status'        => $status,
            'warnings'      => $warnings,
        ];
    }

    /**
     * Build repeater rows for introduction items.
     *
     * @param array  $items        Introduction item payloads.
     * @param string $package_root Package root path.
     * @param int    $post_id      Parent post ID.
     *
     * @return array
     */
    private function build_introduction_items( array $items, $package_root, $post_id ) {
        $rows = [];

        foreach ( $items as $item ) {
            $title = sanitize_text_field( $item['intro_title'] ?? '' );

            if ( '' === $title ) {
                continue;
            }

            $rows[] = [
                'intro_title'  => $title,
                'intro_detail' => wp_kses_post( $item['intro_detail'] ?? '' ),
                'intro_icon'   => $this->import_attachment_reference( $item['intro_icon'] ?? '', $package_root, $post_id ) ?: '',
            ];
        }

        return $rows;
    }

    /**
     * Build repeater rows for learning outcomes.
     *
     * @param array  $outcomes     Outcome payloads.
     * @param string $package_root Package root path.
     * @param int    $post_id      Parent post ID.
     *
     * @return array
     */
    private function build_outcomes( array $outcomes, $package_root, $post_id ) {
        $rows = [];

        foreach ( $outcomes as $outcome ) {
            $title = sanitize_text_field( $outcome['outcome_title'] ?? '' );

            if ( '' === $title ) {
                continue;
            }

            $rows[] = [
                'outcome_title'  => $title,
                'outcome_detail' => wp_kses_post( $outcome['outcome_detail'] ?? '' ),
                'outcome_icon'   => $this->import_attachment_reference( $outcome['outcome_icon'] ?? '', $package_root, $post_id ) ?: '',
            ];
        }

        return $rows;
    }

    /**
     * Build repeater rows for exercise steps.
     *
     * @param array  $steps        Step payloads.
     * @param string $package_root Package root path.
     * @param int    $post_id      Parent post ID.
     *
     * @return array
     */
    private function build_steps( array $steps, $package_root, $post_id ) {
        $rows = [];

        foreach ( $steps as $step ) {
            $title = sanitize_text_field( $step['step_title'] ?? '' );

            if ( '' === $title ) {
                continue;
            }

            $rows[] = [
                'step_title'      => $title,
                'step_content'    => wp_kses_post( $step['step_content'] ?? '' ),
                'step_image'      => $this->import_attachment_reference( $step['step_image'] ?? '', $package_root, $post_id ) ?: '',
                'step_hotspot_x'  => $this->clamp_hotspot( $step['step_hotspot_x'] ?? 50 ),
                'step_hotspot_y'  => $this->clamp_hotspot( $step['step_hotspot_y'] ?? 50 ),
            ];
        }

        return $rows;
    }

    /**
     * Build repeater rows for chapters.
     *
     * @param array  $chapters     Chapter payloads.
     * @param string $package_root Package root path.
     * @param int    $post_id      Parent post ID.
     *
     * @return array
     */
    private function build_chapters( array $chapters, $package_root, $post_id ) {
        $rows = [];

        foreach ( $chapters as $chapter ) {
            $title = sanitize_text_field( $chapter['chapter_title'] ?? '' );

            if ( '' === $title ) {
                continue;
            }

            $rows[] = [
                'chapter_title'   => $title,
                'chapter_content' => wp_kses_post( $chapter['chapter_content'] ?? '' ),
                'chapter_image'   => $this->import_attachment_reference( $chapter['chapter_image'] ?? '', $package_root, $post_id ) ?: '',
            ];
        }

        return $rows;
    }

    /**
     * Build the module activity aims group.
     *
     * @param array $aims Activity aims payload.
     *
     * @return array
     */
    private function build_activity_aims( array $aims ) {
        return [
            'activity_aim'           => wp_kses_post( $aims['activity_aim'] ?? '' ),
            'activity_youth_worker'  => wp_kses_post( $aims['activity_youth_worker'] ?? '' ),
            'activity_wellme_goals'  => wp_kses_post( $aims['activity_wellme_goals'] ?? '' ),
        ];
    }

    /**
     * Build repeater rows for assessment questions.
     *
     * @param array $questions Assessment payloads.
     *
     * @return array
     */
    private function build_assessment_questions( array $questions ) {
        $rows = [];

        foreach ( $questions as $question ) {
            $prompt      = sanitize_text_field( $question['prompt'] ?? '' );
            $correct_key = strtoupper( sanitize_text_field( $question['correct_option'] ?? '' ) );
            $options     = [];

            if ( '' === $prompt ) {
                continue;
            }

            foreach ( (array) ( $question['options'] ?? [] ) as $key => $text ) {
                $option_key  = strtoupper( sanitize_text_field( is_string( $key ) ? $key : '' ) );
                $option_text = sanitize_text_field( $text );

                if ( '' === $option_key || '' === $option_text ) {
                    continue;
                }

                $options[] = [
                    'option_key'  => $option_key,
                    'option_text' => $option_text,
                ];
            }

            if ( count( $options ) < 2 || '' === $correct_key ) {
                continue;
            }

            $rows[] = [
                'assessment_question_prompt'         => $prompt,
                'assessment_question_options'        => $options,
                'assessment_question_correct_option' => $correct_key,
                'assessment_question_explanation'    => sanitize_text_field( $question['explanation'] ?? '' ),
            ];
        }

        return $rows;
    }

    /**
     * Build repeater rows for reflection questions.
     *
     * @param array $questions Reflection question payloads.
     *
     * @return array
     */
    private function build_reflection_questions( array $questions ) {
        $rows = [];

        foreach ( $questions as $question ) {
            if ( is_array( $question ) ) {
                $text = sanitize_textarea_field( $question['reflection_question'] ?? '' );
            } else {
                $text = sanitize_textarea_field( $question );
            }

            if ( '' === $text ) {
                continue;
            }

            $rows[] = [
                'reflection_question' => $text,
            ];
        }

        return $rows;
    }

    /**
     * Import a gallery list into attachment IDs.
     *
     * @param array  $gallery      Relative file paths.
     * @param string $package_root Package root path.
     * @param int    $post_id      Parent post ID.
     *
     * @return array
     */
    private function import_gallery( array $gallery, $package_root, $post_id ) {
        $ids = [];

        foreach ( $gallery as $relative_path ) {
            $attachment_id = $this->import_attachment_reference( $relative_path, $package_root, $post_id );

            if ( $attachment_id ) {
                $ids[] = $attachment_id;
            }
        }

        return array_values( array_unique( $ids ) );
    }

    /**
     * Import an attachment from a package-relative path.
     *
     * @param string $relative_path Package-relative file path.
     * @param string $package_root  Package root path.
     * @param int    $post_id       Parent post ID.
     *
     * @return int
     */
    private function import_attachment_reference( $relative_path, $package_root, $post_id ) {
        $relative_path = trim( (string) $relative_path );

        if ( '' === $relative_path ) {
            return 0;
        }

        $source_path = $this->resolve_package_path( $package_root, $relative_path );
        $cache_key   = wp_normalize_path( $source_path );

        if ( isset( $this->attachment_cache[ $cache_key ] ) ) {
            return $this->attachment_cache[ $cache_key ];
        }

        $uploads  = wp_upload_dir();
        $filename = wp_unique_filename( $uploads['path'], basename( $source_path ) );
        $target   = trailingslashit( $uploads['path'] ) . $filename;

        if ( ! wp_mkdir_p( $uploads['path'] ) ) {
            throw new RuntimeException( __( 'Could not prepare the WordPress uploads directory.', 'wellme-pamphlets' ) );
        }

        if ( ! copy( $source_path, $target ) ) {
            throw new RuntimeException(
                sprintf(
                    /* translators: %s: file name */
                    __( 'Could not copy imported asset %s into the uploads directory.', 'wellme-pamphlets' ),
                    basename( $source_path )
                )
            );
        }

        $filetype = wp_check_filetype( $filename, null );
        $mime     = ! empty( $filetype['type'] ) ? $filetype['type'] : 'application/octet-stream';

        $attachment_id = wp_insert_attachment(
            [
                'guid'           => trailingslashit( $uploads['url'] ) . $filename,
                'post_mime_type' => $mime,
                'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ],
            $target,
            $post_id,
            true
        );

        if ( is_wp_error( $attachment_id ) ) {
            throw new RuntimeException( $attachment_id->get_error_message() );
        }

        $metadata = wp_generate_attachment_metadata( $attachment_id, $target );

        if ( ! is_wp_error( $metadata ) ) {
            wp_update_attachment_metadata( $attachment_id, $metadata );
        }

        $this->attachment_cache[ $cache_key ] = (int) $attachment_id;

        return (int) $attachment_id;
    }

    /**
     * Find an existing module by module number or slug.
     *
     * @param int    $module_number Module number.
     * @param string $post_name     Slug candidate.
     *
     * @return int
     */
    private function find_existing_module_id( $module_number, $post_name ) {
        $existing = get_posts(
            [
                'post_type'              => 'wellme_module',
                'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
                'numberposts'            => 1,
                'fields'                 => 'ids',
                'meta_key'               => 'module_number',
                'meta_value'             => (string) $module_number,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'suppress_filters'       => false,
            ]
        );

        if ( ! empty( $existing ) ) {
            return (int) $existing[0];
        }

        $post_name = sanitize_title( $post_name );

        if ( '' === $post_name ) {
            return 0;
        }

        $existing = get_posts(
            [
                'post_type'              => 'wellme_module',
                'post_status'            => [ 'publish', 'draft', 'pending', 'future', 'private' ],
                'name'                   => $post_name,
                'numberposts'            => 1,
                'fields'                 => 'ids',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'suppress_filters'       => false,
            ]
        );

        return empty( $existing ) ? 0 : (int) $existing[0];
    }

    /**
     * Load and validate the manifest.
     *
     * @param string $manifest_path Absolute path to manifest.json.
     *
     * @return array
     */
    private function read_manifest( $manifest_path ) {
        $raw = file_get_contents( $manifest_path );

        if ( false === $raw ) {
            throw new RuntimeException( __( 'Could not read the manifest file from the uploaded package.', 'wellme-pamphlets' ) );
        }

        $payload = json_decode( $raw, true );

        if ( JSON_ERROR_NONE !== json_last_error() ) {
            throw new RuntimeException( __( 'The uploaded package manifest is not valid JSON.', 'wellme-pamphlets' ) );
        }

        $schema_version = isset( $payload['schema_version'] ) ? (int) $payload['schema_version'] : 0;

        if ( $schema_version < 1 || $schema_version > 2 ) {
            throw new RuntimeException( __( 'This import package uses an unsupported schema version.', 'wellme-pamphlets' ) );
        }

        if ( empty( $payload['modules'] ) || ! is_array( $payload['modules'] ) ) {
            throw new RuntimeException( __( 'The uploaded package does not contain any modules.', 'wellme-pamphlets' ) );
        }

        return $payload;
    }

    /**
     * Find the package manifest after extraction.
     *
     * @param string $extract_dir Extraction directory.
     *
     * @return string
     */
    private function find_manifest_path( $extract_dir ) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $extract_dir,
                FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ( $iterator as $file_info ) {
            if ( 'manifest.json' === $file_info->getFilename() ) {
                return $file_info->getPathname();
            }
        }

        throw new RuntimeException( __( 'The uploaded ZIP does not contain a manifest.json file.', 'wellme-pamphlets' ) );
    }

    /**
     * Resolve a package-relative path safely within the extracted package.
     *
     * @param string $package_root  Package root path.
     * @param string $relative_path Relative file path.
     *
     * @return string
     */
    private function resolve_package_path( $package_root, $relative_path ) {
        $package_root  = realpath( $package_root );
        $relative_path = ltrim( str_replace( [ '\\', '/' ], DIRECTORY_SEPARATOR, $relative_path ), DIRECTORY_SEPARATOR );
        $candidate     = realpath( $package_root . DIRECTORY_SEPARATOR . $relative_path );

        if ( ! $package_root || ! $candidate ) {
            throw new RuntimeException(
                sprintf(
                    /* translators: %s: relative path */
                    __( 'Missing asset referenced by the import package: %s', 'wellme-pamphlets' ),
                    $relative_path
                )
            );
        }

        $normalized_root      = trailingslashit( wp_normalize_path( $package_root ) );
        $normalized_candidate = wp_normalize_path( $candidate );

        if ( 0 !== strpos( $normalized_candidate, $normalized_root ) ) {
            throw new RuntimeException( __( 'The import package contains an invalid asset path.', 'wellme-pamphlets' ) );
        }

        return $candidate;
    }

    /**
     * Clamp hotspot values into the supported 0-100 range.
     *
     * @param mixed $value Hotspot value.
     *
     * @return float
     */
    private function clamp_hotspot( $value ) {
        $value = (float) $value;

        if ( $value < 0 ) {
            return 0.0;
        }

        if ( $value > 100 ) {
            return 100.0;
        }

        return $value;
    }

    /**
     * Persist a short-lived report for the current admin user.
     *
     * @param array $report Report payload.
     */
    private function store_report( array $report ) {
        set_transient( $this->get_report_key(), $report, 10 * MINUTE_IN_SECONDS );
    }

    /**
     * Retrieve and clear the current user's report.
     *
     * @return array|null
     */
    private function pull_report() {
        $key    = $this->get_report_key();
        $report = get_transient( $key );

        if ( false !== $report ) {
            delete_transient( $key );
            return $report;
        }

        return null;
    }

    /**
     * Build the transient key for the current user.
     *
     * @return string
     */
    private function get_report_key() {
        return 'wellme_pamphlets_import_report_' . get_current_user_id();
    }

    /**
     * Get the importer admin URL.
     *
     * @return string
     */
    private function get_page_url() {
        return admin_url( 'edit.php?post_type=wellme_module&page=' . self::MENU_SLUG );
    }

    /**
     * Remove a temporary file or directory after import.
     *
     * @param string $path Absolute path.
     */
    private function cleanup_path( $path ) {
        if ( ! $path || ! file_exists( $path ) ) {
            return;
        }

        if ( is_dir( $path ) ) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ( $iterator as $item ) {
                if ( $item->isDir() ) {
                    rmdir( $item->getPathname() );
                } else {
                    unlink( $item->getPathname() );
                }
            }

            rmdir( $path );
            return;
        }

        unlink( $path );
    }
}
