# Just Responsive Images 
### WordPress plugin 


Just Responsive Images plugin registers few new functions, which help print responsive images with <picture> tag, 
<img> tag with srcset and generate background media queries based on simple configuration array with image
 dimensions.

Starting from version 4.4, WordPress really generate <img> tags with srcset attribute. 
It does it automatically by placing all available registered image sizes, which is ridiculous for big sites. 
If you have a big site with 10 or more image sizes this will increase your html code significantly. There are a few
hooks which allows you to control these attributes, however to use them you should have strong technical background. 
A lot of themes has big visual images placed as backgrounds as well. Which is not supported by WordPress core at all. 

If you create your own hand-coded mobile-friendly html/css for your theme (or order it in really good frontend developers) 
you will find <picture> tags and background technics inside html. Manually creating functions to print each image is
too time consuming.

That's why we're here! We want to provide easy-to-use control and functions to print <img srcset>, <picture> tags 
as long as backgrounds css.

## Installation

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend

## Register configuration hook

To set configuration options you should create new function and register new filter hook `rwd_image_sizes`:

	add_filter('rwd_image_sizes', 'my_rwd_image_sizes');
	function my_rwd_image_sizes( $image_sizes ) {
		$my_image_sizes = array( ... );
		return $my_image_sizes;
	}

If you have a complex sizes with a lot of different image sizes - we recommend to use separate file to store settings.
In this case load function will looks like this:

	add_filter('rwd_image_sizes', 'my_rwd_image_sizes');
	function my_rwd_image_sizes( $image_sizes ) {
		return include get_stylesheet_directory() . '/rwd-image-sizes.php';
	}

## Configuration array

Here comes the most important part, you need to configure it very carefully. I will try to explain how to create 
this array step-by-step.

We will use configuration as separate file. 

Configuration array is an associative multi-dimensional array. 

#### Main image sizes

The first level indicates main image sizes, which should be registered with Wordpress `add_image_size()` function.
We recommend to re-write standard sizes as well, like thumnail, medium and large. 
Each main size should be an array as well, so it looks like this:

	<?php
	return array(
		'thumbnail' => array( ... ),
		'medium' => array( ... ),
		'large' => array( ... ),
		'hd' => array( ... ),
	);

In this example we overwrite standard 3 wordpress sizes thumbnail, medium and large and register new one called 'hd'.

#### Single image size

For small images you don't need to add any responsive tags, because they are smaller then any screen size (even mobile).
For example you have post featured image displayed on blog listing in size of 250 x 200.

In this case you should specify size array under your main image size:
 
	<?php
	return array(
		'thumbnail' => array(
			array( 250, 200, true ), // single size, dimensions set here
		),
		...
	);
	
The dimensions array looks the same as parameters passed to wordpress `add_image_size()` function. So they are:

* width, integer
* height, integer
* crop, true|false|array, which set crop position

#### Nested sizes

Let's imagine more complex example. You have full-width visual image on the top of your single post. For desktop version
you will use 1920px or even 2400px image. However if you use phone to browse the site - user don't like to wait
loading of such big images, because what he see is a 320-400px on his screen. So we need to examplain, that for
big visual image we should use smaller versions on smaller resolutions.

Here we will use more complex configuration. Now all arrays inside main size will be much more complex and will have
special structure like this:

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

As you can see here we register new image size "visual" with size 1920x500px, and it has lower resolutions, which 
has name there own names as well:

* 1024x300 (the full qualified name will be "visual-tablet")
* 414x200 (the full qualified name will be "visual-mobile")

Furthermore, each size has to have responsive options.

#### Responsive options

These are options, which will be used to generate <picture>, <img> or background html/css.

Let's start from an example:
 
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
    			'picture' => '<img srcset="{src}" alt="{alt}">',,
    			'bg' => '@media screen and (max-width:980px)',
    			'srcset' => '980w',
    			'sizes' => '(min-width: 415px) 980px',
    		),
    	),
    );

For each image size you can specify any of 4 keys:

* `picture` - a code part to generate <source> or <img> tags inside <picture> tag. 
* `bg` - media query to be used to wrap the css selector. Keep blank to set default image here
* `srcset` - <img> attribute part - width descriptor for this image size. 
* `sizes`  - <img> attribute part - condition when current dimension should be visible.

If you use only one way to print your image (for example <picture> tag) - you can skip all other parts and set only
1 key `picture` inside responsive options.

#### Re-usable sizes

Some images can have similar small sizes inside. In this case you can use previously-defined keys inside other main sizes.
Imagine that we already have the code for "visual" image size, as shown above, then we can re-use it like this:

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
	
#### Pre-defined RWD set

The plugin has it's own set of rwd styles, which are optimal for big images display and resize them to smaller resolutions,
which passed Google Page Speed tests well.

It looks like this:

	return array(
		'rwd' => array(
			'desktop' => array(
				array( 1920, 9999 ),
				'picture' => '<source srcset="{src}" media="(min-width: 1281px)">',
				'bg' => '@media screen and (min-width:1281px)', // main image.
				'srcset' => '1920w',
				'sizes' => '(min-width: 1281px) 1920px',
			),
			'laptop' => array(
				array( 1280, 9999 ),
				'picture' => '<source srcset="{src}" media="(min-width: 981px)">',
				'bg' => '@media screen and (max-width:1280px)',
				'srcset' => '1280w',
				'sizes' => '(min-width: 981px) 1280px',
			),
			'tablet' => array(
				array( 980, 9999 ),
				'picture' => '<source srcset="{src}" media="(min-width: 415px)">',
				'bg' => '@media screen and (max-width:980px)',
				'srcset' => '980w',
				'sizes' => '(min-width: 415px) 980px',
			),
			'mobile' => array(
				array( 414, 9999 ),
				'picture' => '<img srcset="{src}" alt="{alt}">', // main img.
				'bg' => '@media screen and (max-width:414px)',
				'srcset' => '414w',
				'sizes' => '414px',
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

Sometimes mobile images are different from desktop images and there is a need to use another attachment image for smaller sizes.
To support this feature you can pass array here and specify which size use another attachment ID or object.

Let's check the example:

	rwd_attachment_image( $featured_image_id, array(
			'visual', // main size, which should be rendered; it should has a zero key!
			'mobile' => $mobile_image_id, // rewrite a key under 'visual' main size to use another image 
	));
	
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
		<source srcset="image-landscape.jpg" media="(min-width: 981px)"><!-- 1280px !-->
		<source srcset="image-tablet.jpg" media="(min-width: 415px)"><!-- 980px image!-->
		<img srcset="image-mobile.jpg" alt="test"> <!-- 414px image!-->
	</picture>
	
**img**
	
	<img sizes="(min-width: 1281px) 1920px, (min-width: 981px) 1280px, (min-width: 415px) 980px, 414px"
         srcset="examples/images/medium.jpg 414w,
         examples/images/large.jpg 980w,
         examples/images/extralarge.jpg 1280w,
         examples/images/extralarge-2x.jpg 2560w,
         examples/images/extralarge.jpg 1920w" alt="...">
         
**background**
    
    <style type="text/css">
    	/* rwd-background-styles */
    	.art-img {
    		background-image: url(images/article-bg-hd.jpg);
    	}
    	@media screen and (max-width:1280px) {
    		.art-img {
    			background-image: url(images/article-bg-landscape.jpg);
    		}
    	}
    	@media screen and (max-width:980px) {
    		.art-img {
    			background-image: url(images/article-bg-tablet.jpg);
    		}
    	}
    	@media screen and (max-width:414px) {
    		.art-img {
    			background-image: url(build/images/article-bg-mobile.jpg);
    		}
    	}
    </style>     