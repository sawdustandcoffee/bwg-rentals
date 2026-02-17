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
                        <td>grid, list, masonry</td>
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
                        <td>name, beds, sleeps, sqft</td>
                        <td>name</td>
                        <td><?php _e( 'Sort properties by field', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>pagination</code></td>
                        <td>true, false</td>
                        <td>false</td>
                        <td><?php _e( 'Enable pagination', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>per_page</code></td>
                        <td>number</td>
                        <td>12</td>
                        <td><?php _e( 'Properties per page (when pagination enabled)', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_filters</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show filter dropdowns', 'bwg-rentals' ); ?></td>
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

        <!-- [bwg_property_slider] Shortcode -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_slider]</code></h3>
            <p><?php _e( 'Displays properties in a carousel/slider.', 'bwg-rentals' ); ?></p>

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
                        <td><code>limit</code></td>
                        <td>number</td>
                        <td>-1 (all)</td>
                        <td><?php _e( 'Max properties to show', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>orderby</code></td>
                        <td>name, beds, sleeps, sqft</td>
                        <td>name</td>
                        <td><?php _e( 'Sort order', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>autoplay</code></td>
                        <td>true, false</td>
                        <td>false</td>
                        <td><?php _e( 'Auto-advance slides', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>speed</code></td>
                        <td>number (ms)</td>
                        <td>5000</td>
                        <td><?php _e( 'Autoplay interval', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>slides_to_show</code></td>
                        <td>1-4</td>
                        <td>1</td>
                        <td><?php _e( 'Visible slides at once', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>slides_to_scroll</code></td>
                        <td>1-4</td>
                        <td>1</td>
                        <td><?php _e( 'Slides to advance per step', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>navigation</code></td>
                        <td>arrows, dots, both, none</td>
                        <td>both</td>
                        <td><?php _e( 'Navigation controls', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>loop</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Loop back to start', 'bwg-rentals' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_slider]</code></pre>
            <pre><code>[bwg_property_slider autoplay="true" speed="3000"]</code></pre>
            <pre><code>[bwg_property_slider slides_to_show="3" navigation="dots"]</code></pre>
        </div>

        <!-- [bwg_properties_featured] Shortcode -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_properties_featured]</code></h3>
            <p><?php _e( 'Displays a curated selection of featured properties.', 'bwg-rentals' ); ?></p>

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
                        <td><code>ids</code></td>
                        <td>comma-separated IDs</td>
                        <td></td>
                        <td><?php _e( 'Specific properties to feature', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>limit</code></td>
                        <td>number</td>
                        <td>3</td>
                        <td><?php _e( 'Max properties if no IDs specified', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>layout</code></td>
                        <td>grid, slider</td>
                        <td>grid</td>
                        <td><?php _e( 'Display layout', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>columns</code></td>
                        <td>2-4</td>
                        <td>3</td>
                        <td><?php _e( 'Grid columns', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>orderby</code></td>
                        <td>name, beds, sleeps, sqft</td>
                        <td>name</td>
                        <td><?php _e( 'Sort order', 'bwg-rentals' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_properties_featured]</code></pre>
            <pre><code>[bwg_properties_featured ids="1,2,3"]</code></pre>
            <pre><code>[bwg_properties_featured layout="slider" limit="5"]</code></pre>
        </div>

        <!-- [bwg_property_search] Shortcode -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property_search]</code></h3>
            <p><?php _e( 'Displays a property search form with filters.', 'bwg-rentals' ); ?></p>

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
                        <td><code>show_dates</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show date pickers', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_guests</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show guest count filter', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_bedrooms</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show bedroom filter', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_amenities</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show amenities filter', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_location</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show location filter', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>results_page</code></td>
                        <td>URL</td>
                        <td></td>
                        <td><?php _e( 'Page to show results (empty = same page)', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>button_text</code></td>
                        <td>text</td>
                        <td>Search Properties</td>
                        <td><?php _e( 'Submit button text', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>layout</code></td>
                        <td>horizontal, vertical, inline</td>
                        <td>horizontal</td>
                        <td><?php _e( 'Form layout', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>compact</code></td>
                        <td>true, false</td>
                        <td>false</td>
                        <td><?php _e( 'Compact mode with expandable filters', 'bwg-rentals' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property_search]</code></pre>
            <pre><code>[bwg_property_search layout="vertical" compact="true"]</code></pre>
            <pre><code>[bwg_property_search show_dates="false" show_amenities="false"]</code></pre>
        </div>

        <!-- [bwg_reviews_slider] Shortcode -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_reviews_slider]</code></h3>
            <p><?php _e( 'Displays guest reviews in a carousel.', 'bwg-rentals' ); ?></p>

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
                        <td><code>limit</code></td>
                        <td>number</td>
                        <td>-1 (all)</td>
                        <td><?php _e( 'Max reviews to show', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>autoplay</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Auto-advance slides', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>speed</code></td>
                        <td>number (ms)</td>
                        <td>5000</td>
                        <td><?php _e( 'Autoplay interval', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>slides_to_show</code></td>
                        <td>1-4</td>
                        <td>1</td>
                        <td><?php _e( 'Visible slides at once', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>slides_to_scroll</code></td>
                        <td>1-4</td>
                        <td>1</td>
                        <td><?php _e( 'Slides to advance per step', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>navigation</code></td>
                        <td>arrows, dots, both, none</td>
                        <td>both</td>
                        <td><?php _e( 'Navigation controls', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>loop</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Loop back to start', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>show_property_name</code></td>
                        <td>true, false</td>
                        <td>true</td>
                        <td><?php _e( 'Show which property the review is for', 'bwg-rentals' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>style</code></td>
                        <td>testimonial, card, minimal</td>
                        <td>testimonial</td>
                        <td><?php _e( 'Visual style', 'bwg-rentals' ); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_reviews_slider]</code></pre>
            <pre><code>[bwg_reviews_slider style="card" slides_to_show="2"]</code></pre>
            <pre><code>[bwg_reviews_slider style="minimal" autoplay="false"]</code></pre>
        </div>
    </div>

    <!-- Property Shortcodes Section -->
    <div id="property-shortcodes" class="bwg-docs-section" style="display: none;">
        <h2><?php _e( 'Property Shortcodes', 'bwg-rentals' ); ?></h2>
        <p><?php _e( 'These shortcodes display individual components of a single property. Use them to build custom property detail pages.', 'bwg-rentals' ); ?></p>

        <!-- [bwg_property] -->
        <div class="bwg-shortcode-doc">
            <h3><code>[bwg_property]</code></h3>
            <p><?php _e( 'Full property page - displays a complete property detail page with all sections.', 'bwg-rentals' ); ?></p>
            <h4><?php _e( 'Attributes:', 'bwg-rentals' ); ?></h4>
            <ul>
                <li><code>id</code> - Property ID (default: from URL)</li>
                <li><code>layout</code> - standard, compact, minimal (default: standard)</li>
                <li><code>show_breadcrumbs</code> - true, false (default: true)</li>
                <li><code>show_gallery</code> - true, false (default: true)</li>
                <li><code>show_title</code> - true, false (default: true)</li>
                <li><code>show_specs</code> - true, false (default: true)</li>
                <li><code>show_description</code> - true, false (default: true)</li>
                <li><code>show_amenities</code> - true, false (default: true)</li>
                <li><code>show_availability</code> - true, false (default: true)</li>
                <li><code>show_rates</code> - true, false (default: true)</li>
                <li><code>show_location</code> - true, false (default: true)</li>
                <li><code>show_policies</code> - true, false (default: true)</li>
                <li><code>show_booking_button</code> - true, false (default: true)</li>
                <li><code>show_anchors</code> - true, false (default: true) - Show section navigation</li>
                <li><code>show_related</code> - true, false (default: true) - Show related properties</li>
                <li><code>related_limit</code> - Number of related properties (default: 4)</li>
            </ul>
            <h4><?php _e( 'Examples:', 'bwg-rentals' ); ?></h4>
            <pre><code>[bwg_property]</code></pre>
            <pre><code>[bwg_property id="123"]</code></pre>
            <pre><code>[bwg_property layout="minimal"]</code></pre>
            <pre><code>[bwg_property show_availability="false" show_rates="false"]</code></pre>
        </div>

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
                <li><code>months_to_show</code> - Number of months to display (default: 3)</li>
                <li><code>start_month</code> - Starting month (default: current)</li>
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
                <li><code>show_map</code> - true, false (default: false)</li>
                <li><code>map_height</code> - Map height (default: 300px)</li>
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
