<?php

namespace jri\models;

/**
 * Class RwdImage
 *
 * Used to generate resposive HTML for post attachment
 *
 * @package jri\objects
 */
class RwdImage {

	/**
	 * Image attachment to be displayed
	 *
	 * @var \WP_Post|null
	 */
	public $attachment = null;

	/**
	 * RwdSet required to display the image
	 *
	 * @var RwdSet
	 */
	public $rwd_set;

	/**
	 * Post attachments which should replace some main attachment resolution in rwd set.
	 *
	 * @var \WP_Post[]
	 */
	public $rwd_rewrite = array();

	/**
	 * Generated sources for each rwd set option
	 *
	 * @var array()
	 */
	public $sources;

	/**
	 * Cache for metadata images
	 *
	 * @var array()
	 */
	protected static $meta_datas;

	/**
	 * Cache for image base urls
	 *
	 * @var array()
	 */
	protected static $base_urls;

	/**
	 * Warnings to be printed in comments before image
	 *
	 * @var array()
	 */
	protected $warnings;

	/**
	 * End line character
	 *
	 * @var string
	 */
	protected $eol = "\n";


	/**
	 * RwdImage constructor.
	 *
	 * @param \WP_Post|int|null $attachment Image attachment to be displayed.
	 */
	public function __construct( $attachment ) {
		$this->attachment = $this->load_attachment( $attachment );
	}

	/**
	 * Generate <picture> tag for the current attachment with specified size
	 *
	 * @param string|array $size Required image size.
	 * @param array        $attributes  Additional html attributes to be used for main tag.
	 *
	 * @return string
	 */
	public function picture( $size, $attributes = array() ) {
		if ( ! $this->attachment ) {
			return '';
		}

		$html = '';
		if ( $this->set_sizes( $size ) && $sources = $this->get_set_sources() ) {
			// prepare image attributes (class, alt, title etc).
			$attr = array(
				'class' => "attachment-{$this->rwd_set->key} size-{$this->rwd_set->key} wp-post-picture",
				'alt'   => trim( strip_tags( get_post_meta( $this->attachment->ID, '_wp_attachment_image_alt', true ) ) ),
				'src'   => '', // it's not used, but included for compatibility with other plugins.
			);
			if ( ! empty( $attributes['class'] ) ) {
				$attributes['class'] = $attr['class'] . ' ' . $attributes['class'];
			}
			$attr = array_merge( $attr, $attributes );
			$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $this->attachment, $this->rwd_set->key );
			if ( isset( $attr['src'] ) ) { // remove compatibility key, which is not used actually.
				unset( $attr['src'] );
			}
			$attr = array_map( 'esc_attr', $attr );

			// default template (if we have only 1 size).
			$default_template = '<img srcset="{src}" alt="{alt}">';

			// generation of responsive sizes.
			$html = '<picture';
			foreach ( $attr as $name => $value ) {
				if ( 'alt' !== $name ) {
					$html .= " $name=" . '"' . $value . '"';
				}
			}
			$html .= '>' . $this->eol;
			foreach ( $this->rwd_set->options as $subkey => $option ) {
				if ( ! isset( $sources[ $subkey ] ) || is_null( $option->picture ) ) {
					continue;
				}
				//get retina images
				$retina_src = ', ';
				$meta_data = $this->get_attachment_metadata( $sources[ $subkey ]['attachment_id'] );
				if( $option->retina ) {
					foreach( $option->retina as $retina_key => $retina_value ) {
						if( $meta_data['sizes'][ $option->key . '_' . $retina_key ] ) {
							$retina_src .= esc_attr( $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] ) .
								$meta_data['sizes'][ $option->key . '_' . $retina_key ]['file'] . ' ' . $retina_key . ', ');
						}
					}
				}
				$template = $option->picture ? $option->picture : $default_template;
				$tokens   = array(
					'{src}'   => esc_attr( $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] ) . $sources[ $subkey ]['file'] ) .
						rtrim( $retina_src, ', ' ),
					'{alt}'   => $attr['alt'],
					'{w}'     => $meta_data['sizes'][ $option->key ]['width'],
				);

				$html .= strtr( $template, $tokens ) . $this->eol;
			}
			$html .= '</picture>';
		}

		$html = $this->get_warnings_comment() . $html;

		return $html;
	}

	/**
	 * Generate <img> tag for the current attachment with specified size
	 *
	 * @param string|array $size Required image size.
	 * @param array        $attributes  Additional html attributes to be used for main tag.
	 *
	 * @return string
	 */
	public function img( $size, $attributes = array() ) {
		if ( ! $this->attachment ) {
			return '';
		}

		$html = '';
		if ( $this->set_sizes( $size ) && $sources = $this->get_set_sources() ) {
			// prepare image attributes (class, alt, title etc).
			$attr = array(
				'class' => "attachment-{$this->rwd_set->key} size-{$this->rwd_set->key} wp-post-image",
				'alt'   => trim( strip_tags( get_post_meta( $this->attachment->ID, '_wp_attachment_image_alt', true ) ) ),
			);
			if ( ! empty( $attributes['class'] ) ) {
				$attributes['class'] = $attr['class'] . ' ' . $attributes['class'];
			}
			$attr = array_merge( $attr, $attributes );

			$src = '';
			$srcset = array();
			$sizes = array();

			// generation of responsive sizes.
			foreach ( $this->rwd_set->options as $subkey => $option ) {
				if ( ! isset( $sources[ $subkey ] ) || is_null( $option->srcset ) ) {
					continue;
				}

				$meta_data = $this->get_attachment_metadata( $sources[ $subkey ]['attachment_id'] );

				$tokens    = array(
					'{src}'   => esc_attr( $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] ) . $sources[ $subkey ]['file'] ),
					'{w}'     => $meta_data['sizes'][ $option->key ]['width'],
				);

				$src = $tokens['{src}'];
				$srcset[] = strtr( "{src} $option->srcset", $tokens );
				$sizes[] = strtr( $option->sizes, $tokens );
			}

			$attr['src'] = $src;
			$attr['srcset'] = implode( ', ', $srcset );
			$attr['sizes'] = implode( ', ', $sizes );

			// the part taken from WP core.
			$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $this->attachment, $this->rwd_set->key );
			$attr = array_map( 'esc_attr', $attr );
			$html = '<img';
			foreach ( $attr as $name => $value ) {
				$html .= " $name=" . '"' . $value . '"';
			}
			$html .= '>';
		}

		$html = $this->get_warnings_comment() . $html;

		return $html;
	}

	/**
	 * Generate background media queries
	 *
	 * @param string       $selector CSS selector.
	 * @param string|array $size Required image size.
	 *
	 * @return string Generated html comments warnings.
	 */
	public function background( $selector, $size ) {
		if ( ! $this->attachment ) {
			return;
		}

		if ( $this->set_sizes( $size ) && $sources = $this->get_set_sources() ) {
			global $rwd_background_styles;

			// generation of responsive sizes.
			foreach ( $this->rwd_set->options as $subkey => $option ) {
				if ( ! isset( $sources[ $subkey ] ) || is_null( $option->bg ) ) {
					continue;
				}

				$meta_data = $this->get_attachment_metadata( $sources[ $subkey ]['attachment_id'] );

				$src = $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] ) . $sources[ $subkey ]['file'];
				$media = str_replace( '{w}', $meta_data['sizes'][ $option->key ]['width'], $option->bg );

				if ( ! isset( $rwd_background_styles[ $media ] ) ) {
					$rwd_background_styles[ $media ] = array();
				}

				$rwd_background_styles[ $media ][ $selector ] = "$selector{background-image:url('$src');}";
			}
		}

		return $this->get_warnings_comment();
	}

	/**
	 * Set rwd_set and rwd_rewrite based on size.
	 *
	 * @param string|array $size Required image size.
	 *
	 * @return bool
	 */
	public function set_sizes( $size ) {
		$rwd_sizes = $this->get_registered_rwd_sizes();
		if ( is_string( $size ) ) {
			$size = array( $size );
		}

		if ( empty( $size[0] ) || ! isset( $rwd_sizes[ $size[0] ] ) ) {
			$this->warnings[] = 'RwdImage::set_size() : Unknown image size "' . esc_html( @$size[0] ) . '"';

			return false;
		} else {
			$this->rwd_set = $rwd_sizes[ $size[0] ];

			if ( 1 < count( $size ) ) {
				unset( $size[0] );
				foreach ( $size as $subkey => $attachment ) {
					if ( $attachment = $this->load_attachment( $attachment ) ) {
						$this->rwd_rewrite[ $subkey ] = $attachment;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Prepare rwd set real file sources to be displayed
	 *
	 * @return array|null
	 */
	public function get_set_sources() {
		if ( empty( $this->rwd_set ) ) {
			return null;
		}

		$sources          = array();
		$attachment_meta  = $this->get_attachment_metadata( $this->attachment->ID );
		$attachment_width = ! empty( $attachment_meta['sizes'][ $this->rwd_set->key ] ) ?
			$attachment_meta['sizes'][ $this->rwd_set->key ]['width'] : $attachment_meta['width'];

		foreach ( $this->rwd_set->options as $subkey => $option ) {
			$attachment = empty( $this->rwd_rewrite[ $subkey ] ) ? $this->attachment : $this->rwd_rewrite[ $subkey ];
			$meta_data  = $this->get_attachment_metadata( $attachment->ID );

			// for lower images we use max image size for the bigger sizes.
			if ( ! isset( $meta_data['sizes'][ $option->key ] ) && $meta_data['width'] <= $option->size->w ) {
				$meta_data['sizes'][ $option->key ] = array(
					'width' => $meta_data['width'],
					'height' => $meta_data['height'],
					'file' => basename( $meta_data['file'] ),
				);
				// save to cache.
				$this->set_attachment_metadata( $attachment->ID, $meta_data );
			}

			// however if we didn't find correct size - we skip this size with warning.
			if ( ! isset( $meta_data['sizes'][ $option->key ] ) ) {
				$this->warnings[] = "Attachment {$attachment->ID}: missing image size \"{$this->rwd_set->key}:{$subkey}\"";
				continue;
			}

			// check that image size width is lower than size width.
			if ( $attachment_width < $meta_data['sizes'][ $option->key ]['width'] ) {
				continue;
			}

			$sources[ $subkey ]                  = $meta_data['sizes'][ $option->key ];
			$sources[ $subkey ]['attachment_id'] = $attachment->ID;
		}

		return $sources;
	}

	/**
	 * Validate $attachment argument, find media post in DB and return it.
	 *
	 * @param \WP_Post|int|null $attachment Attachment argument to validate.
	 *
	 * @return \WP_Post|null|
	 */
	protected function load_attachment( $attachment ) {
		if ( empty( $attachment ) ) {
			if ( ! empty( $this->attachment ) ) {
				$attachment = $this->attachment;
			} else {
				$attachment = get_post_thumbnail_id( get_the_ID() );
			}
		}
		if ( is_numeric( $attachment ) && $attachment = get_post( $attachment ) ) {
			// check that ID passed is really an attachment.
			if ( 'attachment' !== $attachment->post_type ) {
				$attachment = null;
			}
		}
		if ( is_a( $attachment, '\WP_Post' ) ) {
			return $attachment;
		}

		return null;
	}

	/**
	 * Generate HTML comments for warnings
	 *
	 * @return string
	 */
	protected function get_warnings_comment() {
		if ( ! empty( $this->warnings ) ) {
			return '<!-- ' . implode( "-->{$this->eol}<!--", $this->warnings ) . '-->' . $this->eol;
		} else {
			return '';
		}
	}

	/**
	 * Cache for wp_get_attachment_metadata function.
	 *
	 * @param int $attachment_id Attachment post to get it's metadata.
	 *
	 * @return mixed
	 */
	protected function get_attachment_metadata( $attachment_id ) {
		if ( ! isset( static::$meta_datas[ $attachment_id ] ) ) {
			static::$meta_datas[ $attachment_id ] = wp_get_attachment_metadata( $attachment_id );
		}

		return static::$meta_datas[ $attachment_id ];
	}

	/**
	 * Set updated values to cache
	 *
	 * @param int   $attachment_id Attachment post to update it's metadata cache.
	 * @param array $meta_data  New meta data values.
	 */
	protected function set_attachment_metadata( $attachment_id, $meta_data ) {
		static::$meta_datas[ $attachment_id ] = $meta_data;
	}

	/**
	 * Cache for attachment baseurl generation
	 *
	 * @param int $attachment_id Attachment ID to find out baseurl to.
	 *
	 * @return mixed
	 */
	protected function get_attachment_baseurl( $attachment_id ) {
		if ( ! isset( static::$base_urls[ $attachment_id ] ) ) {
			$image_meta = $this->get_attachment_metadata( $attachment_id );

			$dirname = _wp_get_attachment_relative_path( $image_meta['file'] );

			if ( $dirname ) {
				$dirname = trailingslashit( $dirname );
			}

			$upload_dir    = wp_get_upload_dir();
			$image_baseurl = trailingslashit( $upload_dir['baseurl'] ) . $dirname;

			if ( is_ssl() && 'https' !== substr( $image_baseurl, 0, 5 ) && parse_url( $image_baseurl, PHP_URL_HOST ) === $_SERVER['HTTP_HOST'] ) {
				$image_baseurl = set_url_scheme( $image_baseurl, 'https' );
			}

			static::$base_urls[ $attachment_id ] = $image_baseurl;
		}

		return static::$base_urls[ $attachment_id ];
	}

	/**
	 * Alias for global variable to simlify code.
	 *
	 * @return mixed
	 */
	protected function get_registered_rwd_sizes() {
		global $rwd_image_sizes;

		return $rwd_image_sizes;
	}
}