<?php
/**
 * Create test page for Feature #16: [bwg_property_card] show_image attribute
 */

// Load WordPress
require_once('vendor/autoload.php');

use WP_CLI\Utils;

// WordPress installation path
define('WP_PATH', '/tmp/wordpress');

// Load WordPress
require_once(WP_PATH . '/wp-load.php');

// Check if page already exists
$existing_page = get_page_by_title('Test Property Card show_image - Feature 16', OBJECT, 'page');

if ($existing_page) {
    echo "Page already exists (ID: {$existing_page->ID})\n";
    echo "URL: " . get_permalink($existing_page->ID) . "\n";
    exit;
}

// Create the test page
$page_content = <<<EOD
<h1>Property Card show_image Attribute Test</h1>

<p>This page tests Feature #16: The show_image attribute controls image visibility</p>

<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0;">
    <h2>Test 1: show_image="true" (Image Should Show)</h2>
    <p><strong>Expected:</strong> Image should be visible at the top of the card</p>
    [bwg_property_card id="1" show_image="true"]
</div>

<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0;">
    <h2>Test 2: show_image="false" (Image Should Be Hidden)</h2>
    <p><strong>Expected:</strong> NO image should be visible, only content</p>
    [bwg_property_card id="1" show_image="false"]
</div>

<div style="border: 2px solid #ddd; padding: 20px; margin: 20px 0;">
    <h2>Test 3: Default (no show_image attribute)</h2>
    <p><strong>Expected:</strong> Image should be visible (defaults to "true")</p>
    [bwg_property_card id="1"]
</div>

<hr>

<h3>Verification Steps:</h3>
<ol>
    <li>✅ Test show_image="true" - Image should be visible</li>
    <li>✅ Verify image shows - Check for img element in card</li>
    <li>✅ Test show_image="false" - Image should be hidden</li>
    <li>✅ Verify image hidden - No img element in card</li>
</ol>

<h3>Additional Checks:</h3>
<ul>
    <li>Default behavior: Image shows when attribute not specified</li>
    <li>Property title and specs still display when image hidden</li>
    <li>No JavaScript console errors</li>
    <li>No CSS layout issues</li>
</ul>
EOD;

$page_data = array(
    'post_title'   => 'Test Property Card show_image - Feature 16',
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
echo "\nOpen this URL in your browser to test Feature #16\n";
