<?php
/**
 * Template functions to simplify using <picture> tags and dynamic mobile-friendly backgrounds.
 */

use jri\models\RwdImage;

/**
 * Displays <picture> or <img> for post attachment image specifying correct responsive rules,
 * which should be set through 'rwd_image_sizes' filter hook.
 *
 * @param WP_Post|int|null $attachment WordPress attachment object, ID or null. If null passed will take featured image of current post.
 * @param string|array     $size {
 *              Single image size name OR array
 *
 *              @type int 0 =>  $size (first element should be the name of image size),
 *              @type string            $subsize => $attachment_id  ($attachment to be used to rewrite image in another resolution)
 *        }
 * Image size name or array with custom attachment IDs for specific rwd sizes.
 *
 * @param string           $tag Specify which tag should be used: picture|img.
 * @param array            $attr  Additional html attributes to be used for main tag.
 */
function rwd_attachment_image( $attachment = null, $size = 'thumbnail', $tag = null, $attr = array() ) {
	if( empty( $tag ) ) {
		$tag = apply_filters( 'rwd_tag_type', 'picture' );
	}
	echo get_rwd_attachment_image( $attachment, $size, $tag, $attr );
}

/**
 * Generate <picture> or <img> html for post attachment image specifying correct responsive rules,
 * which should be set through 'rwd_image_sizes' filter hook.
 *
 * @param WP_Post|int|null $attachment WordPress attachment object, ID or null. If null passed will take featured image of current post.
 * @param string|array     $size {
 *              Single image size name OR array
 *
 *              @type int 0 =>  $size (first element should be the name of image size),
 *              @type string            $subsize => $attachment_id  ($attachment to be used to rewrite image in another resolution)
 *        }
 * @param string           $tag Specify which tag should be used: picture|img.
 * @param array            $attr  Additional html attributes to be used for main tag.
 *
 * @return string Generated html.
 */
function get_rwd_attachment_image( $attachment = null, $size = 'thumbnail', $tag = null, $attr = array() ) {
	$rwd_image = new RwdImage( $attachment );

	if( empty( $tag ) ) {
		$tag = apply_filters( 'rwd_tag_type', 'picture' );
	}

	$size = apply_filters( 'post_thumbnail_size', $size );

	if ( 'img' != $tag ) {
		$html = $rwd_image->picture( $size, $attr );
	} else {
		$html = $rwd_image->img( $size, $attr );
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
 * @param string|array     $size {
 *              Single image size name OR array
 *
 *              @type int 0 =>  $size (first element should be the name of image size),
 *              @type string            $subsize => $attachment_id  ($attachment to be used to rewrite image in another resolution)
 *        }
 */
function rwd_attachment_background( $selector, $attachment = null, $size = 'thumbnail' ) {
	$rwd_image = new RwdImage( $attachment );
	echo $rwd_image->background( $selector, $size );
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

	$primary_media = RwdImage::get_background_primary_sizes();
	// merge with media keys to get correct sorting.
	$ordered_styles = array_merge( array_flip( $primary_media ), $rwd_background_styles );
	foreach ( $ordered_styles as $media => $selectors ) {
		if ( empty( $selectors ) || ! is_array( $selectors ) ) {
			continue;
		}

		$media_css = implode( ' ', $selectors );
		if ( '' === $media ) {
			$styles .= " $media_css ";
		} else {
			$styles .= " $media{ $media_css } ";
		}
	}

	print "<style type=\"text/css\">/* rwd-background-styles */ {$styles}</style> \n";
	$rwd_background_styles = array();
}