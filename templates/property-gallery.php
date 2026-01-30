<?php
/**
 * Property Gallery Template
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$images = $property['images'] ?? array();
$layout = $atts['layout'];

if ( empty( $images ) ) {
    return;
}
?>
<?php if ( 'slider' === $layout ) : ?>
    <div class="bwg-property-gallery bwg-property-gallery--slider">
        <div class="bwg-property-gallery__slider">
            <div class="bwg-property-gallery__slides">
                <?php foreach ( $images as $image ) : ?>
                    <div class="bwg-property-gallery__slide">
                        <img
                            src="<?php echo esc_url( $image['url'] ?? '' ); ?>"
                            alt="<?php echo esc_attr( $image['alt'] ?? $property['name'] ?? '' ); ?>"
                        />
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ( count( $images ) > 1 ) : ?>
                <button class="bwg-property-gallery__nav bwg-property-gallery__nav--prev" aria-label="<?php esc_attr_e( 'Previous', 'bwg-rentals' ); ?>">
                    &#8249;
                </button>
                <button class="bwg-property-gallery__nav bwg-property-gallery__nav--next" aria-label="<?php esc_attr_e( 'Next', 'bwg-rentals' ); ?>">
                    &#8250;
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php elseif ( 'grid' === $layout || 'lightbox' === $layout ) : ?>
    <div class="bwg-property-gallery bwg-property-gallery--grid">
        <?php foreach ( $images as $image ) : ?>
            <img
                src="<?php echo esc_url( $image['url'] ?? '' ); ?>"
                alt="<?php echo esc_attr( $image['alt'] ?? $property['name'] ?? '' ); ?>"
            />
        <?php endforeach; ?>
    </div>
<?php endif; ?>
