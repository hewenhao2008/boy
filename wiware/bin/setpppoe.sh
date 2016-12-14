#!/bin/sh
#paralist="$*"

if [ $# -lt 2 ]; then
	echo "$0 <username> <password>"
	exit
fi

username=$1
password=$2
if [ "x$username" = "x" ]; then
	exit 1
fi
if [ "x$password"= "x" ]; then
	exit 1
fi

#uci delete network.wwan 2>/dev/null
uci delete wireless.sta 2>/dev/null

uci delete network.wan
uci set network.wan=interface
uci set network.wan.proto=pppoe
uci set network.wan.ifname=eth2.2
uci set network.wan.username="$username"
uci set network.wan.password="$password"

uci set wipark.conf.nettype=pppoe
uci commit 

