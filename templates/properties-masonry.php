<?php
/**
 * Properties Masonry Template
 *
 * Displays properties in a Pinterest-style masonry grid layout.
 *
 * @package BWG_Rentals
 * @var array $properties Array of properties.
 * @var array $atts       Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$columns_class = 'bwg-properties--masonry-' . absint( $atts['columns'] );
?>
<div class="bwg-properties bwg-properties--masonry <?php echo esc_attr( $columns_class ); ?>" data-masonry>
	<?php foreach ( $properties as $property ) : ?>
		<div class="bwg-property-card bwg-property-card--masonry">
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
					<?php if ( isset( $property['guests'] ) ) : ?>
						<span class="bwg-property-specs__item">
							<?php echo esc_html( $property['guests'] ); ?> <?php esc_html_e( 'Guests', 'bwg-rentals' ); ?>
						</span>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $property['description'] ) ) : ?>
					<div class="bwg-property-card__excerpt">
						<?php
						// Show excerpt for masonry layout to create varying heights
						$excerpt = wp_trim_words( $property['description'], 15, '...' );
						echo wp_kses_post( $excerpt );
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
