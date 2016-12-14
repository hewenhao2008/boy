<?php
//weixin 扫码连wifi

$app->get(
    '/wxlogon',
    function () use ($app) {
        $client_ip = $app->request->headers->get('REMOTE_ADDR');
        $seconds=exec("uci get wipark.conf.accesstime 2>/dev/null");
        if( !isset($seconds) || empty($seconds) ) {
            $seconds=3600;
        }

        exec("/wiware/bin/netshell/ip_add.sh $client_ip $seconds", $output, $return_var);

        $res="success"; //already, or else failed
        if( $return_var == 0 ){
            $res = 'success';
        }
        else {
            $res = 'failed';
        }
        $client_mac=trim(shell_exec("ebget ipmac $client_ip"));

        //TODO: inform server logon success
        $server_url="";
        //$server_url="http://wxwifi.neoap.com/weixin/apNoticeInfo?login_state=$login_state&client_mac=$client_mac";
//        $ctx = stream_context_create(array( 'http' => array( 'timeout' => 5 ) ) );
//        file_get_contents("$server_url", 0, $ctx);
//        header("Location: http://www.wainguo.cn/?res=$res");
        $app->redirect("http://www.wiparkrun.cn/?res=$res");

    }
);

$app->get(
    '/wxlogout',
    function () use ($app) {

    }
);

?>