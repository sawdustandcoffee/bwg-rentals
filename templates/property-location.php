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

// Extract numeric value from map_height for OpenStreetMap (default to 300 if not parseable)
$map_height_num = absint( preg_replace( '/[^0-9]/', '', $map_height ) );
if ( $map_height_num < 100 ) {
    $map_height_num = 300;
}
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
        // Use OpenStreetMap embed which doesn't require an API key
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
        <div class="bwg-property-location__map-container" style="margin-top: 15px;">
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
</div>
