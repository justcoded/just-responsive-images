<?php

namespace jri\core;

/**
 * Class Singleton
 * Singleton design pattern basic class.
 *
 * @package jri\core
 */
class Singleton {
	/**
	 * Refers to a single instance of this class.
	 *
	 * @var Singleton
	 */
	protected static $instance = null;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Singleton A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Alias for creating object of *Singleton* pattern
	 *
	 * @return Singleton A single instance of this class.
	 */
	public static function run() {
		return static::get_instance();
	}

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	protected function __construct() {
	}

	/**
	 * Private clone method to prevent cloning of the instance of the
	 * *Singleton* instance.
	 *
	 * @return void
	 */
	final public function __clone() {
		throw new \RuntimeException(static::class . ' is singleton and cannot be cloned/serialized.');
	}

	/**
	 * Private unserialize method to prevent unserializing of the *Singleton*
	 * instance.
	 *
	 * @return void
	 */
	final public function __wakeup() {
		throw new \RuntimeException(static::class . ' is singleton and cannot be cloned/serialized.');
	}

}
