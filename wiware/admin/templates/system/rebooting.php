<?php
require("header.php");
require("navbar.php");
?>

<style type="text/css">
    .loading-indicator {
        height: 80px;
        width: 80px;
        background: url('assets/img/loading.gif');
        background-repeat: no-repeat;
        background-position: center center;
    }
    .loading-indicator-overlay {
        background-color: #FFFFFF;
        opacity: 0.6;
        filter: alpha(opacity = 60);
    }
</style>

<div class="container">
    <p class="heading">重启系统</p>
    <div id="restarting">
        <div class="jumbotron">
            <?php if (isset($flash['info']) ):?>
                <p class="text-danger"><?=$flash['info']?></p>
            <?php endif ?>
            <p>正在重启系统，请稍候...</p>
        </div>
    </div>
</div>

<script src="assets/js/jquery.js"></script>
<script src="assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        jQuery('#restarting').showLoading();
        setTimeout("my_reboot()",0);
    });

    function my_reboot(){
        $.ajax({
            url: 'reboot',
            type: 'GET',
//            data: 'proto='+$('#proto').attr('name'),
            dataType: 'text',
            timeout: 30000, //毫秒
            error: function(){
                jQuery('#restarting').hideLoading();
                $("#restarting").html('<a href="/" class="btn btn-lg btn-default btn-block">返回</a><p></p><div class="alert alert-danger"><p><strong>重启完成</strong><br>由于重启过程会中断您的WIFI连接，如果您发现不能连网。请首先检查和确认您连接的WIFI网络是否正确。</p></div>');
            },
            success: function(resdata){
                location.href = "logout";
            }
        });
    }
</script>

<?php
require("footer.php")
?>
