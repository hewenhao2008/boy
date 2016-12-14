#!/bin/sh
# $1 - the mac

. /wiware/bin/wicore.sh

mac=$(echo $1 |sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d')
if [ "x$mac" != "x" ]; then
	existed=$(uci get wipark.user.white 2>/dev/null|grep $mac|wc -l)
	if [ $existed -gt 0 ]; then
		uci del_list wipark.user.white=$mac
		uci commit
		iptables -t nat -D PREROUTING -m mac --mac-source $mac -j ACCEPT 2>/dev/null
		wipark_log "Deleted mac:$mac from white mac list"
	fi
fi

