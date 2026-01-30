<?php
/**
 * BWG Rentals Cache Class
 *
 * Handles caching using WordPress Transients API.
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cache management class
 */
class BWG_Cache {

    /**
     * Cache prefix
     */
    const CACHE_PREFIX = 'bwg_rentals_';

    /**
     * Default cache duration in seconds (24 hours)
     */
    const DEFAULT_DURATION = 86400;

    /**
     * Short cache duration for availability (15 minutes)
     */
    const AVAILABILITY_DURATION = 900;

    /**
     * Get cache duration in seconds
     *
     * @param string $type Cache type (default or availability).
     * @return int Duration in seconds.
     */
    private function get_duration( $type = 'default' ) {
        if ( 'availability' === $type ) {
            return self::AVAILABILITY_DURATION;
        }

        $hours = get_option( 'bwg_rentals_cache_duration', 24 );
        return absint( $hours ) * HOUR_IN_SECONDS;
    }

    /**
     * Get a cached value
     *
     * @param string $key  Cache key.
     * @param string $type Cache type.
     * @return mixed Cached value or false if not found.
     */
    public function get( $key, $type = 'default' ) {
        $full_key = self::CACHE_PREFIX . $key;
        return get_transient( $full_key );
    }

    /**
     * Set a cached value
     *
     * @param string $key   Cache key.
     * @param mixed  $value Value to cache.
     * @param string $type  Cache type.
     * @return bool Success.
     */
    public function set( $key, $value, $type = 'default' ) {
        $full_key = self::CACHE_PREFIX . $key;
        $duration = $this->get_duration( $type );

        // Also store metadata for cache status
        $this->update_metadata( $key );

        return set_transient( $full_key, $value, $duration );
    }

    /**
     * Delete a cached value
     *
     * @param string $key Cache key.
     * @return bool Success.
     */
    public function delete( $key ) {
        $full_key = self::CACHE_PREFIX . $key;
        return delete_transient( $full_key );
    }

    /**
     * Clear all plugin cache
     *
     * @return int Number of items cleared.
     */
    public function clear_all() {
        global $wpdb;

        // Delete all transients with our prefix
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options}
                WHERE option_name LIKE %s
                OR option_name LIKE %s",
                '_transient_' . self::CACHE_PREFIX . '%',
                '_transient_timeout_' . self::CACHE_PREFIX . '%'
            )
        );

        // Clear metadata
        delete_option( 'bwg_rentals_cache_metadata' );

        BWG_Rentals::log( 'Cache cleared. Removed ' . $result . ' items.', 'info' );

        return $result;
    }

    /**
     * Update cache metadata
     *
     * @param string $key Cache key being set.
     */
    private function update_metadata( $key ) {
        $metadata = get_option( 'bwg_rentals_cache_metadata', array() );

        $metadata['last_updated'] = current_time( 'timestamp' );
        $metadata['items']        = isset( $metadata['items'] ) ? $metadata['items'] : array();

        if ( ! in_array( $key, $metadata['items'], true ) ) {
            $metadata['items'][] = $key;
        }

        update_option( 'bwg_rentals_cache_metadata', $metadata );
    }

    /**
     * Get cache status information
     *
     * @return array Cache status.
     */
    public function get_status() {
        $metadata = get_option( 'bwg_rentals_cache_metadata', array() );

        $last_updated = isset( $metadata['last_updated'] )
            ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $metadata['last_updated'] )
            : __( 'Never', 'bwg-rentals' );

        $item_count = isset( $metadata['items'] ) ? count( $metadata['items'] ) : 0;

        return array(
            'last_updated' => $last_updated,
            'item_count'   => $item_count,
            'duration'     => get_option( 'bwg_rentals_cache_duration', 24 ) . ' ' . __( 'hours', 'bwg-rentals' ),
        );
    }
}
