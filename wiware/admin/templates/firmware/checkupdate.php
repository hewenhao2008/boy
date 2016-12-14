<style>
    .new-title
    {
        font-size: 28px;
        color: #222;
        line-height:24px; /* This needs to be the same as the 2nd color stop position in -webkit-repeating-linear-gradient of error-title.error-text. */
        font-weight: bold;
    }
    .new-text-inner-shadow {
        /*position: absolute;*/
        top: 2px;
        left: 0;
        right: 0;
        color: rgba(0, 0, 0, 0.75);
    }
</style>
<form action="doupdate" method="post">
    <input type="hidden" name="downloadurl" value="<?=$downloadurl?>" id="downloadurl">
    <input type="hidden" id="checksum" name="checksum" value="<?=$checksum?>">
    <input type="hidden" id="newversion" name="newversion" value="<?=$newversion?>">

    <div id="foundnewversion" class="panel panel-default">
        <div class="panel-heading">
<!--            <div class="pull-right">-->
<!--                <button type="button" id="changelog" class="btn btn-primary btn-xs" role="button">查看更新日志</button>-->
<!--            </div>-->
            <h3 class="panel-title"><strong>发现新的版本</strong></h3>
        </div>
        <div class="panel-body">
            <div style="text-align: center;margin-bottom: 15px;">
                <span class="new-title new-text-inner-shadow"> <?=$osname?> <?=$newversion?></span>
                <br>
                <span>发布日期：<?=$releasetime?></span>
            </div>
            <button type="submit" class="btn btn-danger btn-lg btn-block">点击开始更新</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $('#changelog').click(function () {
        $("#bodycontent").load("changelog/<?=$newversion?>", function (response, status, xhr) {
            $('#myModal').modal('show');
        });
    });
</script>