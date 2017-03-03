<picture>
	<source srcset="image-desktop.jpg, image-desktop-2x.jpg 2x" media="(min-width: 1281px)"><!-- 1920px image!-->
	<source srcset="image-landscape.jpg" media="(min-width: 981px)"><!-- 1280px !-->
	<source srcset="image-tablet.jpg" media="(min-width: 415px)"><!-- 980px image!-->
	<img srcset="image-mobile.jpg" alt="test"> <!-- 414px image!-->
</picture>

<style>
	.art-img {
		background-image: url(build/images/article-bg.jpg);
	}

	@media screen and (max-width:1280px) {
		.art-img {
			background-image: url(build/images/article-bg-landscape.jpg);
		}
	}

	@media screen and (max-width:980px) {
		.art-img {
			background-image: url(build/images/article-bg.jpg);
		}
	}
	@media screen and (max-width:414px) {
		.art-img {
			background-image: url(build/images/article-bg-1-mobile.jpg);
		}
	}
</style>

<img sizes="(min-width: 1281px) 1920px, (min-width: 981px) 1280px, (min-width: 415px) 980px, 414px"
     srcset="examples/images/medium.jpg 414w,
     examples/images/large.jpg 980w,
     examples/images/extralarge.jpg 1280w,
     examples/images/extralarge-2x.jpg 2560w,
     examples/images/extralarge.jpg 1920w" alt="...">


<?php

 	/**
	 * Filter max image sizes
	 *
	 * @param array        $max_size  Max width and max height values as an array.
	 * @param string|array $size      Size of what the result image should be.
	 * @param string       $context   context where called: display|edit.
	 *
	 * @return array  new sizes
	 /
public function add_max_image_sizes( $max_size, $size, $context ) {
	if ( is_array( $size ) || (is_string( $size ) && empty( $this->post_thumbnails[ $size ] )) ) {
		return $max_size;
	}

	$size_conf = $this->post_thumbnails[ $size ];
	if ( is_array( $size_conf ) ) {
		$size_conf = $size_conf[0];
	}

	$max_size = explode( 'x', $size_conf );

	return $max_size;
}

	 */
?>