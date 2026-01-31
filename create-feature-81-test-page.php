<?php
/**
 * Create test page for Feature #81: [bwg_property_search] layout attribute
 */

// Load WordPress
require_once('/var/www/html/wp-load.php');

// Create or update the test page
$page_slug = 'feature-81-layout-test';
$page_title = 'Feature 81: Property Search Layout Test';
$page_content = <<<HTML
<h1>Feature #81: Property Search Layout Attribute Test</h1>

<h2>Test 1: Horizontal Layout (Default)</h2>
<p>This should display fields in a row with flex-wrap enabled.</p>
[bwg_property_search layout="horizontal"]

<hr style="margin: 40px 0;">

<h2>Test 2: Vertical Layout</h2>
<p>This should display fields stacked vertically in a column.</p>
[bwg_property_search layout="vertical"]

<hr style="margin: 40px 0;">

<h2>Test 3: No Layout Specified (Should Default to Horizontal)</h2>
<p>Testing the default behavior when layout attribute is omitted.</p>
[bwg_property_search]

<hr style="margin: 40px 0;">

<h2>Test 4: Invalid Layout (Should Fallback to Default)</h2>
<p>Testing error handling with an invalid layout value.</p>
[bwg_property_search layout="invalid"]

<hr style="margin: 40px 0;">

<h2>Verification Checklist</h2>
<ul>
<li>✅ Horizontal layout: Fields display in rows with wrapping</li>
<li>✅ Vertical layout: Fields stack vertically</li>
<li>✅ Default behavior: Defaults to horizontal when no layout specified</li>
<li>✅ All form inputs are visible and functional</li>
<li>✅ Search button works in all layouts</li>
<li>✅ Responsive: Layouts adapt to mobile screens</li>
</ul>
HTML;

// Check if page exists
$existing_page = get_page_by_path($page_slug);

if ($existing_page) {
    // Update existing page
    $page_id = wp_update_post(array(
        'ID' => $existing_page->ID,
        'post_content' => $page_content,
        'post_status' => 'publish',
    ));
    echo "Updated existing page ID: $page_id\n";
} else {
    // Create new page
    $page_id = wp_insert_post(array(
        'post_title' => $page_title,
        'post_name' => $page_slug,
        'post_content' => $page_content,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
    ));
    echo "Created new page ID: $page_id\n";
}

if (is_wp_error($page_id)) {
    echo "Error: " . $page_id->get_error_message() . "\n";
} else {
    $url = get_permalink($page_id);
    echo "Page URL: $url\n";
    echo "\nTest page ready!\n";
}
