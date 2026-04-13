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
$gallery      = get_field( 'module_gallery',           $module->ID ) ?: [];
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
        </div>
    </section>

    <?php /* ── Chapter navigation ─────────────────────────────────── */ ?>
    <?php if ( ! empty( $chapters ) ) : ?>
    <section class="wellme-pamphlet-section wellme-section-chapters">
        <div class="wellme-section-inner wellme-scroll-reveal">
            <h2><?php esc_html_e( 'Chapters', 'wellme-pamphlets' ); ?></h2>
            <nav class="wellme-chapter-nav" aria-label="<?php esc_attr_e( 'Module chapters', 'wellme-pamphlets' ); ?>">
                <?php foreach ( $chapters as $i => $chapter ) : ?>
                <button class="wellme-chapter-btn"
                        data-chapter="<?php echo esc_attr( $i ); ?>"
                        aria-controls="wellme-chapter-panel-<?php echo esc_attr( $module->ID . '-' . $i ); ?>">
                    <?php echo esc_html( $chapter['chapter_title'] ); ?>
                </button>
                <?php endforeach; ?>
            </nav>

            <?php foreach ( $chapters as $i => $chapter ) :
                $ch_img = $chapter['chapter_image']['url'] ?? '';
            ?>
            <div class="wellme-chapter-panel"
                 id="wellme-chapter-panel-<?php echo esc_attr( $module->ID . '-' . $i ); ?>"
                 data-chapter="<?php echo esc_attr( $i ); ?>"
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
            <div class="wellme-outcomes-list">
                <?php foreach ( $outcomes as $i => $outcome ) :
                    $icon_url = $outcome['outcome_icon']['url'] ?? '';
                    $panel_id = 'wellme-outcome-panel-' . $module->ID . '-' . $i;
                ?>
                <button class="wellme-outcome-btn"
                        data-target="<?php echo esc_attr( $panel_id ); ?>"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr( $panel_id ); ?>">
                    <?php if ( $icon_url ) : ?>
                    <img src="<?php echo esc_url( $icon_url ); ?>" alt="" aria-hidden="true" class="wellme-outcome-icon">
                    <?php endif; ?>
                    <?php echo esc_html( $outcome['outcome_title'] ); ?>
                </button>
                <?php endforeach; ?>
            </div>

            <?php /* Side-panel overlay for outcome detail */ ?>
            <?php foreach ( $outcomes as $i => $outcome ) :
                $panel_id = 'wellme-outcome-panel-' . $module->ID . '-' . $i;
            ?>
            <div class="wellme-outcome-panel"
                 id="<?php echo esc_attr( $panel_id ); ?>"
                 role="region"
                 aria-label="<?php echo esc_attr( $outcome['outcome_title'] ); ?>"
                 hidden>
                <button class="wellme-outcome-panel-close" aria-label="<?php esc_attr_e( 'Close', 'wellme-pamphlets' ); ?>">&times;</button>
                <h3><?php echo esc_html( $outcome['outcome_title'] ); ?></h3>
                <div class="wellme-outcome-detail">
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
            <h2><?php esc_html_e( 'Exercise Steps', 'wellme-pamphlets' ); ?></h2>

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

    <?php /* ── Video ─────────────────────────────────────────────── */ ?>
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
