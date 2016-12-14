<?php
define('FIRMWARE_UPGRADE_FILE', "/tmp/firmware.upgrade.bin");

$app->get(
    '/system',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('system/system.php');
    }
);

$app->get(
    '/sysinfo',
    $authenticateUser('user'),
    function () use ($app) {
        sysInfo();
    }
);

$app->get(
    '/upgrade',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('system/upgrade.php');
    }
);

$app->get(
    '/logread',
    $authenticateUser('user'),
    function () use ($app) {
        $syslog=shell_exec("logread|grep -v crond|sed 's/$/<br>/'");
        if( !isset($syslog)) $syslog="暂无日志信息";
        $app->render('system/logread.php', array('syslog'=>$syslog));
    }
);

$app->get(
    '/setprinter',
    $authenticateUser('user'),
    function () use ($app) {
        $hasprinter=exec("uci get wipark.conf.hasprinter || echo 0");
        $app->render('system/setprinter.php', array('hasprinter'=>$hasprinter));
    }
);

$app->get(
    '/printtest',
    $authenticateUser('user'),
    function () use ($app) {
        exec("xprint /wiware/admin/templates/system/printtestpage.txt", $output, $return_var);
        if( $return_var == 0 ){
            echo 'success';
        }
        else{
            echo 'failed';
        }
    }
);

$app->get(
    '/about',
    $authenticateUser('user'),
    function () use ($app) {
        aboutInfo();
    }
);


$app->get(
    '/rebooting',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('system/rebooting.php');
    }
);

//ajax route
$app->get(
    '/reboot',
    $authenticateUser('user'),
    function () use ($app) {
        exec("reboot");
	sleep(10);
    }
);

$app->post(
    '/actionSetPrinter',
    $authenticateUser('user'),
    function () use ($app) {
        $hasprinter=$app->request->post('hasprinter');
        if( $hasprinter == '0' ){
            exec("/wiware/bin/setprinter.sh off");
            $app->flashNow('info', '已经关闭了打印服务功能');
        }else{
            exec("/wiware/bin/setprinter.sh on");
            $app->flashNow('info', '已经开启了打印服务功能');
        }
        $app->render('flash.php',array('redirect'=>'system'));
    }
);

$app->post(
    '/uploadware',
    $authenticateUser('user'),
    function () use ($app) {
        if ($_FILES["file"]["type"] != "application/macbinary"
            && $_FILES["file"]["type"] != "application/octet-stream"
            || strrchr($_FILES["file"]["name"],".bin") !== ".bin"){
            unlink($_FILES["file"]["tmp_name"]);
            die(json_encode(array("status"=>"error","rsp"=>"上传文件格式错误")));
        }
        else if ($_FILES["file"]["size"] < 3*1024*1024){
            unlink($_FILES["file"]["tmp_name"]);
            die(json_encode(array("status"=>"error","rsp"=>"上传文件大小不正确")));
        }
        else if ($_FILES["file"]["size"] > 15*1024*1024){
            unlink($_FILES["file"]["tmp_name"]);
            die(json_encode(array("status"=>"error","rsp"=>"上传文件大小不正确")));
        }
        else if ($_FILES["file"]["error"] > 0){
            unlink($_FILES["file"]["tmp_name"]);
            die(json_encode(array("status"=>"error","rsp"=>"上传错误，错误码(".$_FILES["file"]["error"].")")));
        }
        else
        {
//            $firmwareName=$_FILES["file"]["name"];
            if(move_uploaded_file($_FILES["file"]["tmp_name"], FIRMWARE_UPGRADE_FILE)){
                $result = array(
                    "status" => "success",
                    "rsp" => "上传文件成功"
                );
                echo json_encode($result);
            }else{
                $result = array(
                    "status" => "error",
                    "rsp" => "上传文件错误"
                );
                echo json_encode($result);
            }
        }
    }
);

$app->get(
    '/writeware',
    $authenticateUser('user'),
    function () use ($app) {
        //需要检测，已经有在执行，则马上退出。
        $existed=exec("ps|grep sysupgrade|grep -v grep|wc -l");
        if($existed > 0){
            echo "正在升级";
            exit(0);
        }
        set_time_limit(0);// 取消脚本运行时间的超时上限
//        exec("cd /tmp/ && mtd write ".FIRMWARE_UPGRADE_FILE." firmware", $output, $return_var);
//        exec("sysupgrade -c -v -d 3 ".FIRMWARE_UPGRADE_FILE." &", $output, $return_var);
        exec("sysupgrade -b /tmp/sysupgrade.tgz >/dev/null 2>&1");
        exec("mtd -r write ".FIRMWARE_UPGRADE_FILE." -j /tmp/sysupgrade.tgz firmware &", $output, $return_var);
        if( $return_var == 0 ){
            echo "beginupgrade";
        }
        else{
            echo "升级失败";
        }
    }
);

function sysInfo(){
    $hardware=exec("/wiware/bin/wiget hardware");
    $kernelv=exec("uname -r");
    $wiwarev=exec("/wiware/bin/wiget versiontag");
    $cpuinfo="600MHz";
    $meminfo=exec("free|grep 'Mem:'|grep -v grep|awk '{free=$4/1000;total=$2/1000;printf(\"可用%dMB/总共%dMB\",free,total)}'");

    $diskinfo=exec("df -h|grep mmcblk0p1|grep -v grep|awk '{free=$4;total=$2;printf(\"可用%s/总共%s\",free,total)}'");
    if(!isset($diskinfo) || empty($diskinfo)){
        $diskinfo='无';
    }

    $sysData = array('hardware' => $hardware,'kernelversion'=> $kernelv, 'wiwareversion'=>$wiwarev,
        'cpuinfo'=>$cpuinfo, 'meminfo'=>$meminfo, 'diskinfo'=>$diskinfo);

    $app = \Slim\Slim::getInstance();
    $app->render('system/sysinfo.php', $sysData);
}

function aboutInfo(){
    $hardware=exec("/wiware/bin/wiget hardware");
    $kernelv=exec("uname -r");
    $wiwarev=exec("/wiware/bin/wiget versiontag");
    $varifycode=exec("/wiware/bin/wiget varifycode");
    $sysData = array('hardware' => $hardware,'kernelversion'=> $kernelv, 'wiwareversion'=>$wiwarev, 'wicode'=>$varifycode,);

    $app = \Slim\Slim::getInstance();
    $app->render('system/about.php', $sysData);
}

?>
