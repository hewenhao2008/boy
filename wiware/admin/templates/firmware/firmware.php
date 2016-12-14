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
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <a href="system" class="btn btn-lg btn-default">返回</a>
            </div>
            <div class="btn-group">
                <button id="b01" type="button" class="btn btn-primary btn-lg">检查更新</button>
            </div>
        </div>

        <div style="text-align:center;color:#222; padding-top:15px;">
            <p>当前系统版本：<?=$osname?> <?=$curversion?></p>
        </div>

        <div id="scanDiv">
            <div style="text-align: center">正在检查更新...</div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">更新日志</h4>
            </div>
            <div class="modal-body" id="bodycontent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        setTimeout("loadupdate()",1000);
    });
    function loadupdate(){
        jQuery('#scanDiv').showLoading();
        $("#scanDiv").load("loadupdate", function (response, status, xhr) {
            jQuery('#scanDiv').hideLoading();
        });
    }
    $('#b01').click(function () {
        jQuery('#scanDiv').showLoading();
        $("#scanDiv").load("checkupdate", function (response, status, xhr) {
            jQuery('#scanDiv').hideLoading();
        });
    });
</script>

<br>
<br>
<br>
<br>
<?php
require("footer.php")
?>