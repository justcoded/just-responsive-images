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
	 * JIO Attachment status in queue
	 *
	 * @var int
	 */
	const STATUS_IN_QUEUE = 1;

	/**
	 * RwdImage constructor.
	 *
	 * @param \WP_Post|int|null $attachment Image attachment to be displayed.
	 */
	public function __construct( $attachment ) {
		defined( 'JRI_DUMMY_IMAGE' ) || define( 'JRI_DUMMY_IMAGE', false );
		$this->attachment = $this->load_attachment( $attachment );
	}

	/**
	 * Verify that attachment mime type is SVG image
	 *
	 * @param \WP_Post $attachment Post-attachment object to be validated.
	 *
	 * @return boolean
	 */
	public function verify_svg_mime_type( $attachment ) {
		if ( false !== strpos( $attachment->post_mime_type, 'image/svg' ) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Generate <picture> tag for the current attachment with specified size
	 *
	 * @param string|array $size Required image size.
	 * @param array        $attributes Additional html attributes to be used for main tag.
	 *
	 * @return string
	 */
	public function picture( $size, $attributes = array() ) {
		if ( ! $this->attachment ) {
			return '';
		}

		/* Check if svg and print it */
		if ( $this->verify_svg_mime_type( $this->attachment ) ) {
			return $this->svg( $size, $attributes );
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

				$meta_data = $this->get_attachment_metadata( $sources[ $subkey ]['attachment_id'] );
				$baseurl   = $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] );

				$src = array(
					$this->get_attachment_url( $baseurl, $sources[ $subkey ] ),
				);
				// get retina sources.
				if ( $option->retina_options ) {
					foreach ( $option->retina_options as $retina_descriptor => $multiplier ) {
						$retina_image_size = ImageSize::get_retina_key( $option->key, $retina_descriptor );
						if ( ! empty( $meta_data['sizes'][ $retina_image_size ] ) ) {
							$src[] = $this->get_attachment_url( $baseurl, $meta_data['sizes'][ $retina_image_size ] )
									. ' ' . $retina_descriptor;
						}
					}
				}

				$tokens = array(
					'{src}'        => esc_attr( implode( ', ', $src ) ),
					'{alt}'        => $attr['alt'],
					'{w}'          => $meta_data['sizes'][ $option->key ]['width'],
					'{single-src}' => reset( $src ),
				);

				$template = $option->picture ? $option->picture : $default_template;
				$html    .= strtr( $template, $tokens ) . $this->eol;
			}
			$html .= '</picture>';
		} // End if().

		$html = $this->get_warnings_comment() . $html;

		return $html;
	}

	/**
	 * Generate <img> tag for the current attachment with specified size
	 *
	 * @param string|array $size Required image size.
	 * @param array        $attributes Additional html attributes to be used for main tag.
	 *
	 * @return string
	 */
	public function img( $size, $attributes = array() ) {
		if ( ! $this->attachment ) {
			return '';
		}

		/* Check if svg and print it */
		if ( $this->verify_svg_mime_type( $this->attachment ) ) {
			return $this->svg( $size, $attributes );
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

			$src    = '';
			$srcset = array();
			$sizes  = array();
			// generation of responsive sizes.
			foreach ( $this->rwd_set->options as $subkey => $option ) {
				if ( ! isset( $sources[ $subkey ] ) || is_null( $option->srcset ) ) {
					continue;
				}

				$baseurl   = $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] );
				$meta_data = $this->get_attachment_metadata( $sources[ $subkey ]['attachment_id'] );

				$tokens = array(
					'{src}' => esc_attr( $this->get_attachment_url( $baseurl, $sources[ $subkey ] ) ),
					'{w}'   => $meta_data['sizes'][ $option->key ]['width'],
				);

				// get retina sources.
				if ( $option->retina_options ) {
					foreach ( $option->retina_options as $retina_descriptor => $multiplier ) {
						$retina_image_size = ImageSize::get_retina_key( $option->key, $retina_descriptor );
						if ( ! empty( $meta_data['sizes'][ $retina_image_size ]['width'] ) ) {
							$retina_width = $meta_data['sizes'][ $retina_image_size ]['width'];
							$srcset[]     = $this->get_attachment_url( $baseurl, $meta_data['sizes'][ $retina_image_size ] )
											. ' ' . $retina_width . 'w';
						}
					}
				}

				$src      = $tokens['{src}'];
				$srcset[] = strtr( "{src} $option->srcset", $tokens );
				if ( $option->sizes ) {
					$sizes[] = strtr( $option->sizes, $tokens );
				}
			}

			$attr['src']    = $src;
			$attr['srcset'] = implode( ', ', $srcset );
			if ( ! empty( $sizes ) ) {
				$attr['sizes'] = implode( ', ', $sizes );
			}

			// the part taken from WP core.
			$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $this->attachment, $this->rwd_set->key );
			$attr = array_map( 'esc_attr', $attr );
			$html = '<img';
			foreach ( $attr as $name => $value ) {
				$html .= " $name=" . '"' . $value . '"';
			}
			$html .= '>';
		} // End if().

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

			// define the strategy: mobile- or desktop- first. Desktop-first will start from empty media query, mobile-first will start with min-width media query.
			$rwd_options = $this->rwd_set->options;
			if ( false !== strpos( reset( $rwd_options )->bg, 'min-width' ) ) {
				$rwd_options = array_reverse( $rwd_options, true );
			}
			// generation of responsive sizes.
			foreach ( $rwd_options as $subkey => $option ) {
				if ( ! isset( $sources[ $subkey ] ) || is_null( $option->bg ) ) {
					continue;
				}
				$baseurl   = $this->get_attachment_baseurl( $sources[ $subkey ]['attachment_id'] );
				$meta_data = $this->get_attachment_metadata( $sources[ $subkey ]['attachment_id'] );

				$src   = $this->get_attachment_url( $baseurl, $sources[ $subkey ] );
				$media = str_replace( '{w}', $meta_data['sizes'][ $option->key ]['width'], $option->bg );

				if ( ! isset( $rwd_background_styles[ $media ] ) ) {
					$rwd_background_styles[ $media ] = array();
				}
				$rwd_background_styles[ $media ][ $selector ] = "$selector{background-image:url('$src');}";

				// get retina sources.
				if ( $option->retina_options ) {
					foreach ( $option->retina_options as $retina_descriptor => $multiplier ) {
						// Check media pixel and media resolution dpi.
						$media_pixel_ration = ( $multiplier < 2.5 ? 1.5 : 2.5 );
						$media_resolution   = ( $multiplier < 2.5 ? '144dpi' : '192dpi' );

						$retina_image_size = ImageSize::get_retina_key( $option->key, $retina_descriptor );

						if ( ! empty( $meta_data['sizes'][ $retina_image_size ] ) ) {
							$src_retina   = $this->get_attachment_url( $baseurl, $meta_data['sizes'][ $retina_image_size ] );
							$media_retina = strtr( $option->bg_retina, array(
								'{dpr}'     => "(-webkit-min-device-pixel-ratio:{$media_pixel_ration})",
								'{min_res}' => "(min-resolution : {$media_resolution})",
							) );
							if ( ! isset( $rwd_background_styles[ $media_retina ] ) ) {
								$rwd_background_styles[ $media_retina ] = array();
							}
							$rwd_background_styles[ $media_retina ][ $selector ] = "$selector{background-image:url('$src_retina');}";
						}
					}
				} // End if().
			} // End foreach().
		} // End if().

		return $this->get_warnings_comment();
	}

	/**
	 * Generate img tag for svg image
	 *
	 * @param string|array $size Required image size.
	 * @param array        $attributes Image attributes.
	 *
	 * @return string Generated html comments warnings.
	 */
	public function svg( $size, $attributes ) {

		// prepare image attributes (class, alt, title etc).
		$attr = array(
			'class' => 'wp-post-image',
			'alt'   => trim( strip_tags( get_post_meta( $this->attachment->ID, '_wp_attachment_image_alt', true ) ) ),
		);

		if ( ! empty( $attributes['class'] ) ) {
			$attributes['class'] = $attr['class'] . ' ' . $attributes['class'];
		}

		$attr        = array_merge( $attr, $attributes );
		$attr['src'] = esc_url( wp_get_attachment_url( $this->attachment->ID ) );
		$attr['alt'] = trim( strip_tags( get_post_meta( $this->attachment->ID, '_wp_attachment_image_alt', true ) ) );

		if ( $this->set_sizes( $size ) ) {
			$attr['width']  = $this->rwd_set->size->w;
			$attr['height'] = $this->rwd_set->size->h;
		}

		// the part taken from WP core.
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $this->attachment, $this->rwd_set->key );
		$attr = array_map( 'esc_attr', $attr );
		$html = '<img';
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= '>';

		$html = $this->get_warnings_comment() . $html;

		return $html;
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
		$sources           = array();
		$attachment_meta   = $this->get_attachment_metadata( $this->attachment->ID );
		$is_attachment_svg = $this->verify_svg_mime_type( $this->attachment );

		// for non-svg define main image size.
		if ( ! $is_attachment_svg ) {
			$attachment_width = ! empty( $attachment_meta['sizes'][ $this->rwd_set->key ] ) ?
				$attachment_meta['sizes'][ $this->rwd_set->key ]['width'] : $attachment_meta['width'];
		}

		$dummy_sizes = array();
		$dummy_meta  = array();
		foreach ( $this->rwd_set->options as $subkey => $option ) {
			$attachment     = empty( $this->rwd_rewrite[ $subkey ] ) ? $this->attachment : $this->rwd_rewrite[ $subkey ];
			$meta_data      = $this->get_attachment_metadata( $attachment->ID );
			$is_subsize_svg = $this->verify_svg_mime_type( $attachment );

			// svg images doesn't have meta data, so we need to generate it.
			if ( $is_subsize_svg ) {
				if ( ! is_array( $meta_data ) ) {
					$meta_data = array();
				}

				$upload_dir        = wp_upload_dir();
				$meta_data['file'] = str_replace(
					$upload_dir['basedir'] . '/',
					'',
					get_attached_file( $attachment->ID, true )
				);

				$meta_data['sizes'][ $option->key ] = array(
					'width'  => $option->size->w,
					'height' => $option->size->h,
					'file'   => basename( $meta_data['file'] ),
				);
				// save to cache.
				$this->set_attachment_metadata( $attachment->ID, $meta_data );
			} else {
				// Resize image if not exists.
				$meta_data = $this->resize_image(
					$attachment->ID,
					$meta_data,
					$option->key,
					$option->size->w,
					$option->size->h,
					$option->size->crop
				);
				if ( JRI_DUMMY_IMAGE && empty( $meta_data['sizes'][ $option->key ]['valid'] ) ) {
					$dummy_sizes[ $option->key ] = $this->dummy_source( $option, false, $attachment->ID, $meta_data );
				}

				// Resize retina images if not exists.
				if ( $option->retina_options ) {
					foreach ( $option->retina_options as $retina_descriptor => $multiplier ) {
						$retina_image_size = ImageSize::get_retina_key( $option->key, $retina_descriptor );
						// generate retina only if original size is bigger in all dimensions than retina size.
						$retina_w = $option->size->w * $multiplier;
						$retina_h = $option->size->h * $multiplier;
						if ( $retina_w <= $meta_data['width'] && $retina_h <= $meta_data['height'] ) {
							$meta_data = $this->resize_image(
								$attachment->ID,
								$meta_data,
								$retina_image_size,
								$retina_w,
								$retina_h,
								$option->size->crop
							);
						}

						if ( JRI_DUMMY_IMAGE && empty( $meta_data['sizes'][ $retina_image_size ]['valid'] ) ) {
							$dummy_sizes[ $retina_image_size ] = $this->dummy_source( $option, $multiplier, $attachment->ID, $meta_data );
						}
					}
				}
			}

			if ( JRI_DUMMY_IMAGE ) {
				if ( ! isset( $dummy_meta[ $attachment->ID ] ) ) {
					$dummy_meta[ $attachment->ID ] = $meta_data;
				}
				$meta_data['sizes'] = array_merge( $dummy_meta[ $attachment->ID ]['sizes'], $dummy_sizes );
				$dummy_meta[ $attachment->ID ] = $meta_data;
			}

			// however if we didn't find correct size - we skip this size with warning.
			if ( ! isset( $meta_data['sizes'][ $option->key ] ) ) {
				$this->warnings[] = "Attachment {$attachment->ID}: missing image size \"{$this->rwd_set->key}:{$subkey}\"";
				continue;
			}

			$sources[ $subkey ]                  = $meta_data['sizes'][ $option->key ];
			$sources[ $subkey ]['attachment_id'] = $attachment->ID;
		} // End foreach().

		// cache all dummy retina sizes to get correct width/height options for retina.
		if ( JRI_DUMMY_IMAGE ) {
			foreach ( $dummy_meta as $attachment_id => $meta_data ) {
				$this->set_attachment_metadata( $attachment_id, $meta_data );
			}
		}

		return $sources;
	}

	/**
	 * Generate generate fake src for empty image sizes
	 *
	 * @param RwdOption $option empty image size options.
	 * @param int|bool  $retina_multiplier retina multiplier for retina size.
	 * @param int       $attachment_id attachment ID to get dummy image.
	 * @param array     $meta_data image attachment WP metadata.
	 *
	 * @return string
	 */
	public function dummy_source( $option, $retina_multiplier, $attachment_id, $meta_data ) {

		$sizename = $option->key;

		$w = $option->size->w;
		$h = $option->size->h;

		$ratio = null;
		if ( ! empty( $meta_data['width'] ) && ! empty( $meta_data['height'] ) ) {
			$ratio = $meta_data['width'] / $meta_data['height'];
		}

		if ( is_null( $h ) || 9999 === $h ) {
			$h = $ratio ? floor( $w / $ratio ) : $w;
		} elseif ( is_null( $w ) || 9999 === $w ) {
			$w = $ratio ? floor( $h * $ratio ) : $h;
		}

		if ( $retina_multiplier ) {
			$w *= $retina_multiplier;
			$h *= $retina_multiplier;
		}

		$color     = substr( md5( "{$meta_data['file']}-$w-$h" ), 0, 6 );
		$dummy_url = "http://via.placeholder.com/{$w}x{$h}/$color?text=%23{$attachment_id}:+{$w}x{$h}";

		return [
			'file'   => $dummy_url,
			'width'  => $w,
			'height' => $h,
		];
	}

	/**
	 * Dynamically resize image.
	 *
	 * @param int    $attach_id Attachment ID.
	 * @param array  $meta_data Attachment meta data.
	 * @param string $key Image size key.
	 * @param int    $width Image width.
	 * @param int    $height Image height.
	 * @param int    $crop Crop image.
	 *
	 * @return array
	 */
	public function resize_image( $attach_id, $meta_data, $key, $width, $height, $crop ) {
		$crop_str = ImageSize::crop_string( $crop );

		$upload_dir        = wp_get_upload_dir();
		$intermediate_size = image_get_intermediate_size( $attach_id, $key );
		$intermediate_path = '';

		if ( ! empty( $intermediate_size ) ) {
			$intermediate_path = $intermediate_size['path'];
		}

		$image_baseurl = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $intermediate_path;

		$meta_data['sizes'][ $key ]['valid'] = true;

		if ( ! file_exists( $image_baseurl ) || ! isset( $meta_data['sizes'][ $key ]['rwd_width'] )
			|| $meta_data['sizes'][ $key ]['rwd_width'] !== $width || $meta_data['sizes'][ $key ]['rwd_height'] !== $height
			|| ( 0 !== strcmp( $crop_str, $meta_data['sizes'][ $key ]['crop'] ) )
		) {
			// in dummy mode we do not resize anything.
			if ( JRI_DUMMY_IMAGE ) {
				$meta_data['sizes'][ $key ]['valid'] = false;
				return $meta_data;
			}

			// Get WP Image Editor Instance.
			$image_path   = get_attached_file( $attach_id );
			$image_editor = wp_get_image_editor( $image_path );
			if ( ! is_wp_error( $image_editor ) ) {
				// Create new image.
				$auto_width  = ( 19998 === $width || 9999 === $width ? null : $width );
				$auto_height = ( 19998 === $height || 9999 === $height ? null : $height );
				// WP Image Editor resize.
				$image_editor->resize( $auto_width, $auto_height, $crop );
				// Generate filename.
				$resize_filename = basename( $image_editor->generate_filename() );
				$resize_sizes    = $image_editor->get_size();

				// We taked resized image only if we sure that resize was successful and resized dimensions are correct.
				// - if original image is bigger than resized copy - resize was successful.
				if ( ( $meta_data['width'] > $resize_sizes['width'] && $meta_data['height'] > $resize_sizes['height'] )
					// - if crop enabled and resized image match the requested size - resize was successful.
					|| ( ! empty( $crop ) && $resize_sizes['width'] == $width && $resize_sizes['height'] == $height )
				) {
					// WP Image Editor save image.
					$image_editor->save();
					$meta_data['sizes'][ $key ] = array(
						'width'      => $resize_sizes['width'],
						'height'     => $resize_sizes['height'],
						'rwd_width'  => $width,
						'rwd_height' => $height,
						'crop'       => $crop_str,
						'file'       => $resize_filename,
						'mime-type'  => get_post_mime_type( $attach_id ),
					);
				} else {
					// use max image size for the bigger sizes.
					if ( ! strpos( $key, '@' ) ) {
						$meta_data['sizes'][ $key ] = array(
							'width'      => $meta_data['width'],
							'height'     => $meta_data['height'],
							'file'       => basename( $meta_data['file'] ),
							'rwd_width'  => $width,
							'rwd_height' => $height,
							'crop'       => $crop_str,
							'mime-type'  => get_post_mime_type( $attach_id ),
						);
					} else {
						unset( $meta_data['sizes'][ $key ] );
					}
				}
				// save to cache.
				$this->set_attachment_metadata( $attach_id, $meta_data );
				// update metadata.
				wp_update_attachment_metadata( $attach_id, $meta_data );
				// update JIO attachment status.
				update_post_meta( $attach_id, '_just_img_opt_status', self::STATUS_IN_QUEUE );
			}
		}

		return $meta_data;
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
	 * @param array $meta_data New meta data values.
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
	 * Generate final file URL.
	 *
	 * @param string $baseurl Attachment folder base url.
	 * @param array  $source File sources array.
	 *
	 * @return string
	 */
	protected function get_attachment_url( $baseurl, $source ) {
		$url = $source['file'];

		if ( ! preg_match( '/^http/', $url ) ) {
			$url = $baseurl . $url;
		}
		return $url;
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

	/**
	 * List of primary sizes, which should be printed before all other styles
	 *
	 * @return array
	 */
	public static function get_background_primary_sizes() {
		return array(
			'', // no media query.
			'@media (-webkit-min-device-pixel-ratio:1.5), (min-resolution : 144dpi)', // 2x retina media query.
			'@media (-webkit-min-device-pixel-ratio:2.5), (min-resolution : 192dpi)', // 3x retina media query.
		);
	}
}