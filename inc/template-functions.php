<?php
/**
 * Template functions to simplify using <picture> tags and dynamic mobile-firendly backgrounds.
 */

use jri\objects\RwdImage;

/**
 * Displays <picture> or <img> for post attachment image specifying correct responsive rules,
 * which should be set through 'rwd_image_sizes' filter hook.
 *
 * @param WP_Post|int|null $attachment WordPress attachment object, ID or null. If null passed will take featured image of current post.
 * @param string|array     $size Image size name or array with custom attachment IDs for specific rwd sizes.
 * @param string           $tag Specify which tag should be used: picture|img.
 */
function rwd_attachment_image( $attachment = null, $size = 'thumbnail', $tag = 'picture' ) {
	echo get_rwd_attachment_image( $attachment, $size, $tag );
}

/**
 * Generate <picture> or <img> html for post attachment image specifying correct responsive rules,
 * which should be set through 'rwd_image_sizes' filter hook.
 *
 * @param WP_Post|int|null $attachment WordPress attachment object, ID or null. If null passed will take featured image of current post.
 * @param string|array     $size Image size name or array with custom attachment IDs for specific rwd sizes.
 * @param string           $tag Specify which tag should be used: picture|img.
 *
 * @return string Generated html.
 */
function get_rwd_attachment_image( $attachment = null, $size = 'thumbnail', $tag = 'picture' ) {
	$rwd_image = new RwdImage( $attachment );

	$size = apply_filters( 'post_thumbnail_size', $size );
	if ( 'img' != $tag  ) {
		$html = $rwd_image->picture( $size );
	} else {
		$html = $rwd_image->img( $size );
	}

	return $html;
}

/**
 * Generate css media styles for background image for specific css selector and specific rwd sizes,
 * which should be set through 'rwd_image_sizes' filter hook.
 *
 * Generated styles are add to cache and then print them in wp_foot or by calling rwd_print_css();
 *
 * @param string           $selector Dynamic css selector
 * @param WP_Post|int|null $attachment WordPress attachment object, ID or null. If null passed will take featured image of current post.
 * @param string|array     $size Image size name or array with custom attachment IDs for specific rwd sizes.
 */
function rwd_attachment_background( $selector, $attachment = null, $size = 'thumbnail' ) {
	$rwd_image = new RwdImage( $attachment );
	$rwd_image->background( $selector, $size );
}

/**
 * Print styles from global cache
 */
function rwd_print_styles() {
	global $rwd_background_styles;

	if ( empty( $rwd_background_styles ) || ! is_array( $rwd_background_styles ) ) {
		return;
	}

	$styles = '';
	foreach ( $rwd_background_styles as $media => $selectors ) {
		$media_css = implode( ' ', $selectors );
		if ( '' === $media ) {
			$styles .= " $media_css ";
		} else {
			$styles .= " $media{ $media_css } ";
		}
	}
	print '<style type="text/css">/* rwd-background-styles */' . $styles . '</style>';
	$rwd_background_styles = array();
}