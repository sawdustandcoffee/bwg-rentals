<?php
/**
 * Create test page for Feature #35: [bwg_property_availability] basic rendering
 */

// Load WordPress
require_once('vendor/autoload.php');

use WP_CLI\Utils;

// WordPress installation path
define('WP_PATH', '/tmp/wordpress');

// Load WordPress
require_once(WP_PATH . '/wp-load.php');

// Check if page already exists
$existing_page = get_page_by_title('Test Availability Calendar - Feature 35', OBJECT, 'page');

if ($existing_page) {
    echo "Page already exists (ID: {$existing_page->ID})\n";
    echo "URL: " . get_permalink($existing_page->ID) . "\n";
    exit;
}

// Create the test page
$page_content = <<<EOD
<h1>Property Availability Calendar Test</h1>

<p>This page tests Feature #35: [bwg_property_availability] basic rendering</p>

<h2>Test 1: Basic Calendar (Property ID 1)</h2>
[bwg_property_availability id="1"]

<h2>Test 2: Show 6 Months</h2>
[bwg_property_availability id="1" months_to_show="6"]

<h2>Test 3: Different Property (ID 2)</h2>
[bwg_property_availability id="2"]

<hr>

<h3>Verification Points:</h3>
<ul>
    <li>Calendar displays with proper styling</li>
    <li>Month titles are visible</li>
    <li>Day headers (Sun-Sat) are shown</li>
    <li>Calendar grid shows all dates</li>
    <li>Available/unavailable dates are color-coded</li>
    <li>Legend shows color indicators</li>
    <li>Navigation buttons work (Previous/Next)</li>
    <li>No JavaScript console errors</li>
</ul>
EOD;

$page_data = array(
    'post_title'   => 'Test Availability Calendar - Feature 35',
    'post_content' => $page_content,
    'post_status'  => 'publish',
    'post_type'    => 'page',
    'post_author'  => 1,
);

$page_id = wp_insert_post($page_data);

if (is_wp_error($page_id)) {
    echo "Error creating page: " . $page_id->get_error_message() . "\n";
    exit(1);
}

echo "✓ Test page created successfully!\n";
echo "Page ID: {$page_id}\n";
echo "URL: " . get_permalink($page_id) . "\n";
echo "\nOpen this URL in your browser to test Feature #35\n";
