<?php

return array(
	'thumbnail' => [
		array( 300, 200, true ),
	],
	'medium' => [
		[
			array( 1200, 900 ),
			'picture' => '<source srcset="{src}" media="(min-width: {w}px)">',
			'bg' => '',
			'srcset' => '{w}w',
			'sizes' => '(min-width: 535px) {w}px',
		],
		'rwd-mobile' => 'inherit',
	],
	'large' => [
		[
			array( 1200, 900 ),
			'picture' => '<source srcset="{src}" media="(min-width: {w}px)">',
			'bg' => '',
			'srcset' => '{w}w',
			'sizes' => '(min-width: 981px) {w}px',
		],
		'rwd-tablet' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
	'hd' => [
		'rwd-tablet' => 'inherit',
		'rwd-laptop' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
	'uhd' => [
		'rwd-desktop' => 'inherit',
		'rwd-laptop' => 'inherit',
		'rwd-tablet' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
);