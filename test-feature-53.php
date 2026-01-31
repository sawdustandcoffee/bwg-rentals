<?php
/**
 * Test Feature #53: Cache respects duration setting
 *
 * Test Steps:
 * 1. Set cache duration
 * 2. Verify transient expiration matches setting
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

echo "=== Feature #53: Cache respects duration setting ===\n\n";

// Get cache instance
$cache = new BWG_Cache();

// Test 1: Set cache duration to 2 hours and verify
echo "TEST 1: Set cache duration to 2 hours\n";
echo "--------------------------------------\n";

$test_duration = 2; // hours
update_option('bwg_rentals_cache_duration', $test_duration);
echo "✓ Set bwg_rentals_cache_duration to {$test_duration} hours\n";

// Set a cache entry
$test_key = 'test_duration_' . time();
$test_value = 'Test data for duration verification';
$cache->set($test_key, $test_value, 'default');
echo "✓ Created cache entry with key: {$test_key}\n";

// Check the transient timeout in the database
global $wpdb;
$transient_timeout_key = '_transient_timeout_bwg_rentals_' . $test_key;
$timeout = $wpdb->get_var($wpdb->prepare(
    "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
    $transient_timeout_key
));

if ($timeout) {
    $current_time = time();
    $expected_timeout = $current_time + ($test_duration * HOUR_IN_SECONDS);
    $actual_duration_seconds = $timeout - $current_time;
    $actual_duration_hours = $actual_duration_seconds / HOUR_IN_SECONDS;

    echo "✓ Transient timeout found: {$timeout}\n";
    echo "  Current time: {$current_time}\n";
    echo "  Expected timeout: {$expected_timeout}\n";
    echo "  Actual duration: " . round($actual_duration_hours, 2) . " hours\n";

    // Allow for a small timing difference (within 10 seconds)
    $diff = abs($timeout - $expected_timeout);
    if ($diff <= 10) {
        echo "✅ PASS: Transient expiration matches setting (within {$diff} seconds)\n";
    } else {
        echo "❌ FAIL: Transient expiration differs by {$diff} seconds\n";
    }
} else {
    echo "❌ FAIL: No timeout found for transient\n";
}

echo "\n";

// Test 2: Change duration to 12 hours and verify
echo "TEST 2: Change cache duration to 12 hours\n";
echo "-------------------------------------------\n";

$test_duration2 = 12; // hours
update_option('bwg_rentals_cache_duration', $test_duration2);
echo "✓ Set bwg_rentals_cache_duration to {$test_duration2} hours\n";

// Set another cache entry
$test_key2 = 'test_duration_' . time() . '_2';
$test_value2 = 'Test data for 12 hour duration';
$cache->set($test_key2, $test_value2, 'default');
echo "✓ Created cache entry with key: {$test_key2}\n";

// Check the transient timeout
$transient_timeout_key2 = '_transient_timeout_bwg_rentals_' . $test_key2;
$timeout2 = $wpdb->get_var($wpdb->prepare(
    "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
    $transient_timeout_key2
));

if ($timeout2) {
    $current_time2 = time();
    $expected_timeout2 = $current_time2 + ($test_duration2 * HOUR_IN_SECONDS);
    $actual_duration_seconds2 = $timeout2 - $current_time2;
    $actual_duration_hours2 = $actual_duration_seconds2 / HOUR_IN_SECONDS;

    echo "✓ Transient timeout found: {$timeout2}\n";
    echo "  Current time: {$current_time2}\n";
    echo "  Expected timeout: {$expected_timeout2}\n";
    echo "  Actual duration: " . round($actual_duration_hours2, 2) . " hours\n";

    $diff2 = abs($timeout2 - $expected_timeout2);
    if ($diff2 <= 10) {
        echo "✅ PASS: Transient expiration matches setting (within {$diff2} seconds)\n";
    } else {
        echo "❌ FAIL: Transient expiration differs by {$diff2} seconds\n";
    }
} else {
    echo "❌ FAIL: No timeout found for transient\n";
}

echo "\n";

// Test 3: Verify availability cache uses separate duration (15 minutes)
echo "TEST 3: Verify availability cache uses 15-minute duration\n";
echo "-----------------------------------------------------------\n";

$test_key3 = 'test_availability_' . time();
$test_value3 = 'Test availability data';
$cache->set($test_key3, $test_value3, 'availability');
echo "✓ Created availability cache entry with key: {$test_key3}\n";

$transient_timeout_key3 = '_transient_timeout_bwg_rentals_' . $test_key3;
$timeout3 = $wpdb->get_var($wpdb->prepare(
    "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
    $transient_timeout_key3
));

if ($timeout3) {
    $current_time3 = time();
    $expected_timeout3 = $current_time3 + 900; // 15 minutes = 900 seconds
    $actual_duration_seconds3 = $timeout3 - $current_time3;
    $actual_duration_minutes3 = $actual_duration_seconds3 / 60;

    echo "✓ Transient timeout found: {$timeout3}\n";
    echo "  Current time: {$current_time3}\n";
    echo "  Expected timeout: {$expected_timeout3}\n";
    echo "  Actual duration: " . round($actual_duration_minutes3, 2) . " minutes\n";

    $diff3 = abs($timeout3 - $expected_timeout3);
    if ($diff3 <= 10) {
        echo "✅ PASS: Availability cache uses 15-minute duration (within {$diff3} seconds)\n";
    } else {
        echo "❌ FAIL: Availability cache duration differs by {$diff3} seconds\n";
    }
} else {
    echo "❌ FAIL: No timeout found for availability transient\n";
}

echo "\n";

// Cleanup
echo "CLEANUP\n";
echo "-------\n";
$cache->delete($test_key);
$cache->delete($test_key2);
$cache->delete($test_key3);
echo "✓ Deleted test cache entries\n";

// Restore default
update_option('bwg_rentals_cache_duration', 24);
echo "✓ Restored cache duration to 24 hours\n";

echo "\n=== Test Complete ===\n";
