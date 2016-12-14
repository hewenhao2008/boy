<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <div class="pull-right"><a href="logread" class="btn btn-danger btn-sm" role="button">系统日志</a></div>
    <p class="heading">系统信息</p>
    <ul class="list-group">
        <li class="list-group-item">处理器：<span class="pull-right"><?=$cpuinfo?></span></li>
        <li class="list-group-item">运行内存：<span class="pull-right"><?=$meminfo?></span></li>
        <li class="list-group-item">扩展存储：<span class="pull-right"><?=$diskinfo?></span></li>
    </ul>
</div>

<?php
require("footer.php")
?>