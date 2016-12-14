<?php
require("header.php");
require("navbar.php");
?>

<div class="container">
    <p class="heading">客户端DHCP列表</p>
    <div class="panel panel-default">
<!--        <div class="panel-heading">客户端DHCP列表</div>-->
        <table class="table table-hover">
            <tbody>
            <tr class="active"><th>#</th><th>客户端MAC</th><th>IP地址</th><th>标识</th></tr>
            <?php
            $dhcplist=file('/tmp/dhcp.leases');
            $num=0;
            foreach($dhcplist as $item){
                $dhcpclient=explode(" ",$item);
                $num += 1;
//                $expire=date("Y-m-d H:i:s", $dhcpclient[0]);
//                $diff=$dhcpclient-time();
                $mac=$dhcpclient[1];
                $ip=$dhcpclient[2];
                $note=$dhcpclient[3];
                echo "<tr id='$num'><td><b>$num</b></td><td><b>$mac</b></td><td>$ip</td><td>$note</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>


</div>

<?php
require("footer.php")
?>