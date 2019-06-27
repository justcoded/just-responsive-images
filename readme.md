# Just Responsive Images 
### WordPress plugin 


The Just Responsive Images plugin gives you control of responsive image properties, which WordPress 4.4+ 
inserts to all post thumbnails by default.

The default solution is to insert all available image sizes as srcset attribute into img tag. 
This is not optimal, because the browser gets too much image resolutions, it can generate more requests to 
the server (to get the right image) and it takes longer to display the image itself. Not to mention, Google 
Page Speed inspector is not satisfied with such a method.

If you have hand-coded a mobile-friendly HTML/CSS for your theme it usually has media queries for background 
images and \<picture\> tags instead of \<img\> tags. A lot of images are used as block backgrounds very often, 
which should be editable from CMS. All these best practices are not supported in the WordPress core by default 
and you end up wasting your time re-writing standard functions.

That's why we're here! We want to provide easy-to-use control for customizing srcset for each image size 
dimension you use inside your theme. Also we're happy to provide you with a few helpers which will generate 
tags and generate required media queries for backgrounds.


## Installation

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend

## Register configuration hook

To set configuration options you should create a new function and register a new filter hook `rwd_image_sizes` inside your `functions.php` file:

	add_filter('rwd_image_sizes', 'my_rwd_image_sizes');
	function my_rwd_image_sizes( $image_sizes ) {
		$my_image_sizes = array( ... );
		return $my_image_sizes;
	}

If you have complex sizes with a lot of different image sizes - we recommend using a separate file to store settings. 
In this case, load function will look like this:

	add_filter('rwd_image_sizes', 'my_rwd_image_sizes');
	function my_rwd_image_sizes( $image_sizes ) {
		return include get_stylesheet_directory() . '/rwd-image-sizes.php';
	}

After that you need to create a new file called `rwd-image-sizes.php` and set a configuration array there.

## Configuration array

Here comes the most important part, you need to configure it very carefully. I will try to explain how to create 
this array step-by-step.

Configuration array is an associative multidimensional array.

_*All examples is based on a second option of configuration settings - as a separate configuration file._

#### Main image sizes

The first level indicates the main image sizes, which should be registered with the Wordpress `add_image_size()` 
function. We recommend re-writing standard sizes as well, like thumbnail, medium, and large. 
Each main size should be an array as well, so it looks like this:


	<?php
	return array(
		'thumbnail' => array( ... ),
		'medium' => array( ... ),
		'large' => array( ... ),
		'hd' => array( ... ),
	);

In this example we overwrite the 3 standard WordPress sizes: thumbnail, medium and large, 
and register a new one called 'hd'.

#### Single image size

For small images you don't need to add any responsive tags, because they are smaller than any screen size 
(even mobile). For example, you have post featured image displayed on a blog listing in a 250 x 200 size .

In this case you should specify the size array under your main image size:
 
	<?php
	return array(
		'thumbnail' => array(
			array( 250, 200, true ), // single size, dimensions set here
		),
		...
	);
	
The dimensions array looks the same as parameters passed to the WordPress `add_image_size()` function. function. So they are:

* width, integer
* height, integer
* crop, true|false|array, which set crop position

#### Nested sizes

Let's imagine a more complex example. You have a full-width visual image at the top of your single post. 
For desktop versions you will use a 1920px or even 2400px image. However, if you use a phone to browse the site, 
the user doesn't like to wait while such big images load because what he sees is 320-400px on his screen. 
So we need to set, that for big visual images we should use smaller versions on smaller resolutions.

Here we will use a more complex configuration. Now all arrays inside the main size will be much more complex 
and will have a special structure like this:

	<?php 
	return array(
		'visual' => array(
			array(
				array( 1920, 500, true ),
				// responsive options:
				...
			),
			'tablet' => array(
				array( 1024, 300, true ),
				// responsive options:
				...
			),
			'mobile' => array(
				array( 414, 200, true ),
				// responsive options:
				...
			),
		),
	);

As you can see here we registered a new image size "visual" with the size of 1920x500px, and it has lower resolutions 
which has it’s own name:

* 1024x300 (the full qualified name will be "visual-tablet")
* 414x200 (the full qualified name will be "visual-mobile")

In addition, each size has to have responsive options.

#### Responsive options

These are options, which will be used to generate \<picture\>, \<img\> or background HTML/CSS.

Let's start with an example:
 
	return array(
    	'visual' => array(
    		array(
    			array( 1920, 500, true ),
    			'picture' => '<source srcset="{src}" media="(min-width: 1281px)">',
    			'bg' => '', // main image, no media wrapper will be used.
    			'srcset' => '1920w', // descriptor
    			'sizes' => '(min-width: 1281px) 1920px', // condition
    		),
    		'tablet' => array(
    			array( 980, 9999 ),
    			'picture' => '<img src="{single-src}" srcset="{src}" alt="{alt}">',,
    			'bg' => '@media screen and (max-width:980px)',
    			'srcset' => '980w',
    			'sizes' => '(min-width: 415px) 980px',
    		),
    	),
    );

For each image size you can specify any of the following 4 keys:

* `picture` -  a code part to generate \<source\> or \<img\> tags inside a \<picture\> tag. 
* `bg` - media query to be used to wrap the css selector. Keep blank to set default image here.
* `srcset` - \<img\> attribute part - width descriptor for this image size. 
* `sizes`  - \<img\> attribute part - condition when current dimension should be visible.

If you use only one way to print your image (for example \<picture\>  tag) - you can skip all other parts and 
set only 1 key `picture` inside responsive options.

#### Re-usable sizes

Some images can have similar small sizes inside. In this case you can use previously defined keys inside other
main sizes. Imagine that we already have the code for "visual" image size, as shown above, then we can re-use 
it like this:

	return array(
		'visual' => array( ... ),
		'big-banner' => array(
			array(
				array( 2400, 500, true ),
				'picture' => '<source srcset="{src}" media="(min-width: 1281px)">',
				'bg' => '', // main image, no media wrapper will be used.
				'srcset' => '1920w', // descriptor
				'sizes' => '(min-width: 1281px) 1920px', // condition
			),
			'visual-tablet' => 'inherit',
		),
	);
	
#### Retina support
	
To support retina screens and print both usual and bigger images 
	you should add retina multiplier to your size name, separated with a space:

	return array(
		'visual 2x' => array( ... ),
	);

You can add more different multipliers for same size to generate even more retina sizes:

	return array(
		'visual 2x 3x' => array( ... ),
	);

However we recommend to use one retina size - `2x`, this size provides good visual image quality on screen and keep your file space less 
(comparing to setting several retina multipliers).

##### Retina background @media queries

By default plugin search such patterns inside `'bg'` property: 
	
	(min-width: XXXpx) or (max-width: XXXpx)
	
If such entries found, then plugin replace them with the structure below to generate @media retina query:
 
 	(min-width: XXXpx) and <min device pixel ratio query>, (min-width: XXXpx) and <min resolution query>
 	 
If you want to set your own specific media query for retina size you can use `'bg_retina'` property like this:

	return array(
		'big-banner 2x' => array(
			array(
				array( 2400, 500, true ),
				...
				'bg' => '@media (min-width:1281px)',
				'bg_retina' => '@media (min-width:1281px) and {dpr}, (min-width:1281px) and {min_res}',
				...
			),
			...
		),
	);

Special tokens `{dpr}` and `{min_res}` will be automatically replaced with a corresponding min device pixel ratio
	and min resolution values based on retina multiplier (2x or 3x).
	
#### Pre-defined RWD set

The plugin has its own set of rwd styles, which are optimal for big images display and resize them to smaller 
resolutions, which passed Google Page Speed tests well. This set has retina 2x support by default.

It looks like this:

	return array(
		'rwd 2x' => array(
			'desktop' => array(
				array( 1920, 9999 ),
				'picture' => '<source srcset="{src}" media="(min-width: 1281px)">',
				'bg' => '@media (min-width:1281px)',
				'bg_retina' => '@media (min-width:1281px) and {dpr}, (min-width:1281px) and {min_res}',
				'srcset' => '{w}w',
				'sizes' => '(min-width: 1281px) {w}px',
			),
			'laptop' => array(
				array( 1280, 9999 ),
				'picture' => '<source srcset="{src}" media="(min-width: 981px)">',
				'bg' => '@media (min-width: 981px) ',
				'bg_retina' => '@media (min-width: 981px) and {dpr}, (min-width: 981px) and {min_res}',
				'srcset' => '{w}w',
				'sizes' => '(min-width: 981px) {w}px',
			),
			'tablet' => array(
				array( 980, 9999 ),
				'picture' => '<source srcset="{src}" media="(min-width: 415px)">',
				'bg' => '@media (min-width: 415px)',
				'bg_retina' => '@media (min-width: 415px) and {dpr}, (min-width: 415px) and {min_res}',
				'srcset' => '{w}w',
				'sizes' => '(min-width: 415px) {w}px',
			),
			'mobile' => array(
				array( 414, 9999 ),
				'picture' => '<img src="{single-src}" srcset="{src}" alt="{alt}">', // mobile-first strategy picture img.
				'bg' => '',                                                 // mobile-first strategy bg.
				'bg_retina' => '@media {dpr}, {min_res}',
				'srcset' => '{w}w',
				'sizes' => '{w}px',
			),
		),
	);
	
You can use these presets inside your own styles.

## Template functions

We have 3 new template functions:

### rwd_attachment_image

`rwd_attachment_image( $attachment = null, $size = 'thumbnail', $tag = 'picture', $attr = array() )`

- WP_Post|int|null **$attachment** Atatchment object, Attachment ID or null. In case of null the function will search current post featured image.
- string|array **$size** Image size name or array of sizes. We will check this option in more details below.
- string **$tag** Available options are 'picture' and 'img'. The html tag, which will be used for image generation.
- array **$attr** Additional html attributes to be used for main tag (for example "class", "id", etc.).

**$size** option as array:

Sometimes mobile images are different from desktop images and there is a need to use another attachment image for 
smaller sizes. To support this feature you can pass array here and specify which size use another attachment ID
or object.

Let's check the example:

	rwd_attachment_image( $featured_image_id, array(
			'visual', // main size, which should be rendered; it should has a zero key!
			'mobile' => $mobile_image_id, // rewrite a key under 'visual' main size to use another image 
	));
	
**Important**: If you use retina multipliers we do not recommend to use `'img'` tag with sized array. 
In this case browser will automatically decide which image will be renedered and you won't get the same 
images on specific resolution in all browsers/devices.

	
### rwd_attachment_background
	
`rwd_attachment_background( $selector, $attachment = null, $size = 'thumbnail' )`	

- string **selector** CSS selector to be used inside inline styles code.
- WP_Post|int|null **$attachment** Atatchment object, Attachment ID or null. In case of null the function will search current post featured image.
- string|array **$size** Image size name or array of sizes. Same as for `rwd_attachment_image()`.

This function generate css styles and place them inside "buffer". This buffer will be printed in wp_footer().

If you want to print it earlier you can use next template function:

### rwd_print_styles

`rwd_print_styles()`

This function print styles from buffer, which were generated in `rwd_attachment_background()`.

## Generated code samples

It can generate code similar to this one:

**picture**

	<picture>
		<source srcset="image-desktop.jpg, image-desktop-2x.jpg 2x" media="(min-width: 1281px)"><!-- 1920px image!-->
		<source srcset="image-landscape.jpg, image-landscape-2x.jpg 2x" media="(min-width: 981px)"><!-- 1280px !-->
		<source srcset="image-tablet.jpg, image-tablet-2x.jpg 2x" media="(min-width: 415px)"><!-- 980px image!-->
		<img srcset="image-mobile.jpg, image-mobile-2x.jpg 2x" alt="test"> <!-- 414px image!-->
	</picture>
	
**img**
	
	<img sizes="(min-width: 1281px) 1920px, (min-width: 981px) 1280px, (min-width: 415px) 980px, 414px"
         srcset="examples/images/medium.jpg 414w,
         examples/images/medium-2x.jpg 828w,
         examples/images/large.jpg 980w,
         examples/images/large-2x.jpg 1960w,
         examples/images/extralarge.jpg 1280w,
         examples/images/extralarge-2x.jpg 2560w,
         examples/images/desctop.jpg 1920w,
         examples/images/desctop-2x.jpg 3840w" alt="...">
         
**background**
    
    <style type="text/css">
    	/* rwd-background-styles */
    	.article-bg {
    		background-image: url('images/bg-mobile.jpg');
    	}
    	@media (-webkit-min-device-pixel-ratio: 1.5), (min-resolution: 144dpi) {
    		.article-bg {
    			background-image: url('images/bg-mobile-2x.jpg');
    		}
    	}
    	@media (min-width: 415px) {
    		.article-bg {
    			background-image: url('images/bg-tablet.jpg');
    		}
    	}
    	@media (min-width: 415px) and (-webkit-min-device-pixel-ratio: 1.5), (min-width: 415px) and (min-resolution: 144dpi) {
    		.article-bg {
    			background-image: url('images/bg-tablet-2x.jpg');
    		}
    	}
    	@media (min-width: 981px) {
    		.article-bg {
    			background-image: url('images/bg-laptop.jpg');
    		}
    	}
    	@media (min-width: 981px) and (-webkit-min-device-pixel-ratio: 1.5), (min-width: 981px) and (min-resolution: 144dpi) {
    		.article-bg {
    			background-image: url('images/bg-laptop-2x.jpg');
    		}
    	}
    </style> 
