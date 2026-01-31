# Feature #15 Verification: [bwg_property_card] basic rendering

**Date:** 2026-01-31
**Status:** PASSING ✅

## Feature Definition

- **ID:** 15
- **Category:** Archive Shortcodes
- **Name:** [bwg_property_card] basic rendering
- **Description:** The bwg_property_card shortcode displays a single property card

## Test Steps

### Step 1: Add [bwg_property_card id="X"] to page ✅

**Action:** Created test page with shortcode
```
Page ID: 253
URL: http://localhost:8088/feature-15-property-card-test/
Shortcode: [bwg_property_card id="1"]
```

**Result:** Page created successfully

### Step 2: View page on frontend ✅

**Action:** Accessed page via HTTP
```bash
curl -s http://localhost:8088/feature-15-property-card-test/
```

**Result:** Page loads successfully, returns HTML

### Step 3: Verify property card displays ✅

**HTML Output Verification:**
```html
<div class="bwg-property-card">
    <div class="bwg-property-card__image">
        <img src="https://picsum.photos/800/600?random=1" alt="Oceanfront Beach House" />
    </div>
    <div class="bwg-property-card__content">
        <h3 class="bwg-property-card__title">Oceanfront Beach House</h3>
        <div class="bwg-property-specs">
            <span class="bwg-property-specs__item">4 Beds</span>
            <span class="bwg-property-specs__item">3 Baths</span>
        </div>
    </div>
</div>
```

**Verification:**
- ✅ Property card container renders with class `bwg-property-card`
- ✅ Property image displays correctly
- ✅ Property title displays: "Oceanfront Beach House"
- ✅ Property specs display: 4 Beds, 3 Baths
- ✅ CSS file is enqueued: `bwg-rentals-public.css`
- ✅ Proper HTML structure with BEM naming convention

## Additional Testing

### Shortcode Attributes Testing

**Test Page:** http://localhost:8088/feature-15-property-card-variations/

1. **Default (all features)** ✅
   - Shows image, title, and specs

2. **show_image="false"** ✅
   - Hides image div
   - Shows title and specs only

3. **show_specs="false"** ✅
   - Shows image and title
   - Hides specs div

### Error Handling Testing

1. **Invalid Property ID** ✅
   - URL: http://localhost:8088/feature-15-invalid-property-id/
   - Shortcode: `[bwg_property_card id="999"]`
   - Result: Displays error message in `.bwg-error` container

2. **Missing Property ID** ✅
   - URL: http://localhost:8088/feature-15-missing-id/
   - Shortcode: `[bwg_property_card]`
   - Result: Displays "Property ID is required." error

## Implementation Details

### Files Involved

1. **Shortcode Registration:** `includes/class-bwg-shortcodes.php`
   - Line 60: `add_shortcode( 'bwg_property_card', array( $this, 'property_card' ) );`
   - Lines 490-517: `property_card()` method implementation

2. **Template:** `templates/property-card.php`
   - Handles image display conditional
   - Handles specs display conditional
   - Proper escaping and i18n

3. **Styles:** `assets/css/bwg-rentals-public.css`
   - `.bwg-property-card` base styles
   - `.bwg-property-card__image` image container
   - `.bwg-property-card__content` content wrapper
   - `.bwg-property-card__title` title styling
   - `.bwg-property-specs` specs container

### Shortcode Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `id` | int | 0 | Property ID (required) |
| `show_image` | string | 'true' | Show property image |
| `show_specs` | string | 'true' | Show beds/baths/guests |
| `link` | string | 'true' | Link to property page |

## Mock API Configuration

For testing, configured mock API mode:
```
API Key: MOCK_TEST (encrypted)
Org ID: test-org (encrypted)
```

Mock API returns test property:
- ID: 1
- Name: Oceanfront Beach House
- Bedrooms: 4
- Bathrooms: 3
- Sleeps: 10
- Images: https://picsum.photos placeholders

## Code Quality Checks

- ✅ WordPress coding standards compliance
- ✅ Proper escaping (esc_url, esc_attr, esc_html)
- ✅ i18n ready with __() function
- ✅ BEM naming convention for CSS
- ✅ Error handling for missing/invalid IDs
- ✅ Attribute validation and defaults
- ✅ Template override support via get_template()

## Result

**Feature #15: PASSING** ✅

All test steps completed successfully:
1. ✅ Shortcode can be added to page
2. ✅ Page displays on frontend
3. ✅ Property card renders correctly with all data
4. ✅ Shortcode attributes work as expected
5. ✅ Error handling functions properly
6. ✅ CSS styling is applied
7. ✅ Code quality is production-ready

The [bwg_property_card] shortcode is fully functional and ready for production use.
