<?php
namespace jri\models;

/**
 * Class RwdSize
 *
 * @package jri\components
 */
class RwdSet {
	/**
	 * Base image size key.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Base image size dimensions.
	 *
	 * @var ImageSize
	 */
	public $size;

	/**
	 * Responsive options.
	 *
	 * @var RwdOption[]
	 */
	public $options = array();

	/**
	 * Retina options.
	 *
	 * @var RwdOption[]
	 */
	public $retina = array();

	/**
	 * RwdSet constructor.
	 *
	 * @param string $key Base image size key.
	 * @param array  $params Image size options and responsive presents.
	 *
	 * @throws \Exception Wrong configuration passed.
	 */
	public function __construct( $key, $params ) {
		if ( empty( $params ) ) {
			throw new \Exception( "RwdSet::__construct() : Main image size is missing for '$key'" );
		}

		$this->key = $this->clean_key( $key );
		$this->parse_retina_options( $key );

		// this means we doesn't have any responsive options (for example this is a small thumbnail).
		if ( 1 === count( $params ) ) {
			$this->size = new ImageSize( $this->key, array_shift( $params ), $this->retina );
			$this->options[$this->key] = new RwdOption( $this->key, array(
				array( $this->size->w, $this->size->h, $this->size->crop ),
				'picture' => '<img srcset="{src}" alt="{alt}">',
				'bg' => '',
				'srcset' => '{w}w',
				'sizes' => '{w}px',
			), $this->retina);
		} else {
			$this->parse_options( $params, $this->retina );
		}

		// save to global.
		global $rwd_image_sizes;
		$rwd_image_sizes[ $this->key ] = $this;
	}

	/**
	 * Parse argument to create RwdOption objects.
	 *
	 * @param array $params Set intiail params passed in constructor.
	 *
	 * @throws \Exception Using unregistered preset.
	 */
	public function parse_options( $params, $retina ) {
		global $rwd_image_options;

		foreach ( $params as $subkey => $conf ) {
			// If we have numeric key, that means we set dimension for the Set itself.
			// If we find numeric index not first time - this is error in config, however we just add numeric suffix and continue.
			if ( is_numeric( $subkey ) && 0 == $subkey ) {
				$subkey = $this->key;
			}

			// Check if we have inherit property.
			if ( is_string( $conf ) ) {
				if ( isset( $rwd_image_options[ $subkey ] ) ) {
					$this->options[ $subkey ] = $rwd_image_options[ $subkey ];
				} else {
					throw new \Exception( "RwdSet::__construct() : Using not registered preset '$subkey' in '$this->key'" );
				}
			} else {
				$nested_key               = ( $this->key === $subkey ) ? $this->key : "$this->key-$subkey";
				$this->options[ $subkey ] = new RwdOption( $nested_key, $conf, $retina );
			}
		}

		// get first option and take size object to save it as Set size.
		$first      = reset( $this->options );
		$this->size = new ImageSize( $this->key, array( $first->size->w, $first->size->h, $first->size->crop ), $retina );
	}

	/**
	 * Parse key to create Retina array options size.
	 *
	 * @param string $key Base image size key.
	 */
	public function parse_retina_options( $key ) {
		preg_match_all( '/(\s[0-9.]+x)/', $key, $retina_parse );
		if( $retina_parse ){
			foreach( $retina_parse as $key_retina => $retina_value ) {
				foreach( $retina_value as $value ){
					$this->retina[ trim($value) ] = floatval( $value );
				}
			}
		}
	}

	/**
	 * Clean image size key without retina size.
	 *
	 * @param string $key Base image size key.
	 *
	 * @return string Image size key.
	 */
	public function clean_key( $key ) {
		preg_match('/^(\s?\S+)/', $key, $clean_key);
		return trim( $clean_key[0] );
	}
}