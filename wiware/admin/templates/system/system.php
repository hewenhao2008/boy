<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <div class="block-justified">
        <div class="block" style="width:50%;">
            <a href="sysinfo" class="blocka" style="background-color: #7CCF45;">
                <span class="bighd">系统信息</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-list-alt"></i>
                </div>
                <div class="biginfo">
                    <span>系统相关信息</span>
                </div>
            </a>
        </div>

        <div class="block" style="width:50%;">
            <a href="firmware" class="blocka" style="background-color: #FF8554;">
                <span class="bighd">在线固件升级</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-cloud-download"></i>
                </div>
                <div class="biginfo">
                    <span>自动检查新版本和升级</span>
                </div>
            </a>
        </div>
    </div>
    <div class="block-justified">
        <div class="block" style="width:50%;">
            <a href="setprinter" class="blocka" style="background-color: #FF667A;">
                <span class="bighd">打印服务设置</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-print"></i>
                </div>
                <div class="biginfo">
                    <span>开启/关闭打印机服务</span>
                </div>
            </a>
        </div>
        <div class="block" style="width:50%;">
            <a href="upgrade" class="blocka" style="background-color: #00C5D7;">
                <span class="bighd">本地固件升级</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-open"></i>
                </div>
                <div class="biginfo">
                    <span>从本地上传并升级固件</span>
                </div>
            </a>
        </div>
    </div>

    <div class="block-justified">
        <div class="block" style="width:100%;">
            <a href="about" class="blocka" style="background-color: #3191ED;">
                <span class="bighd">关于盒子</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-info-sign"></i>
                </div>

                <div class="biginfo">
                    <span>用心打造世界全新的无线网络服务!</span>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
require("footer.php")
?>