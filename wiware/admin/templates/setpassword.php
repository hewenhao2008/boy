<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">设置登录密码</p>
    <form class="form-wipark" role="form" action="actionSetpassword" method="post"  onSubmit="return checkpassword()">
        <?php if (isset($flash['error']) ):?>
            <p class="text-danger"><?=$flash['error']?></p>
        <?php endif ?>

        <p><a href="/" class="btn btn-lg btn-default btn-block">返回</a></p>
        <label for="inputPassword" class="control-label">密码：</label>
        <input type="password" class="form-control input-lg" name="inputPassword" id="inputPassword" placeholder="输入密码">
        <span class="help-block">6-20位，必须包含字母、数字，可包含特殊字符</span>

        <label for="confirmPassword" class="control-label">确认密码：</label>
        <input type="password" class="form-control input-lg" name="confirmPassword" id="confirmPassword" placeholder="输入确认密码">
        <button type="submit" class="btn btn-lg btn-danger btn-block">确定</button>
    </form>
</div>

<script type="text/javascript">
    function ispwdok(strPasswd) {
        var re=/^(?!\D+$)(?![^a-zA-Z]+$)\S{6,20}$/g;
        //var re=/^[a-zA-Z0-9!@#$%^&*()_+|{}?><\-\]\\[\/]{6,20}$/i;
        if(re.test(strPasswd))
            return true;
        return false;
    }

    function checkpassword(){
        obj=document.getElementById("inputPassword");
        if(ispwdok(obj.value.trim())==false){
            alert("密码格式不合法！");
            obj.focus();
            return false;
        }

        obj2=document.getElementById("confirmPassword");
        if(obj2.value!=obj.value){
            alert("两次输入的密码不一致！");
            obj2.focus();
            return false;
        }
        return true;
    }
</script>

<?php
require("footer.php")
?>