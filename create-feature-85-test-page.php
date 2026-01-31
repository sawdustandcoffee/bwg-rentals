<?php
/**
 * Create test page for Feature #85
 * Run this from WordPress root or via curl
 */

// Load WordPress
require_once('/var/www/html/wp-load.php');

// Check if page already exists
$existing_page = get_page_by_path('feature-85-slider-test');

if ($existing_page) {
    echo "Page already exists at: http://localhost:8088/feature-85-slider-test/\n";
    echo "Page ID: " . $existing_page->ID . "\n";
    exit(0);
}

// Create page content
$content = <<<'CONTENT'
<h2>Feature #85: Property Slider - slides_to_show Test</h2>

<h3>Test 1: Default (1 slide at a time)</h3>
[bwg_property_slider]

<hr>

<h3>Test 2: Show 2 slides at once</h3>
[bwg_property_slider slides_to_show="2"]

<hr>

<h3>Test 3: Show 3 slides at once</h3>
[bwg_property_slider slides_to_show="3"]

<hr>

<h3>Test 4: Show 3 slides, scroll 2 at a time</h3>
[bwg_property_slider slides_to_show="3" slides_to_scroll="2"]

<hr>

<h3>Test 5: Show 4 slides (max)</h3>
[bwg_property_slider slides_to_show="4"]
CONTENT;

// Create the page
$page_data = array(
    'post_title'    => 'Feature #85: Property Slider Test',
    'post_content'  => $content,
    'post_status'   => 'publish',
    'post_type'     => 'page',
    'post_name'     => 'feature-85-slider-test',
    'post_author'   => 1,
);

$page_id = wp_insert_post($page_data);

if (is_wp_error($page_id)) {
    echo "Error creating page: " . $page_id->get_error_message() . "\n";
    exit(1);
}

echo "✓ Test page created successfully!\n";
echo "Page ID: $page_id\n";
echo "URL: http://localhost:8088/feature-85-slider-test/\n";
