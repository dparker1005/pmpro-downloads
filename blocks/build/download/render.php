<?php
/**
 * Render the PMPro Download block on the frontend.
 *
 * @since 1.0
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo pmpro_downloads_shortcode( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
