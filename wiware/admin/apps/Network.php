<?php
$app->get(
    '/flashing',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('flash.php');
    }
);

$app->get(
    '/internet',
    $authenticateUser('user'),
    function () use ($app) {
        $has4gmodule=0;
        if( file_exists("/dev/ttyUSB0") || file_exists("/dev/ttyACM0") ) $has4gmodule=1;

        $app->render('network/internet.php', array('has4gmodule'=>$has4gmodule));
    }
);

$app->get(
    '/lannet',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/lannet.php');
    }
);
$app->get(
    '/lan',
    $authenticateUser('user'),
    function () use ($app) {
        $ip=exec("uci get network.lan.ipaddr");
        $netmask=exec("uci get network.lan.netmask 2>/dev/null");
        if(empty($netmask)) $netmask='255.255.255.0';
        $app->render('network/setlan.php',array('ip'=>$ip, 'netmask'=>$netmask));
    }
);
$app->get(
    '/wlan',
    $authenticateUser('user'),
    function () use ($app) {
        $ip=exec("uci get network.wlan.ipaddr");
        $netmask=exec("uci get network.wlan.netmask  2>/dev/null");
        if(empty($netmask)) $netmask='255.255.255.0';
        $app->render('network/setwlan.php',array('ip'=>$ip, 'netmask'=>$netmask));
    }
);

$app->get(
    '/setdhcp',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/setdhcp.php');
    }
);

$app->get(
    '/set4g',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/set4g.php');
    }
);

$app->get(
    '/setpppoe',
    $authenticateUser('user'),
    function () use ($app) {
        $username=exec("uci get network.wan.username");
        $password=exec("uci get network.wan.password");
        $app->render('network/setpppoe.php', array('username'=>$username, 'password'=>$password));
    }
);

$app->get(
    '/setstatic',
    $authenticateUser('user'),
    function () use ($app) {
        $ip=exec("uci get network.wan.ipaddr");
        $netmask=exec("uci get network.wan.netmask");
        $gateway=exec("uci get network.wan.gateway");
        $dns=exec("uci get network.wan.dns");
        $app->render('network/setstatic.php', array('ip'=>$ip, 'netmask'=>$netmask, 'gateway'=>$gateway, 'dns'=>$dns));
    }
);


$app->get(
    '/scanwifi',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/scanresult.php');
    }
);

$app->get(
    '/setwds',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/setwds.php');
    }
);

$app->get(
    '/status',
    $authenticateUser('user'),
    function () use ($app) {
        runningStatus();
    }
);

$app->get(
    '/dhcpleases',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/dhcpleases.php');
    }
);

$app->get(
    '/networkrestarting',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('network/networkrestarting.php');
    }
);

$app->get(
    '/bandwidth',
    $authenticateUser('user'),
    function () use ($app) {
        $bandwidth=exec("uci get wsqos.conf.bandwidth");
        $shared=exec("uci get wsqos.conf.shared");
        $maxclients=exec("apguy -m");
        $app->render('network/bandwidth.php', array('bandwidth'=>$bandwidth, 'shared'=>$shared, 'maxclients'=>$maxclients));
    }
);

$app->get(
    '/inmode',
    $authenticateUser('user'),
    function () use ($app) {
        $inmode=exec("uci get wipark.conf.inmode");
        $app->render('network/setinmode.php', array('inmode'=>$inmode));
    }
);

//ajax route

$app->get(
    '/networkreload',
    $authenticateUser('user'),
    function () use ($app) {
        //exec("/etc/init.d/network reload");
        exec("/etc/init.d/network restart");
    }
);

//post route
$app->post(
    '/actionDhcp',
    $authenticateUser('user'),
    function () use ($app) {
        $dns = $app->request->post('dns');
        setDhcp($dns);
    }
);


$app->post(
    '/actionSet4g',
    $authenticateUser('user'),
    function () use ($app) {
        set4G();
    }
);
$app->post(
    '/actionPPPoE',
    $authenticateUser('user'),
    function () use ($app) {
        $username = $app->request->post('username');
        $password = $app->request->post('password');
        setPPPoE($username, $password);
    }
);

$app->post(
    '/actionStatic',
    $authenticateUser('user'),
    function () use ($app) {
        $ip = $app->request->post('ip');
        $netmask = $app->request->post('netmask');
        $gateway = $app->request->post('gateway');
        $dns = $app->request->post('dns');
        setStatic($ip, $gateway, $netmask, $dns);
    }
);

$app->post(
    '/actionBandCtrl',
    $authenticateUser('user'),
    function () use ($app) {
        $bandwidth = $app->request->post('bandwidth');
        $shared = $app->request->post('shared');
        $maxclients = $app->request->post('maxclients');
        setBandCtrl($bandwidth, $shared, $maxclients);
    }
);

$app->post(
    '/actionSetInmode',
    $authenticateUser('user'),
    function () use ($app) {
        $inmode = $app->request->post('inmode');
        setInmode($inmode);
    }
);
$app->post(
    '/actionWds',
    $authenticateUser('user'),
    function () use ($app) {
        $ssid = $app->request->post('ssid');
        $bssid = $app->request->post('bssid');
        $channel = $app->request->post('channel');
        $auth=$app->request->post('auth');
        $key = $app->request->post('key');

//        $authmode=$app->request->post('authmode');
//        $encryptype=$app->request->post('encryptype');
//        if($auth == "NONE"){
//            $encryptype = "NONE";
//            $key="none";
//        }

        if(!isset($key)) $key = "none";
        setWds($ssid, $bssid, $channel, $auth, $key);
//        setWds($ssid, $bssid, $channel, $authmode, $encryptype, $key);
    }
);

$app->post(
    '/actionLan',
    $authenticateUser('user'),
    function () use ($app) {
        $boxip = $app->request->post('ip');
        $netmask = $app->request->post('netmask');
        setLan($boxip, $netmask);
    }
);
$app->post(
    '/actionWlan',
    $authenticateUser('user'),
    function () use ($app) {
        $boxip = $app->request->post('ip');
        $netmask = $app->request->post('netmask');
        setWlan($boxip, $netmask);
    }
);

function getNettypeDesc($nettype){
    $desc = '动态IP';
    if($nettype == 'dhcp'){
        $desc = '动态IP';
    }
    else if($nettype == 'pppoe'){
        $desc = '宽带拨号';
    }else if($nettype == 'sta'){
        $desc = '无线中继';
    }else if($nettype == 'static'){
        $desc = '静态IP';
    }else if( $nettype == '3g'){
        $desc = '数据卡拨号';
    }else{
        $desc = $desc.'?';
    }
    return $desc;
}

function getEncryptionDesc($encryption){
    $desc = '开放';
    if( $encryption == 'psk+psk2' ){
        $desc = 'WPA/WPA2个人版';
    }
    else if( $encryption == 'psk2' ){
        $desc = 'WPA2个人版';
    }
    else if( $encryption == 'psk' ){
        $desc = 'WPA个人版';
    }
    else if( $encryption == 'wep' ){
        $desc = 'WEP';
    }
    return $desc;
}

function setDhcp($dns){
    exec("/wiware/bin/setdhcp.sh $dns");
    $app = \Slim\Slim::getInstance();
    $app->flash('info', '连网方式已设置为DHCP');
    $app->redirect('networkrestarting');
}

function set4G(){
    exec("/wiware/bin/set4g.sh");
    $app = \Slim\Slim::getInstance();
    $app->flash('info', '连网方式已设置为4G数据网');
    $app->redirect('networkrestarting');
}

function setPPPoE($username, $password){
    exec("/wiware/bin/setpppoe.sh \"$username\" \"$password\"");
    $app = \Slim\Slim::getInstance();
    $app->flash('info', '连网方式已设置为PPPoE');
    $app->redirect('networkrestarting');
}

function setStatic($ip, $gateway, $netmask, $dns){
    exec("/wiware/bin/setstatic.sh $ip $gateway $netmask  $dns");
    $app = \Slim\Slim::getInstance();
    $app->flash('info', '连网方式已设置为静态IP');
    $app->redirect('networkrestarting');
}

function setWds($ssid, $bssid, $channel, $auth, $key) {

    if( empty($auth) ){
        $auth = 'NONE';
    }
    exec("/wiware/bin/setwds.sh \"$ssid\" $bssid $channel $auth \"$key\"", $output, $return_var);

    $app = \Slim\Slim::getInstance();
    if( $return_var  == 0 ){
        $app->flash('info', '连网方式已设置为中继');
        $app->redirect('networkrestarting');
    }
    else {
        $app->flash('error', '中继参数错误，请重新选择和设置');
        $app->redirect('setwds');
    }

}

function setLan($boxip, $netmask) {
    if( empty($boxip)) $boxip='10.10.10.1';
    if( empty($netmask)) $netmask='255.255.255.0';

    exec("/wiware/bin/setlan.sh $boxip $netmask", $output, $return_var);

    $app = \Slim\Slim::getInstance();
    if( $return_var  == 0 ){
        $app->flash('info', 'LAN设置保存成功');
        $app->redirect('networkrestarting');
    }
    else {
        $app->flash('error', 'LAN设置保存失败');
        $app->redirect('lan');
    }
}

function setWlan($boxip, $netmask) {
    if( empty($boxip)) $boxip='10.10.10.1';
    if( empty($netmask)) $netmask='255.255.255.0';

    exec("/wiware/bin/setwlan.sh $boxip $netmask", $output, $return_var);

    $app = \Slim\Slim::getInstance();
    if( $return_var  == 0 ){
        $app->flash('info', 'WLAN设置保存成功');
        $app->redirect('networkrestarting');
    }
    else {
        $app->flash('error', 'WLAN设置保存失败');
        $app->redirect('wlan');
    }
}

function setBandCtrl($bandwidth, $shared, $maxclients){
    if(!isset($bandwidth)) $bandwidth = 30;
    if(!isset($shared)) $shared = 1;
    if(!isset($maxclients)) $maxclients = 32;

    exec("/wiware/bin/setbandctrl.sh $bandwidth $shared $maxclients >/dev/null");
    $app = \Slim\Slim::getInstance();
    $app->flashNow('info', '带宽控制设置已成功');
    $app->render('flash.php');
}

function setInmode($inmode){
    if(!isset($inmode)) $inmode = 1;

    exec("/wiware/bin/setinmode.sh $inmode >/dev/null");
    $app = \Slim\Slim::getInstance();
    $app->redirect('status');
}

function runningStatus () {
    $uptime=shell_exec("cat /proc/uptime | awk -F. '{run_days=$1 / 86400;run_hour=($1 % 86400)/3600;run_minute=($1 % 3600)/60;run_second=$1 % 60;printf(\"%d天%d小时%d分%d 秒\",run_days,run_hour,run_minute,run_second)}'");
    $onlineUser=shell_exec("apguy -g |wc -l");

    $nettype=exec("/wiware/bin/wiget nettype");
    $wannet="0.0.0.0/255.255.255.0";
    $desc="动态IP（DHCP）";
    if( $nettype == "dhcp" ){
        $wannet=shell_exec("ifconfig eth2.2|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
        $desc="动态IP（DHCP）";
    }
    else if( $nettype == "pppoe" ){
        $wannet=shell_exec("ifconfig eth2.2|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
        $desc="宽带拨号（PPPoE）";
    }
    else if( $nettype == "4g" ){
        $wannet=shell_exec("ifconfig br-3g|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
        $desc="4G数据网";
    }
    else if( $nettype == "static" ){
        $wannet=shell_exec("ifconfig eth2.2|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
        $desc="静态IP";
    }
    else if( $nettype == "sta" ){
        $wannet=shell_exec("ifconfig apcli0|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
        $desc="无线中继";
    }
    else{
        $desc="$nettype";
    }

    $wanmac=exec("/wiware/bin/wiget boxmac");
    $inmode=exec("/wiware/bin/wiget inmode");
    $gateway=exec("route|grep default|awk '{print $2}'");

    $lannet=shell_exec("ifconfig br-lan|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
    $lanmac=shell_exec("ifconfig br-lan |grep HWaddr|awk '{print $5}'|tr [a-z] [A-Z]");
    $wlannet=shell_exec("ifconfig br-wlan|grep 'inet addr'|awk '{print $2\":\"$4}'|awk -F':' '{print $2\"/\"$4}'");
    $wlanmac=shell_exec("ifconfig br-wlan |grep HWaddr|awk '{print $5}'|tr [a-z] [A-Z]");

    $ssid=exec("uci get wireless.guest.ssid");
    $encryption=exec("uci get wireless.guest.encryption 2>/dev/null || echo 'none'");
    $encryption=getEncryptionDesc($encryption);

    $officeenabled=exec("uci get wireless.office 2>/dev/null|| echo 0");
    $officessid=exec("uci get wireless.office.ssid");
    $officeencryption=exec("uci get wireless.office.encryption 2>/dev/null || echo 'none'");
    $officeencryption=getEncryptionDesc($officeencryption);
    $working=shell_exec("uci get wireless.guest||echo '0'");
    $wirelessWorking="无线信号工作中";
    if($working == 0) $wirelessWorking="无线信号关闭";

    $statusData = array('uptime'=>$uptime, 'nettype'=>$desc, 'inmode'=>$inmode,
        'online'=>$onlineUser, 'wannet'=>$wannet,'wanmac'=>$wanmac, 'gateway'=>$gateway,
        'lannet'=>$lannet, 'lanmac'=>$lanmac, 'wlannet'=>$wlannet, 'wlanmac'=>$wlanmac,
        'ssid'=>$ssid, 'encryption'=>$encryption,'working'=>$wirelessWorking,
        'officeenabled'=>$officeenabled, 'officessid'=>$officessid, 'officeencryption'=>$officeencryption);

    $app = \Slim\Slim::getInstance();
    $app->render('network/status.php', $statusData);
};

?>
