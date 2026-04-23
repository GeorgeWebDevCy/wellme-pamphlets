<?php
/**
 * Individual module pamphlet template.
 *
 * Variables available:
 *   $module  WP_Post
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$number       = (int) get_field( 'module_number',       $module->ID );
$subtitle     = get_field( 'module_subtitle',     $module->ID );
$description  = get_field( 'module_description',  $module->ID );
$color        = get_field( 'module_color',        $module->ID ) ?: '#005b96';
$cover        = get_field( 'module_cover_image',  $module->ID );
$cover_url    = $cover['url'] ?? '';
$motto        = get_field( 'module_motto',        $module->ID );
$video_url    = get_field( 'module_video_url',    $module->ID );
$outcomes     = get_field( 'module_learning_outcomes', $module->ID ) ?: [];
$steps        = get_field( 'module_exercise_steps',    $module->ID ) ?: [];
$chapters     = get_field( 'module_chapters',          $module->ID ) ?: [];
$activity_aims = get_field( 'module_activity_aims',    $module->ID ) ?: [];
$gallery      = get_field( 'module_gallery',           $module->ID ) ?: [];
$eu_text      = get_field( 'module_eu_funding_text',   $module->ID );
$toc          = get_field( 'module_table_of_contents', $module->ID );
$introduction = get_field( 'module_introduction',      $module->ID );
$conclusion   = get_field( 'module_conclusion',        $module->ID );
$reflection   = get_field( 'module_reflection_questions', $module->ID ) ?: [];
$assessment_questions = Wellme_Pamphlets_Assessment::get_module_questions( $module->ID );
$display_chapters     = $chapters;
$activity_aim_tabs     = [
    [
        'key'     => 'aim',
        'title'   => __( 'Aim', 'wellme-pamphlets' ),
        'content' => $activity_aims['activity_aim'] ?? '',
    ],
    [
        'key'     => 'youth-worker',
        'title'   => __( 'Youth Worker', 'wellme-pamphlets' ),
        'content' => $activity_aims['activity_youth_worker'] ?? '',
    ],
    [
        'key'     => 'wellme-goals',
        'title'   => $number
            ? sprintf( __( 'Module %d WellMe Goals', 'wellme-pamphlets' ), $number )
            : __( 'Module WellMe Goals', 'wellme-pamphlets' ),
        'content' => $activity_aims['activity_wellme_goals'] ?? '',
    ],
];
$activity_aim_tabs     = array_values(
    array_filter(
        $activity_aim_tabs,
        static function ( $tab ) {
            return '' !== trim( wp_strip_all_tags( (string) $tab['content'] ) );
        }
    )
);

if ( ! empty( $assessment_questions ) ) {
    $display_chapters = array_values(
        array_filter(
            $chapters,
            static function ( $chapter ) {
                $title = strtolower( wp_strip_all_tags( $chapter['chapter_title'] ?? '' ) );

                return false === strpos( $title, 'assessment' ) && false === strpos( $title, 'answer' );
            }
        )
    );
}
?>
<div class="wellme-pamphlet" style="--module-color: <?php echo esc_attr( $color ); ?>;" data-module-id="<?php echo esc_attr( $module->ID ); ?>">

    <?php /* ── Cover slide ─────────────────────────────────────────── */ ?>
    <section class="wellme-pamphlet-section wellme-section-cover">
        <?php if ( $cover_url ) : ?>
        <div class="wellme-cover-image" style="background-image: url('<?php echo esc_url( $cover_url ); ?>');"></div>
        <?php endif; ?>

        <div class="wellme-cover-content wellme-scroll-reveal">
            <span class="wellme-cover-number"><?php echo esc_html( sprintf( __( 'Module %d', 'wellme-pamphlets' ), $number ) ); ?></span>
            <h1 class="wellme-cover-title"><?php echo esc_html( get_the_title( $module ) ); ?></h1>
            <?php if ( $subtitle ) : ?>
            <p class="wellme-cover-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
            <?php if ( $description ) : ?>
            <p class="wellme-cover-description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
            <?php if ( $eu_text ) : ?>
            <p class="wellme-cover-eu-text"><?php echo esc_html( $eu_text ); ?></p>
            <?php endif; ?>
            <?php if ( $toc ) : ?>
            <div class="wellme-cover-toc">
                <h4 class="wellme-cover-toc-title"><?php esc_html_e( 'Contents', 'wellme-pamphlets' ); ?></h4>
                <ul class="wellme-cover-toc-list">
                    <?php foreach ( explode( "\n", $toc ) as $toc_item ) :
                        $toc_item = trim( $toc_item );
                        if ( $toc_item ) : ?>
                    <li><?php echo esc_html( $toc_item ); ?></li>
                    <?php endif;
                    endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php /* ── Introduction (Theoretical Background) ──────────────── */ ?>
    <?php if ( $introduction ) : ?>
    <section class="wellme-pamphlet-section wellme-section-introduction">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Introduction', 'wellme-pamphlets' ); ?></h2>
            <div class="wellme-introduction-content">
                <?php echo wp_kses_post( $introduction ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ── Chapter navigation ─────────────────────────────────── */ ?>
    <?php if ( ! empty( $activity_aim_tabs ) || ! empty( $display_chapters ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-chapters">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2>
                <?php
                echo esc_html(
                    $number
                        ? sprintf( __( 'Module %d Activity', 'wellme-pamphlets' ), $number )
                        : __( 'Module Activity', 'wellme-pamphlets' )
                );
                ?>
            </h2>
            <nav class="wellme-chapter-nav" aria-label="<?php esc_attr_e( 'Module activity tabs', 'wellme-pamphlets' ); ?>">
                <?php
                $activity_tab_index = 0;
                foreach ( $activity_aim_tabs as $tab ) :
                    $panel_index  = $activity_tab_index++;
                    $aim_panel_id = 'wellme-chapter-panel-' . $module->ID . '-aim-' . $tab['key'];
                ?>
                <button type="button"
                        class="wellme-chapter-btn wellme-chapter-btn--aim"
                        data-chapter="<?php echo esc_attr( $panel_index ); ?>"
                        aria-controls="<?php echo esc_attr( $aim_panel_id ); ?>">
                    <?php echo esc_html( $tab['title'] ); ?>
                </button>
                <?php endforeach; ?>
                <?php foreach ( $display_chapters as $i => $chapter ) :
                    $panel_index = $activity_tab_index++;
                ?>
                <button type="button"
                        class="wellme-chapter-btn"
                        data-chapter="<?php echo esc_attr( $panel_index ); ?>"
                        aria-controls="wellme-chapter-panel-<?php echo esc_attr( $module->ID . '-' . $i ); ?>">
                    <?php echo esc_html( $chapter['chapter_title'] ); ?>
                </button>
                <?php endforeach; ?>
            </nav>

            <?php
            $activity_tab_index = 0;
            foreach ( $activity_aim_tabs as $tab ) :
                $panel_index  = $activity_tab_index++;
                $aim_panel_id = 'wellme-chapter-panel-' . $module->ID . '-aim-' . $tab['key'];
            ?>
            <div class="wellme-chapter-panel wellme-chapter-panel--aim"
                 id="<?php echo esc_attr( $aim_panel_id ); ?>"
                 data-chapter="<?php echo esc_attr( $panel_index ); ?>"
                 hidden>
                <div class="wellme-activity-aim-content">
                    <?php echo wp_kses_post( $tab['content'] ); ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php foreach ( $display_chapters as $i => $chapter ) :
                $ch_img = $chapter['chapter_image']['url'] ?? '';
                $panel_index = $activity_tab_index++;
            ?>
            <div class="wellme-chapter-panel"
                 id="wellme-chapter-panel-<?php echo esc_attr( $module->ID . '-' . $i ); ?>"
                 data-chapter="<?php echo esc_attr( $panel_index ); ?>"
                 hidden>
                <?php if ( $ch_img ) : ?>
                <img src="<?php echo esc_url( $ch_img ); ?>" alt="" class="wellme-chapter-image">
                <?php endif; ?>
                <div class="wellme-chapter-content">
                    <?php echo wp_kses_post( $chapter['chapter_content'] ); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ── Learning outcomes (Partou pattern) ─────────────────── */ ?>
    <?php if ( ! empty( $outcomes ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-outcomes">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Learning Outcomes', 'wellme-pamphlets' ); ?></h2>
            <p class="wellme-outcomes-intro"><?php esc_html_e( 'Click on each outcome to learn more.', 'wellme-pamphlets' ); ?></p>
            <div class="wellme-outcomes-links">
                <?php foreach ( $outcomes as $i => $outcome ) :
                    $icon_url = $outcome['outcome_icon']['url'] ?? '';
                    $panel_id = 'wellme-outcome-panel-' . $module->ID . '-' . $i;
                    $detail   = strip_tags( $outcome['outcome_detail'] ?? '' );
                    $preview  = wp_trim_words( $detail, 15, '…' );
                ?>
                <a class="wellme-outcome-link"
                   href="#<?php echo esc_attr( $panel_id ); ?>"
                   data-target="<?php echo esc_attr( $panel_id ); ?>"
                   aria-expanded="false"
                   aria-controls="<?php echo esc_attr( $panel_id ); ?>">
                    <?php if ( $icon_url ) : ?>
                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="" aria-hidden="true" class="wellme-outcome-icon">
                    <?php endif; ?>
                    <span class="wellme-outcome-link-body">
                        <span class="wellme-outcome-link-title"><?php echo esc_html( $outcome['outcome_title'] ); ?></span>
                        <?php if ( $preview ) : ?>
                        <span class="wellme-outcome-link-desc"><?php echo esc_html( $preview ); ?></span>
                        <?php endif; ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>

            <?php /* Inline expandable panels (Partou pattern) */ ?>
            <?php foreach ( $outcomes as $i => $outcome ) :
                $panel_id = 'wellme-outcome-panel-' . $module->ID . '-' . $i;
            ?>
            <div class="wellme-outcome-detail-inline"
                 id="<?php echo esc_attr( $panel_id ); ?>"
                 role="region"
                 aria-label="<?php echo esc_attr( $outcome['outcome_title'] ); ?>"
                 hidden>
                <div class="wellme-outcome-detail-header">
                    <h3><?php echo esc_html( $outcome['outcome_title'] ); ?></h3>
                    <button class="wellme-outcome-detail-close" aria-label="<?php esc_attr_e( 'Close', 'wellme-pamphlets' ); ?>">&times;</button>
                </div>
                <div class="wellme-outcome-detail-body">
                    <?php echo wp_kses_post( $outcome['outcome_detail'] ); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ── Exercise steps with pulsing hotspots (Outremer pattern) ── */ ?>
    <?php if ( ! empty( $steps ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-steps">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Activity Steps', 'wellme-pamphlets' ); ?></h2>

            <p class="wellme-exercise-hint"><?php esc_html_e( 'Click the numbered dots on the image to explore each step.', 'wellme-pamphlets' ); ?></p>

            <?php /* Layout image with numbered pulsing dots */ ?>
            <div class="wellme-hotspot-map">
                <?php if ( $cover_url ) : ?>
                <img src="<?php echo esc_url( $cover_url ); ?>" alt="" class="wellme-hotspot-base-image">
                <?php endif; ?>

                <?php foreach ( $steps as $i => $step ) :
                    $x = (float) ( $step['step_hotspot_x'] ?? 50 );
                    $y = (float) ( $step['step_hotspot_y'] ?? 50 );
                    $panel_id = 'wellme-step-panel-' . $module->ID . '-' . $i;
                ?>
                <button class="wellme-hotspot-dot"
                        style="left: <?php echo esc_attr( $x ); ?>%; top: <?php echo esc_attr( $y ); ?>%;"
                        data-target="<?php echo esc_attr( $panel_id ); ?>"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr( $panel_id ); ?>"
                        aria-label="<?php echo esc_attr( sprintf( __( 'Step %d: %s', 'wellme-pamphlets' ), $i + 1, $step['step_title'] ) ); ?>">
                    <span class="wellme-hotspot-number"><?php echo esc_html( $i + 1 ); ?></span>
                    <span class="wellme-hotspot-pulse"></span>
                    <span class="wellme-hotspot-label"><?php echo esc_html( $step['step_title'] ); ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <?php /* Step content panels */ ?>
            <div class="wellme-step-panels">
                <?php foreach ( $steps as $i => $step ) :
                    $panel_id = 'wellme-step-panel-' . $module->ID . '-' . $i;
                    $img_url  = $step['step_image']['url'] ?? '';
                ?>
                <div class="wellme-step-panel"
                     id="<?php echo esc_attr( $panel_id ); ?>"
                     hidden>
                    <button class="wellme-step-panel-close" aria-label="<?php esc_attr_e( 'Close', 'wellme-pamphlets' ); ?>">&times;</button>
                    <div class="wellme-step-panel-header">
                        <span class="wellme-step-number"><?php echo esc_html( $i + 1 ); ?></span>
                        <h3><?php echo esc_html( $step['step_title'] ); ?></h3>
                    </div>
                    <?php if ( $img_url ) : ?>
                    <img src="<?php echo esc_url( $img_url ); ?>" alt="" class="wellme-step-image">
                    <?php endif; ?>
                    <div class="wellme-step-content">
                        <?php echo wp_kses_post( $step['step_content'] ); ?>
                    </div>
                    <?php if ( $i > 0 || $i < count( $steps ) - 1 ) : ?>
                    <div class="wellme-step-nav">
                        <?php if ( $i > 0 ) : $prev_id = 'wellme-step-panel-' . $module->ID . '-' . ( $i - 1 ); ?>
                        <button class="wellme-step-nav-btn" data-target="<?php echo esc_attr( $prev_id ); ?>" data-current="<?php echo esc_attr( $panel_id ); ?>">
                            &larr; <?php esc_html_e( 'Previous', 'wellme-pamphlets' ); ?>
                        </button>
                        <?php endif; ?>
                        <?php if ( $i < count( $steps ) - 1 ) : $next_id = 'wellme-step-panel-' . $module->ID . '-' . ( $i + 1 ); ?>
                        <button class="wellme-step-nav-btn" data-target="<?php echo esc_attr( $next_id ); ?>" data-current="<?php echo esc_attr( $panel_id ); ?>">
                            <?php esc_html_e( 'Next', 'wellme-pamphlets' ); ?> &rarr;
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ── Conclusion ──────────────────────────────────────── */ ?>
    <?php if ( $conclusion || ! empty( $reflection ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-conclusion">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Conclusion', 'wellme-pamphlets' ); ?></h2>

            <?php if ( $conclusion ) : ?>
            <div class="wellme-conclusion-content">
                <?php echo wp_kses_post( $conclusion ); ?>
            </div>
            <?php endif; ?>

            <?php if ( ! empty( $reflection ) ) : ?>
            <div class="wellme-reflection-questions">
                <h3><?php esc_html_e( 'Reflection Questions', 'wellme-pamphlets' ); ?></h3>
                <ol class="wellme-reflection-list">
                    <?php foreach ( $reflection as $rq ) : ?>
                    <li><?php echo esc_html( $rq['reflection_question'] ); ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ── Video ─────────────────────────────────────────────── */ ?>
    <?php if ( ! empty( $assessment_questions ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-assessment">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Assessment', 'wellme-pamphlets' ); ?></h2>
            <p class="wellme-assessment-intro">
                <?php esc_html_e( 'Choose one answer per question, then check your results.', 'wellme-pamphlets' ); ?>
            </p>

            <form class="wellme-assessment-form" novalidate>
                <?php foreach ( $assessment_questions as $i => $question ) :
                    $question_name = 'wellme-assessment-' . $module->ID . '-' . $i;
                    $feedback_id   = $question_name . '-feedback';
                ?>
                <fieldset class="wellme-assessment-question"
                          data-correct-option="<?php echo esc_attr( $question['correct_option'] ); ?>"
                          aria-describedby="<?php echo esc_attr( $feedback_id ); ?>">
                    <legend class="wellme-assessment-question-title">
                        <span class="wellme-assessment-question-number">
                            <?php echo esc_html( sprintf( __( 'Question %d', 'wellme-pamphlets' ), $i + 1 ) ); ?>
                        </span>
                        <span class="wellme-assessment-question-text"><?php echo esc_html( $question['prompt'] ); ?></span>
                    </legend>

                    <div class="wellme-assessment-options">
                        <?php foreach ( $question['options'] as $option_key => $option_text ) :
                            $option_id = $question_name . '-' . strtolower( $option_key );
                        ?>
                        <label class="wellme-assessment-option" for="<?php echo esc_attr( $option_id ); ?>">
                            <input id="<?php echo esc_attr( $option_id ); ?>"
                                   type="radio"
                                   name="<?php echo esc_attr( $question_name ); ?>"
                                   value="<?php echo esc_attr( $option_key ); ?>">
                            <span class="wellme-assessment-option-key"><?php echo esc_html( $option_key ); ?></span>
                            <span class="wellme-assessment-option-text"><?php echo esc_html( $option_text ); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="wellme-assessment-feedback" id="<?php echo esc_attr( $feedback_id ); ?>" hidden>
                        <p class="wellme-assessment-feedback-status"></p>
                        <?php if ( ! empty( $question['explanation'] ) ) : ?>
                        <p class="wellme-assessment-feedback-explanation"><?php echo esc_html( $question['explanation'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </fieldset>
                <?php endforeach; ?>

                <div class="wellme-assessment-actions">
                    <button type="submit" class="wellme-assessment-submit">
                        <?php esc_html_e( 'Check Answers', 'wellme-pamphlets' ); ?>
                    </button>
                    <button type="button" class="wellme-assessment-reset">
                        <?php esc_html_e( 'Try Again', 'wellme-pamphlets' ); ?>
                    </button>
                </div>

                <div class="wellme-assessment-summary" aria-live="polite" aria-atomic="true" hidden></div>
            </form>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $video_url ) : ?>
    <section class="wellme-pamphlet-section wellme-section-video">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Video', 'wellme-pamphlets' ); ?></h2>
            <div class="wellme-video-wrapper">
                <?php echo wp_oembed_get( esc_url( $video_url ) ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* ── Gallery ────────────────────────────────────────────── */ ?>
    <?php if ( ! empty( $gallery ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-gallery">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <div class="wellme-gallery">
                <?php foreach ( $gallery as $img ) : ?>
                <figure class="wellme-gallery-item">
                    <img src="<?php echo esc_url( $img['sizes']['medium'] ?? $img['url'] ); ?>"
                         alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>">
                </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</div><?php /* .wellme-pamphlet */ ?>
