#!/bin/bash

# BWG Rentals WordPress Plugin - Development Environment Setup
# This script sets up the development environment for the BWG Rentals plugin

set -e

PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_NAME="bwg-rentals"

echo "============================================"
echo "BWG Rentals Plugin - Development Setup"
echo "============================================"
echo ""

# Check PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed or not in PATH"
    echo "Please install PHP 7.4 or higher"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "PHP Version: $PHP_VERSION"

# Check PHP version meets minimum requirement (7.4)
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")
if [ "$PHP_MAJOR" -lt 7 ] || ([ "$PHP_MAJOR" -eq 7 ] && [ "$PHP_MINOR" -lt 4 ]); then
    echo "ERROR: PHP 7.4+ is required (found $PHP_VERSION)"
    exit 1
fi
echo "PHP version check passed"

# Check if Composer is available (for dependencies)
if command -v composer &> /dev/null; then
    echo "Composer found: $(composer --version 2>/dev/null | head -n 1)"
else
    echo "WARNING: Composer not found - install it for development dependencies"
fi

# Check for WP-CLI (helpful for development)
if command -v wp &> /dev/null; then
    echo "WP-CLI found: $(wp --version 2>/dev/null)"
else
    echo "NOTE: WP-CLI not found - consider installing for WordPress development"
fi

echo ""
echo "============================================"
echo "Project Structure Setup"
echo "============================================"
echo ""

# Create plugin directory structure
DIRS=(
    "includes"
    "shortcodes"
    "assets/css"
    "assets/js"
    "templates"
    "vendor"
    "languages"
)

for dir in "${DIRS[@]}"; do
    if [ ! -d "$PLUGIN_DIR/$dir" ]; then
        mkdir -p "$PLUGIN_DIR/$dir"
        echo "Created: $dir/"
    else
        echo "Exists:  $dir/"
    fi
done

echo ""
echo "============================================"
echo "Plugin Info"
echo "============================================"
echo ""
echo "Plugin Name:     BWG Rentals"
echo "Plugin Dir:      $PLUGIN_DIR"
echo "Text Domain:     bwg-rentals"
echo ""
echo "Technology Stack:"
echo "  - WordPress 6.0+"
echo "  - PHP 7.4+"
echo "  - Direct Software API Integration"
echo "  - Plugin Update Checker (GitHub releases)"
echo ""

echo "============================================"
echo "Development Instructions"
echo "============================================"
echo ""
echo "1. Install this plugin in a WordPress development environment:"
echo "   - Copy/symlink plugin folder to wp-content/plugins/"
echo "   - Or use a tool like Local by Flywheel, MAMP, or Docker"
echo ""
echo "2. Configure API credentials in WordPress Admin:"
echo "   - Settings > BWG Rentals"
echo "   - Enter your Direct Software API Key"
echo "   - Enter your Organization ID"
echo ""
echo "3. Use shortcodes on pages:"
echo "   - [bwg_properties] - Display all properties"
echo "   - [bwg_property_card id=\"X\"] - Single property card"
echo "   - [bwg_property_gallery id=\"X\"] - Property images"
echo "   - [bwg_property_title id=\"X\"] - Property name"
echo "   - [bwg_property_specs id=\"X\"] - Beds, baths, guests"
echo "   - [bwg_property_description id=\"X\"] - Description"
echo "   - [bwg_property_amenities id=\"X\"] - Amenities list"
echo "   - [bwg_property_availability id=\"X\"] - Calendar"
echo "   - [bwg_property_rates id=\"X\"] - Pricing table"
echo "   - [bwg_property_booking_button id=\"X\"] - Booking CTA"
echo "   - [bwg_property_location id=\"X\"] - Address"
echo "   - [bwg_property_policies id=\"X\"] - House rules"
echo ""
echo "4. For production deployment:"
echo "   - Tag a new version in GitHub"
echo "   - GitHub Actions will create a release"
echo "   - Plugin will auto-update from GitHub releases"
echo ""
echo "============================================"
echo "Setup Complete!"
echo "============================================"
