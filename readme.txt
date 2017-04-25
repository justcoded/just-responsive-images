=== Just Responsive Images ===
Contributors: aprokopenko
Plugin Name: Just Responsive Images
Description: Providing full control to set your own responsive image properties for WordPress 4.4+, the ability to use the &lt;picture&gt; tag, auto-generate image backgrounds and supports retina images.
Tags: responsive post thumbnail, responsive post thumbnail background, responsive post attachment, responsive images, responsive attachments, post thumbnails, media, retina support, retina images
Author: JustCoded / Alex Prokopenko
Author URI: http://justcoded.com/
Requires at least: 4.4
Tested up to: 4.7.2
License: GPL3
Stable tag: trunk

Providing full control to set your own responsive image properties for WP 4.4+, the ability to use the &lt;picture&gt; tag and image backgrounds.

== Description ==

The Just Responsive Images plugin gives you control of responsive image properties,
which WordPress 4.4+ inserts to all post thumbnails by default.

The default solution is to insert all available image sizes as srcset attribute into img tag. This is not optimal,
because the browser gets too much image resolutions, it can generate more requests to the server (to get the right image)
and it takes longer to display the image itself. Not to mention, Google Page Speed inspector is not satisfied with
such a method.

If you have hand-coded a mobile-friendly HTML/CSS for your theme it usually has media queries for background images
and &lt;picture&gt; tags instead of &lt;img&gt; tags. A lot of images are used as block backgrounds very often,
which should be editable from CMS. All these best practices are not supported in the WordPress core by default and
you end up wasting your time re-writing standard functions.

That's why we're here! We want to provide easy-to-use control for customizing srcset for each image size dimension you
use inside your theme. Also we're happy to provide you with a few helpers which will generate tags and generate required
media queries for backgrounds.


Full documentation and configuration options are available on our github page:
https://github.com/justcoded/just-responsive-images.

Feel free to post your suggestions or bugs under Issues section on github.

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend
3. Add hook `add_filter('rwd_image_sizes', 'my_rwd_image_sizes');`
4. Create new function `my_rwd_image_sizes` and set the configuration array. The example can be found inside the plugin folder in `/data/config-example.php`.

== Upgrade Notice ==

To upgrade remove the old plugin folder. After than follow the installation steps 1-2.

== Changelog ==
= Version 1.1.3 - 25 April 2017 =
	* Bug fix: responsive image with "img" tag does not work in IE11
= Version 1.1.2 - 14 April 2017 =
	* Bug fix: responsive image with "img" tag does not work in IE
= Version 1.1.1 - 31 March 2017 =
	* New: Print available image sizes on Media attachment edit screen. Useful for debugging.
= Version 1.1 - 24 March 2017 =
	* New: Ability to clean up all resized images from disk. To use go to Tools > Uploads Cleanup.
= Version 1.0.8 - 22 March 2017 =
	* Bug fix: Background main styles were printed after media queries.
= Version 1.0.7 - 16 March 2017 =
	* Bug fix: Post can be passed instead of attachment (now can't fixed)
	* Bug fix: Wrong image is taken as default if main image is not a featured image and mobile image missing (now default is correct)
= Version 1.0.6 =
	* Code refactoring (basically renaming files and class names)
= Version 1.0.5 =
	* Critical fix: Non-inherit settings were ignored during html generation with "Missing image size" warning.
= Version 1.0.4 =
	* Bug fix: Added "src" to <img> tag (Google Chrome has a bug with max-width css property without "src" attribute).
= Version 1.0.3 =
	* Bug fix: Fixed options which passed 'wp_get_attachment_image_attributes' filter. Some plugins use keys, which were missing in previous version.
= Version 1.0.2 =
	* New: Added parameter to pass additional html attributes into `rwd_attachment_image()`.
= Version 1.0.1 =
	* Bug fix: Single size option return empty result
	* Improvement: Smaller images now display lower resolution size even for big screens.
= Version 1.0 =
	* First version of our plugin.
