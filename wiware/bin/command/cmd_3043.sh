#!/bin/sh
# add black mac list

. /wiware/bin/command/common.sh

wipark_log "$0 add black mac list." 

oldIFS="$IFS"
IFS="#"
arr="$msgbody"
for item in $arr
do
	mac=$(echo $item |sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d')
	if [ "x$mac" != "x" ]; then
		iptables -t nat -D PREROUTING -p tcp -m mac --mac-source $mac -j DNAT --to-destination $destip:80
		sed -i '/$mac/d' /wiware/etc/black.mac.list

		iptables -t nat -I PREROUTING -p tcp -m mac --mac-source $mac -j DNAT --to-destination $destip:80
		echo "$mac" >> /wiware/etc/black.mac.list
	else
		wipark_log error "$0 mac:$item was not added to black mac list. format err."
	fi
done
IFS="$oldIFS"
report $S_SUCCESS

