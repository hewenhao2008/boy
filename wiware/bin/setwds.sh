#!/bin/sh
. /wiware/bin/wicore.sh

# for wds connection
#parameters: 	$1:ssid
#		$2:bssid
#		$3:channel
#		$4:auth
#		$5:key

wipark_log "$*"
if [ $# -lt 4 ]; then
	wipark_log error "setwds numbers of parameters error"
fi

ssid="$1"
bssid="$2"
channel="$3"
auth="$4"
key="$5"

#check ssid
if [ $(expr length $ssid) -gt 32 ];then
	wipark_log error "setwds ssid($ssid) format error"
	exit 1
fi
#check whether mac addr is valid or not
regex='[a-f0-9][a-f0-9]:[a-f0-9][a-f0-9]:[a-f0-9][a-f0-9]:[a-f0-9][a-f0-9]:[a-f0-9][a-f0-9]:[a-f0-9][a-f0-9]'
if [ $(expr length $bssid) -eq 17 ]&&[ $(echo $bssid|grep -Eqi $regex && echo YES || echo NO) == "NO" ]; then
	wipark_log error "setwds bssid($bssid) error"
	exit 1
fi
#check channel 
if [ $channel -lt 1 ] || [ $channel -gt 13 ];then
	wipark_log error "setwds channel($3) error"
	exit 1
fi

#check auth:NONE WPAPSK WPA2PSK

if [ $(echo "$auth" | grep -E "WPA|PSK") ]; then
	if [ $(expr length $key) -lt 8 ] || [ $(expr length $key) -gt 64 ]; then
		wipark_log error "setwds auth($auth) key($key) length error"
		exit 1
       	fi
elif [ $(echo "$auth" | grep "WEP") ]; then
	if [ $(expr length $key) -ne 10 ] && [ $(expr length $key) -ne 5 ]; then
		wipark_log error "setwds auth($auth) key($key) length error"
		exit 1
       	fi
elif [ $(echo "$auth" | grep "NONE") ]; then
	key="none"
else
	wipark_log error "setwds encryption($auth) error"
	exit 1
fi

uci set wireless.ra0.channel="$channel"
#uci set wireless.guest.cliname=apcli0

uci set wireless.sta=wifi-iface
uci set wireless.sta.ssid="$ssid"
uci set wireless.sta.bssid="$bssid"
uci set wireless.sta.device=ra0
uci set wireless.sta.mode=sta
uci set wireless.sta.network=wwan
uci set wireless.sta.encryption="$auth"
uci set wireless.sta.key="$key"

uci delete network.wwan 2>/dev/null
uci set network.wwan=interface
uci set network.wwan.ifname=apcli0
uci set network.wwan.proto=dhcp

uci set wipark.conf.nettype=sta
uci reorder wireless.sta=3
uci commit

iwpriv apcli0 set Channel="$channel"

#iwpriv apcli0 set ApCliEnable=0
#iwpriv apcli0 set ApCliSsid=$1
#iwpriv apcli0 set ApCliBssid=$2
#iwpriv apcli0 set ApCliAuthMode=$authmode
#iwpriv apcli0 set ApCliEncrypType=$encryptype
#iwpriv apcli0 set ApCliEnable=1

#ifconfig apcli0 up
/etc/init.d/network restart
