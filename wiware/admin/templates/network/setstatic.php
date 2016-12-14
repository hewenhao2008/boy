<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">静态IP设置</p>
    <form action="actionStatic" method="post" class="form-wipark"  onSubmit="return checkip()">
        <p><a href="internet" class="btn btn-lg btn-default btn-block">返回</a></p>
        <label class="control-label">IP地址:</label>
        <input type="text" class="form-control input-lg" name="ip" id="ip" value="<?=$ip?>" placeholder="填写IP地址">
        <label class="control-label">子网掩码:</label>
        <input type="text" class="form-control input-lg" name="netmask" id="netmask" value="<?=$netmask?>" placeholder="填写子网掩码">
        <label class="control-label">网关地址:</label>
        <input type="text" class="form-control input-lg" name="gateway" id="gateway" value="<?=$gateway?>" placeholder="填写网关地址">
        <label class="control-label">DNS:</label>
        <input type="text" class="form-control input-lg" name="dns" id="dns" value="<?=$dns?>" placeholder="填写DNS地址"> (可选)
        <p></p>
        <button type="submit" class="btn btn-lg btn-danger btn-block">应用</button>
    </form>
</div>

<script type="text/javascript">
function isip(strIP) {
    var re=/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/g;
    if(re.test(strIP))
    {
        if( RegExp.$1 <256 && RegExp.$2<256 && RegExp.$3<256 && RegExp.$4<256)
            return true;
    }
    return false;
}

function checkip(){
    obj=document.getElementById("ip");
    if(isip(obj.value.trim())==false){
        alert("IP 地址格式不对！");
        obj.focus();
        return false;
    }
    obj=document.getElementById("netmask");
    if(isip(obj.value.trim())==false){
        alert("掩码格式不对！");
        obj.focus();
        return false;
    }
    obj=document.getElementById("gateway");
    if(isip(obj.value.trim())==false){
        alert("网关地址格式不对！");
        obj.focus();
        return false;
    }
    obj=document.getElementById("dns");
    if(obj.value.trim()!="" && isip(obj.value.trim())==false){
        alert("DNS地址格式不对！");
        obj.focus();
        return false;
    }
    return true;
}
</script>

<?php
require("footer.php");
?>
