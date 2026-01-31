<?php
/**
 * Template for property search form
 *
 * Available variables:
 * @var bool   $show_dates      Whether to show date picker fields
 * @var bool   $show_guests     Whether to show guests dropdown
 * @var bool   $show_bedrooms   Whether to show bedrooms filter
 * @var string $results_page    URL or page slug for search results
 * @var string $button_text     Search button text
 * @var string $layout          Layout mode: horizontal, vertical
 * @var array  $bedroom_options Available bedroom counts from properties
 *
 * @package BWG_Rentals
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Determine form action
$action_url = ! empty( $results_page ) ? get_permalink( get_page_by_path( $results_page ) ) : get_permalink();
if ( empty( $action_url ) ) {
    $action_url = home_url( '/' );
}

// Get current search values from URL
$check_in  = isset( $_GET['check_in'] ) ? sanitize_text_field( $_GET['check_in'] ) : '';
$check_out = isset( $_GET['check_out'] ) ? sanitize_text_field( $_GET['check_out'] ) : '';
$guests    = isset( $_GET['guests'] ) ? absint( $_GET['guests'] ) : '';
$bedrooms  = isset( $_GET['bedrooms'] ) ? absint( $_GET['bedrooms'] ) : '';
?>

<form class="bwg-property-search bwg-property-search--<?php echo esc_attr( $layout ); ?>" method="get" action="<?php echo esc_url( $action_url ); ?>">

    <?php if ( $show_dates ) : ?>
    <div class="bwg-property-search__field">
        <label for="bwg-search-check-in" class="bwg-property-search__label">
            <?php esc_html_e( 'Check-In', 'bwg-rentals' ); ?>
        </label>
        <input
            type="date"
            id="bwg-search-check-in"
            name="check_in"
            class="bwg-property-search__input"
            value="<?php echo esc_attr( $check_in ); ?>"
            min="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>"
        />
    </div>

    <div class="bwg-property-search__field">
        <label for="bwg-search-check-out" class="bwg-property-search__label">
            <?php esc_html_e( 'Check-Out', 'bwg-rentals' ); ?>
        </label>
        <input
            type="date"
            id="bwg-search-check-out"
            name="check_out"
            class="bwg-property-search__input"
            value="<?php echo esc_attr( $check_out ); ?>"
            min="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>"
        />
    </div>
    <?php endif; ?>

    <?php if ( $show_guests ) : ?>
    <div class="bwg-property-search__field">
        <label for="bwg-search-guests" class="bwg-property-search__label">
            <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
        </label>
        <select id="bwg-search-guests" name="guests" class="bwg-property-search__select">
            <option value=""><?php esc_html_e( 'Any', 'bwg-rentals' ); ?></option>
            <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
            <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $guests, $i ); ?>>
                <?php echo esc_html( sprintf( _n( '%d Guest', '%d Guests', $i, 'bwg-rentals' ), $i ) ); ?>
            </option>
            <?php endfor; ?>
        </select>
    </div>
    <?php endif; ?>

    <?php if ( $show_bedrooms && ! empty( $bedroom_options ) ) : ?>
    <div class="bwg-property-search__field">
        <label for="bwg-search-bedrooms" class="bwg-property-search__label">
            <?php esc_html_e( 'Bedrooms', 'bwg-rentals' ); ?>
        </label>
        <select id="bwg-search-bedrooms" name="bedrooms" class="bwg-property-search__select">
            <option value=""><?php esc_html_e( 'Any', 'bwg-rentals' ); ?></option>
            <?php foreach ( $bedroom_options as $bedroom_count ) : ?>
            <option value="<?php echo esc_attr( $bedroom_count ); ?>" <?php selected( $bedrooms, $bedroom_count ); ?>>
                <?php echo esc_html( sprintf( _n( '%d Bedroom', '%d Bedrooms', $bedroom_count, 'bwg-rentals' ), $bedroom_count ) ); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <div class="bwg-property-search__actions">
        <button type="submit" class="bwg-property-search__button">
            <?php echo esc_html( $button_text ); ?>
        </button>
        <button type="reset" class="bwg-property-search__reset" onclick="window.location.href='<?php echo esc_url( strtok( $_SERVER['REQUEST_URI'], '?' ) ); ?>';return false;">
            <?php esc_html_e( 'Clear', 'bwg-rentals' ); ?>
        </button>
    </div>

</form>
