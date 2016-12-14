<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">动态IP设置（DHCP）</p>
    <form action="actionDhcp" method="post" class="form-wipark">
        <p><a href="internet" class="btn btn-lg btn-default btn-block">返回</a></p>
        <div class="alert alert-info">
            <h4>小提示：</h4>
            一般直接点击“应用”设置联网方式为DHCP即可。如有需要也可以手动指定DNS
        </div>
        <label class="checkbox">
            <input id="assigndns" type="checkbox" value="assigndns" onclick="onAssigndns()">
            手动指定DNS地址
        </label>
        <div id="inputdns" class="control-group" style="display:none">
<!--            <label for="mac" class="control-label" for="dns1">DNS：</label>-->
            <input type="text" class="form-control  input-lg" id="mac" name="mac" placeholder="填写DNS地址">
        </div>
        <br>
        <button type="submit" class="btn btn-lg btn-danger btn-block">应用</button>
    </form>
</div>

<script type="text/javascript">
    function onAssigndns(){
        objName= document.getElementById("assigndns");
        var target= document.getElementById("inputdns");
        if (objName.checked){
            target.style.display="block";
        }
        else{
            target.style.display="none";
        }
    }
</script>

<?php
require("footer.php");
?>