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

        // Add filter for mock API responses (useful for testing without real credentials)
        add_filter( 'pre_http_request', array( $this, 'maybe_mock_api_response' ), 10, 3 );
    }

    /**
     * Mock API response for testing purposes
     *
     * When API key starts with 'MOCK_', return mock data instead of making real API calls.
     * This allows testing the plugin functionality without valid Direct Software credentials.
     *
     * @param false|array|WP_Error $preempt      A preemptive return value of an HTTP request.
     * @param array                $parsed_args  HTTP request arguments.
     * @param string               $url          The request URL.
     * @return false|array Preemptive return value or false to continue with request.
     */
    public function maybe_mock_api_response( $preempt, $parsed_args, $url ) {
        // Only intercept requests to the Direct Software API
        if ( strpos( $url, self::API_BASE_URL ) === false ) {
            return $preempt;
        }

        // Check if using mock credentials (API key starts with MOCK_)
        $credentials = $this->get_credentials();
        if ( empty( $credentials['api_key'] ) || strpos( $credentials['api_key'], 'MOCK_' ) !== 0 ) {
            return $preempt;
        }

        // Determine which endpoint is being called
        $mock_data = array();

        if ( strpos( $url, '/properties/' ) !== false && strpos( $url, '/availability' ) !== false ) {
            // Mock availability data
            $mock_data = $this->get_mock_availability();
        } elseif ( strpos( $url, '/properties/' ) !== false && strpos( $url, '/rates' ) !== false ) {
            // Mock rates data
            $mock_data = $this->get_mock_rates();
        } elseif ( preg_match( '/\/properties\/(\d+)$/', $url, $matches ) ) {
            // Mock single property data
            $mock_data = $this->get_mock_property( (int) $matches[1] );
        } elseif ( strpos( $url, '/properties' ) !== false ) {
            // Mock properties list - check for empty mock key
            if ( strpos( $credentials['api_key'], 'MOCK_EMPTY_' ) === 0 ) {
                $mock_data = array(); // Return empty array to test empty state
            } else {
                $mock_data = $this->get_mock_properties();
            }
        }

        // Return mock HTTP response
        return array(
            'headers'  => array( 'content-type' => 'application/json' ),
            'body'     => wp_json_encode( $mock_data ),
            'response' => array(
                'code'    => 200,
                'message' => 'OK',
            ),
            'cookies'  => array(),
            'filename' => '',
        );
    }

    /**
     * Get mock properties list
     *
     * @return array Mock property data.
     */
    private function get_mock_properties() {
        return array(
            array(
                'id'          => 1,
                'name'        => 'Oceanfront Beach House',
                'headline'    => 'Stunning oceanfront property with private beach access',
                'description' => 'Experience luxury living in this beautiful 4-bedroom oceanfront beach house. Wake up to stunning ocean views, enjoy morning coffee on your private deck, and take a short walk to your own private beach.',
                'bedrooms'    => 4,
                'bathrooms'   => 3,
                'sleeps'      => 10,
                'sqft'        => 2500,
                'images'      => array(
                    array( 'url' => 'https://picsum.photos/800/600?random=1', 'caption' => 'Living Room' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=2', 'caption' => 'Master Bedroom' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=3', 'caption' => 'Kitchen' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=4', 'caption' => 'Ocean View' ),
                ),
                'amenities'   => array( 'WiFi', 'Air Conditioning', 'Beach Access', 'Pool', 'Hot Tub', 'Grill', 'Parking' ),
                'address'     => array(
                    'street'  => '123 Ocean Drive',
                    'city'    => 'Beach Town',
                    'state'   => 'FL',
                    'zip'     => '33139',
                    'country' => 'USA',
                ),
                'latitude'    => 25.7617,
                'longitude'   => -80.1918,
            ),
            array(
                'id'          => 2,
                'name'        => 'Mountain Retreat Cabin',
                'headline'    => 'Cozy cabin with breathtaking mountain views',
                'description' => 'Escape to this charming 3-bedroom cabin nestled in the mountains. Perfect for a peaceful getaway with hiking trails nearby and a cozy fireplace for chilly evenings.',
                'bedrooms'    => 3,
                'bathrooms'   => 2,
                'sleeps'      => 8,
                'sqft'        => 1800,
                'images'      => array(
                    array( 'url' => 'https://picsum.photos/800/600?random=5', 'caption' => 'Cabin Exterior' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=6', 'caption' => 'Living Area' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=7', 'caption' => 'Mountain View' ),
                ),
                'amenities'   => array( 'WiFi', 'Fireplace', 'Hot Tub', 'Hiking Trails', 'BBQ Grill', 'Mountain Views' ),
                'address'     => array(
                    'street'  => '456 Mountain Road',
                    'city'    => 'Highland',
                    'state'   => 'CO',
                    'zip'     => '80517',
                    'country' => 'USA',
                ),
                'latitude'    => 40.3428,
                'longitude'   => -105.6836,
            ),
            array(
                'id'          => 3,
                'name'        => 'Downtown Luxury Condo',
                'headline'    => 'Modern condo in the heart of downtown',
                'description' => 'Stay in style at this sleek 2-bedroom condo located in downtown. Walking distance to restaurants, shopping, and entertainment. Perfect for business or leisure travel.',
                'bedrooms'    => 2,
                'bathrooms'   => 2,
                'sleeps'      => 4,
                'sqft'        => 1200,
                'images'      => array(
                    array( 'url' => 'https://picsum.photos/800/600?random=8', 'caption' => 'Living Space' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=9', 'caption' => 'City View' ),
                ),
                'amenities'   => array( 'WiFi', 'Air Conditioning', 'Gym Access', 'Rooftop Pool', 'Parking', 'Concierge' ),
                'address'     => array(
                    'street'  => '789 Main Street',
                    'city'    => 'Metro City',
                    'state'   => 'NY',
                    'zip'     => '10001',
                    'country' => 'USA',
                ),
                'latitude'    => 40.7484,
                'longitude'   => -73.9857,
            ),
        );
    }

    /**
     * Get mock single property
     *
     * @param int $property_id Property ID.
     * @return array Mock property data.
     */
    private function get_mock_property( $property_id ) {
        $properties = $this->get_mock_properties();

        foreach ( $properties as $property ) {
            if ( $property['id'] === $property_id ) {
                // Add additional details for single property view
                $property['policies'] = array(
                    'check_in'     => '4:00 PM',
                    'check_out'    => '10:00 AM',
                    'cancellation' => 'Free cancellation up to 7 days before check-in. After that, the first night is non-refundable.',
                    'house_rules'  => array(
                        'No smoking',
                        'No pets allowed',
                        'No parties or events',
                        'Quiet hours: 10 PM - 8 AM',
                    ),
                );
                return $property;
            }
        }

        // Return first property if ID not found
        $properties[0]['policies'] = array(
            'check_in'     => '4:00 PM',
            'check_out'    => '10:00 AM',
            'cancellation' => 'Free cancellation up to 7 days before check-in.',
            'house_rules'  => array( 'No smoking', 'No pets' ),
        );
        return $properties[0];
    }

    /**
     * Get mock availability data
     *
     * @return array Mock availability calendar.
     */
    private function get_mock_availability() {
        $availability = array();
        $today = new DateTime();

        // Generate 90 days of availability
        for ( $i = 0; $i < 90; $i++ ) {
            $date = clone $today;
            $date->modify( "+{$i} days" );
            $date_str = $date->format( 'Y-m-d' );

            // Random availability (80% available)
            $availability[] = array(
                'date'      => $date_str,
                'available' => ( rand( 1, 10 ) > 2 ),
                'min_stay'  => ( $i % 7 === 0 || $i % 7 === 6 ) ? 3 : 2, // Weekend min stay 3, weekday 2
            );
        }

        return $availability;
    }

    /**
     * Get mock rates data
     *
     * @return array Mock pricing data.
     */
    private function get_mock_rates() {
        return array(
            'base_rate'      => 250,
            'currency'       => 'USD',
            'cleaning_fee'   => 150,
            'service_fee'    => 50,
            'seasonal_rates' => array(
                array(
                    'name'       => 'Peak Season',
                    'start_date' => date( 'Y-06-01' ),
                    'end_date'   => date( 'Y-08-31' ),
                    'rate'       => 350,
                ),
                array(
                    'name'       => 'Holiday Season',
                    'start_date' => date( 'Y-12-15' ),
                    'end_date'   => date( 'Y-01-05' ),
                    'rate'       => 400,
                ),
            ),
            'discounts'      => array(
                array(
                    'name'     => 'Weekly Discount',
                    'type'     => 'percentage',
                    'value'    => 10,
                    'min_stay' => 7,
                ),
                array(
                    'name'     => 'Monthly Discount',
                    'type'     => 'percentage',
                    'value'    => 20,
                    'min_stay' => 28,
                ),
            ),
        );
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
