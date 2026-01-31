#!/bin/bash
# Test Feature #58: Empty properties shows empty state
# This script sets up a test to verify the empty state message

echo "Feature #58: Empty properties shows empty state"
echo "================================================"
echo ""
echo "Test Steps:"
echo "1. Save current API key"
echo "2. Set API key to MOCK_EMPTY_TEST (triggers empty array response)"
echo "3. Create/update test page with [bwg_properties] shortcode"
echo "4. Visit page and verify empty state message displays"
echo "5. Restore original API key"
echo ""

# Note: This would require WP-CLI or direct database access to execute
# For manual testing:
echo "Manual Testing Instructions:"
echo "1. Go to WordPress Admin > Settings > BWG Rentals"
echo "2. Change API Key to: MOCK_EMPTY_TEST"
echo "3. Save Settings"
echo "4. Create a new page or edit an existing one"
echo "5. Add shortcode: [bwg_properties]"
echo "6. Publish and view the page"
echo "7. Expected Result: Empty state message with house icon"
echo "   - Message: 'No properties available at this time. Please check back later.'"
echo "   - Styled with light gray background and dashed border"
echo "   - House emoji icon (🏠)"
echo "8. Restore API key to original value (e.g., MOCK_API_KEY_123)"
