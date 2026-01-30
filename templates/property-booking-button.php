<?php
/**
 * Property Booking Button Template
 *
 * @package BWG_Rentals
 * @var array  $property    Property data.
 * @var string $booking_url Booking URL.
 * @var string $text        Button text.
 * @var string $class       Additional CSS classes.
 * @var string $target      Link target.
 * @var array  $atts        Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<a
    href="<?php echo esc_url( $booking_url ); ?>"
    class="bwg-property-booking-button <?php echo esc_attr( $class ); ?>"
    target="<?php echo esc_attr( $target ); ?>"
    rel="noopener noreferrer"
>
    <?php echo esc_html( $text ); ?>
</a>
