<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">局域网设置</p>

    <div class="block-justified">
        <div class="block" style="width:100%;">
            <a href="lan" class="blocka" style="background-color: #3191ED;">
                <span class="bighd">有线局域网（LAN）</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-resize-horizontal"></i>
                </div>
                <div class="biginfo">
                    <span>通过网线直连盒子LAN口，网线接口所在的局域网<br>网线连接可以直接访问互联网</span>
                </div>
            </a>
        </div>
    </div>
    <div class="block-justified">
        <div class="block" style="width:100%;">
            <a href="wlan" class="blocka" style="background-color: #FF8554;">
                <span class="bighd">无线局域网（WLAN）</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-transfer"></i>
                </div>
                <div class="biginfo">
                    <span>通过无线连接到盒子，无线接口所在的局域网<br>无线连接需要获取时长才能访问互联网</span>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
require("footer.php")
?>