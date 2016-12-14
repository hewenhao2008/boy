<?php
require("header.php");
require("navbar.php");
?>
<style>
    .my-input-group {
        position: relative;
        display: table;
        border-collapse: separate;
    }
    .my-input-group .form-control {
        display: table-cell;
        position: relative;
        z-index: 2;
        float: left;
        margin-bottom: 0;
    }
    .my-input-group .form-control:first-child {
        border-right: 0;
        text-align: right;
        width: 40%;
        white-space: nowrap;
        vertical-align: middle;
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
    }
    .my-input-group .form-control:last-child {
        width: 60%;
        border-bottom-left-radius: 0;
        border-top-left-radius: 0;
    }

</style>
<div class="container">
    <p class="heading">无线设置</p>

    <form class="form-wipark" role="form" action="actionWguest" method="post" onSubmit="return checkkey()">
        <?php if (isset($flash['error'])): ?>
            <p class="text-danger"><?= $flash['error'] ?></p>
        <?php endif ?>

        <p><a href="wireless" class="btn btn-lg btn-default btn-block">返回</a></p>

        <label for="apn" class="control-label">网络名称：</label>
        <div class="my-input-group">
<!--            <span class="input-group-addon">--><?//=$apprefix?><!--</span>-->
            <input type="text" class="form-control  input-lg" id="apprefix" name="apprefix" maxlength="10" value="<?=$apprefix?>"/>
            <input type="text" class="form-control  input-lg" id="apname" name="apname" maxlength="20" value="<?=$apname?>" tabindex="1"/>
        </div>
        <br>
        <?php if ($nettype != 'sta'): ?>
            <label for="channel" class="control-label">选择信道：</label>
            <select id="channel" name="channel" class="form-control input-lg">
                <option value="auto">自动选择</option>
                <option value="1">1信道</option>
                <option value="2">2信道</option>
                <option value="3">3信道</option>
                <option value="4">4信道</option>
                <option value="5">5信道</option>
                <option value="6">6信道</option>
                <option value="7">7信道</option>
                <option value="8">8信道</option>
                <option value="9">9信道</option>
                <option value="10">10信道</option>
                <option value="11">11信道</option>
            </select>
        <?php else: ?>
            <h5>当前信道：<?=$channel?> &nbsp;&nbsp;(中继状态下不可更改)</h5>
        <?php endif ?>

        <label for="encryption" class="control-label">安全类型：</label>

        <label class="radio">
            <input type="radio" name="encryption" id="encryption1" value="none"
                   onclick="onEncryption()" <?php if ($encryption != "psk+psk2") echo "checked"; ?>>
            开放（无需密码访问）
        </label>

        <label class="radio">
            <input type="radio" name="encryption" id="encryption2" value="psk+psk2"
                   onclick="onEncryption()" <?php if ($encryption == "psk+psk2") echo "checked"; ?>>
            加密（WPA/WPA2个人版）
        </label>

        <div id="encryptionPsswd" class="form-group" style="display:none">
            <label for="channel" class="control-label">密码：</label>
            <input type="password" class="form-control input-lg" id="key" name="key"  maxlength="64" value="<?=$key?>">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="showpassword" id="showpassword" onclick="onShowPassword()">
                    显示密码
                </label>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-lg btn-danger btn-block">确定</button>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#channel").val("<?=$channel?>");
        onEncryption();
    });

    //A-Z,a~z,0~9,",","-"
    function isAscii(str) {
        return str.toString().match(/^[A-Za-z0-9,-]*$/gi) != null;
    }

    function checkkey() {
        obj = document.getElementById("apprefix");
        if(obj.value.trim() == ""){
            alert("热点前缀不能为空！");
            obj.focus();
            return false;
        }
        obj = document.getElementById("apname");
        if(obj.value.trim() == ""){
            alert("热点名不能为空！");
            obj.focus();
            return false;
        }

        encryption2 = document.getElementById("encryption2");
        if (encryption2.checked == false) {
            return true;
        }

        obj = document.getElementById("key");
        keylen = obj.value.length;
        if (keylen < 8 || keylen > 64) {
            alert("密码长度不合法，密码要求是8-64位ASCII字符！");
            obj.focus();
            return false;
        }

        if (isAscii(obj.value) != true) {
            alert("密码中有非法字符，密码要求是8-64位的ASCII字符！");
            obj.focus();
            return false;
        }

        return true;
    }

    function onEncryption() {
        objName = document.getElementById("encryption2");

        var target = document.getElementById("encryptionPsswd");
        if (objName.checked) {
            target.style.display = "block";
        }
        else {
            target.style.display = "none";
        }
    }

    function onShowPassword() {
        objName = document.getElementById("showpassword");

        var target = document.getElementById("key");
        if (objName.checked) {
            target.type = "text";
        }
        else {
            target.type = "password";
        }
    }
</script>

<?php
require("footer.php")
?>