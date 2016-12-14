<?php

$app->get(
    '/accessctrl',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('accessctrl/accessctrl.php');
    }
);

$app->get(
    '/popwindow',
    $authenticateUser('user'),
    function () use ($app) {
        $popwindow = exec("uci get wipark.conf.popwindow");
        $app->render('accessctrl/popwindow.php',array('popwindow'=>$popwindow));
    }
);

$app->get(
    '/accesstime',
    $authenticateUser('user'),
    function () use ($app) {
        $accesstime=exec("uci get wipark.conf.accesstime");
        $accesstime /= 3600;
        $app->render('accessctrl/accesstime.php',array('accesstime'=>$accesstime));
    }
);

$app->get(
    '/getaccesstime',
    $authenticateUser('user'),
    function () use ($app) {
        $userip=$_SERVER["REMOTE_ADDR"];
        $seconds=exec("uci get wipark.conf.accesstime");
        if( !isset($seconds) || empty($seconds) ) {
            $seconds=24*3600;
        }
        exec("/wiware/bin/netshell/ip_add.sh $userip $seconds", $output, $return_var);
        if( $return_var == 0 ){
            $hours=$seconds/3600;
            $app->render('navipage.php', array('hours'=>$hours));
        }
        else{
            $app->flash('info', '获取时长失败，请重试');
            $app->redirect('accessctrl');
        }
    }
);

$app->get(
    '/whitelist(/:op/:mac)',
    $authenticateUser('user'),
    function ($op=false, $mac=false) use ($app) {
        if($op === false){
            $ip = $_SERVER['REMOTE_ADDR'];
            $mymac=shell_exec("wiget ipmac $ip");
            $app->render('accessctrl/whitelist.php', array('mymac'=>$mymac));
        }
        setWhitelist($op, $mac);
    }
);

$app->get(
    '/blacklist(/:op/:mac)',
    $authenticateUser('user'),
    function ($op=false, $mac=false) use ($app) {
        if($op === false){
            $app->render('accessctrl/blacklist.php');
        }
        setBlacklist($op, $mac);
    }
);

$app->post(
    '/actionSetPopwindow',
    $authenticateUser('user'),
    function () use ($app) {
        $popup=$app->request->post('popup');
        if( $popup == '0' ){
            shell_exec("/bin/sh /wiware/bin/setpopwindow.sh off");
            $app->flashNow('info', '成功关闭了WiPark自动弹窗功能');
        }else{
            shell_exec("/bin/sh /wiware/bin/setpopwindow.sh on");
            $app->flashNow('info', '成功开启了WiPark自动弹窗功能');
        }
        $app->render('flash.php',array('redirect'=>'accessctrl'));
    }
);

$app->post(
    '/actionSetAccesstime',
    $authenticateUser('user'),
    function () use ($app) {
        $accesstime=$app->request->post('accesstime');
        $app->flashNow('info', '设置上网时长失败');
        if(isset($accesstime)){
            $seconds=$accesstime * 3600;
            exec("uci set wipark.conf.accesstime=$seconds && uci commit",$output, $return_var);
            if( $return_var == 0 ){
                $app->flashNow('info', '设置上网时长成功');
            }
        }
        $app->render('flash.php',array('redirect'=>'accessctrl'));
    }
);


function setWhitelist($op, $mac) {
    if( $op == 'add'){
        if( isset($mac) && !empty($mac) ){
            exec("/wiware/bin/addwhitemac.sh $mac", $output, $return_var);
            if( $return_var == 0 ){
                return 0;
            }
            else {
                return 1;
            }
        }
    }else if($op == 'del'){
        if( isset($mac) && !empty($mac) ){
            exec("ls '/tmp/-.$mac.*'|sed 's/:/\\:/g'|xargs rm -f >/dev/null");
            exec("/wiware/bin/delwhitemac.sh $mac", $output, $return_var);
            if( $return_var == 0 ){
                return 0;
            }
            else {
                return 1;
            }
        }
    }
};

function setBlacklist($op, $mac) {
    if( $op == 'add'){
        if( isset($mac) && !empty($mac) ){
            exec("/wiware/bin/addblackmac.sh $mac", $output, $return_var);
            if( $return_var == 0 ){
                return 0;
            }
            else {
                return 1;
            }
        }
    }else if($op == 'del'){
        if( isset($mac) && !empty($mac) ){
            exec("/wiware/bin/delblackmac.sh $mac",$output, $return_var);
            if( $return_var == 0 ){
                return 0;
            }
            else {
                return 1;
            }
        }
    }
};

?>
