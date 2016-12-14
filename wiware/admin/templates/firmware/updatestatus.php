<?php
require("header.php");
require("navbar.php");
?>
<style type="text/css">
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
    <p class="heading">系统更新</p>
    <div class="form-wipark">
        <div id="step1" class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right"><p id="download" style="color:#A94442;">下载中...</p></div>
                <h3 class="panel-title"><strong>第1步：下载新系统</strong></h3>
            </div>
            <div id="downloadDiv" class="panel-body">
                <div class="progress">
                    <div id="downloadprogressbar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                        0%
                    </div>
                </div>
                <div id="errinfo" style="text-align: center;color: #ff0000"></div>
            </div>
        </div>

        <div id="step2" class="panel panel-default" style="display:none">
            <div class="panel-heading">
                <div class="pull-right"><p id="checksum" style="color:#A94442;">校验中...</p></div>
                <h3 class="panel-title"><strong>第2步：校验新系统</strong></h3>
            </div>
        </div>

        <div id="step3" class="panel panel-default" style="display:none">
            <div class="panel-heading">
                <div class="pull-right"><p id="writeware" style="color:#A94442;"></p></div>
                <h3 class="panel-title"><strong>第3步：升级新系统</strong></h3>
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

        <div id="step4" class="panel panel-default" style="display:none">
            <div class="panel-heading">
                <div class="pull-right"><p id="rebooting" style="color:#A94442;">重启中...</p></div>
                <h3 class="panel-title"><strong>第4步：重新启动</strong></h3>
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

<script src="assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
    var writecnt = 0;
    $(document).ready(function (){
        setTimeout("my_progress()",1500);
        $.post("firmwaredownload",
            {
                downloadurl:"<?=$downloadurl?>",
                checksum:"<?=$checksum?>"
            },
            function(data, status){
//                alert("Data: " + data + "\nStatus: " + status);
            });
    });

    function my_progress(){
        $.get("firmwareprogress.php",function(data){
            if(data>=0 && data<=100){
                $("#downloadprogressbar").css("width",data+"%");
                $("#downloadprogressbar").text(data+"%");
                setTimeout("my_progress()",1500);
            }
            else if(data == 'downloadsuccess'){
                $("#downloadprogressbar").css("width","100%");
                $("#downloadprogressbar").text("100%");
                $("#download").css("color","#3C763D");
                $("#download").text("下载成功");
                var target=document.getElementById("downloadDiv");
                target.style.display = "none";
                my_checksum();
            }
            else if(data == 'downloadfailed'){
                $("#download").text("下载失败");
                $("#errinfo").text("下载失败了，请返回重试");
            }
            else{
                setTimeout("my_progress()",1500);
            }
        });
    }

    function my_checksum(){
        var target=document.getElementById("step2");
        target.style.display = "block";
        $.get("firmwarechecksum",function(data){
            if(data == 'checksumsuccess'){
                $("#checksum").css("color","#3C763D");
                $("#checksum").text("校验成功");
//                my_writefirmware();
                document.getElementById("writebutton").style.display = "block";
                document.getElementById("writeprogress").style.display = "none";
                document.getElementById("step3").style.display = "block";
            }else{
                $("#checksum").text(data);
            }
        });
    }
    function my_writefirmware(){
        $("#writeware").css("color","#A94442");
        $("#writeware").text("升级中...");
        $("#writeprogressbar").css("width","0%");
        $("#writeprogressbar").text( "0%");
        document.getElementById("writebutton").style.display = "none";
        document.getElementById("writeprogress").style.display = "block";
        writecnt=0;
        setTimeout("my_writeprogress()",1000);
        $.get("firmwarewrite",function(data){
            if(data == 'beginupgrade'){
//                writecnt = 100;
//                $("#writeprogressbar").css("width","100%");
//                $("#writeprogressbar").text("100%");
//                $("#writeware").css("color","#3C763D");
//                $("#writeware").text("升级成功");
//                jQuery('#waiteDiv').hideLoading();
//                my_firmwaresuccess();
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
                writecnt += Math.floor(10*Math.random());
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
        document.getElementById("step4").style.display = "block";
        //target.style.display = "block";

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
require("footer.php")
?>