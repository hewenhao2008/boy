<?php
    $carouselCount = 0;
    foreach($wpWSiteStore['wsite_carousels'] as $carousel){
        if(!empty($carousel)) $carouselCount++;
    }
?>
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
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/style.css">
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/owl.carousel.css"/>
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/owl.theme.css"/>
    <link rel="stylesheet" href="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/css/buttons.css"/>
    <style>
        body{
            background-color: #222;
            padding: 0;
            margin:0;
        }
        .wp-carousel-wrapper{
            margin: 0 auto;
        }

        .wp-success-wrapper{
            padding-top:20px;
            display: none;
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

        .wp-carousel-wrapper .wp-carousel-widget{
            height: 100%;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-card{
            display: table;
            width: 100%;
            /*overflow: hidden;*/
            position: relative;
        }


        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-intro{
            background-color: #f4f4f4;
            color:#60d7a9;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-gat{
            background-color: #f4f4f4;
            color:#60d7a9;
        }


        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-gat .copyright{
            position: absolute;
            bottom:50px;
            width: 100%;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-gat .main{
            width: 100%;
            position: absolute;
            top:25%;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-intro .copyright{
            position: absolute;
            bottom:50px;
            width: 100%;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-intro .main{
            width: 100%;
            position: absolute;
            top:25%;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-card .img-wrapper{
            display: table-cell;
            text-align: center;
            vertical-align: middle;
        }

        .wp-carousel-wrapper .wp-carousel-widget .wp-carousel-card img{
            max-width: 720px;
            margin:0 auto;
            width: 100%;
        }

    </style>
</head>
<body>


<div class="wp-success-wrapper container">
    <h4 class="text-center">恭喜，你可以免费上网啦</h4>
    <h5 class="text-danger text-center">获得了<strong class="free-time">未知</strong>小时的免费上网时间</h5>
    <hr>
    <ul class="links clearfix">
        <li style="width: 100%;">试试这些网站吧：</li>
        <li><a target="_blank" href="/"><?=$wpWSiteStore['wsite_title']?></a></li>
        <li><a target="_blank" href="http://www.baidu.com">百度</a></li>
        <li><a target="_blank" href="http://www.qq.com">腾讯</a></li>
        <li><a target="_blank" href="http://music.163.com">网易云音乐</a></li>
        <li><a target="_blank" href="http://www.douban.com">豆瓣</a></li>
        <li><a target="_blank" href="http://www.sohu.com">搜狐</a></li>
        <li><a target="_blank" href="http://www.taobao.com">淘宝</a></li>
        <li><a target="_blank" href="http://www.163.com">豆瓣</a></li>
        <li><a target="_blank" href="http://www.tmall.com">天猫</a></li>
        <li><a target="_blank" href="http://www.youku.com">优酷</a></li>
        <li><a target="_blank" href="http://www.tudou.com">土豆</a></li>
        <li><a target="_blank" href="http://www.tianya.com">天涯</a></li>
        <li><a target="_blank" href="http://www.renren.com">人人网</a></li>
        <li><a target="_blank" href="http://www.weibo.com">新浪微博</a></li>
        <li><a target="_blank" href="http://t.qq.com">腾讯微博</a></li>
    </ul>
    <div class="text-center" style="opacity: .6;margin-top: 20px;">本服务由wipark提供</div>
</div>

<div class="wp-carousel-wrapper">
    <div class="owl-carousel wp-carousel-widget">
        <div class="wp-carousel-card wp-carousel-intro">
            <div class="main">
                <h3 class="text-center"><?=$wpWSiteStore['wsite_title']?></h3>
                <h5 class="text-center" style="opacity: .7;">向左滑动 <strong class="text-danger"><?=$carouselCount?></strong> 张图片免费上网</h5>
                <p class="text-center" style="font-size: 30px;color:#60d7a9;margin-top: 40px;">
                    <i class="glyphicon glyphicon-hand-left"></i>
                </p>
            </div>
            <div class="copyright text-center" style="opacity: .4;">本服务由wipark提供</div>
        </div>
        <?foreach($wpWSiteStore['wsite_carousels'] as $key => $carousel): ?>
            <?if(!empty($carousel)):?>
                <div class="wp-carousel-card">
                    <span class="img-wrapper">
                        <?if(empty($carousel['carousel_redirecturl'])):?>
                            <img src="<?=M_BASE_URL?>/contents/images/<?=$carousel['carousel_img']?>">
                        <?else:?>
                            <a href="<?=$carousel['carousel_redirecturl']?>" target="_blank">
                                <img src="<?=M_BASE_URL?>/contents/images/<?=$carousel['carousel_img']?>">
                            </a>
                        <?endif;?>
                    </span>
                </div>
            <?endif;?>
        <?endforeach;?>
        <div class="wp-carousel-card wp-carousel-gat">
            <div class="main">
                <h3 class="text-center"><?=$wpWSiteStore['wsite_title']?></h3>
                <h5 class="text-center" style="opacity: .7;">点下面的按钮开始免费上网</h5>
                <p class="text-center" style="margin-top:30px;">
                    <a href="javascript:void(0);" class="button button-circle glow wp-getfreewifi-btn" style="font-size:70px;line-height: 120px;color:#60d7a9;">
                        <i style="margin-left: -8px;" class="glyphicon glyphicon-plane"></i>
                    </a>
                </p>
            </div>
            <div class="copyright text-center" style="opacity: .4;">本服务由wipark提供</div>
        </div>
    </div>
</div>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/jquery.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/bootstrap.min.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/lib/owl.carousel.min.js"></script>
<script src="<?if($runEnv == "_M_"):?><?=$W_BASE_URL?><?endif?>/assets/js/lib/buttons.js"></script>
<script>
    $(function(){

        var REDIRECT_URL = "<?=$wpWSiteStore['wsite_success_redirecturl']?>"

        function windowResize(){
            $(".wp-success-wrapper").css({"min-height":$(window).height()});
            $(".wp-carousel-card").height($(window).height());
            $(".wp-carousel-card").width($(window).width());
        }
        windowResize();
        $(window).resize(function(){
            windowResize();
        });


        var wpCarousel = $(".owl-carousel");

        wpCarousel.owlCarousel({
            items : 1,
            responsive:false,
            autoHeight:true
        });

        $(".wp-getfreewifi-btn").click(function(){
            var self = this;
            var origHtml = $(this).html();
            $(this).html("..");
            $.ajax({
                url:"/fw.php",
                dataType:"json",
                success:function(rsp){
                    $(self).html(origHtml);
                    if(rsp.status == "ok"){
                        if(REDIRECT_URL){
                            alert("恭喜你，你可以在"+rsp.time+"小时内免费上网啦");
                            window.location.href = REDIRECT_URL;
                        }else{
                            $(".wp-carousel-wrapper").hide();
                            $(".wp-success-wrapper").slideDown();
                            $(".wp-success-wrapper .free-time").html(rsp.time);
                        }
                    }else{
                        alert("请求免费上网失败："+rsp.msg+"，请稍候重试！");
                    }
                },
                error:function(e,msg){
                    $(self).html(origHtml);
                    alert("请求免费上网失败："+msg+"，请稍候重试！");
                }
            });
        });
    });
</script>
</body>
</html>



