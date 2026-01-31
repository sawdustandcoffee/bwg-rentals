<?php
/**
 * Property Slider Template
 *
 * Displays properties in a carousel/slider format.
 *
 * @package BWG_Rentals
 * @var array $properties Array of properties.
 * @var array $atts       Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Generate unique ID for this slider instance
$slider_id = 'bwg-slider-' . wp_rand( 1000, 9999 );

// Determine which navigation elements to show
$show_arrows = in_array( $atts['navigation'], array( 'arrows', 'both' ), true );
$show_dots   = in_array( $atts['navigation'], array( 'dots', 'both' ), true );
?>
<div class="bwg-property-slider" id="<?php echo esc_attr( $slider_id ); ?>" data-slider-id="<?php echo esc_attr( $slider_id ); ?>" data-autoplay="<?php echo esc_attr( $atts['autoplay'] ); ?>" data-speed="<?php echo esc_attr( $atts['speed'] ); ?>" data-slides-to-show="<?php echo esc_attr( $atts['slides_to_show'] ); ?>" data-slides-to-scroll="<?php echo esc_attr( $atts['slides_to_scroll'] ); ?>">
    <div class="bwg-property-slider__container">
        <div class="bwg-property-slider__track">
            <?php foreach ( $properties as $index => $property ) : ?>
                <div class="bwg-property-slider__slide" data-slide-index="<?php echo absint( $index ); ?>">
                    <div class="bwg-property-card">
                        <?php if ( ! empty( $property['images'] ) ) : ?>
                            <div class="bwg-property-card__image">
                                <img
                                    src="<?php echo esc_url( $property['images'][0]['url'] ?? '' ); ?>"
                                    alt="<?php echo esc_attr( $property['name'] ?? '' ); ?>"
                                    loading="lazy"
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
                                <?php if ( isset( $property['guests'] ) ) : ?>
                                    <span class="bwg-property-specs__item">
                                        <?php echo esc_html( $property['guests'] ); ?> <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ( $show_arrows ) : ?>
    <!-- Navigation Controls -->
    <button class="bwg-property-slider__nav bwg-property-slider__nav--prev" aria-label="<?php esc_attr_e( 'Previous property', 'bwg-rentals' ); ?>">
        <span aria-hidden="true">&lsaquo;</span>
    </button>
    <button class="bwg-property-slider__nav bwg-property-slider__nav--next" aria-label="<?php esc_attr_e( 'Next property', 'bwg-rentals' ); ?>">
        <span aria-hidden="true">&rsaquo;</span>
    </button>
    <?php endif; ?>

    <?php if ( $show_dots ) : ?>
    <!-- Slide Indicators/Dots -->
    <div class="bwg-property-slider__indicators">
        <?php foreach ( $properties as $index => $property ) : ?>
            <button
                class="bwg-property-slider__indicator<?php echo 0 === $index ? ' bwg-property-slider__indicator--active' : ''; ?>"
                data-slide-to="<?php echo absint( $index ); ?>"
                aria-label="<?php echo esc_attr( sprintf( __( 'Go to property %d', 'bwg-rentals' ), $index + 1 ) ); ?>"
            ></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
