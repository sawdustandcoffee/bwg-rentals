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
?>
<div class="bwg-property-rates">
    <table class="bwg-property-rates__table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Season', 'bwg-rentals' ); ?></th>
                <th><?php esc_html_e( 'Dates', 'bwg-rentals' ); ?></th>
                <th><?php esc_html_e( 'Nightly Rate', 'bwg-rentals' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( isset( $rates['seasons'] ) && is_array( $rates['seasons'] ) ) : ?>
                <?php foreach ( $rates['seasons'] as $season ) : ?>
                    <tr>
                        <td><?php echo esc_html( $season['name'] ?? '' ); ?></td>
                        <td>
                            <?php
                            if ( isset( $season['start_date'] ) && isset( $season['end_date'] ) ) {
                                echo esc_html( $season['start_date'] . ' - ' . $season['end_date'] );
                            }
                            ?>
                        </td>
                        <td>
                            <span class="bwg-property-rates__price">
                                <?php
                                if ( isset( $season['nightly_rate'] ) ) {
                                    echo esc_html( '$' . number_format( $season['nightly_rate'], 2 ) );
                                }
                                ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <?php if ( isset( $rates['nightly_rate'] ) ) : ?>
                    <tr>
                        <td><?php esc_html_e( 'Standard', 'bwg-rentals' ); ?></td>
                        <td><?php esc_html_e( 'Year round', 'bwg-rentals' ); ?></td>
                        <td>
                            <span class="bwg-property-rates__price">
                                <?php echo esc_html( '$' . number_format( $rates['nightly_rate'], 2 ) ); ?>
                            </span>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ( $show_discounts && ! empty( $rates['discounts'] ) ) : ?>
        <div class="bwg-property-rates__discounts">
            <h4><?php esc_html_e( 'Discounts', 'bwg-rentals' ); ?></h4>
            <ul>
                <?php foreach ( $rates['discounts'] as $discount ) : ?>
                    <li><?php echo esc_html( $discount['description'] ?? '' ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
