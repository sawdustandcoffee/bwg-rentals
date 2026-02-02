<?php
/**
 * BWG Rentals Shortcodes Class
 *
 * Registers and handles all plugin shortcodes.
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcodes registration class
 */
class BWG_Shortcodes {

    /**
     * API instance
     *
     * @var BWG_API
     */
    private $api;

    /**
     * Whether assets have been enqueued
     *
     * @var bool
     */
    private $assets_enqueued = false;

    /**
     * Constructor
     *
     * @param BWG_API $api API instance.
     */
    public function __construct( $api ) {
        $this->api = $api;
        $this->register_shortcodes();
        $this->register_ajax_handlers();
    }

    /**
     * Register AJAX handlers
     */
    private function register_ajax_handlers() {
        // Public AJAX handlers (accessible to both logged-in and logged-out users)
        add_action( 'wp_ajax_bwg_filter_properties', array( $this, 'ajax_filter_properties' ) );
        add_action( 'wp_ajax_nopriv_bwg_filter_properties', array( $this, 'ajax_filter_properties' ) );
        add_action( 'wp_ajax_bwg_search_properties', array( $this, 'ajax_search_properties' ) );
        add_action( 'wp_ajax_nopriv_bwg_search_properties', array( $this, 'ajax_search_properties' ) );
        add_action( 'wp_ajax_bwg_get_availability', array( $this, 'ajax_get_availability' ) );
        add_action( 'wp_ajax_nopriv_bwg_get_availability', array( $this, 'ajax_get_availability' ) );
        add_action( 'wp_ajax_bwg_get_rates', array( $this, 'ajax_get_rates' ) );
        add_action( 'wp_ajax_nopriv_bwg_get_rates', array( $this, 'ajax_get_rates' ) );
    }

    /**
     * Register all shortcodes
     */
    private function register_shortcodes() {
        // Archive shortcodes
        add_shortcode( 'bwg_properties', array( $this, 'properties' ) );
        add_shortcode( 'bwg_property_card', array( $this, 'property_card' ) );
        add_shortcode( 'bwg_property_slider', array( $this, 'property_slider' ) );
        add_shortcode( 'bwg_properties_featured', array( $this, 'properties_featured' ) );

        // Single property shortcodes
        add_shortcode( 'bwg_property', array( $this, 'property_full' ) ); // Full property page
        add_shortcode( 'bwg_property_gallery', array( $this, 'property_gallery' ) );
        add_shortcode( 'bwg_property_title', array( $this, 'property_title' ) );
        add_shortcode( 'bwg_property_specs', array( $this, 'property_specs' ) );
        add_shortcode( 'bwg_property_description', array( $this, 'property_description' ) );
        add_shortcode( 'bwg_property_amenities', array( $this, 'property_amenities' ) );
        add_shortcode( 'bwg_property_availability', array( $this, 'property_availability' ) );
        add_shortcode( 'bwg_property_rates', array( $this, 'property_rates' ) );
        add_shortcode( 'bwg_property_booking_button', array( $this, 'property_booking_button' ) );
        add_shortcode( 'bwg_property_location', array( $this, 'property_location' ) );
        add_shortcode( 'bwg_property_policies', array( $this, 'property_policies' ) );

        // Search shortcode
        add_shortcode( 'bwg_property_search', array( $this, 'property_search' ) );
    }

    /**
     * Enqueue frontend assets when shortcode is used
     */
    private function enqueue_assets() {
        if ( $this->assets_enqueued ) {
            return;
        }

        wp_enqueue_style( 'bwg-rentals-public' );
        wp_enqueue_script( 'bwg-rentals-public' );

        // Localize script for AJAX
        wp_localize_script( 'bwg-rentals-public', 'bwgRentals', array(
            'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
            'filterNonce'       => wp_create_nonce( 'bwg_filter_properties' ),
            'searchNonce'       => wp_create_nonce( 'bwg_search_properties' ),
            'availabilityNonce' => wp_create_nonce( 'bwg_get_availability' ),
            'ratesNonce'        => wp_create_nonce( 'bwg_get_rates' ),
        ) );

        $this->assets_enqueued = true;
    }

    /**
     * Enqueue Google Maps assets if API key is configured
     *
     * Only enqueues if the Google Maps API key is set in plugin settings.
     */
    private function enqueue_google_maps() {
        static $google_maps_enqueued = false;

        if ( $google_maps_enqueued ) {
            return;
        }

        // Check if Google Maps API key is configured
        $google_maps_api_key = BWG_Admin::decrypt_value( get_option( 'bwg_rentals_google_maps_api_key', '' ) );

        if ( ! empty( $google_maps_api_key ) ) {
            // Enqueue Google Maps initialization script
            wp_enqueue_script( 'bwg-google-maps' );

            // Enqueue Google Maps API script (will load after bwg-google-maps due to callback)
            wp_enqueue_script( 'google-maps-api' );

            $google_maps_enqueued = true;
        }
    }

    /**
     * Get template file path
     *
     * Checks for theme override first, then plugin templates.
     *
     * @param string $template Template filename.
     * @return string Full template path.
     */
    private function get_template( $template ) {
        // Check theme override
        $theme_template = locate_template( 'bwg-rentals/' . $template );

        if ( $theme_template ) {
            return $theme_template;
        }

        return BWG_RENTALS_PLUGIN_DIR . 'templates/' . $template;
    }

    /**
     * Render error message
     *
     * @param string $message Error message.
     * @return string HTML output.
     */
    private function render_error( $message ) {
        return '<div class="bwg-error">' . esc_html( $message ) . '</div>';
    }

    /**
     * Get property ID from shortcode attribute or URL parameter
     *
     * @param int|string $id_from_atts Property ID from shortcode attributes.
     * @return int Property ID.
     */
    private function get_property_id_from_request( $id_from_atts = 0 ) {
        // If ID provided in shortcode attributes, use that
        if ( ! empty( $id_from_atts ) ) {
            return absint( $id_from_atts );
        }

        // Otherwise, check for property_id URL parameter
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter only
        if ( isset( $_GET['property_id'] ) ) {
            return absint( $_GET['property_id'] );
        }

        return 0;
    }

    /**
     * Sort properties array based on orderby parameter
     *
     * @param array  $properties Array of property data.
     * @param string $orderby    Field to sort by (name, beds, sleeps, sqft).
     * @return array Sorted properties array.
     */
    private function sort_properties( $properties, $orderby ) {
        if ( empty( $properties ) || ! is_array( $properties ) ) {
            return $properties;
        }

        // Define sort field mapping
        $sort_mappings = array(
            'name'     => 'name',
            'beds'     => 'bedrooms',
            'bedrooms' => 'bedrooms',
            'sleeps'   => 'sleeps',
            'guests'   => 'sleeps',
            'sqft'     => 'sqft',
            'size'     => 'sqft',
        );

        // Get the actual field to sort by
        $sort_field = isset( $sort_mappings[ $orderby ] ) ? $sort_mappings[ $orderby ] : 'name';

        // Sort the properties
        usort(
            $properties,
            function ( $a, $b ) use ( $sort_field ) {
                // Get values, default to empty string/0 if not set
                $val_a = isset( $a[ $sort_field ] ) ? $a[ $sort_field ] : '';
                $val_b = isset( $b[ $sort_field ] ) ? $b[ $sort_field ] : '';

                // Numeric comparison for numeric fields
                if ( in_array( $sort_field, array( 'bedrooms', 'sleeps', 'sqft' ), true ) ) {
                    return intval( $val_a ) - intval( $val_b );
                }

                // String comparison for name (case-insensitive)
                return strcasecmp( strval( $val_a ), strval( $val_b ) );
            }
        );

        return $properties;
    }

    /**
     * Get related properties based on similarity
     *
     * Finds properties similar to the given property based on:
     * - Number of bedrooms (primary factor)
     * - Location (city/state)
     *
     * @param array $property The current property.
     * @param int   $limit    Number of related properties to return (default 4).
     * @return array Array of related properties.
     */
    private function get_related_properties( $property, $limit = 4 ) {
        // Get all properties
        $all_properties = $this->api->get_properties();

        if ( is_wp_error( $all_properties ) || empty( $all_properties ) ) {
            return array();
        }

        $current_id      = isset( $property['id'] ) ? $property['id'] : '';
        $current_beds    = isset( $property['bedrooms'] ) ? intval( $property['bedrooms'] ) : 0;
        $current_city    = isset( $property['address']['city'] ) ? $property['address']['city'] : '';
        $current_state   = isset( $property['address']['state'] ) ? $property['address']['state'] : '';

        $scored_properties = array();

        foreach ( $all_properties as $prop ) {
            // Skip the current property
            if ( isset( $prop['id'] ) && $prop['id'] === $current_id ) {
                continue;
            }

            $score = 0;

            // Score based on bedroom similarity (0-100 points)
            $prop_beds = isset( $prop['bedrooms'] ) ? intval( $prop['bedrooms'] ) : 0;
            $bed_diff  = abs( $current_beds - $prop_beds );

            if ( $bed_diff === 0 ) {
                $score += 100; // Exact match
            } elseif ( $bed_diff === 1 ) {
                $score += 50;  // Close match
            } elseif ( $bed_diff === 2 ) {
                $score += 25;  // Somewhat similar
            }

            // Score based on location (0-50 points)
            $prop_city  = isset( $prop['address']['city'] ) ? $prop['address']['city'] : '';
            $prop_state = isset( $prop['address']['state'] ) ? $prop['address']['state'] : '';

            if ( $prop_city === $current_city && ! empty( $current_city ) ) {
                $score += 50; // Same city
            } elseif ( $prop_state === $current_state && ! empty( $current_state ) ) {
                $score += 25; // Same state
            }

            // Only include properties with some similarity
            if ( $score > 0 ) {
                $scored_properties[] = array(
                    'property' => $prop,
                    'score'    => $score,
                );
            }
        }

        // Sort by score (highest first)
        usort(
            $scored_properties,
            function ( $a, $b ) {
                return $b['score'] - $a['score'];
            }
        );

        // Extract just the properties and limit
        $related = array_slice(
            array_map(
                function ( $item ) {
                    return $item['property'];
                },
                $scored_properties
            ),
            0,
            $limit
        );

        return $related;
    }

    /**
     * Render empty state message
     *
     * @param string $message Empty state message.
     * @param string $icon    Optional icon character (emoji or Unicode).
     * @return string HTML output.
     */
    private function render_empty( $message, $icon = '🏠' ) {
        $output = '<div class="bwg-empty">';
        if ( ! empty( $icon ) ) {
            $output .= '<div class="bwg-empty__icon">' . esc_html( $icon ) . '</div>';
        }
        $output .= '<p class="bwg-empty__message">' . esc_html( $message ) . '</p>';
        $output .= '</div>';
        return $output;
    }

    /**
     * Render pagination HTML
     *
     * @param int $current_page Current page number.
     * @param int $total_pages  Total number of pages.
     * @return string HTML output.
     */
    private function render_pagination( $current_page, $total_pages ) {
        if ( $total_pages <= 1 ) {
            return '';
        }

        $output = '<nav class="bwg-pagination" aria-label="' . esc_attr__( 'Properties pagination', 'bwg-rentals' ) . '">';
        $output .= '<ul class="bwg-pagination__list">';

        // Get current URL without bwg_page parameter
        $base_url = remove_query_arg( 'bwg_page' );

        // Previous button
        if ( $current_page > 1 ) {
            $prev_url = add_query_arg( 'bwg_page', $current_page - 1, $base_url );
            $output .= '<li class="bwg-pagination__item bwg-pagination__item--prev">';
            $output .= '<a href="' . esc_url( $prev_url ) . '" class="bwg-pagination__link">';
            $output .= '<span aria-hidden="true">&laquo;</span>';
            $output .= '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'bwg-rentals' ) . '</span>';
            $output .= '</a>';
            $output .= '</li>';
        } else {
            $output .= '<li class="bwg-pagination__item bwg-pagination__item--prev bwg-pagination__item--disabled">';
            $output .= '<span class="bwg-pagination__link" aria-disabled="true">&laquo;</span>';
            $output .= '</li>';
        }

        // Page numbers
        $range = 2; // Number of pages to show on each side of current page
        $start = max( 1, $current_page - $range );
        $end = min( $total_pages, $current_page + $range );

        // First page + ellipsis if needed
        if ( $start > 1 ) {
            $first_url = add_query_arg( 'bwg_page', 1, $base_url );
            $output .= '<li class="bwg-pagination__item">';
            $output .= '<a href="' . esc_url( $first_url ) . '" class="bwg-pagination__link">1</a>';
            $output .= '</li>';

            if ( $start > 2 ) {
                $output .= '<li class="bwg-pagination__item bwg-pagination__item--ellipsis">';
                $output .= '<span class="bwg-pagination__link">&hellip;</span>';
                $output .= '</li>';
            }
        }

        // Page number links
        for ( $i = $start; $i <= $end; $i++ ) {
            if ( $i === $current_page ) {
                $output .= '<li class="bwg-pagination__item bwg-pagination__item--current">';
                $output .= '<span class="bwg-pagination__link" aria-current="page">' . esc_html( $i ) . '</span>';
                $output .= '</li>';
            } else {
                $page_url = add_query_arg( 'bwg_page', $i, $base_url );
                $output .= '<li class="bwg-pagination__item">';
                $output .= '<a href="' . esc_url( $page_url ) . '" class="bwg-pagination__link">' . esc_html( $i ) . '</a>';
                $output .= '</li>';
            }
        }

        // Last page + ellipsis if needed
        if ( $end < $total_pages ) {
            if ( $end < $total_pages - 1 ) {
                $output .= '<li class="bwg-pagination__item bwg-pagination__item--ellipsis">';
                $output .= '<span class="bwg-pagination__link">&hellip;</span>';
                $output .= '</li>';
            }

            $last_url = add_query_arg( 'bwg_page', $total_pages, $base_url );
            $output .= '<li class="bwg-pagination__item">';
            $output .= '<a href="' . esc_url( $last_url ) . '" class="bwg-pagination__link">' . esc_html( $total_pages ) . '</a>';
            $output .= '</li>';
        }

        // Next button
        if ( $current_page < $total_pages ) {
            $next_url = add_query_arg( 'bwg_page', $current_page + 1, $base_url );
            $output .= '<li class="bwg-pagination__item bwg-pagination__item--next">';
            $output .= '<a href="' . esc_url( $next_url ) . '" class="bwg-pagination__link">';
            $output .= '<span aria-hidden="true">&raquo;</span>';
            $output .= '<span class="screen-reader-text">' . esc_html__( 'Next page', 'bwg-rentals' ) . '</span>';
            $output .= '</a>';
            $output .= '</li>';
        } else {
            $output .= '<li class="bwg-pagination__item bwg-pagination__item--next bwg-pagination__item--disabled">';
            $output .= '<span class="bwg-pagination__link" aria-disabled="true">&raquo;</span>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</nav>';

        return $output;
    }

    /**
     * Properties grid/list/masonry shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function properties( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'layout'       => 'grid',
                'columns'      => 3,
                'limit'        => -1,
                'orderby'      => 'name',
                'pagination'   => 'false',
                'per_page'     => 12,
                'show_filters' => 'true',
            ),
            $atts,
            'bwg_properties'
        );

        $properties = $this->api->get_properties();

        if ( is_wp_error( $properties ) ) {
            return $this->render_error( $properties->get_error_message() );
        }

        if ( empty( $properties ) ) {
            return $this->render_empty( __( 'No properties available at this time. Please check back later.', 'bwg-rentals' ) );
        }

        // Apply sorting based on orderby attribute
        $orderby = sanitize_text_field( $atts['orderby'] );
        $properties = $this->sort_properties( $properties, $orderby );

        // Store total count for pagination
        $total_properties = count( $properties );

        // Apply limit (after sorting so we get the right items)
        // Note: limit takes precedence over pagination
        if ( $atts['limit'] > 0 ) {
            $properties = array_slice( $properties, 0, absint( $atts['limit'] ) );
            $total_properties = count( $properties );
        }

        // Handle pagination
        $pagination_enabled = 'true' === $atts['pagination'] || '1' === $atts['pagination'];
        $current_page = 1;
        $total_pages = 1;
        $pagination_html = '';

        if ( $pagination_enabled && $atts['limit'] <= 0 ) {
            $per_page = absint( $atts['per_page'] );
            if ( $per_page < 1 ) {
                $per_page = 12; // Default fallback
            }

            // Get current page from query parameter
            $current_page = isset( $_GET['bwg_page'] ) ? absint( $_GET['bwg_page'] ) : 1;
            if ( $current_page < 1 ) {
                $current_page = 1;
            }

            // Calculate pagination
            $total_pages = ceil( $total_properties / $per_page );
            if ( $current_page > $total_pages && $total_pages > 0 ) {
                $current_page = $total_pages;
            }

            // Slice properties for current page
            $offset = ( $current_page - 1 ) * $per_page;
            $properties = array_slice( $properties, $offset, $per_page );

            // Generate pagination HTML
            $pagination_html = $this->render_pagination( $current_page, $total_pages );
        }

        ob_start();

        // Select template based on layout
        $layout = sanitize_text_field( $atts['layout'] );
        if ( 'masonry' === $layout ) {
            $template = 'properties-masonry.php';
        } elseif ( 'list' === $layout ) {
            $template = 'properties-list.php';
        } else {
            $template = 'properties-grid.php';
        }

        include $this->get_template( $template );

        // Add pagination after the property list
        if ( $pagination_html ) {
            echo $pagination_html;
        }

        return ob_get_clean();
    }

    /**
     * Single property card shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_card( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'         => 0,
                'show_image' => 'true',
                'show_specs' => 'true',
                'link'       => 'true',
            ),
            $atts,
            'bwg_property_card'
        );

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-card.php' );
        return ob_get_clean();
    }

    /**
     * Property gallery shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_gallery( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'             => 0,
                'layout'         => 'slider',
                'thumbnail_size' => 'medium',
            ),
            $atts,
            'bwg_property_gallery'
        );

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $property_id );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-gallery.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_gallery_output', $output, $property );
    }

    /**
     * Property title shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_title( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'    => 0,
                'tag'   => 'h1',
                'class' => '',
            ),
            $atts,
            'bwg_property_title'
        );

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $property_id );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div' );
        $tag          = in_array( $atts['tag'], $allowed_tags, true ) ? $atts['tag'] : 'h1';
        $class        = esc_attr( $atts['class'] );
        $name         = isset( $property['name'] ) ? esc_html( $property['name'] ) : '';

        $output = sprintf(
            '<%1$s class="bwg-property-title %2$s">%3$s</%1$s>',
            $tag,
            $class,
            $name
        );

        return apply_filters( 'bwg_property_title_output', $output, $property );
    }

    /**
     * Property specs shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_specs( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'         => 0,
                'show_icons' => 'true',
                'layout'     => 'inline',
            ),
            $atts,
            'bwg_property_specs'
        );

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $property_id );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-specs.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_specs_output', $output, $property );
    }

    /**
     * Property description shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_description( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'             => 0,
                'excerpt_length' => 0,
                'show_full'      => 'true',
            ),
            $atts,
            'bwg_property_description'
        );

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $property_id );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        $description = isset( $property['description'] ) ? $property['description'] : '';

        // Truncate if needed
        if ( $atts['excerpt_length'] > 0 ) {
            $description = wp_trim_words( $description, absint( $atts['excerpt_length'] ) );
        }

        $output = '<div class="bwg-property-description">' . wp_kses_post( $description ) . '</div>';

        return apply_filters( 'bwg_property_description_output', $output, $property );
    }

    /**
     * Property amenities shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_amenities( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'         => 0,
                'show_icons' => 'true',
                'columns'    => 2,
                'limit'      => 0,
            ),
            $atts,
            'bwg_property_amenities'
        );

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $property_id );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-amenities.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_amenities_output', $output, $property );
    }

    /**
     * Property availability calendar shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_availability( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'             => 0,
                'months_to_show' => 3,
                'start_month'    => 'current',
            ),
            $atts,
            'bwg_property_availability'
        );

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $availability = $this->api->get_availability( $property_id );

        if ( is_wp_error( $availability ) ) {
            return $this->render_error( $availability->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-availability.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_availability_output', $output, $availability );
    }

    /**
     * Property rates shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_rates( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'             => 0,
                'show_seasonal'  => 'true',
                'show_discounts' => 'true',
            ),
            $atts,
            'bwg_property_rates'
        );

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $rates = $this->api->get_rates( $atts['id'] );

        if ( is_wp_error( $rates ) ) {
            return $this->render_error( $rates->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-rates.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_rates_output', $output, $rates );
    }

    /**
     * Property booking button shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_booking_button( $atts ) {
        $this->enqueue_assets();

        $default_text = get_option( 'bwg_rentals_booking_button_text', __( 'Book Now', 'bwg-rentals' ) );

        $atts = shortcode_atts(
            array(
                'id'     => 0,
                'text'   => $default_text,
                'class'  => '',
                'target' => '_blank',
            ),
            $atts,
            'bwg_property_booking_button'
        );

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        $booking_url = $this->api->get_booking_url( $property );
        $text        = apply_filters( 'bwg_booking_button_text', esc_html( $atts['text'] ), $property );
        $class       = esc_attr( $atts['class'] );
        $target      = esc_attr( $atts['target'] );

        ob_start();
        include $this->get_template( 'property-booking-button.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_booking_button_output', $output, $property );
    }

    /**
     * Property location shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_location( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'         => 0,
                'show_map'   => 'false',
                'map_height' => '300px',
            ),
            $atts,
            'bwg_property_location'
        );

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        // Enqueue Google Maps scripts if map is enabled and API key is configured
        if ( 'true' === $atts['show_map'] ) {
            $this->enqueue_google_maps();
        }

        ob_start();
        include $this->get_template( 'property-location.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_location_output', $output, $property );
    }

    /**
     * Property policies shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_policies( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'id'       => 0,
                'sections' => 'all',
            ),
            $atts,
            'bwg_property_policies'
        );

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        ob_start();
        include $this->get_template( 'property-policies.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_policies_output', $output, $property );
    }

    /**
     * Render full property page
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_full( $atts ) {
        $atts = shortcode_atts(
            array(
                'id'                   => '',
                'layout'               => 'standard', // standard, compact, minimal
                'show_breadcrumbs'     => 'true',
                'show_gallery'         => 'true',
                'show_title'           => 'true',
                'show_specs'           => 'true',
                'show_description'     => 'true',
                'show_amenities'       => 'true',
                'show_availability'    => 'true',
                'show_rates'           => 'true',
                'show_location'        => 'true',
                'show_policies'        => 'true',
                'show_booking_button'  => 'true',
                'show_anchors'         => 'true', // Show section anchor navigation
                'show_related'         => 'true', // Show related/similar properties
                'related_limit'        => '4',    // Number of related properties to show
            ),
            $atts,
            'bwg_property'
        );

        // Apply minimal layout preset - show only essential info
        if ( 'minimal' === $atts['layout'] ) {
            // Essential: gallery, title, specs, description, booking button
            // Non-essential (hide these): breadcrumbs, amenities, availability, rates, location, policies, anchors, related
            $atts['show_breadcrumbs']  = 'false';
            $atts['show_amenities']    = 'false';
            $atts['show_availability'] = 'false';
            $atts['show_rates']        = 'false';
            $atts['show_location']     = 'false';
            $atts['show_policies']     = 'false';
            $atts['show_anchors']      = 'false';
            $atts['show_related']      = 'false';
        }

        $this->enqueue_assets();

        // Get property ID from shortcode attribute or URL parameter
        $property_id = $this->get_property_id_from_request( $atts['id'] );

        if ( empty( $property_id ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $property_id );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        // Availability and rates are now loaded via AJAX for faster initial page render
        // Pass null to template - JS will fetch these asynchronously
        $availability = null;
        $rates        = null;

        // Get related properties if enabled
        $related_properties = null;
        if ( 'true' === $atts['show_related'] ) {
            $related_properties = $this->get_related_properties( $property, absint( $atts['related_limit'] ) );
        }

        // Enqueue Google Maps scripts if location is enabled
        if ( 'true' === $atts['show_location'] ) {
            $this->enqueue_google_maps();
        }

        ob_start();
        include $this->get_template( 'property-full.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_full_output', $output, $property );
    }

    /**
     * Property slider shortcode
     *
     * Displays properties in a carousel/slider format with navigation controls.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_slider( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'limit'           => -1,
                'orderby'         => 'name',
                'autoplay'        => 'false',
                'speed'           => '5000',
                'slides_to_show'  => '1',
                'slides_to_scroll' => '1',
                'navigation'      => 'both',
                'loop'            => 'true',
            ),
            $atts,
            'bwg_property_slider'
        );

        $properties = $this->api->get_properties();

        if ( is_wp_error( $properties ) ) {
            return $this->render_error( $properties->get_error_message() );
        }

        if ( empty( $properties ) ) {
            return $this->render_empty( __( 'No properties available for the slider.', 'bwg-rentals' ) );
        }

        // Validate and sanitize slides_to_show (1-4)
        $atts['slides_to_show'] = max( 1, min( 4, absint( $atts['slides_to_show'] ) ) );

        // Validate and sanitize slides_to_scroll (1-4)
        $atts['slides_to_scroll'] = max( 1, min( 4, absint( $atts['slides_to_scroll'] ) ) );

        // Validate navigation attribute (arrows, dots, both, none)
        $valid_navigation = array( 'arrows', 'dots', 'both', 'none' );
        $atts['navigation'] = in_array( $atts['navigation'], $valid_navigation, true )
            ? $atts['navigation']
            : 'both';

        // Apply sorting
        $orderby = sanitize_text_field( $atts['orderby'] );
        $properties = $this->sort_properties( $properties, $orderby );

        // Apply limit
        if ( $atts['limit'] > 0 ) {
            $properties = array_slice( $properties, 0, absint( $atts['limit'] ) );
        }

        ob_start();
        include $this->get_template( 'property-slider.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_slider_output', $output, $properties );
    }

    /**
     * Featured properties shortcode
     *
     * Displays a curated selection of featured properties. Properties can be specified
     * by ID or the first N properties will be used if no IDs are provided.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function properties_featured( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'ids'     => '',     // Comma-separated list of property IDs
                'limit'   => 3,      // Max properties to show if no IDs specified
                'layout'  => 'grid', // grid or slider
                'columns' => 3,      // Columns for grid layout
                'orderby' => 'name', // Sort order: name, beds, sleeps, sqft
            ),
            $atts,
            'bwg_properties_featured'
        );

        $properties = $this->api->get_properties();

        if ( is_wp_error( $properties ) ) {
            return $this->render_error( $properties->get_error_message() );
        }

        if ( empty( $properties ) ) {
            return $this->render_empty( __( 'No properties available to feature.', 'bwg-rentals' ) );
        }

        // Filter properties by IDs if specified
        if ( ! empty( $atts['ids'] ) ) {
            $featured_ids = array_map( 'trim', explode( ',', $atts['ids'] ) );
            $featured_ids = array_map( 'absint', $featured_ids );

            $properties = array_filter( $properties, function( $property ) use ( $featured_ids ) {
                return in_array( $property['id'], $featured_ids, true );
            } );

            // Preserve the order specified in the ids attribute
            $ordered_properties = array();
            foreach ( $featured_ids as $id ) {
                foreach ( $properties as $property ) {
                    if ( $property['id'] === $id ) {
                        $ordered_properties[] = $property;
                        break;
                    }
                }
            }
            $properties = $ordered_properties;
        } else {
            // No specific IDs - use first N properties
            $orderby = sanitize_text_field( $atts['orderby'] );
            $properties = $this->sort_properties( $properties, $orderby );

            if ( $atts['limit'] > 0 ) {
                $properties = array_slice( $properties, 0, absint( $atts['limit'] ) );
            }
        }

        if ( empty( $properties ) ) {
            return $this->render_empty( __( 'No featured properties found.', 'bwg-rentals' ) );
        }

        // Render based on layout
        $layout = sanitize_text_field( $atts['layout'] );

        ob_start();
        if ( $layout === 'slider' ) {
            // Reuse the slider template
            include $this->get_template( 'property-slider.php' );
        } else {
            // Use grid layout (reuse properties grid template)
            include $this->get_template( 'properties-grid.php' );
        }
        $output = ob_get_clean();

        return apply_filters( 'bwg_properties_featured_output', $output, $properties );
    }

    /**
     * Property search form shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function property_search( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'show_dates'     => 'true',
                'show_guests'    => 'true',
                'show_bedrooms'  => 'true',
                'show_amenities' => 'true',
                'show_location'  => 'true',
                'results_page'   => '',
                'button_text'    => 'Search Properties',
                'layout'         => 'horizontal',
                'compact'        => 'false',
            ),
            $atts,
            'bwg_property_search'
        );

        // Convert string booleans to actual booleans
        $show_dates     = filter_var( $atts['show_dates'], FILTER_VALIDATE_BOOLEAN );
        $show_guests    = filter_var( $atts['show_guests'], FILTER_VALIDATE_BOOLEAN );
        $show_bedrooms  = filter_var( $atts['show_bedrooms'], FILTER_VALIDATE_BOOLEAN );
        $show_amenities = filter_var( $atts['show_amenities'], FILTER_VALIDATE_BOOLEAN );
        $show_location  = filter_var( $atts['show_location'], FILTER_VALIDATE_BOOLEAN );
        $results_page   = sanitize_text_field( $atts['results_page'] );
        $button_text    = sanitize_text_field( $atts['button_text'] );
        $layout         = sanitize_text_field( $atts['layout'] );
        $compact        = filter_var( $atts['compact'], FILTER_VALIDATE_BOOLEAN );

        // Validate layout - must be one of: horizontal, vertical, inline
        $valid_layouts = array( 'horizontal', 'vertical', 'inline' );
        if ( ! in_array( $layout, $valid_layouts, true ) ) {
            $layout = 'horizontal'; // Default fallback
        }

        // Get properties to extract bedroom, amenity, and location options
        $properties = $this->api->get_properties();
        $bedroom_options = array();
        $amenity_options = array();
        $location_options = array();

        if ( ! is_wp_error( $properties ) && ! empty( $properties ) ) {
            foreach ( $properties as $property ) {
                // Extract bedroom counts
                if ( isset( $property['bedrooms'] ) ) {
                    $bedroom_options[] = absint( $property['bedrooms'] );
                }

                // Extract amenities
                if ( isset( $property['amenities'] ) && is_array( $property['amenities'] ) ) {
                    foreach ( $property['amenities'] as $amenity ) {
                        // Skip XSS test amenities
                        if ( strpos( $amenity, '<' ) === false && strpos( $amenity, 'script' ) === false ) {
                            $amenity_options[] = sanitize_text_field( $amenity );
                        }
                    }
                }

                // Extract unique cities from address data
                if ( isset( $property['address']['city'] ) && ! empty( $property['address']['city'] ) ) {
                    $location_options[] = sanitize_text_field( $property['address']['city'] );
                }
            }
            $bedroom_options = array_unique( $bedroom_options );
            sort( $bedroom_options );

            $amenity_options = array_unique( $amenity_options );
            sort( $amenity_options );

            $location_options = array_unique( $location_options );
            sort( $location_options );
        }

        ob_start();
        include $this->get_template( 'property-search.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_search_output', $output );
    }

    /**
     * AJAX handler for filtering properties
     *
     * @return void
     */
    public function ajax_filter_properties() {
        // Verify nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'bwg_filter_properties' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'bwg-rentals' ) ) );
        }

        // Get filter parameters
        $beds = isset( $_POST['beds'] ) ? absint( $_POST['beds'] ) : 0;
        $baths = isset( $_POST['baths'] ) ? absint( $_POST['baths'] ) : 0;
        $sleeps = isset( $_POST['sleeps'] ) ? absint( $_POST['sleeps'] ) : 0;

        // Get shortcode attributes
        $atts = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
        $atts = shortcode_atts(
            array(
                'layout'     => 'grid',
                'columns'    => 3,
                'limit'      => -1,
                'orderby'    => 'name',
                'pagination' => 'false',
                'per_page'   => 12,
            ),
            $atts,
            'bwg_properties'
        );

        // Get all properties
        $properties = $this->api->get_properties();

        if ( is_wp_error( $properties ) ) {
            wp_send_json_error( array( 'message' => $properties->get_error_message() ) );
        }

        if ( empty( $properties ) ) {
            wp_send_json_success( array(
                'html' => '<div class="bwg-empty">' . esc_html__( 'No properties found matching your filters.', 'bwg-rentals' ) . '</div>',
                'count' => 0,
            ) );
        }

        // Apply filters
        if ( $beds > 0 || $baths > 0 || $sleeps > 0 ) {
            $properties = array_filter( $properties, function( $property ) use ( $beds, $baths, $sleeps ) {
                $matches = true;

                if ( $beds > 0 && isset( $property['bedrooms'] ) ) {
                    $matches = $matches && ( $property['bedrooms'] >= $beds );
                }

                if ( $baths > 0 && isset( $property['bathrooms'] ) ) {
                    $matches = $matches && ( $property['bathrooms'] >= $baths );
                }

                if ( $sleeps > 0 && isset( $property['sleeps'] ) ) {
                    $matches = $matches && ( $property['sleeps'] >= $sleeps );
                }

                return $matches;
            } );
        }

        // Apply sorting
        $orderby = sanitize_text_field( $atts['orderby'] );
        $properties = $this->sort_properties( $properties, $orderby );

        // Store count before limiting
        $total_count = count( $properties );

        // Apply limit
        if ( $atts['limit'] > 0 ) {
            $properties = array_slice( $properties, 0, absint( $atts['limit'] ) );
        }

        // Generate HTML
        ob_start();
        foreach ( $properties as $property ) :
            ?>
            <div class="bwg-property-card">
                <?php if ( ! empty( $property['images'] ) ) : ?>
                    <div class="bwg-property-card__image">
                        <img
                            src="<?php echo esc_url( $property['images'][0]['url'] ?? '' ); ?>"
                            alt="<?php echo esc_attr( $property['name'] ?? '' ); ?>"
                        />
                    </div>
                <?php endif; ?>
                <div class="bwg-property-card__content">
                    <h3 class="bwg-property-card__title">
                        <?php echo esc_html( $property['name'] ?? '' ); ?>
                    </h3>
                    <div class="bwg-property-specs">
                        <?php if ( isset( $property['bedrooms'] ) ) : ?>
                            <span class="bwg-property-specs__item">
                                <?php echo esc_html( $property['bedrooms'] ); ?> <?php esc_html_e( 'Beds', 'bwg-rentals' ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( isset( $property['bathrooms'] ) ) : ?>
                            <span class="bwg-property-specs__item">
                                <?php echo esc_html( $property['bathrooms'] ); ?> <?php esc_html_e( 'Baths', 'bwg-rentals' ); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ( isset( $property['guests'] ) ) : ?>
                            <span class="bwg-property-specs__item">
                                <?php echo esc_html( $property['guests'] ); ?> <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        endforeach;
        $html = ob_get_clean();

        // Send success response
        wp_send_json_success( array(
            'html' => $html,
            'count' => $total_count,
        ) );
    }

    /**
     * Check if a property is available for a given date range
     *
     * @param int    $property_id Property ID to check.
     * @param string $check_in    Check-in date (Y-m-d format).
     * @param string $check_out   Check-out date (Y-m-d format).
     * @return bool True if property is available for all dates in range, false otherwise.
     */
    private function is_property_available( $property_id, $check_in, $check_out ) {
        // Validate dates
        $check_in_date  = DateTime::createFromFormat( 'Y-m-d', $check_in );
        $check_out_date = DateTime::createFromFormat( 'Y-m-d', $check_out );

        if ( ! $check_in_date || ! $check_out_date ) {
            return false; // Invalid date format
        }

        if ( $check_out_date <= $check_in_date ) {
            return false; // Check-out must be after check-in
        }

        // Get availability data for this property
        $availability = $this->api->get_availability( $property_id );

        if ( is_wp_error( $availability ) || empty( $availability ) ) {
            // If we can't get availability data, assume property is available
            // (fail open to avoid hiding all properties on API errors)
            return true;
        }

        // Create array of dates we need to check
        $dates_to_check = array();
        $current_date   = clone $check_in_date;
        while ( $current_date < $check_out_date ) {
            $dates_to_check[] = $current_date->format( 'Y-m-d' );
            $current_date->modify( '+1 day' );
        }

        // Create a map of availability by date for faster lookup
        $availability_map = array();
        foreach ( $availability as $avail ) {
            if ( isset( $avail['date'] ) ) {
                $availability_map[ $avail['date'] ] = $avail;
            }
        }

        // Check if all dates in the range are available
        foreach ( $dates_to_check as $date ) {
            if ( ! isset( $availability_map[ $date ] ) ) {
                // Date not in availability data - assume unavailable
                return false;
            }

            if ( empty( $availability_map[ $date ]['available'] ) ) {
                // Date is explicitly marked as unavailable
                return false;
            }
        }

        // All dates are available
        return true;
    }

    /**
     * AJAX handler for searching properties
     *
     * @return void
     */
    public function ajax_search_properties() {
        // Verify nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'bwg_search_properties' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'bwg-rentals' ) ) );
        }

        // Get search parameters
        $check_in  = isset( $_POST['check_in'] ) ? sanitize_text_field( $_POST['check_in'] ) : '';
        $check_out = isset( $_POST['check_out'] ) ? sanitize_text_field( $_POST['check_out'] ) : '';
        $guests    = isset( $_POST['guests'] ) ? absint( $_POST['guests'] ) : 0;
        $bedrooms  = isset( $_POST['bedrooms'] ) ? absint( $_POST['bedrooms'] ) : 0;
        $location  = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
        $amenities = isset( $_POST['amenities'] ) && is_array( $_POST['amenities'] ) ? array_map( 'sanitize_text_field', $_POST['amenities'] ) : array();
        $min_price = isset( $_POST['min_price'] ) ? absint( $_POST['min_price'] ) : 0;
        $max_price = isset( $_POST['max_price'] ) ? absint( $_POST['max_price'] ) : 0;

        // Get all properties (bypass cache to get latest data with base_rate)
        $properties = $this->api->get_properties( false );

        if ( is_wp_error( $properties ) ) {
            wp_send_json_error( array( 'message' => $properties->get_error_message() ) );
        }

        if ( empty( $properties ) ) {
            wp_send_json_success( array(
                'html'  => '<div class="bwg-search-results__empty">' . esc_html__( 'No properties available.', 'bwg-rentals' ) . '</div>',
                'count' => 0,
            ) );
        }

        // Apply search filters
        $filtered_properties = array_filter( $properties, function( $property ) use ( $check_in, $check_out, $guests, $bedrooms, $location, $amenities, $min_price, $max_price ) {
            $matches = true;

            // Filter by date range availability
            if ( ! empty( $check_in ) && ! empty( $check_out ) ) {
                $matches = $matches && $this->is_property_available( $property['id'], $check_in, $check_out );
            }

            // Filter by guests (must accommodate at least the requested number)
            if ( $guests > 0 && isset( $property['guests'] ) ) {
                $matches = $matches && ( $property['guests'] >= $guests );
            }

            // Filter by bedrooms (must have at least the requested number)
            if ( $bedrooms > 0 && isset( $property['bedrooms'] ) ) {
                $matches = $matches && ( $property['bedrooms'] >= $bedrooms );
            }

            // Filter by location (exact match on city)
            if ( ! empty( $location ) && isset( $property['address']['city'] ) ) {
                $matches = $matches && ( strcasecmp( trim( $property['address']['city'] ), trim( $location ) ) === 0 );
            }

            // Filter by amenities (property must have all selected amenities)
            if ( ! empty( $amenities ) && isset( $property['amenities'] ) && is_array( $property['amenities'] ) ) {
                foreach ( $amenities as $required_amenity ) {
                    $has_amenity = false;
                    foreach ( $property['amenities'] as $property_amenity ) {
                        if ( strcasecmp( trim( $property_amenity ), trim( $required_amenity ) ) === 0 ) {
                            $has_amenity = true;
                            break;
                        }
                    }
                    if ( ! $has_amenity ) {
                        $matches = false;
                        break;
                    }
                }
            }

            // Filter by price range (nightly rate)
            if ( ( $min_price > 0 || $max_price > 0 ) && isset( $property['base_rate'] ) ) {
                $rate = absint( $property['base_rate'] );

                // Check minimum price
                if ( $min_price > 0 && $rate < $min_price ) {
                    $matches = false;
                }

                // Check maximum price
                if ( $max_price > 0 && $rate > $max_price ) {
                    $matches = false;
                }
            }

            return $matches;
        } );

        // Store filtered count
        $count = count( $filtered_properties );

        // Generate property cards HTML
        ob_start();
        if ( $count > 0 ) {
            echo '<div class="bwg-properties bwg-properties--grid bwg-properties--grid-3">';
            foreach ( $filtered_properties as $property ) :
                ?>
                <div class="bwg-property-card">
                    <?php if ( ! empty( $property['images'] ) ) : ?>
                        <div class="bwg-property-card__image">
                            <img
                                src="<?php echo esc_url( $property['images'][0]['url'] ?? '' ); ?>"
                                alt="<?php echo esc_attr( $property['name'] ?? '' ); ?>"
                                loading="lazy"
                            />
                        </div>
                    <?php endif; ?>
                    <div class="bwg-property-card__content">
                        <h3 class="bwg-property-card__title">
                            <?php echo esc_html( $property['name'] ?? '' ); ?>
                        </h3>
                        <div class="bwg-property-specs">
                            <?php if ( isset( $property['bedrooms'] ) ) : ?>
                                <span class="bwg-property-specs__item">
                                    <span class="bwg-icon">🛏️</span>
                                    <?php echo esc_html( $property['bedrooms'] ); ?> <?php esc_html_e( 'Beds', 'bwg-rentals' ); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ( isset( $property['bathrooms'] ) ) : ?>
                                <span class="bwg-property-specs__item">
                                    <span class="bwg-icon">🚿</span>
                                    <?php echo esc_html( $property['bathrooms'] ); ?> <?php esc_html_e( 'Baths', 'bwg-rentals' ); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ( isset( $property['guests'] ) ) : ?>
                                <span class="bwg-property-specs__item">
                                    <span class="bwg-icon">👥</span>
                                    <?php echo esc_html( $property['guests'] ); ?> <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if ( isset( $property['starting_rate'] ) ) : ?>
                            <div class="bwg-property-card__rate">
                                <?php esc_html_e( 'From', 'bwg-rentals' ); ?>
                                <strong>$<?php echo esc_html( number_format( $property['starting_rate'], 2 ) ); ?></strong>
                                <?php esc_html_e( '/night', 'bwg-rentals' ); ?>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( $property['booking_url'] ?? '#' ); ?>" class="bwg-property-card__button" target="_blank">
                            <?php esc_html_e( 'View Details', 'bwg-rentals' ); ?>
                        </a>
                    </div>
                </div>
                <?php
            endforeach;
            echo '</div>';
        } else {
            echo '<div class="bwg-search-results__empty">';
            echo '<p>' . esc_html__( 'No properties found matching your search criteria.', 'bwg-rentals' ) . '</p>';
            echo '<p>' . esc_html__( 'Try adjusting your filters to see more results.', 'bwg-rentals' ) . '</p>';
            echo '</div>';
        }
        $html = ob_get_clean();

        // Send success response
        wp_send_json_success( array(
            'html'  => $html,
            'count' => $count,
        ) );
    }

    /**
     * AJAX handler for getting property availability
     *
     * Returns availability data for lazy loading on property pages.
     *
     * @return void
     */
    public function ajax_get_availability() {
        // Verify nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bwg_get_availability' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'bwg-rentals' ) ) );
        }

        // Get property ID
        $property_id = isset( $_POST['property_id'] ) ? absint( $_POST['property_id'] ) : 0;

        if ( empty( $property_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Property ID is required', 'bwg-rentals' ) ) );
        }

        // Get availability data from API (will use cache if available)
        $availability = $this->api->get_availability( $property_id );

        if ( is_wp_error( $availability ) ) {
            wp_send_json_error( array( 'message' => $availability->get_error_message() ) );
        }

        // Generate HTML for the availability calendar
        ob_start();
        $this->render_availability_html( $availability, $property_id );
        $html = ob_get_clean();

        wp_send_json_success( array(
            'html' => $html,
            'data' => $availability,
        ) );
    }

    /**
     * AJAX handler for getting property rates
     *
     * Returns rates data for lazy loading on property pages.
     *
     * @return void
     */
    public function ajax_get_rates() {
        // Verify nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bwg_get_rates' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'bwg-rentals' ) ) );
        }

        // Get property ID
        $property_id = isset( $_POST['property_id'] ) ? absint( $_POST['property_id'] ) : 0;

        if ( empty( $property_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Property ID is required', 'bwg-rentals' ) ) );
        }

        // Get rates data from API (will use cache if available)
        $rates = $this->api->get_rates( $property_id );

        if ( is_wp_error( $rates ) ) {
            wp_send_json_error( array( 'message' => $rates->get_error_message() ) );
        }

        // Generate HTML for the rates section
        ob_start();
        $this->render_rates_html( $rates );
        $html = ob_get_clean();

        wp_send_json_success( array(
            'html' => $html,
            'data' => $rates,
        ) );
    }

    /**
     * Render availability calendar HTML
     *
     * @param array $availability Availability data.
     * @param int   $property_id  Property ID.
     */
    private function render_availability_html( $availability, $property_id ) {
        if ( empty( $availability['availability'] ) ) {
            echo '<p class="bwg-property-availability__empty">' . esc_html__( 'Availability information is not available.', 'bwg-rentals' ) . '</p>';
            return;
        }

        // Group availability by month
        $availability_by_month = array();
        foreach ( $availability['availability'] as $day ) {
            $month_key = gmdate( 'Y-m', strtotime( $day['date'] ) );
            if ( ! isset( $availability_by_month[ $month_key ] ) ) {
                $availability_by_month[ $month_key ] = array();
            }
            $availability_by_month[ $month_key ][] = $day;
        }

        echo '<div class="bwg-property-availability__calendar">';

        // Show first 3 months
        $months_shown = 0;
        foreach ( $availability_by_month as $month_key => $days ) {
            if ( $months_shown >= 3 ) {
                break;
            }
            $month_name = gmdate( 'F Y', strtotime( $month_key . '-01' ) );
            ?>
            <div class="bwg-property-availability__month">
                <h3 class="bwg-property-availability__month-name"><?php echo esc_html( $month_name ); ?></h3>
                <div class="bwg-property-availability__grid">
                    <?php foreach ( $days as $day ) : ?>
                        <div class="bwg-property-availability__day <?php echo $day['available'] ? 'bwg-property-availability__day--available' : 'bwg-property-availability__day--unavailable'; ?>">
                            <span class="bwg-property-availability__date"><?php echo esc_html( gmdate( 'j', strtotime( $day['date'] ) ) ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
            $months_shown++;
        }

        echo '</div>';
        ?>
        <div class="bwg-property-availability__legend">
            <span class="bwg-property-availability__legend-item">
                <span class="bwg-property-availability__legend-color bwg-property-availability__legend-color--available"></span>
                <?php esc_html_e( 'Available', 'bwg-rentals' ); ?>
            </span>
            <span class="bwg-property-availability__legend-item">
                <span class="bwg-property-availability__legend-color bwg-property-availability__legend-color--unavailable"></span>
                <?php esc_html_e( 'Unavailable', 'bwg-rentals' ); ?>
            </span>
        </div>
        <?php
    }

    /**
     * Render rates section HTML
     *
     * @param array $rates Rates data.
     */
    private function render_rates_html( $rates ) {
        if ( empty( $rates ) ) {
            echo '<p class="bwg-property-rates__empty">' . esc_html__( 'Rate information is not available.', 'bwg-rentals' ) . '</p>';
            return;
        }

        ?>
        <div class="bwg-property-rates">
            <?php if ( isset( $rates['base_rate'] ) ) : ?>
                <div class="bwg-property-rates__base">
                    <span class="bwg-property-rates__label"><?php esc_html_e( 'Base Rate:', 'bwg-rentals' ); ?></span>
                    <span class="bwg-property-rates__value">
                        <?php echo esc_html( '$' . number_format( $rates['base_rate'] ) ); ?>
                        <span class="bwg-property-rates__period"><?php esc_html_e( '/ night', 'bwg-rentals' ); ?></span>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $rates['seasonal_rates'] ) ) : ?>
                <div class="bwg-property-rates__seasonal">
                    <h3><?php esc_html_e( 'Seasonal Rates', 'bwg-rentals' ); ?></h3>
                    <ul class="bwg-property-rates__list">
                        <?php foreach ( $rates['seasonal_rates'] as $season ) : ?>
                            <li class="bwg-property-rates__item">
                                <span class="bwg-property-rates__season"><?php echo esc_html( $season['season'] ); ?></span>
                                <span class="bwg-property-rates__amount"><?php echo esc_html( '$' . number_format( $season['rate'] ) ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $rates['discounts'] ) ) : ?>
                <div class="bwg-property-rates__discounts">
                    <h3><?php esc_html_e( 'Discounts', 'bwg-rentals' ); ?></h3>
                    <ul class="bwg-property-rates__list">
                        <?php foreach ( $rates['discounts'] as $discount ) : ?>
                            <li class="bwg-property-rates__item">
                                <span class="bwg-property-rates__type"><?php echo esc_html( $discount['type'] ); ?></span>
                                <span class="bwg-property-rates__amount"><?php echo esc_html( $discount['amount'] ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
