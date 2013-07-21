<?php

$params = parse_ini_file(dirname(__FILE__)."/../../../../object-depot.ini");
$params = array_merge(require(dirname(__FILE__).'/params.php'), $params);

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'My Console Application',
    	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.commands.*',
	),

	'components'=>array(
        'request' => array(
            'hostInfo' => $params['site-url'],
            'baseUrl' => '',
            'scriptUrl' => '',
        ),
		'urlManager'=>array(
            'class' => 'DirectUrlManager',
			'urlFormat'=>'path',
			'rules'=>array(
                array('class' => 'UserProjectFileUrlRule'),
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
        'file'=>array(
            'class' => 'FileApiWrapper'
        ),

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=' . $params['dbname'],
			'emulatePrepare' => true,
			'username' => $params['dbuser'],
			'password' => $params['dbpass'],
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'GroupWritableFileLogRoute',
                    'perms' => 0777,
					'levels'=> $params['log-levels'],
				),
				array(
					'class'=>'ConsoleLogRoute',
					'levels'=> $params['log-levels'],
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
    ),
    'params'=> $params);


?>