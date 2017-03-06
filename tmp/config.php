<?php

return array(
	'thumbnail' => [
		[ 300, 200, true ],
	],
	'medium' => [
		[
			[ 640, 480 ],
			'picture' => '<source srcset="{src}" media="(min-width: {w}px)">',
			'bg' => '',
			'srcset' => '{w}w',
			'sizes' => '(min-width: 535px) {w}px',
		],
		'home' => [
			[ 530, 360 ],
			'picture' => '<source srcset="{src}" media="(min-width: {w}px)">',
			'bg' => '',
			'srcset' => '{w}w',
			'sizes' => '(min-width: 415px) {w}px',
		],
		'rwd-mobile' => 'inherit',
	],
	'large' => [
		[
			[ 1200, 800 ],
			'picture' => '<source srcset="{src}" media="(min-width: {w}px)">',
			'bg' => '',
			'srcset' => '{w}w',
			'sizes' => '(min-width: 981px) {w}px',
		],
		'rwd-tablet' => 'inherit',
		//'medium' => 'inherit',
		//'medium-home' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
	'hd' => [
		[ [ 1600, 900 ], ],
		'rwd-tablet' => 'inherit',
		'rwd-laptop' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
	'uhd' => [
		[ [ 1920, 1080 ], ],
		'rwd-desktop' => 'inherit',
		'rwd-laptop' => 'inherit',
		'rwd-tablet' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
);