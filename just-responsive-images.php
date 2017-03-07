<?php

/*
Plugin Name: Just Responsive Images
Description: Provides full control to set responsive image properties for WordPress 4.4+ and support retina images (2x). Allows printing of post attachments images as backgrounds.
Tags: responsive post thumbnail, responsive post thumbnail background, responsive post attachment, responsive images, responsive attachments, post thumbnails, media, retina support
Version: 1.0.2
Author: JustCoded / Alex Prokopenko
Author URI: http://justcoded.com/
License: GPL3
*/

define( 'JRI_ROOT', dirname( __FILE__ ) );
require_once( JRI_ROOT . '/core/Autoload.php' );

use jri\core;
use jri\components;

/**
 * Class JustResponsiveImages
 * Main plugin entry point. Includes components in constructor
 */
class JustResponsiveImages extends core\Singleton {
	/**
	 * Textual plugin name
	 *
	 * @var string
	 */
	public static $plugin_name;

	/**
	 * Current plugin version
	 *
	 * @var float
	 */
	public static $version;

	/**
	 * Plugin text domain for translations
	 */
	const TEXTDOMAIN = 'jri';

	/**
	 * Plugin main entry point
	 *
	 * Protected constructor prevents creating another plugin instance with "new" operator.
	 */
	protected function __construct() {
		// init plugin name and version.
		self::$plugin_name = __( 'Just Responsive Images', JustResponsiveImages::TEXTDOMAIN );
		self::$version     = 0.100;

		// init features, which this plugin is created for.
		new components\AttachmentImage();
	}

}

JustResponsiveImages::run();
