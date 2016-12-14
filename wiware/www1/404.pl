<?php
	function captiveIos( $ua ){
		//ua like "CaptiveNetworkSupport-277.10.5 wispr"
		if(stripos($ua, "CaptiveNetworkSupport") !== false){
			return true;
		}
		return false;
	}
	function magic( $mac ){
		$fset = "/tmp/captive.$mac";
		$str = file_get_contents($fset);
		$str = $str + 1;
		file_put_contents($fset, "$str", LOCK_EX);
		return $str;
	}
	
	$success = '<HTML><HEAD><TITLE>Success</TITLE></HEAD><BODY>Success</BODY></HTML>';
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$iosdomain = array("www.thinkdifferent.us","www.airport.us","www.apple.com",
		"www.ibook.info","www.itools.info","captive.apple.com","www.appleiphonecell.com");
	$ip = $_SERVER['REMOTE_ADDR'];
	$client_mac = exec("/wiware/bin/wiget ipmac $ip");
	
	$request_domain=$_SERVER['HTTP_HOST'];
	if( captiveIos($useragent) || in_array($request_domain, $iosdomain) ){
		$times = magic($client_mac);
		if( $times == 2){
			Header("Location: /index.pl");
			exit(0);
		}
		else if( $times >= 3 ){
			echo $success;
		}
	}
	
	$domain=exec("/sbin/uci get wipark.conf.domain 2>/dev/null");
	if(empty($domain)){
		$domain="m.wipark.cn";
	}
	Header("Location: http://".$domain."/index.pl");
	
?>
