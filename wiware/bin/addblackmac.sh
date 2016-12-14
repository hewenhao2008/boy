#!/bin/sh
# $1 - the mac

. /wiware/bin/wicore.sh

mac=$(echo $1 |sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d')
if [ "x$mac" != "x" ]; then
	existed=$(uci get wipark.user.black 2>/dev/null|grep $mac|wc -l)
	if [ $existed -eq 0 ]; then
		if [ "x$(uci get wipark.user 2>/dev/null)" = "x" ]; then
			uci set wipark.user=wipark
		fi
		uci add_list wipark.user.black=$mac
		uci commit
		destip=$(netget wlanip)
		destip=${destip:-10.10.10.1}
		iptables -t nat -D PREROUTING -p tcp -m mac --mac-source $mac -j DNAT --to-destination $destip:80 2>/dev/null
		iptables -t nat -I PREROUTING -p tcp -m mac --mac-source $mac -j DNAT --to-destination $destip:80
		wipark_log "Added mac:$mac to black mac list"
	fi
fi

