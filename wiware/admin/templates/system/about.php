<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">关于微站盒子</p>
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><strong>盒子WI 码：<span class="pull-right"><?=$wicode?></span></strong></div>

        <ul class="list-group">
            <li class="list-group-item">盒子型号：<span class="pull-right"><?=$hardware?></span></li>
            <li class="list-group-item">WiWare 版本：<span class="pull-right"><?=$wiwareversion?></span></li>
            <li class="list-group-item">内核版本：<span class="pull-right"><?=$kernelversion?></span></li>
        </ul>

<!--        <div class="panel-body">-->
<!--            <p><strong>关于WiPark</strong><br>-->
<!--                我们用心打造世界全新的无线网络服务！<br>-->
<!--                官方网站：<a href="http://www.wipark.cn">http://www.wipark.cn</a><br>-->
<!--                合作和服务请联系：service@wipark.cn<br>-->
<!--            </p>-->
<!--        </div>-->
    </div>
</div>

<?php
require("footer.php")
?>