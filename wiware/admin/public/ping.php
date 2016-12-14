<?php
    exec("/wiware/bin/ping.sh", $output, $return_var);
    if( $return_var == 0 ){
        echo '外网连通';
    }
    else{
        echo '外网断开';
    }
?>
