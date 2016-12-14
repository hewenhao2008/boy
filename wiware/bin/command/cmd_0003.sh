#!/bin/sh
#--------
# config from server
#--------

. /wiware/bin/command/common.sh

error=''
oldIFS="$IFS"
IFS="#"
arr="$msgbody"
change_ssid=0
change_domainalies=0
change_lanip=0
change_accesstime=0
change_hasprinter=0
change_popup=0
for config in $arr
do
        /sbin/uci set $config 2>/dev/null && {
        	let change_ssid=$(echo "$config"|grep -E 'ap_prefix|ap_name|randssid'|wc -l)+$change_ssid
		let change_domainalies=$(echo "$config"|grep 'domain_alies'|wc -l)+$change_domainalies
		let change_lanip=$(echo "$config"|grep 'network.lan.ipaddr'|wc -l)+$change_lanip
		let change_accesstime=$(echo "$config"|grep 'accesstime'|wc -l)+$change_accesstime
		let change_hasprinter=$(echo "$config"|grep 'hasprinter'|wc -l)+$change_hasprinter
		let change_popup=$(echo "$config"|grep 'popup'|wc -l)+$change_popup
	}
done
#echo "change_ssid=$change_ssid, change_domainalies=$change_domainalies, change_lanip=$change_lanip, change_accesstime=$change_accesstime, change_hasprinter=$change_hasprinter"
IFS="$oldIFS"

#==== ssid ====
if [ $change_ssid -ne 0 ]; then
	wipark_log "cmd_0003 change_ssid"
	apprefix=$(/sbin/uci get wipark.conf.ssid_prefix)
	apname=$(/sbin/uci get wipark.conf.ssid_name)
	randssid=$(/sbin/uci get wipark.conf.randssid||echo '0')
	if [ $randssid -gt 0 ]; then
        	mactail=$(wiget idmac|awk '{print substr($1,9,4)}'|tr [a-z] [A-Z] )
        	ssid="$apprefix$apname"-"$mactail"
	else
        	ssid="$apprefix$apname"
	fi
	if [ ${#ssid} -gt 32 ]; then
		/sbin/uci revert wipark.conf.ssid_prefix
		/sbin/uci revert wipark.conf.ssid_name
		/sbin/uci revert wipark.conf.randssid
		error="The ssid ($ssid) is too long"
		wipark_log error "cmd_0003 $error"
	fi
fi
   
#==== domain_alies ====
if [ $change_domainalies -ne 0 ]; then
	wipark_log "cmd_0003 change_domainalies" 
	domain_alies=$(/sbin/uci get wipark.conf.domain_alies 2>/dev/null)
	isdomain=$(echo $domain_alies|sed -r '/^[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+){1,2}$/!d')
	if [ "x$isdomain" = "x" ]; then
		/sbin/uci revert wipark.conf.domain_alies
		error="The domain should look like (xxx.xxx.xxx)"
		wipark_log error "cmd_0003 $error"
	else
		domain=$(uci get wipark.conf.domain 2>/dev/null || echo 'm.wipark.cn')
		domain_alies=$(uci get wipark.conf.domain_alies 2>/dev/null||echo "u.$domaintail")
        	sed -ir "s/server_name.*$/server_name $domain $domain_alies;/" /etc/nginx/conf.d/main.conf
        	/bin/sh /wiware/bin/setuphosts.sh
        	/etc/init.d/dnsmasq restart
        	nginx -s reload 
	fi
fi

if [ $change_lanip -ne 0 ]; then
	wipark_log "cmd_0003 change_lanip"
	tmpip=$(/sbin/uci get network.lan.ipaddr)
	lanip=$(echo $tmpip|awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
	
	if [ "x$lanip" = "x" ]; then
		/sbin/uci revert network.lan.ipaddr
		error="The lanip ($tmpip) not allowed."
		wipark_log error "cmd_0003 $error"
	fi
fi

#==== accesstime ====
if [ $change_accesstime -ne 0 ]; then
	wipark_log "cmd_0003 change_accesstime" 
	accesstime=$(/sbin/uci get wipark.conf.accesstime)
	if [ $accesstime -lt 3600 ] || [ $accesstime -gt 604800 ]; then
		/sbin/uci revert wipark.conf.accesstime	
		error="The accesstime ($accesstime) should be in (3600~604800)"
		wipark_log error "cmd_0003 $error"
	fi
fi

#==== popup on associated ====
if [ $change_popup -ne 0 ]; then
	popup=$(/sbin/uci get wipark.conf.popwindow 2>/dev/null || echo 0)
	if [ $popup -gt 0 ]; then
		/bin/sh /wiware/bin/setpopwindow.sh on
	else
		/bin/sh /wiware/bin/setpopwindow.sh off
	fi
	nginx -s reload
fi

/sbin/uci commit

#==== take effect ====
if [ $change_ssid -ne 0 ]; then
	#/sbin/uci set wireless.@wifi-iface[0].ssid="$ssid"
	/sbin/uci set wireless.guest.ssid="$ssid"
   	/sbin/wifi restart
fi

if [ "x$change_lanip" != "x" ]; then 
	/wiware/bin/setuphosts.sh
fi

#==== report ====
if [ "x$error" = "x" ]; then
   	report "$S_SUCCESS#success"
else
   	report "$S_FAILED#$error"
fi

if [ -f /wiware/www/__SYS__/hatch.php ]; then
	php-cgi /wiware/www/__SYS__/hatch.php force &
fi

