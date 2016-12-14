<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">访问控制</p>
<!--        <p>想要WiPark对用户做哪些访问控制？<br>可以根据实际需要进行灵活的设置。</p>-->
    <div class="block-justified">
        <div class="block" style="width:50%;">
            <a href="popwindow" class="blocka" style="background-color: #3191ED;">
                <span class="bighd">自动弹窗设置</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-comment"></i>
                </div>
                <div class="biginfo">
                    <span>设置用户连上盒子后是否自动弹出微站首页</span>
                </div>
            </a>
        </div>
        <div class="block" style="width:50%;">
            <a href="accesstime" class="blocka" style="background-color: #00C5D7;">
                <span class="bighd">上网时长设置</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-time"></i>
                </div>
                <div class="biginfo">
                    <span>设置用户每次可以获取的上网时长</span>
                </div>
            </a>
        </div>
    </div>

    <div class="block-justified">
        <div class="block" style="width:50%;">
            <a href="whitelist" class="blocka" style="background-color: #7CCF45;">
                <span class="bighd">白名单设置</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-leaf"></i>
                </div>
                <div class="biginfo">
                    <span>允许哪些设备可以自由畅游互联网</span>
                </div>
            </a>
        </div>
        <div class="block" style="width:50%;">
            <a href="blacklist" class="blocka" style="background-color: #F5B53F;">
                <span class="bighd">黑名单设置</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-list-alt"></i>
                </div>
                <div class="biginfo">
                     <span>禁止哪些设备访问互联网但允许访问本地内容</span>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
require("footer.php")
?>