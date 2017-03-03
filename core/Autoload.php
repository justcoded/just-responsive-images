<?php

namespace jri\core;

/**
 * SPL autoload registration for plugin to prevent using file includes
 */
class Autoloader {

	/**
	 * Class contructor register SPL autoload callback function
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'loader' ) );
	}

	/**
	 * Search for the class by namespace path and include it if found.
	 *
	 * @param string $class_name   Undefined class name to search for.
	 */
	public function loader( $class_name ) {
		$class_path = str_replace( '\\', '/', $class_name );

		// check if this class is related to the plugin namespace. exit if not.
		if ( strpos( $class_path, 'jri' ) !== 0 ) {
			return;
		}

		$path = preg_replace( '/^jri\//', JRI_ROOT . '/', $class_path ) . '.php';

		if ( is_file( $path ) ) {
			require_once( $path );
		}
	}

}

new Autoloader();
