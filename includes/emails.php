<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace [pmpro_download] shortcodes in email bodies with plain download links.
 *
 * Shortcodes are not processed in emails, so this action swaps each
 * [pmpro_download id="X"] occurrence with an HTML link to the file.
 *
 * Hooked to pmpro_before_email_sent so that it runs after the Liquid
 * renderer has substituted {{ membership_level_confirmation_message }}
 * into the body.
 *
 * @since 1.1
 *
 * @param PMProEmail $email The email object.
 */
function pmpro_downloads_swap_shortcodes_in_email( $email ) {
	// Bail early if there is no shortcode in the body.
	if ( false === strpos( $email->body, '[pmpro_download' ) ) {
		return;
	}

	// Match [pmpro_download ...] shortcodes (self-closing only).
	$pattern = '/\[pmpro_download\s+([^\]]*)\]/';

	$email->body = preg_replace_callback( $pattern, 'pmpro_downloads_replace_shortcode_with_link', $email->body );
}
add_action( 'pmpro_before_email_sent', 'pmpro_downloads_swap_shortcodes_in_email' );

/**
 * Callback to replace a single [pmpro_download] shortcode match with a download link.
 *
 * If the shortcode is invalid (missing ID, ID does not point to a published
 * pmpro_download post, or the download has no file), the original shortcode text is returned
 * unchanged so the admin who configured the email can see the typo and fix it. Recipients
 * also see this text, including when a download is unpublished after the email is set up.
 *
 * @since 1.1
 *
 * @param array $matches Regex matches from preg_replace_callback.
 * @return string HTML link, or the original shortcode if the download is invalid.
 */
function pmpro_downloads_replace_shortcode_with_link( $matches ) {
	// Parse the shortcode attributes from the matched string.
	$atts = shortcode_parse_atts( $matches[1] );

	$post_id = isset( $atts['id'] ) ? intval( $atts['id'] ) : 0;
	if ( empty( $post_id ) ) {
		return $matches[0];
	}

	// Verify this is a valid, published download post.
	$download = get_post( $post_id );
	if ( empty( $download ) || 'pmpro_download' !== $download->post_type || 'publish' !== get_post_status( $download ) ) {
		return $matches[0];
	}

	// Build the download URL.
	$download_url = pmpro_downloads_get_download_url( $post_id );
	if ( empty( $download_url ) ) {
		return $matches[0];
	}

	// Determine the display name.
	$label = isset( $atts['label'] ) ? $atts['label'] : 'title';
	if ( 'filename' === $label ) {
		$display_name = get_post_meta( $post_id, '_pmpro_download_uploaded_filename', true );
	}

	if ( empty( $display_name ) ) {
		$display_name = ! empty( $download->post_title ) ? $download->post_title : get_post_meta( $post_id, '_pmpro_download_uploaded_filename', true );
	}

	if ( empty( $display_name ) ) {
		$display_name = __( 'Download', 'pmpro-downloads' );
	}

	// Link to the login page with the download URL as the redirect destination.
	$login_url = pmpro_login_url( $download_url );

	return '<a href="' . esc_url( $login_url ) . '">' . esc_html( $display_name ) . '</a>';
}
