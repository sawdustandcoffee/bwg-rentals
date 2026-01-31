<?php
/**
 * Property Card Template - THEME OVERRIDE TEST
 *
 * THIS IS A CUSTOM THEME OVERRIDE FOR TESTING FEATURE #54
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$show_image = 'true' === $atts['show_image'];
$show_specs = 'true' === $atts['show_specs'];
?>
<div class="bwg-property-card" style="border: 3px solid #ff6600; background-color: #fff9e6;">
    <!-- THEME OVERRIDE ACTIVE - Feature #54 Test -->
    <div style="background: #ff6600; color: white; padding: 5px; font-weight: bold; text-align: center; margin-bottom: 10px;">
        ✓ THEME OVERRIDE ACTIVE
    </div>
    <?php if ( $show_image && ! empty( $property['images'] ) ) : ?>
        <div class="bwg-property-card__image">
            <img
                src="<?php echo esc_url( $property['images'][0] ); ?>"
                alt="<?php echo esc_attr( $property['name'] ); ?>"
                loading="lazy"
            />
        </div>
    <?php endif; ?>
    <div class="bwg-property-card__content">
        <h3 class="bwg-property-card__title">
            <?php echo esc_html( $property['name'] ); ?>
        </h3>
        <?php if ( $show_specs ) : ?>
            <div class="bwg-property-specs">
                <?php if ( ! empty( $property['bedrooms'] ) ) : ?>
                    <span class="bwg-property-specs__item">
                        <?php echo esc_html( $property['bedrooms'] ); ?> Beds
                    </span>
                <?php endif; ?>
                <?php if ( ! empty( $property['bathrooms'] ) ) : ?>
                    <span class="bwg-property-specs__item">
                        <?php echo esc_html( $property['bathrooms'] ); ?> Baths
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
