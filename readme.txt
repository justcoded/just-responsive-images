=== Just Responsive Images === 
Plugin Name: Just Responsive Images
Description: Gives you full control to customize responsive image properties for WordPress 4.4+. Also helps to use post images as responsive backgrounds.
Author: Private Company
Requires at least: 4.4
Tested up to: 4.7.2
License: GPL3

Gives you full control to customize responsive image properties for WordPress 4.4+. Also helps to use post images as responsive backgrounds.

== Description ==

Just Responsive Images plugin gives you control of responsive image properties, which WordPress 4.4+ insert to all post thumbnails by default.

The default solution is to insert all available image sizes as srcset into img srcset.
This is not optimal, because browser gets too much image resolutions, it can generate more requests to server (to get the right image) and it takes longer to display image itself.
Furthermore, Google Page Speed inspector is not satisfied with such method.

If you have hand-coded mobile-friendly html/css for your theme it have to have media queries for background images, <picture> tags instead of <img> tag.
All these best practices is not supported by default WordPress core and you should waste your time to re-write standard functions.

That's why we're here! We want to provide easy-to-use control for customizing srcset for each image size dimension you use inside your theme.
Also we're happy to provide you with few helpers which will generate <picture> tag, generate required media queries for backgrounds.

Full documentation will be released soon as separate website.


== Changelog ==
= Version 1.0 =
	* First version of our plugin
