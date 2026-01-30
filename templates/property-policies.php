<?php
/**
 * Property Policies Template
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$sections_filter = $atts['sections'];
$policies        = $property['policies'] ?? array();

// Define available policy sections
$available_sections = array(
    'house_rules'   => __( 'House Rules', 'bwg-rentals' ),
    'cancellation'  => __( 'Cancellation Policy', 'bwg-rentals' ),
    'check_in'      => __( 'Check-In/Check-Out', 'bwg-rentals' ),
    'pets'          => __( 'Pet Policy', 'bwg-rentals' ),
    'smoking'       => __( 'Smoking Policy', 'bwg-rentals' ),
    'damage'        => __( 'Damage Policy', 'bwg-rentals' ),
);

// Filter sections if specified
if ( 'all' !== $sections_filter ) {
    $requested_sections = array_map( 'trim', explode( ',', $sections_filter ) );
    $available_sections = array_intersect_key( $available_sections, array_flip( $requested_sections ) );
}

if ( empty( $policies ) && empty( $property['house_rules'] ) ) {
    return;
}
?>
<div class="bwg-property-policies">
    <?php foreach ( $available_sections as $key => $title ) : ?>
        <?php
        $content = '';

        // Try to get content from policies array or direct property
        if ( isset( $policies[ $key ] ) ) {
            $content = $policies[ $key ];
        } elseif ( isset( $property[ $key ] ) ) {
            $content = $property[ $key ];
        }

        // Skip empty sections
        if ( empty( $content ) ) {
            continue;
        }
        ?>
        <div class="bwg-property-policies__section">
            <h4 class="bwg-property-policies__title">
                <?php echo esc_html( $title ); ?>
            </h4>
            <div class="bwg-property-policies__content">
                <?php echo wp_kses_post( $content ); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
