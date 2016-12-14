<?php

if(is_dir("/wiware/")){
    $ip = $_SERVER['REMOTE_ADDR'];
    //==检测标记位,看是否与网络连通，这里会有两分钟盲区
    if(!file_exists("/tmp/neton")){
        die(json_encode(array("status"=>"error","msg"=>"你的盒子没有与网络连通")));
    }
    //==获取时长
    $seconds = exec("uci get wipark.conf.accesstime");
    if (!isset($seconds) || empty($seconds)) {
        $seconds = 60 * 60;
    }
    exec("/wiware/bin/netshell/ip_add.sh $ip $seconds", $output, $return_var);
    $allowTime = 0;
    //获取时长失败
    if ($return_var == 0) {
        $allowTime = intval($seconds) / 3600;
        die(json_encode(array("status"=>"ok","msg"=>"ok","time"=>$allowTime)));
    } else {
        die(json_encode(array("status"=>"error","msg"=>"0x001")));
    }
}else{
    die(json_encode(array("status"=>"error","msg"=>"你可能没有在盒子上访问这个页面")));
}