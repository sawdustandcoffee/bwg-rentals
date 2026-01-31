<?php
/**
 * Create test page for Feature #20: [bwg_property] full page shortcode
 *
 * This creates a WordPress page with the [bwg_property] shortcode to verify
 * that all sections render correctly.
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
$existing = get_page_by_title('Feature 20 - Full Property Page Test', OBJECT, 'page');

if ($existing) {
    echo "<h1>✅ Test Page Already Exists</h1>";
    echo "<p><strong>Page ID:</strong> " . $existing->ID . "</p>";
    echo "<p><strong>URL:</strong> <a href='" . get_permalink($existing->ID) . "'>" . get_permalink($existing->ID) . "</a></p>";
    echo "<p><a href='" . get_permalink($existing->ID) . "' style='background: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;'>View Test Page</a></p>";
    exit(0);
}

// Create new page with the full property shortcode
$page_content = '
<h1>Feature #20: [bwg_property] Full Page Shortcode Test</h1>

<p><strong>Expected behavior:</strong> The shortcode should render a complete property page with ALL of the following sections:</p>
<ul>
<li>✓ Gallery (photo slider)</li>
<li>✓ Title (property name)</li>
<li>✓ Specs (beds, baths, guests, sqft)</li>
<li>✓ Description (full property description)</li>
<li>✓ Amenities (list of features)</li>
<li>✓ Availability (calendar showing available dates)</li>
<li>✓ Rates (pricing information)</li>
<li>✓ Location (address information)</li>
<li>✓ Policies (check-in, check-out, cancellation, etc.)</li>
<li>✓ Booking Button (link to book the property)</li>
</ul>

<hr>

<h2>Test 1: Full Property Page (All Sections - Default)</h2>
<p>This should display property ID 1 with all default sections enabled.</p>
[bwg_property id="1"]

<hr>

<h2>Test 2: Property ID 2</h2>
<p>Testing with a different property to ensure it works with multiple properties.</p>
[bwg_property id="2"]

<hr>

<h2>Test 3: Property ID 3</h2>
<p>Testing with a third property.</p>
[bwg_property id="3"]

<hr>

<h2>Test 4: Minimal Layout (Only Required Sections)</h2>
<p>Testing with breadcrumbs and related properties disabled.</p>
[bwg_property id="1" show_breadcrumbs="false" show_related="false"]

<hr>

<h2>Test 5: Custom Configuration</h2>
<p>Testing with some sections disabled.</p>
[bwg_property id="1" show_availability="false" show_policies="false"]
';

$page_data = array(
    'post_title'    => 'Feature 20 - Full Property Page Test',
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
echo "<hr>";
echo "<h2>Verification Steps:</h2>";
echo "<ol>";
echo "<li>Click the link above to view the test page</li>";
echo "<li>Verify that ALL 10 sections are rendered for each property</li>";
echo "<li>Check that the gallery shows property images</li>";
echo "<li>Check that the title displays the property name</li>";
echo "<li>Check that specs show bedrooms, bathrooms, guests, sqft</li>";
echo "<li>Check that the description is displayed</li>";
echo "<li>Check that amenities are listed</li>";
echo "<li>Check that the availability calendar is shown</li>";
echo "<li>Check that rates/pricing information is displayed</li>";
echo "<li>Check that the location/address is shown</li>";
echo "<li>Check that policies are displayed</li>";
echo "<li>Check that the booking button is present</li>";
echo "</ol>";
