<?php
/**
 * BWG Rentals Data Sync Class
 *
 * Handles syncing API data to local WordPress storage (Custom Post Type) for improved
 * performance and offline resilience. Uses the BWG_CPT class for actual storage.
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Data Sync Class
 */
class BWG_Data_Sync {

    /**
     * Option name for last sync timestamp
     */
    const LAST_SYNC_OPTION = 'bwg_rentals_last_sync';

    /**
     * Option name for sync status
     */
    const SYNC_STATUS_OPTION = 'bwg_rentals_sync_status';

    /**
     * Sync all properties from API to local storage
     *
     * The /properties list endpoint only returns basic info (id, name, featured_image).
     * To get full property details including specs (bedrooms, bathrooms, etc.), we need
     * to fetch each property individually using the /properties/{id} endpoint.
     *
     * @param BWG_API $api API instance.
     * @return array Result with success status and message.
     */
    public function sync_all_properties( $api ) {
        // Update sync status
        $this->set_sync_status( 'running', __( 'Fetching properties list from API...', 'bwg-rentals' ) );

        // Fetch properties list from API (bypass cache to get fresh data)
        // Note: This only returns basic info - we need to fetch full details for each property
        $properties_list = $api->get_properties( false );

        if ( is_wp_error( $properties_list ) ) {
            $error_message = $properties_list->get_error_message();
            $this->set_sync_status( 'error', $error_message );
            BWG_Rentals::log( 'Sync failed: ' . $error_message, 'error' );
            return array(
                'success' => false,
                'message' => $error_message,
            );
        }

        if ( empty( $properties_list ) ) {
            $this->set_sync_status( 'complete', __( 'No properties found in API.', 'bwg-rentals' ) );
            return array(
                'success' => true,
                'message' => __( 'No properties found in API.', 'bwg-rentals' ),
                'count'   => 0,
            );
        }

        $total_count = count( $properties_list );
        $synced_count = 0;
        $errors = array();

        // Fetch full details for each property individually
        foreach ( $properties_list as $index => $basic_property ) {
            $property_id = isset( $basic_property['id'] ) ? absint( $basic_property['id'] ) : 0;

            if ( ! $property_id ) {
                $errors[] = 'unknown';
                continue;
            }

            // Update status with progress
            $this->set_sync_status( 'running', sprintf(
                /* translators: 1: current property number, 2: total properties, 3: property name */
                __( 'Syncing property %1$d of %2$d: %3$s...', 'bwg-rentals' ),
                $index + 1,
                $total_count,
                isset( $basic_property['name'] ) ? $basic_property['name'] : $property_id
            ) );

            // Fetch full property details (includes bedrooms, bathrooms, etc. from units array)
            $full_property = $api->get_property( $property_id, false );

            if ( is_wp_error( $full_property ) ) {
                $errors[] = $property_id;
                BWG_Rentals::log( 'Failed to fetch property ' . $property_id . ': ' . $full_property->get_error_message(), 'error' );
                continue;
            }

            // Upsert the full property data
            $result = BWG_CPT::upsert_property( $full_property );

            if ( is_wp_error( $result ) ) {
                $errors[] = $property_id;
                BWG_Rentals::log( 'Failed to sync property ' . $property_id . ': ' . $result->get_error_message(), 'error' );
            } else {
                $synced_count++;
            }

            // Small delay to avoid rate limiting (100ms between requests)
            usleep( 100000 );
        }

        // Update last sync time
        update_option( self::LAST_SYNC_OPTION, current_time( 'timestamp' ) );

        // Set final status
        if ( empty( $errors ) ) {
            $message = sprintf(
                /* translators: %d: number of properties synced */
                __( 'Successfully synced %d properties.', 'bwg-rentals' ),
                $synced_count
            );
            $this->set_sync_status( 'complete', $message );
        } else {
            $message = sprintf(
                /* translators: 1: number of properties synced, 2: number of errors */
                __( 'Synced %1$d properties with %2$d errors.', 'bwg-rentals' ),
                $synced_count,
                count( $errors )
            );
            $this->set_sync_status( 'partial', $message );
        }

        BWG_Rentals::log( $message, 'info' );

        return array(
            'success' => true,
            'message' => $message,
            'count'   => $synced_count,
            'errors'  => $errors,
        );
    }

    /**
     * Sync a single property from API to local storage
     *
     * @param BWG_API $api         API instance.
     * @param int     $property_id Property ID.
     * @return array Result with success status and message.
     */
    public function sync_single_property( $api, $property_id ) {
        $property_id = absint( $property_id );

        // Fetch property from API (bypass cache)
        $property = $api->get_property( $property_id, false );

        if ( is_wp_error( $property ) ) {
            return array(
                'success' => false,
                'message' => $property->get_error_message(),
            );
        }

        $result = BWG_CPT::upsert_property( $property );

        if ( is_wp_error( $result ) ) {
            return array(
                'success' => false,
                'message' => $result->get_error_message(),
            );
        }

        return array(
            'success' => true,
            'message' => sprintf(
                /* translators: %d: property ID */
                __( 'Property %d synced successfully.', 'bwg-rentals' ),
                $property_id
            ),
        );
    }

    /**
     * Sync availability and rates for all local properties
     *
     * @param BWG_API $api API instance.
     * @return array Result with success status and message.
     */
    public function sync_availability_and_rates( $api ) {
        $api_ids = BWG_CPT::get_all_api_ids();

        if ( empty( $api_ids ) ) {
            return array(
                'success' => true,
                'message' => __( 'No local properties to update.', 'bwg-rentals' ),
                'count'   => 0,
            );
        }

        $updated_count = 0;
        $errors = array();

        foreach ( $api_ids as $property_id ) {
            // Sync availability
            $availability = $api->get_availability( $property_id, false );
            if ( ! is_wp_error( $availability ) ) {
                BWG_CPT::update_availability( $property_id, $availability );
            }

            // Sync rates
            $rates = $api->get_rates( $property_id, false );
            if ( ! is_wp_error( $rates ) ) {
                BWG_CPT::update_rates( $property_id, $rates );
            }

            if ( ! is_wp_error( $availability ) || ! is_wp_error( $rates ) ) {
                $updated_count++;
            } else {
                $errors[] = $property_id;
            }
        }

        $message = sprintf(
            /* translators: %d: number of properties updated */
            __( 'Updated availability/rates for %d properties.', 'bwg-rentals' ),
            $updated_count
        );

        BWG_Rentals::log( $message, 'info' );

        return array(
            'success' => true,
            'message' => $message,
            'count'   => $updated_count,
            'errors'  => $errors,
        );
    }

    /**
     * Get property count from local storage
     *
     * @return int Number of properties.
     */
    public function get_property_count() {
        return BWG_CPT::get_property_count();
    }

    /**
     * Delete all local properties
     *
     * @return int Number of deleted properties.
     */
    public function delete_all_properties() {
        $deleted = BWG_CPT::delete_all_properties();

        // Clear sync status
        delete_option( self::LAST_SYNC_OPTION );
        $this->set_sync_status( 'never', __( 'Never synced', 'bwg-rentals' ) );

        BWG_Rentals::log( 'Deleted ' . $deleted . ' local properties.', 'info' );

        return $deleted;
    }

    /**
     * Set sync status
     *
     * @param string $status  Status (running, complete, error, partial, never).
     * @param string $message Status message.
     */
    private function set_sync_status( $status, $message ) {
        update_option( self::SYNC_STATUS_OPTION, array(
            'status'    => $status,
            'message'   => $message,
            'timestamp' => current_time( 'timestamp' ),
        ) );
    }

    /**
     * Get sync status
     *
     * Returns status in format expected by admin settings template:
     * - last_sync_time: Human-readable last sync time
     * - last_sync_status: Status key (completed, running, failed, unknown)
     * - status_message: Additional status message
     * - property_count: Number of locally stored properties
     * - auto_sync: Whether auto-sync is enabled
     * - next_scheduled: Human-readable next scheduled sync time
     * - errors: Array of recent errors
     *
     * @return array Status data.
     */
    public function get_sync_status() {
        $stored_status = get_option( self::SYNC_STATUS_OPTION, array(
            'status'    => 'never',
            'message'   => '',
            'timestamp' => null,
        ) );

        // Map internal status to template-expected format
        $status_map = array(
            'never'    => 'unknown',
            'running'  => 'running',
            'complete' => 'completed',
            'partial'  => 'completed',
            'error'    => 'failed',
        );

        $last_sync = get_option( self::LAST_SYNC_OPTION, null );
        $last_sync_time = $last_sync
            ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_sync )
            : __( 'Never', 'bwg-rentals' );

        // Check for scheduled sync
        $next_scheduled = wp_next_scheduled( 'bwg_rentals_auto_sync' );
        $next_scheduled_time = $next_scheduled
            ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next_scheduled )
            : __( 'Not scheduled', 'bwg-rentals' );

        // Check if auto-sync is enabled
        $auto_sync = (bool) $next_scheduled;

        // Build response in expected format
        return array(
            'last_sync_time'   => $last_sync_time,
            'last_sync_status' => isset( $status_map[ $stored_status['status'] ] )
                ? $status_map[ $stored_status['status'] ]
                : 'unknown',
            'status_message'   => isset( $stored_status['message'] ) ? $stored_status['message'] : '',
            'property_count'   => $this->get_property_count(),
            'auto_sync'        => $auto_sync,
            'next_scheduled'   => $next_scheduled_time,
            'errors'           => array(), // TODO: Implement error tracking
            // Legacy keys for backward compatibility
            'last_sync'        => $last_sync_time,
            'status'           => isset( $stored_status['status'] ) ? $stored_status['status'] : 'never',
        );
    }

    /**
     * Check if local data is stale
     *
     * Compares last sync time against cache duration setting.
     *
     * @return bool True if data is stale or never synced.
     */
    public function is_data_stale() {
        $last_sync = get_option( self::LAST_SYNC_OPTION, null );

        if ( ! $last_sync ) {
            return true; // Never synced
        }

        $cache_duration = get_option( 'bwg_rentals_cache_duration', 24 );
        $stale_time = $last_sync + ( $cache_duration * HOUR_IN_SECONDS );

        return current_time( 'timestamp' ) > $stale_time;
    }

    /**
     * Schedule automatic sync
     *
     * @param string $frequency How often to sync (hourly, twicedaily, daily).
     */
    public static function schedule_sync( $frequency = 'twicedaily' ) {
        $hook = 'bwg_rentals_auto_sync';

        // Clear existing schedule
        wp_clear_scheduled_hook( $hook );

        // Schedule new event
        if ( ! wp_next_scheduled( $hook ) ) {
            wp_schedule_event( time(), $frequency, $hook );
        }
    }

    /**
     * Unschedule automatic sync
     */
    public static function unschedule_sync() {
        wp_clear_scheduled_hook( 'bwg_rentals_auto_sync' );
    }

    /**
     * Run scheduled sync
     *
     * Callback for wp_cron event.
     */
    public static function run_scheduled_sync() {
        $cache = new BWG_Cache();
        $api = new BWG_API( $cache );
        $sync = new self();

        $result = $sync->sync_all_properties( $api );

        BWG_Rentals::log( 'Scheduled sync completed: ' . $result['message'], 'info' );
    }
}
