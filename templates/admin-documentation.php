<?php
/**
 * Admin Documentation Page Template
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap bwg-documentation">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <div class="bwg-docs-intro">
        <p><?php _e( 'Welcome to the BWG Rentals plugin documentation. This page provides comprehensive information about all available shortcodes and how to use them effectively.', 'bwg-rentals' ); ?></p>
    </div>

    <nav class="nav-tab-wrapper">
        <a href="#overview" class="nav-tab nav-tab-active"><?php _e( 'Overview', 'bwg-rentals' ); ?></a>
        <a href="#archive-shortcodes" class="nav-tab"><?php _e( 'Archive Shortcodes', 'bwg-rentals' ); ?></a>
        <a href="#property-shortcodes" class="nav-tab"><?php _e( 'Property Shortcodes', 'bwg-rentals' ); ?></a>
        <a href="#troubleshooting" class="nav-tab"><?php _e( 'Troubleshooting', 'bwg-rentals' ); ?></a>
    </nav>

    <!-- Overview Section -->
    <div id="overview" class="bwg-docs-section">
        <h2><?php _e( 'Overview', 'bwg-rentals' ); ?></h2>
        <p><?php _e( 'BWG Rentals integrates with the Direct Software vacation rental API to display properties on your WordPress website. The plugin provides flexible shortcodes that work with any page builder.', 'bwg-rentals' ); ?></p>

        <h3><?php _e( 'Getting Started', 'bwg-rentals' ); ?></h3>
        <ol>
            <li><?php _e( 'Configure your API credentials in the Settings page', 'bwg-rentals' ); ?></li>
            <li><?php _e( 'Test your API connection to ensure everything is working', 'bwg-rentals' ); ?></li>
            <li><?php _e( 'Add shortcodes to your pages using the examples below', 'bwg-rentals' ); ?></li>
        </ol>
    </div>

    <!-- Archive Shortcodes Section -->
    <div id="archive-shortcodes" class="bwg-docs-section" style="display: none;">
        <h2><?php _e( 'Archive Shortcodes', 'bwg-rentals' ); ?></h2>
        <p><?php _e( 'These shortcodes display lists or grids of multiple properties.', 'bwg-rentals' ); ?></p>

        <!-- [bwg_properties] Shortcode -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_properties]</code></h3>
            <p><?php _e( 'Displays a grid or list of all properties.', 'bwg-rentals' ); ?></p>

            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e( 'Attribute', 'bwg-rentals' ); ?></th>
                        <th><?php _e( 'Values', 'bwg-rentals' ); ?></th>
                        <th><?php _e( 'Default', 'bwg-rentals' ); ?></th>
                        <th><?php _e( 'Description', 'bwg-rentals' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>layout</code></td>
                        <td>grid, list</td>
                        <td>grid</td>
                        <td><?php _e( 'Display layout style', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>columns</code></td>
                        <td>2, 3, 4</td>
                        <td>3</td>
                        <td><?php _e( 'Number of columns (grid layout only)', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td>number</td>
                        <td>-1 (all)</td>
                        <td><?php _e( 'Maximum number of properties to display', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>orderby</code></td>
                        <td>name, id</td>
                        <td>name</td>
                        <td><?php _e( 'Sort properties by field', 'bwg-rentals' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_properties]</code></pre>
            <pre><code>[bwg_properties layout="grid" columns="4"]</code></pre>
            <pre><code>[bwg_properties layout="list" limit="6"]</code></pre>
        </div>

        <!-- [bwg_property_card] Shortcode -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_card]</code></h3>
            <p><?php _e( 'Displays a single property card.', 'bwg-rentals' ); ?></p>

            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e( 'Attribute', 'bwg-rentals' ); ?></th>
                        <th><?php _e( 'Values', 'bwg-rentals' ); ?></th>
                        <th><?php _e( 'Default', 'bwg-rentals' ); ?></th>
                        <th><?php _e( 'Description', 'bwg-rentals' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>id</code></td>
                        <td>number</td>
                        <td>required</td>
                        <td><?php _e( 'Property ID', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_image</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show property image', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_specs</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show property specs (beds, baths, etc.)', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>link</code></td>
                        <td>URL</td>
                        <td></td>
                        <td><?php _e( 'Custom link for the card', 'bwg-rentals' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_card id="123"]</code></pre>
            <pre><code>[bwg_property_card id="123" show_image="false"]</code></pre>
            <pre><code>[bwg_property_card id="123" link="/properties/beach-house/"]</code></pre>
        </div>
    </div>

    <!-- Property Shortcodes Section -->
    <div id="property-shortcodes" class="bwg-docs-section" style="display: none;">
        <h2><?php _e( 'Property Shortcodes', 'bwg-rentals' ); ?></h2>
        <p><?php _e( 'These shortcodes display individual components of a single property. Use them to build custom property detail pages.', 'bwg-rentals' ); ?></p>

        <!-- [bwg_property_gallery] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_gallery]</code></h3>
            <p><?php _e( 'Displays property image gallery or slideshow.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>layout</code> - slider, grid, lightbox (default: slider)</li>
                <li><code>thumbnail_size</code> - Size of thumbnails in grid layout</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_gallery id="123" layout="slider"]</code></pre>
        </div>

        <!-- [bwg_property_title] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_title]</code></h3>
            <p><?php _e( 'Displays property name/headline.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>tag</code> - h1, h2, h3 (default: h1)</li>
                <li><code>class</code> - Custom CSS class</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_title id="123" tag="h2"]</code></pre>
        </div>

        <!-- [bwg_property_specs] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_specs]</code></h3>
            <p><?php _e( 'Displays property specifications (beds, baths, guests, square footage).', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>show_icons</code> - true, false (default: true)</li>
                <li><code>layout</code> - inline, stacked (default: inline)</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_specs id="123" show_icons="true"]</code></pre>
        </div>

        <!-- [bwg_property_description] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_description]</code></h3>
            <p><?php _e( 'Displays property main description.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>excerpt_length</code> - Number of words for excerpt</li>
                <li><code>show_full</code> - true, false (default: true)</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_description id="123"]</code></pre>
        </div>

        <!-- [bwg_property_amenities] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_amenities]</code></h3>
            <p><?php _e( 'Displays property amenities list.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>show_icons</code> - true, false (default: true)</li>
                <li><code>columns</code> - Number of columns (default: 2)</li>
                <li><code>limit</code> - Maximum amenities to show</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_amenities id="123" columns="3"]</code></pre>
        </div>

        <!-- [bwg_property_availability] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_availability]</code></h3>
            <p><?php _e( 'Displays property availability calendar.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>months_to_show</code> - Number of months to display (default: 2)</li>
                <li><code>start_month</code> - Starting month offset (default: 0)</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_availability id="123" months_to_show="3"]</code></pre>
        </div>

        <!-- [bwg_property_rates] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_rates]</code></h3>
            <p><?php _e( 'Displays property pricing/rate table.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>show_seasonal</code> - true, false (default: true)</li>
                <li><code>show_discounts</code> - true, false (default: true)</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_rates id="123"]</code></pre>
        </div>

        <!-- [bwg_property_booking_button] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_booking_button]</code></h3>
            <p><?php _e( 'Displays call-to-action button linking to Direct Software booking site.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>text</code> - Button text (default: setting value)</li>
                <li><code>class</code> - Custom CSS class</li>
                <li><code>target</code> - _blank, _self (default: _blank)</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_booking_button id="123" text="Reserve Now"]</code></pre>
        </div>

        <!-- [bwg_property_location] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_location]</code></h3>
            <p><?php _e( 'Displays property address and optional map.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>show_map</code> - true, false (default: true)</li>
                <li><code>map_height</code> - Map height in pixels (default: 300)</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_location id="123" show_map="true"]</code></pre>
        </div>

        <!-- [bwg_property_policies] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_policies]</code></h3>
            <p><?php _e( 'Displays house rules, cancellation policy, and other policies.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (required)</li>
                <li><code>sections</code> - Comma-separated list of sections to show</li>
            </ul>
            <h4><?php _e( 'Example:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_policies id="123"]</code></pre>
        </div>
    </div>

    <!-- Troubleshooting Section -->
    <div id="troubleshooting" class="bwg-docs-section" style="display: none;">
        <h2><?php _e( 'Troubleshooting', 'bwg-rentals' ); ?></h2>

        <h3><?php _e( 'Common Issues', 'bwg-rentals' ); ?></h3>

        <div class="bwg-troubleshooting-item">
            <h4><?php _e( 'Properties not displaying', 'bwg-rentals' ); ?></h4>
            <ol>
                <li><?php _e( 'Verify your API credentials are correct in Settings', 'bwg-rentals' ); ?></li>
                <li><?php _e( 'Click "Test API Connection" to check connectivity', 'bwg-rentals' ); ?></li>
                <li><?php _e( 'Try clearing the cache in Settings', 'bwg-rentals' ); ?></li>
                <li><?php _e( 'Check that your shortcode has the correct property ID', 'bwg-rentals' ); ?></li>
            </ol>
        </div>

        <div class="bwg-troubleshooting-item">
            <h4><?php _e( 'Styling issues', 'bwg-rentals' ); ?></h4>
            <p><?php _e( 'The plugin uses BEM-style CSS classes with the .bwg- prefix. You can override these styles in your theme\'s CSS file.', 'bwg-rentals' ); ?></p>
        </div>

        <div class="bwg-troubleshooting-item">
            <h4><?php _e( 'Cache not updating', 'bwg-rentals' ); ?></h4>
            <p><?php _e( 'If you\'ve made changes in Direct Software but they\'re not appearing on your site, go to Settings > BWG Rentals and click "Clear All Cache".', 'bwg-rentals' ); ?></p>
        </div>

        <h3><?php _e( 'Need More Help?', 'bwg-rentals' ); ?></h3>
        <p><?php _e( 'Contact support for additional assistance with BWG Rentals.', 'bwg-rentals' ); ?></p>
    </div>
</div>

<style>
.bwg-documentation {
    max-width: 1200px;
}

.bwg-docs-intro {
    background: #f9f9f9;
    border-left: 4px solid #2271b1;
    padding: 15px 20px;
    margin: 20px 0;
}

.bwg-docs-section {
    margin-top: 30px;
}

.bwg-shortcode-doc {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin: 20px 0;
    border-radius: 4px;
}

.bwg-shortcode-doc h3 {
    margin-top: 0;
    color: #2271b1;
}

.bwg-shortcode-doc code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 13px;
}

.bwg-shortcode-doc pre {
    background: #f0f0f1;
    padding: 12px;
    border-radius: 3px;
    overflow-x: auto;
}

.bwg-shortcode-doc pre code {
    background: none;
    padding: 0;
}

.bwg-shortcode-doc table {
    margin: 15px 0;
}

.bwg-shortcode-doc table th {
    background: #f0f0f1;
}

.bwg-shortcode-doc ul {
    list-style: disc;
    padding-left: 20px;
}

.bwg-troubleshooting-item {
    background: #fff;
    border-left: 4px solid #d63638;
    padding: 15px 20px;
    margin: 15px 0;
}

.bwg-troubleshooting-item h4 {
    margin-top: 0;
}

.nav-tab-wrapper {
    margin: 20px 0;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab navigation
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();

        // Update tab states
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Show corresponding section
        var target = $(this).attr('href');
        $('.bwg-docs-section').hide();
        $(target).show();
    });
});
</script>
