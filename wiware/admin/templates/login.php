<?php
require("header.php");
?>

<div class="container">
    <div class="jumbotron">
    <form action="actionLogin" method="post" class="form-wipark">
        <h2 class="form-wipark-heading"><i class="glyphicon glyphicon-globe"></i>&nbsp;微站盒子管理</h2>
        <input type="password" class="form-control input-lg" name="password" id="password" placeholder="输入登录密码">
        <?php if (isset($flash['error']) ):?>
            <p class="text-danger"><?=$flash['error']?></p>
        <?php endif ?>
        <button type="submit" class="btn btn-lg btn-danger btn-block">登录</button>
    </form>
    </div>
</div>

<?php
require("footer.php")
?>