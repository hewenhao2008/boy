<?php
require("header.php");
require("navbar.php");
?>
<style type="text/css">
    .loading-indicator {
        height: 80px;
        width: 80px;
        background: url( 'assets/img/loading.gif' );
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
    <p class="heading">中继设置</p>

    <form action="actionStatic" method="post" class="form-wipark" onSubmit="return checkip()">
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <a href="internet" class="btn btn-lg btn-default">返回</a>
            </div>
            <div class="btn-group">
                <button id="b01" type="button" class="btn btn-primary btn-lg" data-loading-text="Loading...">扫描刷新</button>
            </div>
        </div>
        <p></p>
        <?php if (isset($flash['error']) ):?>
            <p class="text-danger"><?=$flash['error']?></p>
        <?php endif ?>
        <div id="scanDiv">
            <div class="jumbotron"> 扫描中 ...</div>
        </div>
        <a href="#myModal" role="button" class="btn btn-lg btn-danger btn-block" style="clear: both;" data-toggle="modal">确定</a>
    </form>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actionWds" method="post" class="form-horizontal" tabindex="-1" onSubmit="return checkkey()">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 id="myModalLabel">中继到无线信号</h3>
                </div>
                <div class="modal-body">
                    <table id="selectedap" class="table table-striped">
                        <thead><tr><th>SSID</th><th>BSSID</th><th>通道</th></tr></thead>
                        <tbody><tr><td></td><td></td><td></td></tr></tbody>
                    </table>
                    <fieldset>
                        <input type="hidden" name="proto" value="sta" id="proto">
                        <input type="hidden" id="ssid" name="ssid" value="">
                        <input type="hidden" id="bssid" name="bssid" value="">
                        <input type="hidden" id="channel" name="channel" value="">
                        <input type="hidden" id="auth" name="auth" value="">
<!--                        <input type="hidden" id="authmode" name="authmode" value="">-->
<!--                        <input type="hidden" id="encryptype" name="encryptype" value="">-->
                        <div id="accountDiv" style="display:none">
                            <label class="control-label" for="key">无线密码：</label>
                            <input type="password" class="form-control input-lg" id="key" name="key" placeholder="填写无线密码">
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default btn-lg" data-dismiss="modal" aria-hidden="true">关闭</button>
                    <button type="submit" class="btn btn-danger btn-lg">应用</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        jQuery('#scanDiv').showLoading();
        $("#scanDiv").load("scanwifi", function (response, status, xhr) {
            jQuery('#scanDiv').hideLoading();
        });
    });
    $('#b01').click(function () {
        jQuery('#scanDiv').showLoading();
        $("#scanDiv").load("scanwifi", function (response, status, xhr) {
            jQuery('#scanDiv').hideLoading();
        });
    });

    $("tr").live("click", function () {
        $(this).find("input:radio").attr("checked", "checked");
    });

    $("tr").bind("click", function () {
        $(this).find("input:radio").attr("checked", "checked");
    });

    $('#myModal').on('shown.bs.modal', function () {
        var cnt = $('input:radio[name="apselect"]:checked').size();
        if (cnt == 0) {
            alert("您还没有选中想要中继的无线信号");
            return false;
        }

        var v = $('input:radio[name="apselect"]:checked').val();

        arr = v.split(",");
        var sig = arr[0];
        var bssid = arr[1];
        var ssid = arr[2];
        var channel = arr[3];
        var auth = arr[4];

//        tmp = auth.split("/");
//        var authmode = tmp[0];
//        var encryptype = tmp[1];

        $("#selectedap tr:eq(1) td:nth-child(1)").html(ssid);
        $("#selectedap tr:eq(1) td:nth-child(2)").html(bssid);
        $("#selectedap tr:eq(1) td:nth-child(3)").html(channel);

        $('#ssid').val(ssid);
        $('#bssid').val(bssid);
        $('#channel').val(channel);
        $('#auth').val(auth);
//        $('#authmode').val(authmode);
//        $('#encryptype').val(encryptype);

        if (auth == "NONE") {
            $('#accountDiv').hide();
        } else {
            $('#accountDiv').show();
        }
    });

    function checkkey(){
        var auth = $('#auth').val();
        obj=document.getElementById("key");
        var key = obj.value.trim();
        if(auth.indexOf('WPA')>=0 && auth.indexOf('PSK')>=0){
            if(key.length < 8 || key.length > 63){
                alert('输入的密码位数不正确');
                obj.focus();
                return false;
            }
        }else if(auth == 'OPEN/WEP'){
            if(key.length != 5 && key.length != 10){
                alert('请输入正确的密码');
                obj.focus();
                return false;
            }
        }
        return true;
    }
</script>

<?php
require("footer.php");
?>
