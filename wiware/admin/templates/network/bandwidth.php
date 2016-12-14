<?php
require("header.php");
require("navbar.php");
?>

<link href="assets/css/blue.css" rel="stylesheet">

<div class="container">
    <p class="heading">带宽控制</p>
    <form class="form-wipark" role="form" action="actionBandCtrl" method="post" onSubmit="return checkvalue()">
        <p><a href="/" class="btn btn-lg btn-default btn-block">返回</a></p>
        <p>可以根据您的网络实际的带宽大小指定盒子无线带宽，例如ADSL10M宽带，可填写10</p>
        <label for="bandwidth" class="control-label">无线带宽：</label>
        <div class="input-group">
            <input type="text" class="form-control" id="bandwidth" name="bandwidth"
                   onkeypress="return inputOnlyNumber(event)" value="<?=$bandwidth?>">
            <span class="input-group-addon">兆（Mbps）</span>
        </div>
        <p></p>
        <label for="shareband" class="control-label">带宽控制策略：</label>
        <div class="skin-square">
            <ul class="list">
                <li>
                    <input type="radio" id="radio-1" name="shared" value="0" <?php if($shared == 0) echo "checked"; ?>>
                    <label for="radio-1">平均分配（接入的用户平分带宽）</label>
                </li>
                <li>
                    <input type="radio" id="radio-2" name="shared" value="1" <?php if($shared == 1) echo "checked"; ?>>
                    <label for="radio-2">共享（接入的用户共享带宽）</label>
                </li>
            </ul>
        </div>
        <label for="maxclients" class="control-label">允许的最多用户数：</label>
        <div class="input-group">
            <input type="text" class="form-control" id="maxclients" name="maxclients"
                   onkeypress="return inputOnlyNumber(event)" value="<?=$maxclients?>">
            <span class="input-group-addon">人同时在线</span>
        </div>
        <br>
        <p></p>
        <button type="submit" class="btn btn-lg btn-danger btn-block">保存</button>
    </form>
</div>

<script src="assets/js/icheck.min.js"></script>
<script>
    $(document).ready(function(){
        $('input').icheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });
    });

    function checkvalue() {
        obj = document.getElementById("bandwidth");
        if (obj.value <=0 || obj.value > 1000) {
            alert("带宽值不合法，必须是1~1000间的整数");
            obj.focus();
            return false;
        }

        obj = document.getElementById("maxclients");
        if (obj.value <=3 || obj.value > 50) {
            alert("允许的最多用户数不合法，必须是3~50间的整数");
            obj.focus();
            return false;
        }
        return true;
    }
</script>

<?php
require("footer.php")
?>