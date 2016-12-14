<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">有线局域网(LAN)设置</p>
    <form action="actionLan" method="post" class="form-wipark"  onSubmit="return checkform()">
        <p><a href="lannet" class="btn btn-lg btn-default btn-block">返回</a></p>
        <?php if (isset($flash['error']) ):?>
            <p class="text-danger"><?=$flash['error']?></p>
        <?php endif ?>
        <label class="control-label">盒子有线IP地址：</label>
        <input type="text" class="form-control input-lg" name="ip" id="ip" value="<?=$ip?>" placeholder="填写IP地址">
        <label class="control-label">子网掩码：</label>
        <input type="text" class="form-control input-lg" name="netmask" id="netmask" value="<?=$netmask?>" placeholder="填写子网掩码">
        <p></p>
        <button type="submit" class="btn btn-lg btn-danger btn-block">应用</button>
    </form>
</div>

<script type="text/javascript">
    function isip(strIP) {
        var re=/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/g;
        if(re.test(strIP))
        {
            if( RegExp.$1==192 && RegExp.$2==168 && RegExp.$3<256 && RegExp.$4<256)
                return true;
        }
        return false;
    }
    function ismask(mask) {
        var obj=mask;
        var exp=/^(254|252|248|240|224|192|128|0)\.0\.0\.0|255\.(254|252|248|240|224|192|128|0)\.0\.0|255\.255\.(254|252|248|240|224|192|128|0)\.0|255\.255\.255\.(254|252|248|240|224|192|128|0)$/;
        var reg = obj.match(exp);
        if(reg==null) {
            return false;
        }
        else {
            return true;
        }
    }

    function checkform(){
        obj=document.getElementById("ip");
        if(isip(obj.value.trim())==false){
            alert("IP 地址格式不对！正确格式为192.168.x.x");
            obj.focus();
            return false;
        }
        obj=document.getElementById("netmask");
        if(ismask(obj.value.trim())==false){
            alert("掩码格式不对！");
            obj.focus();
            return false;
        }
        return true;
    }
</script>

<?php
require("footer.php");
?>
