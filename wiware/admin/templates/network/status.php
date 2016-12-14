<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <div class="pull-right"><a href="dhcpleases" class="btn btn-danger btn-sm" role="button">客户端列表</a></div>
    <p class="heading">系统状态</p>

    <ul class="list-group">
        <a href="inmode" class="list-group-item">
            <span class="badge" style="background-color:#5CB85C;"><?php echo ($inmode=='0')?"普通路由模式":"智能路由模式";?></span>
            当前运行在：
        </a>
        <li class="list-group-item">
            <span class="badge"><?=$online?> 人</span>
            在线客户：
        </li>
        <li class="list-group-item">
            <span class="badge"><?=$uptime?></span>
            运行时长：
        </li>
    </ul>

    <div class="panel panel-default">
        <div class="panel-heading">互联网<button type="button" class="btn btn-danger btn-xs pull-right">连网方式：<?=$nettype?></button></div>
        <ul class="list-group">
            <li class="list-group-item">IP地址：<span class="pull-right"><?=$wannet?></span></li>
            <li class="list-group-item">MAC地址：<span class="pull-right"><?=$wanmac?></span></li>
            <li class="list-group-item">网关地址：<span class="pull-right"><?=$gateway?></span></li>
        </ul>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">无线局域网</div>
        <ul class="list-group">
            <li class="list-group-item">IP地址：<span class="pull-right"><?=$wlannet?></span></li>
            <li class="list-group-item">MAC地址：<span class="pull-right"><?=$wlanmac?></span></li>
        </ul>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">有线局域网</div>
        <ul class="list-group">
            <li class="list-group-item">IP地址：<span class="pull-right"><?=$lannet?></span></li>
            <li class="list-group-item">MAC地址：<span class="pull-right"><?=$lanmac?></span></li>
        </ul>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">无线网络（访客网络）</div>
        <ul class="list-group">
            <li class="list-group-item">网络名称（SSID）：<span class="pull-right"><?=$ssid?></span></li>
            <li class="list-group-item">加密方式：<span class="pull-right"><?=$encryption?></span></li>
        </ul>
    </div>

    <?php if ($officeenabled != 0): ?>
        <div class="panel panel-default">
            <div class="panel-heading">无线网络（个人办公）</div>
            <ul class="list-group">
                <li class="list-group-item">网络名称（SSID）：<span class="pull-right"><?=$officessid?></span></li>
                <li class="list-group-item">加密方式：<span class="pull-right"><?=$officeencryption?></span></li>
            </ul>
        </div>
    <?php endif ?>
</div>
<?php
require("footer.php")
?>