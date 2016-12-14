<?php
//get Client Mac
$ip = $_SERVER['REMOTE_ADDR'];
$userMac = exec("/wiware/bin/wiget ipmac $ip");
if (empty($userMac)) {
   die("HTTP 500");     
}

//AJAX
if(!isset($_GET['cb']) AND !isset($_GET['callback'])){
   echo json_encode(array("mac"=>$userMac));
   exit(0);
}

//JSONP
$cb = "";
if(isset($_GET['callback'])) $cb = $_GET['callback'];
if(isset($_GET['cb'])) $cb = $_GET['cb'];
$json = json_encode(array("mac"=>$userMac));
echo $cb."(".$json.")";
exit(0);

