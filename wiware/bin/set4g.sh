#!/bin/sh

uci delete wireless.sta 2>/dev/null

uci delete network.wan
uci set network.wan=interface
uci set network.wan.proto=dhcp
uci set network.wan.ifname=eth2.2

uci delete network.wwan 2>/dev/null
uci set network.wwan=interface
uci set network.wwan.proto=dhcp
uci set network.wwan.autodial=1

. /lib/ralink.sh

boardname=$(ralink_board_name)
if [ "$boardname" = "wr209afdd" ]; then
	uci set network.wwan.ifname='wwan0'
	uci set network.wwan.device='/dev/ttyUSB1'
else
	uci set network.wwan.ifname=eth0
	uci set network.wwan.device='/dev/ttyUSB0'
fi

uci set wipark.conf.nettype=4g
uci commit 

