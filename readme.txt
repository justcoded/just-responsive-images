=== Just Responsive Images ===
Contributors: aprokopenko
Plugin Name: Just Responsive Images
Description: Providing full control to set your own responsive image properties for WordPress 4.4+, the ability to use the &lt;picture&gt; tag, auto-generate image backgrounds and supports retina images.
Tags: responsive post thumbnail, post thumbnail as background, retina support, retina image, retina post thumbnail, responsive post attachment, responsive images, responsive attachments, post thumbnails, media
Author: JustCoded / Alex Prokopenko
Author URI: http://justcoded.com/
Requires at least: 4.5
Tested up to: 5.8
Requires PHP: >=5.6
License: GPL3
Stable tag: trunk

Providing full control to set your own responsive image properties for WP 4.4+, the ability to use the &lt;picture&gt; tag and image backgrounds.

== Description ==

The Just Responsive Images plugin gives you control of responsive image properties, which WordPress 4.4+ inserts to all post thumbnails by default.

The default solution is to insert all available image sizes as srcset attribute into img tag. This is not optimal, because the browser gets too much image resolutions, it can generate more requests to the server (to get the right image) and it takes longer to display the image itself. Not to mention, Google Page Speed inspector is not satisfied with such a method.

If you have hand-coded a mobile-friendly HTML/CSS for your theme it usually has media queries for background images and &lt;picture&gt; tags instead of &lt;img&gt; tags. A lot of images are used as block backgrounds very often, which should be editable from CMS. All these best practices are not supported in the WordPress core by default and you end up wasting your time re-writing standard functions.

That's why we're here! We want to provide easy-to-use control for customizing srcset for each image size dimension you use inside your theme. Also we're happy to provide you with a few helpers which will generate tags and generate required media queries for backgrounds.


Full documentation and configuration options are available on our github page:
[https://github.com/justcoded/just-responsive-images](https://github.com/justcoded/just-responsive-images).

Feel free to post your suggestions or bugs under Issues section on github.

= Generating image sizes on request =

This plugin DOES NOT register image sizes with WordPress `add_image_size` function. So they are not resized on upload in admin panel. This is done because on big sites there are too much image sizes (especially with enabled retina option) and in this case WordPress wastes the disk space and database with useless files.

Our plugin resize images ONLY when you open the page with an image, printed with rwd functions. Resize is performed through WordPress built in functions, so once resized - WordPress will have information about new sizes available for your image. **Unfortunately it's impact the site speed, when you load the page with images by a first time.** Any good copywriter/administrator checks his content before publishing, so this delay will be seen by admin users in most cases.

= IMPORTANT =

In version 1.2 default RWD set background options updated to mobile-first strategy (from desktop-first).
If you use nested rules from RWD set you should update your main size background option to have @media query with `min-width` rule.

= DEV Mode =

In DEV mode plugin does not resize any images and simply use placeholder images. This can be used to reduce disk space, while you develop and configure our plugin to match all required screen sizes.

To enable it you need to define new constant in your wp-config.php file:

`define( 'JRI_DUMMY_IMAGE', true );`

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend
3. Add hook `add_filter('rwd_image_sizes', 'my_rwd_image_sizes');`
4. Create new function `my_rwd_image_sizes` and set the configuration array. The example can be found inside the plugin folder in `/data/config-example.php`.

== Screenshots ==

1. Responsive image sizes declaration/config.
2. Template basic usage example.
3. Generated HTML.

== Upgrade Notice ==

= Version 1.4 =
By default plugin doesn't require any special requirements through upgrade. 

However if you want to clean up your disk from unused images/image sizes you need to complete several steps:

BEFORE UPGRADE:
- Install plugins [Image Cleanup](https://wordpress.org/plugins/image-cleanup/) and [Regenerate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/)
- Backup your Database and `wp-content/uploads` folder!
- Go to Dashboard > Tools > Uploads Cleanup (this page is provided by Just Responsive Images plugin)
- Press "Remove cropped/resized images" - this will clean up your disk from all intermediate image files.
- Now activate "Image Cleanup" plugin and open Dashboard > Tools > Image Cleanup.
- Press "Index Images" button.
- You should see a filter called "Invalid Attachment Meta (XXX)" after indexing, click on it.
- Now select all rows, choose "Delete, Except [Full]" and press "Apply". If you have pagination available - repeat this action for all pages.
- Now you should upgrade Just Responsive Images plugin

AFTER UPGRADE:
- After upgrade the only registered image sizes will be `thumbnail`, `medium` and `large` and your theme specific if there are some. After "Uploads cleanup" we don't have any resized images on disk, so Media library will show missing images.
- To fix missing images in Media Library - use Regenerate Thumbnails plugin.
- Now you can open your site and Just Responsive Images will generate only required sizes.
- We recommend to click through the most important pages of your site to generate required images and do not annoy your visitors with long delay in page load.

= Version 1.0 - 1.3 =
There are no any special upgrade instructions for version 1.0 - 1.3
To upgrade remove the old plugin folder. After than follow the installation steps 1-2.

== Changelog ==

= Version 1.6.7 - 6 September 2021 =
    * PHP 8 support
= Version 1.6.6 - 16 November 2020 =
    * PHP 7.4 support
= Version 1.6.5 - 5 March 2019 =
    * Fixed retina size, which match image original size. 
= Version 1.6.4 - 25 January 2019 =
    * Fixed always resize for retina if 2x is bigger than original image. 
    * Add ability to set default tag for rwd_attachment_image() with a filter.
= Version 1.6.3 - 9 August 2018 =
    * Fix background usage if no other options specified. 
= Version 1.6.2 - 3 July 2018 =
    * Fix dev mode dummy images with sizes set in DB but missing on file system. 
= Version 1.6.1 - 28 June 2018 =
    * Added attachment ID text to dev mode dummy images. 
= Version 1.6.0 - 27 June 2018 =
    * Dev mode with placeholder images instead of real images in case correct sizes are missing. 
= Version 1.5.1 - 3 April 2018 =
    * Added compatibility with Crop Images plugin
    * Fix main editor content responsive images (it was broken after some WP update)
= Version 1.5 - 15 March 2018 =
    * Added compatibility with Just Image Optimizer plugin
= Version 1.4.1 - 9 March 2018 =
    * Bug fix: Wrong image source taken when image crop enabled and original width/height is the same and required height/width are less than origin.
= Version 1.4 - 7 March 2018 =
	* Upgrate: Changed image size generation to generate images on request from site frontend. This impact site speed on first page request!
	* Update: Add alt to svg and set default class
= Version 1.3 - 16 January 2018 =
	* Update: If media is SVG - then we always print <img> with hard-coded width/height attributes from configuration array
= Version 1.2.1 - 12 September 2017 =
	* Bug fix: Small images generate warning in featured image box
= Version 1.2 - 12 July 2017 =
	* New: True retina support
	* Bug fix: responsive image with "img" tag does not work in different IE/Edge versions
= Version 1.1.4 - 4 May 2017 =
	* New: SVG images support (If user used SVG instead of usual image - it will be printed)
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
