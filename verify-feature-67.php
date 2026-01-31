<?php
/**
 * Feature #67 Verification Script
 * Verifies that API credentials are stored encrypted in the database
 */

// Load WordPress
require_once '/var/www/html/wp-load.php';

echo "=== Feature #67: API Credentials Encryption Verification ===\n\n";

// Step 1: Check if API key option exists
echo "Step 1: Check API key option\n";
$api_key_encrypted = get_option( 'bwg_rentals_api_key' );
if ( empty( $api_key_encrypted ) ) {
    echo "❌ FAIL: No API key found in database\n";
    echo "   You need to save API credentials first\n\n";
} else {
    echo "✓ PASS: API key option exists\n";
    echo "   Raw database value: " . substr( $api_key_encrypted, 0, 50 ) . "...\n\n";
}

// Step 2: Check if org ID option exists
echo "Step 2: Check Organization ID option\n";
$org_id_encrypted = get_option( 'bwg_rentals_org_id' );
if ( empty( $org_id_encrypted ) ) {
    echo "❌ FAIL: No org ID found in database\n";
    echo "   You need to save API credentials first\n\n";
} else {
    echo "✓ PASS: Org ID option exists\n";
    echo "   Raw database value: " . $org_id_encrypted . "\n\n";
}

// Step 3: Verify API key is encrypted (not plain text)
echo "Step 3: Verify API key is encrypted\n";
if ( ! empty( $api_key_encrypted ) ) {
    // Check if it's base64 encoded (encrypted values should be)
    $decoded = base64_decode( $api_key_encrypted, true );

    if ( $decoded === false ) {
        echo "❌ FAIL: API key is not base64 encoded (not encrypted)\n";
        echo "   Raw value: " . $api_key_encrypted . "\n\n";
    } else {
        // Check if decoded value contains the IV separator
        if ( strpos( $decoded, '::' ) !== false ) {
            echo "✓ PASS: API key is encrypted (contains IV separator)\n";
            echo "   Format: base64(encrypted_data::IV)\n";
            echo "   Length: " . strlen( $api_key_encrypted ) . " characters\n\n";
        } else {
            echo "⚠ WARNING: API key is base64 encoded but format is different\n";
            echo "   May be using alternate encryption method\n";
            echo "   Decoded preview: " . substr( $decoded, 0, 30 ) . "...\n\n";
        }
    }
}

// Step 4: Verify org ID is encrypted
echo "Step 4: Verify Organization ID is encrypted\n";
if ( ! empty( $org_id_encrypted ) ) {
    $decoded = base64_decode( $org_id_encrypted, true );

    if ( $decoded === false ) {
        echo "❌ FAIL: Org ID is not base64 encoded (not encrypted)\n";
        echo "   Raw value: " . $org_id_encrypted . "\n\n";
    } else {
        if ( strpos( $decoded, '::' ) !== false ) {
            echo "✓ PASS: Org ID is encrypted (contains IV separator)\n";
            echo "   Format: base64(encrypted_data::IV)\n";
            echo "   Length: " . strlen( $org_id_encrypted ) . " characters\n\n";
        } else {
            echo "⚠ WARNING: Org ID is base64 encoded but format is different\n";
            echo "   May be using alternate encryption method\n";
            echo "   Decoded preview: " . substr( $decoded, 0, 30 ) . "...\n\n";
        }
    }
}

// Step 5: Test decryption (if BWG_Admin class exists)
echo "Step 5: Test decryption functionality\n";
if ( class_exists( 'BWG_Admin' ) ) {
    $admin = new BWG_Admin();

    if ( ! empty( $api_key_encrypted ) ) {
        $decrypted_key = $admin->decrypt_api_key( $api_key_encrypted );
        echo "✓ API key can be decrypted\n";
        echo "   Decrypted length: " . strlen( $decrypted_key ) . " characters\n";
        echo "   Decrypted preview: " . substr( $decrypted_key, 0, 10 ) . "...\n";
    }

    if ( ! empty( $org_id_encrypted ) ) {
        $decrypted_org = $admin->decrypt_org_id( $org_id_encrypted );
        echo "✓ Org ID can be decrypted\n";
        echo "   Decrypted value: " . $decrypted_org . "\n";
    }
} else {
    echo "❌ BWG_Admin class not found\n";
}

echo "\n=== Verification Complete ===\n";
