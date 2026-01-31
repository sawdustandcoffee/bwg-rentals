<?php
/**
 * Create test page for Feature #57: API Connection Failure Error Handling
 *
 * This script creates a WordPress page with property shortcodes to test
 * that API connection failures display user-friendly error messages.
 *
 * Run this file by accessing: http://localhost:8088/wp-content/plugins/bwg-rentals/create-feature-57-test-page.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is logged in and is an administrator
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('You must be logged in as an administrator to run this script.');
}

// Create test page
$page_title = 'Feature 57: API Error Test';
$page_content = '
<h2>Test 1: Properties Archive (with current API credentials)</h2>
[bwg_properties limit="3"]

<hr>

<h2>Test 2: Single Property Shortcodes (Property ID 1)</h2>
<h3>Property Title</h3>
[bwg_property_title id="1"]

<h3>Property Gallery</h3>
[bwg_property_gallery id="1"]

<h3>Property Specs</h3>
[bwg_property_specs id="1"]

<h3>Property Description</h3>
[bwg_property_description id="1"]

<h3>Property Amenities</h3>
[bwg_property_amenities id="1"]

<h3>Property Location</h3>
[bwg_property_location id="1"]

<h3>Property Booking Button</h3>
[bwg_property_booking_button id="1"]

<hr>

<h2>Test 3: Invalid Property ID (Should show 404 error)</h2>
[bwg_property_title id="99999"]

<hr>

<h2>Instructions for Testing:</h2>
<ol>
    <li><strong>Test with Valid Credentials:</strong> View this page with current API credentials to verify it works normally</li>
    <li><strong>Test with No Credentials:</strong> Go to Settings > BWG Rentals, clear the API key and Org ID, save, then reload this page</li>
    <li><strong>Test with Invalid Credentials:</strong> Set API Key to "INVALID_KEY_123" and reload</li>
    <li><strong>Test Timeout:</strong> Set API Key to "MOCK_TIMEOUT_TEST" to simulate network timeout</li>
    <li><strong>Test Invalid Property ID:</strong> See "Test 3" section above (should show "Property not found" error)</li>
</ol>

<p><strong>Expected Results:</strong></p>
<ul>
    <li>✅ All errors should display in a red error box (class: <code>bwg-error</code>)</li>
    <li>✅ Error messages should be user-friendly (not technical PHP errors)</li>
    <li>✅ Page should not crash or show blank output</li>
    <li>✅ No JavaScript console errors</li>
</ul>
';

// Check if page already exists
$existing_page = get_page_by_title($page_title, OBJECT, 'page');

if ($existing_page) {
    // Update existing page
    $page_id = wp_update_post(array(
        'ID'           => $existing_page->ID,
        'post_content' => $page_content,
        'post_status'  => 'publish',
    ));
    $message = 'Test page updated!';
} else {
    // Create new page
    $page_id = wp_insert_post(array(
        'post_title'   => $page_title,
        'post_content' => $page_content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ));
    $message = 'Test page created!';
}

if (is_wp_error($page_id)) {
    wp_die('Error creating page: ' . $page_id->get_error_message());
}

// Get the page URL
$page_url = get_permalink($page_id);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Feature #57 Test Page Created</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f0f0f1;
        }
        .success-box {
            background: #d7f0d7;
            border-left: 4px solid #00a32a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box h1 {
            margin-top: 0;
            color: #00a32a;
        }
        .button {
            display: inline-block;
            background: #2271b1;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .button:hover {
            background: #135e96;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
            border: 1px solid #c3c4c7;
        }
        code {
            background: #f6f7f7;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: Consolas, Monaco, monospace;
        }
    </style>
</head>
<body>
    <div class="success-box">
        <h1>✓ <?php echo esc_html($message); ?></h1>
        <p><strong>Page ID:</strong> <?php echo absint($page_id); ?></p>
        <p><strong>Page URL:</strong> <a href="<?php echo esc_url($page_url); ?>" target="_blank"><?php echo esc_url($page_url); ?></a></p>
    </div>

    <div class="info-box">
        <h2>Next Steps:</h2>
        <ol>
            <li><a href="<?php echo esc_url($page_url); ?>" class="button" target="_blank">View Test Page</a></li>
            <li><a href="<?php echo admin_url('options-general.php?page=bwg-rentals'); ?>" class="button">Go to Settings</a></li>
        </ol>

        <h3>Testing Scenarios:</h3>
        <p><strong>1. Test with Valid Credentials (Baseline):</strong></p>
        <ul>
            <li>View the test page with current valid API credentials</li>
            <li>Verify properties load normally (or show mock data)</li>
        </ul>

        <p><strong>2. Test with No Credentials:</strong></p>
        <ul>
            <li>Go to Settings > BWG Rentals</li>
            <li>Clear both API Key and Organization ID fields</li>
            <li>Save changes</li>
            <li>Reload the test page</li>
            <li>✅ Expected: "API credentials not configured." error</li>
        </ul>

        <p><strong>3. Test with Timeout Simulation:</strong></p>
        <ul>
            <li>Go to Settings > BWG Rentals</li>
            <li>Set API Key to: <code>MOCK_TIMEOUT_TEST</code></li>
            <li>Set Organization ID to: <code>test</code></li>
            <li>Save changes</li>
            <li>Reload the test page</li>
            <li>✅ Expected: "Unable to connect to the property service. The server is taking too long to respond. Please try again later."</li>
        </ul>

        <p><strong>4. Test Invalid Property ID (404):</strong></p>
        <ul>
            <li>Scroll to "Test 3" section on the test page</li>
            <li>✅ Expected: "Property not found. Please check the property ID."</li>
        </ul>
    </div>

    <div class="info-box">
        <h3>Error Message Verification Checklist:</h3>
        <ul>
            <li>✅ Error displayed in styled error box (red border, light red background)</li>
            <li>✅ Error message is user-friendly (not technical PHP error)</li>
            <li>✅ Page does not crash or show blank output</li>
            <li>✅ No JavaScript console errors</li>
            <li>✅ Error has proper CSS class: <code>bwg-error</code></li>
        </ul>
    </div>
</body>
</html>
<?php
