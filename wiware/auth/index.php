<?php include('../auth/encrypt.php');

$ip = $_SERVER['REMOTE_ADDR'];
$client_mac=trim(shell_exec("wiget ipmac $ip"));
$box_mac=trim(shell_exec("wiget idmac"));
$ssid=trim(exec("/sbin/uci get wireless.guest.ssid 2>/dev/null"));

$text = "{\"boxMac\":\"$box_mac\",\"clientMac\":\"$client_mac\", \"ssid\":\"$ssid\"}";
$encrypted_text = encrypt($text, 'E');
$encoded = urlencode($encrypted_text);

header("Location: http://m.wiparkrun.cn/?auth=$encoded");

?>
