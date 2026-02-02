<?php
/**
 * Property Location Template
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$show_map   = 'true' === $atts['show_map'];
$map_height = $atts['map_height'];

$address_parts = array_filter( array(
    $property['address']['street'] ?? '',
    $property['address']['city'] ?? '',
    $property['address']['state'] ?? '',
    $property['address']['zip'] ?? '',
    $property['address']['country'] ?? '',
) );

$full_address = implode( ', ', $address_parts );

// Extract numeric value from map_height (default to 300 if not parseable)
$map_height_num = absint( preg_replace( '/[^0-9]/', '', $map_height ) );
if ( $map_height_num < 100 ) {
    $map_height_num = 300;
}

// Check if Google Maps API key is configured
$google_maps_api_key = '';
if ( class_exists( 'BWG_Admin' ) ) {
    $google_maps_api_key = BWG_Admin::decrypt_value( get_option( 'bwg_rentals_google_maps_api_key', '' ) );
}
$use_google_maps = ! empty( $google_maps_api_key );
?>
<div class="bwg-property-location">
    <?php if ( ! empty( $full_address ) ) : ?>
        <div class="bwg-property-location__address">
            <?php echo esc_html( $full_address ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $show_map && isset( $property['latitude'] ) && isset( $property['longitude'] ) ) : ?>
        <?php
        $lat = floatval( $property['latitude'] );
        $lon = floatval( $property['longitude'] );
        $map_id = 'bwg-map-' . uniqid();
        ?>

        <?php if ( $use_google_maps ) : ?>
            <!-- Google Maps Integration -->
            <div class="bwg-property-location__map-container bwg-property-location__map-container--google" style="margin-top: 15px;">
                <div
                    id="<?php echo esc_attr( $map_id ); ?>"
                    class="bwg-property-location__google-map"
                    data-lat="<?php echo esc_attr( $lat ); ?>"
                    data-lng="<?php echo esc_attr( $lon ); ?>"
                    data-title="<?php echo esc_attr( $property['name'] ?? '' ); ?>"
                    data-address="<?php echo esc_attr( $full_address ); ?>"
                    style="width: 100%; height: <?php echo esc_attr( $map_height_num ); ?>px; border: 1px solid #ddd; border-radius: 4px;"
                >
                    <div class="bwg-property-location__map-loading">
                        <span class="bwg-spinner"></span>
                        <span><?php esc_html_e( 'Loading map...', 'bwg-rentals' ); ?></span>
                    </div>
                </div>
                <div class="bwg-property-location__map-actions">
                    <a
                        href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( $lat ); ?>,<?php echo esc_attr( $lon ); ?>"
                        class="bwg-property-location__directions-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?php esc_html_e( 'Get Directions', 'bwg-rentals' ); ?>
                    </a>
                    <a
                        href="https://www.google.com/maps/@<?php echo esc_attr( $lat ); ?>,<?php echo esc_attr( $lon ); ?>,15z"
                        class="bwg-property-location__view-larger"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?php esc_html_e( 'View Larger Map', 'bwg-rentals' ); ?>
                    </a>
                </div>
            </div>
        <?php else : ?>
            <!-- OpenStreetMap Fallback (no API key required) -->
            <?php
            $osm_url = sprintf(
                'https://www.openstreetmap.org/export/embed.html?bbox=%s,%s,%s,%s&layer=mapnik&marker=%s,%s',
                esc_attr( $lon - 0.01 ), // left
                esc_attr( $lat - 0.01 ), // bottom
                esc_attr( $lon + 0.01 ), // right
                esc_attr( $lat + 0.01 ), // top
                esc_attr( $lat ),
                esc_attr( $lon )
            );
            ?>
            <div class="bwg-property-location__map-container bwg-property-location__map-container--osm" style="margin-top: 15px;">
                <iframe
                    class="bwg-property-location__map"
                    width="100%"
                    height="<?php echo esc_attr( $map_height_num ); ?>"
                    style="border: 1px solid #ddd; border-radius: 4px;"
                    loading="lazy"
                    src="<?php echo esc_url( $osm_url ); ?>"
                    title="<?php echo esc_attr__( 'Property location map', 'bwg-rentals' ); ?>"
                ></iframe>
                <small class="bwg-property-location__map-attribution">
                    <a href="https://www.openstreetmap.org/?mlat=<?php echo esc_attr( $lat ); ?>&mlon=<?php echo esc_attr( $lon ); ?>#map=15/<?php echo esc_attr( $lat ); ?>/<?php echo esc_attr( $lon ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'View Larger Map', 'bwg-rentals' ); ?>
                    </a>
                </small>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
