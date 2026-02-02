<?php
/**
 * Property Full Page Template
 *
 * Displays a complete property page with all sections.
 *
 * @package BWG_Rentals
 * @var array $property     Property data.
 * @var array $availability Availability data.
 * @var array $rates        Rates data.
 * @var array $atts         Shortcode attributes.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$layout = isset( $atts['layout'] ) ? $atts['layout'] : 'standard';

// Get booking URL using the helper function
$booking_url = bwg_get_booking_url( $property );

// Build list of visible sections for anchor navigation
$sections = array();
if ( 'true' === $atts['show_gallery'] && ! empty( $property['images'] ) ) {
	$sections['gallery'] = __( 'Photos', 'bwg-rentals' );
}
if ( 'true' === $atts['show_description'] && ! empty( $property['description'] ) ) {
	$sections['description'] = __( 'Description', 'bwg-rentals' );
}
if ( 'true' === $atts['show_amenities'] && ! empty( $property['amenities'] ) ) {
	$sections['amenities'] = __( 'Amenities', 'bwg-rentals' );
}
if ( 'true' === $atts['show_availability'] && ! is_wp_error( $availability ) && ! empty( $availability['availability'] ) ) {
	$sections['availability'] = __( 'Availability', 'bwg-rentals' );
}
if ( 'true' === $atts['show_rates'] && ! is_wp_error( $rates ) ) {
	$sections['rates'] = __( 'Rates', 'bwg-rentals' );
}
if ( 'true' === $atts['show_location'] && ! empty( $property['address'] ) ) {
	$sections['location'] = __( 'Location', 'bwg-rentals' );
}
if ( 'true' === $atts['show_policies'] && ! empty( $property['policies'] ) ) {
	$sections['policies'] = __( 'Policies', 'bwg-rentals' );
}
?>

<div class="bwg-property-full bwg-property-full--<?php echo esc_attr( $layout ); ?>">

	<?php if ( 'true' === $atts['show_breadcrumbs'] ) : ?>
		<nav class="bwg-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'bwg-rentals' ); ?>">
			<ol class="bwg-breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
				<li class="bwg-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bwg-breadcrumbs__link" itemprop="item">
						<span itemprop="name"><?php esc_html_e( 'Home', 'bwg-rentals' ); ?></span>
					</a>
					<meta itemprop="position" content="1" />
				</li>
				<li class="bwg-breadcrumbs__separator" aria-hidden="true">/</li>
				<li class="bwg-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<?php
					// Try to find a properties archive page
					$properties_page = get_posts( array(
						'post_type'   => 'page',
						'post_status' => 'publish',
						'meta_query'  => array(
							array(
								'key'     => '_bwg_is_properties_archive',
								'compare' => 'EXISTS',
							),
						),
						'numberposts' => 1,
					) );

					if ( ! empty( $properties_page ) ) :
						$properties_url = get_permalink( $properties_page[0]->ID );
						?>
						<a href="<?php echo esc_url( $properties_url ); ?>" class="bwg-breadcrumbs__link" itemprop="item">
							<span itemprop="name"><?php esc_html_e( 'Properties', 'bwg-rentals' ); ?></span>
						</a>
					<?php else : ?>
						<span itemprop="name"><?php esc_html_e( 'Properties', 'bwg-rentals' ); ?></span>
					<?php endif; ?>
					<meta itemprop="position" content="2" />
				</li>
				<li class="bwg-breadcrumbs__separator" aria-hidden="true">/</li>
				<li class="bwg-breadcrumbs__item bwg-breadcrumbs__item--current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
					<span itemprop="name" aria-current="page"><?php echo esc_html( $property['name'] ); ?></span>
					<meta itemprop="position" content="3" />
				</li>
			</ol>
		</nav>
	<?php endif; ?>

	<?php if ( 'true' === $atts['show_gallery'] && ! empty( $property['images'] ) ) : ?>
		<?php $gallery_id = 'bwg-full-gallery-' . uniqid(); ?>
		<div id="bwg-section-gallery" class="bwg-property-full__gallery">
			<div class="bwg-property-gallery bwg-property-gallery--full" id="<?php echo esc_attr( $gallery_id ); ?>">
				<!-- Main Image Display -->
				<div class="bwg-property-gallery__main-container">
					<div class="bwg-property-gallery__main-image">
						<?php foreach ( $property['images'] as $index => $image ) : ?>
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
					<?php if ( count( $property['images'] ) > 1 ) : ?>
						<button class="bwg-property-gallery__nav bwg-property-gallery__nav--prev" aria-label="<?php esc_attr_e( 'Previous image', 'bwg-rentals' ); ?>">&#8249;</button>
						<button class="bwg-property-gallery__nav bwg-property-gallery__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'bwg-rentals' ); ?>">&#8250;</button>
						<div class="bwg-property-gallery__counter">
							<span class="bwg-property-gallery__current">1</span> / <?php echo count( $property['images'] ); ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Thumbnail Strip -->
				<?php if ( count( $property['images'] ) > 1 ) : ?>
				<div class="bwg-property-gallery__thumb-strip">
					<button class="bwg-property-gallery__thumb-nav bwg-property-gallery__thumb-nav--prev" aria-label="<?php esc_attr_e( 'Scroll thumbnails left', 'bwg-rentals' ); ?>">&#8249;</button>
					<div class="bwg-property-gallery__thumb-track">
						<div class="bwg-property-gallery__thumb-slider">
							<?php foreach ( $property['images'] as $index => $image ) :
								$thumb_url = ! empty( $image['small'] ) ? $image['small'] : ( ! empty( $image['medium'] ) ? $image['medium'] : $image['url'] );
							?>
								<button class="bwg-property-gallery__thumb<?php echo 0 === $index ? ' bwg-property-gallery__thumb--active' : ''; ?>"
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
					</div>
					<button class="bwg-property-gallery__thumb-nav bwg-property-gallery__thumb-nav--next" aria-label="<?php esc_attr_e( 'Scroll thumbnails right', 'bwg-rentals' ); ?>">&#8250;</button>
				</div>
				<?php endif; ?>
			</div>

			<!-- Lightbox Modal -->
			<div class="bwg-lightbox" id="<?php echo esc_attr( $gallery_id ); ?>-lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Image gallery lightbox', 'bwg-rentals' ); ?>">
				<div class="bwg-lightbox__overlay"></div>
				<div class="bwg-lightbox__content">
					<button class="bwg-lightbox__close" aria-label="<?php esc_attr_e( 'Close lightbox', 'bwg-rentals' ); ?>">&times;</button>
					<div class="bwg-lightbox__image-container">
						<img class="bwg-lightbox__image" src="" alt="" />
					</div>
					<?php if ( count( $property['images'] ) > 1 ) : ?>
						<button class="bwg-lightbox__nav bwg-lightbox__nav--prev" aria-label="<?php esc_attr_e( 'Previous image', 'bwg-rentals' ); ?>">&#8249;</button>
						<button class="bwg-lightbox__nav bwg-lightbox__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'bwg-rentals' ); ?>">&#8250;</button>
						<div class="bwg-lightbox__counter">
							<span class="bwg-lightbox__current">1</span> / <?php echo count( $property['images'] ); ?>
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
					}, $property['images'] ) ); ?>
				</script>
			</div>
		</div>
	<?php endif; ?>

	<!-- Two-column layout: Main content + Sticky sidebar -->
	<div class="bwg-property-full__layout">

		<!-- Main Content Column -->
		<div class="bwg-property-full__content">

			<?php if ( 'true' === $atts['show_title'] ) : ?>
				<div class="bwg-property-full__section bwg-property-full__section--title">
					<h1 class="bwg-property-title"><?php echo esc_html( $property['name'] ?? '' ); ?></h1>
					<?php if ( ! empty( $property['headline'] ) ) : ?>
						<p class="bwg-property-headline"><?php echo esc_html( $property['headline'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_specs'] ) : ?>
				<div class="bwg-property-full__section bwg-property-full__section--specs">
					<div class="bwg-property-specs">
						<?php if ( isset( $property['bedrooms'] ) ) : ?>
							<span class="bwg-property-specs__item">
								<span class="bwg-property-specs__icon">🛏️</span>
								<span class="bwg-property-specs__value"><?php echo esc_html( $property['bedrooms'] ); ?></span>
								<span class="bwg-property-specs__label"><?php echo esc_html( _n( 'Bed', 'Beds', $property['bedrooms'], 'bwg-rentals' ) ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( isset( $property['bathrooms'] ) ) : ?>
							<span class="bwg-property-specs__item">
								<span class="bwg-property-specs__icon">🚿</span>
								<span class="bwg-property-specs__value"><?php echo esc_html( $property['bathrooms'] ); ?></span>
								<span class="bwg-property-specs__label"><?php echo esc_html( _n( 'Bath', 'Baths', $property['bathrooms'], 'bwg-rentals' ) ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( isset( $property['guests'] ) ) : ?>
							<span class="bwg-property-specs__item">
								<span class="bwg-property-specs__icon">👥</span>
								<span class="bwg-property-specs__value"><?php echo esc_html( $property['guests'] ); ?></span>
								<span class="bwg-property-specs__label"><?php echo esc_html( _n( 'Guest', 'Guests', $property['guests'], 'bwg-rentals' ) ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( isset( $property['sqft'] ) ) : ?>
							<span class="bwg-property-specs__item">
								<span class="bwg-property-specs__icon">📏</span>
								<span class="bwg-property-specs__value"><?php echo esc_html( number_format( $property['sqft'] ) ); ?></span>
								<span class="bwg-property-specs__label"><?php esc_html_e( 'sq ft', 'bwg-rentals' ); ?></span>
							</span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_description'] && ! empty( $property['description'] ) ) : ?>
				<div id="bwg-section-description" class="bwg-property-full__section bwg-property-full__section--description">
					<h2><?php esc_html_e( 'Description', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-description">
						<?php echo wp_kses_post( wpautop( $property['description'] ) ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_amenities'] && ! empty( $property['amenities'] ) ) : ?>
				<div id="bwg-section-amenities" class="bwg-property-full__section bwg-property-full__section--amenities">
					<h2><?php esc_html_e( 'Amenities', 'bwg-rentals' ); ?></h2>
					<ul class="bwg-property-amenities bwg-property-amenities--columns-2">
						<?php foreach ( $property['amenities'] as $amenity ) : ?>
							<li class="bwg-property-amenities__item">
								<span class="bwg-property-amenities__icon">✓</span>
								<span class="bwg-property-amenities__name"><?php echo esc_html( $amenity ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_availability'] && ! is_wp_error( $availability ) && ! empty( $availability['availability'] ) ) : ?>
				<div id="bwg-section-availability" class="bwg-property-full__section bwg-property-full__section--availability">
					<h2><?php esc_html_e( 'Availability', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-availability">
						<div class="bwg-property-availability__calendar">
							<?php
							// Group availability by month
							$availability_by_month = array();
							foreach ( $availability['availability'] as $day ) {
								$month_key = date( 'Y-m', strtotime( $day['date'] ) );
								if ( ! isset( $availability_by_month[ $month_key ] ) ) {
									$availability_by_month[ $month_key ] = array();
								}
								$availability_by_month[ $month_key ][] = $day;
							}

							// Show first 3 months
							$months_shown = 0;
							foreach ( $availability_by_month as $month_key => $days ) :
								if ( $months_shown >= 3 ) {
									break;
								}
								$month_name = date( 'F Y', strtotime( $month_key . '-01' ) );
								?>
								<div class="bwg-property-availability__month">
									<h3 class="bwg-property-availability__month-name"><?php echo esc_html( $month_name ); ?></h3>
									<div class="bwg-property-availability__grid">
										<?php foreach ( $days as $day ) : ?>
											<div class="bwg-property-availability__day <?php echo $day['available'] ? 'bwg-property-availability__day--available' : 'bwg-property-availability__day--unavailable'; ?>">
												<span class="bwg-property-availability__date"><?php echo esc_html( date( 'j', strtotime( $day['date'] ) ) ); ?></span>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
								<?php
								$months_shown++;
							endforeach;
							?>
						</div>
						<div class="bwg-property-availability__legend">
							<span class="bwg-property-availability__legend-item">
								<span class="bwg-property-availability__legend-color bwg-property-availability__legend-color--available"></span>
								<?php esc_html_e( 'Available', 'bwg-rentals' ); ?>
							</span>
							<span class="bwg-property-availability__legend-item">
								<span class="bwg-property-availability__legend-color bwg-property-availability__legend-color--unavailable"></span>
								<?php esc_html_e( 'Unavailable', 'bwg-rentals' ); ?>
							</span>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_rates'] && ! is_wp_error( $rates ) ) : ?>
				<div id="bwg-section-rates" class="bwg-property-full__section bwg-property-full__section--rates">
					<h2><?php esc_html_e( 'Rates', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-rates">
						<?php if ( isset( $rates['base_rate'] ) ) : ?>
							<div class="bwg-property-rates__base">
								<span class="bwg-property-rates__label"><?php esc_html_e( 'Base Rate:', 'bwg-rentals' ); ?></span>
								<span class="bwg-property-rates__value">
									<?php echo esc_html( '$' . number_format( $rates['base_rate'] ) ); ?>
									<span class="bwg-property-rates__period"><?php esc_html_e( '/ night', 'bwg-rentals' ); ?></span>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $rates['seasonal_rates'] ) ) : ?>
							<div class="bwg-property-rates__seasonal">
								<h3><?php esc_html_e( 'Seasonal Rates', 'bwg-rentals' ); ?></h3>
								<ul class="bwg-property-rates__list">
									<?php foreach ( $rates['seasonal_rates'] as $season ) : ?>
										<li class="bwg-property-rates__item">
											<span class="bwg-property-rates__season"><?php echo esc_html( $season['season'] ); ?></span>
											<span class="bwg-property-rates__amount"><?php echo esc_html( '$' . number_format( $season['rate'] ) ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $rates['discounts'] ) ) : ?>
							<div class="bwg-property-rates__discounts">
								<h3><?php esc_html_e( 'Discounts', 'bwg-rentals' ); ?></h3>
								<ul class="bwg-property-rates__list">
									<?php foreach ( $rates['discounts'] as $discount ) : ?>
										<li class="bwg-property-rates__item">
											<span class="bwg-property-rates__type"><?php echo esc_html( $discount['type'] ); ?></span>
											<span class="bwg-property-rates__amount"><?php echo esc_html( $discount['amount'] ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_location'] && ! empty( $property['address'] ) ) : ?>
				<div id="bwg-section-location" class="bwg-property-full__section bwg-property-full__section--location">
					<h2><?php esc_html_e( 'Location', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-location">
						<div class="bwg-property-location__address">
							<?php
							$address_parts = array();
							if ( ! empty( $property['address']['street'] ) ) {
								$address_parts[] = $property['address']['street'];
							}
							$city_state_zip = array();
							if ( ! empty( $property['address']['city'] ) ) {
								$city_state_zip[] = $property['address']['city'];
							}
							if ( ! empty( $property['address']['state'] ) ) {
								$city_state_zip[] = $property['address']['state'];
							}
							if ( ! empty( $property['address']['zip'] ) ) {
								$city_state_zip[] = $property['address']['zip'];
							}
							if ( ! empty( $city_state_zip ) ) {
								$address_parts[] = implode( ', ', $city_state_zip );
							}
							if ( ! empty( $property['address']['country'] ) ) {
								$address_parts[] = $property['address']['country'];
							}
							echo esc_html( implode( ', ', $address_parts ) );
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_policies'] && ! empty( $property['policies'] ) ) : ?>
				<div id="bwg-section-policies" class="bwg-property-full__section bwg-property-full__section--policies">
					<h2><?php esc_html_e( 'Policies', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-policies">
						<?php if ( ! empty( $property['policies']['check_in'] ) ) : ?>
							<div class="bwg-property-policies__item">
								<strong class="bwg-property-policies__label"><?php esc_html_e( 'Check-in:', 'bwg-rentals' ); ?></strong>
								<span class="bwg-property-policies__value"><?php echo esc_html( $property['policies']['check_in'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $property['policies']['check_out'] ) ) : ?>
							<div class="bwg-property-policies__item">
								<strong class="bwg-property-policies__label"><?php esc_html_e( 'Check-out:', 'bwg-rentals' ); ?></strong>
								<span class="bwg-property-policies__value"><?php echo esc_html( $property['policies']['check_out'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $property['policies']['cancellation'] ) ) : ?>
							<div class="bwg-property-policies__item">
								<strong class="bwg-property-policies__label"><?php esc_html_e( 'Cancellation:', 'bwg-rentals' ); ?></strong>
								<span class="bwg-property-policies__value"><?php echo esc_html( $property['policies']['cancellation'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $property['policies']['house_rules'] ) ) : ?>
							<div class="bwg-property-policies__item">
								<strong class="bwg-property-policies__label"><?php esc_html_e( 'House Rules:', 'bwg-rentals' ); ?></strong>
								<span class="bwg-property-policies__value"><?php echo esc_html( $property['policies']['house_rules'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $property['policies']['cleaning_fee'] ) ) : ?>
							<div class="bwg-property-policies__item">
								<strong class="bwg-property-policies__label"><?php esc_html_e( 'Cleaning Fee:', 'bwg-rentals' ); ?></strong>
								<span class="bwg-property-policies__value"><?php echo esc_html( $property['policies']['cleaning_fee'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $property['policies']['security_deposit'] ) ) : ?>
							<div class="bwg-property-policies__item">
								<strong class="bwg-property-policies__label"><?php esc_html_e( 'Security Deposit:', 'bwg-rentals' ); ?></strong>
								<span class="bwg-property-policies__value"><?php echo esc_html( $property['policies']['security_deposit'] ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'true' === $atts['show_booking_button'] ) : ?>
				<div class="bwg-property-full__section bwg-property-full__section--booking">
					<a
						href="<?php echo esc_url( $booking_url ); ?>"
						class="bwg-property-booking-button"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php echo esc_html( get_option( 'bwg_rentals_booking_button_text', 'Book Now' ) ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div><!-- .bwg-property-full__content -->

		<!-- Sticky Booking Sidebar -->
		<aside class="bwg-property-full__sidebar">
			<div class="bwg-property-sidebar">
				<div class="bwg-property-sidebar__inner">

					<!-- Property Name -->
					<h3 class="bwg-property-sidebar__title"><?php echo esc_html( $property['name'] ?? '' ); ?></h3>

					<!-- Key Specs -->
					<?php if ( isset( $property['bedrooms'] ) || isset( $property['bathrooms'] ) || isset( $property['guests'] ) ) : ?>
						<div class="bwg-property-sidebar__specs">
							<?php if ( isset( $property['bedrooms'] ) ) : ?>
								<span class="bwg-property-sidebar__spec">
									<span class="bwg-property-sidebar__spec-icon">🛏️</span>
									<span class="bwg-property-sidebar__spec-value"><?php echo esc_html( $property['bedrooms'] ); ?> <?php echo esc_html( _n( 'Bed', 'Beds', $property['bedrooms'], 'bwg-rentals' ) ); ?></span>
								</span>
							<?php endif; ?>
							<?php if ( isset( $property['bathrooms'] ) ) : ?>
								<span class="bwg-property-sidebar__spec">
									<span class="bwg-property-sidebar__spec-icon">🚿</span>
									<span class="bwg-property-sidebar__spec-value"><?php echo esc_html( $property['bathrooms'] ); ?> <?php echo esc_html( _n( 'Bath', 'Baths', $property['bathrooms'], 'bwg-rentals' ) ); ?></span>
								</span>
							<?php endif; ?>
							<?php if ( isset( $property['guests'] ) ) : ?>
								<span class="bwg-property-sidebar__spec">
									<span class="bwg-property-sidebar__spec-icon">👥</span>
									<span class="bwg-property-sidebar__spec-value"><?php echo esc_html( $property['guests'] ); ?> <?php echo esc_html( _n( 'Guest', 'Guests', $property['guests'], 'bwg-rentals' ) ); ?></span>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<!-- Starting Rate -->
					<?php if ( ! is_wp_error( $rates ) && isset( $rates['base_rate'] ) ) : ?>
						<div class="bwg-property-sidebar__rate">
							<span class="bwg-property-sidebar__rate-label"><?php esc_html_e( 'Starting at:', 'bwg-rentals' ); ?></span>
							<span class="bwg-property-sidebar__rate-amount">
								<?php echo esc_html( '$' . number_format( $rates['base_rate'] ) ); ?>
								<span class="bwg-property-sidebar__rate-period"><?php esc_html_e( '/ night', 'bwg-rentals' ); ?></span>
							</span>
						</div>
					<?php endif; ?>

					<!-- Booking Button -->
					<?php if ( 'true' === $atts['show_booking_button'] ) : ?>
						<a
							href="<?php echo esc_url( $booking_url ); ?>"
							class="bwg-property-sidebar__button"
							target="_blank"
							rel="noopener noreferrer"
						>
							<?php echo esc_html( get_option( 'bwg_rentals_booking_button_text', 'Book Now' ) ); ?>
						</a>
					<?php endif; ?>

					<!-- Contact/Inquiry Link (Optional) -->
					<div class="bwg-property-sidebar__contact">
						<a href="<?php echo esc_url( $booking_url ); ?>" class="bwg-property-sidebar__link" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Questions? Contact us', 'bwg-rentals' ); ?>
						</a>
					</div>

					<!-- Section Anchor Navigation -->
					<?php if ( 'true' === $atts['show_anchors'] && ! empty( $sections ) ) : ?>
						<nav class="bwg-property-anchors" aria-label="<?php esc_attr_e( 'Jump to section', 'bwg-rentals' ); ?>">
							<h4 class="bwg-property-anchors__title"><?php esc_html_e( 'On this page', 'bwg-rentals' ); ?></h4>
							<ul class="bwg-property-anchors__list">
								<?php foreach ( $sections as $id => $label ) : ?>
									<li class="bwg-property-anchors__item">
										<a href="#bwg-section-<?php echo esc_attr( $id ); ?>" class="bwg-property-anchors__link">
											<?php echo esc_html( $label ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

				</div>
			</div>
		</aside>

	</div><!-- .bwg-property-full__layout -->

	<?php if ( 'true' === $atts['show_related'] && ! empty( $related_properties ) ) : ?>
		<div class="bwg-property-full__related">
			<h2 class="bwg-property-full__related-title"><?php esc_html_e( 'You May Also Like', 'bwg-rentals' ); ?></h2>
			<div class="bwg-related-properties">
				<?php foreach ( $related_properties as $related_prop ) : ?>
					<div class="bwg-related-property-card">
						<?php if ( ! empty( $related_prop['images'][0]['url'] ) ) : ?>
							<div class="bwg-related-property-card__image">
								<img
									src="<?php echo esc_url( $related_prop['images'][0]['url'] ); ?>"
									alt="<?php echo esc_attr( $related_prop['name'] ?? '' ); ?>"
									loading="lazy"
								/>
							</div>
						<?php endif; ?>
						<div class="bwg-related-property-card__content">
							<h3 class="bwg-related-property-card__title">
								<?php echo esc_html( $related_prop['name'] ?? '' ); ?>
							</h3>
							<?php if ( isset( $related_prop['bedrooms'] ) || isset( $related_prop['bathrooms'] ) || isset( $related_prop['guests'] ) ) : ?>
								<div class="bwg-related-property-card__specs">
									<?php if ( isset( $related_prop['bedrooms'] ) ) : ?>
										<span class="bwg-related-property-card__spec">
											🛏️ <?php echo esc_html( $related_prop['bedrooms'] . ' ' . _n( 'bed', 'beds', $related_prop['bedrooms'], 'bwg-rentals' ) ); ?>
										</span>
									<?php endif; ?>
									<?php if ( isset( $related_prop['bathrooms'] ) ) : ?>
										<span class="bwg-related-property-card__spec">
											🚿 <?php echo esc_html( $related_prop['bathrooms'] . ' ' . _n( 'bath', 'baths', $related_prop['bathrooms'], 'bwg-rentals' ) ); ?>
										</span>
									<?php endif; ?>
									<?php if ( isset( $related_prop['guests'] ) ) : ?>
										<span class="bwg-related-property-card__spec">
											👥 <?php echo esc_html( $related_prop['guests'] . ' ' . _n( 'guest', 'guests', $related_prop['guests'], 'bwg-rentals' ) ); ?>
										</span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<a
								href="<?php echo esc_url( bwg_get_booking_url( $related_prop ) ); ?>"
								class="bwg-related-property-card__link"
								target="_blank"
								rel="noopener noreferrer"
							>
								<?php esc_html_e( 'View Property', 'bwg-rentals' ); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

</div>
