<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if the current admin screen is any pmpro_download screen.
 *
 * @since 1.0
 *
 * @return bool
 */
function pmpro_downloads_is_download_screen() {
	$screen = get_current_screen();
	return $screen && 'pmpro_download' === $screen->post_type;
}

/**
 * Check if the current admin screen is the Downloads list table.
 *
 * Returns false on the single post edit/new screen so we don't
 * interfere with the block editor's own header and layout.
 *
 * @since 1.0
 *
 * @return bool
 */
function pmpro_downloads_is_download_list_screen() {
	$screen = get_current_screen();
	return $screen && 'pmpro_download' === $screen->post_type && 'edit' === $screen->base;
}

/**
 * Output the PMPro admin header on Downloads list table admin screens.
 *
 * @since 1.0
 */
function pmpro_downloads_admin_header() {
	if ( ! pmpro_downloads_is_download_list_screen() ) {
		return;
	}

	if ( ! function_exists( 'pmpro_admin_header' ) ) {
		return;
	}

	// Temporarily set $_GET['page'] so pmpro_admin_header() passes its own check.
	$original_page = isset( $_GET['page'] ) ? $_GET['page'] : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Read-only; the original value is saved and restored verbatim after pmpro_admin_header().
	$_GET['page']  = 'pmpro-downloads';

	pmpro_admin_header();

	if ( null === $original_page ) {
		unset( $_GET['page'] );
	} else {
		$_GET['page'] = $original_page;
	}
}
add_action( 'admin_notices', 'pmpro_downloads_admin_header', 1 );

/**
 * Enqueue admin styles on Downloads screens.
 *
 * Loads PMPro's admin CSS (for the banner and shared admin components)
 * plus the plugin's own admin stylesheet.
 *
 * @since 1.0
 */
function pmpro_downloads_enqueue_admin_styles() {
	if ( ! pmpro_downloads_is_download_list_screen() ) {
		return;
	}

	// Ensure PMPro's admin CSS is loaded so the banner is styled correctly.
	// The pmpro_admin handle is registered by PMPro on admin_enqueue_scripts.
	if ( wp_style_is( 'pmpro_admin', 'registered' ) ) {
		wp_enqueue_style( 'pmpro_admin' );
	}

	wp_enqueue_style(
		'pmpro-downloads-admin',
		plugins_url( 'css/pmpro-downloads-admin.css', __DIR__ ),
		array(),
		PMPRO_DOWNLOADS_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'pmpro_downloads_enqueue_admin_styles' );

/**
 * Add the pmpro_admin, pmpro_events_admin body class to Downloads admin screens.
 *
 * @since 1.0
 *
 * @param string $classes Space-separated list of body classes.
 * @return string
 */
function pmpro_downloads_admin_body_class( $classes ) {
	if ( pmpro_downloads_is_download_list_screen() ) {
		$classes .= ' pmpro_admin pmpro_events_admin';
	}
	return $classes;
}
add_filter( 'admin_body_class', 'pmpro_downloads_admin_body_class' );

/**
 * Add Membership and Shortcode columns to the Downloads list table.
 *
 * @since 1.0
 *
 * @param array $columns Existing column definitions.
 * @return array
 */
function pmpro_downloads_list_table_columns( $columns ) {
	// Insert after the title column.
	$new_columns = array();
	foreach ( $columns as $key => $label ) {
		$new_columns[ $key ] = $label;
		if ( 'title' === $key ) {
			$new_columns['pmpro_membership'] = __( 'Membership', 'pmpro-downloads' );
			$new_columns['pmpro_shortcode']  = __( 'Shortcode', 'pmpro-downloads' );
		}
	}
	return $new_columns;
}
add_filter( 'manage_pmpro_download_posts_columns', 'pmpro_downloads_list_table_columns' );

/**
 * Render content for the Membership and Shortcode columns.
 *
 * @since 1.0
 *
 * @param string $column_name Column identifier.
 * @param int    $post_id     Current post ID.
 */
function pmpro_downloads_list_table_column_content( $column_name, $post_id ) {
	if ( 'pmpro_membership' === $column_name ) {
		if ( ! function_exists( 'pmpro_has_membership_access' ) ) {
			echo '&mdash;';
			return;
		}

		$access = pmpro_has_membership_access( $post_id, null, true );
		if ( ! is_array( $access ) || empty( $access[2] ) ) {
			// No levels assigned — not restricted.
			echo '&mdash;';
			return;
		}

		echo esc_html( implode( ', ', $access[2] ) );
	}

	if ( 'pmpro_shortcode' === $column_name ) {
		printf(
			'<code>[pmpro_download id=&quot;%d&quot;]</code>',
			absint( $post_id )
		);
	}
}
add_action( 'manage_pmpro_download_posts_custom_column', 'pmpro_downloads_list_table_column_content', 10, 2 );

/**
 * Change the title placeholder text for download posts.
 *
 * @since 1.0
 *
 * @param string  $title The placeholder text.
 * @param WP_Post $post  The current post.
 * @return string
 */
function pmpro_downloads_title_placeholder( $title, $post ) {
	if ( 'pmpro_download' === $post->post_type ) {
		return __( 'Download Name', 'pmpro-downloads' );
	}
	return $title;
}
add_filter( 'enter_title_here', 'pmpro_downloads_title_placeholder', 10, 2 );
