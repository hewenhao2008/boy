#!/bin/sh
# $1 - the mac

. /wiware/bin/wicore.sh

mac=$(echo $1 |sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d')
if [ "x$mac" != "x" ]; then
	existed=$(uci get wipark.user.black 2>/dev/null|grep $mac|wc -l)
	if [ $existed -gt 0 ]; then
		uci del_list wipark.user.black=$mac
		uci commit
		destip=$(netget wlanip)
                destip=${destip:-10.10.10.1}
		iptables -t nat -D PREROUTING -p tcp -m mac --mac-source $mac -j DNAT --to-destination $destip:80
		wipark_log "Deleted mac:$mac from black mac list"
	fi
fi

