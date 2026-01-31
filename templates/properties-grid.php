<?php
/**
 * Properties Grid Template
 *
 * @package BWG_Rentals
 * @var array $properties Array of properties.
 * @var array $atts       Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$columns_class = 'bwg-properties--grid-' . absint( $atts['columns'] );

// Get unique values for filters
$bedrooms_options = array();
$bathrooms_options = array();
$sleeps_options = array();

foreach ( $properties as $property ) {
    if ( isset( $property['bedrooms'] ) ) {
        $bedrooms_options[ $property['bedrooms'] ] = true;
    }
    if ( isset( $property['bathrooms'] ) ) {
        $bathrooms_options[ $property['bathrooms'] ] = true;
    }
    if ( isset( $property['sleeps'] ) ) {
        $sleeps_options[ $property['sleeps'] ] = true;
    }
}

// Sort options
$bedrooms_options = array_keys( $bedrooms_options );
$bathrooms_options = array_keys( $bathrooms_options );
$sleeps_options = array_keys( $sleeps_options );
sort( $bedrooms_options, SORT_NUMERIC );
sort( $bathrooms_options, SORT_NUMERIC );
sort( $sleeps_options, SORT_NUMERIC );

// Get current filter values from URL
$current_beds = isset( $_GET['bwg_beds'] ) ? absint( $_GET['bwg_beds'] ) : 0;
$current_baths = isset( $_GET['bwg_baths'] ) ? absint( $_GET['bwg_baths'] ) : 0;
$current_sleeps = isset( $_GET['bwg_sleeps'] ) ? absint( $_GET['bwg_sleeps'] ) : 0;
$current_orderby = isset( $_GET['bwg_orderby'] ) ? sanitize_text_field( $_GET['bwg_orderby'] ) : sanitize_text_field( $atts['orderby'] );

// Generate unique ID for this instance
$instance_id = 'bwg-filters-' . uniqid();

// Check if filters should be shown
$show_filters = filter_var( $atts['show_filters'], FILTER_VALIDATE_BOOLEAN );
?>
<div class="bwg-properties-container" id="<?php echo esc_attr( $instance_id ); ?>">
    <?php if ( $show_filters ) : ?>
    <!-- Filter Controls -->
    <div class="bwg-filters">
        <div class="bwg-filters__inner">
            <label for="bwg-filter-beds-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__label">
                <?php esc_html_e( 'Bedrooms', 'bwg-rentals' ); ?>
            </label>
            <select id="bwg-filter-beds-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__select" name="bwg_beds" data-filter="beds">
                <option value=""><?php esc_html_e( 'Any', 'bwg-rentals' ); ?></option>
                <?php foreach ( $bedrooms_options as $beds ) : ?>
                    <option value="<?php echo esc_attr( $beds ); ?>" <?php selected( $current_beds, $beds ); ?>>
                        <?php echo esc_html( $beds ); ?>+
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="bwg-filter-baths-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__label">
                <?php esc_html_e( 'Bathrooms', 'bwg-rentals' ); ?>
            </label>
            <select id="bwg-filter-baths-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__select" name="bwg_baths" data-filter="baths">
                <option value=""><?php esc_html_e( 'Any', 'bwg-rentals' ); ?></option>
                <?php foreach ( $bathrooms_options as $baths ) : ?>
                    <option value="<?php echo esc_attr( $baths ); ?>" <?php selected( $current_baths, $baths ); ?>>
                        <?php echo esc_html( $baths ); ?>+
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="bwg-filter-sleeps-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__label">
                <?php esc_html_e( 'Sleeps', 'bwg-rentals' ); ?>
            </label>
            <select id="bwg-filter-sleeps-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__select" name="bwg_sleeps" data-filter="sleeps">
                <option value=""><?php esc_html_e( 'Any', 'bwg-rentals' ); ?></option>
                <?php foreach ( $sleeps_options as $sleeps ) : ?>
                    <option value="<?php echo esc_attr( $sleeps ); ?>" <?php selected( $current_sleeps, $sleeps ); ?>>
                        <?php echo esc_html( $sleeps ); ?>+
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="bwg-filter-sort-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__label">
                <?php esc_html_e( 'Sort By', 'bwg-rentals' ); ?>
            </label>
            <select id="bwg-filter-sort-<?php echo esc_attr( $instance_id ); ?>" class="bwg-filter__select" name="bwg_orderby" data-filter="orderby">
                <option value="name" <?php selected( $current_orderby, 'name' ); ?>>
                    <?php esc_html_e( 'Property Name', 'bwg-rentals' ); ?>
                </option>
                <option value="beds" <?php selected( $current_orderby, 'beds' ); ?>>
                    <?php esc_html_e( 'Bedrooms', 'bwg-rentals' ); ?>
                </option>
                <option value="guests" <?php selected( $current_orderby, 'guests' ); ?>>
                    <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
                </option>
                <option value="sqft" <?php selected( $current_orderby, 'sqft' ); ?>>
                    <?php esc_html_e( 'Size (sqft)', 'bwg-rentals' ); ?>
                </option>
            </select>

            <button type="button" class="bwg-filter__reset" id="bwg-filter-reset-<?php echo esc_attr( $instance_id ); ?>">
                <?php esc_html_e( 'Clear Filters', 'bwg-rentals' ); ?>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Properties Grid -->
    <?php
    // Get property detail page for links
    $property_page_id = get_option( 'bwg_rentals_property_page', 0 );
    ?>
    <div class="bwg-properties bwg-properties--grid <?php echo esc_attr( $columns_class ); ?>" data-instance="<?php echo esc_attr( $instance_id ); ?>">
        <?php foreach ( $properties as $property ) :
            // Generate property detail page URL
            $property_url = '#';
            if ( $property_page_id > 0 ) {
                $property_url = add_query_arg( 'property_id', $property['id'], get_permalink( $property_page_id ) );
            } else {
                $property_url = add_query_arg( 'property_id', $property['id'] );
            }
            $property_url = apply_filters( 'bwg_property_card_url', $property_url, $property );
        ?>
        <a href="<?php echo esc_url( $property_url ); ?>" class="bwg-property-card bwg-property-card--linked">
            <?php if ( ! empty( $property['images'] ) ) : ?>
                <div class="bwg-property-card__image">
                    <img
                        src="<?php echo esc_url( $property['images'][0]['url'] ?? '' ); ?>"
                        alt="<?php echo esc_attr( $property['name'] ?? '' ); ?>"
                    />
                </div>
            <?php endif; ?>
            <div class="bwg-property-card__content">
                <h3 class="bwg-property-card__title">
                    <?php echo esc_html( $property['name'] ?? '' ); ?>
                </h3>
                <div class="bwg-property-specs">
                    <?php if ( isset( $property['bedrooms'] ) ) : ?>
                        <span class="bwg-property-specs__item">
                            <?php echo esc_html( $property['bedrooms'] ); ?> <?php esc_html_e( 'Beds', 'bwg-rentals' ); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ( isset( $property['bathrooms'] ) ) : ?>
                        <span class="bwg-property-specs__item">
                            <?php echo esc_html( $property['bathrooms'] ); ?> <?php esc_html_e( 'Baths', 'bwg-rentals' ); ?>
                        </span>
                    <?php endif; ?>
                    <?php
                    // Normalize guests field - API may return 'guests' or 'sleeps'
                    $guests = isset( $property['guests'] ) ? $property['guests'] : ( isset( $property['sleeps'] ) ? $property['sleeps'] : null );
                    if ( $guests ) :
                    ?>
                        <span class="bwg-property-specs__item">
                            <?php echo esc_html( $guests ); ?> <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    </div>
</div>
