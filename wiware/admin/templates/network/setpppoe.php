<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">宽带拨号设置（PPPoE）</p>
    <form action="actionPPPoE" method="post" class="form-wipark"  onSubmit="return checkform()">
        <p><a href="internet" class="btn btn-lg btn-default btn-block">返回</a></p>
        <label class="control-label">宽带账号:</label>
        <input type="text" class="form-control input-lg" id="username" name="username" value="<?=$username?>" placeholder="填写宽带账号">
        <label class="control-label">密码:</label>
        <input type="password" class="form-control input-lg" id="password" name="password" value="<?=$password?>" placeholder="填写密码">
        <p></p>
        <button type="submit" class="btn btn-lg btn-danger btn-block">应用</button>
    </form>
</div>

<script type="text/javascript">
    function checkform(){
        obj=document.getElementById("username");
        if ( obj.value.trim() == "" ){
            alert("请填写宽带账号！");
            obj.focus();
            return false;
        }
        obj=document.getElementById("password");
        if ( obj.value.trim() == "" ){
            alert("密码不能留空！");
            obj.focus();
            return false;
        }
        return true;
    }
</script>

<?php
require("footer.php");
?>
