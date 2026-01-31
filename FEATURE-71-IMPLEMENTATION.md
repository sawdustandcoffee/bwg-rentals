# Feature #71: Uninstall Cleans Up Data

## Feature Definition
- **ID:** 71
- **Name:** Uninstall cleans up data
- **Category:** Uninstall
- **Description:** Plugin uninstall removes all options and transients
- **Dependencies:** Feature #1

## Implementation Steps

### Step 1: Analyze Options and Transients Used by Plugin

I performed a comprehensive analysis of the codebase to identify all WordPress options and transients created by the plugin.

#### Options Found:

1. **API Key**
   - `bwg_rentals_api_key` (used in API, Admin, Rentals classes)

2. **Organization ID** (INCONSISTENCY FOUND - TWO NAMES)
   - `bwg_rentals_org_id` (used in Admin class - registered setting)
   - `bwg_rentals_organization_id` (used in API class, main Rentals class)

3. **Cache Duration**
   - `bwg_rentals_cache_duration` (used in Admin, Cache classes)

4. **Button Text** (INCONSISTENCY FOUND - TWO NAMES)
   - `bwg_rentals_booking_button_text` (used in Admin class - registered setting)
   - `bwg_rentals_button_text` (used in Shortcodes class)

5. **Cache Metadata**
   - `bwg_rentals_cache_metadata` (used in Cache class)

#### Transients Found:

All transients use the prefix `bwg_rentals_`:
- `_transient_bwg_rentals_properties`
- `_transient_bwg_rentals_property_{id}`
- `_transient_bwg_rentals_{various cache keys}`
- `_transient_timeout_bwg_rentals_{corresponding timeouts}`

#### Scheduled Events:

- `bwg_rentals_cache_refresh` (scheduled hook for cache refresh)

### Step 2: Identified Issues in Original uninstall.php

The original `uninstall.php` file had several problems:

1. **Missing organization ID variant:** Only deleted `bwg_rentals_organization_id`, not `bwg_rentals_org_id`
2. **Wrong button text name:** Deleted `bwg_rentals_button_text` but not `bwg_rentals_booking_button_text`
3. **Incomplete option list:** Did not account for naming inconsistencies in the codebase

These inconsistencies exist because:
- Admin class registers settings with one naming convention
- Other classes read options with different names
- This creates potential for orphaned data during uninstall

### Step 3: Updated uninstall.php

Modified the options array to include ALL variants:

```php
$options = array(
    // API credentials (primary option name)
    'bwg_rentals_api_key',

    // Organization ID (both variants - admin uses org_id, API uses organization_id)
    'bwg_rentals_org_id',
    'bwg_rentals_organization_id',

    // Cache settings
    'bwg_rentals_cache_duration',
    'bwg_rentals_cache_metadata',

    // Button text (both variants - admin uses booking_button_text, shortcodes use button_text)
    'bwg_rentals_booking_button_text',
    'bwg_rentals_button_text',
);
```

**Total options deleted:** 7 (up from 5 in original)

### Step 4: Transient Cleanup

The transient cleanup was already correctly implemented using SQL:

```php
$wpdb->query(
    "DELETE FROM {$wpdb->options}
    WHERE option_name LIKE '_transient_bwg_rentals_%'
    OR option_name LIKE '_transient_timeout_bwg_rentals_%'"
);
```

This handles:
- All transient values (`_transient_bwg_rentals_*`)
- All transient timeouts (`_transient_timeout_bwg_rentals_*`)

### Step 5: Scheduled Event Cleanup

Already correctly implemented:

```php
wp_clear_scheduled_hook( 'bwg_rentals_cache_refresh' );
```

## Changes Made

### File Modified: `uninstall.php`

**Before:**
```php
$options = array(
    'bwg_rentals_api_key',
    'bwg_rentals_organization_id',
    'bwg_rentals_cache_duration',
    'bwg_rentals_button_text',
    'bwg_rentals_cache_metadata',
);
```

**After:**
```php
$options = array(
    // API credentials (primary option name)
    'bwg_rentals_api_key',

    // Organization ID (both variants - admin uses org_id, API uses organization_id)
    'bwg_rentals_org_id',
    'bwg_rentals_organization_id',

    // Cache settings
    'bwg_rentals_cache_duration',
    'bwg_rentals_cache_metadata',

    // Button text (both variants - admin uses booking_button_text, shortcodes use button_text)
    'bwg_rentals_booking_button_text',
    'bwg_rentals_button_text',
);
```

## Testing Strategy

### Manual Test Process

Since WordPress plugin uninstall can only be tested by actually deleting the plugin, the test process is:

1. **Setup Phase:**
   - Activate the plugin in WordPress
   - Configure API credentials (creates `bwg_rentals_api_key` and `bwg_rentals_org_id`)
   - Set cache duration and button text options
   - Visit some property pages to create transient cache

2. **Verification Phase (Pre-Uninstall):**
   - Query database to confirm options exist
   - Query database to confirm transients exist
   - Document current state

3. **Uninstall Phase:**
   - Deactivate the plugin
   - Delete the plugin (this triggers `uninstall.php`)

4. **Verification Phase (Post-Uninstall):**
   - Query database to confirm ALL options removed
   - Query database to confirm ALL transients removed
   - Verify no orphaned data remains

### Database Queries for Testing

```sql
-- Check for any remaining options
SELECT option_name, option_value
FROM wp_options
WHERE option_name LIKE 'bwg_rentals%';

-- Check for any remaining transients
SELECT option_name, option_value
FROM wp_options
WHERE option_name LIKE '%transient%bwg_rentals%';

-- Expected result: 0 rows (empty result set)
```

## Code Quality

✅ **WordPress Standards:** Uses WordPress uninstall hook correctly
✅ **Security:** Checks for `WP_UNINSTALL_PLUGIN` constant
✅ **Completeness:** Deletes all options, transients, and scheduled events
✅ **Database Safety:** Uses proper `$wpdb` methods
✅ **Documentation:** Clear comments explaining each option group

## Potential Future Improvements

### Recommended: Fix Naming Inconsistencies

The codebase has naming inconsistencies that could cause bugs:

1. **Organization ID:**
   - Admin saves to: `bwg_rentals_org_id`
   - API reads from: `bwg_rentals_organization_id`
   - Solution: Standardize on one name

2. **Button Text:**
   - Admin saves to: `bwg_rentals_booking_button_text`
   - Shortcodes read from: `bwg_rentals_button_text`
   - Solution: Standardize on one name

These should be fixed in a future feature to prevent confusion and ensure settings work correctly.

## Summary

The uninstall process now properly cleans up:
- ✅ 7 plugin options (including all variants)
- ✅ All transients with `bwg_rentals_` prefix
- ✅ Scheduled cache refresh events
- ✅ No orphaned data left in database

**Result:** Plugin uninstall is now complete and thorough.
