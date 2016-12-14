<?php
require("header.php");
require("navbar.php");
?>

<link href="assets/css/blue.css" rel="stylesheet">
<script src="assets/js/icheck.min.js"></script>

<div class="container">
    <p class="heading">打印服务首页设置</p>
    <form class="form-wipark" role="form" action="actionSetPrinter" method="post">
        <p><a href="system" class="btn btn-lg btn-default btn-block">返回</a></p>
        <div class="alert alert-info">
            <h4>小提示：</h4>
            设置该盒子是否开启打印服务。（打印节点连接小票打印机，启动打印服务）
        </div>
        <button type="button" class="btn btn-primary" onclick="printtest()">打印测试页</button>
        <br>
        <br>
        <label for="hasprinter" class="control-label">选择弹窗策略：</label>
        <div class="skin-square">
            <ul class="list">
                <li>
                    <input tabindex="1" type="radio" id="radio-1" name="hasprinter" value="1" <?php if($hasprinter == 1) echo "checked"; ?>>
                    <label for="radio-1">开启打印服务</label>
                </li>
                <li>
                    <input tabindex="2" type="radio" id="radio-2" name="hasprinter" value="0" <?php if($hasprinter == 0) echo "checked"; ?>>
                    <label for="radio-2">关闭打印服务</label>
                </li>
            </ul>
        </div>
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
    });

    function printtest(){
        $.get("printtest",function(data){
            if(data == "success"){
                alert('打印测试页成功');
            }
            else{
                alert('打印测试页失败');
            }
        });
    }
</script>

<?php
require("footer.php")
?>