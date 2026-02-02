<?php
/**
 * BWG Rentals Custom Post Type Class
 *
 * Registers the bwg_property custom post type for local data storage.
 * This CPT stores property data synced from the Direct API to enable
 * fast page loads without API calls.
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Post Type class for local property storage
 */
class BWG_CPT {

    /**
     * Custom post type name
     */
    const POST_TYPE = 'bwg_property';

    /**
     * Meta key prefix
     */
    const META_PREFIX = '_bwg_';

    /**
     * Constructor - registers hooks
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
    }

    /**
     * Register the custom post type
     */
    public function register_post_type() {
        $args = array(
            'labels'              => array(
                'name'          => __( 'BWG Properties', 'bwg-rentals' ),
                'singular_name' => __( 'BWG Property', 'bwg-rentals' ),
            ),
            'public'              => false,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'has_archive'         => false,
            'rewrite'             => false,
            'supports'            => array( 'title' ),
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
        );

        register_post_type( self::POST_TYPE, $args );
    }

    /**
     * Get all meta field keys used by the CPT
     *
     * @return array List of meta keys.
     */
    public static function get_meta_keys() {
        return array(
            'property_id',    // Direct API property ID
            'bedrooms',
            'bathrooms',
            'guests',
            'sqft',
            'amenities',      // Serialized array
            'address',        // Serialized array
            'latitude',
            'longitude',
            'base_rate',
            'images',         // Serialized array
            'reviews',        // Serialized array
            'availability',   // Serialized array
            'rates',          // Serialized array
            'policies',       // Serialized array
            'headline',
            'description',
            'last_synced',    // Timestamp of last sync
            'raw_data',       // Full API response serialized
        );
    }

    /**
     * Find a CPT post by the Direct API property ID
     *
     * @param int $api_property_id The property ID from Direct API.
     * @return WP_Post|null The post object or null if not found.
     */
    public static function get_post_by_api_id( $api_property_id ) {
        $posts = get_posts( array(
            'post_type'      => self::POST_TYPE,
            'meta_key'       => self::META_PREFIX . 'property_id',
            'meta_value'     => absint( $api_property_id ),
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );

        return ! empty( $posts ) ? $posts[0] : null;
    }

    /**
     * Get all local property posts
     *
     * @param array $args Optional. Additional query arguments.
     * @return array Array of WP_Post objects.
     */
    public static function get_all_posts( $args = array() ) {
        $defaults = array(
            'post_type'      => self::POST_TYPE,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        );

        $query_args = wp_parse_args( $args, $defaults );
        return get_posts( $query_args );
    }

    /**
     * Convert a CPT post to an API-compatible property array
     *
     * This method ensures the returned data matches the format that
     * templates expect from the API, so templates work unchanged.
     *
     * @param WP_Post $post The CPT post object.
     * @return array Property data array matching API format.
     */
    public static function cpt_to_property_array( $post ) {
        if ( ! $post instanceof WP_Post ) {
            return array();
        }

        $property = array(
            'id'          => get_post_meta( $post->ID, self::META_PREFIX . 'property_id', true ),
            'name'        => $post->post_title,
            'headline'    => get_post_meta( $post->ID, self::META_PREFIX . 'headline', true ),
            'description' => get_post_meta( $post->ID, self::META_PREFIX . 'description', true ),
            'bedrooms'    => get_post_meta( $post->ID, self::META_PREFIX . 'bedrooms', true ),
            'bathrooms'   => get_post_meta( $post->ID, self::META_PREFIX . 'bathrooms', true ),
            'guests'      => get_post_meta( $post->ID, self::META_PREFIX . 'guests', true ),
            'sleeps'      => get_post_meta( $post->ID, self::META_PREFIX . 'guests', true ), // Alias for compatibility
            'sqft'        => get_post_meta( $post->ID, self::META_PREFIX . 'sqft', true ),
            'base_rate'   => get_post_meta( $post->ID, self::META_PREFIX . 'base_rate', true ),
            'latitude'    => get_post_meta( $post->ID, self::META_PREFIX . 'latitude', true ),
            'longitude'   => get_post_meta( $post->ID, self::META_PREFIX . 'longitude', true ),
        );

        // Unserialize array fields
        $amenities = get_post_meta( $post->ID, self::META_PREFIX . 'amenities', true );
        $property['amenities'] = ! empty( $amenities ) ? maybe_unserialize( $amenities ) : array();

        $address = get_post_meta( $post->ID, self::META_PREFIX . 'address', true );
        $property['address'] = ! empty( $address ) ? maybe_unserialize( $address ) : array();

        $images = get_post_meta( $post->ID, self::META_PREFIX . 'images', true );
        $property['images'] = ! empty( $images ) ? maybe_unserialize( $images ) : array();

        $reviews = get_post_meta( $post->ID, self::META_PREFIX . 'reviews', true );
        $property['reviews'] = ! empty( $reviews ) ? maybe_unserialize( $reviews ) : array();

        $policies = get_post_meta( $post->ID, self::META_PREFIX . 'policies', true );
        $property['policies'] = ! empty( $policies ) ? maybe_unserialize( $policies ) : array();

        // Cast numeric fields to proper types
        $property['bedrooms']  = absint( $property['bedrooms'] );
        $property['bathrooms'] = floatval( $property['bathrooms'] );
        $property['guests']    = absint( $property['guests'] );
        $property['sleeps']    = $property['guests'];
        $property['sqft']      = absint( $property['sqft'] );
        $property['base_rate'] = floatval( $property['base_rate'] );
        $property['latitude']  = floatval( $property['latitude'] );
        $property['longitude'] = floatval( $property['longitude'] );

        // Include _local flag to indicate this came from local storage
        $property['_local'] = true;
        $property['_last_synced'] = get_post_meta( $post->ID, self::META_PREFIX . 'last_synced', true );

        return $property;
    }

    /**
     * Get local property availability data
     *
     * @param int $api_property_id The Direct API property ID.
     * @return array|null Availability data or null if not found.
     */
    public static function get_local_availability( $api_property_id ) {
        $post = self::get_post_by_api_id( $api_property_id );
        if ( ! $post ) {
            return null;
        }

        $availability = get_post_meta( $post->ID, self::META_PREFIX . 'availability', true );
        return ! empty( $availability ) ? maybe_unserialize( $availability ) : null;
    }

    /**
     * Get local property rates data
     *
     * @param int $api_property_id The Direct API property ID.
     * @return array|null Rates data or null if not found.
     */
    public static function get_local_rates( $api_property_id ) {
        $post = self::get_post_by_api_id( $api_property_id );
        if ( ! $post ) {
            return null;
        }

        $rates = get_post_meta( $post->ID, self::META_PREFIX . 'rates', true );
        return ! empty( $rates ) ? maybe_unserialize( $rates ) : null;
    }

    /**
     * Create or update a CPT post from API property data
     *
     * @param array $property Property data from the API.
     * @return int|WP_Error Post ID on success, WP_Error on failure.
     */
    public static function upsert_property( $property ) {
        if ( empty( $property['id'] ) ) {
            return new WP_Error( 'missing_id', __( 'Property ID is required.', 'bwg-rentals' ) );
        }

        $api_property_id = absint( $property['id'] );

        // Check if property already exists
        $existing_post = self::get_post_by_api_id( $api_property_id );

        $post_data = array(
            'post_type'   => self::POST_TYPE,
            'post_status' => 'publish',
            'post_title'  => isset( $property['name'] ) ? sanitize_text_field( $property['name'] ) : '',
        );

        if ( $existing_post ) {
            $post_data['ID'] = $existing_post->ID;
            $post_id = wp_update_post( $post_data, true );
        } else {
            $post_id = wp_insert_post( $post_data, true );
        }

        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        // Update all meta fields
        update_post_meta( $post_id, self::META_PREFIX . 'property_id', $api_property_id );
        update_post_meta( $post_id, self::META_PREFIX . 'headline', isset( $property['headline'] ) ? sanitize_text_field( $property['headline'] ) : '' );
        update_post_meta( $post_id, self::META_PREFIX . 'description', isset( $property['description'] ) ? wp_kses_post( $property['description'] ) : '' );
        update_post_meta( $post_id, self::META_PREFIX . 'bedrooms', isset( $property['bedrooms'] ) ? absint( $property['bedrooms'] ) : 0 );
        update_post_meta( $post_id, self::META_PREFIX . 'bathrooms', isset( $property['bathrooms'] ) ? floatval( $property['bathrooms'] ) : 0 );

        // Handle guests/sleeps field (API may use either)
        $guests = isset( $property['guests'] ) ? $property['guests'] : ( isset( $property['sleeps'] ) ? $property['sleeps'] : 0 );
        update_post_meta( $post_id, self::META_PREFIX . 'guests', absint( $guests ) );

        update_post_meta( $post_id, self::META_PREFIX . 'sqft', isset( $property['sqft'] ) ? absint( $property['sqft'] ) : 0 );
        update_post_meta( $post_id, self::META_PREFIX . 'base_rate', isset( $property['base_rate'] ) ? floatval( $property['base_rate'] ) : 0 );
        update_post_meta( $post_id, self::META_PREFIX . 'latitude', isset( $property['latitude'] ) ? floatval( $property['latitude'] ) : '' );
        update_post_meta( $post_id, self::META_PREFIX . 'longitude', isset( $property['longitude'] ) ? floatval( $property['longitude'] ) : '' );

        // Serialize array fields
        update_post_meta( $post_id, self::META_PREFIX . 'amenities', maybe_serialize( isset( $property['amenities'] ) ? $property['amenities'] : array() ) );
        update_post_meta( $post_id, self::META_PREFIX . 'address', maybe_serialize( isset( $property['address'] ) ? $property['address'] : array() ) );
        update_post_meta( $post_id, self::META_PREFIX . 'images', maybe_serialize( isset( $property['images'] ) ? $property['images'] : array() ) );
        update_post_meta( $post_id, self::META_PREFIX . 'reviews', maybe_serialize( isset( $property['reviews'] ) ? $property['reviews'] : array() ) );
        update_post_meta( $post_id, self::META_PREFIX . 'policies', maybe_serialize( isset( $property['policies'] ) ? $property['policies'] : array() ) );

        // Store raw data for reference
        update_post_meta( $post_id, self::META_PREFIX . 'raw_data', maybe_serialize( $property ) );

        // Update sync timestamp
        update_post_meta( $post_id, self::META_PREFIX . 'last_synced', current_time( 'timestamp' ) );

        return $post_id;
    }

    /**
     * Update availability data for a property
     *
     * @param int   $api_property_id The Direct API property ID.
     * @param array $availability    Availability data from API.
     * @return bool True on success, false on failure.
     */
    public static function update_availability( $api_property_id, $availability ) {
        $post = self::get_post_by_api_id( $api_property_id );
        if ( ! $post ) {
            return false;
        }

        update_post_meta( $post->ID, self::META_PREFIX . 'availability', maybe_serialize( $availability ) );
        return true;
    }

    /**
     * Update rates data for a property
     *
     * @param int   $api_property_id The Direct API property ID.
     * @param array $rates           Rates data from API.
     * @return bool True on success, false on failure.
     */
    public static function update_rates( $api_property_id, $rates ) {
        $post = self::get_post_by_api_id( $api_property_id );
        if ( ! $post ) {
            return false;
        }

        update_post_meta( $post->ID, self::META_PREFIX . 'rates', maybe_serialize( $rates ) );
        return true;
    }

    /**
     * Delete a local property by API ID
     *
     * @param int $api_property_id The Direct API property ID.
     * @return bool True on success, false on failure.
     */
    public static function delete_property( $api_property_id ) {
        $post = self::get_post_by_api_id( $api_property_id );
        if ( ! $post ) {
            return false;
        }

        $result = wp_delete_post( $post->ID, true ); // Force delete, bypass trash
        return ( false !== $result );
    }

    /**
     * Delete all local properties
     *
     * @return int Number of properties deleted.
     */
    public static function delete_all_properties() {
        $posts = self::get_all_posts();
        $deleted = 0;

        foreach ( $posts as $post ) {
            if ( wp_delete_post( $post->ID, true ) ) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Get count of local properties
     *
     * @return int Number of local properties.
     */
    public static function get_property_count() {
        $count = wp_count_posts( self::POST_TYPE );
        return isset( $count->publish ) ? absint( $count->publish ) : 0;
    }

    /**
     * Get the oldest sync timestamp among all properties
     *
     * @return int|null Unix timestamp or null if no properties.
     */
    public static function get_oldest_sync_time() {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MIN(pm.meta_value)
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE p.post_type = %s
                AND p.post_status = 'publish'
                AND pm.meta_key = %s",
                self::POST_TYPE,
                self::META_PREFIX . 'last_synced'
            )
        );

        return $result ? absint( $result ) : null;
    }

    /**
     * Get IDs of all API properties currently stored locally
     *
     * @return array Array of API property IDs.
     */
    public static function get_all_api_ids() {
        global $wpdb;

        $results = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT pm.meta_value
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                WHERE p.post_type = %s
                AND p.post_status = 'publish'
                AND pm.meta_key = %s",
                self::POST_TYPE,
                self::META_PREFIX . 'property_id'
            )
        );

        return array_map( 'absint', $results );
    }
}
