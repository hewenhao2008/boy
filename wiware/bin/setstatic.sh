#!/bin/sh

if [ $# -lt 2 ]; then
	echo "Usage: $0 <ip> <gateway> <netmask> [dns]"
	exit 1
fi
get_mask(){
	mask="$1"
	a=$(printf "0X%02X%02X%02X%02X\n" $(echo "$mask"| awk -F'.' '{print $1" "$2" "$3" "$4}'))
	let b=$a-1
	let c=$(($a|$b))
	if [ $c == $((0xFFFFFFFF)) ]; then
		echo $mask
	else
		echo '255.255.255.0'
	fi
}

ip=$(echo $1|awk -F'.' '$1<=255 && $1>=0 && $2<=255 && $2>=0 && $3<=255 && $3>=0 && $4<=255 && $4>=0 {print $0}')
gateway=$(echo $2|awk -F'.' '$1<=255 && $1>=0 && $2<=255 && $2>=0 && $3<=255 && $3>=0 && $4<=255 && $4>=0 {print $0}')
netmask=$(get_mask $3)
dns=$(echo $4|awk -F'.' '$1<=255 && $1>=0 && $2<=255 && $2>=0 && $3<=255 && $3>=0 && $4<=255 && $4>=0 {print $0}')

[ "x$ip" = "x" ] && exit 1
[ "x$gateway" = "x" ] && exit 1

#uci delete network.wwan 2>/dev/null
uci delete wireless.sta 2>/dev/null
uci delete network.wan
uci set network.wan=interface
uci set network.wan.ifname=eth2.2
uci set network.wan.proto=static
uci set network.wan.ipaddr=$ip
uci set network.wan.netmask=$netmask
uci set network.wan.gateway=$gateway
uci set network.wan.dns=$dns

uci set wipark.conf.nettype=static
uci commit

