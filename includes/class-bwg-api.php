<?php
/**
 * BWG Rentals API Client Class
 *
 * Handles communication with the Direct Software API.
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * API client class
 */
class BWG_API {

    /**
     * Base API URL
     */
    const API_BASE_URL = 'https://app.getdirect.io/api/public/';

    /**
     * API version header
     */
    const API_VERSION = 'application/vnd.direct.v1';

    /**
     * Cache instance
     *
     * @var BWG_Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param BWG_Cache $cache Cache instance.
     */
    public function __construct( $cache ) {
        $this->cache = $cache;
    }

    /**
     * Get API credentials
     *
     * @return array API key and organization ID.
     */
    private function get_credentials() {
        return array(
            'api_key' => BWG_Admin::decrypt_value( get_option( 'bwg_rentals_api_key' ) ),
            'org_id'  => BWG_Admin::decrypt_value( get_option( 'bwg_rentals_organization_id' ) ),
        );
    }

    /**
     * Make an API request
     *
     * @param string $endpoint API endpoint.
     * @param array  $args     Request arguments.
     * @return array|WP_Error Response data or error.
     */
    private function request( $endpoint, $args = array() ) {
        $credentials = $this->get_credentials();

        if ( empty( $credentials['api_key'] ) || empty( $credentials['org_id'] ) ) {
            return new WP_Error( 'no_credentials', __( 'API credentials not configured.', 'bwg-rentals' ) );
        }

        $url = self::API_BASE_URL . $credentials['org_id'] . '/' . $endpoint;

        $default_args = array(
            'headers' => array(
                'Authorization' => 'Token ' . $credentials['api_key'],
                'Accept'        => self::API_VERSION,
                'Content-Type'  => 'application/json',
            ),
            'timeout' => 30,
        );

        $args = wp_parse_args( $args, $default_args );

        $response = wp_remote_get( $url, $args );

        // Check for errors
        if ( is_wp_error( $response ) ) {
            BWG_Rentals::log( 'API request failed: ' . $response->get_error_message() );
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );

        // Handle rate limiting
        if ( 429 === $status_code ) {
            BWG_Rentals::log( 'API rate limited. Retrying in 2 seconds.', 'warning' );
            sleep( 2 );
            return $this->request( $endpoint, $args );
        }

        // Handle errors
        if ( $status_code >= 400 ) {
            $error_message = sprintf(
                /* translators: %d: HTTP status code */
                __( 'API error: HTTP %d', 'bwg-rentals' ),
                $status_code
            );
            BWG_Rentals::log( $error_message );
            return new WP_Error( 'api_error', $error_message, array( 'status' => $status_code ) );
        }

        // Parse JSON response
        $data = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            BWG_Rentals::log( 'Failed to parse API response: ' . json_last_error_msg() );
            return new WP_Error( 'json_error', __( 'Failed to parse API response.', 'bwg-rentals' ) );
        }

        return $data;
    }

    /**
     * Test API connection
     *
     * @return array Result with success boolean and message.
     */
    public function test_connection() {
        $result = $this->request( 'properties' );

        if ( is_wp_error( $result ) ) {
            return array(
                'success' => false,
                'message' => $result->get_error_message(),
            );
        }

        return array(
            'success' => true,
            'message' => __( 'Connection successful! API is responding.', 'bwg-rentals' ),
        );
    }

    /**
     * Get all properties
     *
     * @param bool $use_cache Whether to use cached data.
     * @return array|WP_Error Properties array or error.
     */
    public function get_properties( $use_cache = true ) {
        $cache_key = 'properties';

        if ( $use_cache ) {
            $cached = $this->cache->get( $cache_key );
            if ( false !== $cached ) {
                return $cached;
            }
        }

        $data = $this->request( 'properties' );

        if ( ! is_wp_error( $data ) ) {
            $this->cache->set( $cache_key, $data );
        }

        return $data;
    }

    /**
     * Get a single property
     *
     * @param int  $property_id Property ID.
     * @param bool $use_cache   Whether to use cached data.
     * @return array|WP_Error Property data or error.
     */
    public function get_property( $property_id, $use_cache = true ) {
        $property_id = absint( $property_id );
        $cache_key   = 'property_' . $property_id;

        if ( $use_cache ) {
            $cached = $this->cache->get( $cache_key );
            if ( false !== $cached ) {
                return $cached;
            }
        }

        $data = $this->request( 'properties/' . $property_id );

        if ( ! is_wp_error( $data ) ) {
            $this->cache->set( $cache_key, $data );
        }

        return $data;
    }

    /**
     * Get property availability
     *
     * @param int  $property_id Property ID.
     * @param bool $use_cache   Whether to use cached data.
     * @return array|WP_Error Availability data or error.
     */
    public function get_availability( $property_id, $use_cache = true ) {
        $property_id = absint( $property_id );
        $cache_key   = 'availability_' . $property_id;

        if ( $use_cache ) {
            $cached = $this->cache->get( $cache_key, 'availability' );
            if ( false !== $cached ) {
                return $cached;
            }
        }

        $data = $this->request( 'properties/' . $property_id . '/availability' );

        if ( ! is_wp_error( $data ) ) {
            $this->cache->set( $cache_key, $data, 'availability' );
        }

        return $data;
    }

    /**
     * Get property rates
     *
     * @param int  $property_id Property ID.
     * @param bool $use_cache   Whether to use cached data.
     * @return array|WP_Error Rates data or error.
     */
    public function get_rates( $property_id, $use_cache = true ) {
        $property_id = absint( $property_id );
        $cache_key   = 'rates_' . $property_id;

        if ( $use_cache ) {
            $cached = $this->cache->get( $cache_key );
            if ( false !== $cached ) {
                return $cached;
            }
        }

        $data = $this->request( 'properties/' . $property_id . '/rates' );

        if ( ! is_wp_error( $data ) ) {
            $this->cache->set( $cache_key, $data );
        }

        return $data;
    }

    /**
     * Get the booking URL for a property
     *
     * @param int $property_id Property ID.
     * @return string Booking URL.
     */
    public function get_booking_url( $property_id ) {
        $credentials = $this->get_credentials();
        $property_id = absint( $property_id );

        // Format: https://app.getdirect.io/listings/{org_id}/{property_id}
        return 'https://app.getdirect.io/listings/' . $credentials['org_id'] . '/' . $property_id;
    }
}
