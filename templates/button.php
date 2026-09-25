<?php
/**
 * Button template for pmpro-downloads.
 *
 * Displays a download as a prominent CTA button with two-line text.
 *
 * @since 1.0
 *
 * @var array $template_vars Template variables passed from the shortcode.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $template_vars['has_access'] ) : ?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro pmpro_download pmpro_download-button' ) ); ?>">
		<?php if ( ! empty( $template_vars['embed_image'] ) && pmpro_downloads_is_image( $template_vars['file_type'] ) ) : ?>
			<a href="<?php echo esc_url( $template_vars['download_url'] ); ?>">
				<img class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-image' ) ); ?>" src="<?php echo esc_url( $template_vars['download_url'] ); ?>" alt="<?php echo esc_attr( $template_vars['display_name'] ); ?>" loading="lazy" />
			</a>
		<?php endif; ?>
		<a class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-download' ) ); ?>" href="<?php echo esc_url( $template_vars['download_url'] ); ?>">
			<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-icon' ) ); ?>">
				<?php echo pmpro_downloads_get_icon_svg( 'download', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG markup; size is absint(). ?>
			</span>
			<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-title' ) ); ?>">
				<?php printf( esc_html__( 'Download %s', 'pmpro-downloads' ), esc_html( $template_vars['display_name'] ) ); ?>
			</span>
		</a>
	</div>

<?php else : ?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro pmpro_download pmpro_download-button pmpro_download-locked' ) ); ?>">
		<a class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-download' ) ); ?>" href="<?php echo esc_url( $template_vars['no_access_url'] ); ?>">
			<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-icon' ) ); ?>">
				<?php echo pmpro_downloads_get_icon_svg( 'lock', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG markup; size is absint(). ?>
			</span>
			<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-title' ) ); ?>">
				<?php printf( esc_html__( 'Download %s', 'pmpro-downloads' ), esc_html( $template_vars['display_name'] ) ); ?>
			</span>
		</a>
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-levels' ) ); ?>">
			<?php
			if ( ! empty( $template_vars['level_names'] ) ) {
				/* translators: %s: membership level name(s) */
				printf( esc_html__( 'Requires %s membership', 'pmpro-downloads' ), esc_html( pmpro_implodeToEnglish( $template_vars['level_names'], 'or' ) ) );
			} else {
				esc_html_e( 'Requires membership', 'pmpro-downloads' );
			}
			?>
		</div>
	</div>
<?php endif; ?>
