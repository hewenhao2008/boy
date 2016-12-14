#!/bin/sh
# del white mac list

. /wiware/bin/command/common.sh

wipark_log "$0 del white mac list." 

oldIFS="$IFS"
IFS="#"
arr="$msgbody"
for item in $arr
do
	mac=$(echo $item |sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d')
	if [ "x$mac" != "x" ]; then
		iptables -t nat -D PREROUTING -m mac --mac-source $mac -j ACCEPT
		sed -i '/$mac/d' /wiware/etc/white.mac.list
		ls /tmp/-.$mac.*|xargs rm -f
	else
		wipark_log error "$0 mac:$item was not added to white mac list. format err."
	fi
done
IFS="$oldIFS"

report $S_SUCCESS

