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

        $this->assets_enqueued = true;
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
     * Properties grid/list shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function properties( $atts ) {
        $this->enqueue_assets();

        $atts = shortcode_atts(
            array(
                'layout'  => 'grid',
                'columns' => 3,
                'limit'   => -1,
                'orderby' => 'name',
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

        // Apply limit (after sorting so we get the right items)
        if ( $atts['limit'] > 0 ) {
            $properties = array_slice( $properties, 0, absint( $atts['limit'] ) );
        }

        ob_start();

        $template = 'grid' === $atts['layout'] ? 'properties-grid.php' : 'properties-list.php';
        include $this->get_template( $template );

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

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

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

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

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

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

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

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

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

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

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

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $availability = $this->api->get_availability( $atts['id'] );

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

        $default_text = get_option( 'bwg_rentals_button_text', __( 'Book Now', 'bwg-rentals' ) );

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

        $booking_url = $this->api->get_booking_url( $atts['id'] );
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
            ),
            $atts,
            'bwg_property'
        );

        $this->enqueue_assets();

        if ( empty( $atts['id'] ) ) {
            return $this->render_error( __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $property = $this->api->get_property( $atts['id'] );

        if ( is_wp_error( $property ) ) {
            return $this->render_error( $property->get_error_message() );
        }

        // Get availability and rates data for the full property view
        $availability = 'true' === $atts['show_availability'] ? $this->api->get_availability( $atts['id'] ) : null;
        $rates        = 'true' === $atts['show_rates'] ? $this->api->get_rates( $atts['id'] ) : null;

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
                'limit'   => -1,
                'orderby' => 'name',
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
                'results_page'   => '',
                'button_text'    => 'Search Properties',
                'layout'         => 'horizontal',
            ),
            $atts,
            'bwg_property_search'
        );

        // Convert string booleans to actual booleans
        $show_dates    = filter_var( $atts['show_dates'], FILTER_VALIDATE_BOOLEAN );
        $show_guests   = filter_var( $atts['show_guests'], FILTER_VALIDATE_BOOLEAN );
        $show_bedrooms = filter_var( $atts['show_bedrooms'], FILTER_VALIDATE_BOOLEAN );
        $results_page  = sanitize_text_field( $atts['results_page'] );
        $button_text   = sanitize_text_field( $atts['button_text'] );
        $layout        = sanitize_text_field( $atts['layout'] );

        // Get properties to extract bedroom options
        $properties = $this->api->get_properties();
        $bedroom_options = array();

        if ( ! is_wp_error( $properties ) && ! empty( $properties ) ) {
            foreach ( $properties as $property ) {
                if ( isset( $property->bedrooms ) ) {
                    $bedroom_options[] = absint( $property->bedrooms );
                }
            }
            $bedroom_options = array_unique( $bedroom_options );
            sort( $bedroom_options );
        }

        ob_start();
        include $this->get_template( 'property-search.php' );
        $output = ob_get_clean();

        return apply_filters( 'bwg_property_search_output', $output );
    }
}
