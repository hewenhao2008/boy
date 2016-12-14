<?php

$app->get(
    '/wireless',
    $authenticateUser('user'),
    function () use ($app) {
        $channel = exec("uci get wireless.office.channel");
        $officeenabled = exec("uci get wireless.office || echo 0");
        $guestenabled = exec("uci get wireless.guest || echo 0");
        $guestssid = exec("uci get wireless.guest.ssid");
        if(!isset($guestssid) || empty($guestssid)){
            $apprefix=exec("uci get wipark.conf.ssid_prefix 2>/dev/null");
            $apname=exec("uci get wipark.conf.ssid_name 2>/dev/null");
            $guestssid="$apprefix$apname";
        }

        $officessid = exec("uci get wireless.office.ssid");
        if(!isset($officessid) || empty($officessid)){
            $officessid="WIPARK-OFFICE";
        }
        $wirelessData = array('channel' => $channel,
            'officessid' => $officessid, 'officeenabled'=>$officeenabled,
            'guestssid'=>$guestssid, 'guestenabled'=>$guestenabled);
        $app->render('wireless/wireless.php', $wirelessData);
    }
);

$app->get(
    '/wirelessoffice',
    $authenticateUser('user'),
    function () use ($app) {
        $enabled = exec("uci get wireless.office 2>/dev/null || echo 0");
        if($enabled != '0') $enabled = '1';

        $ssid = exec("uci get wireless.office.ssid");
        if(!isset($ssid) || empty($ssid)){
            $ssid="WiFi-Office";
        }
        $encryption = exec("uci get wireless.office.encryption");
        $key = exec("uci get wireless.office.key");
        if ( $key == "none" ) $key="";
        $channel = exec("uci get wireless.@wifi-device[0].channel");
        $nettype = exec("uci get wipark.conf.nettype");

        $wirelessData = array('ssid' => $ssid, 'encryption' => $encryption, 'key' => $key, 'enabled'=>$enabled,
            'channel' => $channel, 'nettype' => $nettype);
        $app->render('wireless/wirelessoffice.php', $wirelessData);
    }
);

$app->get(
    '/wirelessguest',
    $authenticateUser('user'),
    function () use ($app) {
        $apprefix = exec("uci get wipark.conf.ssid_prefix");
        $apname = exec("uci get wipark.conf.ssid_name");
        $randssid = exec("uci get wipark.conf.randssid");
        $mactail = exec("wiget idmac|awk '{print substr($1,9,4)}'");
        $fullapname = ($randssid) ? "$apname-$mactail" : $apname;

        $enabled = exec("uci get wireless.guest 2>/dev/null|| echo 0");
        if($enabled != '0') $enabled = '1';
        $encryption = exec("uci get wireless.guest.encryption");
        $key = exec("uci get wireless.guest.key");
        if ( $key == "none" ) $key="";

        $channel = exec("uci get wireless.@wifi-device[0].channel");
        $nettype = exec("uci get wipark.conf.nettype");

        $wirelessData = array('apprefix' => $apprefix, 'apname' => $fullapname, 'randssid' => $randssid,
            'encryption' => $encryption, 'key' => $key, 'enabled'=>$enabled, 'channel' => $channel, 'nettype' => $nettype);
        $app->render('wireless/wirelessguest.php', $wirelessData);
    }
);

$app->get(
    '/setchannel',
    $authenticateUser('user'),
    function () use ($app) {
        $nettype = exec("uci get wipark.conf.nettype");
        $channel = exec("uci get wireless.@wifi-device[0].channel");
        $ht = exec("uci get wireless.@wifi-device[0].ht");
        $app->render('wireless/channel.php', array('channel'=>$channel, 'ht'=>$ht, 'nettype'=>$nettype));
    }
);

$app->get(
    '/wifirestarting',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('wireless/wifirestarting.php');
    }
);

$app->get(
    '/wifirestart',
    $authenticateUser('user'),
    function () use ($app) {
        //exec("/etc/init.d/network reload");
        exec("/sbin/wifi restart && /sbin/wifi restart");
    }
);

$app->post(
    '/actionSetChannel',
    $authenticateUser('user'),
    function () use ($app) {
        $channel = $app->request->post('channel');
        $ht = $app->request->post('ht');
        if( !isset($channel) ){
            $channel = exec("uci get wireless.@wifi-device[0].channel");
        }
        if( !isset($ht) ){
            $ht = exec("uci get wireless.@wifi-device[0].ht");
        }
        exec("/wiware/bin/setchannel.sh $channel $ht");
        $app->flash('info', '信道设置成功');
        $app->redirect('setchannel');
    }
);

$app->post(
    '/actionWoffice',
    $authenticateUser('user'),
    function () use ($app) {
        $enabled = $app->request->post('enabled');
        if( !isset($enabled)) $enabled = 0;
        $channel = $app->request->post('channel');
        if( !isset($channel) ){
            $channel = exec("uci get wireless.@wifi-device[0].channel");
        }
        $apname = $app->request->post('ssid');
        if( !isset($apname) ) $apname=exec("uci get wireless.office.ssid");
        $encryption = $app->request->post('encryption');
        if( !isset($encryption) ) $encryption = 'none';
        $key = $app->request->post('key');
        if( !isset($key) ) $key = 'none';

        setWirelessOffice($enabled, $apname, $channel, $encryption, $key);
    }
);

$app->post(
    '/actionWguest',
    $authenticateUser('user'),
    function () use ($app) {
        $enabled=1;
        $apprefix = $app->request->post('apprefix');
        if( !isset($apprefix) ) $apprefix=exec("uci get wipark.conf.ssid_prefix");

        $apname = $app->request->post('apname');
        if( !isset($apname) ) $apname=exec("uci get wipark.conf.ssid_name");

        $channel = $app->request->post('channel');
        if( !isset($channel) ){
            $channel = exec("uci get wireless.@wifi-device[0].channel");
        }

        $encryption = $app->request->post('encryption');
        if( !isset($encryption) ) $encryption = 'none';

        $key = $app->request->post('key');
        if( !isset($key) ) $key = 'none';
        setWirelessGuest($enabled, $apprefix, $apname, $channel, $encryption, $key);
    }
);

function setWirelessOffice($enabled, $apname, $channel, $encryption, $key) {
    exec("/wiware/bin/setwireless.sh office $enabled noprefix \"$apname\" $encryption $key $channel");
    $app = \Slim\Slim::getInstance();
    $app->flash('info', '无线设置成功');
    $app->redirect('wifirestarting'); //from encryption to none, only netrestart not ok.
//    $app->redirect('rebooting');
};

function setWirelessGuest($enabled, $apprefix, $apname, $channel, $encryption, $key) {
    exec("/wiware/bin/setwireless.sh guest $enabled \"$apprefix\" \"$apname\" $encryption $key $channel");
    $app = \Slim\Slim::getInstance();
    $app->flash('info', '无线设置成功');
    $app->redirect('wifirestarting');
//    $app->redirect('rebooting');
}
