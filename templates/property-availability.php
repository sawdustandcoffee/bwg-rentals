<?php
/**
 * Property Availability Calendar Template
 *
 * @package BWG_Rentals
 * @var array $availability Availability data.
 * @var array $atts         Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$months_to_show = absint( $atts['months_to_show'] );
$start_month    = $atts['start_month'];
$property_id    = isset( $atts['id'] ) ? absint( $atts['id'] ) : 0;

// Get current date
$current_date = new DateTime();
if ( 'current' !== $start_month ) {
    try {
        $current_date = new DateTime( $start_month );
    } catch ( Exception $e ) {
        // Fall back to current date
    }
}

// Store the base date for navigation
$base_date = $current_date->format( 'Y-m-d' );

$day_names = array(
    __( 'Sun', 'bwg-rentals' ),
    __( 'Mon', 'bwg-rentals' ),
    __( 'Tue', 'bwg-rentals' ),
    __( 'Wed', 'bwg-rentals' ),
    __( 'Thu', 'bwg-rentals' ),
    __( 'Fri', 'bwg-rentals' ),
    __( 'Sat', 'bwg-rentals' ),
);
?>
<div class="bwg-property-availability"
     data-property-id="<?php echo esc_attr( $property_id ); ?>"
     data-months-to-show="<?php echo esc_attr( $months_to_show ); ?>"
     data-base-date="<?php echo esc_attr( $base_date ); ?>"
     data-offset="0">

    <!-- Calendar Navigation -->
    <div class="bwg-availability-calendar__navigation">
        <button type="button" class="bwg-availability-calendar__nav bwg-availability-calendar__nav--prev" data-direction="prev" aria-label="<?php esc_attr_e( 'Previous months', 'bwg-rentals' ); ?>">
            <span class="bwg-availability-calendar__nav-icon">&laquo;</span>
            <span class="bwg-availability-calendar__nav-text"><?php esc_html_e( 'Previous', 'bwg-rentals' ); ?></span>
        </button>
        <button type="button" class="bwg-availability-calendar__nav bwg-availability-calendar__nav--next" data-direction="next" aria-label="<?php esc_attr_e( 'Next months', 'bwg-rentals' ); ?>">
            <span class="bwg-availability-calendar__nav-text"><?php esc_html_e( 'Next', 'bwg-rentals' ); ?></span>
            <span class="bwg-availability-calendar__nav-icon">&raquo;</span>
        </button>
    </div>

    <?php
    // Build lookup array for availability data
    $availability_lookup = array();
    if ( is_array( $availability ) ) {
        foreach ( $availability as $day_data ) {
            if ( isset( $day_data['date'] ) ) {
                $availability_lookup[ $day_data['date'] ] = isset( $day_data['available'] ) ? $day_data['available'] : true;
            }
        }
    }
    ?>
    <div class="bwg-availability-calendar">
        <?php for ( $m = 0; $m < $months_to_show; $m++ ) : ?>
            <?php
            // Use first day of current month to avoid day overflow issues
            $month_date  = new DateTime( $current_date->format( 'Y-m-01' ) );
            $month_date->modify( '+' . $m . ' months' );
            $month_start = new DateTime( $month_date->format( 'Y-m-01' ) );
            $month_end   = new DateTime( $month_date->format( 'Y-m-t' ) );
            $first_day   = (int) $month_start->format( 'w' );
            $days_in_month = (int) $month_date->format( 't' );
            ?>
            <div class="bwg-availability-calendar__month">
                <div class="bwg-availability-calendar__title">
                    <?php echo esc_html( $month_date->format( 'F Y' ) ); ?>
                </div>
                <div class="bwg-availability-calendar__grid">
                    <?php foreach ( $day_names as $day_name ) : ?>
                        <div class="bwg-availability-calendar__day-header">
                            <?php echo esc_html( $day_name ); ?>
                        </div>
                    <?php endforeach; ?>

                    <?php
                    // Empty cells before first day
                    for ( $i = 0; $i < $first_day; $i++ ) :
                    ?>
                        <div class="bwg-availability-calendar__day bwg-availability-calendar__day--empty"></div>
                    <?php endfor; ?>

                    <?php
                    // Days of the month
                    for ( $day = 1; $day <= $days_in_month; $day++ ) :
                        $date_str = $month_date->format( 'Y-m-' ) . str_pad( $day, 2, '0', STR_PAD_LEFT );

                        // Check availability data for this date
                        $is_available = true; // Default to available
                        if ( isset( $availability_lookup[ $date_str ] ) ) {
                            $is_available = (bool) $availability_lookup[ $date_str ];
                        }
                        $day_class = $is_available
                            ? 'bwg-availability-calendar__day bwg-availability-calendar__day--available'
                            : 'bwg-availability-calendar__day bwg-availability-calendar__day--unavailable';
                    ?>
                        <div class="<?php echo esc_attr( $day_class ); ?>">
                            <?php echo esc_html( $day ); ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="bwg-availability-calendar__legend">
        <div class="bwg-availability-calendar__legend-item">
            <span class="bwg-availability-calendar__legend-color bwg-availability-calendar__legend-color--available"></span>
            <?php esc_html_e( 'Available', 'bwg-rentals' ); ?>
        </div>
        <div class="bwg-availability-calendar__legend-item">
            <span class="bwg-availability-calendar__legend-color bwg-availability-calendar__legend-color--unavailable"></span>
            <?php esc_html_e( 'Unavailable', 'bwg-rentals' ); ?>
        </div>
    </div>
</div>
