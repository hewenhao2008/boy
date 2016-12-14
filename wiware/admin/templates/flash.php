<?php
require("header.php");
require("navbar.php");
?>

<style type="text/css">
    .loading-indicator {
        height: 80px;
        width: 80px;
        background: url('assets/img/loading.circles.gif');
        background-repeat: no-repeat;
        background-position: center center;
    }
    .loading-indicator-overlay {
        background-color: #FFFFFF;
        opacity: 0.4;
        filter: alpha(opacity = 40);
    }
</style>

<div class="container">
    <p class="heading">操作结果</p>
    <div id="restarting">
        <div class="jumbotron">
            <?php if (isset($flash['info']) ):?>
                <p class="text-danger"><?=$flash['info']?></p>
            <?php endif ?>
            <p>三秒钟后，自动返回...</p>
        </div>
    </div>
</div>

<script src="assets/js/jquery.js"></script>
<script src="assets/js/jquery.showloading.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        jQuery('#restarting').showLoading();
        setTimeout("redirect()",3000);
    });

    function redirect(){
        location.href = '<?=(isset($redirect))?$redirect : "/";?>';
    }

</script>

<?php
require("footer.php")
?>
