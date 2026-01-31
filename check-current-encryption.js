const db = require('node:sqlite');

// Check WordPress database for current option values
const { exec } = require('child_process');

exec('wp db query "SELECT option_name, option_value FROM wp_options WHERE option_name IN (\'bwg_rentals_api_key\', \'bwg_rentals_org_id\')" --allow-root --path=/var/www/html 2>&1', (error, stdout, stderr) => {
    console.log('=== Current WordPress Options ===\n');

    if (error) {
        console.log('Error running wp-cli:', error.message);
        console.log('Trying alternative method...\n');

        // Alternative: try to read from PHP
        exec('php /home/buckneri/projects/bwg-rentals/get-current-options.php 2>&1', (err2, out2, stderr2) => {
            if (err2) {
                console.log('PHP error:', err2.message);
            } else {
                console.log(out2);
            }
        });
    } else {
        console.log(stdout);
        if (stderr) {
            console.log('Stderr:', stderr);
        }
    }
});
