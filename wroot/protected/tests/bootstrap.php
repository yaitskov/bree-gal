<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 1);
require_once($yiit);
//require_once(dirname(__FILE__).'/WebTestCase.php');

/*
Uncomment this block to see trace log
function shutdown_yii_after_test() {
    Yii::app()->end();
}

register_shutdown_function('shutdown_yii_after_test');
*/
Yii::createWebApplication($config);
