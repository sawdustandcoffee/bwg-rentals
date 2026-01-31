<?php
/**
 * Setup Mock API Credentials
 * Run with: wp eval-file setup-mock-credentials.php
 */

require_once ABSPATH . 'wp-content/plugins/bwg-rentals/includes/class-bwg-admin.php';
$admin = new BWG_Admin();

// Encrypt the mock API key
$encrypted_key = $admin->encrypt_value('MOCK_TEST_API');
$encrypted_org = $admin->encrypt_value('test_org');

// Update options
update_option('bwg_rentals_api_key', $encrypted_key);
update_option('bwg_rentals_org_id', $encrypted_org);

echo "Encrypted API Key: " . $encrypted_key . "\n";
echo "Encrypted Org ID: " . $encrypted_org . "\n";

// Verify decryption works
echo "Decrypted API Key: " . BWG_Admin::decrypt_value($encrypted_key) . "\n";
echo "Decrypted Org ID: " . BWG_Admin::decrypt_value($encrypted_org) . "\n";

echo "\nDone! Mock credentials have been set up.\n";
