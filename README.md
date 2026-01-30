# BWG Rentals - WordPress Plugin

A WordPress plugin that integrates with the Direct Software vacation rental API to display properties on WordPress websites.

## Description

BWG Rentals provides flexible shortcodes for page builder compatibility, allowing admins to create custom property archive pages and detailed single property pages. Visitors can browse properties, check availability, and click through to the Direct Software booking site to finalize reservations.

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Direct Software account with API access
- API Key and Organization ID from Direct Software

## Installation

1. Download the plugin zip file from GitHub releases
2. Go to WordPress Admin > Plugins > Add New > Upload Plugin
3. Upload the zip file and click "Install Now"
4. Activate the plugin
5. Go to Settings > BWG Rentals to configure API credentials

## Configuration

1. Navigate to **Settings > BWG Rentals**
2. Enter your **API Key** from Direct Software
3. Enter your **Organization ID**
4. Click **Test Connection** to verify credentials
5. Adjust cache duration if needed (default: 24 hours)
6. Save settings

## Shortcodes

### Archive Shortcodes

#### `[bwg_properties]`
Display a grid/list of all properties.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `layout` | `grid` | `grid` or `list` |
| `columns` | `3` | Number of columns (2, 3, or 4) |
| `limit` | `-1` | Maximum properties to show (-1 for all) |
| `orderby` | `name` | Sort by: `name`, `price` |

#### `[bwg_property_card]`
Display a single property card summary.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `id` | (required) | Property ID |
| `show_image` | `true` | Show property image |
| `show_specs` | `true` | Show beds/baths/guests |
| `link` | `true` | Link to property page |

### Single Property Shortcodes

All single property shortcodes require an `id` attribute.

#### `[bwg_property_gallery id="X"]`
Image gallery/slideshow.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `layout` | `slider` | `slider`, `grid`, or `lightbox` |
| `thumbnail_size` | `medium` | Image size |

#### `[bwg_property_title id="X"]`
Property name/headline.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `tag` | `h1` | HTML tag: `h1`, `h2`, `h3` |
| `class` | `''` | Additional CSS classes |

#### `[bwg_property_specs id="X"]`
Beds, baths, guests, sqft.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `show_icons` | `true` | Show icons with specs |
| `layout` | `inline` | `inline` or `stacked` |

#### `[bwg_property_description id="X"]`
Main description text.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `excerpt_length` | `0` | Truncate to characters (0 = full) |
| `show_full` | `true` | Show "read more" link |

#### `[bwg_property_amenities id="X"]`
Amenities list.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `show_icons` | `true` | Show amenity icons |
| `columns` | `2` | Display columns |
| `limit` | `0` | Max amenities (0 = all) |

#### `[bwg_property_availability id="X"]`
Availability calendar.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `months_to_show` | `3` | Number of months |
| `start_month` | `current` | Starting month |

#### `[bwg_property_rates id="X"]`
Pricing/rate table.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `show_seasonal` | `true` | Show seasonal rates |
| `show_discounts` | `true` | Show discount info |

#### `[bwg_property_booking_button id="X"]`
CTA button to Direct Software booking.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `text` | `Book Now` | Button text |
| `class` | `''` | Additional CSS classes |
| `target` | `_blank` | Link target |

#### `[bwg_property_location id="X"]`
Address and optional map.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `show_map` | `false` | Show map if coords available |
| `map_height` | `300px` | Map height |

#### `[bwg_property_policies id="X"]`
House rules, cancellation, etc.

| Attribute | Default | Description |
|-----------|---------|-------------|
| `sections` | `all` | Comma-separated list of sections |

## Template Overrides

Templates can be overridden in your theme. Copy templates from:
```
plugins/bwg-rentals/templates/
```

To your theme:
```
themes/your-theme/bwg-rentals/
```

## CSS Customization

The plugin uses BEM naming convention with `bwg-` prefix. All classes can be customized with CSS:

```css
/* Example: Change primary button color */
:root {
    --bwg-primary-color: #0066cc;
    --bwg-button-background: #0066cc;
}

/* Example: Customize property cards */
.bwg-property-card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

## Filters

Developers can modify output using WordPress filters:

```php
// Modify property title output
add_filter('bwg_property_title_output', function($output, $property) {
    return '<div class="custom-wrapper">' . $output . '</div>';
}, 10, 2);

// Modify button text
add_filter('bwg_booking_button_text', function($text, $property) {
    return 'Reserve ' . $property->name;
}, 10, 2);
```

## Automatic Updates

This plugin updates automatically from GitHub releases. When a new version is tagged in the repository, WordPress will notify you of the available update.

## Development

Run the setup script to prepare the development environment:

```bash
./init.sh
```

### File Structure

```
bwg-rentals/
├── bwg-rentals.php          # Main plugin file
├── readme.txt               # WordPress readme
├── uninstall.php            # Cleanup on uninstall
├── includes/
│   ├── class-bwg-rentals.php
│   ├── class-bwg-admin.php
│   ├── class-bwg-api.php
│   ├── class-bwg-cache.php
│   ├── class-bwg-shortcodes.php
│   └── class-bwg-updater.php
├── shortcodes/              # Individual shortcode classes
├── assets/
│   ├── css/
│   └── js/
├── templates/               # Frontend templates
├── vendor/                  # Plugin Update Checker
└── languages/               # Translations
```

## License

GPL v2 or later

## Support

For issues and feature requests, please use the GitHub issue tracker.
