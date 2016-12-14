<table class="table table-striped table-hover">
    <tbody>
    <!--<tr><td>选择</td><td>SSID</td><td>BSS(MAC)</td><td>通道</td><td>加密方式</td><td>信号</td></tr>-->
    <tr><td>选择</td><td>SSID</td><td><span class="pull-right">加密/信号</span></td></tr>
    <?php
    $aplistStr=exec("/wiware/bin/scan.sh");
    $aplist=array();
    $aplist=explode('|',$aplistStr);
    $cnt=0;
    foreach($aplist as $ap)
    {
        if(empty($ap)){
            continue;
        }
        list($SIG,$BSS,$SSID,$channel,$AUTH)=explode(',',$ap);
        if( !isset($SSID) || empty($SSID) ) {
            continue;
        }

        if( $SIG >= 75 ){
            $sigimg='signal-1.png';
        }else if ($SIG >= 50 ){
            $sigimg='signal-2.png';
        }else if ($SIG >= 25 ){
            $sigimg='signal-3.png';
        }else{
            $sigimg='signal-4.png';
        }

        echo "<tr><td><input type='radio' name='apselect' id='apselect' value='$ap'></td>";
        echo "<td style='max-width:166px;word-wrap:break-word;'>$SSID</td><td><p class='pull-right'>";
        if( $AUTH != 'NONE' ){
            echo "<span class='glyphicon glyphicon-lock'></span> ";
        }
        echo "<img src='assets/img/$sigimg' /></p></td><tr>";
        $cnt++;
    }
    ?>
    </tbody>
</table>
