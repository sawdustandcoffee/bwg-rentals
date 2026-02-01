<?php
/**
 * Property Card Template
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
    <strong style="color:#ff0;">BWG Property Card Debug: <?php echo esc_html( $property['name'] ?? 'Unknown' ); ?></strong><br><br>
    <strong>ID:</strong> <?php echo esc_html( $property['id'] ?? 'N/A' ); ?><br>
    <strong>Images:</strong> <?php echo count( $property['images'] ?? [] ); ?> total<br>
    <?php if ( ! empty( $property['images'][0]['url'] ) ) : ?>
        <strong>First image:</strong> <?php echo esc_html( $property['images'][0]['url'] ); ?><br>
    <?php else : ?>
        <strong style="color:#f00;">First image: MISSING!</strong><br>
    <?php endif; ?>
    <strong>Bedrooms:</strong> <?php echo esc_html( $property['bedrooms'] ?? 'N/A' ); ?><br>
    <strong>Bathrooms:</strong> <?php echo esc_html( $property['bathrooms'] ?? 'N/A' ); ?><br>
    <strong>Guests:</strong> <?php echo esc_html( $property['guests'] ?? $property['sleeps'] ?? 'N/A' ); ?><br>
    <strong>Keys:</strong> <?php echo esc_html( implode( ', ', array_keys( $property ) ) ); ?>
</div>
<script>
console.log('BWG Property Card:', <?php echo wp_json_encode( $property ); ?>);
</script>
<?php endif;

$show_image = 'true' === $atts['show_image'];
$show_specs = 'true' === $atts['show_specs'];
$enable_link = 'true' === $atts['link'];

// Generate property page URL if linking is enabled
$property_url = '#';
if ( $enable_link ) {
    // Get the property page setting or use current page
    $property_page_id = get_option( 'bwg_rentals_property_page', 0 );

    if ( $property_page_id > 0 ) {
        // Link to configured property page with property_id parameter
        $property_url = add_query_arg( 'property_id', $property['id'], get_permalink( $property_page_id ) );
    } else {
        // No configured page - use current page with property_id parameter
        $property_url = add_query_arg( 'property_id', $property['id'] );
    }

    // Allow developers to customize the URL
    $property_url = apply_filters( 'bwg_property_card_url', $property_url, $property );
}

// Determine if card should be wrapped in a link
$tag_open = $enable_link ? '<a href="' . esc_url( $property_url ) . '" class="bwg-property-card bwg-property-card--linked">' : '<div class="bwg-property-card">';
$tag_close = $enable_link ? '</a>' : '</div>';
?>
<?php echo $tag_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
        <?php endif; ?>
    </div>
<?php echo $tag_close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
