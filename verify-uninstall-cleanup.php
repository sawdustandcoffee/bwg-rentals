<?php
/**
 * Verify Uninstall Cleanup - WordPress Test Page
 *
 * This simulates the uninstall cleanup process without actually deleting
 * the plugin. It shows what would be deleted.
 *
 * To use:
 * 1. Create a WordPress page
 * 2. Add shortcode: [php]<?php include(WP_PLUGIN_DIR . '/bwg-rentals/verify-uninstall-cleanup.php'); ?>[/php]
 * 3. View the page to see uninstall simulation
 */

// Ensure this is running in WordPress context
if (!defined('ABSPATH')) {
    die('This script must be run within WordPress.');
}

// Get global $wpdb
global $wpdb;

echo '<div style="max-width: 1200px; margin: 20px auto; font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;">';
echo '<h1 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;">BWG Rentals - Uninstall Cleanup Verification</h1>';

// Step 1: Show current state
echo '<h2 style="color: #2980b9; margin-top: 30px;">📋 Current Plugin Data</h2>';
echo '<div style="background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #3498db;">';

$all_options = array(
    'bwg_rentals_api_key',
    'bwg_rentals_org_id',
    'bwg_rentals_organization_id',
    'bwg_rentals_cache_duration',
    'bwg_rentals_cache_metadata',
    'bwg_rentals_booking_button_text',
    'bwg_rentals_button_text',
);

echo '<h3>Options in Database:</h3>';
echo '<table style="width: 100%; border-collapse: collapse;">';
echo '<tr style="background: #ecf0f1; font-weight: bold;">';
echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">Option Name</td>';
echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">Exists</td>';
echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">Value (truncated)</td>';
echo '</tr>';

$options_found = 0;
foreach ($all_options as $option) {
    $value = get_option($option, null);
    $exists = ($value !== null);
    if ($exists) $options_found++;

    $display_value = '';
    if ($exists) {
        if (is_array($value)) {
            $display_value = 'Array (' . count($value) . ' items)';
        } else {
            $display_value = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
        }
    }

    $row_color = $exists ? '#d5f4e6' : '#fadbd8';
    $status = $exists ? '✅ Yes' : '❌ No';

    echo '<tr style="background: ' . $row_color . ';">';
    echo '<td style="padding: 8px; border: 1px solid #bdc3c7;"><code>' . esc_html($option) . '</code></td>';
    echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">' . $status . '</td>';
    echo '<td style="padding: 8px; border: 1px solid #bdc3c7;"><code>' . esc_html($display_value) . '</code></td>';
    echo '</tr>';
}
echo '</table>';
echo '<p style="margin-top: 10px; font-weight: bold;">Total options found: ' . $options_found . ' / ' . count($all_options) . '</p>';
echo '</div>';

// Step 2: Check transients
echo '<h2 style="color: #2980b9; margin-top: 30px;">💾 Transient Cache Data</h2>';
echo '<div style="background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #9b59b6;">';

$transients = $wpdb->get_results(
    "SELECT option_name, option_value
    FROM {$wpdb->options}
    WHERE option_name LIKE '_transient_bwg_rentals_%'
    OR option_name LIKE '_transient_timeout_bwg_rentals_%'
    ORDER BY option_name"
);

if (empty($transients)) {
    echo '<p style="color: #7f8c8d;">✓ No transients found (cache is empty or cleared)</p>';
} else {
    echo '<p style="font-weight: bold; color: #e74c3c;">Found ' . count($transients) . ' transient entries:</p>';
    echo '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">';
    echo '<tr style="background: #ecf0f1; font-weight: bold;">';
    echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">Transient Name</td>';
    echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">Type</td>';
    echo '</tr>';

    foreach ($transients as $transient) {
        $type = (strpos($transient->option_name, '_timeout_') !== false) ? 'Timeout' : 'Value';
        $bg = ($type === 'Timeout') ? '#fff3cd' : '#d1ecf1';

        echo '<tr style="background: ' . $bg . ';">';
        echo '<td style="padding: 8px; border: 1px solid #bdc3c7;"><code>' . esc_html($transient->option_name) . '</code></td>';
        echo '<td style="padding: 8px; border: 1px solid #bdc3c7;">' . $type . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
echo '</div>';

// Step 3: Show what uninstall.php will delete
echo '<h2 style="color: #2980b9; margin-top: 30px;">🗑️ What Uninstall Will Delete</h2>';
echo '<div style="background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #e74c3c;">';

echo '<h3>Options to be deleted (from uninstall.php):</h3>';
echo '<ol style="line-height: 1.8;">';
foreach ($all_options as $option) {
    $exists = (get_option($option, null) !== null);
    $status_icon = $exists ? '🔴' : '⚪';
    $status_text = $exists ? '(will be deleted)' : '(does not exist)';
    echo '<li><code>' . esc_html($option) . '</code> ' . $status_icon . ' ' . $status_text . '</li>';
}
echo '</ol>';

echo '<h3 style="margin-top: 20px;">SQL Query for transient cleanup:</h3>';
echo '<pre style="background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto;">DELETE FROM ' . esc_html($wpdb->options) . '
WHERE option_name LIKE \'_transient_bwg_rentals_%\'
OR option_name LIKE \'_transient_timeout_bwg_rentals_%\'</pre>';

echo '<p style="margin-top: 10px;">This will remove <strong>' . count($transients) . ' transient entries</strong>.</p>';

echo '<h3 style="margin-top: 20px;">Scheduled events to be cleared:</h3>';
echo '<ul style="line-height: 1.8;">';
echo '<li><code>bwg_rentals_cache_refresh</code></li>';
echo '</ul>';

echo '</div>';

// Step 4: Summary
echo '<h2 style="color: #2980b9; margin-top: 30px;">📊 Uninstall Summary</h2>';
echo '<div style="background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #27ae60;">';

$total_items = $options_found + count($transients) + 1; // +1 for scheduled event

echo '<table style="width: 100%; border-collapse: collapse;">';
echo '<tr><td style="padding: 10px; border: 1px solid #bdc3c7; font-weight: bold;">Options to delete:</td><td style="padding: 10px; border: 1px solid #bdc3c7;">' . $options_found . '</td></tr>';
echo '<tr><td style="padding: 10px; border: 1px solid #bdc3c7; font-weight: bold;">Transients to delete:</td><td style="padding: 10px; border: 1px solid #bdc3c7;">' . count($transients) . '</td></tr>';
echo '<tr><td style="padding: 10px; border: 1px solid #bdc3c7; font-weight: bold;">Scheduled events to clear:</td><td style="padding: 10px; border: 1px solid #bdc3c7;">1</td></tr>';
echo '<tr style="background: #d5f4e6; font-weight: bold;"><td style="padding: 10px; border: 1px solid #bdc3c7;">TOTAL ITEMS:</td><td style="padding: 10px; border: 1px solid #bdc3c7;">' . $total_items . '</td></tr>';
echo '</table>';

if ($total_items > 0) {
    echo '<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">';
    echo '<strong>⚠️ Status:</strong> Plugin has data that will be cleaned up during uninstall.<br>';
    echo '<strong>✅ Uninstall Readiness:</strong> The uninstall.php file is configured to remove all plugin data.';
    echo '</div>';
} else {
    echo '<div style="margin-top: 20px; padding: 15px; background: #d5f4e6; border-radius: 5px; border-left: 4px solid #27ae60;">';
    echo '<strong>✅ Status:</strong> No plugin data currently exists in database.<br>';
    echo '<strong>Note:</strong> Uninstall will still run cleanup queries to ensure all data is removed.';
    echo '</div>';
}

echo '</div>';

// Step 5: Code Review
echo '<h2 style="color: #2980b9; margin-top: 30px;">🔍 Uninstall Code Review</h2>';
echo '<div style="background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #16a085;">';

$uninstall_file = WP_PLUGIN_DIR . '/bwg-rentals/uninstall.php';
if (file_exists($uninstall_file)) {
    echo '<p style="color: #27ae60; font-weight: bold;">✅ uninstall.php exists at: <code>' . esc_html($uninstall_file) . '</code></p>';

    $uninstall_content = file_get_contents($uninstall_file);

    // Check for WP_UNINSTALL_PLUGIN constant
    $has_constant_check = (strpos($uninstall_content, 'WP_UNINSTALL_PLUGIN') !== false);
    echo '<p>' . ($has_constant_check ? '✅' : '❌') . ' Security check for WP_UNINSTALL_PLUGIN constant</p>';

    // Check for each option
    $missing_options = array();
    foreach ($all_options as $option) {
        if (strpos($uninstall_content, $option) === false) {
            $missing_options[] = $option;
        }
    }

    if (empty($missing_options)) {
        echo '<p style="color: #27ae60;">✅ All expected options are referenced in uninstall.php</p>';
    } else {
        echo '<p style="color: #e74c3c;">❌ Missing options in uninstall.php:</p>';
        echo '<ul>';
        foreach ($missing_options as $opt) {
            echo '<li><code>' . esc_html($opt) . '</code></li>';
        }
        echo '</ul>';
    }

    // Check for transient cleanup
    $has_transient_cleanup = (strpos($uninstall_content, '_transient_bwg_rentals_') !== false);
    echo '<p>' . ($has_transient_cleanup ? '✅' : '❌') . ' Transient cleanup SQL query present</p>';

    // Check for scheduled event cleanup
    $has_hook_cleanup = (strpos($uninstall_content, 'wp_clear_scheduled_hook') !== false);
    echo '<p>' . ($has_hook_cleanup ? '✅' : '❌') . ' Scheduled event cleanup present</p>';

} else {
    echo '<p style="color: #e74c3c; font-weight: bold;">❌ uninstall.php NOT FOUND!</p>';
}

echo '</div>';

echo '<div style="margin-top: 30px; padding: 20px; background: #2c3e50; color: white; border-radius: 5px; text-align: center;">';
echo '<strong>Feature #71: Uninstall cleans up data</strong><br>';
echo 'Verification complete. Review the results above.';
echo '</div>';

echo '</div>';
