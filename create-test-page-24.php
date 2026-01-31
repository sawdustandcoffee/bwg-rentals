<?php
/**
 * Create test page for Feature #24: [bwg_property_title] basic rendering
 *
 * Access this file via: http://localhost:8088/wp-content/plugins/bwg-rentals/create-test-page-24.php
 */

// Load WordPress
$wp_load_paths = [
    '/var/www/html/wp-load.php',
    '/srv/www/wordpress/wp-load.php',
    '/var/www/wordpress/wp-load.php',
    dirname(__FILE__) . '/../../../wp-load.php',
    dirname(__FILE__) . '/../../../../wp-load.php',
];

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('ERROR: Could not find WordPress. Tried paths: ' . implode(', ', $wp_load_paths));
}

// Check if page already exists
$existing = get_page_by_title('Feature 24 - Property Title Test', OBJECT, 'page');

if ($existing) {
    echo "<h1>✅ Test Page Already Exists</h1>";
    echo "<p><strong>Page ID:</strong> " . $existing->ID . "</p>";
    echo "<p><strong>URL:</strong> <a href='" . get_permalink($existing->ID) . "'>" . get_permalink($existing->ID) . "</a></p>";
    echo "<p><a href='" . get_permalink($existing->ID) . "' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;'>View Test Page</a></p>";
    exit(0);
}

// Create new page with the shortcode
$page_content = '
<h2>Test 1: Default (H1 tag)</h2>
[bwg_property_title id="1"]

<h2>Test 2: Custom Tag (H2)</h2>
[bwg_property_title id="1" tag="h2"]

<h2>Test 3: Custom Tag (H3)</h2>
[bwg_property_title id="1" tag="h3"]

<h2>Test 4: With Custom Class</h2>
[bwg_property_title id="1" tag="h2" class="custom-title-class"]

<h2>Test 5: Different Property (ID 2)</h2>
[bwg_property_title id="2"]

<h2>Test 6: Another Property (ID 3)</h2>
[bwg_property_title id="3"]

<h2>Test 7: Missing ID (Error Case)</h2>
[bwg_property_title]

<h2>Test 8: Invalid Property ID</h2>
[bwg_property_title id="99999"]
';

$page_data = array(
    'post_title'    => 'Feature 24 - Property Title Test',
    'post_content'  => $page_content,
    'post_status'   => 'publish',
    'post_type'     => 'page',
    'post_author'   => 1,
    'comment_status' => 'closed',
    'ping_status'   => 'closed'
);

$page_id = wp_insert_post($page_data);

if (is_wp_error($page_id)) {
    echo "<h1>❌ Error Creating Page</h1>";
    echo "<p>" . $page_id->get_error_message() . "</p>";
    exit(1);
}

echo "<h1>✅ Test Page Created Successfully!</h1>";
echo "<p><strong>Page ID:</strong> $page_id</p>";
echo "<p><strong>URL:</strong> <a href='" . get_permalink($page_id) . "'>" . get_permalink($page_id) . "</a></p>";
echo "<p><a href='" . get_permalink($page_id) . "' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;'>View Test Page</a></p>";
