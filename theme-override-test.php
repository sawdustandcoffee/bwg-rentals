<?php
/**
 * Property Card Template - THEME OVERRIDE
 *
 * This is a theme override template for testing Feature #59.
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
<div class="bwg-property-card bwg-theme-override">
    <!-- THEME OVERRIDE MARKER: Feature #59 Test -->
    <div class="bwg-property-card__override-notice" style="background: #28a745; color: white; padding: 8px; margin-bottom: 10px; border-radius: 4px; font-weight: bold;">
        THEME OVERRIDE ACTIVE - Feature #59 Verified
    </div>
    <?php if ( $show_image && ! empty( $property['images'] ) ) : ?>
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
        <?php if ( $show_specs ) : ?>
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
        <?php endif; ?>
    </div>
</div>
