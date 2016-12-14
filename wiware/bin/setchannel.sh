#!/bin/sh
if [ $# -lt 1 ]; then
	exit 1
fi

channel="$1"
ht=${2:-20}

#nettype=$(uci get wipark.conf.nettype)
#if [ "$nettype" = "sta" ]; then
#	exit 1	
#fi


if [ "$channel" = "auto" ]; then
	uci set wireless.@wifi-device[0].channel=auto
	uci commit
	iwpriv apcli0 set Channel="11" &
elif [ $channel -gt 0 ] && [ $channel -le 15 ]; then 
	uci set wireless.@wifi-device[0].channel=$channel
	uci commit
	iwpriv apcli0 set Channel="$channel" &
else
	exit 1
fi

oldht=$(uci get wireless.@wifi-device[0].ht)
if [ "$oldht" != "$ht" ]; then
	uci set wireless.@wifi-device[0].ht="$ht"
	(sleep 2 && wifi restart) &
fi

