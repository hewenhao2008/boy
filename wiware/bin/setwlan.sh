#!/bin/sh
if [ $# -lt 2 ]; then
        echo "$0 <wlanip> <wlannetmask>"
        exit 1
fi

get_mask(){
	mask="$1"
	a=$(printf "0X%02X%02X%02X%02X\n" $(echo "$mask"| awk -F'.' '{print $1" "$2" "$3" "$4}'))
	let b=$a-1
	let c=$(($a|$b))
	if [ $c -eq $((0xFFFFFFFF)) ]; then
		echo $mask
	else
		echo '255.255.255.0'
	fi
}

wlanip=$(echo $1|awk -F'.' '$1==10 && $2<=255 && $2>=0 && $3<=255 && $3>=0 && $4<=255 && $4>=0 {print $0}')
netmask=$(echo $2|awk -F'.' '$1<=255 && $1>=0 && $2<=255 && $2>=0 && $3<=255 && $3>=0 && $4<255 && $4>=0 {print $0}')

[ "x$wlanip" = "x" ] && exit 1

netmask=$(get_mask ${netmask:-255.255.255.0})
/sbin/uci set network.wlan.ipaddr="$wlanip"
/sbin/uci set network.wlan.netmask="$netmask"
/sbin/uci commit

/bin/sh /wiware/bin/setuphosts.sh >/dev/null 2>&1 &
