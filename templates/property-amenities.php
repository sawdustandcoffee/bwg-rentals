<?php
/**
 * Property Amenities Template
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$amenities  = $property['amenities'] ?? array();
$show_icons = 'true' === $atts['show_icons'];
$columns    = absint( $atts['columns'] );
$limit      = absint( $atts['limit'] );

if ( empty( $amenities ) ) {
    return;
}

if ( $limit > 0 ) {
    $amenities = array_slice( $amenities, 0, $limit );
}

$list_class = 'bwg-property-amenities__list bwg-property-amenities__list--columns-' . $columns;
?>
<div class="bwg-property-amenities">
    <ul class="<?php echo esc_attr( $list_class ); ?>">
        <?php foreach ( $amenities as $amenity ) : ?>
            <li class="bwg-property-amenities__item">
                <?php if ( $show_icons ) : ?>
                    <span class="bwg-property-amenities__icon">✓</span>
                <?php endif; ?>
                <?php echo esc_html( is_array( $amenity ) ? ( $amenity['name'] ?? '' ) : $amenity ); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
