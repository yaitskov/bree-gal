<?php

return array(
	'older-100-days' => array(
		'route' => 'older-100-days',
		'duration' => 10.0,
        'created' => time() - 3600 * 24 * 101,
	),

	'1-day-age' => array(
		'route' => '1-day-age',
		'duration' => 10.0,
        'created' => time() - 3600 * 24 * 1,
	),

);
