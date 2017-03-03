<?php

return array(
	'rwd' => array(
		'desktop' => array(
			array( 1920, 9999 ),
			'picture' => '(min-width: 1281px)',
			'bg' => '@media screen and (min-width:1281px)', // main image.
			'srcset' => '1920w',
			'sizes' => '(min-width: 1281px) 1920px',
		),
		'laptop' => array(
			array( 1280, 9999 ),
			'picture' => '(min-width: 981px)',
			'bg' => '@media screen and (max-width:1281px)',
			'srcset' => '1280w',
			'sizes' => '(min-width: 981px) 1280px',
		),
		'tablet' => array(
			array( 980, 9999 ),
			'picture' => '(min-width: 415px)',
			'bg' => '@media screen and (max-width:980px)',
			'srcset' => '980w',
			'sizes' => '(min-width: 415px) 980px',
		),
		'mobile' => array(
			array( 414, 9999 ),
			'picture' => '(max-width: 414px)', // main img.
			'bg' => '@media screen and (max-width:414px)',
			'srcset' => '414w',
			'sizes' => '414px',
		),
	),
);