<?php
/**
 * Test Feature #64: bwg_booking_button_text filter works
 *
 * Test Steps:
 * 1. Add filter in theme functions.php
 * 2. Verify button text is modified
 */

// Load WordPress
require_once '/var/www/html/wp-load.php';

echo "=== Feature #64: bwg_booking_button_text filter test ===\n\n";

// Step 1: Add the filter (simulating theme functions.php)
echo "Step 1: Adding filter to modify booking button text...\n";

add_filter('bwg_booking_button_text', function($text, $property) {
    // Prepend "CUSTOM: " to the text to verify filter works
    return 'CUSTOM: ' . $text;
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

// Test Case A: Default button text
echo "\n--- Test Case A: Default button text ---\n";
$shortcodes = BWG_Shortcodes::get_instance();
$output_default = $shortcodes->property_booking_button(['id' => $test_property_id]);

echo "Shortcode: [bwg_property_booking_button id=\"{$test_property_id}\"]\n";
echo "Output:\n" . $output_default . "\n";

// Verify filter modified the default text
if (strpos($output_default, 'CUSTOM:') !== false) {
    echo "✓ Filter applied to default text\n";
} else {
    echo "✗ ERROR: Filter NOT applied to default text\n";
    exit(1);
}

// Test Case B: Custom text attribute
echo "\n--- Test Case B: Custom text attribute ---\n";
$output_custom = $shortcodes->property_booking_button([
    'id' => $test_property_id,
    'text' => 'Reserve Now'
]);

echo "Shortcode: [bwg_property_booking_button id=\"{$test_property_id}\" text=\"Reserve Now\"]\n";
echo "Output:\n" . $output_custom . "\n";

// Verify filter modified the custom text
if (strpos($output_custom, 'CUSTOM: Reserve Now') !== false) {
    echo "✓ Filter applied to custom text\n";
} else {
    echo "✗ ERROR: Filter NOT applied to custom text\n";
    echo "  Expected to find: 'CUSTOM: Reserve Now'\n";
    exit(1);
}

// Step 3: Verify the filter receives property object
echo "\n--- Test Case C: Verify property object is passed ---\n";

$property_received = null;
add_filter('bwg_booking_button_text', function($text, $property) use (&$property_received) {
    $property_received = $property;
    return $text;
}, 20, 2);

// Call shortcode again
$shortcodes->property_booking_button(['id' => $test_property_id]);

if ($property_received !== null) {
    echo "✓ Property object passed to filter\n";
    echo "  Property ID: " . (isset($property_received['id']) ? $property_received['id'] : 'N/A') . "\n";
    echo "  Property Name: " . (isset($property_received['name']) ? $property_received['name'] : 'N/A') . "\n";
} else {
    echo "✗ ERROR: Property object NOT passed to filter\n";
    exit(1);
}

// Step 4: Test property-specific text modification
echo "\n--- Test Case D: Property-specific text modification ---\n";

// Remove previous test filters
remove_all_filters('bwg_booking_button_text');

// Add a filter that uses the property data
add_filter('bwg_booking_button_text', function($text, $property) {
    if (isset($property['name'])) {
        return 'Book ' . $property['name'];
    }
    return $text;
}, 10, 2);

$output_property_specific = $shortcodes->property_booking_button(['id' => $test_property_id]);

echo "Filter: Returns 'Book {property_name}'\n";
echo "Output:\n" . $output_property_specific . "\n";

if (isset($properties[0]['name']) && strpos($output_property_specific, 'Book ' . $properties[0]['name']) !== false) {
    echo "✓ Property-specific text modification works\n";
} else {
    echo "✗ ERROR: Property-specific text modification failed\n";
    exit(1);
}

// Step 5: Verify the text is properly escaped
echo "\n--- Test Case E: Security - HTML escaping ---\n";

remove_all_filters('bwg_booking_button_text');

add_filter('bwg_booking_button_text', function($text, $property) {
    return '<script>alert("XSS")</script>Unsafe Text';
}, 10, 2);

$output_escaped = $shortcodes->property_booking_button(['id' => $test_property_id]);

echo "Filter returns: '<script>alert(\"XSS\")</script>Unsafe Text'\n";
echo "Output:\n" . $output_escaped . "\n";

// Check that script tags are escaped
if (strpos($output_escaped, '<script>') === false && strpos($output_escaped, '&lt;script&gt;') !== false) {
    echo "✓ HTML properly escaped (XSS protection working)\n";
} else {
    echo "✗ ERROR: HTML not properly escaped (security vulnerability!)\n";
    exit(1);
}

echo "\n=== ALL TESTS PASSED ✓ ===\n";
echo "\nFeature #64 verification complete:\n";
echo "- Filter hook 'bwg_booking_button_text' exists\n";
echo "- Filter receives text and property parameters\n";
echo "- Filter can modify the button text\n";
echo "- Filter works with both default and custom text\n";
echo "- Filter receives property object for context-aware modifications\n";
echo "- Output is properly escaped for security\n";
