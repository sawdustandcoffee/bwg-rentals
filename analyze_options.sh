#!/bin/bash
echo "=== OPTIONS FOUND IN CODEBASE ==="
echo ""
echo "1. API Key (TWO NAMES FOUND):"
grep -r "bwg_rentals_api_key" includes/ bwg-rentals.php --include="*.php" | grep -E "get_option|update_option|register_setting" | head -5
echo ""
echo "2. Organization ID (TWO NAMES FOUND):"
grep -r "bwg_rentals_org" includes/ bwg-rentals.php --include="*.php" | grep -E "get_option|update_option|register_setting" | head -5
echo ""
echo "3. Cache Duration:"
grep -r "bwg_rentals_cache_duration" includes/ --include="*.php" | grep -E "get_option|update_option|register_setting" | head -3
echo ""
echo "4. Button Text (TWO NAMES FOUND):"
grep -r "bwg_rentals.*button" includes/ --include="*.php" | grep -E "get_option|update_option|register_setting" | head -5
echo ""
echo "5. Cache Metadata:"
grep -r "bwg_rentals_cache_metadata" includes/ --include="*.php" | grep -E "get_option|update_option|delete_option" | head -5
