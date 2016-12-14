<?php
require("header.php");
require("navbar.php");
?>

<style type="text/css">
    .container img{
        width: 100%;
        height:auto;
        width:expression(this.width > 710 ? "710px" : this.width);
    }
</style>

<div class="container" xmlns="http://www.w3.org/1999/html">
    <div class="alert alert-success">
        <p class="heading">恭喜您获取到<?=$hours?>小时上网时长</p>
    </div>
    <div>
        <img src="../assets/img/nav-top.jpg" />
    </div>
    <div class="form-wipark">
        <p>
            <a href="http://www.wipark.cn" class="btn btn-success">WiPark官方网站</a> &nbsp;&nbsp;
            <a href="<?=$homelink?>" type="button" class="btn btn-danger">本地资讯和内容</a>
        </p>
    </div>
    <table class="table table-condensed">
        <tr><td><a href="http://www.baidu.com" target="_blank">百度 baidu.com</a></td>
            <td><a href="http://www.sohu.com" target="_blank">搜狐网 sohu.com</a></td></tr>
        <tr><td><a href="http://www.sina.com.cn" target="_blank">新浪网 sina.com.cn</a></td>
            <td><a href="http://www.163.com" target="_blank">网易 163.com</a></td></tr>
        <tr><td><a href="http://www.qq.com" target="_blank">腾讯网 QQ.COM</a></td>
            <td><a href="http://www.douban.com" target="_blank">豆瓣 douban.com</a></td></tr>
    </table>

    <img src="../assets/img/nav-bottom.jpg">
</div>

<?php
require("footer.php")
?>