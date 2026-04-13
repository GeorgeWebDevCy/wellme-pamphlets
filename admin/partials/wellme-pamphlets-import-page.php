<?php
/**
 * Import page for WELLME module packages.
 *
 * @since      1.0.2
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/admin/partials
 */

defined( 'ABSPATH' ) || exit;

$report_type    = $report['type'] ?? '';
$report_message = $report['message'] ?? '';
$summary        = $report['report'] ?? [];
?>
<div class="wrap wellme-import-page">
    <h1><?php esc_html_e( 'Import WELLME Modules', 'wellme-pamphlets' ); ?></h1>

    <div class="wellme-import-card">
        <p>
            <?php esc_html_e( 'Generate a WELLME import package locally, then upload the ZIP here to create or update the six WELLME module posts automatically.', 'wellme-pamphlets' ); ?>
        </p>

        <div class="wellme-import-help">
            <strong><?php esc_html_e( 'Suggested local command', 'wellme-pamphlets' ); ?></strong>
            <code>python scripts/build-import-package.py --source-root "C:\Users\georg\Downloads\WellMe-20260413T092223Z-3-001\WellMe" --output "dist\wellme-import-package.zip"</code>
        </div>

        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" class="wellme-import-form">
            <?php wp_nonce_field( Wellme_Pamphlets_Importer::ACTION ); ?>
            <input type="hidden" name="action" value="<?php echo esc_attr( Wellme_Pamphlets_Importer::ACTION ); ?>">

            <label for="wellme-import-package" class="wellme-import-label">
                <?php esc_html_e( 'Import package ZIP', 'wellme-pamphlets' ); ?>
            </label>
            <input
                type="file"
                name="wellme_import_package"
                id="wellme-import-package"
                accept=".zip,application/zip"
                required
            >

            <p class="description">
                <?php esc_html_e( 'The package should contain a manifest.json file plus any referenced images.', 'wellme-pamphlets' ); ?>
            </p>

            <?php submit_button( __( 'Import Modules', 'wellme-pamphlets' ) ); ?>
        </form>
    </div>

    <?php if ( $report_type && $report_message ) : ?>
    <div class="notice notice-<?php echo 'error' === $report_type ? 'error' : 'success'; ?> is-dismissible">
        <p><?php echo esc_html( $report_message ); ?></p>
    </div>
    <?php endif; ?>

    <?php if ( ! empty( $summary ) ) : ?>
    <div class="wellme-import-card wellme-import-report">
        <h2><?php esc_html_e( 'Latest Import Summary', 'wellme-pamphlets' ); ?></h2>

        <div class="wellme-import-stats">
            <div>
                <span class="wellme-import-stat-value"><?php echo esc_html( (string) ( $summary['created'] ?? 0 ) ); ?></span>
                <span class="wellme-import-stat-label"><?php esc_html_e( 'Created', 'wellme-pamphlets' ); ?></span>
            </div>
            <div>
                <span class="wellme-import-stat-value"><?php echo esc_html( (string) ( $summary['updated'] ?? 0 ) ); ?></span>
                <span class="wellme-import-stat-label"><?php esc_html_e( 'Updated', 'wellme-pamphlets' ); ?></span>
            </div>
        </div>

        <?php if ( ! empty( $summary['generated_at'] ) ) : ?>
        <p class="description">
            <?php
            printf(
                /* translators: %s: ISO datetime */
                esc_html__( 'Package generated at %s.', 'wellme-pamphlets' ),
                esc_html( $summary['generated_at'] )
            );
            ?>
        </p>
        <?php endif; ?>

        <?php if ( ! empty( $summary['modules'] ) ) : ?>
        <table class="widefat striped wellme-import-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Module', 'wellme-pamphlets' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'wellme-pamphlets' ); ?></th>
                    <th><?php esc_html_e( 'Post ID', 'wellme-pamphlets' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $summary['modules'] as $module_report ) : ?>
                <tr>
                    <td>
                        <?php
                        printf(
                            /* translators: 1: module number 2: module title */
                            esc_html__( 'Module %1$d: %2$s', 'wellme-pamphlets' ),
                            (int) ( $module_report['module_number'] ?? 0 ),
                            esc_html( $module_report['title'] ?? '' )
                        );
                        ?>
                    </td>
                    <td><?php echo esc_html( ucfirst( (string) ( $module_report['status'] ?? '' ) ) ); ?></td>
                    <td><?php echo esc_html( (string) ( $module_report['post_id'] ?? '' ) ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if ( ! empty( $summary['warnings'] ) ) : ?>
        <div class="wellme-import-warnings">
            <h3><?php esc_html_e( 'Warnings', 'wellme-pamphlets' ); ?></h3>
            <ul>
                <?php foreach ( $summary['warnings'] as $warning ) : ?>
                <li><?php echo esc_html( $warning ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
