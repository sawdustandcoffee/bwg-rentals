<?php
/**
 * Admin Settings Page Template
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e( 'BWG Rentals Settings', 'bwg-rentals' ); ?></h1>

    <?php settings_errors( 'bwg_rentals_settings' ); ?>

    <form method="post" action="options.php" id="bwg-settings-form" novalidate>
        <?php settings_fields( 'bwg_rentals_settings' ); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_api_key"><?php esc_html_e( 'API Key', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="password"
                            name="bwg_rentals_api_key"
                            id="bwg_rentals_api_key"
                            value="<?php echo esc_attr( $api_key ); ?>"
                            class="regular-text"
                            autocomplete="off"
                            data-validate="api-key"
                            minlength="8"
                            pattern="[a-zA-Z0-9_\-]+"
                        />
                        <span class="bwg-field-error" id="bwg_rentals_api_key-error" role="alert"></span>
                        <p class="description">
                            <?php esc_html_e( 'Enter your Direct Software API key.', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_org_id"><?php esc_html_e( 'Organization ID', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            name="bwg_rentals_org_id"
                            id="bwg_rentals_org_id"
                            value="<?php echo esc_attr( $org_id ); ?>"
                            class="regular-text"
                            data-validate="org-id"
                            minlength="2"
                            pattern="[a-zA-Z0-9_\-]+"
                        />
                        <span class="bwg-field-error" id="bwg_rentals_org_id-error" role="alert"></span>
                        <p class="description">
                            <?php esc_html_e( 'Enter your Direct Software organization ID.', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_cache_duration"><?php esc_html_e( 'Cache Duration', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="number"
                            name="bwg_rentals_cache_duration"
                            id="bwg_rentals_cache_duration"
                            value="<?php echo esc_attr( $cache_dur ); ?>"
                            class="small-text"
                            min="1"
                            max="168"
                            data-validate="cache-duration"
                            required
                        />
                        <?php esc_html_e( 'hours', 'bwg-rentals' ); ?>
                        <span class="bwg-field-error" id="bwg_rentals_cache_duration-error" role="alert"></span>
                        <p class="description">
                            <?php esc_html_e( 'How long to cache property data (1-168 hours). Availability data is cached for 15 minutes regardless of this setting.', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_booking_button_text"><?php esc_html_e( 'Default Button Text', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            name="bwg_rentals_booking_button_text"
                            id="bwg_rentals_booking_button_text"
                            value="<?php echo esc_attr( $btn_text ); ?>"
                            class="regular-text"
                            data-validate="button-text"
                            maxlength="50"
                        />
                        <span class="bwg-field-error" id="bwg_rentals_booking_button_text-error" role="alert"></span>
                        <p class="description">
                            <?php esc_html_e( 'Default text for the booking button shortcode (max 50 characters).', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_property_page"><?php esc_html_e( 'Property Detail Page', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_dropdown_pages( array(
                            'name'              => 'bwg_rentals_property_page',
                            'id'                => 'bwg_rentals_property_page',
                            'selected'          => $property_page,
                            'show_option_none'  => __( '— Select a page —', 'bwg-rentals' ),
                            'option_none_value' => '0',
                        ) );
                        ?>
                        <p class="description">
                            <?php esc_html_e( 'Select the page that displays individual property details. Add the [bwg_property_full] shortcode to that page.', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_booking_base_url"><?php esc_html_e( 'Booking Base URL', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="url"
                            name="bwg_rentals_booking_base_url"
                            id="bwg_rentals_booking_base_url"
                            value="<?php echo esc_attr( $booking_base_url ); ?>"
                            class="regular-text"
                            placeholder="https://bookings.example.com"
                        />
                        <p class="description">
                            <?php esc_html_e( 'Base URL for booking links. Property slug will be appended (e.g., /listings/beach-please). Leave empty to use default Direct Software URL.', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="bwg_rentals_google_maps_api_key"><?php esc_html_e( 'Google Maps API Key', 'bwg-rentals' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="password"
                            name="bwg_rentals_google_maps_api_key"
                            id="bwg_rentals_google_maps_api_key"
                            value="<?php echo esc_attr( $google_maps_api_key ); ?>"
                            class="regular-text"
                            autocomplete="off"
                        />
                        <p class="description">
                            <?php esc_html_e( 'Your Google Maps API key for displaying property locations on maps. Leave empty to disable maps.', 'bwg-rentals' ); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>

    <hr />

    <h2><?php esc_html_e( 'API Connection', 'bwg-rentals' ); ?></h2>
    <p>
        <button type="button" class="button" id="bwg-test-connection">
            <?php esc_html_e( 'Test Connection', 'bwg-rentals' ); ?>
        </button>
        <span id="bwg-connection-status"></span>
    </p>

    <hr />

    <h2><?php esc_html_e( 'Cache Management', 'bwg-rentals' ); ?></h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e( 'Cache Status', 'bwg-rentals' ); ?></th>
                <td>
                    <p>
                        <strong><?php esc_html_e( 'Last Updated:', 'bwg-rentals' ); ?></strong>
                        <?php echo esc_html( $cache_status['last_updated'] ); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Cached Items:', 'bwg-rentals' ); ?></strong>
                        <?php echo esc_html( $cache_status['item_count'] ); ?>
                        <?php if ( isset( $cache_status['local_count'] ) && $cache_status['local_count'] > 0 ) : ?>
                            <span class="description" style="margin-left: 5px;">
                                (<?php
                                printf(
                                    /* translators: %1$d: local properties count, %2$d: transient cache count */
                                    esc_html__( '%1$d local, %2$d transients', 'bwg-rentals' ),
                                    $cache_status['local_count'],
                                    isset( $cache_status['transient_count'] ) ? $cache_status['transient_count'] : 0
                                );
                                ?>)
                            </span>
                        <?php endif; ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Duration:', 'bwg-rentals' ); ?></strong>
                        <?php echo esc_html( $cache_status['duration'] ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Clear Cache', 'bwg-rentals' ); ?></th>
                <td>
                    <button type="button" class="button" id="bwg-clear-cache">
                        <?php esc_html_e( 'Clear All Cache', 'bwg-rentals' ); ?>
                    </button>
                    <span id="bwg-cache-status"></span>
                    <p class="description">
                        <?php esc_html_e( 'Clear all cached property data. Fresh data will be fetched from the API on next page load.', 'bwg-rentals' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <hr />

    <h2><?php esc_html_e( 'Local Data Sync', 'bwg-rentals' ); ?></h2>
    <p class="description" style="margin-bottom: 15px;">
        <?php esc_html_e( 'Store property data locally in your WordPress database for instant page loads. Synced data is used first, with the API as a fallback.', 'bwg-rentals' ); ?>
    </p>
    <?php
    // Get sync status from BWG_Data_Sync
    $data_sync = new BWG_Data_Sync();
    $sync_status = $data_sync->get_sync_status();
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e( 'Sync Status', 'bwg-rentals' ); ?></th>
                <td>
                    <div id="bwg-sync-status-container">
                        <p>
                            <strong><?php esc_html_e( 'Last Sync:', 'bwg-rentals' ); ?></strong>
                            <span id="bwg-sync-last-sync"><?php echo esc_html( $sync_status['last_sync_time'] ); ?></span>
                        </p>
                        <p>
                            <strong><?php esc_html_e( 'Local Properties:', 'bwg-rentals' ); ?></strong>
                            <span id="bwg-sync-property-count"><?php echo esc_html( $sync_status['property_count'] ); ?></span>
                        </p>
                        <p>
                            <strong><?php esc_html_e( 'Status:', 'bwg-rentals' ); ?></strong>
                            <span id="bwg-sync-status-message">
                                <?php
                                $status_labels = array(
                                    'unknown'   => __( 'Unknown', 'bwg-rentals' ),
                                    'running'   => __( 'Sync in progress...', 'bwg-rentals' ),
                                    'completed' => __( 'Complete', 'bwg-rentals' ),
                                    'failed'    => __( 'Failed', 'bwg-rentals' ),
                                );
                                $status_key = isset( $sync_status['last_sync_status'] ) ? $sync_status['last_sync_status'] : 'unknown';
                                $label = isset( $status_labels[ $status_key ] ) ? $status_labels[ $status_key ] : ucfirst( $status_key );
                                echo esc_html( $label );
                                if ( ! empty( $sync_status['status_message'] ) ) {
                                    echo ' - ' . esc_html( $sync_status['status_message'] );
                                }
                                ?>
                            </span>
                        </p>
                        <p>
                            <strong><?php esc_html_e( 'Next Scheduled Sync:', 'bwg-rentals' ); ?></strong>
                            <span id="bwg-sync-next-scheduled"><?php echo esc_html( $sync_status['next_scheduled'] ); ?></span>
                        </p>
                        <?php if ( ! empty( $sync_status['errors'] ) ) : ?>
                        <div class="bwg-sync-errors" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin-top: 10px;">
                            <strong><?php esc_html_e( 'Sync Errors:', 'bwg-rentals' ); ?></strong>
                            <ul style="margin: 5px 0 0 20px;">
                                <?php foreach ( array_slice( $sync_status['errors'], 0, 5 ) as $error ) : ?>
                                    <li><?php echo esc_html( $error ); ?></li>
                                <?php endforeach; ?>
                                <?php if ( count( $sync_status['errors'] ) > 5 ) : ?>
                                    <li><em><?php printf( esc_html__( '...and %d more errors', 'bwg-rentals' ), count( $sync_status['errors'] ) - 5 ); ?></em></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Auto-Sync', 'bwg-rentals' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="bwg_auto_sync_enabled" id="bwg_auto_sync_enabled" value="1" <?php checked( $sync_status['auto_sync'], true ); ?> />
                        <?php esc_html_e( 'Enable daily automatic sync (runs at 3 AM)', 'bwg-rentals' ); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e( 'When enabled, property data will be automatically synced from the API every day at 3 AM.', 'bwg-rentals' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Manual Sync', 'bwg-rentals' ); ?></th>
                <td>
                    <button type="button" class="button button-primary" id="bwg-sync-now">
                        <span class="bwg-sync-text"><?php esc_html_e( 'Sync Now', 'bwg-rentals' ); ?></span>
                        <span class="bwg-sync-spinner spinner" style="display: none; float: none; margin: 0 0 0 5px;"></span>
                    </button>
                    <span id="bwg-sync-result" class="bwg-ajax-status"></span>
                    <p class="description">
                        <?php esc_html_e( 'Fetch all property data from the API and store it locally. This may take a moment depending on the number of properties.', 'bwg-rentals' ); ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
