<?php
/**
 * Card template for pmpro-downloads.
 *
 * Displays a download as a PMPro card with title, description,
 * and action button.
 *
 * @since 1.0
 *
 * @var array $template_vars Template variables passed from the shortcode.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php if ( $template_vars['has_access'] ) : ?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro' ) ); ?>">
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card pmpro_download pmpro_download-card', 'pmpro_download-card' ) ); ?>">
			<h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_title pmpro_font-large' ) ); ?>">
				<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-icon' ) ); ?>">
					<?php echo pmpro_downloads_get_file_icon( $template_vars['file_extension'], 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG markup; size is absint(). ?>
				</span>
				<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-title' ) ); ?>">
					<?php echo esc_html( $template_vars['display_name'] ); ?>
				</span>
			</h2>
			<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
				<?php if ( ! empty( $template_vars['embed_image'] ) && pmpro_downloads_is_image( $template_vars['file_type'] ) ) : ?>
					<a href="<?php echo esc_url( $template_vars['download_url'] ); ?>">
						<img class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-image' ) ); ?>" src="<?php echo esc_url( $template_vars['download_url'] ); ?>" alt="<?php echo esc_attr( $template_vars['display_name'] ); ?>" loading="lazy" />
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $template_vars['description'] ) ) : ?>
					<?php echo wp_kses_post( $template_vars['description'] ); ?>
				<?php endif; ?>
				<p>
					<a class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-download' ) ); ?>" href="<?php echo esc_url( $template_vars['download_url'] ); ?>">
						<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-icon' ) ); ?>">
							<?php echo pmpro_downloads_get_icon_svg( 'download', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG markup; size is absint(). ?>
						</span>
						<?php esc_html_e( 'Download', 'pmpro-downloads' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>

<?php else : ?>
	<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro' ) ); ?>">
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card pmpro_download pmpro_download-card pmpro_download-locked', 'pmpro_download-card' ) ); ?>">
			<h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_title pmpro_font-large' ) ); ?>">
				<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-icon' ) ); ?>">
					<?php echo pmpro_downloads_get_icon_svg( 'lock', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG markup; size is absint(). ?>
				</span>
				<span class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-title' ) ); ?>">
					<?php echo esc_html( $template_vars['display_name'] ); ?>
				</span>
			</h2>
			<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
				<?php if ( ! empty( $template_vars['description'] ) ) : ?>
					<?php echo wp_kses_post( $template_vars['description'] ); ?>
				<?php endif; ?>
				<p class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_download-levels' ) ); ?>">
					<?php
					if ( ! empty( $template_vars['level_names'] ) ) {
						/* translators: %s: membership level name(s) */
						printf( esc_html__( 'You must be a %s member to access this download.', 'pmpro-downloads' ), esc_html( pmpro_implodeToEnglish( $template_vars['level_names'], __( 'or', 'pmpro-downloads' ) ) ) );
					} else {
						esc_html_e( 'You must be a member to access this download.', 'pmpro-downloads' );
					}
					?>
				</p>
				<p>
					<a class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn' ) ); ?>" href="<?php echo esc_url( $template_vars['no_access_url'] ); ?>">
						<?php
							if ( count( $template_vars['level_ids'] ) === 1 ) {
								esc_html_e( 'Join Now', 'pmpro-downloads' );
							} else {
								esc_html_e( 'View Membership Levels', 'pmpro-downloads' );
							}
						?>
					</a>
				</p>
			</div>
			<?php
				// If the user is not logged in, show a link to log in if the message doesn't already have one.
				if ( ! is_user_logged_in() ) : ?>
					<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_actions' ) ); ?>">
						<?php esc_html_e( 'Already a member?', 'pmpro-downloads' ); ?> <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Log in here', 'pmpro-downloads' ); ?></a>
					</div>
				<?php endif;
			?>
		</div>
	</div>
<?php endif; ?>
