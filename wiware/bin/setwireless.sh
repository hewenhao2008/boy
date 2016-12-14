#!/bin/sh

if [ $# -lt 6 ]; then
        echo "$0 <office|guest> <enabled> <apprefix> <apname> <encryption> <key> <channel>"
        exit 1
fi

. /wiware/bin/wicore.sh

wipark_log "setwireless.sh $*"

witch="$1"
enabled="$2"
apprefix="$3"
apname="$4"
encryption="$5"
key="$6"
channel="$7"

if [ $witch = 'office' ]; then
	if [ "$enabled" = "0" ]; then
		uci delete wireless.office
	else
		if [ "$encryption" != "none" ] && [ ${#key} -ge 8 ] && [ ${#key} -lt 64 ]; then
			encryption='psk+psk2'
		else
			wipark_log "setwireless guest: encryption=$encryption so to none"
			encryption='none'
			key='none'
		fi

		if [ ${#apname} -gt 32 ]; then
			apname=$(uci get wireless.office.ssid)
		fi

	 	wipark_log "setwireless office ssid to: [$apname]"
		uci set wireless.office=wifi-iface
		uci set wireless.office.device=ra0
        	uci set wireless.office.network=lan
		uci set wireless.office.mode=ap
		uci set wireless.office.ssid="$apname"
        	uci set wireless.office.encryption="$encryption"
        	uci set wireless.office.key="$key"
	fi
elif [ $witch = 'guest' ]; then
	uci set wipark.conf.randssid=0
	if [ "$apprefix" != "none" ]; then
		uci set wipark.conf.ssid_prefix="$apprefix"
	fi
	if [ "$apname" != "none" ]; then
		uci set wipark.conf.ssid_name="$apname"
	fi

	apprefix=$(uci get wipark.conf.ssid_prefix)
	apname=$(uci get wipark.conf.ssid_name)
	randssid=$(uci get wipark.conf.randssid 2>/dev/null|| echo '0')
	if [ $randssid -gt 0 ]; then
		mactail=$(/wiware/bin/wiget idmac|awk '{print substr($1,9,4)}'|tr [a-z] [A-Z] )
		ssid="$apprefix$apname"-"$mactail"
	else
		ssid="$apprefix$apname"
	fi

	if [ ${#ssid} -gt 32 ]; then
		ssid=$(uci get wireless.guest.ssid)
	fi

	 wipark_log "setwireless guest ssid to: [$ssid]"
	
	if [ "$encryption" != "none" ] && [ ${#key} -ge 8 ] && [ ${#key} -lt 64 ]; then
		encryption='psk+psk2'
	else
		wipark_log "setwireless guest: encryption=$encryption so to none"
		encryption='none'
		key='none'
	fi

	uci set wireless.guest=wifi-iface
	uci set wireless.guest.device=ra0
        uci set wireless.guest.network=wlan
        uci set wireless.guest.mode=ap
	uci set wireless.guest.ssid="$ssid"
	uci set wireless.guest.encryption="$encryption"
	uci set wireless.guest.key="$key"
	uci set wireless.guest.wpa_group_rekey=0
	uci set wireless.guest.wpa_pair_rekey=0
	uci set wireless.guest.wpa_master_rekey=0
else
	echo "$0 <office|guest> <apprefix> <apname> <encryption> <key> <channel> <enabled>"
	exit 1
fi

nettype=$(uci get wipark.conf.nettype)
if [ "$nettype" != "sta" ] && [ "x$channel" != "x" ]; then
	if [ "$channel" = "auto" ]; then
		uci set wireless.@wifi-device[0].channel=auto
		iwpriv apcli0 set Channel='11'
	elif [ $channel -gt 0 ] && [ $channel -le 15 ]; then
		uci set wireless.@wifi-device[0].channel=auto
		iwpriv apcli0 set Channel="$channel"
	fi
fi

uci reorder wireless.guest=1
uci reorder wireless.sta=3

uci commit

