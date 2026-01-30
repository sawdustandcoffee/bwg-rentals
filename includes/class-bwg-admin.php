<?php
/**
 * BWG Rentals Admin Class
 *
 * Handles the admin settings page and related functionality.
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin settings class
 */
class BWG_Admin {

    /**
     * API instance
     *
     * @var BWG_API
     */
    private $api;

    /**
     * Cache instance
     *
     * @var BWG_Cache
     */
    private $cache;

    /**
     * Constructor
     *
     * @param BWG_API   $api   API instance.
     * @param BWG_Cache $cache Cache instance.
     */
    public function __construct( $api, $cache ) {
        $this->api   = $api;
        $this->cache = $cache;

        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add settings page
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

        // Register settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Enqueue admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // AJAX handlers
        add_action( 'wp_ajax_bwg_test_connection', array( $this, 'ajax_test_connection' ) );
        add_action( 'wp_ajax_bwg_clear_cache', array( $this, 'ajax_clear_cache' ) );
    }

    /**
     * Add settings page to WordPress admin
     */
    public function add_settings_page() {
        add_options_page(
            __( 'BWG Rentals Settings', 'bwg-rentals' ),
            __( 'BWG Rentals', 'bwg-rentals' ),
            'manage_options',
            'bwg-rentals',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        // API Key
        register_setting(
            'bwg_rentals_settings',
            'bwg_rentals_api_key',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_api_key' ),
            )
        );

        // Organization ID
        register_setting(
            'bwg_rentals_settings',
            'bwg_rentals_organization_id',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_organization_id' ),
            )
        );

        // Cache duration
        register_setting(
            'bwg_rentals_settings',
            'bwg_rentals_cache_duration',
            array(
                'type'              => 'integer',
                'sanitize_callback' => array( $this, 'sanitize_cache_duration' ),
                'default'           => 24,
            )
        );

        // Default button text
        register_setting(
            'bwg_rentals_settings',
            'bwg_rentals_button_text',
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_button_text' ),
                'default'           => __( 'Book Now', 'bwg-rentals' ),
            )
        );
    }

    /**
     * Sanitize and validate API key
     *
     * @param string $value API key value.
     * @return string Encrypted API key or existing value on error.
     */
    public function sanitize_api_key( $value ) {
        $value = sanitize_text_field( $value );

        // Allow empty value (user clearing the field)
        if ( empty( $value ) ) {
            return '';
        }

        // Check for minimum length (most API keys are at least 16 characters)
        if ( strlen( $value ) < 8 ) {
            add_settings_error(
                'bwg_rentals_api_key',
                'api_key_too_short',
                __( 'API Key appears to be too short. Please verify your API key.', 'bwg-rentals' ),
                'error'
            );
            // Return existing value to prevent saving invalid data
            return get_option( 'bwg_rentals_api_key' );
        }

        // Check for invalid characters (API keys typically only have alphanumeric, dash, underscore)
        if ( ! preg_match( '/^[a-zA-Z0-9_\-]+$/', $value ) ) {
            add_settings_error(
                'bwg_rentals_api_key',
                'api_key_invalid_chars',
                __( 'API Key contains invalid characters. Only letters, numbers, dashes, and underscores are allowed.', 'bwg-rentals' ),
                'error'
            );
            return get_option( 'bwg_rentals_api_key' );
        }

        return $this->encrypt_value( $value );
    }

    /**
     * Sanitize and validate Organization ID
     *
     * @param string $value Organization ID value.
     * @return string Encrypted Organization ID or existing value on error.
     */
    public function sanitize_organization_id( $value ) {
        $value = sanitize_text_field( $value );

        // Allow empty value
        if ( empty( $value ) ) {
            return '';
        }

        // Check for minimum length
        if ( strlen( $value ) < 2 ) {
            add_settings_error(
                'bwg_rentals_organization_id',
                'org_id_too_short',
                __( 'Organization ID appears to be too short. Please verify your Organization ID.', 'bwg-rentals' ),
                'error'
            );
            return get_option( 'bwg_rentals_organization_id' );
        }

        // Check for invalid characters
        if ( ! preg_match( '/^[a-zA-Z0-9_\-]+$/', $value ) ) {
            add_settings_error(
                'bwg_rentals_organization_id',
                'org_id_invalid_chars',
                __( 'Organization ID contains invalid characters. Only letters, numbers, dashes, and underscores are allowed.', 'bwg-rentals' ),
                'error'
            );
            return get_option( 'bwg_rentals_organization_id' );
        }

        return $this->encrypt_value( $value );
    }

    /**
     * Sanitize and validate cache duration
     *
     * @param mixed $value Cache duration value.
     * @return int Sanitized cache duration.
     */
    public function sanitize_cache_duration( $value ) {
        $value = absint( $value );

        // Check minimum value (at least 1 hour)
        if ( $value < 1 ) {
            add_settings_error(
                'bwg_rentals_cache_duration',
                'cache_duration_too_low',
                __( 'Cache duration must be at least 1 hour.', 'bwg-rentals' ),
                'error'
            );
            return 1; // Return minimum valid value
        }

        // Check maximum value (168 hours = 1 week)
        if ( $value > 168 ) {
            add_settings_error(
                'bwg_rentals_cache_duration',
                'cache_duration_too_high',
                __( 'Cache duration cannot exceed 168 hours (1 week).', 'bwg-rentals' ),
                'error'
            );
            return 168; // Return maximum valid value
        }

        return $value;
    }

    /**
     * Sanitize and validate button text
     *
     * @param string $value Button text value.
     * @return string Sanitized button text.
     */
    public function sanitize_button_text( $value ) {
        $value = sanitize_text_field( $value );

        // Set default if empty
        if ( empty( $value ) ) {
            return __( 'Book Now', 'bwg-rentals' );
        }

        // Check for maximum length
        if ( strlen( $value ) > 50 ) {
            add_settings_error(
                'bwg_rentals_button_text',
                'button_text_too_long',
                __( 'Button text is too long. Maximum 50 characters allowed.', 'bwg-rentals' ),
                'error'
            );
            return substr( $value, 0, 50 );
        }

        return $value;
    }

    /**
     * Encrypt sensitive values before storing
     *
     * @param string $value Value to encrypt.
     * @return string Encrypted value.
     */
    public function encrypt_value( $value ) {
        if ( empty( $value ) ) {
            return '';
        }

        // Use WordPress auth key for encryption
        $key = wp_salt( 'auth' );

        if ( function_exists( 'openssl_encrypt' ) ) {
            $iv     = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
            $encrypted = openssl_encrypt( $value, 'aes-256-cbc', $key, 0, $iv );
            return base64_encode( $iv . $encrypted );
        }

        // Fallback: base64 encode if openssl not available
        return base64_encode( $value );
    }

    /**
     * Decrypt stored values
     *
     * @param string $encrypted_value Encrypted value.
     * @return string Decrypted value.
     */
    public static function decrypt_value( $encrypted_value ) {
        if ( empty( $encrypted_value ) ) {
            return '';
        }

        $key = wp_salt( 'auth' );

        if ( function_exists( 'openssl_decrypt' ) ) {
            $data   = base64_decode( $encrypted_value );
            $iv_len = openssl_cipher_iv_length( 'aes-256-cbc' );
            $iv     = substr( $data, 0, $iv_len );
            $encrypted = substr( $data, $iv_len );

            $decrypted = openssl_decrypt( $encrypted, 'aes-256-cbc', $key, 0, $iv );

            return $decrypted !== false ? $decrypted : '';
        }

        // Fallback: base64 decode
        return base64_decode( $encrypted_value );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our settings page
        if ( 'settings_page_bwg-rentals' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'bwg-rentals-admin',
            BWG_RENTALS_PLUGIN_URL . 'assets/css/bwg-rentals-admin.css',
            array(),
            BWG_RENTALS_VERSION
        );

        wp_enqueue_script(
            'bwg-rentals-admin',
            BWG_RENTALS_PLUGIN_URL . 'assets/js/bwg-rentals-admin.js',
            array( 'jquery' ),
            BWG_RENTALS_VERSION,
            true
        );

        wp_localize_script(
            'bwg-rentals-admin',
            'bwgRentalsAdmin',
            array(
                'nonce'    => wp_create_nonce( 'bwg_rentals_admin' ),
                'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
                'strings'  => array(
                    'testing'              => __( 'Testing connection...', 'bwg-rentals' ),
                    'success'              => __( 'Connection successful!', 'bwg-rentals' ),
                    'error'                => __( 'Connection failed.', 'bwg-rentals' ),
                    'clearing'             => __( 'Clearing cache...', 'bwg-rentals' ),
                    'cacheCleared'         => __( 'Cache cleared successfully!', 'bwg-rentals' ),
                    // Validation messages
                    'apiKeyTooShort'       => __( 'API Key must be at least 8 characters.', 'bwg-rentals' ),
                    'apiKeyInvalidChars'   => __( 'API Key can only contain letters, numbers, dashes, and underscores.', 'bwg-rentals' ),
                    'orgIdTooShort'        => __( 'Organization ID must be at least 2 characters.', 'bwg-rentals' ),
                    'orgIdInvalidChars'    => __( 'Organization ID can only contain letters, numbers, dashes, and underscores.', 'bwg-rentals' ),
                    'cacheDurationRequired' => __( 'Cache duration is required.', 'bwg-rentals' ),
                    'cacheDurationTooLow'  => __( 'Cache duration must be at least 1 hour.', 'bwg-rentals' ),
                    'cacheDurationTooHigh' => __( 'Cache duration cannot exceed 168 hours.', 'bwg-rentals' ),
                    'buttonTextTooLong'    => __( 'Button text cannot exceed 50 characters.', 'bwg-rentals' ),
                ),
            )
        );
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Get current values (decrypted for display)
        $api_key    = self::decrypt_value( get_option( 'bwg_rentals_api_key' ) );
        $org_id     = self::decrypt_value( get_option( 'bwg_rentals_organization_id' ) );
        $cache_dur  = get_option( 'bwg_rentals_cache_duration', 24 );
        $btn_text   = get_option( 'bwg_rentals_button_text', __( 'Book Now', 'bwg-rentals' ) );

        // Get cache status
        $cache_status = $this->cache->get_status();

        include BWG_RENTALS_PLUGIN_DIR . 'templates/admin-settings.php';
    }

    /**
     * AJAX handler for testing API connection
     */
    public function ajax_test_connection() {
        // Verify nonce
        check_ajax_referer( 'bwg_rentals_admin', 'nonce' );

        // Verify capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bwg-rentals' ) ) );
        }

        // Test the connection
        $result = $this->api->test_connection();

        if ( $result['success'] ) {
            wp_send_json_success( array( 'message' => $result['message'] ) );
        } else {
            wp_send_json_error( array( 'message' => $result['message'] ) );
        }
    }

    /**
     * AJAX handler for clearing cache
     */
    public function ajax_clear_cache() {
        // Verify nonce
        check_ajax_referer( 'bwg_rentals_admin', 'nonce' );

        // Verify capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bwg-rentals' ) ) );
        }

        // Clear the cache
        $this->cache->clear_all();

        wp_send_json_success( array( 'message' => __( 'Cache cleared successfully.', 'bwg-rentals' ) ) );
    }
}
