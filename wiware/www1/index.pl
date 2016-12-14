<?php
	$request_domain=$_SERVER['HTTP_HOST'];
	$domain = $request_domain;
	if( file_exists("/wiware/bin/wiget") ){
		$domain=exec("/sbin/uci get wipark.conf.domain 2>/dev/null");
		if($domain != $request_domain){
			$domain=exec("/sbin/uci get wipark.conf.domain_alies 2>/dev/null");
		}
		if(!isset($domain) || empty($domain)){
			$domain="m.wipark.cn";
        	}
	}
        
        $config_obj = null;
        //$jsonfile="/wiware/www/config.json";
        $jsonfile=__DIR__."/config.json";
 	if(file_exists($jsonfile)){
 		$config_data = file_get_contents( $jsonfile );
		$config_obj = json_decode($config_data);
	}
	
	if(null == $config_obj){
		$config_obj = new stdClass;
		$config_obj->index = "wp";
	}
	
	$target = "http://$domain/".$config_obj->index;
	if( stristr($config_obj->index, "http://") != false ){
		$target = $config_obj->index;
	}
	
	Header("Expires: Mon, 26 Jul 1990 08:00:00 GMT");
	Header("Cache-Control: no-stroe,no-cache,must-revalidate,post-check=0,pre-check=0");
	Header("Pragma: no-cache");
	Header("Location: $target");
?>

