<?php
$req_started_at = microtime(true);
defined('YII_DEBUG') or define('YII_DEBUG',true);

$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();

