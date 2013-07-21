<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
	array(
        'params' => array(
            'dbname' => $params['dbname'],
            'project-repos-path' => sys_get_temp_dir() . '/object-depot-repos',
            'backup-folder' => sys_get_temp_dir() . "/object-depot-backup"
        ),
        'import' => array(
            'application.controllers.*',
            'application.extensions.mailer.*'
        ),
		'components'=>array(
            'db'=>array(
                'connectionString' => 'mysql:host=localhost;dbname=' . $params['dbname'],
            ),
            'request' => array(
                'hostInfo' => $params['site-url'],
                'baseUrl' => '',
                'scriptUrl' => '',
            ),
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
		),
	)
);
