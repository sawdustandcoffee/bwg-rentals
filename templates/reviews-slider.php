<?php
/**
 * Reviews Slider Template
 *
 * Displays reviews in a carousel/slider format.
 * Reuses the bwg-property-slider class structure so existing JS handles sliding.
 *
 * @package BWG_Rentals
 * @var array $reviews Array of reviews.
 * @var array $atts    Shortcode attributes.
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

$style_class          = 'bwg-review-card--' . esc_attr( $atts['style'] );
$show_property_name   = 'true' === $atts['show_property_name'];
?>
<div class="bwg-reviews-slider bwg-property-slider" id="<?php echo esc_attr( $slider_id ); ?>" data-slider-id="<?php echo esc_attr( $slider_id ); ?>" data-autoplay="<?php echo esc_attr( $atts['autoplay'] ); ?>" data-speed="<?php echo esc_attr( $atts['speed'] ); ?>" data-slides-to-show="<?php echo esc_attr( $atts['slides_to_show'] ); ?>" data-slides-to-scroll="<?php echo esc_attr( $atts['slides_to_scroll'] ); ?>" data-loop="<?php echo esc_attr( $atts['loop'] ); ?>" data-pause-on-hover="<?php echo esc_attr( $atts['pause_on_hover'] ); ?>">
    <div class="bwg-property-slider__container">
        <div class="bwg-property-slider__track">
            <?php foreach ( $reviews as $index => $review ) : ?>
                <div class="bwg-property-slider__slide" data-slide-index="<?php echo absint( $index ); ?>">
                    <div class="bwg-review-card <?php echo esc_attr( $style_class ); ?>">

                        <?php if ( 'testimonial' === $atts['style'] ) : ?>
                            <div class="bwg-review-card__quote-icon" aria-hidden="true">&ldquo;</div>
                        <?php endif; ?>

                        <?php
                        // Star rating
                        $rating = isset( $review['rating'] ) ? max( 1, min( 5, intval( $review['rating'] ) ) ) : 5;
                        ?>
                        <div class="bwg-review-card__stars" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'bwg-rentals' ), $rating ) ); ?>">
                            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                <span class="bwg-review-card__star <?php echo $i <= $rating ? 'bwg-review-card__star--filled' : 'bwg-review-card__star--empty'; ?>" aria-hidden="true">&#9733;</span>
                            <?php endfor; ?>
                        </div>

                        <?php if ( ! empty( $review['title'] ) ) : ?>
                            <h3 class="bwg-review-card__title"><?php echo esc_html( $review['title'] ); ?></h3>
                        <?php endif; ?>

                        <?php if ( ! empty( $review['body'] ) ) : ?>
                            <div class="bwg-review-card__body">
                                <?php echo esc_html( wp_strip_all_tags( $review['body'] ) ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="bwg-review-card__footer">
                            <?php if ( ! empty( $review['reviewer_name'] ) ) : ?>
                                <span class="bwg-review-card__author"><?php echo esc_html( $review['reviewer_name'] ); ?></span>
                            <?php endif; ?>

                            <?php if ( ! empty( $review['date'] ) ) : ?>
                                <span class="bwg-review-card__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review['date'] ) ) ); ?></span>
                            <?php endif; ?>

                            <?php if ( $show_property_name && ! empty( $review['property_name'] ) ) : ?>
                                <span class="bwg-review-card__property"><?php echo esc_html( $review['property_name'] ); ?></span>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ( $show_arrows ) : ?>
    <button class="bwg-property-slider__nav bwg-property-slider__nav--prev" aria-label="<?php esc_attr_e( 'Previous review', 'bwg-rentals' ); ?>">
        <span aria-hidden="true">&lsaquo;</span>
    </button>
    <button class="bwg-property-slider__nav bwg-property-slider__nav--next" aria-label="<?php esc_attr_e( 'Next review', 'bwg-rentals' ); ?>">
        <span aria-hidden="true">&rsaquo;</span>
    </button>
    <?php endif; ?>

    <?php if ( $show_dots ) : ?>
    <div class="bwg-property-slider__indicators">
        <?php foreach ( $reviews as $index => $review ) : ?>
            <button
                class="bwg-property-slider__indicator<?php echo 0 === $index ? ' bwg-property-slider__indicator--active' : ''; ?>"
                data-slide-to="<?php echo absint( $index ); ?>"
                aria-label="<?php echo esc_attr( sprintf( __( 'Go to review %d', 'bwg-rentals' ), $index + 1 ) ); ?>"
            ></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
