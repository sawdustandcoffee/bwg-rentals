<?php
/**
 * Get current API credential options from WordPress
 */

// Load WordPress
require_once '/var/www/html/wp-load.php';

echo "=== Current API Credential Options ===\n\n";

// Get raw option values
$api_key = get_option( 'bwg_rentals_api_key', '' );
$org_id = get_option( 'bwg_rentals_org_id', '' );

echo "API Key:\n";
if ( empty( $api_key ) ) {
    echo "  Status: NOT SET\n";
} else {
    echo "  Status: SET\n";
    echo "  Length: " . strlen( $api_key ) . " characters\n";
    echo "  First 50 chars: " . substr( $api_key, 0, 50 ) . "...\n";

    // Check if it looks encrypted
    $decoded = base64_decode( $api_key, true );
    if ( $decoded !== false ) {
        echo "  Format: base64 encoded ✓\n";
        if ( strpos( $decoded, '::' ) !== false ) {
            echo "  Encryption: Uses IV separator (::) ✓\n";
        } else {
            echo "  Encryption: No IV separator found\n";
        }
    } else {
        echo "  Format: NOT base64 encoded (likely plain text) ✗\n";
    }
}

echo "\nOrganization ID:\n";
if ( empty( $org_id ) ) {
    echo "  Status: NOT SET\n";
} else {
    echo "  Status: SET\n";
    echo "  Value length: " . strlen( $org_id ) . " characters\n";
    echo "  Full value: " . $org_id . "\n";

    // Check if it looks encrypted
    $decoded = base64_decode( $org_id, true );
    if ( $decoded !== false ) {
        echo "  Format: base64 encoded ✓\n";
        if ( strpos( $decoded, '::' ) !== false ) {
            echo "  Encryption: Uses IV separator (::) ✓\n";
        } else {
            echo "  Encryption: No IV separator found\n";
        }
    } else {
        echo "  Format: NOT base64 encoded (likely plain text) ✗\n";
    }
}
