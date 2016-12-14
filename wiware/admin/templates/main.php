<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <?if($newversion):?>
    <div style="width:100%;">
        <a href="firmware" class="blocka" style="background-color: #D9534F;color: #FFF;">
            <strong><i class="glyphicon glyphicon-info-sign pull-right" style="font-size: 32px;"></i>发现新版本：WIWARE <?=$newversion?></strong>
            <div class="info">
                <span>点此立即检查升级</span>
            </div>
        </a>
    </div>
    <?endif?>

    <div class="block-justified">
    <div class="block" style="width:40%;">
        <a href="internet" class="blocka" style="background-color: #3191ED;">
            <strong>外网设置</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-globe"></i>
            </div>
            <div class="info">
                <span>连接到互联网</span>
            </div>
        </a>
    </div>
    <div class="block" style="width:28%;">
        <a href="status" class="blocka" style="background-color: #767CE1;">
            <strong>网络状态</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-stats"></i>
            </div>
            <div class="info">
                <span class="label label-success">
                     <?php echo ($inmode=='0')?"普通模式":"智能模式";?>
                </span>
            </div>
        </a>
    </div>
    <div class="block" style="width:28%;">
<!--        <a href="getaccesstime" class="blocka" style="background-color: #9A59EB;">-->
        <a href="wisite" class="blocka" style="background-color: #9A59EB;">
            <strong>我的微站</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-heart"></i>
            </div>
            <div class="info">
                <span>微站管理</span>
            </div>
        </a>
    </div>
    </div>

    <div class="block-justified">
    <div class="block" style="width:40%;">
        <a href="lannet" class="blocka" style="background-color: #00C5D7;">
            <strong>内网设置</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-transfer"></i>
            </div>
            <div class="info">
                <span>局域网设置</span>
            </div>
        </a>
    </div>

    <div class="block" style="width:28%;">
        <a href="wpcenter"  class="blocka" style="background-color: #FFF; color: #FF9838;">
        <div id="centralpark">
            <strong>WPCenter</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-user"></i>
            </div>
            <div class="info">
                <span>&nbsp;</span>
            </div>
        </div>
        </a>
    </div>
    <div class="block" style="width:28%;">
        <a href="accessctrl" class="blocka" style="background-color: #FF667A;">
            <strong>访问控制</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-hand-up"></i>
            </div>
            <div class="info">
                <span>弹窗白名单</span>
            </div>
        </a>
    </div>
    </div>

    <div class="block-justified">
    <div class="block" style="width:40%;">
        <a href="wireless" class="blocka" style="background-color: #7CCF45;">
            <strong>无线管理</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-signal"></i>
            </div>
            <div class="info">
                <span>&nbsp;</span>
            </div>
        </a>
    </div>
    <div class="block" style="width:28%;">
        <a href="bandwidth" class="blocka" style="background-color: #F5B53F;">
        <div class="blockinner">
            <strong>带宽控制</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-road"></i>
            </div>
            <div class="info">
                <span>智能控制</span>
            </div>
        </div>
        </a>
    </div>
    <div class="block" style="width:28%;">
        <a href="system" class="blocka" style="background-color: #FF8554;">
            <strong>系统</strong>
            <div class="bg">
                <i class="glyphicon glyphicon-cog"></i>
            </div>
            <div class="info">
                <span>系统与外设</span>
            </div>
        </a>
    </div>
    </div>

    <div style="width:100%;">
        <div class="blocka" style="background-color: #00AC4A;">
            <p>
                <strong>MAC：<?=$boxmac?>&nbsp;&nbsp;WI码：<?=$wicode?></strong>
            </p>
            <p>
                <strong>当前：
                    <span class="label label-warning"><?=$nettypedesc?></span>
                <?php
                    if(($nettype=='dhcp' || $nettype=='static' || $nettype=='pppoe') && $wanlink=='disconnected')
                        echo '<span class="label label-danger">网线断开</span>';
                ?>
                <span id="netstatus" class="label label-primary">外网检测...</span>
                </strong>
            </p>
        </div>
    </div>
</div>

<script src="../assets/js/jquery.js"></script>
<script src="../assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: 'ping.php',
            type: 'GET',
//            data: 'proto='+$('#proto').attr('name'),
            dataType: 'text',
            timeout: 20000, //毫秒
            error: function(){
                $("#netstatus").text('检测失败');
            },
            success: function(resdata){
                $('#netstatus').text(resdata);
            }
        });
    });
</script>

<?php
require("footer.php")
?>
