<?php
define('CHECKUPDATE_SAVE_NAME', "firmware.json");
define('FIRMWARE_SAVE_NAME', "firmware.bin");
define('CHECKSUM_SAVE_NAME', "firmware.checksum");
define('PROGRESS_SAVE_NAME', "firmware.progress");
define('CHECKUPDATE_SAVE_FILE', "/tmp/".CHECKUPDATE_SAVE_NAME);
define('FIRMWARE_SAVE_FILE', "/tmp/".FIRMWARE_SAVE_NAME);
define('CHECKSUM_SAVE_FILE', "/tmp/".CHECKSUM_SAVE_NAME);
define('PROGRESS_SAVE_FILE', "/tmp/".PROGRESS_SAVE_NAME);

$app->get(
    '/firmware',
    $authenticateUser('user'),
    function () use ($app) {
        $osname=exec("/wiware/bin/wiget osname");
        $curversion=exec("/wiware/bin/wiget version");
        $app->render('firmware/firmware.php', array('osname'=>$osname, 'curversion'=>$curversion));
    }
);

$app->get(
    '/checkupdate',
    $authenticateUser('user'),
    function () use ($app) {
        checkFirmwareUpdate();
    }
);

$app->get(
    '/loadupdate',
    $authenticateUser('user'),
    function () use ($app) {
        loadFirmwareUpdate();
    }
);

$app->get(
    '/changelog(/:version)',
    $authenticateUser('user'),
    function ($version) use ($app) {
        getChangelog($version);
    }
);

//$app->get(
//    '/firmwareprogress',
//    $authenticateUser('user'),
//    function () use ($app) {
//        $progressfile="/tmp/".PROGRESS_SAVE_NAME;
//        $progress=exec("cat $progressfile");
//        echo $progress;
//    }
//);

$app->get(
    '/firmwarechecksum',
    $authenticateUser('user'),
    function () use ($app) {
        exec("cd /tmp/ && md5sum -cs ".CHECKSUM_SAVE_FILE, $output, $return_var);
        if( $return_var == 0 ){
            echo "checksumsuccess";
        }
        else{
            echo "校验失败";
            exec("rm ".CHECKSUM_SAVE_FILE);
            exec("rm ".FIRMWARE_SAVE_FILE);
            exec("rm ".PROGRESS_SAVE_FILE);
        }
    }
);

$app->get(
    '/firmwarewrite',
    $authenticateUser('user'),
    function () use ($app) {
        //检测已经有在执行，则马上退出。
        $existed=exec("ps|grep sysupgrade|grep -v grep|wc -l");
        if($existed > 0){
            echo "正在升级";
            exit(0);
        }
        set_time_limit(0);// 取消脚本运行时间的超时上限
//        exec("cd /tmp/ && mtd write ".FIRMWARE_SAVE_NAME." firmware", $output, $return_var);
//        exec("sysupgrade -c -v -d 3 ".FIRMWARE_SAVE_NAME." &", $output, $return_var);
        exec("sysupgrade -b /tmp/sysupgrade.tgz >/dev/null 2>&1");
        exec("mtd -r write ".FIRMWARE_SAVE_FILE." -j /tmp/sysupgrade.tgz firmware &", $output, $return_var);
        if( $return_var == 0 ){
            echo "beginupgrade";
        }
        else{
            echo "升级失败";
            exec("rm ".CHECKSUM_SAVE_FILE);
            exec("rm ".FIRMWARE_SAVE_FILE);
            exec("rm ".PROGRESS_SAVE_FILE);
        }
    }
);

$app->post(
    '/doupdate',
    $authenticateUser('user'),
    function () use ($app) {
        $downloadurl = $app->request->post('downloadurl');
        $checksum = $app->request->post('checksum');
        $newversion=$app->request->post('newversion');
        updateFirmware($downloadurl, $checksum, $newversion);
    }
);

$app->post(
    '/firmwaredownload',
    $authenticateUser('user'),
    function () use ($app) {
        $downloadurl = $app->request->post('downloadurl');
        $checksum = $app->request->post('checksum');
//        $newversion=$app->request->post('newversion');

        ignore_user_abort(true); // 后台运行
        set_time_limit(0); // 取消脚本运行时间的超时上限
        downloadFirmware($downloadurl, $checksum);
    }
);

//function checkFirmwareUpdate(){
//    $mac=exec("/wiware/bin/wiget idmac");
//    $hardware=exec("/wiware/bin/wiget hardware");
//    $curversion=exec("/wiware/bin/wiget version");
//    $checkupdateurl="http://www.wipark.cn/checkupdate.php?mac=";
//    $checkupdateurl .= urlencode($mac)."&hardware=".urlencode($hardware)."&curversion=".urlencode($curversion);
//    $ch = curl_init($checkupdateurl);
//    curl_setopt($ch, CURLOPT_HEADER, 0);
//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_USERAGENT, 'WiPark-Box');
//    curl_setopt($ch, CURLOPT_NOPROGRESS, true);
//    $json_ret=curl_exec($ch);
//
//    if (curl_errno($ch)) {
//        //        $info="检查更新失败了：".curl_error($ch);
//        $info="检查更新失败了";
//        curl_close($ch);
//    }
//    else{
//        curl_close($ch);
//        $arr = json_decode($json_ret, true);
//        if ($arr){
//
//            $file = fopen(CHECKUPDATE_SAVE_FILE,"w");
//            fprintf($file,"%s",$json_ret);
//            fclose($file);
//            if(!isset($newversion)) $newversion = '1.00';
//            if($curversion < $arr['newversion']){
//                $app = \Slim\Slim::getInstance();
//                if(!isset($arr['downloadurl'])) $arr['downloadurl']="";
//                if(!isset($arr['checksum'])) $arr['checksum']="";
//                if(!isset($arr['osname'])) $arr['osname']="WIWARE";
//                if(!isset($arr['releasetime'])) $arr['releasetime']="";
//                $app->render('firmware/checkupdate.php', $arr);
//            }else{
//                $info="已经是最新版本了";
//            }
//        }
//        else{
//            $info="未发现新的版本";
//        }
//    }
//
//    if(isset($info)){
//        echo "<div style='text-align:center;color:#ff0000'>$info</div>";
//    }
//
//}

function checkFirmwareUpdate(){
    exec("/wiware/bin/checkupdate.sh");
    loadFirmwareUpdate();
}

function loadFirmwareUpdate(){
    if( !file_exists(CHECKUPDATE_SAVE_FILE)){
        echo "<div style='text-align:center;color:#ff0000'>请先检查更新</div>";
        exit(0);
    }

    $json_string = file_get_contents(CHECKUPDATE_SAVE_FILE);
    $arr = json_decode($json_string, true);

    if ( isset($arr) && isset($arr['downloadurl']) && isset ($arr['checksum']) ){
        $curversion=exec("/wiware/bin/wiget version");
        if($curversion < $arr['newversion']){
            if( ! isset($arr['osname']) ) $arr['osname'] = 'WIWARE';
            $app = \Slim\Slim::getInstance();
            $app->render('firmware/checkupdate.php', $arr);
        }else{
            $info="已经是最新版本了";
        }
    }
    else{
        $info="未发现新的版本";
    }

    if(isset($info)){
        echo "<div style='text-align:center;color:#ff0000'>$info</div>";
    }
}

function updateFirmware($downloadurl, $checksum, $newversion){
    $app = \Slim\Slim::getInstance();
    $app->render('firmware/updatestatus.php',
        array('downloadurl'=>$downloadurl, 'checksum'=>$checksum, 'newversion'=>$newversion));
}

function getChangelog($version){
    $changelogurl="http://www.wipark.cn/changelog.php?version=$version";
    $ch = curl_init($changelogurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'WiPark-Box');
    curl_setopt($ch, CURLOPT_NOPROGRESS, true);
    $changlog=curl_exec($ch);

    if (curl_errno($ch)) {
        curl_error($ch);
    }
    curl_close($ch);

    echo "$changlog";
}

function curl_progress_callback($dltotal, $dlnow, $uptotal, $upnow){
    //Return a non-zero value to abort the transfer. In which case, the transfer will set a CURLE_ABORTED_BY_CALLBACK error.
    if($dltotal > 0){
        $file = fopen(PROGRESS_SAVE_FILE,"w");
        fprintf($file,"%d",($dlnow/$dltotal)*100);
        fclose($file);
    }
}

function downloadFirmware($downloadurl, $checksum){
    if (file_exists(CHECKSUM_SAVE_FILE)){
        //说明有正在下载固件的php进程在
        return;
    }

    $fchecksum = fopen(CHECKSUM_SAVE_FILE,"w");
    fprintf($fchecksum,"%s  %s", $checksum, FIRMWARE_SAVE_NAME);
    fclose($fchecksum);

    $fp = fopen(FIRMWARE_SAVE_FILE,"w");
    $ch = curl_init($downloadurl);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'WiPark-Box');
    curl_setopt($ch, CURLOPT_NOPROGRESS, false);
    curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'curl_progress_callback');
    curl_exec($ch);

    $fprogress = fopen(PROGRESS_SAVE_FILE,"w");
    if (curl_errno($ch)) {
//        echo "下载出错：".curl_error($ch);
        fprintf($fprogress,"downloadfailed");
        exec("rm ".CHECKSUM_SAVE_FILE);
        exec("rm ".FIRMWARE_SAVE_FILE);
        exec("rm ".PROGRESS_SAVE_FILE);
    }
    else{
        fprintf($fprogress,"downloadsuccess");
    }
    fclose($fp);
    curl_close($ch);
    fclose($fprogress);
}

function hasNewFirmware(){
    if( ! file_exists(CHECKUPDATE_SAVE_FILE)){
        return false;
    }
    $json_string = file_get_contents(CHECKUPDATE_SAVE_FILE);
    $arr = json_decode($json_string, true);
    if ($arr){
        $curversion=exec("/wiware/bin/wiget version");
        if($curversion < $arr['newversion']){
            return $arr['newversion'];
        }else{
            return false;
        }
    }
}

?>
