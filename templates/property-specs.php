<?php
/**
 * Property Specs Template
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$show_icons = 'true' === $atts['show_icons'];
$layout     = $atts['layout'];
$class      = 'bwg-property-specs';

if ( 'stacked' === $layout ) {
    $class .= ' bwg-property-specs--stacked';
}

// Normalize field names - API may return different names
$guests = isset( $property['guests'] ) ? $property['guests'] : ( isset( $property['sleeps'] ) ? $property['sleeps'] : null );
$square_feet = isset( $property['square_feet'] ) ? $property['square_feet'] : ( isset( $property['sqft'] ) ? $property['sqft'] : null );
?>
<div class="<?php echo esc_attr( $class ); ?>">
    <?php if ( isset( $property['bedrooms'] ) ) : ?>
        <span class="bwg-property-specs__item">
            <?php if ( $show_icons ) : ?>
                <span class="bwg-property-specs__icon">🛏️</span>
            <?php endif; ?>
            <?php echo esc_html( $property['bedrooms'] ); ?> <?php esc_html_e( 'Bedrooms', 'bwg-rentals' ); ?>
        </span>
    <?php endif; ?>

    <?php if ( isset( $property['bathrooms'] ) ) : ?>
        <span class="bwg-property-specs__item">
            <?php if ( $show_icons ) : ?>
                <span class="bwg-property-specs__icon">🚿</span>
            <?php endif; ?>
            <?php echo esc_html( $property['bathrooms'] ); ?> <?php esc_html_e( 'Bathrooms', 'bwg-rentals' ); ?>
        </span>
    <?php endif; ?>

    <?php if ( $guests ) : ?>
        <span class="bwg-property-specs__item">
            <?php if ( $show_icons ) : ?>
                <span class="bwg-property-specs__icon">👥</span>
            <?php endif; ?>
            <?php echo esc_html( $guests ); ?> <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
        </span>
    <?php endif; ?>

    <?php if ( $square_feet ) : ?>
        <span class="bwg-property-specs__item">
            <?php if ( $show_icons ) : ?>
                <span class="bwg-property-specs__icon">📐</span>
            <?php endif; ?>
            <?php echo esc_html( number_format( $square_feet ) ); ?> <?php esc_html_e( 'sq ft', 'bwg-rentals' ); ?>
        </span>
    <?php endif; ?>
</div>
