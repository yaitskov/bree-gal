<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('ext',dirname(__FILE__).'/../extensions');

//$params = @parse_ini_file(dirname(__FILE__)."/../../../../bree-gal.ini");
$params = array_merge(require(dirname(__FILE__).'/params.php')) ;//, $params);

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=> $params['sitename'],
    // 'language' => 'en',

	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.actions.*',
		'application.components.*',
        'application.commands.*'
	),

	'defaultController' => 'gallery',

	'components'=>array(
        'viewRenderer' => array(
            'class' => 'ext.twig.ETwigViewRenderer',
            'twigPathAlias' => 'application.extensions.twig.Twig',
            // All parameters below are optional, change them to your needs
            'fileExtension' => '.twig',
            'options' => array(
                'autoescape' => true,
            ),
            'extensions' => array(
                /* 'My_Twig_Extension', */
            ),
            'globals' => array(
                'html' => 'CHtml',
                'y' => 'Yii',
            ),
            'functions' => array(
                'get_class' => 'get_class',
            ),
            'filters' => array( // stupid twig swap argument
                'dt' => function ($dt) {
                    return Yii::app()->dateFormatter->formatDateTime($dt);
                },
                'htmlId' => function ($text) {
                    return preg_replace('/[ #.:]/', '-', $text);
                },
                'md5' => 'md5',
                't' => function ($text, $category='app', $vars=array()) {
                    return Yii::t($category, $text, $vars);
                },
                'substr' => function ($text, $max=6) {
                    $l = strlen($text);
                    if ($l > $max) {
                        return substr($text, 0, $max); // . '... ';
                    } else {
                        return $text;
                    }
                },
                'cr' => function ($text, $route) {
                    if ($route === Yii::app()->controller->route)
                        return $text;
                    return '';
                },
                'humanPeriod' => 'DateTimeUtils::formatPeriod',
                'money' => 'NumUtils::formatMoney',
                'humanBytes' => 'NumUtils::formatBytes',
                'jencode' => 'CJSON::encode',
            ),
            // Change template syntax to Smarty-like (not recommended)
            /* 'lexerOptions' => array( */
            /*     'tag_comment'  => array('{*', '*}'), */
            /*     'tag_block'    => array('{', '}'), */
            /*     'tag_variable' => array('{$', '}') */
            /* ), */
        ),
        'imagine'=>array(
            'class' => 'application.extensions.ImagineExt'
        ),
        'http'=>array(
            'class' => 'HttpWrapper'
        ),
        'file'=>array(
            'class' => 'FileApiWrapper'
        ),
		'user'=>array(
            'class' => 'application.components.WebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=' . $params['dbname'],
			'emulatePrepare' => true,
			'username' => $params['dbuser'],
			'password' => $params['dbpass'],
			'charset' => 'utf8',
			'tablePrefix' => 'tbl_',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'urlManager'=>array(
            'class' => 'DirectUrlManager',
			'urlFormat'=>'path',
			'rules'=>array(
				'login'=>'site/login',
				'logout'=>'site/logout',
				'settings'=>'user/mySettings',
				'register'=>'site/register',
				'my-projects'=>'project/myProjects',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'GroupWritableFileLogRoute',
                                        'perms' => 0777,
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

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=> $params
);