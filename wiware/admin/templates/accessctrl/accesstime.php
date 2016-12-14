<?php
require("header.php");
require("navbar.php");
?>

<link href="assets/css/blue.css" rel="stylesheet">
<script src="assets/js/icheck.min.js"></script>

<div class="container">
    <p class="heading">上网时长设置</p>
    <form class="form-wipark" role="form" action="actionSetAccesstime" method="post">
        <p><a href="accessctrl" class="btn btn-lg btn-default btn-block">返回</a></p>
        <div class="alert alert-info">
            <h4>小提示：</h4>
            用户获取上网时长后才可以访问互联网。
            您可以选择设置用户单次可获取的网络时长，超时后，用户对互联网的任何访问将被转向到您的微站。
        </div>
        <label for="accesstime" class="control-label">选择时长：</label>
        <select id="accesstime" name="accesstime" class="form-control input-lg">
            <option value="0.5">半个小时</option>
            <option value="1">1个小时</option>
            <option value="2">2个小时</option>
            <option value="3">3个小时</option>
            <option value="6">6个小时</option>
            <option value="12">12个小时</option>
            <option value="24">全天24小时</option>
<!--            <option value="48">2天48小时</option>-->
<!--            <option value="72">3天72小时</option>-->
        </select>
        <br>
        <button type="submit" class="btn btn-lg btn-danger btn-block">确定</button>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('input').icheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });

        $("#accesstime").val(<?=$accesstime?>);
    });

</script>

<?php
require("footer.php")
?>