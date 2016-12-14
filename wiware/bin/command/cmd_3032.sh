#!/bin/sh
# set whether ban all ports beside 80,8080 that will always be ban

. /wiware/bin/command/common.sh

banall=$(echo $msgbody|awk -F '#' '{print $1}')
[ "$banall" != "0" ] banall='1'
uci set wipark.conf.banport=$banall
uci commit
sh /wiware/bin/netshell/fw_add.sh

report $S_SUCCESS

