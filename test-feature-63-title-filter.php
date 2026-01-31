<?php
/**
 * Test Feature #63: bwg_property_title_output filter works
 *
 * Test Steps:
 * 1. Add filter in theme functions.php
 * 2. Verify title output is modified
 */

// Load WordPress
require_once '/var/www/html/wp-load.php';

echo "=== Feature #63: bwg_property_title_output filter test ===\n\n";

// Step 1: Add the filter (simulating theme functions.php)
echo "Step 1: Adding filter to modify title output...\n";

add_filter('bwg_property_title_output', function($output, $property) {
    // Wrap the title in a custom div with a test marker
    return '<div class="custom-title-wrapper" data-test="filter-applied">' . $output . '</div>';
}, 10, 2);

echo "✓ Filter added successfully\n\n";

// Step 2: Test the filter by calling the shortcode
echo "Step 2: Testing shortcode output with filter...\n";

// Get a property ID to test with
$api = new BWG_API();
$properties = $api->get_properties();

if (is_wp_error($properties) || empty($properties)) {
    echo "✗ ERROR: Could not get properties for testing\n";
    exit(1);
}

$test_property_id = $properties[0]['id'];
echo "Using property ID: {$test_property_id}\n";

// Call the shortcode
$shortcodes = BWG_Shortcodes::get_instance();
$output = $shortcodes->property_title(['id' => $test_property_id]);

echo "\nShortcode output:\n";
echo $output . "\n\n";

// Step 3: Verify the filter was applied
echo "Step 3: Verifying filter was applied...\n";

// Check if our custom wrapper is present
if (strpos($output, 'custom-title-wrapper') !== false) {
    echo "✓ Custom wrapper class found\n";
} else {
    echo "✗ ERROR: Custom wrapper class NOT found\n";
    exit(1);
}

// Check if our test data attribute is present
if (strpos($output, 'data-test="filter-applied"') !== false) {
    echo "✓ Test data attribute found\n";
} else {
    echo "✗ ERROR: Test data attribute NOT found\n";
    exit(1);
}

// Check if the original title HTML is still inside
if (strpos($output, 'class="bwg-property-title') !== false) {
    echo "✓ Original title HTML preserved inside wrapper\n";
} else {
    echo "✗ ERROR: Original title HTML NOT found\n";
    exit(1);
}

// Verify the property object was passed to the filter
echo "\nStep 4: Testing filter receives property object...\n";

$property_received = null;
add_filter('bwg_property_title_output', function($output, $property) use (&$property_received) {
    $property_received = $property;
    return $output;
}, 20, 2);

// Call shortcode again
$shortcodes->property_title(['id' => $test_property_id]);

if ($property_received !== null) {
    echo "✓ Property object passed to filter\n";
    echo "  Property ID: " . (isset($property_received['id']) ? $property_received['id'] : 'N/A') . "\n";
    echo "  Property Name: " . (isset($property_received['name']) ? $property_received['name'] : 'N/A') . "\n";
} else {
    echo "✗ ERROR: Property object NOT passed to filter\n";
    exit(1);
}

echo "\n=== ALL TESTS PASSED ✓ ===\n";
echo "\nFeature #63 verification complete:\n";
echo "- Filter hook 'bwg_property_title_output' exists\n";
echo "- Filter receives output and property parameters\n";
echo "- Filter can modify the title output\n";
echo "- Modified output is returned correctly\n";
