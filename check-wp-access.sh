#!/bin/bash
# Check various potential WordPress URLs
echo "Checking for WordPress installation..."

# Check if Apache/Nginx is running
if systemctl is-active --quiet apache2 2>/dev/null; then
    echo "✓ Apache is running"
elif systemctl is-active --quiet nginx 2>/dev/null; then
    echo "✓ Nginx is running"
else
    echo "⚠ Web server status unknown"
fi

# Check if WordPress is in /var/www/html
if [ -d "/var/www/html/wp-content" ]; then
    echo "✓ WordPress found at /var/www/html"
    
    # Check if our plugin is symlinked
    if [ -L "/var/www/html/wp-content/plugins/bwg-rentals" ]; then
        echo "✓ BWG Rentals plugin is symlinked"
    elif [ -d "/var/www/html/wp-content/plugins/bwg-rentals" ]; then
        echo "✓ BWG Rentals plugin directory exists"
    else
        echo "⚠ BWG Rentals plugin not found in plugins directory"
    fi
else
    echo "✗ WordPress not found at /var/www/html"
fi

# Try to determine the site URL
if [ -f "/var/www/html/wp-config.php" ]; then
    echo "✓ wp-config.php found"
    WP_HOME=$(grep "WP_HOME" /var/www/html/wp-config.php 2>/dev/null | head -1)
    WP_SITEURL=$(grep "WP_SITEURL" /var/www/html/wp-config.php 2>/dev/null | head -1)
    
    if [ -n "$WP_HOME" ]; then
        echo "  WP_HOME: $WP_HOME"
    fi
    if [ -n "$WP_SITEURL" ]; then
        echo "  WP_SITEURL: $WP_SITEURL"
    fi
fi

echo ""
echo "Likely WordPress URL: http://localhost or http://127.0.0.1"
