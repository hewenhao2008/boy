#!/bin/sh
source /lib/functions/network.sh
source /wiware/bin/wicore.sh

if ! network_get_ipaddr lanip "lan"; then
	lanip=$(/sbin/uci get network.lan.ipaddr 2>/dev/null)
	lanip=${lanip:-192.168.88.253}
       	wipark_log error "netget lanip failed. default to $lanip"
fi

if ! network_get_ipaddr wlanip "wlan"; then
	wlanip=$(/sbin/uci get network.wlan.ipaddr 2>/dev/null)
	wlanip=${wlanip:-10.10.10.1}
	wipark_log error "netget wlan ip failed. default to $wlanip"
fi

network_get_ipaddr wanip "wan"
network_get_ipaddr wwanip "wwan"

lanseg=$(echo $lanip|awk -F'.' '{print $1"."$2"."$3}')
wlanseg=$(echo $wlanip|awk -F'.' '{print $1"."$2"."$3}')

wanseg=$(echo $wanip|awk -F'.' '{print $1"."$2"."$3}')
wwanseg=$(echo $wwanip|awk -F'.' '{print $1"."$2"."$3}')

flag=0
if [ "$lanseg" = "$wanseg" ]; then
	wipark_log "checklanip: lanip($lanip) and wanip($wanip) have same subnet($lanseg), thats not OK"
	lanip=$(echo $lanip|awk -F'.' '{print $1"."$2"."($3+1)%255"."$4}')
	wipark_log "checklanip: auto change lanip to ($lanip)"
	uci set network.lan.ipaddr="$lanip"
	uci commit
	flag=$(($flag+1))
elif [ "$lanseg" = "$wwanseg" ]; then
	wipark_log "checklanip: lanip($lanip) and wwanip($wwanip) is the same subnet($lanseg)"
	lanip=$(echo $lanip|awk -F'.' '{print $1"."$2"."($3+1)%255"."$4}')
	wipark_log "checklanip: change lanip to ($lanip)"
	uci set network.lan.ipaddr="$lanip" 
	uci commit
	flag=$(($flag+1))
else
	wipark_log "checklanip: lanip($lanip) is in defferent subnet from wwanip($wwanip) and wanip($wanip), thats OK."
fi

if [ "$wlanseg" = "$wanseg" ]; then
	wipark_log "checklanip: wlanip($wlanip) and wanip($wanip) is the same subnet($wlanseg)"
	wlanip=$(echo $wlanip|awk -F'.' '{print $1"."$2"."($3+1)%255"."$4}')
	wipark_log "checklanip: change wlanip to ($wlanip)"
	uci set network.wlan.ipaddr="$wlanip" 
	uci commit
	flag=$(($flag+1))
elif [ "$wlanseg" = "$wwanseg" ]; then
	wipark_log "checklanip: wlanip($wlanip) and wwanip($wwanip) is the same subnet($wlanseg)"
	wlanip=$(echo $wlanip|awk -F'.' '{print $1"."$2"."($3+1)%255"."$4}')
	wipark_log "checklanip: change wlanip to ($wlanip)"
	uci set network.wlan.ipaddr="$wlanip"
	uci commit
	flag=$(($flag+1))
else
	wipark_log "checklanip: wlanip($wlanip) is in defferent subnet from wwanip($wwanip) and wanip($wanip), thats OK."
fi

if [ $flag -ne 0 ]; then
	/etc/init.d/network restart
	sh /wiware/bin/setuphosts.sh
fi

