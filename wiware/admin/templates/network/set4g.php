<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">4G数据联网</p>
    <form action="actionSet4g" method="post" class="form-wipark">
        <p><a href="internet" class="btn btn-lg btn-default btn-block">返回</a></p>
        <p>直接点击“应用”即可设置联网方式为4G数据联网。盒子将自动拨号联网。请确保4G数据卡已缴费可正常使用。</p>

        <button type="submit" class="btn btn-lg btn-danger btn-block">应用</button>
    </form>
</div>

<?php
require("footer.php");
?>