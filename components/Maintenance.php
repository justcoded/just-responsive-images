<?php

namespace jri\components;

/**
 * Class Maintenance
 * Performe maintenance tasks on plugin upgrade (like show upgrade notices etc).
 *
 * @package jri\components
 */
class Maintenance {
	const VER_OPTION = 'just_responsive_images_version';

	/**
	 * Current plugin version.
	 *
	 * @var float
	 */
	public $version;

	/**
	 * Previous plugin version.
	 *
	 * @var float|null
	 */
	public $prev_version;

	/**
	 * Maintenance constructor.
	 */
	public function __construct() {
		$this->version = \JustResponsiveImages::$version;
		if ( $this->prev_version = get_option( self::VER_OPTION, null ) ) {
			$this->maintenance( $this->prev_version, $this->version );
		}

		update_option( self::VER_OPTION, $this->version );
	}

	/**
	 * Perform maintenance tasks on upgrade.
	 *
	 * @param float $prev_version Previous plugin version.
	 * @param float $new_version Current plugin version.
	 */
	public function maintenance( $prev_version, $new_version ) {
		// TODO: write new code if necessary.
	}
}