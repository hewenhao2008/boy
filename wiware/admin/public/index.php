<?php

$sessiondir = '/tmp/sessions';
if (!file_exists($sessiondir)) {
    mkdir($sessiondir);
}

session_save_path($sessiondir);
session_cache_limiter(false);
session_start();

ini_set('include_path', '.:../templates');

define("ADMIN_ROOT_PATH",dirname(dirname(__FILE__)));

require '../lib/Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
    'mode' => 'production'
));

$app->config(array(
    'debug' => false,
    'log.enable' => false,
    'templates.path' => ADMIN_ROOT_PATH.'/templates',
));


$oem = "WiPark";
if ( file_exists("/etc/OEM") ){
    $oem = file_get_contents("/etc/OEM");
    if(trim($oem) == "seocoo") $oem = "无线互联";
}

$app->view->setData('OEM', $oem);

//foreach(glob(ADMIN_ROOT_PATH . '/apps/*.php') as $model) {
//    require $model;
//}

require ADMIN_ROOT_PATH.'/apps/Auth.php';
require ADMIN_ROOT_PATH.'/apps/Network.php';
require ADMIN_ROOT_PATH.'/apps/Wireless.php';
require ADMIN_ROOT_PATH.'/apps/System.php';
require ADMIN_ROOT_PATH.'/apps/Accessctrl.php';
require ADMIN_ROOT_PATH.'/apps/FirmwareUpdate.php';
require ADMIN_ROOT_PATH.'/apps/WPCenter.php';
require ADMIN_ROOT_PATH.'/apps/WPSite.php';
require ADMIN_ROOT_PATH.'/apps/Weixin.php';
//require 'WPSite.php';

$app->notFound(function () use ($app) {
    $app->render('404.php');
});

$app->get(
    '/ping',
    function () {
        echo "pong";
    }
);

$app->run();
