<?php

return array(
	'older-100-days' => array(
		'route' => 'older-100-days',
		'duration' => 10.0,
        'type' => 'GET',
        'created' => time() - 3600 * 24 * 101,
        'user_id' => 123,
        'ip' => '127.0.0.1'
	),

	'1-day-age' => array(
		'route' => '1-day-age',
		'duration' => 10.0,
        'type' => 'GET',
        'created' => time() - 3600 * 24 * 2,
        'user_id' => 123,
        'ip' => '127.0.0.1'
	),

);
