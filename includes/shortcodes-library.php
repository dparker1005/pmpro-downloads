<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode to display a library of downloads.
 *
 * Usage: [pmpro_download_library template="card" layout="grid" columns="2" category="slug-1,slug-2"]
 *
 * @since 1.0
 *
 * @param array $atts Shortcode attributes.
 * @return string Shortcode output.
 */
function pmpro_download_library_shortcode( $atts ) {
	// Bail if PMPro is not active.
	if ( ! function_exists( 'pmpro_has_membership_access' ) ) {
		return '';
	}

	$atts = shortcode_atts( array(
		'template' => 'link',
		'layout'   => 'list',
		'columns'  => 2,
		'label'    => 'title',
		'limit'    => -1,
		'orderby'  => 'title',
		'order'    => 'asc',
		'category' => '',
	), $atts, 'pmpro_download_library' );

	// Validate attributes.
	$allowed_templates = array( 'link', 'card', 'button' );
	$template          = in_array( $atts['template'], $allowed_templates, true ) ? $atts['template'] : 'link';

	$allowed_layouts = array( 'list', 'grid' );
	$layout          = in_array( $atts['layout'], $allowed_layouts, true ) ? $atts['layout'] : 'list';

	$columns = in_array( intval( $atts['columns'] ), array( 2, 3 ), true ) ? intval( $atts['columns'] ) : 2;

	$allowed_labels = array( 'title', 'filename' );
	$label          = in_array( $atts['label'], $allowed_labels, true ) ? $atts['label'] : 'title';

	$limit = intval( $atts['limit'] );

	$allowed_orderby = array( 'title', 'date' );
	$orderby         = in_array( $atts['orderby'], $allowed_orderby, true ) ? $atts['orderby'] : 'title';

	$allowed_order = array( 'asc', 'desc' );
	$order         = in_array( strtolower( $atts['order'] ), $allowed_order, true ) ? strtoupper( $atts['order'] ) : 'ASC';

	// Query downloads. Uses WP_Query so that PMPro's pmpro_search_filter
	// can exclude restricted downloads when "Filter searches and archives" is enabled.
	$query_args = array(
		'post_type'      => 'pmpro_download',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'orderby'        => $orderby,
		'order'          => $order,
	);

	// Filter by download category. Accepts a comma-separated list of term slugs or IDs.
	if ( ! empty( $atts['category'] ) ) {
		$term_ids   = array();
		$term_slugs = array();
		foreach ( array_filter( array_map( 'trim', explode( ',', $atts['category'] ) ) ) as $term ) {
			if ( is_numeric( $term ) ) {
				$term_ids[] = intval( $term );
			} else {
				$term_slugs[] = sanitize_title( $term );
			}
		}

		$tax_query = array( 'relation' => 'OR' );
		if ( ! empty( $term_ids ) ) {
			$tax_query[] = array(
				'taxonomy' => 'pmpro_download_category',
				'field'    => 'term_id',
				'terms'    => $term_ids,
			);
		}
		if ( ! empty( $term_slugs ) ) {
			$tax_query[] = array(
				'taxonomy' => 'pmpro_download_category',
				'field'    => 'slug',
				'terms'    => $term_slugs,
			);
		}

		if ( count( $tax_query ) > 1 ) {
			$query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}
	}

	$query = new WP_Query( $query_args );

	$downloads = $query->posts;

	if ( empty( $downloads ) ) {
		return '';
	}

	// Render each download using the single download shortcode.
	$items = '';
	foreach ( $downloads as $download ) {
		$items .= pmpro_downloads_shortcode( array(
			'id'       => $download->ID,
			'template' => $template,
			'label'    => $label,
		) );
	}

	if ( empty( $items ) ) {
		return '';
	}

	// Build container CSS classes.
	$classes = array(
		'pmpro_download_library',
		'pmpro_download_library-' . $layout,
	);

	if ( 'grid' === $layout ) {
		$classes[] = 'pmpro_download_library-columns-' . $columns;
	}

	return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $items . '</div>';
}
add_shortcode( 'pmpro_download_library', 'pmpro_download_library_shortcode' );
