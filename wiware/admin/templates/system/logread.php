<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">系统日志</p>
    <div class="panel panel-default">
        <div class="panel-heading">日志信息</div>
        <div class="panel-body">
            <p><?=$syslog?></p>
        </div>
    </div>
</div>

<?php
require("footer.php")
?>