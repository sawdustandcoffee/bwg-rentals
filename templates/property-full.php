<?php
/**
 * Property Full Page Template - Complete Redesign
 *
 * Feature #131: Complete redesign matching vacation rental booking site standards.
 * Includes: full-width gallery, icon row, sticky navigation, description toggle,
 * amenities lightbox, bedroom/bathroom cards, policies, reviews, and sticky sidebar.
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

// Extract guest count (support both 'guests' and 'sleeps')
$guest_count = isset( $property['guests'] ) ? $property['guests'] : ( isset( $property['sleeps'] ) ? $property['sleeps'] : 0 );

// Build navigation sections list
$nav_sections = array();
if ( 'true' === $atts['show_description'] || 'true' === $atts['show_amenities'] ) {
	$nav_sections['overview'] = __( 'Overview', 'bwg-rentals' );
}
if ( 'true' === $atts['show_location'] && ! empty( $property['address'] ) ) {
	$nav_sections['location'] = __( 'Location', 'bwg-rentals' );
}
// Reviews section is always shown (even if empty, shows placeholder)
$nav_sections['reviews'] = __( 'Reviews', 'bwg-rentals' );
?>

<div class="bwg-property-full bwg-property-full--redesigned bwg-property-full--<?php echo esc_attr( $layout ); ?>">

	<?php // ===================== GALLERY SECTION (Full Width) ===================== ?>
	<?php if ( 'true' === $atts['show_gallery'] && ! empty( $property['images'] ) ) : ?>
		<?php
		$gallery_id = 'bwg-full-gallery-' . uniqid();
		$gallery_images = array_map( function( $img ) {
			return array(
				'url'    => $img['url'] ?? '',
				'medium' => $img['medium'] ?? $img['url'] ?? '',
				'small'  => $img['small'] ?? $img['medium'] ?? $img['url'] ?? '',
				'alt'    => $img['alt'] ?? '',
			);
		}, $property['images'] );
		$first_image = $gallery_images[0] ?? array();
		$first_medium = $first_image['medium'] ?? $first_image['url'] ?? '';
		?>
		<div id="bwg-section-gallery" class="bwg-property-full__gallery">
			<div class="bwg-property-gallery bwg-property-gallery--full" id="<?php echo esc_attr( $gallery_id ); ?>">
				<div class="bwg-property-gallery__main-container">
					<div class="bwg-property-gallery__main-image"
						 role="button"
						 tabindex="0"
						 aria-label="<?php esc_attr_e( 'Click to open lightbox', 'bwg-rentals' ); ?>">
						<img
							class="bwg-property-gallery__main-img"
							src="<?php echo esc_url( $first_medium ); ?>"
							data-full="<?php echo esc_url( $first_image['url'] ?? '' ); ?>"
							alt="<?php echo esc_attr( $first_image['alt'] ?? $property['name'] ?? '' ); ?>"
						/>
						<div class="bwg-property-gallery__loading" style="display:none;">
							<span class="bwg-spinner"></span>
						</div>
					</div>
					<?php if ( count( $property['images'] ) > 1 ) : ?>
						<button class="bwg-property-gallery__nav bwg-property-gallery__nav--prev" aria-label="<?php esc_attr_e( 'Previous image', 'bwg-rentals' ); ?>">&#8249;</button>
						<button class="bwg-property-gallery__nav bwg-property-gallery__nav--next" aria-label="<?php esc_attr_e( 'Next image', 'bwg-rentals' ); ?>">&#8250;</button>
						<div class="bwg-property-gallery__counter">
							<span class="bwg-property-gallery__current">1</span> / <?php echo count( $property['images'] ); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( count( $property['images'] ) > 1 ) : ?>
				<div class="bwg-property-gallery__thumb-strip">
					<button class="bwg-property-gallery__thumb-nav bwg-property-gallery__thumb-nav--prev" aria-label="<?php esc_attr_e( 'Scroll thumbnails left', 'bwg-rentals' ); ?>">&#8249;</button>
					<div class="bwg-property-gallery__thumb-track">
						<div class="bwg-property-gallery__thumb-slider">
							<?php foreach ( $gallery_images as $index => $image ) : ?>
								<button class="bwg-property-gallery__thumb<?php echo 0 === $index ? ' bwg-property-gallery__thumb--active' : ''; ?>"
										data-index="<?php echo esc_attr( $index ); ?>"
										aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'bwg-rentals' ), $index + 1 ) ); ?>">
									<img
										src="<?php echo esc_url( $image['small'] ); ?>"
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

				<script type="application/json" class="bwg-gallery__data">
					<?php echo wp_json_encode( $gallery_images ); ?>
				</script>
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
				<script type="application/json" class="bwg-lightbox__data">
					<?php echo wp_json_encode( array_map( function( $img ) {
						return array(
							'url' => $img['url'] ?? '',
							'alt' => $img['alt'] ?? '',
						);
					}, $gallery_images ) ); ?>
				</script>
			</div>
		</div>
	<?php endif; ?>

	<?php // ===================== TWO-COLUMN LAYOUT ===================== ?>
	<div class="bwg-property-full__layout bwg-property-full__layout--redesigned">

		<?php // ===================== MAIN CONTENT COLUMN ===================== ?>
		<div class="bwg-property-full__content bwg-property-full__content--redesigned">

			<?php // ===================== HEADER: Title + Subtitle + Icon Row ===================== ?>
			<?php if ( 'true' === $atts['show_title'] ) : ?>
				<header class="bwg-property-header bwg-property-header--redesigned">
					<h1 class="bwg-property-header__title"><?php echo esc_html( $property['name'] ?? '' ); ?></h1>
					<?php if ( ! empty( $property['headline'] ) ) : ?>
						<p class="bwg-property-header__subtitle"><?php echo esc_html( $property['headline'] ); ?></p>
					<?php endif; ?>

					<?php // Icon Row ?>
					<div class="bwg-property-header__icons">
						<?php if ( ! empty( $property['address']['city'] ) || ! empty( $property['address']['state'] ) ) : ?>
							<span class="bwg-property-header__icon-item">
								<svg class="bwg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
								<span><?php
									$location_parts = array();
									if ( ! empty( $property['address']['city'] ) ) {
										$location_parts[] = $property['address']['city'];
									}
									if ( ! empty( $property['address']['state'] ) ) {
										$location_parts[] = $property['address']['state'];
									}
									echo esc_html( implode( ', ', $location_parts ) );
								?></span>
							</span>
						<?php endif; ?>
						<span class="bwg-property-header__icon-item">
							<svg class="bwg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
							<span><?php esc_html_e( 'House', 'bwg-rentals' ); ?></span>
						</span>
						<?php if ( isset( $property['bedrooms'] ) && $property['bedrooms'] > 0 ) : ?>
							<span class="bwg-property-header__icon-item">
								<svg class="bwg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
								<span><?php echo esc_html( $property['bedrooms'] ); ?> <?php echo esc_html( _n( 'Bed', 'Beds', $property['bedrooms'], 'bwg-rentals' ) ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( isset( $property['bathrooms'] ) && $property['bathrooms'] > 0 ) : ?>
							<span class="bwg-property-header__icon-item">
								<svg class="bwg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="10" x2="8" y1="5" y2="7"/><line x1="2" x2="22" y1="12" y2="12"/><line x1="7" x2="7" y1="19" y2="21"/><line x1="17" x2="17" y1="19" y2="21"/></svg>
								<span><?php echo esc_html( $property['bathrooms'] ); ?> <?php echo esc_html( _n( 'Bath', 'Baths', $property['bathrooms'], 'bwg-rentals' ) ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( $guest_count > 0 ) : ?>
							<span class="bwg-property-header__icon-item">
								<svg class="bwg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
								<span><?php echo esc_html( $guest_count ); ?> <?php echo esc_html( _n( 'Guest', 'Guests', $guest_count, 'bwg-rentals' ) ); ?></span>
							</span>
						<?php endif; ?>
					</div>
				</header>
			<?php endif; ?>

			<?php // ===================== STICKY NAVIGATION MENU ===================== ?>
			<nav class="bwg-property-nav" id="bwg-property-nav" aria-label="<?php esc_attr_e( 'Property sections', 'bwg-rentals' ); ?>">
				<ul class="bwg-property-nav__list">
					<?php foreach ( $nav_sections as $section_id => $section_label ) : ?>
						<li class="bwg-property-nav__item">
							<a href="#bwg-section-<?php echo esc_attr( $section_id ); ?>" class="bwg-property-nav__link" data-section="<?php echo esc_attr( $section_id ); ?>">
								<?php echo esc_html( $section_label ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<?php // ===================== CHECK-IN / CHECK-OUT TIMES ===================== ?>
			<?php
			$check_in_time = $property['policies']['check_in'] ?? $property['check_in_time'] ?? '';
			$check_out_time = $property['policies']['check_out'] ?? $property['check_out_time'] ?? '';
			if ( $check_in_time || $check_out_time ) :
			?>
			<div class="bwg-property-times">
				<?php if ( $check_in_time ) : ?>
					<span class="bwg-property-times__item">
						<strong><?php esc_html_e( 'Check-in:', 'bwg-rentals' ); ?></strong>
						<?php echo esc_html( $check_in_time ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $check_out_time ) : ?>
					<span class="bwg-property-times__item">
						<strong><?php esc_html_e( 'Check-out:', 'bwg-rentals' ); ?></strong>
						<?php echo esc_html( $check_out_time ); ?>
					</span>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php // ===================== OVERVIEW SECTION ===================== ?>
			<div id="bwg-section-overview" class="bwg-property-section bwg-property-section--overview">

				<?php // ===================== DESCRIPTION with Show More/Less ===================== ?>
				<?php if ( 'true' === $atts['show_description'] && ! empty( $property['description'] ) ) : ?>
					<div class="bwg-property-description-section">
						<h2 class="bwg-property-section__title"><?php esc_html_e( 'About This Property', 'bwg-rentals' ); ?></h2>
						<div class="bwg-property-description bwg-property-description--collapsible" data-collapsed="true">
							<div class="bwg-property-description__content">
								<?php echo wp_kses_post( wpautop( $property['description'] ) ); ?>
							</div>
							<div class="bwg-property-description__gradient"></div>
						</div>
						<button class="bwg-property-description__toggle" type="button" aria-expanded="false">
							<span class="bwg-property-description__toggle-text"><?php esc_html_e( 'Show More', 'bwg-rentals' ); ?></span>
							<svg class="bwg-icon bwg-property-description__toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
						</button>
					</div>
				<?php endif; ?>

				<?php // ===================== AMENITIES with Show All Modal ===================== ?>
				<?php if ( 'true' === $atts['show_amenities'] && ! empty( $property['amenities'] ) ) : ?>
					<?php
					$all_amenities = $property['amenities'];
					$visible_amenities = array_slice( $all_amenities, 0, 10 );
					$has_more = count( $all_amenities ) > 10;

					// Amenity icon mapping
					$amenity_icons = array(
						'wifi' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><circle cx="12" cy="20" r="1"/></svg>',
						'kitchen' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>',
						'pool' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12h20"/><path d="M6 12v5c0 2 3 3 6 3s6-1 6-3v-5"/><circle cx="12" cy="6" r="3"/></svg>',
						'tv' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>',
						'parking' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>',
						'ac' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v10"/><path d="M18.5 7l-5.2 5.2"/><path d="M5.5 7l5.2 5.2"/><path d="M4.22 14h15.56"/><path d="M3 18h18"/><path d="M4.22 22h15.56"/></svg>',
						'washer' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 8V2"/></svg>',
						'dryer' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M8 6h8"/></svg>',
						'default' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
					);

					function bwg_get_amenity_icon( $amenity_name, $icons ) {
						$name_lower = strtolower( $amenity_name );
						foreach ( $icons as $key => $icon ) {
							if ( false !== strpos( $name_lower, $key ) ) {
								return $icon;
							}
						}
						return $icons['default'];
					}
					?>
					<div class="bwg-property-amenities-section">
						<h2 class="bwg-property-section__title"><?php esc_html_e( 'Amenities', 'bwg-rentals' ); ?></h2>
						<div class="bwg-property-amenities bwg-property-amenities--grid">
							<?php foreach ( $visible_amenities as $amenity ) :
								$amenity_name = is_array( $amenity ) ? ( $amenity['name'] ?? '' ) : $amenity;
								if ( empty( $amenity_name ) ) continue;
							?>
								<div class="bwg-property-amenities__item">
									<span class="bwg-property-amenities__icon"><?php echo bwg_get_amenity_icon( $amenity_name, $amenity_icons ); ?></span>
									<span class="bwg-property-amenities__name"><?php echo esc_html( $amenity_name ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
						<?php if ( $has_more ) : ?>
							<button class="bwg-property-amenities__show-all" type="button" data-amenities-modal>
								<?php printf( esc_html__( 'Show All %d Amenities', 'bwg-rentals' ), count( $all_amenities ) ); ?>
							</button>

							<!-- Amenities Lightbox/Modal -->
							<div class="bwg-modal bwg-modal--amenities" id="bwg-amenities-modal" role="dialog" aria-modal="true" aria-labelledby="bwg-amenities-modal-title">
								<div class="bwg-modal__overlay"></div>
								<div class="bwg-modal__content">
									<div class="bwg-modal__header">
										<h3 class="bwg-modal__title" id="bwg-amenities-modal-title"><?php esc_html_e( 'All Amenities', 'bwg-rentals' ); ?></h3>
										<button class="bwg-modal__close" type="button" aria-label="<?php esc_attr_e( 'Close', 'bwg-rentals' ); ?>">&times;</button>
									</div>
									<div class="bwg-modal__body">
										<div class="bwg-property-amenities bwg-property-amenities--modal-grid">
											<?php foreach ( $all_amenities as $amenity ) :
												$amenity_name = is_array( $amenity ) ? ( $amenity['name'] ?? '' ) : $amenity;
												if ( empty( $amenity_name ) ) continue;
											?>
												<div class="bwg-property-amenities__item">
													<span class="bwg-property-amenities__icon"><?php echo bwg_get_amenity_icon( $amenity_name, $amenity_icons ); ?></span>
													<span class="bwg-property-amenities__name"><?php echo esc_html( $amenity_name ); ?></span>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php // ===================== BEDROOM INFORMATION CARDS ===================== ?>
				<?php
				$bedrooms_data = $property['bedrooms_details'] ?? $property['rooms'] ?? array();
				if ( ! empty( $bedrooms_data ) || ( isset( $property['bedrooms'] ) && $property['bedrooms'] > 0 ) ) :
				?>
				<div class="bwg-property-rooms-section">
					<h2 class="bwg-property-section__title"><?php esc_html_e( 'Sleeping Arrangements', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-rooms bwg-property-rooms--bedrooms">
						<?php if ( ! empty( $bedrooms_data ) ) : ?>
							<?php foreach ( $bedrooms_data as $index => $bedroom ) : ?>
								<div class="bwg-property-rooms__card">
									<div class="bwg-property-rooms__card-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
									</div>
									<div class="bwg-property-rooms__card-content">
										<h4 class="bwg-property-rooms__card-title">
											<?php echo esc_html( $bedroom['name'] ?? sprintf( __( 'Bedroom %d', 'bwg-rentals' ), $index + 1 ) ); ?>
										</h4>
										<p class="bwg-property-rooms__card-detail">
											<?php echo esc_html( $bedroom['bed_type'] ?? $bedroom['beds'] ?? __( '1 Bed', 'bwg-rentals' ) ); ?>
										</p>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<?php // Fallback: Generate cards based on bedroom count ?>
							<?php for ( $i = 1; $i <= min( $property['bedrooms'], 6 ); $i++ ) : ?>
								<div class="bwg-property-rooms__card">
									<div class="bwg-property-rooms__card-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
									</div>
									<div class="bwg-property-rooms__card-content">
										<h4 class="bwg-property-rooms__card-title"><?php printf( esc_html__( 'Bedroom %d', 'bwg-rentals' ), $i ); ?></h4>
									</div>
								</div>
							<?php endfor; ?>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

				<?php // ===================== BATHROOM INFORMATION CARDS ===================== ?>
				<?php
				$bathrooms_data = $property['bathrooms_details'] ?? array();
				if ( ! empty( $bathrooms_data ) || ( isset( $property['bathrooms'] ) && $property['bathrooms'] > 0 ) ) :
				?>
				<div class="bwg-property-rooms-section">
					<h2 class="bwg-property-section__title"><?php esc_html_e( 'Bathrooms', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-rooms bwg-property-rooms--bathrooms">
						<?php if ( ! empty( $bathrooms_data ) ) : ?>
							<?php foreach ( $bathrooms_data as $index => $bathroom ) : ?>
								<div class="bwg-property-rooms__card">
									<div class="bwg-property-rooms__card-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="10" x2="8" y1="5" y2="7"/><line x1="2" x2="22" y1="12" y2="12"/><line x1="7" x2="7" y1="19" y2="21"/><line x1="17" x2="17" y1="19" y2="21"/></svg>
									</div>
									<div class="bwg-property-rooms__card-content">
										<h4 class="bwg-property-rooms__card-title">
											<?php echo esc_html( $bathroom['name'] ?? sprintf( __( 'Bathroom %d', 'bwg-rentals' ), $index + 1 ) ); ?>
										</h4>
										<?php if ( ! empty( $bathroom['type'] ) ) : ?>
											<p class="bwg-property-rooms__card-detail"><?php echo esc_html( $bathroom['type'] ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<?php // Fallback: Generate cards based on bathroom count ?>
							<?php for ( $i = 1; $i <= min( $property['bathrooms'], 6 ); $i++ ) : ?>
								<div class="bwg-property-rooms__card">
									<div class="bwg-property-rooms__card-icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="10" x2="8" y1="5" y2="7"/><line x1="2" x2="22" y1="12" y2="12"/><line x1="7" x2="7" y1="19" y2="21"/><line x1="17" x2="17" y1="19" y2="21"/></svg>
									</div>
									<div class="bwg-property-rooms__card-content">
										<h4 class="bwg-property-rooms__card-title"><?php printf( esc_html__( 'Bathroom %d', 'bwg-rentals' ), $i ); ?></h4>
									</div>
								</div>
							<?php endfor; ?>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

			</div><!-- /.bwg-property-section--overview -->

			<?php // ===================== POLICIES SECTIONS ===================== ?>
			<?php if ( 'true' === $atts['show_policies'] ) : ?>
				<?php
				$policies = $property['policies'] ?? array();
				$terms = $property['terms'] ?? $policies['terms'] ?? '';
				$cancellation = $policies['cancellation'] ?? '';
				$house_rules = $policies['house_rules'] ?? '';
				?>

				<?php if ( $terms ) : ?>
				<div class="bwg-property-section bwg-property-section--terms">
					<h2 class="bwg-property-section__title"><?php esc_html_e( 'Terms', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-policy-content">
						<?php echo wp_kses_post( wpautop( $terms ) ); ?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $cancellation ) : ?>
				<div class="bwg-property-section bwg-property-section--cancellation">
					<h2 class="bwg-property-section__title"><?php esc_html_e( 'Cancellation Policy', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-policy-content">
						<?php echo wp_kses_post( wpautop( $cancellation ) ); ?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $check_in_time || $check_out_time || $house_rules ) : ?>
				<div class="bwg-property-section bwg-property-section--checkin-policy">
					<h2 class="bwg-property-section__title"><?php esc_html_e( 'Check-in/Check-out Policy', 'bwg-rentals' ); ?></h2>
					<div class="bwg-property-policy-content">
						<?php if ( $check_in_time ) : ?>
							<p><strong><?php esc_html_e( 'Check-in time:', 'bwg-rentals' ); ?></strong> <?php echo esc_html( $check_in_time ); ?></p>
						<?php endif; ?>
						<?php if ( $check_out_time ) : ?>
							<p><strong><?php esc_html_e( 'Check-out time:', 'bwg-rentals' ); ?></strong> <?php echo esc_html( $check_out_time ); ?></p>
						<?php endif; ?>
						<?php if ( $house_rules ) : ?>
							<h4><?php esc_html_e( 'House Rules', 'bwg-rentals' ); ?></h4>
							<?php echo wp_kses_post( wpautop( $house_rules ) ); ?>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php // ===================== LOCATION SECTION with MAP ===================== ?>
			<?php if ( 'true' === $atts['show_location'] && ! empty( $property['address'] ) ) : ?>
				<?php
				$address_parts = array();
				$city_state_zip = array();
				if ( ! empty( $property['address']['city'] ) ) {
					$city_state_zip[] = $property['address']['city'];
				}
				if ( ! empty( $property['address']['state'] ) ) {
					$city_state_zip[] = $property['address']['state'];
				}
				if ( ! empty( $city_state_zip ) ) {
					$address_parts[] = implode( ', ', $city_state_zip );
				}
				if ( ! empty( $property['address']['country'] ) ) {
					$address_parts[] = $property['address']['country'];
				}
				$display_address = implode( ', ', $address_parts );

				// Check for coordinates
				$has_coordinates = isset( $property['latitude'] ) && isset( $property['longitude'] );
				$google_maps_api_key = '';
				if ( class_exists( 'BWG_Admin' ) ) {
					$google_maps_api_key = BWG_Admin::decrypt_value( get_option( 'bwg_rentals_google_maps_api_key', '' ) );
				}
				$use_google_maps = ! empty( $google_maps_api_key );
				$map_id = 'bwg-map-' . uniqid();
				?>
				<div id="bwg-section-location" class="bwg-property-section bwg-property-section--location">
					<h2 class="bwg-property-section__title"><?php esc_html_e( 'Location', 'bwg-rentals' ); ?></h2>
					<p class="bwg-property-location__address"><?php echo esc_html( $display_address ); ?></p>

					<?php if ( $has_coordinates ) : ?>
						<?php
						$lat = floatval( $property['latitude'] );
						$lon = floatval( $property['longitude'] );
						?>

						<?php if ( $use_google_maps ) : ?>
							<div class="bwg-property-location__map-container">
								<div
									id="<?php echo esc_attr( $map_id ); ?>"
									class="bwg-property-location__google-map"
									data-lat="<?php echo esc_attr( $lat ); ?>"
									data-lng="<?php echo esc_attr( $lon ); ?>"
									data-title="<?php echo esc_attr( $property['name'] ?? '' ); ?>"
									data-address="<?php echo esc_attr( $display_address ); ?>"
								>
									<div class="bwg-property-location__map-loading">
										<span class="bwg-spinner"></span>
										<span><?php esc_html_e( 'Loading map...', 'bwg-rentals' ); ?></span>
									</div>
								</div>
							</div>
						<?php else : ?>
							<?php
							$osm_url = sprintf(
								'https://www.openstreetmap.org/export/embed.html?bbox=%s,%s,%s,%s&layer=mapnik&marker=%s,%s',
								esc_attr( $lon - 0.01 ),
								esc_attr( $lat - 0.01 ),
								esc_attr( $lon + 0.01 ),
								esc_attr( $lat + 0.01 ),
								esc_attr( $lat ),
								esc_attr( $lon )
							);
							?>
							<div class="bwg-property-location__map-container">
								<iframe
									class="bwg-property-location__map"
									width="100%"
									height="350"
									loading="lazy"
									src="<?php echo esc_url( $osm_url ); ?>"
									title="<?php echo esc_attr__( 'Property location map', 'bwg-rentals' ); ?>"
								></iframe>
							</div>
						<?php endif; ?>
						<p class="bwg-property-location__note">
							<svg class="bwg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
							<?php esc_html_e( 'Exact address will be provided upon booking confirmation.', 'bwg-rentals' ); ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php // ===================== REVIEWS SECTION ===================== ?>
			<div id="bwg-section-reviews" class="bwg-property-section bwg-property-section--reviews">
				<h2 class="bwg-property-section__title"><?php esc_html_e( 'Reviews', 'bwg-rentals' ); ?></h2>
				<?php
				$reviews = $property['reviews'] ?? array();
				$review_count = count( $reviews );
				$average_rating = $property['rating'] ?? $property['average_rating'] ?? 0;
				?>

				<?php if ( $review_count > 0 ) : ?>
					<div class="bwg-property-reviews__summary">
						<span class="bwg-property-reviews__rating">
							<svg class="bwg-icon bwg-icon--star" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
							<?php echo esc_html( number_format( $average_rating, 1 ) ); ?>
						</span>
						<span class="bwg-property-reviews__count">(<?php echo esc_html( $review_count ); ?> <?php echo esc_html( _n( 'review', 'reviews', $review_count, 'bwg-rentals' ) ); ?>)</span>
					</div>
					<div class="bwg-property-reviews__list">
						<?php foreach ( $reviews as $review ) : ?>
							<div class="bwg-property-reviews__item">
								<div class="bwg-property-reviews__item-header">
									<span class="bwg-property-reviews__author"><?php echo esc_html( $review['author'] ?? __( 'Guest', 'bwg-rentals' ) ); ?></span>
									<?php if ( ! empty( $review['date'] ) ) : ?>
										<span class="bwg-property-reviews__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $review['date'] ) ) ); ?></span>
									<?php endif; ?>
								</div>
								<?php if ( ! empty( $review['rating'] ) ) : ?>
									<div class="bwg-property-reviews__item-rating">
										<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
											<svg class="bwg-icon bwg-icon--star <?php echo $i <= $review['rating'] ? 'bwg-icon--star-filled' : ''; ?>" viewBox="0 0 24 24" fill="<?php echo $i <= $review['rating'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
										<?php endfor; ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $review['text'] ) || ! empty( $review['content'] ) ) : ?>
									<p class="bwg-property-reviews__item-text"><?php echo esc_html( $review['text'] ?? $review['content'] ); ?></p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="bwg-property-reviews__empty"><?php esc_html_e( 'No reviews yet. Be the first to review this property after your stay!', 'bwg-rentals' ); ?></p>
				<?php endif; ?>
			</div>

		</div><!-- /.bwg-property-full__content -->

		<?php // ===================== STICKY BOOKING SIDEBAR ===================== ?>
		<aside class="bwg-property-full__sidebar">
			<div class="bwg-property-sidebar bwg-property-sidebar--redesigned">

				<?php // Price Display ?>
				<?php
				$base_rate = 0;
				if ( ! is_wp_error( $rates ) && isset( $rates['base_rate'] ) ) {
					$base_rate = $rates['base_rate'];
				} elseif ( isset( $property['base_rate'] ) ) {
					$base_rate = $property['base_rate'];
				} elseif ( isset( $property['price'] ) ) {
					$base_rate = $property['price'];
				}
				?>
				<?php if ( $base_rate > 0 ) : ?>
				<div class="bwg-property-sidebar__price">
					<span class="bwg-property-sidebar__price-amount">$<?php echo esc_html( number_format( $base_rate ) ); ?></span>
					<span class="bwg-property-sidebar__price-period"><?php esc_html_e( '/ night', 'bwg-rentals' ); ?></span>
				</div>
				<?php endif; ?>

				<?php // Rating Display ?>
				<?php
				$reviews = $property['reviews'] ?? array();
				$review_count = count( $reviews );
				$average_rating = $property['rating'] ?? $property['average_rating'] ?? 0;
				if ( $average_rating > 0 || $review_count > 0 ) :
				?>
				<div class="bwg-property-sidebar__rating">
					<svg class="bwg-icon bwg-icon--star" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<span class="bwg-property-sidebar__rating-value"><?php echo esc_html( $average_rating > 0 ? number_format( $average_rating, 1 ) : 'New' ); ?></span>
					<?php if ( $review_count > 0 ) : ?>
						<span class="bwg-property-sidebar__rating-count">(<?php echo esc_html( $review_count ); ?> <?php echo esc_html( _n( 'review', 'reviews', $review_count, 'bwg-rentals' ) ); ?>)</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php // Date Pickers ?>
				<div class="bwg-property-sidebar__dates">
					<div class="bwg-property-sidebar__date-field">
						<label for="bwg-checkin"><?php esc_html_e( 'Check-in', 'bwg-rentals' ); ?></label>
						<input type="date" id="bwg-checkin" name="checkin" class="bwg-property-sidebar__date-input" />
					</div>
					<div class="bwg-property-sidebar__date-field">
						<label for="bwg-checkout"><?php esc_html_e( 'Check-out', 'bwg-rentals' ); ?></label>
						<input type="date" id="bwg-checkout" name="checkout" class="bwg-property-sidebar__date-input" />
					</div>
				</div>

				<?php // Guest Selector ?>
				<div class="bwg-property-sidebar__guests">
					<label for="bwg-guests"><?php esc_html_e( 'Guests', 'bwg-rentals' ); ?></label>
					<select id="bwg-guests" name="guests" class="bwg-property-sidebar__guest-select">
						<?php
						$max_guests = $guest_count > 0 ? $guest_count : 10;
						for ( $i = 1; $i <= $max_guests; $i++ ) :
						?>
							<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?> <?php echo esc_html( _n( 'guest', 'guests', $i, 'bwg-rentals' ) ); ?></option>
						<?php endfor; ?>
					</select>
				</div>

				<?php // Book Now Button ?>
				<?php if ( 'true' === $atts['show_booking_button'] ) : ?>
					<a
						href="<?php echo esc_url( $booking_url ); ?>"
						class="bwg-property-sidebar__book-button"
						target="_blank"
						rel="noopener noreferrer"
					>
						<?php echo esc_html( get_option( 'bwg_rentals_booking_button_text', __( 'Reserve', 'bwg-rentals' ) ) ); ?>
					</a>
				<?php endif; ?>

				<p class="bwg-property-sidebar__note"><?php esc_html_e( 'You won\'t be charged yet', 'bwg-rentals' ); ?></p>

			</div>
		</aside>

	</div><!-- /.bwg-property-full__layout -->

	<?php // ===================== RELATED PROPERTIES ===================== ?>
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
											<?php echo esc_html( $related_prop['bedrooms'] . ' ' . _n( 'bed', 'beds', $related_prop['bedrooms'], 'bwg-rentals' ) ); ?>
										</span>
									<?php endif; ?>
									<?php if ( isset( $related_prop['bathrooms'] ) ) : ?>
										<span class="bwg-related-property-card__spec">
											<?php echo esc_html( $related_prop['bathrooms'] . ' ' . _n( 'bath', 'baths', $related_prop['bathrooms'], 'bwg-rentals' ) ); ?>
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
