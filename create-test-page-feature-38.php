<?php
/**
 * Create WordPress test page for Feature #38
 * Run this via: wp eval-file create-test-page-feature-38.php
 * Or: php -r "define('WP_USE_THEMES', false); require('/var/www/html/wp-load.php'); include 'create-test-page-feature-38.php';"
 */

// Check if WordPress is loaded
if (!defined('ABSPATH') && file_exists('/var/www/html/wp-load.php')) {
    define('WP_USE_THEMES', false);
    require('/var/www/html/wp-load.php');
}

if (!function_exists('wp_insert_post')) {
    die("WordPress not loaded. Run this script via WP-CLI or ensure wp-load.php is accessible.\n");
}

$content = <<<'HTML'
<h1>Feature #38 Test: [bwg_property_amenities] show_icons Attribute</h1>

<div style="background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 5px;">
<h3>Feature Under Test</h3>
<p><strong>Feature ID:</strong> 38</p>
<p><strong>Category:</strong> Single Property Shortcodes</p>
<p><strong>Name:</strong> [bwg_property_amenities] show_icons attribute</p>
<p><strong>Description:</strong> The shortcode supports toggling amenity icons on/off</p>
</div>

<hr>

<h2>Test #1: show_icons="true" - Icons Display</h2>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc;">
<p><strong>Shortcode:</strong> <code>[bwg_property_amenities id="1" show_icons="true"]</code></p>
<p><strong>Expected:</strong> Each amenity displays with ✓ icon before the name</p>
</div>

[bwg_property_amenities id="1" show_icons="true"]

<hr>

<h2>Test #2: Default Behavior - Icons Display</h2>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc;">
<p><strong>Shortcode:</strong> <code>[bwg_property_amenities id="1"]</code></p>
<p><strong>Expected:</strong> Icons display by default (default value is "true")</p>
</div>

[bwg_property_amenities id="1"]

<hr>

<h2>Test #3: show_icons="false" - Icons Disabled</h2>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc;">
<p><strong>Shortcode:</strong> <code>[bwg_property_amenities id="1" show_icons="false"]</code></p>
<p><strong>Expected:</strong> No icons display, only amenity names show</p>
</div>

[bwg_property_amenities id="1" show_icons="false"]

<hr>

<h2>Test #4: show_icons="false" with columns="3"</h2>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc;">
<p><strong>Shortcode:</strong> <code>[bwg_property_amenities id="1" show_icons="false" columns="3"]</code></p>
<p><strong>Expected:</strong> 3-column grid layout, no icons</p>
</div>

[bwg_property_amenities id="1" show_icons="false" columns="3"]

<hr>

<h2>Test #5: Edge Case - show_icons="yes" (Invalid Value)</h2>
<div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #0066cc;">
<p><strong>Shortcode:</strong> <code>[bwg_property_amenities id="1" show_icons="yes"]</code></p>
<p><strong>Expected:</strong> Icons disabled (only "true" enables icons)</p>
</div>

[bwg_property_amenities id="1" show_icons="yes"]

<hr>

<div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;">
<h3>Verification Checklist</h3>
<ul>
<li>☐ Test #1: Icons display correctly with show_icons="true"</li>
<li>☐ Test #2: Default behavior shows icons</li>
<li>☐ Test #3: Icons hidden correctly with show_icons="false"</li>
<li>☐ Test #4: Works with columns attribute</li>
<li>☐ Test #5: Invalid values handled safely</li>
<li>☐ No console errors</li>
</ul>
</div>
HTML;

// Create the page
$page_data = array(
    'post_title'   => 'Feature 38 - Amenities show_icons Test',
    'post_content' => $content,
    'post_status'  => 'publish',
    'post_type'    => 'page',
    'post_author'  => 1,
);

$page_id = wp_insert_post($page_data);

if (is_wp_error($page_id)) {
    echo "Error creating page: " . $page_id->get_error_message() . "\n";
    exit(1);
}

$page_url = get_permalink($page_id);

echo "✓ Test page created successfully!\n";
echo "  Page ID: $page_id\n";
echo "  URL: $page_url\n";
echo "\nOpen this URL in your browser to test Feature #38.\n";
