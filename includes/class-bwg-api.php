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
     * Maximum number of retries for rate limiting
     */
    const MAX_RETRIES = 3;

    /**
     * Base delay in seconds for exponential backoff
     */
    const BASE_DELAY = 2;

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
     * Special mock modes:
     * - MOCK_EMPTY_*: Returns empty array (for testing empty states)
     * - MOCK_RATELIMIT_*: Simulates rate limiting (returns 429 first N times based on transient counter)
     * - MOCK_TIMEOUT_*: Simulates network timeout error (returns WP_Error)
     *
     * @param false|array|WP_Error $preempt      A preemptive return value of an HTTP request.
     * @param array                $parsed_args  HTTP request arguments.
     * @param string               $url          The request URL.
     * @return false|array|WP_Error Preemptive return value, WP_Error for simulated failures, or false to continue with request.
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

        // Handle rate limiting simulation
        if ( strpos( $credentials['api_key'], 'MOCK_RATELIMIT_' ) === 0 ) {
            return $this->maybe_mock_rate_limit_response( $url );
        }

        // Handle timeout simulation
        if ( strpos( $credentials['api_key'], 'MOCK_TIMEOUT_' ) === 0 ) {
            BWG_Rentals::log( 'Mock API: Simulating network timeout error.', 'debug' );
            return new WP_Error(
                'http_request_failed',
                'cURL error 28: Operation timed out after 30001 milliseconds with 0 bytes received'
            );
        }

        // Determine which endpoint is being called
        $mock_data = array();
        $status_code = 200;
        $status_message = 'OK';

        if ( strpos( $url, '/properties/' ) !== false && strpos( $url, '/availability' ) !== false ) {
            // Mock availability data
            $mock_data = $this->get_mock_availability();
        } elseif ( strpos( $url, '/properties/' ) !== false && strpos( $url, '/rates' ) !== false ) {
            // Mock rates data
            $mock_data = $this->get_mock_rates();
        } elseif ( preg_match( '/\/properties\/(\d+)$/', $url, $matches ) ) {
            // Mock single property data
            $mock_data = $this->get_mock_property( (int) $matches[1] );

            // Return 404 if property not found
            if ( null === $mock_data ) {
                $status_code = 404;
                $status_message = 'Not Found';
                $mock_data = array(
                    'error'   => 'not_found',
                    'message' => 'Property not found.',
                );
            }
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
                'code'    => $status_code,
                'message' => $status_message,
            ),
            'cookies'  => array(),
            'filename' => '',
        );
    }

    /**
     * Simulate rate limiting for testing
     *
     * Returns 429 response on first 2 requests, then 200 on subsequent requests.
     * Uses a transient counter to track request attempts.
     *
     * @param string $url Request URL.
     * @return array Mock HTTP response.
     */
    private function maybe_mock_rate_limit_response( $url ) {
        // Use transient to track how many times rate limit has been returned
        $transient_key = 'bwg_mock_ratelimit_counter';
        $counter       = (int) get_transient( $transient_key );

        // First 2 requests return 429 (rate limited)
        if ( $counter < 2 ) {
            set_transient( $transient_key, $counter + 1, 60 ); // Expires in 60 seconds

            BWG_Rentals::log(
                sprintf( 'Mock API: Simulating rate limit (attempt %d/2)', $counter + 1 ),
                'debug'
            );

            return array(
                'headers'  => array(
                    'content-type' => 'application/json',
                    'retry-after'  => '2', // Tell client to retry in 2 seconds
                ),
                'body'     => wp_json_encode(
                    array(
                        'error'   => 'rate_limit',
                        'message' => 'Too many requests. Please slow down.',
                    )
                ),
                'response' => array(
                    'code'    => 429,
                    'message' => 'Too Many Requests',
                ),
                'cookies'  => array(),
                'filename' => '',
            );
        }

        // After 2 rate limits, reset counter and return success
        delete_transient( $transient_key );

        BWG_Rentals::log( 'Mock API: Rate limit retry successful, returning data.', 'debug' );

        // Determine which endpoint and return appropriate data
        $mock_data = array();

        if ( strpos( $url, '/properties/' ) !== false && strpos( $url, '/availability' ) !== false ) {
            $mock_data = $this->get_mock_availability();
        } elseif ( strpos( $url, '/properties/' ) !== false && strpos( $url, '/rates' ) !== false ) {
            $mock_data = $this->get_mock_rates();
        } elseif ( preg_match( '/\/properties\/(\d+)$/', $url, $matches ) ) {
            $mock_data = $this->get_mock_property( (int) $matches[1] );
        } elseif ( strpos( $url, '/properties' ) !== false ) {
            $mock_data = $this->get_mock_properties();
        }

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
            'properties' => array(
                array(
                    'id'          => 1,
                    'name'        => 'Oceanfront Beach House',
                'headline'    => 'Stunning oceanfront property with private beach access',
                'description' => 'Experience luxury living in this beautiful 4-bedroom oceanfront beach house. Wake up to stunning ocean views, enjoy morning coffee on your private deck, and take a short walk to your own private beach.',
                'bedrooms'    => 4,
                'bathrooms'   => 3,
                'sleeps'      => 10,
                'sqft'        => 2500,
                'base_rate'   => 350,
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
                'base_rate'   => 225,
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
                'base_rate'   => 175,
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
            // Long Name Test Property - Tests that long property names are handled gracefully
            array(
                'id'          => 5,
                'name'        => 'The Absolutely Magnificent Oceanfront Beach House Estate with Private Pool, Spa, and Direct Beach Access - Perfect for Large Family Reunions and Special Celebrations',
                'headline'    => 'An extraordinarily long headline that tests how the UI handles very long text content across multiple lines',
                'description' => 'This property has a very long name to test that the UI handles long text gracefully without breaking the layout. The card should truncate or wrap the text appropriately.',
                'bedrooms'    => 6,
                'bathrooms'   => 5,
                'sleeps'      => 16,
                'sqft'        => 4500,
                'base_rate'   => 500,
                'images'      => array(
                    array( 'url' => 'https://picsum.photos/800/600?random=11', 'caption' => 'Main View' ),
                    array( 'url' => 'https://picsum.photos/800/600?random=12', 'caption' => 'Pool' ),
                ),
                'amenities'   => array( 'WiFi', 'Pool', 'Hot Tub', 'Beach Access', 'Game Room', 'Home Theater' ),
                'address'     => array(
                    'street'  => '999 Beachfront Boulevard',
                    'city'    => 'Paradise Beach',
                    'state'   => 'FL',
                    'zip'     => '33333',
                    'country' => 'USA',
                ),
                'latitude'    => 26.1224,
                'longitude'   => -80.1373,
            ),
            // XSS Test Property - Tests that HTML/script injection is properly escaped
            array(
                'id'          => 4,
                'name'        => '<script>alert("XSS")</script>Test Property',
                'headline'    => '<img src=x onerror="alert(\'XSS\')">Injected headline',
                'description' => 'This property has <b>HTML tags</b> and <script>document.write("XSS")</script> in its description. Also <a href="javascript:alert(\'click\')">click me</a>.',
                'bedrooms'    => 2,
                'bathrooms'   => 1,
                'sleeps'      => 4,
                'sqft'        => 1000,
                'base_rate'   => 125,
                'images'      => array(
                    array( 'url' => 'https://picsum.photos/800/600?random=10', 'caption' => '<script>alert("XSS")</script>' ),
                ),
                'amenities'   => array( 'WiFi', '<script>alert("amenity")</script>', '<img src=x onerror="alert()">' ),
                'address'     => array(
                    'street'  => '<script>alert("street")</script>123 Test St',
                    'city'    => '<b>Injected City</b>',
                    'state'   => 'CA',
                    'zip'     => '90210',
                    'country' => 'USA',
                ),
                'latitude'    => 34.0902,
                'longitude'   => -118.4065,
            ),
            ),
        );
    }

    /**
     * Get mock single property
     *
     * @param int $property_id Property ID.
     * @return array|null Mock property data or null if not found.
     */
    private function get_mock_property( $property_id ) {
        $properties_data = $this->get_mock_properties();
        $properties = isset( $properties_data['properties'] ) ? $properties_data['properties'] : array();

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

        // Return null if property ID not found (will trigger 404 response)
        return null;
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
            'org_id'  => BWG_Admin::decrypt_value( get_option( 'bwg_rentals_org_id' ) ),
        );
    }

    /**
     * Make an API request
     *
     * @param string $endpoint    API endpoint.
     * @param array  $args        Request arguments.
     * @param int    $retry_count Current retry attempt (for rate limiting).
     * @return array|WP_Error Response data or error.
     */
    private function request( $endpoint, $args = array(), $retry_count = 0 ) {
        $credentials = $this->get_credentials();

        if ( empty( $credentials['api_key'] ) || empty( $credentials['org_id'] ) ) {
            BWG_Rentals::log( 'Credentials check failed - api_key empty: ' . ( empty( $credentials['api_key'] ) ? 'yes' : 'no' ) . ', org_id empty: ' . ( empty( $credentials['org_id'] ) ? 'yes' : 'no' ) );
            return new WP_Error( 'no_credentials', __( 'API credentials not configured.', 'bwg-rentals' ) );
        }

        $url = self::API_BASE_URL . $credentials['org_id'] . '/' . $endpoint;
        BWG_Rentals::log( 'API Request URL: ' . $url );

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

        // Check for WP errors (network issues, etc.)
        if ( is_wp_error( $response ) ) {
            $error_code    = $response->get_error_code();
            $error_message = $response->get_error_message();

            BWG_Rentals::log( 'API request failed: ' . $error_message );

            // Handle timeout errors with user-friendly message
            if ( 'http_request_failed' === $error_code ) {
                // Check for various timeout patterns in the error message
                $timeout_patterns = array(
                    'cURL error 28',        // cURL timeout error
                    'Operation timed out',  // Generic timeout message
                    'Connection timed out', // Connection timeout
                    'timed out',            // Generic timeout
                    'timeout',              // Generic timeout
                );

                $is_timeout = false;
                foreach ( $timeout_patterns as $pattern ) {
                    if ( stripos( $error_message, $pattern ) !== false ) {
                        $is_timeout = true;
                        break;
                    }
                }

                if ( $is_timeout ) {
                    return new WP_Error(
                        'network_timeout',
                        __( 'Unable to connect to the property service. The server is taking too long to respond. Please try again later.', 'bwg-rentals' ),
                        array( 'original_error' => $error_message )
                    );
                }

                // Handle DNS/connection errors
                $connection_patterns = array(
                    'Could not resolve host',     // DNS error
                    'Failed to connect',          // Connection failed
                    'Connection refused',         // Server refused connection
                    'Network is unreachable',     // No network
                    'cURL error 6',               // Could not resolve host
                    'cURL error 7',               // Failed to connect
                );

                foreach ( $connection_patterns as $pattern ) {
                    if ( stripos( $error_message, $pattern ) !== false ) {
                        return new WP_Error(
                            'network_error',
                            __( 'Unable to connect to the property service. Please check your internet connection and try again.', 'bwg-rentals' ),
                            array( 'original_error' => $error_message )
                        );
                    }
                }

                // Generic network error fallback
                return new WP_Error(
                    'network_error',
                    __( 'Unable to connect to the property service. Please try again later.', 'bwg-rentals' ),
                    array( 'original_error' => $error_message )
                );
            }

            return $response;
        }

        $status_code = wp_remote_retrieve_response_code( $response );
        $body        = wp_remote_retrieve_body( $response );
        $headers     = wp_remote_retrieve_headers( $response );

        // Handle rate limiting with exponential backoff
        if ( 429 === $status_code ) {
            if ( $retry_count >= self::MAX_RETRIES ) {
                BWG_Rentals::log( 'API rate limit exceeded after ' . self::MAX_RETRIES . ' retries.', 'error' );
                return new WP_Error(
                    'rate_limit_exceeded',
                    __( 'The API is currently busy. Please try again in a few minutes.', 'bwg-rentals' ),
                    array( 'status' => 429 )
                );
            }

            // Calculate delay: check Retry-After header, or use exponential backoff
            $delay = self::BASE_DELAY;
            if ( isset( $headers['retry-after'] ) ) {
                $retry_after = intval( $headers['retry-after'] );
                if ( $retry_after > 0 && $retry_after <= 60 ) {
                    $delay = $retry_after;
                }
            } else {
                // Exponential backoff: 2, 4, 8 seconds
                $delay = self::BASE_DELAY * pow( 2, $retry_count );
            }

            BWG_Rentals::log(
                sprintf(
                    'API rate limited. Retry %d/%d in %d seconds.',
                    $retry_count + 1,
                    self::MAX_RETRIES,
                    $delay
                ),
                'warning'
            );

            sleep( $delay );
            return $this->request( $endpoint, $args, $retry_count + 1 );
        }

        // Handle errors
        if ( $status_code >= 400 ) {
            // Provide user-friendly error messages for common status codes
            switch ( $status_code ) {
                case 404:
                    $error_message = __( 'Property not found. Please check the property ID.', 'bwg-rentals' );
                    $error_code = 'property_not_found';
                    break;
                case 401:
                case 403:
                    $error_message = __( 'API authentication failed. Please check your API credentials.', 'bwg-rentals' );
                    $error_code = 'auth_error';
                    break;
                case 500:
                case 502:
                case 503:
                    $error_message = __( 'The property service is temporarily unavailable. Please try again later.', 'bwg-rentals' );
                    $error_code = 'server_error';
                    break;
                default:
                    $error_message = sprintf(
                        /* translators: %d: HTTP status code */
                        __( 'API error: HTTP %d', 'bwg-rentals' ),
                        $status_code
                    );
                    $error_code = 'api_error';
            }
            BWG_Rentals::log( sprintf( 'API error: HTTP %d - %s', $status_code, $error_message ) );
            return new WP_Error( $error_code, $error_message, array( 'status' => $status_code ) );
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

        if ( is_wp_error( $data ) ) {
            return $data;
        }

        // Extract properties array from response
        $properties = isset( $data['properties'] ) ? $data['properties'] : array();

        // Normalize images for each property
        $properties = array_map( array( $this, 'normalize_property_images' ), $properties );

        $this->cache->set( $cache_key, $properties );

        return $properties;
    }

    /**
     * Normalize property images from API response
     *
     * Transforms images from the Direct API format into a unified 'images' array
     * for consistent template usage.
     *
     * Handles multiple API formats:
     * - featured_image: { image: { large: { url }, medium: { url }, small: { url } } }
     * - images (Direct API): [ { uri, label }, ... ]
     * - property_images (legacy): [ { image: { large: { url }, ... }, caption }, ... ]
     *
     * Also detects and preserves already-normalized data (mock data with 'url' keys).
     *
     * @param array $property Property data from API.
     * @return array Property data with normalized images array.
     */
    private function normalize_property_images( $property ) {
        // Check if images array exists and is already normalized (has 'url' key in first item)
        // Mock data and already-normalized data will have 'url' keys
        // Raw Direct API data will have 'uri' keys instead
        if ( isset( $property['images'] ) && ! empty( $property['images'] ) ) {
            $first_image = reset( $property['images'] );
            if ( isset( $first_image['url'] ) ) {
                // Already normalized (mock data or previously processed)
                return $property;
            }
        }

        $images = array();

        // Extract featured_image as the first/primary image
        if ( isset( $property['featured_image']['image']['large']['url'] ) ) {
            $images[] = array(
                'url'    => $property['featured_image']['image']['large']['url'],
                'medium' => $property['featured_image']['image']['medium']['url'] ?? '',
                'small'  => $property['featured_image']['image']['small']['url'] ?? '',
                'alt'    => $property['name'] ?? '',
            );
        }

        // Extract additional images from Direct API 'images' array (uri format)
        // Direct API returns: images[].uri and images[].label
        if ( isset( $property['images'] ) && is_array( $property['images'] ) ) {
            foreach ( $property['images'] as $api_image ) {
                // Skip if no uri available
                if ( ! isset( $api_image['uri'] ) ) {
                    continue;
                }

                $images[] = array(
                    'url'     => $api_image['uri'],
                    'medium'  => $api_image['uri'], // Direct API only provides one size
                    'small'   => $api_image['uri'],
                    'alt'     => $api_image['label'] ?? ( $property['name'] ?? '' ),
                    'caption' => $api_image['label'] ?? '',
                );
            }
        }

        // Fallback: Extract additional images from property_images array (legacy format)
        if ( isset( $property['property_images'] ) && is_array( $property['property_images'] ) ) {
            foreach ( $property['property_images'] as $prop_image ) {
                // Skip if no large URL available
                if ( ! isset( $prop_image['image']['large']['url'] ) ) {
                    continue;
                }

                $images[] = array(
                    'url'     => $prop_image['image']['large']['url'],
                    'medium'  => $prop_image['image']['medium']['url'] ?? '',
                    'small'   => $prop_image['image']['small']['url'] ?? '',
                    'alt'     => $prop_image['caption'] ?? ( $property['name'] ?? '' ),
                    'caption' => $prop_image['caption'] ?? '',
                );
            }
        }

        // Set the normalized images array
        $property['images'] = $images;

        return $property;
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
            // Normalize images from API format to unified array
            $data = $this->normalize_property_images( $data );
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
     * Generate a URL-safe slug from a property name
     *
     * @param string $name Property name.
     * @return string URL-safe slug.
     */
    private function generate_slug( $name ) {
        // Convert to lowercase
        $slug = strtolower( $name );

        // Replace special characters with spaces first
        $slug = preg_replace( '/[^\w\s-]/', ' ', $slug );

        // Replace whitespace with dashes
        $slug = preg_replace( '/[\s_]+/', '-', $slug );

        // Remove consecutive dashes
        $slug = preg_replace( '/-+/', '-', $slug );

        // Trim dashes from ends
        $slug = trim( $slug, '-' );

        return $slug;
    }

    /**
     * Get the booking URL for a property
     *
     * Generates a booking URL using the configured base URL (if set) or falls back
     * to the default Direct Software URL format.
     *
     * @param int|array $property Property ID or property data array.
     * @return string Booking URL.
     */
    public function get_booking_url( $property ) {
        $credentials = $this->get_credentials();

        // Handle both property ID (int) and property data (array)
        if ( is_array( $property ) ) {
            $property_id = isset( $property['id'] ) ? absint( $property['id'] ) : 0;
            $property_name = isset( $property['name'] ) ? $property['name'] : '';
        } else {
            $property_id = absint( $property );
            $property_name = '';

            // Fetch property data to get name for slug generation
            $property_data = $this->get_property( $property_id );
            if ( ! is_wp_error( $property_data ) && isset( $property_data['name'] ) ) {
                $property_name = $property_data['name'];
            }
        }

        // Check for custom booking base URL
        $booking_base_url = get_option( 'bwg_rentals_booking_base_url', '' );

        if ( ! empty( $booking_base_url ) && ! empty( $property_name ) ) {
            // Use custom URL format with property slug
            $slug = $this->generate_slug( $property_name );
            return $booking_base_url . '/listings/' . $slug;
        }

        // Fallback to default Direct Software URL format
        return 'https://app.getdirect.io/listings/' . $credentials['org_id'] . '/' . $property_id;
    }
}
