<?php
$progressfile="/tmp/firmware.progress";
$progress=exec("cat $progressfile");
if(isset($progress)){
    echo $progress;
}
?>