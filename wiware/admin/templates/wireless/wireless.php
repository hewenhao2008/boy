<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">无线管理</p>

    <div class="block-justified">
        <div class="block" style="width:100%;">
            <a href="wirelessguest" class="blocka" style="background-color: #FF8554;">
                <span class="bighd">访客无线网络
                    <?php
//                    if($guestenabled=='0'){
//                        echo '<span class="label label-default">未启用</span>';
//                    }else{
//                        echo '<span class="label label-success">已启用</span>';
//                    }
//                    ?>
                </span>
                <div class="bg">
                    <?=$guestssid?>
                </div>
                <div class="biginfo">
                    <span>开放给访客使用的无线网络<br>需要认证才能访问互联网</span>
                </div>
            </a>
        </div>
    </div>

    <div class="block-justified">
        <div class="block" style="width:100%;">
            <a href="wirelessoffice" class="blocka" style="background-color: #3191ED;">
                <span class="bighd">办公无线网络
                    <?php
                    if($officeenabled=='0'){
                        echo '<span class="label label-default">未启用</span>';
                    }else{
                        echo '<span class="label label-success">已启用</span>';
                    }
                    ?>
                </span>
                <div class="bg">
                    <?=$officessid?>
                </div>
                <div class="biginfo">
                    <span>通常加密个人自用的无线网络<br>可以直接访问互联网</span>
                </div>
            </a>
        </div>
    </div>

    <div class="block-justified">
        <div class="block" style="width:100%;">
            <a href="setchannel" class="blocka" style="background-color: #9A59EB;">
                <span class="bighd">无线信道</span>
                <div class="bg">
                    <i class="glyphicon glyphicon-signal"></i>
                </div>
                <div class="biginfo">
                    <span>设置盒子无线信号的信道</span>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
require("footer.php")
?>