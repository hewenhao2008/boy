#!/bin/sh
# $1 - the mac

. /wiware/bin/wicore.sh

mac=$(echo $1 |sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d')
if [ "x$mac" != "x" ]; then
	existed=$(uci get wipark.user.white 2>/dev/null|grep $mac|wc -l)
	if [ $existed -eq 0 ]; then
		if [ "x$(uci get wipark.user 2>/dev/null)" = "x" ]; then
			uci set wipark.user=wipark
		fi
		uci add_list wipark.user.white=$mac
		uci commit
		iptables -t nat -D PREROUTING -m mac --mac-source $mac -j ACCEPT 2>/dev/null
		iptables -t nat -I PREROUTING -m mac --mac-source $mac -j ACCEPT
		wipark_log "Added mac:$mac to white mac list"
	fi
fi

