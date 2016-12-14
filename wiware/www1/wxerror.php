<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <title><?=$wpWSiteStore['wsite_title']?></title>
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/style.css">
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/owl.carousel.css"/>
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/owl.theme.css"/>
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/buttons.css"/>
    <style>
        body{
            /*background-color: #222;*/
            padding: 0;
            margin:0;
        }

        .wp-success-wrapper{
            padding-top:20px;
            background-color: #f4f4f4;
            color:#60d7a9;
        }

        .wp-success-wrapper .links{
            list-style: none;
            margin:0;
            text-align: left;
            padding: 0px;
            padding-top:10px;
            border:1px solid #60d7a9;
            background-color: #60d7a9;
            color:#FFF;
        }

        .wp-success-wrapper .links li{
            float: left;
            margin-left: 10px;
            height: 30px;
            width: 30%;
            line-height: 30px;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight:  bold;
        }
        .wp-success-wrapper .links li a{
            color:#FFF;
        }
    </style>
</head>
<body>
<div class="wp-success-wrapper container">
    <h4 class="text-center">获取时长失败</h4>
    <h5 class="text-danger text-center">错误原因：<?$_errorMsg?></h5>
    <hr>
    <a href="/" class="btn btn-success btn-lg">重新获取</a>
    <div class="text-center" style="opacity: .6;margin-top: 20px;">本服务由<?=$wpWSiteStore['wsite_copyright']?>提供</div>
</div>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/jquery.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/lib/jquery.form.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/lib/jquery.adaptive-backgrounds.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/bootstrap.min.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/lib/owl.carousel.min.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/lib/buttons.js"></script>
</body>
</html>



