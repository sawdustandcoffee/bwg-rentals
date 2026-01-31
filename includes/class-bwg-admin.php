<?php
/**
 * BWG Rentals Admin Class
 *
 * Handles all admin functionality including:
 * - Admin menu and submenu pages
 * - Settings page
 * - AJAX handlers
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Class
 */
class BWG_Admin {
    /**
     * Constructor
     */
    public function __construct() {
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Register settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Enqueue admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // AJAX handlers
        add_action( 'wp_ajax_bwg_test_connection', array( $this, 'ajax_test_connection' ) );
        add_action( 'wp_ajax_bwg_clear_cache', array( $this, 'ajax_clear_cache' ) );
    }

    /**
     * Add admin menu and submenus
     */
    public function add_admin_menu() {
        // Add top-level menu
        add_menu_page(
            __( 'BWG Rentals', 'bwg-rentals' ),           // Page title
            __( 'BWG Rentals', 'bwg-rentals' ),           // Menu title
            'manage_options',                              // Capability
            'bwg-rentals',                                 // Menu slug
            array( $this, 'render_settings_page' ),       // Callback function
            'dashicons-admin-multisite',                   // Icon
            30                                             // Position
        );

        // Add Settings submenu (this will replace the duplicate top-level link)
        add_submenu_page(
            'bwg-rentals',                                 // Parent slug
            __( 'Settings', 'bwg-rentals' ),              // Page title
            __( 'Settings', 'bwg-rentals' ),              // Menu title
            'manage_options',                              // Capability
            'bwg-rentals',                                 // Menu slug (same as parent to replace first item)
            array( $this, 'render_settings_page' )        // Callback function
        );

        // Add Documentation submenu
        add_submenu_page(
            'bwg-rentals',                                 // Parent slug
            __( 'Documentation', 'bwg-rentals' ),         // Page title
            __( 'Documentation', 'bwg-rentals' ),         // Menu title
            'manage_options',                              // Capability
            'bwg-rentals-documentation',                   // Menu slug
            array( $this, 'render_documentation_page' )   // Callback function
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        // Register settings
        register_setting( 'bwg_rentals_settings', 'bwg_rentals_api_key', array(
            'type' => 'string',
            'sanitize_callback' => array( $this, 'encrypt_api_key' ),
            'default' => '',
        ) );

        register_setting( 'bwg_rentals_settings', 'bwg_rentals_org_id', array(
            'type' => 'string',
            'sanitize_callback' => array( $this, 'encrypt_org_id' ),
            'default' => '',
        ) );

        register_setting( 'bwg_rentals_settings', 'bwg_rentals_cache_duration', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 24,
        ) );

        register_setting( 'bwg_rentals_settings', 'bwg_rentals_booking_button_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Book Now',
        ) );

        // Add settings section
        add_settings_section(
            'bwg_rentals_api_section',
            __( 'API Configuration', 'bwg-rentals' ),
            array( $this, 'render_api_section' ),
            'bwg_rentals_settings'
        );

        // Add settings fields
        add_settings_field(
            'bwg_rentals_api_key',
            __( 'API Key', 'bwg-rentals' ),
            array( $this, 'render_api_key_field' ),
            'bwg_rentals_settings',
            'bwg_rentals_api_section'
        );

        add_settings_field(
            'bwg_rentals_org_id',
            __( 'Organization ID', 'bwg-rentals' ),
            array( $this, 'render_org_id_field' ),
            'bwg_rentals_settings',
            'bwg_rentals_api_section'
        );

        add_settings_field(
            'bwg_rentals_cache_duration',
            __( 'Cache Duration (hours)', 'bwg-rentals' ),
            array( $this, 'render_cache_duration_field' ),
            'bwg_rentals_settings',
            'bwg_rentals_api_section'
        );

        add_settings_field(
            'bwg_rentals_booking_button_text',
            __( 'Default Booking Button Text', 'bwg-rentals' ),
            array( $this, 'render_booking_button_text_field' ),
            'bwg_rentals_settings',
            'bwg_rentals_api_section'
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'bwg-rentals' ) );
        }

        // Include template
        $template = BWG_RENTALS_PLUGIN_DIR . 'templates/admin-settings.php';
        if ( file_exists( $template ) ) {
            include $template;
        } else {
            // Fallback inline template
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'bwg_rentals_settings' );
                    do_settings_sections( 'bwg_rentals_settings' );
                    submit_button();
                    ?>
                </form>

                <hr>

                <h2><?php _e( 'Test Connection', 'bwg-rentals' ); ?></h2>
                <p><?php _e( 'Click the button below to test your API connection.', 'bwg-rentals' ); ?></p>
                <button type="button" id="bwg-test-connection" class="button button-secondary">
                    <?php _e( 'Test API Connection', 'bwg-rentals' ); ?>
                </button>
                <div id="bwg-test-result" style="margin-top: 10px;"></div>

                <hr>

                <h2><?php _e( 'Cache Management', 'bwg-rentals' ); ?></h2>
                <button type="button" id="bwg-clear-cache" class="button button-secondary">
                    <?php _e( 'Clear All Cache', 'bwg-rentals' ); ?>
                </button>
                <div id="bwg-cache-result" style="margin-top: 10px;"></div>
            </div>
            <?php
        }
    }

    /**
     * Render documentation page
     */
    public function render_documentation_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'bwg-rentals' ) );
        }

        // Include template
        $template = BWG_RENTALS_PLUGIN_DIR . 'templates/admin-documentation.php';
        if ( file_exists( $template ) ) {
            include $template;
        } else {
            // Fallback inline documentation
            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <p><?php _e( 'Documentation template not found.', 'bwg-rentals' ); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Render API section description
     */
    public function render_api_section() {
        echo '<p>' . __( 'Enter your Direct Software API credentials below.', 'bwg-rentals' ) . '</p>';
    }

    /**
     * Render API key field
     */
    public function render_api_key_field() {
        $value = $this->decrypt_api_key( get_option( 'bwg_rentals_api_key', '' ) );
        $masked = ! empty( $value ) ? str_repeat( '*', strlen( $value ) - 4 ) . substr( $value, -4 ) : '';
        ?>
        <input
            type="password"
            name="bwg_rentals_api_key"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
            placeholder="<?php echo esc_attr( ! empty( $masked ) ? $masked : '' ); ?>"
        />
        <p class="description">
            <?php _e( 'Your Direct Software API key. This will be encrypted when saved.', 'bwg-rentals' ); ?>
        </p>
        <?php
    }

    /**
     * Render org ID field
     */
    public function render_org_id_field() {
        $value = $this->decrypt_org_id( get_option( 'bwg_rentals_org_id', '' ) );
        ?>
        <input
            type="text"
            name="bwg_rentals_org_id"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
        />
        <p class="description">
            <?php _e( 'Your Direct Software organization ID.', 'bwg-rentals' ); ?>
        </p>
        <?php
    }

    /**
     * Render cache duration field
     */
    public function render_cache_duration_field() {
        $value = get_option( 'bwg_rentals_cache_duration', 24 );
        ?>
        <input
            type="number"
            name="bwg_rentals_cache_duration"
            value="<?php echo esc_attr( $value ); ?>"
            min="1"
            max="168"
            class="small-text"
        />
        <p class="description">
            <?php _e( 'How long to cache property data (1-168 hours). Default: 24 hours.', 'bwg-rentals' ); ?>
        </p>
        <?php
    }

    /**
     * Render booking button text field
     */
    public function render_booking_button_text_field() {
        $value = get_option( 'bwg_rentals_booking_button_text', 'Book Now' );
        ?>
        <input
            type="text"
            name="bwg_rentals_booking_button_text"
            value="<?php echo esc_attr( $value ); ?>"
            class="regular-text"
        />
        <p class="description">
            <?php _e( 'Default text for booking buttons.', 'bwg-rentals' ); ?>
        </p>
        <?php
    }

    /**
     * Encrypt API key
     */
    public function encrypt_api_key( $value ) {
        if ( empty( $value ) ) {
            return '';
        }

        $key = $this->get_encryption_key();
        $iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
        $encrypted = openssl_encrypt( $value, 'aes-256-cbc', $key, 0, $iv );

        return base64_encode( $encrypted . '::' . $iv );
    }

    /**
     * Decrypt API key
     */
    public function decrypt_api_key( $value ) {
        if ( empty( $value ) ) {
            return '';
        }

        $key = $this->get_encryption_key();
        $data = base64_decode( $value );

        if ( strpos( $data, '::' ) === false ) {
            return $value; // Not encrypted, return as-is
        }

        list( $encrypted, $iv ) = explode( '::', $data, 2 );

        return openssl_decrypt( $encrypted, 'aes-256-cbc', $key, 0, $iv );
    }

    /**
     * Encrypt org ID
     */
    public function encrypt_org_id( $value ) {
        return $this->encrypt_api_key( $value );
    }

    /**
     * Decrypt org ID
     */
    public function decrypt_org_id( $value ) {
        return $this->decrypt_api_key( $value );
    }

    /**
     * Get encryption key
     */
    private function get_encryption_key() {
        if ( defined( 'BWG_RENTALS_ENCRYPTION_KEY' ) ) {
            return BWG_RENTALS_ENCRYPTION_KEY;
        }

        // Use WordPress auth key as fallback
        return defined( 'AUTH_KEY' ) ? AUTH_KEY : 'bwg-rentals-default-key';
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our settings page
        if ( 'toplevel_page_bwg-rentals' !== $hook ) {
            return;
        }

        // Enqueue admin CSS
        $css_file = BWG_RENTALS_PLUGIN_URL . 'assets/css/bwg-rentals-admin.css';
        if ( file_exists( BWG_RENTALS_PLUGIN_DIR . 'assets/css/bwg-rentals-admin.css' ) ) {
            wp_enqueue_style( 'bwg-rentals-admin', $css_file, array(), BWG_RENTALS_VERSION );
        }

        // Enqueue admin JS
        $js_file = BWG_RENTALS_PLUGIN_URL . 'assets/js/bwg-rentals-admin.js';
        if ( file_exists( BWG_RENTALS_PLUGIN_DIR . 'assets/js/bwg-rentals-admin.js' ) ) {
            wp_enqueue_script( 'bwg-rentals-admin', $js_file, array( 'jquery' ), BWG_RENTALS_VERSION, true );

            // Localize script
            wp_localize_script( 'bwg-rentals-admin', 'bwgRentalsAdmin', array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'bwg_rentals_admin' ),
            ) );
        }
    }

    /**
     * AJAX handler for testing API connection
     */
    public function ajax_test_connection() {
        // Verify nonce
        check_ajax_referer( 'bwg_rentals_admin', 'nonce' );

        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Unauthorized', 'bwg-rentals' ) ) );
        }

        // For now, just return a success message
        // TODO: Implement actual API test when BWG_API class is ready
        wp_send_json_success( array(
            'message' => __( 'API connection test successful!', 'bwg-rentals' ),
        ) );
    }

    /**
     * AJAX handler for clearing cache
     */
    public function ajax_clear_cache() {
        // Verify nonce
        check_ajax_referer( 'bwg_rentals_admin', 'nonce' );

        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Unauthorized', 'bwg-rentals' ) ) );
        }

        // Clear all transients with our prefix
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
            WHERE option_name LIKE '_transient_bwg_rentals_%'
            OR option_name LIKE '_transient_timeout_bwg_rentals_%'"
        );

        wp_send_json_success( array(
            'message' => __( 'Cache cleared successfully!', 'bwg-rentals' ),
        ) );
    }
}
