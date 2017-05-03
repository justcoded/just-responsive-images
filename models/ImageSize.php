<?php
namespace jri\models;

/**
 * Class ImageSize
 *
 * @package jri\objects
 */
class ImageSize {
	/**
	 * Image size unique key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Image width
	 *
	 * @var int
	 */
	public $w;

	/**
	 * Image height
	 *
	 * @var int
	 */
	public $h;

	/**
	 * Image width
	 *
	 * @var int
	 */
	public $w_2x;

	/**
	 * Image height
	 *
	 * @var int
	 */
	public $h_2x;

	/**
	 * Image crop options
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_image_size/
	 *
	 * @var bool|array
	 */
	public $crop = false;

	/**
	 * ImageSize constructor.
	 *
	 * @param string       $key Image size unique key.
	 * @param array|string $params Size width, height, crop  options.
	 *
	 * @throws \Exception  Wrong size parameter passed.
	 */
	public function __construct( $key, $params ) {
		if ( ( is_array( $params ) && count( $params ) < 2 )
			|| ( is_string( $params ) && strpos( $params, 'x' ) === false )
		) {
			throw new \Exception( "ImageSize::_construct() : Wrong size parameters passed for key '{$key}'" );
		}

		if ( ! is_array( $params ) ) {
			$params = explode( 'x', $params );
		}
		if ( 3 > count( $params ) ) {
			$params[] = false;
		}
		$params = array_values( $params );

		$this->key  = $key;
		$this->w    = absint( $params[0] );
		$this->h    = absint( $params[1] );
		$this->w_2x    = absint( $params[0] * 2 );
		$this->h_2x    = absint( $params[1] * 2 );
		$this->crop = absint( $params[2] );

		$this->register();
	}

	/**
	 * Call wordpress function to register current valid size.
	 */
	public function register() {
		add_image_size( $this->key, $this->w, $this->h, $this->crop );
		//register 2x retina image
		add_image_size( $this->key . '_2x', $this->w_2x, $this->h_2x, $this->crop );
		if ( in_array( $this->key, array( 'thumbnail', 'medium', 'large', 'medium_large' ) ) ) {
			update_site_option( "{$this->key}_size_w", $this->w );
			update_site_option( "{$this->key}_size_h", $this->h );
			update_site_option( "{$this->key}_crop", ! empty( $this->crop ) );
		}
	}
}
