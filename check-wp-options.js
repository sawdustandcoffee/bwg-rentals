const db = require('node:sqlite');
const { execSync } = require('child_process');

console.log('=== Checking WordPress Options for API Credentials ===\n');

try {
    // Try to query MySQL directly using mysql command
    const result = execSync(
        `mysql -u root wordpress -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('bwg_rentals_api_key', 'bwg_rentals_org_id')" 2>&1`,
        { encoding: 'utf8' }
    );

    console.log('MySQL Query Result:');
    console.log(result);

    // Parse the result to check encryption
    const lines = result.split('\n');
    lines.forEach(line => {
        if (line.includes('bwg_rentals_api_key') || line.includes('bwg_rentals_org_id')) {
            const parts = line.split('\t');
            if (parts.length >= 2) {
                const optionName = parts[0];
                const optionValue = parts[1];

                console.log(`\n${optionName}:`);
                console.log(`  Length: ${optionValue.length} characters`);
                console.log(`  First 50 chars: ${optionValue.substring(0, 50)}...`);

                // Check if it's base64 encoded
                try {
                    const decoded = Buffer.from(optionValue, 'base64').toString('binary');
                    console.log(`  Format: base64 encoded ✓`);

                    if (decoded.includes('::')) {
                        console.log(`  Encryption: Uses IV separator (::) ✓`);
                    } else {
                        console.log(`  Encryption: No IV separator found`);
                    }
                } catch (e) {
                    console.log(`  Format: NOT base64 encoded ✗`);
                }
            }
        }
    });

} catch (error) {
    console.log('Error querying MySQL:', error.message);
    console.log('\nAttempting wp-cli method...\n');

    try {
        const wpResult = execSync(
            `wp option get bwg_rentals_api_key --allow-root --path=/var/www/html 2>&1`,
            { encoding: 'utf8' }
        );
        console.log('API Key from wp-cli:', wpResult);
    } catch (wpError) {
        console.log('wp-cli also failed:', wpError.message);
    }
}
