<?php

namespace jri\models;

/**
 * Class RwdSize
 *
 * @package jri\components
 */
class RwdOption {
	/**
	 * Option key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Current option image size
	 *
	 * @var ImageSize
	 */
	public $size;

	/**
	 * Picture tag media query
	 *
	 * @var string|null
	 */
	public $picture = null;

	/**
	 * Background media wrappers
	 *
	 * @var string|null
	 */
	public $bg = null;

	/**
	 * Background retina media wrappers
	 *
	 * @var string|null
	 */
	public $bg_retina = null;

	/**
	 * Img tag `srcset` attribute part
	 *
	 * @var string|null
	 */
	public $srcset = null;

	/**
	 * Img tag `sizes` attribute part
	 *
	 * @var string|null
	 */
	public $sizes = null;

	/**
	 * Retina keys
	 *
	 * @var array|null
	 */
	public $retina_options = null;

	/**
	 * RwdOption constructor.
	 *
	 * @param string $key Option key.
	 * @param array  $params Parameters.
	 * @param array  $retina_options Retina options.
	 */
	public function __construct( $key, $params, $retina_options ) {
		$params = array_merge( array(
			'picture'   => null,
			'bg'        => null,
			'bg_retina' => null,
			'srcset'    => null,
			'sizes'     => null,
		), $params );
		if ( ! isset( $params[0] ) ) {
			$params[0] = '';
		}

		$this->key            = $key;
		$this->size           = new ImageSize( $key, $params[0], $retina_options );
		$this->picture        = $params['picture'];
		$this->bg             = $params['bg'];
		$this->bg_retina      = $params['bg_retina'];
		$this->srcset         = $params['srcset'];
		$this->sizes          = $params['sizes'];
		$this->retina_options = $retina_options;

		if ( ! empty( $this->retina_options ) && empty( $this->bg_retina ) ) {
			if ( empty( $this->bg ) ) {
				$this->bg_retina = '@media {dpr}, {min_res}';
			} else {
				preg_match( '%\(\b(max-width.*?|min-width.*?)\b\)%', $this->bg, $bg_media_size );
				$pattern = str_replace( $bg_media_size[0], "{$bg_media_size[0]} and {dpr}, {$bg_media_size[0]} and {min_res}", $this->bg );
				// fix old settings and remove "screen and" option from media query.
				$this->bg_retina = str_replace( 'screen and ', '', $pattern );
			}
		}

		// save to global.
		global $rwd_image_options;
		$rwd_image_options[ $key ] = $this;
	}
}