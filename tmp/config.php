<?php

// retina??? > auto

$images = [
	// default responsive size
	'rwd' => array( '32x32', // super small size, to keep disk space clean.
		'desktop' => [ '1920x1920',
			'picture' => '(min-width: 1281px)',
			'bg' => '', // main image.
			'srcset' => '1920w',
			'sizes' => '(min-width: 1281px) 1920px',
		],
		'laptop' => [ '1280x1280',
			'picture' => '(min-width: 981px)',
			'bg' => 'screen and (max-width:1281px)',
			'srcset' => '1280w',
			'sizes' => '(min-width: 981px) 1280px',
		],
		'tablet' => [ '980x980',
			'picture' => '(min-width: 415px)',
			'bg' => 'screen and (max-width:980px)',
			'srcset' => '980w',
			'sizes' => '(min-width: 415px) 980px',
		],
		'mobile' => [ '414x414',
			'picture' => '', // main img.
			'bg' => 'screen and (max-width:414px)',
			'srcset' => '414w',
			'sizes' => '414px',
		],
	),

	// sizes
	'thumbnail' => [ '300x200' ],

	'medium' => [ '600x450',
		'rwd-mobile' => 'inherit',
	],
	'large' => [ '1200x600',
		'rwd-tablet' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
	'hd' => [ '1600x900',
		'rwd-tablet' => 'inherit',
		'rwd-laptop' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
	'uhd' => [ '1920x1080',
		'rwd-desktop' => 'inherit',
		'rwd-laptop' => 'inherit',
		'rwd-tablet' => 'inherit',
		'rwd-mobile' => 'inherit',
	],
];