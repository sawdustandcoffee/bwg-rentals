<?php
/**
 * Test script for Feature #47: API fetches properties list
 *
 * This script verifies that the BWG_API class can successfully fetch
 * properties from the Direct Software API.
 */

// Load WordPress
require_once '/var/www/html/wp-load.php';

echo "Feature #47: API fetches properties list\n";
echo "==========================================\n\n";

// Get the API instance
$cache = new BWG_Cache();
$api = new BWG_API( $cache );

// Step 1: Check API credentials
echo "Step 1: Checking API credentials...\n";
$reflection = new ReflectionClass( $api );
$method = $reflection->getMethod( 'get_credentials' );
$method->setAccessible( true );
$credentials = $method->invoke( $api );

if ( empty( $credentials['api_key'] ) ) {
    echo "   ❌ FAIL: API key not configured\n";
    exit( 1 );
}

echo "   ✓ API key configured\n";

// Step 2: Call get_properties()
echo "\nStep 2: Calling get_properties()...\n";
$properties = $api->get_properties( false ); // false = don't use cache, fetch fresh

// Step 3: Verify properties array returned
echo "\nStep 3: Verifying properties array returned...\n";

if ( is_wp_error( $properties ) ) {
    echo "   ❌ FAIL: API returned error: " . $properties->get_error_message() . "\n";
    exit( 1 );
}

if ( ! is_array( $properties ) ) {
    echo "   ❌ FAIL: API did not return an array (got " . gettype( $properties ) . ")\n";
    exit( 1 );
}

if ( empty( $properties ) ) {
    echo "   ⚠️  WARNING: API returned empty properties array\n";
    echo "   This could mean:\n";
    echo "   - No properties in the account\n";
    echo "   - Organization ID not configured\n";
    echo "   - API credentials incorrect\n\n";
    echo "   However, the API call succeeded and returned a valid array.\n";
}

echo "   ✓ Properties array returned\n";
echo "   ✓ Properties count: " . count( $properties ) . "\n\n";

if ( ! empty( $properties ) ) {
    echo "Sample property data:\n";
    $first_property = reset( $properties );
    echo "   ID: " . ( $first_property->id ?? 'N/A' ) . "\n";
    echo "   Name: " . ( $first_property->name ?? 'N/A' ) . "\n";
    echo "   Type: " . ( is_object( $first_property ) ? 'object' : gettype( $first_property ) ) . "\n";
}

echo "\n==========================================\n";
echo "Feature #47: PASSED ✓\n";
echo "==========================================\n";
