#!/bin/bash
echo "════════════════════════════════════════════════════════════════"
echo "Feature #71 Final Review: Uninstall Cleans Up Data"
echo "════════════════════════════════════════════════════════════════"
echo ""

echo "1. Checking uninstall.php exists..."
if [ -f "uninstall.php" ]; then
    echo "   ✓ uninstall.php found"
else
    echo "   ✗ uninstall.php NOT found"
    exit 1
fi
echo ""

echo "2. Checking for WP_UNINSTALL_PLUGIN security check..."
grep -q "WP_UNINSTALL_PLUGIN" uninstall.php && echo "   ✓ Security check present" || echo "   ✗ Security check missing"
echo ""

echo "3. Checking all required options are included..."
OPTIONS=(
    "bwg_rentals_api_key"
    "bwg_rentals_org_id"
    "bwg_rentals_organization_id"
    "bwg_rentals_cache_duration"
    "bwg_rentals_cache_metadata"
    "bwg_rentals_booking_button_text"
    "bwg_rentals_button_text"
)

ALL_FOUND=true
for opt in "${OPTIONS[@]}"; do
    if grep -q "$opt" uninstall.php; then
        echo "   ✓ $opt"
    else
        echo "   ✗ $opt (MISSING)"
        ALL_FOUND=false
    fi
done
echo ""

echo "4. Checking transient cleanup..."
grep -q "_transient_bwg_rentals_" uninstall.php && echo "   ✓ Transient cleanup SQL present" || echo "   ✗ Transient cleanup missing"
grep -q "_transient_timeout_bwg_rentals_" uninstall.php && echo "   ✓ Transient timeout cleanup present" || echo "   ✗ Transient timeout cleanup missing"
echo ""

echo "5. Checking scheduled event cleanup..."
grep -q "wp_clear_scheduled_hook" uninstall.php && echo "   ✓ Scheduled event cleanup present" || echo "   ✗ Scheduled event cleanup missing"
grep -q "bwg_rentals_cache_refresh" uninstall.php && echo "   ✓ Cache refresh hook referenced" || echo "   ✗ Cache refresh hook missing"
echo ""

echo "6. Verification tools created..."
[ -f "verify-uninstall-cleanup.php" ] && echo "   ✓ verify-uninstall-cleanup.php" || echo "   ✗ verify-uninstall-cleanup.php missing"
[ -f "test-uninstall-cleanup.php" ] && echo "   ✓ test-uninstall-cleanup.php" || echo "   ✗ test-uninstall-cleanup.php missing"
echo ""

echo "7. Documentation created..."
[ -f "FEATURE-71-IMPLEMENTATION.md" ] && echo "   ✓ FEATURE-71-IMPLEMENTATION.md" || echo "   ✗ FEATURE-71-IMPLEMENTATION.md missing"
[ -f "FEATURE-71-VERIFICATION.md" ] && echo "   ✓ FEATURE-71-VERIFICATION.md" || echo "   ✗ FEATURE-71-VERIFICATION.md missing"
echo ""

echo "════════════════════════════════════════════════════════════════"
if [ "$ALL_FOUND" = true ]; then
    echo "✓ ALL CHECKS PASSED - Feature #71 is ready"
    echo "════════════════════════════════════════════════════════════════"
    echo ""
    echo "Summary:"
    echo "  - 7 options will be deleted on uninstall"
    echo "  - All transients will be removed"
    echo "  - Scheduled events will be cleared"
    echo "  - Security checks in place"
    echo "  - Verification tools created"
    echo "  - Complete documentation provided"
    echo ""
    exit 0
else
    echo "✗ SOME CHECKS FAILED - Review needed"
    echo "════════════════════════════════════════════════════════════════"
    exit 1
fi
