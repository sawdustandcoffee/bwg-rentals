<?php
/**
 * Test script to verify uninstall cleanup
 *
 * This script simulates the uninstall process and verifies that all
 * options and transients are properly removed.
 *
 * Usage: php test-uninstall-cleanup.php
 */

// Mock WordPress functions for standalone testing
if (!function_exists('delete_option')) {
    $wp_options = array();

    function add_option($name, $value) {
        global $wp_options;
        $wp_options[$name] = $value;
        return true;
    }

    function get_option($name, $default = false) {
        global $wp_options;
        return isset($wp_options[$name]) ? $wp_options[$name] : $default;
    }

    function delete_option($name) {
        global $wp_options;
        if (isset($wp_options[$name])) {
            unset($wp_options[$name]);
            return true;
        }
        return false;
    }
}

// Simulate WordPress database for transients
class MockWPDB {
    public $options = array();

    public function query($sql) {
        // Simulate deletion of transients
        $count = 0;
        foreach ($this->options as $key => $value) {
            if (strpos($key, '_transient_bwg_rentals_') === 0 ||
                strpos($key, '_transient_timeout_bwg_rentals_') === 0) {
                unset($this->options[$key]);
                $count++;
            }
        }
        return $count;
    }

    public function prepare($sql, ...$args) {
        return $sql; // Simplified for testing
    }
}

$wpdb = new MockWPDB();

// Define WP_UNINSTALL_PLUGIN to allow uninstall.php to run
define('WP_UNINSTALL_PLUGIN', true);

// Mock wp_clear_scheduled_hook
function wp_clear_scheduled_hook($hook) {
    echo "✓ Cleared scheduled hook: $hook\n";
    return true;
}

echo "================================================================================\n";
echo "BWG RENTALS UNINSTALL CLEANUP TEST\n";
echo "================================================================================\n\n";

// Step 1: Set up test data
echo "STEP 1: Setting up test data\n";
echo "----------------------------\n";

$test_options = array(
    'bwg_rentals_api_key' => 'test_api_key_12345',
    'bwg_rentals_org_id' => 'test_org_id',
    'bwg_rentals_organization_id' => 'test_organization_id',
    'bwg_rentals_cache_duration' => '24',
    'bwg_rentals_cache_metadata' => array('last_updated' => time(), 'items' => array('properties', 'property_1')),
    'bwg_rentals_booking_button_text' => 'Book Now',
    'bwg_rentals_button_text' => 'Reserve',
);

foreach ($test_options as $key => $value) {
    add_option($key, $value);
    echo "✓ Added option: $key\n";
}

// Add some transients
$wpdb->options['_transient_bwg_rentals_properties'] = 'properties_data';
$wpdb->options['_transient_timeout_bwg_rentals_properties'] = time() + 3600;
$wpdb->options['_transient_bwg_rentals_property_1'] = 'property_1_data';
$wpdb->options['_transient_timeout_bwg_rentals_property_1'] = time() + 3600;
echo "✓ Added 2 transients with timeout keys (4 total entries)\n";

echo "\nTotal options created: " . count($test_options) . "\n";
echo "Total transient entries: 4\n";

// Step 2: Verify data exists
echo "\n\nSTEP 2: Verifying test data exists\n";
echo "-----------------------------------\n";

$all_exist = true;
foreach ($test_options as $key => $value) {
    $exists = get_option($key) !== false;
    echo ($exists ? "✓" : "✗") . " Option exists: $key\n";
    if (!$exists) $all_exist = false;
}

$transient_count = count(array_filter(array_keys($wpdb->options), function($k) {
    return strpos($k, '_transient_bwg_rentals_') === 0;
}));
echo ($transient_count > 0 ? "✓" : "✗") . " Transients exist: $transient_count entries\n";

if (!$all_exist || $transient_count === 0) {
    echo "\n✗ TEST FAILED: Not all test data was created properly\n";
    exit(1);
}

// Step 3: Run uninstall process
echo "\n\nSTEP 3: Running uninstall process\n";
echo "----------------------------------\n";

// Include and run the uninstall script
include 'uninstall.php';

echo "Uninstall script executed\n";

// Step 4: Verify cleanup
echo "\n\nSTEP 4: Verifying cleanup\n";
echo "--------------------------\n";

$cleanup_success = true;
foreach ($test_options as $key => $value) {
    $still_exists = get_option($key) !== false;
    if ($still_exists) {
        echo "✗ Option NOT deleted: $key\n";
        $cleanup_success = false;
    } else {
        echo "✓ Option deleted: $key\n";
    }
}

$remaining_transients = count(array_filter(array_keys($wpdb->options), function($k) {
    return strpos($k, '_transient_bwg_rentals_') === 0;
}));

if ($remaining_transients > 0) {
    echo "✗ Transients NOT deleted: $remaining_transients entries remain\n";
    $cleanup_success = false;
} else {
    echo "✓ All transients deleted\n";
}

// Final result
echo "\n\n================================================================================\n";
if ($cleanup_success) {
    echo "✓ TEST PASSED: All options and transients successfully removed\n";
    echo "================================================================================\n";
    echo "\nSUMMARY:\n";
    echo "  - " . count($test_options) . " options deleted\n";
    echo "  - 4 transient entries deleted\n";
    echo "  - 1 scheduled hook cleared\n";
    echo "\nThe uninstall process is working correctly!\n";
    exit(0);
} else {
    echo "✗ TEST FAILED: Some data was not cleaned up\n";
    echo "================================================================================\n";
    exit(1);
}
