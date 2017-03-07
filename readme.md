=== Just Responsive Images === 

Provides full control to set your own responsive image properties for WordPress 4.4+, ability to use &lt;picture&gt; tag and support retina images.

== Description ==

Just Responsive Images plugin gives you control of responsive image properties, which WordPress 4.4+ insert to all post thumbnails by default.

The default solution is to insert all available image sizes as srcset into img srcset.
This is not optimal, because browser gets too much image resolutions, it can generate more requests to server (to get the right image) and it takes longer to display image itself.
Furthermore, Google Page Speed inspector is not satisfied with such method.

If you have hand-coded mobile-friendly html/css for your theme it usually has media queries for background images, &lt;picture&gt; tags instead of &lt;img&gt; tags.
Furthermore, images are used as block backgrounds very often.
All these best practices is not supported in WordPress core by default and you should waste your time to re-write standard functions.

That's why we're here! We want to provide easy-to-use control for customizing srcset for each image size dimension you use inside your theme.
Also we're happy to provide you with few helpers which will generate <picture> tag, generate required media queries for backgrounds.

Full documentation will be added on our github wiki pages.
https://github.com/justcoded/just-responsive-images.

Feel free to post your suggestions or bugs under Issues section on github.

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend
3. Add hook `add_filter('rwd_image_sizes', 'my_rwd_image_sizes');`
4. Create new function `my_rwd_image_sizes` and return configuration array. The example can be found inside the plugin folder in `/data/config-example.php`

== Upgrade Notice ==

To upgrade remove the old plugin folder. After than follow the installation steps 1-2.

== Changelog ==
= Version 1.0 =
	* First version of our plugin
