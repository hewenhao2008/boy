<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">无线设置</p>

    <form class="form-wipark" role="form" action="actionSetChannel" method="post" onSubmit="return checkkey()">
        <p><a href="wireless" class="btn btn-lg btn-default btn-block">返回</a></p>
        <?php if (isset($flash['info']) ):?>
            <p class="text-success"><?=$flash['info']?></p>
        <?php endif ?>

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
            <br/>

        <?php else: ?>
            <div class="alert alert-info">
                <h4>小提示：</h4>
                中继连网方式下信道不可更改，无线信道需要保持与所中继的信号保持一致。
            </div>
            <h5>当前信道：<span style="font-size: 30px;"><?=$channel?></span></h5>
        <?php endif ?>
        <label for="ht" class="control-label">选择频宽模式：</label>
        <select id="ht" name="ht" class="form-control input-lg">
            <option value="20">HT20</option>
            <option value="40">HT40</option>
        </select>
        <small class="text-danger">提示：<br/> HT20适用于多WIFI信号环境下做无线覆盖；<br/> HT40适用于少量WIFI信号的环境（5台以下）</small>
        <br/>
        <br/>
        <button type="submit" class="btn btn-lg btn-danger btn-block">确定</button>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#channel").val("<?=$channel?>");
    $("#ht").val("<?=$ht?>");
});
</script>

<?php
require("footer.php")
?>