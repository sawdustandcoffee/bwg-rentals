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

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$bwg_debug = isset( $_GET['bwg_debug'] ) && current_user_can( 'manage_options' );

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
<?php if ( $bwg_debug ) : ?>
<div style="background:#1a1a2e;color:#0f0;padding:10px;margin:5px 0;font-family:monospace;font-size:11px;border-left:3px solid #0f0;overflow:auto;">
    <strong style="color:#0f0;">[bwg_property_amenities]</strong> <?php echo esc_html( $property['name'] ?? 'Unknown' ); ?> |
    amenities: <?php echo count( $property['amenities'] ?? [] ); ?>
</div>
<?php endif; ?>
