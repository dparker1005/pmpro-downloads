<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode to display a download using a template.
 *
 * Usage: [pmpro_download id="123" template="card" label="title"]
 *
 * @since 1.0
 *
 * @param array $atts Shortcode attributes.
 * @return string Shortcode output.
 */
function pmpro_downloads_shortcode( $atts ) {
	// Bail if PMPro is not active.
	if ( ! function_exists( 'pmpro_has_membership_access' ) ) {
		return '';
	}

	$atts = shortcode_atts( array(
		'id'          => 0,
		'template'    => 'link',
		'label'       => 'title',
		'embed_image' => false,
	), $atts, 'pmpro_download' );

	$post_id = intval( $atts['id'] );
	if ( empty( $post_id ) ) {
		return '';
	}

	// Get the download post.
	$download = get_post( $post_id );
	if ( empty( $download ) || 'pmpro_download' !== $download->post_type ) {
		return '';
	}

	// Validate template.
	$allowed_templates = array( 'link', 'card', 'button' );
	$template          = in_array( $atts['template'], $allowed_templates, true ) ? $atts['template'] : 'link';

	// Validate label.
	$allowed_labels = array( 'title', 'filename' );
	$label          = in_array( $atts['label'], $allowed_labels, true ) ? $atts['label'] : 'title';

	// Check if the user has membership access.
	$hasaccess = pmpro_has_membership_access( $post_id, null, true );
	if ( is_array( $hasaccess ) ) {
		$level_ids   = $hasaccess[1];
		$level_names = $hasaccess[2];
		$hasaccess   = $hasaccess[0];
	} else {
		$level_ids   = array();
		$level_names = array();
	}

	// Hide levels that don't allow signups from locked templates,
	// matching core's no access message behavior.
	list( $level_ids, $level_names ) = pmpro_downloads_get_displayable_levels( $level_ids, $level_names );

	// Gather file metadata.
	$uploaded_filename = get_post_meta( $post_id, '_pmpro_download_uploaded_filename', true );
	$stored_filename   = get_post_meta( $post_id, '_pmpro_download_stored_filename', true );
	$file_type         = get_post_meta( $post_id, '_pmpro_download_file_type', true );
	$file_size         = get_post_meta( $post_id, '_pmpro_download_file_size', true );
	$file_extension    = pmpro_downloads_get_file_extension( ! empty( $stored_filename ) ? $stored_filename : $uploaded_filename );
	$download_url      = $hasaccess ? pmpro_downloads_get_download_url( $post_id ) : '';
	$no_access_url     = ! $hasaccess ? pmpro_downloads_get_no_access_url( $level_ids ) : '';

	if ( empty( $uploaded_filename ) ) {
		$uploaded_filename = $stored_filename;
	}

	// If user has access but no file is available, return empty.
	if ( $hasaccess && ( empty( $stored_filename ) || empty( $download_url ) ) ) {
		return '';
	}

	// Fall back post title to filename if empty.
	$post_title = ! empty( $download->post_title ) ? $download->post_title : $uploaded_filename;

	// Display name based on label attribute.
	$display_name = ( 'filename' === $label ) ? $uploaded_filename : $post_title;

	// Get formatted description from post meta.
	$raw_description = get_post_meta( $post_id, '_pmpro_download_description', true );
	$description     = ! empty( $raw_description ) ? wpautop( wp_kses_post( $raw_description ) ) : '';

	// Build template variables.
	$template_vars = array(
		'post_id'           => $post_id,
		'post_title'        => $post_title,
		'display_name'      => $display_name,
		'filename'          => $uploaded_filename,
		'uploaded_filename' => $uploaded_filename,
		'stored_filename'   => $stored_filename,
		'file_type'         => $file_type,
		'file_size'         => $file_size,
		'file_extension'    => $file_extension,
		'download_url'      => $download_url,
		'no_access_url'     => $no_access_url,
		'has_access'        => $hasaccess,
		'level_ids'         => $level_ids,
		'level_names'       => $level_names,
		'description'       => $description,
		'embed_image'       => filter_var( $atts['embed_image'], FILTER_VALIDATE_BOOLEAN ),
	);

	return pmpro_downloads_render_template( $template, $template_vars );
}
add_shortcode( 'pmpro_download', 'pmpro_downloads_shortcode' );

/**
 * Replace only [pmpro_download] shortcodes in a string of content.
 *
 * Uses core's shortcode regex restricted to the pmpro_download tag so that
 * other shortcodes in the content are left untouched and the [[escaped]]
 * syntax is still respected. The callback receives match groups in the
 * get_shortcode_regex() format (tag in $matches[2], attributes in $matches[3]).
 *
 * @since TBD
 *
 * @param string   $content  Content to search for shortcodes.
 * @param callable $callback Callback passed to preg_replace_callback().
 * @return string Content with [pmpro_download] shortcodes replaced.
 */
function pmpro_downloads_replace_shortcodes( $content, $callback ) {
	// Bail early if there is no shortcode in the content.
	if ( ! is_string( $content ) || false === strpos( $content, '[pmpro_download' ) ) {
		return $content;
	}

	$pattern = get_shortcode_regex( array( 'pmpro_download' ) );
	return preg_replace_callback( "/$pattern/", $callback, $content );
}

/**
 * Render [pmpro_download] shortcodes in a level confirmation message.
 *
 * Shortcode output is not re-parsed for nested shortcodes, so a download
 * shortcode placed in a level confirmation message renders as raw text when
 * the confirmation page uses the [pmpro_confirmation] shortcode. Rendering
 * here covers both the block and shortcode confirmation pages.
 *
 * Note: core echoes this message through wp_kses_post(), which strips the
 * SVG icons from our templates, so downloads render here without icons.
 *
 * @since TBD
 *
 * @param string $message The confirmation message.
 * @return string The confirmation message with download shortcodes rendered.
 */
function pmpro_downloads_confirmation_message( $message ) {
	return pmpro_downloads_replace_shortcodes( $message, 'do_shortcode_tag' );
}
add_filter( 'pmpro_confirmation_message', 'pmpro_downloads_confirmation_message' );
