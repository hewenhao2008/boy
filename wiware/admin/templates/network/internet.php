<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">连接互联网设置</p>
    <p>想要盒子怎样连接到互联网？<br>可根据实际需要选择一种方式进行设置。</p>
    <div class="block-justified">
        <div class="block" style="width:50%;">
            <a href="setdhcp" class="blocka" style="background-color: #3191ED;">
                <span class="bighd">动态IP方式</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-transfer"></i>
                </div>
                <div class="biginfo">
                    <span>DHCP获取动态IP地址</span>
                </div>
            </a>
        </div>
        <div class="block" style="width:50%;">
            <a href="setpppoe" class="blocka" style="background-color: #767CE1;">
                <span class="bighd">宽带拨号方式</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-phone-alt"></i>
                </div>
                <div class="biginfo">
                    <span>ADSL宽带上网</span>
                </div>
            </a>
        </div>
    </div>

    <div class="block-justified">
        <div class="block" style="width:50%;">
            <a href="setstatic" class="blocka" style="background-color: #9A59EB;">
                <span class="bighd">静态IP方式</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-pushpin"></i>
                </div>
                <div class="biginfo">
                    <span>固定IP设置连网</span>
                </div>
            </a>
        </div>

        <div class="block" style="width:50%;">
            <a href="setwds" class="blocka" style="background-color: #FF667A;">
                <span class="bighd">无线中继方式</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-link"></i>
                </div>
                <div class="biginfo">
                    <span>中继其他无线信号</span>
                </div>
            </a>
        </div>
    </div>

    <?php if ($has4gmodule): ?>
        <div class="block-justified">
            <div class="block" style="width:100%;">
                <a href="set4g" class="blocka" style="background-color: #FF8554;">
                    <span class="bighd">4G数据联网</span>
                    <div class="bg">
                        <i class="glyphicon glyphicon-signal"></i>
                    </div>
                    <div class="biginfo">
                        <span>通过4G拨号连网</span>
                    </div>
                </a>
            </div>
        </div>
    <?php endif ?>
</div>


<?php
require("footer.php")
?>