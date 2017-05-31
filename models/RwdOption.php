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
			'picture' => null,
			'bg'      => null,
			'srcset'  => null,
			'sizes'   => null,
		), $params );
		if ( !isset($params[0]) ) $params[0] = '';

		$this->key     = $key;
		$this->size    = new ImageSize( $key, $params[0], $retina_options );
		$this->picture = $params['picture'];
		$this->bg      = $params['bg'];
		$this->srcset  = $params['srcset'];
		$this->sizes   = $params['sizes'];
		$this->retina_options  = $retina_options;

		// save to global.
		global $rwd_image_options;
		$rwd_image_options[ $key ] = $this;
	}
}