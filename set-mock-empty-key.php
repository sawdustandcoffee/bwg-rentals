<?php
// Load WordPress
require_once '/var/www/html/wp-load.php';

// Set API key properly using encryption
$api_key = 'MOCK_EMPTY_';
$org_id = 'TEST_ORG';

// Encrypt and save
$encrypted_key = BWG_Admin::encrypt_value( $api_key );
$encrypted_org = BWG_Admin::encrypt_value( $org_id );

update_option( 'bwg_rentals_api_key', $encrypted_key );
update_option( 'bwg_rentals_organization_id', $encrypted_org );

echo "API Key set to: " . $api_key . "\n";
echo "Encrypted as: " . $encrypted_key . "\n";

// Verify
$decrypted = BWG_Admin::decrypt_value( get_option( 'bwg_rentals_api_key' ) );
echo "Decrypted back to: " . $decrypted . "\n";

if ( strpos( $decrypted, 'MOCK_EMPTY_' ) === 0 ) {
    echo "✓ Successfully set MOCK_EMPTY_ key!\n";
} else {
    echo "✗ Failed to set key properly\n";
}
