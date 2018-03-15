<?php

namespace jri\components;

use jri\core\Controller;

/**
 * Class MediaMetaInfo
 * Add information about available image sizes on Media edit screen.
 */
class MediaMetaInfo extends Controller {
	/**
	 * MediaMetaInfo constructor.
	 */
	public function __construct() {
		// Terminate if not admin.
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'add_meta_boxes_attachment', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Register metabox for media type image.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function add_meta_boxes( $post ) {
		// Add metabox only if this is an image.
		if ( strpos( $post->post_mime_type, 'image/' ) !== 0 ) {
			return;
		}
		add_meta_box( 'jri-attachement-meta-info', __( 'Image sizes' ), array( $this, 'render_meta_info' ) );
	}

	/**
	 * Get meta information and renders it.
	 *
	 * @param \WP_Post $post Post object.
	 */
	public function render_meta_info( $post ) {
		$meta = wp_get_attachment_metadata( $post->ID );
		
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$meta['gcd']     = jri_greatest_commod_divisor( $meta['width'], $meta['height'] );
			$meta['x_ratio'] = (int) $meta['width'] / $meta['gcd'];
			$meta['y_ratio'] = (int) $meta['height'] / $meta['gcd'];
			if ( 20 < $meta['x_ratio'] || 20 < $meta['y_ratio'] ) {
				$meta['x_ratio'] = round( $meta['x_ratio'] / 10 );
				$meta['y_ratio'] = round( $meta['y_ratio'] / 10 );
				$meta['avr_ratio'] = true;
			}
		}

		$this->_render( 'media/meta-box', array(
				'meta' => $meta,
			)
		);
	}
}
