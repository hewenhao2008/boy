<?php
require("header.php");
require("navbar.php");
?>

<link href="assets/css/blue.css" rel="stylesheet">
<script src="assets/js/icheck.min.js"></script>

<div class="container">
    <p class="heading">盒子运行模式设置</p>
    <form class="form-wipark" role="form" action="actionSetInmode" method="post">
        <p><a href="/" class="btn btn-lg btn-default btn-block">返回</a></p>
        <div class="alert alert-info">
            <h4>小提示：</h4>
            智能路由模式支持自动拦截显示微站，访客需要认证获取上网时长才可以上网，还可以让连上盒子WIFI的终端自动弹出微站首页。
        </div>
        <label for="inmode" class="control-label">选择运行模式：</label>
        <div class="skin-square">
            <ul class="list">
                <li>
                    <input tabindex="1" type="radio" id="radio-1" name="inmode" value="1" <?php if($inmode != 0) echo "checked"; ?>>
                    <label for="radio-1">智能路由模式</label>
                </li>
                <li>
                    <input tabindex="2" type="radio" id="radio-2" name="inmode" value="0" <?php if($inmode == 0) echo "checked"; ?>>
                    <label for="radio-2">普通路由模式</label>
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
</script>

<?php
require("footer.php")
?>