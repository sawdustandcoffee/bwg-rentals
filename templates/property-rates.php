<?php
/**
 * Property Rates Template
 *
 * @package BWG_Rentals
 * @var array $rates Rates data.
 * @var array $atts  Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$show_seasonal  = 'true' === $atts['show_seasonal'];
$show_discounts = 'true' === $atts['show_discounts'];

if ( empty( $rates ) ) {
    echo '<p class="bwg-property-rates__empty">' . esc_html__( 'Rates information not available.', 'bwg-rentals' ) . '</p>';
    return;
}

// Normalize rate data - support both 'seasons' and 'seasonal_rates' keys
$seasonal_rates = array();
if ( isset( $rates['seasons'] ) && is_array( $rates['seasons'] ) ) {
    $seasonal_rates = $rates['seasons'];
} elseif ( isset( $rates['seasonal_rates'] ) && is_array( $rates['seasonal_rates'] ) ) {
    $seasonal_rates = $rates['seasonal_rates'];
}

// Get base/nightly rate - support both 'nightly_rate' and 'base_rate' keys
$base_rate = 0;
if ( isset( $rates['nightly_rate'] ) ) {
    $base_rate = $rates['nightly_rate'];
} elseif ( isset( $rates['base_rate'] ) ) {
    $base_rate = $rates['base_rate'];
}

// Get currency symbol
$currency = isset( $rates['currency'] ) ? $rates['currency'] : 'USD';
$currency_symbol = 'USD' === $currency ? '$' : $currency . ' ';
?>
<div class="bwg-property-rates">
    <?php if ( $base_rate > 0 ) : ?>
        <div class="bwg-property-rates__base">
            <span class="bwg-property-rates__base-label"><?php esc_html_e( 'Starting from', 'bwg-rentals' ); ?></span>
            <span class="bwg-property-rates__base-price"><?php echo esc_html( $currency_symbol . number_format( $base_rate, 2 ) ); ?></span>
            <span class="bwg-property-rates__base-period"><?php esc_html_e( '/ night', 'bwg-rentals' ); ?></span>
        </div>
    <?php endif; ?>

    <?php if ( $show_seasonal && ! empty( $seasonal_rates ) ) : ?>
        <table class="bwg-property-rates__table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Season', 'bwg-rentals' ); ?></th>
                    <th><?php esc_html_e( 'Dates', 'bwg-rentals' ); ?></th>
                    <th><?php esc_html_e( 'Nightly Rate', 'bwg-rentals' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $seasonal_rates as $season ) : ?>
                    <?php
                    // Support both 'nightly_rate' and 'rate' keys for the price
                    $season_rate = 0;
                    if ( isset( $season['nightly_rate'] ) ) {
                        $season_rate = $season['nightly_rate'];
                    } elseif ( isset( $season['rate'] ) ) {
                        $season_rate = $season['rate'];
                    }
                    ?>
                    <tr>
                        <td><?php echo esc_html( $season['name'] ?? '' ); ?></td>
                        <td>
                            <?php
                            if ( isset( $season['start_date'] ) && isset( $season['end_date'] ) ) {
                                // Format dates nicely
                                $start = date_create( $season['start_date'] );
                                $end = date_create( $season['end_date'] );
                                if ( $start && $end ) {
                                    echo esc_html( date_format( $start, 'M j' ) . ' - ' . date_format( $end, 'M j' ) );
                                } else {
                                    echo esc_html( $season['start_date'] . ' - ' . $season['end_date'] );
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <span class="bwg-property-rates__price">
                                <?php
                                if ( $season_rate > 0 ) {
                                    echo esc_html( $currency_symbol . number_format( $season_rate, 2 ) );
                                }
                                ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ( ! $show_seasonal && $base_rate > 0 ) : ?>
        <table class="bwg-property-rates__table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Season', 'bwg-rentals' ); ?></th>
                    <th><?php esc_html_e( 'Dates', 'bwg-rentals' ); ?></th>
                    <th><?php esc_html_e( 'Nightly Rate', 'bwg-rentals' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php esc_html_e( 'Standard', 'bwg-rentals' ); ?></td>
                    <td><?php esc_html_e( 'Year round', 'bwg-rentals' ); ?></td>
                    <td>
                        <span class="bwg-property-rates__price">
                            <?php echo esc_html( $currency_symbol . number_format( $base_rate, 2 ) ); ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ( isset( $rates['cleaning_fee'] ) || isset( $rates['service_fee'] ) ) : ?>
        <div class="bwg-property-rates__fees">
            <h4><?php esc_html_e( 'Additional Fees', 'bwg-rentals' ); ?></h4>
            <ul>
                <?php if ( isset( $rates['cleaning_fee'] ) && $rates['cleaning_fee'] > 0 ) : ?>
                    <li>
                        <span class="bwg-property-rates__fee-name"><?php esc_html_e( 'Cleaning Fee:', 'bwg-rentals' ); ?></span>
                        <span class="bwg-property-rates__fee-amount"><?php echo esc_html( $currency_symbol . number_format( $rates['cleaning_fee'], 2 ) ); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ( isset( $rates['service_fee'] ) && $rates['service_fee'] > 0 ) : ?>
                    <li>
                        <span class="bwg-property-rates__fee-name"><?php esc_html_e( 'Service Fee:', 'bwg-rentals' ); ?></span>
                        <span class="bwg-property-rates__fee-amount"><?php echo esc_html( $currency_symbol . number_format( $rates['service_fee'], 2 ) ); ?></span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ( $show_discounts && ! empty( $rates['discounts'] ) ) : ?>
        <div class="bwg-property-rates__discounts">
            <h4><?php esc_html_e( 'Discounts', 'bwg-rentals' ); ?></h4>
            <ul>
                <?php foreach ( $rates['discounts'] as $discount ) : ?>
                    <li>
                        <?php
                        // Support both 'description' and generating from name/value
                        if ( isset( $discount['description'] ) && ! empty( $discount['description'] ) ) {
                            echo esc_html( $discount['description'] );
                        } elseif ( isset( $discount['name'] ) ) {
                            $discount_text = $discount['name'];
                            if ( isset( $discount['value'] ) ) {
                                if ( isset( $discount['type'] ) && 'percentage' === $discount['type'] ) {
                                    $discount_text .= ': ' . $discount['value'] . '% off';
                                } else {
                                    $discount_text .= ': ' . $currency_symbol . number_format( $discount['value'], 2 ) . ' off';
                                }
                            }
                            if ( isset( $discount['min_stay'] ) ) {
                                $discount_text .= ' (min ' . $discount['min_stay'] . ' nights)';
                            }
                            echo esc_html( $discount_text );
                        }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
