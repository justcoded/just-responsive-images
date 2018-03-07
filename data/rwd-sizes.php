<?php

return array(
	'rwd 2x' => array(
		'desktop' => array(
			array( 1920, null ),
			'picture'   => '<source srcset="{src}" media="(min-width: 1281px)">',
			'bg'        => '@media (min-width:1281px)',
			'bg_retina' => '@media (min-width:1281px) and {dpr}, (min-width:1281px) and {min_res}',
			'srcset'    => '{w}w',
			'sizes'     => '(min-width: 1281px) {w}px',
		),
		'laptop'  => array(
			array( 1280, null ),
			'picture'   => '<source srcset="{src}" media="(min-width: 981px)">',
			'bg'        => '@media (min-width: 981px) ',
			'bg_retina' => '@media (min-width: 981px) and {dpr}, (min-width: 981px) and {min_res}',
			'srcset'    => '{w}w',
			'sizes'     => '(min-width: 981px) {w}px',
		),
		'tablet'  => array(
			array( 980, null ),
			'picture'   => '<source srcset="{src}" media="(min-width: 415px)">',
			'bg'        => '@media (min-width: 415px)',
			'bg_retina' => '@media (min-width: 415px) and {dpr}, (min-width: 415px) and {min_res}',
			'srcset'    => '{w}w',
			'sizes'     => '(min-width: 415px) {w}px',
		),
		'mobile'  => array(
			array( 414, null ),
			'picture'   => '<img src="{single-src}" srcset="{src}" alt="{alt}">', // mobile-first strategy picture img.
			'bg'        => '',                                                 // mobile-first strategy bg.
			'bg_retina' => '@media {dpr}, {min_res}',
			'srcset'    => '{w}w',
			'sizes'     => '{w}px',
		),
	),
);
