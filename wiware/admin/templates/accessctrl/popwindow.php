<?php
require("header.php");
require("navbar.php");
?>

    <link href="assets/css/blue.css" rel="stylesheet">
    <script src="assets/js/icheck.min.js"></script>

    <div class="container">
        <p class="heading">自动弹出首页设置</p>
        <form class="form-wipark" role="form" action="actionSetPopwindow" method="post">
            <p><a href="accessctrl" class="btn btn-lg btn-default btn-block">返回</a></p>
            <div class="alert alert-info">
                <h4>小提示：</h4>
                连上盒子WIFI信号后，是否自动弹出微站首页。（仅在<a href="inmode"><strong>智能路由模式</strong></a>下有效，有些终端设备的系统不支持连上WIFI信号自动弹窗。）
            </div>
            <label for="popup" class="control-label">选择弹窗策略：</label>
            <div class="skin-square">
                <ul class="list">
                    <li>
                        <input tabindex="1" type="radio" id="radio-1" name="popup" value="0" <?php if($popwindow == 0) echo "checked"; ?>>
                        <label for="radio-1">不自动弹出首页</label>
                    </li>
                    <li>
                        <input tabindex="2" type="radio" id="radio-2" name="popup" value="1" <?php if($popwindow == 1) echo "checked"; ?>>
                        <label for="radio-2">自动弹出首页</label>
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