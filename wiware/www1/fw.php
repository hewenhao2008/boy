<?php

session_start();

define("WP_SECRET_KEY","wwwwiparkcn");
define("WX_SECRET_KEY",md5("wwwwiparkcn"));

define("SMS_SEND_URL","http://www.wipark.cn/wp/public/index.php?/api/authsmssend/");
define("AUTHSMS_LOG_URL","http://www.wipark.cn/wp/public/index.php?/api/authsmslog/");

function ajaxJSON($status=true,$info="OK",$data = array()){
    echo json_encode(array("status"=>$status,"msg"=>$info,"user"=>$data));
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
    "smssend","smsauth","carouselinit","authcheck","wxauth"
);
$action = $_GET['action'];
if(!$action) ajaxJSON(false,"IVALID PARAMS");
if(!in_array($action,$allowAction)) ajaxJSON(false,"IVALID PARAMS");
call_user_func($action);


/**
 * 获取微站元数据
 */
function getWPSiteStore(){

    $emptyArr = array(
        //成功获取时长后跳转网址
        "wsite_success_redirecturl" => "",
        "wsite_copyright" => "",
        "wsite_sms_open" => "0",
        "wsite_wx_open" => "0",
        "wsite_wx_id" => "",
        //轮播图片
        "wsite_carousels" => array(
            "carousel_0" => array(),
            "carousel_1" => array(),
            "carousel_2" => array(),
            "carousel_3" => array()
        )
    );

    $file = "/wiware/www/contents/wsite_data.json";

    if(!file_exists($file)){
        return $emptyArr;
    }
    $json = file_get_contents($file);
    $arr = json_decode($json,true);
    if(empty($arr) || !is_array($arr)){
        return $emptyArr;
    }
    return $arr;
}


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
        die(json_encode(array("status"=>"error","msg"=>"你的盒子没有与网络连通")));
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
    $allowTime = 0;
    //获取时长失败
    if ($return_var == 0) {
//        $allowTime = intval($seconds) / 3600;
        return $seconds;
    } else {
        return false;
    }
}

/**
 * 发送短信
 */
function smsSend(){
    global $boxMac , $userMac , $ip;
    $phone = isset($_GET['phone']) ? $_GET['phone'] : false;
    if(false === $phone){
        ajaxJSON(false,"没有填写手机号码");
    }
    $validCode = md5($boxMac.WP_SECRET_KEY.$userMac.WP_SECRET_KEY.$ip.WP_SECRET_KEY.$phone);
    $params = base64_encode("{$validCode}|{$boxMac}|{$userMac}|{$ip}|{$phone}");
    $url = SMS_SEND_URL.$params;
    $code = exec("curl {$url}");
    if($code < 1000 || $code >9999){
        ajaxJson(false,"验证码获取失败,错误码：{$code}");
    }
    $_SESSION[$phone."_vcode"] = $code;
    ajaxJSON(true,"200",$code);
}


/**
 * 微信认证
 */
function wxAuth(){
    global $boxMac , $userMac , $ip;
    $metaArr = getWPSiteStore();
    //微站基础数据
    $wpWSiteStore = $metaArr;
    $W_BASE_URL = "http://w.wipark.cn";
    $runEnv = "_M_";

    $baseDir = dirname(__FILE__);
    $skey = isset($_GET['s']) ? $_GET['s'] : false;
    if(false === $skey){
        $_errorMsg = "非法的获取链接";
        require $baseDir."/wxerror.php";
        exit();
    }
    if($skey==WX_SECRET_KEY){
        $leftTime = exec("sh /wiware/bin/netshell/mac_timeleft.sh {$userMac}");
        if($leftTime >= 300){
            $time = "还剩余免费上网时长".prettytime($leftTime);
            require $baseDir."/wxsuccess.php";
            exit();
        }else{
            $allowTime = getAccessTime();
            if(false === $allowTime){
                $_errorMsg = "获取时长失败";
                require $baseDir."/wxerror.php";
                exit();
            }
            $validCode = md5($boxMac.WP_SECRET_KEY.$userMac.WP_SECRET_KEY.$ip);
            $params = base64_encode("{$validCode}|{$boxMac}|{$userMac}|{$ip}");
            $url = AUTHSMS_LOG_URL.$params;
            exec("curl {$url}");
            $time = "已取得免费上网时长".prettytime($allowTime);
            require $baseDir."/wxsuccess.php";
            exit();
        }
    }else{
        $_errorMsg = "校验失败";
        require $baseDir."/wxerror.php";
        exit();
    }
}

/**
 * 认证检测
 */
function authCheck(){
    global $boxMac , $userMac , $ip;
    if(!file_exists("/wiware/bin/netshell/mac_timeleft.sh")){
        ajaxJSON(false,"left.sh file not exists");
    }
    $leftTime = exec("sh /wiware/bin/netshell/mac_timeleft.sh {$userMac}");
    $metaArr = getWPSiteStore();
    $type = "directive";

    //剩余时长大于5分钟
    if($leftTime >= 300){
        $type = "left";
        ajaxJSON(true,"ok",array(
            "type" => "left",
            "time" => $leftTime,
            "pretty_time" => prettytime($leftTime)
        ));
    }else{
        //无认证上网方式
        if($metaArr['wsite_wx_open'] == '0' && $metaArr['wsite_sms_open'] == '0'){
            $allowTime = getAccessTime();
            if(false === $allowTime){
                ajaxJSON(false,"获取免费时长失败");
            }
            $validCode = md5($boxMac.WP_SECRET_KEY.$userMac.WP_SECRET_KEY.$ip);
            $params = base64_encode("{$validCode}|{$boxMac}|{$userMac}|{$ip}");
            $url = AUTHSMS_LOG_URL.$params;
            exec("curl {$url}");
            ajaxJSON(true,"ok",array(
                "type" => "directive",
                "time" => $allowTime,
                "pretty_time" => prettytime($allowTime)
            ));
        }else{
            //开启微信认证，则单独获取
            if($metaArr['wsite_wx_open'] == '1'){
                getAccessTime(5*60);
            }
            ajaxJSON(true,"ok",array(
                "type" => "auth"
            ));
        }
    }





}

/**
 * 短信认证上网
 */
function smsAuth(){
    global $boxMac , $userMac , $ip;
    $phone = isset($_POST['wp_phonenumber']) ? $_POST['wp_phonenumber'] : false;
    $code = isset($_POST['wp_phonecaptcha']) ? $_POST['wp_phonecaptcha'] : false;
    if(false === $phone || false === $code){
        ajaxJSON(false,"没有提交手机号码或验证码");
    }
    if(!isset($_SESSION[$phone."_vcode"])){
        ajaxJSON(false,"验证码已过期，请重新输入");
    }
    //校验通过
    if($_SESSION[$phone."_vcode"] == $code){
        $allowTime = getAccessTime();
        if(false === $allowTime){
            ajaxJSON(false,"获取免费时长失败");
        }
        $validCode = md5($boxMac.WP_SECRET_KEY.$userMac.WP_SECRET_KEY.$ip);
        $params = base64_encode("{$validCode}|{$boxMac}|{$userMac}|{$ip}");
        $url = AUTHSMS_LOG_URL.$params;
        exec("curl {$url}");
        ajaxJSON(true,"ok",array("time"=>$allowTime));
    }else{
        ajaxJSON(false,"验证码错误");
    }
}

/**
 * 检测上网剩余时长
 */
function carouselInit(){
    global $boxMac , $userMac , $ip;

    if(!file_exists("/wiware/bin/netshell/mac_timeleft.sh")){
        ajaxJSON(false,"file not exists");
    }

    $leftTime = exec("sh /wiware/bin/netshell/mac_timeleft.sh {$userMac}");

    $metaArr = getWPSiteStore();

    ajaxJSON(true,"200",array(
        "lefttime" => $leftTime,
        "lefttime_pretty"=>prettytime($leftTime)
    ));
}
