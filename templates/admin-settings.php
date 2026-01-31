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
</div>
