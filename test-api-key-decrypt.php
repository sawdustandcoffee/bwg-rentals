<?php
// Load WordPress
require_once '/var/www/html/wp-load.php';

// Get encrypted value
$encrypted = get_option( 'bwg_rentals_api_key' );
echo "Encrypted value: " . $encrypted . "\n";

// Decrypt it
$decrypted = BWG_Admin::decrypt_value( $encrypted );
echo "Decrypted value: " . $decrypted . "\n";

// Check if it starts with MOCK_EMPTY_
if ( strpos( $decrypted, 'MOCK_EMPTY_' ) === 0 ) {
    echo "✓ Starts with MOCK_EMPTY_\n";
} else {
    echo "✗ Does NOT start with MOCK_EMPTY_\n";
}
