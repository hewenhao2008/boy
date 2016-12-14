<?php

/**
 * 返回ajax数据
 *
 * @param bool $status
 * @param string $info
 * @param array $data
 */
function ajaxJSON($status=true,$info="OK",$data = array()){
    echo json_encode(array("status"=>$status,"msg"=>$info,"dict"=>$data));
    exit(0);
}

global $boxMac , $userMac , $ip;

$ip = $_SERVER['REMOTE_ADDR'];

//==获取当前盒子的MAC地址
$boxMac = exec("/wiware/bin/wiget boxmac");
if (empty($boxMac)) {
    ajaxJSON(false,"无法获取设备MAC");
}
//获取用户的MAC地址
$userMac = exec("/wiware/bin/wiget ipmac $ip");
if (empty($userMac)) {
    ajaxJSON(false,"无法获取用户MAC地址");
}

$allowAction = array(
    "gettime","gettimejsonp","lefttime"
);
$action = $_GET['action'];
if(!$action) ajaxJSON(false,"INVALID PARAMS");
if(!in_array($action,$allowAction)) ajaxJSON(false,"INVALID PARAMS");
call_user_func($action);

/**
 * 美化时间
 *
 * @param $allowTime
 * @return string
 */
function prettytime($allowTime){
    $echoStr = "";
    $hour = floor($allowTime/3600);
    $leftSeconds = $allowTime % 3600;
    $min = floor($leftSeconds/60);
    if($hour > 0 ){
        $echoStr .= $hour."小时";
    }
    if($min > 0){
        $echoStr .= $min."分钟";
    }
    if(empty($echoStr)){
        $echoStr = "0分钟";
    }
    return $echoStr;
}

/**
 * 获取上网时长
 *
 * @return bool|float|int
 */
function getAccessTime($time = false){
    global $ip;
    //==检测标记位,看是否与网络连通，这里会有两分钟盲区
    if(!file_exists("/tmp/neton")){
    }
    //==获取时长
    $seconds = exec("uci get wipark.conf.accesstime");
    if (!isset($seconds) || empty($seconds)) {
        $seconds = 60 * 60;
    }
    if($time !== false){
        $seconds = $time;
    }
    exec("/wiware/bin/netshell/ip_add.sh $ip $seconds", $output, $return_var);
    //获取时长失败
    if ($return_var == 0) {
        return $seconds;
    } else {
        return false;
    }
}

/**
 * 获取时长
 */
function getTime(){
    global $boxMac , $userMac , $ip;
    $time = isset($_GET['time']) ? $_GET['time'] : false;
    //强制重置
    $force = isset($_GET['force']) ? $_GET['force'] : "0";
    if(!file_exists("/wiware/bin/netshell/mac_timeleft.sh")){
        ajaxJSON(false,"left.sh file not exists");
    }
    $leftTime = exec("sh /wiware/bin/netshell/mac_timeleft.sh {$userMac}");

    if($leftTime > 0){
        //强制重置
        if($force == "1"){
            $time = getAccessTime($time);
            ajaxJSON(true,"ok",array("time"=>$time,"time_pretty"=>prettytime($time)));
        }else{
            ajaxJSON(true,"ok",array("time"=>$leftTime,"time_pretty"=>prettytime($leftTime)));
        }
    }else{
        $time = getAccessTime($time);
        ajaxJSON(true,"ok",array("time"=>$time,"time_pretty"=>prettytime($time)));
    }
}

/**
 * 检测当前用户的剩余时长
 */
function leftTime(){
    global $boxMac , $userMac , $ip;
    if(!file_exists("/wiware/bin/netshell/mac_timeleft.sh")){
        ajaxJSON(false,"left.sh file not exists");
    }
    $leftTime = exec("sh /wiware/bin/netshell/mac_timeleft.sh {$userMac}");
    ajaxJSON(true,"ok",array("time"=>$leftTime,"time_pretty"=>prettytime($leftTime)));
}

