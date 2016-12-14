<?php
require("header.php");
require("navbar.php");
?>
<div class="container">
    <p class="heading">白名单管理</p>
    <form class="form-wipark">
        <input type="hidden" name="mymac" value="<?=$mymac?>" id="mymac" >
        <p><a href="accessctrl" class="btn btn-lg btn-default btn-block">返回</a></p>

        <?php if (isset($flash['info'])): ?>
            <p class="text-success"><?= $flash['info'] ?></p>
        <?php endif ?>

        <table class="table table-striped">
            <tr><th>#</th><th>MAC地址</th><th>删除</th></tr>
            <?php
                $num=0;
                $macs=exec("uci get wipark.user.white 2>/dev/null");
                $macarray=explode(" ", $macs);
                foreach($macarray as $value){
                    $macaddr=strtoupper(trim($value));
                    if( isset($macaddr) && !empty($macaddr) ){
                        $num = $num + 1;
                        echo "<tr id='$num'><td><b>$num</b></td><td><b>$macaddr</b></td><td><button type='button' class='btn btn-danger btn-sm' onClick='delmac(\"$macaddr\", $num)' >删除</button></td></tr>";
                    }
                }
            ?>
        </table>

        <label for="mac" class="control-label">添加MAC：</label>
        <input type="text" class="form-control  input-lg" id="mac" name="mac" placeholder="00:00:00:00:00:00">
        <button type="button" class="btn btn-primary" onClick="fillmymac()">点此填入本机MAC</button>
        <p></p>
        <button type="button" class="btn btn-lg btn-danger btn-block" onClick="addmac()">添加</button>
    </form>
<br/>
    <table class="table table-striped">
        <tr><th colspan="3" class="text-center"> 当前在线设备 </th></tr>
        <?php
            $dhcplist=file('/tmp/dhcp.leases');
            $num=0;
            foreach($dhcplist as $item){
                $dhcpclient=explode(" ",$item);
                $num += 1;
                $mac=strtoupper($dhcpclient[1]);
                $ip=$dhcpclient[2];
                $note=$dhcpclient[3];

                if(in_array($mac, $macarray)){
                    echo "<tr><td><b>$mac</b></td><td>$note</td><td id='$num'>已添加</td></tr>";
                }
                else {
                    echo "<tr><td><b>$mac</b></td><td>$note</td><td id='$num'><button type='button' class='btn btn-danger btn-sm' onClick='adddhcpmac(\"$mac\", $num)' >添加</button></td></tr>";
                }
            }
        ?>
    </table>
</div>

<script type="text/javascript">
    function fillmymac(){
        obj=document.getElementById("mac");
        value=document.getElementById("mymac").value;
        obj.value=value;
        return true;
    }

    function delmac( macaddr,trid ){
        if( ! confirm('确定从白名单删除'+macaddr+'吗?') ){
            return;
        }
        $.ajax({
            url: 'whitelist/del/'+macaddr,
            type: 'GET',
            dataType: 'text',
            timeout: 100000, //毫秒
            error: function(){
                alert("从白名单中删除MAC失败！");
            },
            success: function(resdata){
                $("tr#"+trid).remove();
            }
        });
    }

    function adddhcpmac( macaddr,tdid ){
        if( ! confirm('确定添加'+macaddr+'到白名单吗?') ){
            return;
        }
        $.ajax({
            url: 'whitelist/add/'+macaddr,
            type: 'GET',
//            data: 'op=add&mac='+mac,
            dataType: 'text',
            timeout: 100000, //毫秒
            error: function(){
                alert("向白名单中添加MAC失败！");
            },
            success: function(resdata){
//                location.href='whitelist';
                $("td#"+tdid).html('已添加');
            }
        });
    }

    function addmac(){
        obj=document.getElementById("mac");
        mac=obj.value;
        var temp = /[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}:[A-Fa-f0-9]{2}/;
        if( !temp.test(mac) ){
            alert("MAC地址输入有误，正确格式为00:00:00:00:00:00，请检查！");
            obj.focus();
            return false;
        }

        if( ! confirm('确定要添加'+mac+'到白名单吗?') ){
            return;
        }

        $.ajax({
            url: 'whitelist/add/'+mac,
            type: 'GET',
//            data: 'op=add&mac='+mac,
            dataType: 'text',
            timeout: 100000, //毫秒
            error: function(){
                alert("向白名单中添加MAC失败！");
            },
            success: function(resdata){
                location.href='whitelist';
            }
        });
    }
</script>

<?php
require("footer.php")
?>
