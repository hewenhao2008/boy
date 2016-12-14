<?php
require("header.php");
require("navbar.php");
?>
<style type="text/css" xmlns="http://www.w3.org/1999/html">
    .loading-indicator {
        height: 80px;
        width: 80px;
        background: url( 'assets/img/loading.circles.gif' );
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
    <p class="heading">固件升级</p>
    <div class="form-wipark">
        <p><a href="system" class="btn btn-lg btn-default btn-block">返回</a></p>
        <?php if (isset($flash['error']) ):?>
            <p class="text-danger"><?=$flash['error']?></p>
        <?php endif ?>
        <p></p>
        <div id="step1" class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right"><p id="uploadware" style="color:#A94442;"></p></div>
                <h3 class="panel-title"><strong>第1步：上传系统固件</strong></h3>
            </div>
            <div id="uploadDiv" class="panel-body">
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>选择固件</span>
                    <input id="fileupload" type="file" name="file">
                </span>&nbsp;&nbsp;<a href="http://www.wipark.cn/download.php" target="_blank">系统固件下载</a>
                <br>
                <br>
                <div id="uploadprogress" class="progress">
                    <div id="uploadprogressbar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        0%
                    </div>
                </div>

                <div id="uploadinfo" style="padding: 10px 0;">
                </div>
            </div>
        </div>

        <div id="step2" class="panel panel-default" style="display:none">
            <div class="panel-heading">
                <div class="pull-right"><p id="writeware" style="color:#A94442;"></p></div>
                <h3 class="panel-title"><strong>第2步：升级系统固件</strong></h3>
            </div>

            <div id="upgradeDiv" class="panel-body">
                <button id="writebutton" class="btn btn-lg btn-danger btn-block" onClick="my_writefirmware()">立即升级</button>
                <div id="writeprogress" class="progress"  style="display:none">
                    <div id="writeprogressbar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        0%
                    </div>
                </div>
                <div id="waiteDiv"></div>
            </div>
        </div>

        <div id="step3" class="panel panel-default" style="display:none">
            <div class="panel-heading">
                <div class="pull-right"><p id="rebooting" style="color:#A94442;">重启中...</p></div>
                <h3 class="panel-title"><strong>第3步：重新启动系统</strong></h3>
            </div>
            <div id="rebootDiv" class="panel-body">
<!--                <a href="rebooting" class="btn btn-lg btn-danger btn-block">立即重启</a>-->
                <div id="rebootprogress" class="progress">
                    <div id="rebootprogressbar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        0%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/jquery.fileupload-ui.css"/>
<script src="/assets/js/lib/jquery.form.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.ui.widget.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.iframe-transport.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.fileupload.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.fileupload-process.js"></script>
<script src="assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
function confirmUpgrade(){
    if(confirm("确定要升级固件吗?")) {
        return true;
    }
    else{
        return false;
    }
}

$(function(){
    'use strict';
    $('#fileupload').fileupload({
        url: 'uploadware',
        dataType: 'json',
        autoUpload: false,
        acceptFileTypes: '/\.bin/i'
    }).on('fileuploadadd', function (e, data) {
        document.getElementById("step2").style.display='none';
        document.getElementById("step3").style.display='none';
        $("#uploadware").text('');
        $("#uploadprogressbar").css("width","0%");
        $("#uploadprogressbar").text( "0%");
        $('#uploadinfo').html('<span id="filename">'+data.files[0].name+'</span> <span id="errorinfo" class="text-danger"></span>')
        data.context = $('<button/>').text('上传')
            .addClass('btn btn-primary pull-right')
            .appendTo('#uploadinfo')
            .click(function () {
                data.context = $(this).remove();
                $("#uploadware").text("上传中...");
                data.submit();
            });
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $("#uploadprogressbar").css("width",progress + "%");
        $("#uploadprogressbar").text(progress + "%");
    }).on('fileuploaddone', function (e, data) {
        if (data.result.status == "success") {
            $("#uploadware").text("上传成功");
            $("#uploadware").css("color","#3C763D");
            document.getElementById("uploadDiv").style.display="none";

            $("#writeware").text("");
            document.getElementById("writebutton").style.display = "block";
            document.getElementById("writeprogress").style.display = "none";
            document.getElementById("step2").style.display = "block";
        }
        else{
            $("#uploadware").text("上传失败");
            $("#uploadware").css("color","#A94442");
            $("#errorinfo").text(data.result.rsp);
        }
    }).on('fileuploadfail', function (e, data) {
        $("#uploadware").css("color","#A94442");
        $("#uploadware").text("上传失败");
    });
});

//begin sysupgrade
function my_writefirmware(){
    $("#writeware").css("color","#A94442");
    $("#writeware").text("升级中...");
    $("#writeprogressbar").css("width","0%");
    $("#writeprogressbar").text( "0%");
    document.getElementById("writebutton").style.display = "none";
    document.getElementById("writeprogress").style.display = "block";
    writecnt=0;
    setTimeout("my_writeprogress()",1000);
    $.get("writeware",function(data){
        if(data == 'beginupgrade'){

        }else{
            writecnt = 100;
            jQuery('#waiteDiv').hideLoading();
            $("#writeware").text(data);
        }
    });
}

function my_writeprogress(){
    if(writecnt>=0 && writecnt<100){
        $("#writeprogressbar").css("width",writecnt+"%");
        $("#writeprogressbar").text(writecnt+"%");
        setTimeout("my_writeprogress()",1500);
        writecnt += Math.floor(8*Math.random());
    }
    else if(writecnt >= 100){
        $("#writeprogressbar").css("width","100%");
        $("#writeprogressbar").text("100%");
        jQuery('#waiteDiv').showLoading();
        setTimeout("my_rebootprogress()",5000);
        writecnt=0;
    }
}

function my_rebootprogress(){
    jQuery('#waiteDiv').hideLoading();
    $("#writeware").css("color","#3C763D");
    $("#writeware").text("升级成功");
    document.getElementById("upgradeDiv").style.display="none";

    var target=document.getElementById("step3");
    target.style.display = "block";

    if(writecnt>=0 && writecnt<100){
        $("#rebootprogressbar").css("width",writecnt+"%");
        $("#rebootprogressbar").text(writecnt+"%");
        setTimeout("my_rebootprogress()",1500);
        writecnt += Math.floor(10*Math.random());
    }
    else if(writecnt >= 100){
        $("#rebootprogressbar").css("width","100%");
        $("#rebootprogressbar").text("100%");
        setTimeout("my_boxisrunning()",1000);
    }
}

function my_boxisrunning(){
    $.ajax({
        url:"ping",
        type: 'GET',
        timeout: 20000,
        error: function(){
            writecnt = 100;
            $("#rebooting").text("");
            $("#rebootDiv").html('<div class="alert alert-danger"><p>由于重启过程会中断您的WIFI连接，如果您发现不能连网。请首先检查和确认您连接的WIFI网络是否正确。</p></div> <a href="logout" class="btn btn-lg btn-danger btn-block">重新登录</a>');
        },
        success: function(resdata){
            $("#rebooting").text("重启成功");
            $("#rebooting").css("color","#3C763D");
            $("#rebootDiv").html('<div class="alert alert-success"><p><strong>重启完成</strong><br>重启完成需要重新登录。</p></div><a href="logout" class="btn btn-lg btn-danger btn-block">重新登录</a>');
        }
    });
}
</script>

<?php
require("footer.php");
?>
