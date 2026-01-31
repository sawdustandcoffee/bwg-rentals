<?php
/**
 * Create test page for Feature #19: [bwg_property_card] requires id attribute
 *
 * This script creates a WordPress page with the shortcode WITHOUT the id attribute
 * to verify that an error message is displayed.
 */

// Load WordPress
require_once('/var/www/html/wp-load.php');

// Check if page already exists
$existing_page = get_page_by_path('feature-19-property-card-missing-id');

if ($existing_page) {
    echo "Page already exists (ID: {$existing_page->ID})\n";
    echo "URL: http://localhost:8088/feature-19-property-card-missing-id/\n";
    exit(0);
}

// Create page content with shortcode WITHOUT id attribute
$page_content = <<<HTML
<!-- wp:heading -->
<h2>Feature #19: Property Card - Missing ID Test</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This page tests that the [bwg_property_card] shortcode shows an error when the id attribute is missing.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Test 1: Shortcode Without ID Attribute</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The following shortcode should display an error message: "Property ID is required."</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[bwg_property_card]
<!-- /wp:shortcode -->

<!-- wp:heading {"level":3} -->
<h3>Test 2: Shortcode With Empty ID</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This should also display an error:</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[bwg_property_card id=""]
<!-- /wp:shortcode -->

<!-- wp:heading {"level":3} -->
<h3>Test 3: Shortcode With ID=0</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This should also display an error (empty check on id=0):</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[bwg_property_card id="0"]
<!-- /wp:shortcode -->
HTML;

// Create the page
$page_data = array(
    'post_title'   => 'Feature 19: Property Card Missing ID Test',
    'post_content' => $page_content,
    'post_status'  => 'publish',
    'post_type'    => 'page',
    'post_name'    => 'feature-19-property-card-missing-id'
);

$page_id = wp_insert_post($page_data);

if (is_wp_error($page_id)) {
    echo "Error creating page: " . $page_id->get_error_message() . "\n";
    exit(1);
}

echo "✓ Test page created successfully!\n";
echo "Page ID: $page_id\n";
echo "URL: http://localhost:8088/feature-19-property-card-missing-id/\n";
echo "\nExpected result: All three tests should show error message 'Property ID is required.'\n";
