#!/bin/sh

guestenabled=$(uci get wireless.guest 2>/dev/null)
if [ "x$guestenabled" = "x" ]; then
	wipark_log "setupssid Guest wireless is disabled, exit."
	exit 0
fi

apprefix=$(uci get wipark.conf.ssid_prefix 2>/dev/null)
apname=$(uci get wipark.conf.ssid_name 2>/dev/null)
randssid=$(uci get wipark.conf.randssid 2>/dev/null)
if [ ${randssid:-0} -gt 0 ]; then
        mactail=$(/wiware/bin/wiget idmac|awk '{print substr($1,9,4)}')
#	mactail=$(flash -r 30008 -c 2|tr -d " "|awk -F ":" {'print $2$3'}|tr -d "\n")
	ssid="$apprefix$apname"-"$mactail"
else
	ssid="$apprefix$apname"
fi

curssid=$(uci get wireless.guest.ssid)

if [ "$ssid" != "$curssid" ]; then
	uci set wireless.guest.ssid=${ssid:-WIPARK-Free}
	uci commit
fi

