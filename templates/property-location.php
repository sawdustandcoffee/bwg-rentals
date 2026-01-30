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
?>
<div class="bwg-property-location">
    <?php if ( ! empty( $full_address ) ) : ?>
        <div class="bwg-property-location__address">
            <?php echo esc_html( $full_address ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $show_map && isset( $property['latitude'] ) && isset( $property['longitude'] ) ) : ?>
        <iframe
            class="bwg-property-location__map"
            width="100%"
            height="<?php echo esc_attr( $map_height ); ?>"
            style="border:0"
            loading="lazy"
            allowfullscreen
            src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=<?php echo esc_attr( $property['latitude'] . ',' . $property['longitude'] ); ?>"
        ></iframe>
    <?php endif; ?>
</div>
