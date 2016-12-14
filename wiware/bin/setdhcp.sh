#!/bin/sh

#paralist="$*"

#uci delete network.wwan 2>/dev/null
uci delete wireless.sta 2>/dev/null

uci delete network.wan
uci set network.wan=interface
uci set network.wan.proto=dhcp
uci set network.wan.ifname=eth2.2

if [ $# -ge 1 ]; then
	dns=$(echo $1|awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
	[ "x$dns" != "x" ] && uci set network.wan.dns=$dns
fi

uci set wipark.conf.nettype=dhcp
uci commit 

