<?php
/**
 * Plugin Name: BWG Rentals
 * Plugin URI: https://github.com/sawdustandcoffee/bwg-rentals
 * Description: Display vacation rental properties from Direct Software on your WordPress site with flexible shortcodes.
 * Version: 1.0.15
 * Author: Sawdust and Coffee
 * Author URI: https://sawdustandcoffee.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bwg-rentals
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin version
define( 'BWG_RENTALS_VERSION', '1.0.15' );

// Plugin directory path
define( 'BWG_RENTALS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin directory URL
define( 'BWG_RENTALS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Plugin basename
define( 'BWG_RENTALS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoload plugin classes
 */
spl_autoload_register( function ( $class ) {
    // Only autoload our classes
    if ( strpos( $class, 'BWG_' ) !== 0 ) {
        return;
    }

    // Convert class name to file name
    $class_file = strtolower( str_replace( '_', '-', $class ) );
    $class_file = 'class-' . $class_file . '.php';

    // Check includes directory
    $file_path = BWG_RENTALS_PLUGIN_DIR . 'includes/' . $class_file;
    if ( file_exists( $file_path ) ) {
        require_once $file_path;
        return;
    }

    // Check shortcodes directory
    $file_path = BWG_RENTALS_PLUGIN_DIR . 'shortcodes/' . $class_file;
    if ( file_exists( $file_path ) ) {
        require_once $file_path;
        return;
    }
});

/**
 * Initialize the plugin
 */
function bwg_rentals_init() {
    // Load text domain for translations
    load_plugin_textdomain(
        'bwg-rentals',
        false,
        dirname( BWG_RENTALS_PLUGIN_BASENAME ) . '/languages'
    );

    // Initialize main plugin class
    if ( class_exists( 'BWG_Rentals' ) ) {
        BWG_Rentals::get_instance();
    }
}
add_action( 'plugins_loaded', 'bwg_rentals_init' );

/**
 * Plugin activation hook
 */
function bwg_rentals_activate() {
    // Set default options
    $defaults = array(
        'api_key'          => '',
        'organization_id'  => '',
        'cache_duration'   => 24, // hours
        'button_text'      => __( 'Book Now', 'bwg-rentals' ),
    );

    foreach ( $defaults as $key => $value ) {
        if ( get_option( 'bwg_rentals_' . $key ) === false ) {
            add_option( 'bwg_rentals_' . $key, $value );
        }
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'bwg_rentals_activate' );

/**
 * Plugin deactivation hook
 */
function bwg_rentals_deactivate() {
    // Clear any scheduled events
    wp_clear_scheduled_hook( 'bwg_rentals_cache_refresh' );

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'bwg_rentals_deactivate' );

/**
 * Get booking URL for a property
 *
 * Global helper function that templates can use to generate booking URLs.
 *
 * @param array|int $property Property data array or property ID.
 * @return string Booking URL.
 */
function bwg_get_booking_url( $property ) {
    $cache = new BWG_Cache();
    $api = new BWG_API( $cache );
    return $api->get_booking_url( $property );
}
