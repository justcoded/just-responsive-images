<?php

/*
Plugin Name: Just Responsive Images
Description: Providing full control to set your own responsive image properties for WordPress 4.4+, the ability to use the &lt;picture&gt; tag, auto-generate image backgrounds and supports retina images.
Tags: responsive post thumbnail, post thumbnail as background, retina support, retina image, retina post thumbnail, responsive post attachment, responsive images, responsive attachments, post thumbnails, media
Version: 1.6.7
Author: JustCoded / Alex Prokopenko
Author URI: http://justcoded.com/
License: GPL3
*/

define( 'JRI_ROOT', dirname( __FILE__ ) );
require_once( JRI_ROOT . '/core/Autoload.php' );
require_once( JRI_ROOT . '/core/helpers.php' );

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
		self::$version     = '1.650';

		// init features, which this plugin is created for.
		new components\Maintenance();
		new components\PostAttachment();
		new components\MediaMetaInfo();
		new components\UploadsCleanup();
	}

}

JustResponsiveImages::run();
