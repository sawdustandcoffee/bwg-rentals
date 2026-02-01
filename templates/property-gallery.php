<?php
/**
 * Property Gallery Template
 *
 * Supports multiple layouts:
 * - slider: Main image with navigation arrows, thumbnails below
 * - grid: All images in a grid layout
 * - lightbox: Grid with lightbox on click
 *
 * @package BWG_Rentals
 * @var array $property Property data.
 * @var array $atts     Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Debug output - enable with ?bwg_debug=1 in URL
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$bwg_debug = isset( $_GET['bwg_debug'] ) && current_user_can( 'manage_options' );
if ( $bwg_debug ) : ?>
<div style="background:#1e1e1e;color:#0f0;padding:15px;margin:10px 0;font-family:monospace;font-size:12px;border-radius:5px;overflow:auto;">
    <strong style="color:#ff0;">BWG Property Gallery Debug: <?php echo esc_html( $property['name'] ?? 'Unknown' ); ?></strong><br><br>
    <strong>ID:</strong> <?php echo esc_html( $property['id'] ?? 'N/A' ); ?><br>
    <strong>Layout:</strong> <?php echo esc_html( $atts['layout'] ?? 'slider' ); ?><br>
    <strong>Images:</strong> <?php echo count( $property['images'] ?? [] ); ?> total<br>
    <?php if ( ! empty( $property['images'] ) ) : ?>
        <?php foreach ( array_slice( $property['images'], 0, 3 ) as $i => $img ) : ?>
            <strong>Image <?php echo $i + 1; ?>:</strong> <?php echo esc_html( $img['url'] ?? 'NO URL' ); ?><br>
        <?php endforeach; ?>
        <?php if ( count( $property['images'] ) > 3 ) : ?>
            <em>... and <?php echo count( $property['images'] ) - 3; ?> more</em><br>
        <?php endif; ?>
    <?php else : ?>
        <strong style="color:#f00;">NO IMAGES!</strong><br>
    <?php endif; ?>
</div>
<script>
console.log('BWG Property Gallery:', <?php echo wp_json_encode( $property['images'] ?? [] ); ?>);
</script>
<?php endif;

$images = $property['images'] ?? array();
$layout = $atts['layout'];
$gallery_id = 'bwg-gallery-' . uniqid();

if ( empty( $images ) ) {
    return;
}
?>
<?php if ( 'slider' === $layout ) : ?>
    <div class="bwg-property-gallery bwg-property-gallery--slider" id="<?php echo esc_attr( $gallery_id ); ?>">
        <!-- Main Image Display -->
        <div class="bwg-property-gallery__main">
            <div class="bwg-property-gallery__slides" data-current="0">
                <?php foreach ( $images as $index => $image ) : ?>
                    <div class="bwg-property-gallery__slide<?php echo 0 === $index ? ' bwg-property-gallery__slide--active' : ''; ?>"
                         data-index="<?php echo esc_attr( $index ); ?>"
                         role="button"
                         tabindex="0"
                         aria-label="<?php esc_attr_e( 'Click to open lightbox', 'bwg-rentals' ); ?>">
                        <img
                            src="<?php echo esc_url( $image['url'] ?? '' ); ?>"
                            alt="<?php echo esc_attr( $image['alt'] ?? $property['name'] ?? '' ); ?>"
                            loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>"
                        />
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if ( count( $images ) > 1 ) : ?>
                <button class="bwg-property-gallery__nav bwg-property-gallery__nav--prev" aria-label="<?php esc_attr_e( 'Previous image', 'bwg-rentals' ); ?>">
                    &#8249;
                </button>
                <button class="bwg-property-gallery__nav bwg-property-gallery__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'bwg-rentals' ); ?>">
                    &#8250;
                </button>
                <div class="bwg-property-gallery__counter">
                    <span class="bwg-property-gallery__current">1</span> / <?php echo count( $images ); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Thumbnails -->
        <?php if ( count( $images ) > 1 ) : ?>
        <div class="bwg-property-gallery__thumbnails">
            <?php foreach ( $images as $index => $image ) :
                $thumb_url = ! empty( $image['small'] ) ? $image['small'] : ( ! empty( $image['medium'] ) ? $image['medium'] : $image['url'] );
            ?>
                <button class="bwg-property-gallery__thumbnail<?php echo 0 === $index ? ' bwg-property-gallery__thumbnail--active' : ''; ?>"
                        data-index="<?php echo esc_attr( $index ); ?>"
                        aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'bwg-rentals' ), $index + 1 ) ); ?>">
                    <img
                        src="<?php echo esc_url( $thumb_url ); ?>"
                        alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
                        loading="lazy"
                    />
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

<?php elseif ( 'grid' === $layout ) : ?>
    <div class="bwg-property-gallery bwg-property-gallery--grid" id="<?php echo esc_attr( $gallery_id ); ?>">
        <?php foreach ( $images as $index => $image ) : ?>
            <div class="bwg-property-gallery__grid-item"
                 data-index="<?php echo esc_attr( $index ); ?>"
                 role="button"
                 tabindex="0"
                 aria-label="<?php esc_attr_e( 'Click to open lightbox', 'bwg-rentals' ); ?>">
                <img
                    src="<?php echo esc_url( ! empty( $image['medium'] ) ? $image['medium'] : $image['url'] ); ?>"
                    data-full="<?php echo esc_url( $image['url'] ?? '' ); ?>"
                    alt="<?php echo esc_attr( $image['alt'] ?? $property['name'] ?? '' ); ?>"
                    loading="lazy"
                />
            </div>
        <?php endforeach; ?>
    </div>

<?php elseif ( 'lightbox' === $layout ) : ?>
    <div class="bwg-property-gallery bwg-property-gallery--lightbox-grid" id="<?php echo esc_attr( $gallery_id ); ?>">
        <?php foreach ( $images as $index => $image ) : ?>
            <div class="bwg-property-gallery__grid-item"
                 data-index="<?php echo esc_attr( $index ); ?>"
                 role="button"
                 tabindex="0"
                 aria-label="<?php esc_attr_e( 'Click to open lightbox', 'bwg-rentals' ); ?>">
                <img
                    src="<?php echo esc_url( ! empty( $image['medium'] ) ? $image['medium'] : $image['url'] ); ?>"
                    data-full="<?php echo esc_url( $image['url'] ?? '' ); ?>"
                    alt="<?php echo esc_attr( $image['alt'] ?? $property['name'] ?? '' ); ?>"
                    loading="lazy"
                />
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Lightbox Modal (shared by all layouts) -->
<div class="bwg-lightbox" id="<?php echo esc_attr( $gallery_id ); ?>-lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Image gallery lightbox', 'bwg-rentals' ); ?>">
    <div class="bwg-lightbox__overlay"></div>
    <div class="bwg-lightbox__content">
        <button class="bwg-lightbox__close" aria-label="<?php esc_attr_e( 'Close lightbox', 'bwg-rentals' ); ?>">&times;</button>
        <div class="bwg-lightbox__image-container">
            <img class="bwg-lightbox__image" src="" alt="" />
        </div>
        <?php if ( count( $images ) > 1 ) : ?>
            <button class="bwg-lightbox__nav bwg-lightbox__nav--prev" aria-label="<?php esc_attr_e( 'Previous image', 'bwg-rentals' ); ?>">&#8249;</button>
            <button class="bwg-lightbox__nav bwg-lightbox__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'bwg-rentals' ); ?>">&#8250;</button>
            <div class="bwg-lightbox__counter">
                <span class="bwg-lightbox__current">1</span> / <?php echo count( $images ); ?>
            </div>
        <?php endif; ?>
    </div>
    <!-- Store images data for JavaScript -->
    <script type="application/json" class="bwg-lightbox__data">
        <?php echo wp_json_encode( array_map( function( $img ) {
            return array(
                'url' => $img['url'] ?? '',
                'alt' => $img['alt'] ?? '',
            );
        }, $images ) ); ?>
    </script>
</div>
