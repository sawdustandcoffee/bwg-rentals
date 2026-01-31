<?php
/**
 * Create test page for Feature #17
 * Run: php create-test-page-feature-17.php
 */

// Load WordPress
require_once('/var/www/html/wp-load.php');

// Check if page already exists
$existing = get_page_by_title('Feature 17: Property Card show_specs Test', OBJECT, 'page');

if ($existing) {
    echo "Deleting existing test page (ID: {$existing->ID})...\n";
    wp_delete_post($existing->ID, true);
}

// Create page content
$content = '
<h2>Feature #17: [bwg_property_card] show_specs Attribute Test</h2>

<div style="background: #fffbcc; padding: 15px; margin: 20px 0; border-left: 4px solid #ffeb3b;">
    <strong>Test Objective:</strong> Verify that the <code>show_specs</code> attribute controls the visibility of property specifications (beds, baths, guests) in the property card.
</div>

<hr>

<h3>Test 1: show_specs="true" (Explicit)</h3>
<div style="background: #e7f8e7; padding: 15px; margin: 10px 0; border-left: 4px solid #4caf50;">
    <p><strong>Shortcode:</strong> <code>[bwg_property_card id="1" show_specs="true"]</code></p>
    <p><strong>Expected:</strong> Property card with specs section visible (beds, baths, guests)</p>
</div>
[bwg_property_card id="1" show_specs="true"]

<hr>

<h3>Test 2: Default Behavior (No Attribute)</h3>
<div style="background: #e7f8e7; padding: 15px; margin: 10px 0; border-left: 4px solid #4caf50;">
    <p><strong>Shortcode:</strong> <code>[bwg_property_card id="1"]</code></p>
    <p><strong>Expected:</strong> Specs section visible (default is true) - should look identical to Test 1</p>
</div>
[bwg_property_card id="1"]

<hr>

<h3>Test 3: show_specs="false"</h3>
<div style="background: #fff3e0; padding: 15px; margin: 10px 0; border-left: 4px solid #ff9800;">
    <p><strong>Shortcode:</strong> <code>[bwg_property_card id="1" show_specs="false"]</code></p>
    <p><strong>Expected:</strong> Property card WITHOUT specs section - only image and name</p>
</div>
[bwg_property_card id="1" show_specs="false"]

<hr>

<h3>Test 4: Combination Test</h3>
<div style="background: #fff3e0; padding: 15px; margin: 10px 0; border-left: 4px solid #ff9800;">
    <p><strong>Shortcode:</strong> <code>[bwg_property_card id="1" show_image="true" show_specs="false"]</code></p>
    <p><strong>Expected:</strong> Property card with image but no specs</p>
</div>
[bwg_property_card id="1" show_image="true" show_specs="false"]

<hr>

<h2>Verification Checklist</h2>
<ul>
    <li>✓ Test 1 (show_specs="true") displays specs section</li>
    <li>✓ Test 2 (default) displays specs section (identical to Test 1)</li>
    <li>✓ Test 3 (show_specs="false") does NOT display specs section</li>
    <li>✓ Test 4 shows image but no specs</li>
    <li>✓ Specs contain actual data (beds, baths, guests)</li>
    <li>✓ No console errors</li>
</ul>
';

// Create the page
$page_data = array(
    'post_title'    => 'Feature 17: Property Card show_specs Test',
    'post_content'  => $content,
    'post_status'   => 'publish',
    'post_type'     => 'page',
    'post_author'   => 1,
);

$page_id = wp_insert_post($page_data);

if ($page_id) {
    $page_url = get_permalink($page_id);
    echo "✓ Test page created successfully!\n";
    echo "  Page ID: $page_id\n";
    echo "  URL: $page_url\n";
    echo "\nOpen this URL in your browser to view the tests.\n";
} else {
    echo "✗ Failed to create test page\n";
    exit(1);
}
