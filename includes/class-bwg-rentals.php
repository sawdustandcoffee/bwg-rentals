<?php
/**
 * Main BWG Rentals Plugin Class
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class
 */
class BWG_Rentals {

    /**
     * Single instance of the class
     *
     * @var BWG_Rentals
     */
    private static $instance = null;

    /**
     * Admin instance
     *
     * @var BWG_Admin
     */
    public $admin;

    /**
     * API instance
     *
     * @var BWG_API
     */
    public $api;

    /**
     * Cache instance
     *
     * @var BWG_Cache
     */
    public $cache;

    /**
     * Shortcodes instance
     *
     * @var BWG_Shortcodes
     */
    public $shortcodes;

    /**
     * Get single instance of the class
     *
     * @return BWG_Rentals
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_components();
        $this->init_hooks();
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        // Initialize cache first as API depends on it
        $this->cache = new BWG_Cache();

        // Initialize API client
        $this->api = new BWG_API( $this->cache );

        // Initialize shortcodes
        $this->shortcodes = new BWG_Shortcodes( $this->api );

        // Initialize admin (loads class for decrypt_value method, but only initializes hooks in admin context)
        $this->admin = new BWG_Admin( $this->api, $this->cache );

        // Initialize updater
        $this->init_updater();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Admin notices
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Initialize the updater
     *
     * Uses Plugin Update Checker library to check for updates.
     * By default, checks GitHub releases. Can be overridden with a JSON metadata URL
     * by defining BWG_RENTALS_UPDATE_URL constant.
     */
    private function init_updater() {
        // Include Plugin Update Checker if available
        $updater_file = BWG_RENTALS_PLUGIN_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';

        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;

            if ( class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
                // Allow override of update URL for testing or custom update servers
                $update_url = defined( 'BWG_RENTALS_UPDATE_URL' )
                    ? BWG_RENTALS_UPDATE_URL
                    : 'https://github.com/sawdustandcoffee/bwg-rentals';

                $update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
                    $update_url,
                    BWG_RENTALS_PLUGIN_DIR . 'bwg-rentals.php',
                    'bwg-rentals'
                );

                // Only set branch for GitHub URLs
                if ( strpos( $update_url, 'github.com' ) !== false ) {
                    $update_checker->setBranch( 'main' );
                }
            }
        }
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only enqueue if shortcode is used on the page
        // Assets will be enqueued by individual shortcodes when rendered

        // Register styles (will be enqueued when needed)
        wp_register_style(
            'bwg-rentals-public',
            BWG_RENTALS_PLUGIN_URL . 'assets/css/bwg-rentals-public.css',
            array(),
            BWG_RENTALS_VERSION
        );

        // Register scripts
        wp_register_script(
            'bwg-rentals-public',
            BWG_RENTALS_PLUGIN_URL . 'assets/js/bwg-rentals-public.js',
            array( 'jquery' ),
            BWG_RENTALS_VERSION,
            true
        );

        // Register Google Maps API script if API key is configured
        $google_maps_api_key = BWG_Admin::decrypt_value( get_option( 'bwg_rentals_google_maps_api_key', '' ) );
        if ( ! empty( $google_maps_api_key ) ) {
            wp_register_script(
                'google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key=' . urlencode( $google_maps_api_key ) . '&callback=bwgInitGoogleMaps',
                array(),
                null,
                true
            );

            // Register Google Maps initialization script
            wp_register_script(
                'bwg-google-maps',
                BWG_RENTALS_PLUGIN_URL . 'assets/js/bwg-google-maps.js',
                array( 'jquery' ),
                BWG_RENTALS_VERSION,
                true
            );
        }
    }

    /**
     * Display admin notices
     */
    public function admin_notices() {
        // Check if API is configured
        $api_key = get_option( 'bwg_rentals_api_key' );
        $org_id  = get_option( 'bwg_rentals_organization_id' );

        if ( empty( $api_key ) || empty( $org_id ) ) {
            $settings_url = admin_url( 'options-general.php?page=bwg-rentals' );
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'BWG Rentals:', 'bwg-rentals' ); ?></strong>
                    <?php
                    printf(
                        /* translators: %s: settings page URL */
                        esc_html__( 'Please configure your API credentials in the %s to start displaying properties.', 'bwg-rentals' ),
                        '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'settings page', 'bwg-rentals' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Log errors to debug.log when WP_DEBUG_LOG is enabled
     *
     * @param string $message Error message to log.
     * @param string $level   Log level (error, warning, info).
     */
    public static function log( $message, $level = 'error' ) {
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            $prefix = '[BWG Rentals ' . strtoupper( $level ) . '] ';
            error_log( $prefix . $message );
        }
    }
}
