<?php
/**
 * BWG Rentals Uninstall
 *
 * Removes all plugin data when the plugin is deleted.
 *
 * @package BWG_Rentals
 */

// Exit if not called by WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete all plugin options
 */
$options = array(
    // API credentials (primary option name)
    'bwg_rentals_api_key',

    // Organization ID (both variants - admin uses org_id, API uses organization_id)
    'bwg_rentals_org_id',
    'bwg_rentals_organization_id',

    // Cache settings
    'bwg_rentals_cache_duration',
    'bwg_rentals_cache_metadata',

    // Button text (both variants - admin uses booking_button_text, shortcodes use button_text)
    'bwg_rentals_booking_button_text',
    'bwg_rentals_button_text',
);

foreach ( $options as $option ) {
    delete_option( $option );
}

/**
 * Delete all transients (cached data)
 */
global $wpdb;

// Delete transients
$wpdb->query(
    "DELETE FROM {$wpdb->options}
    WHERE option_name LIKE '_transient_bwg_rentals_%'
    OR option_name LIKE '_transient_timeout_bwg_rentals_%'"
);

// Clear any scheduled events
wp_clear_scheduled_hook( 'bwg_rentals_cache_refresh' );
