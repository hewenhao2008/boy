#!/bin/sh
# add site ip list

. /wiware/bin/command/common.sh

wipark_log "$0 add site internet ip list." 

oldIFS="$IFS"
IFS="#"
arr="$msgbody"
for item in $arr
do
	tmpip=$(echo $item|awk -F'/' '{print $1}')
	mask=$(echo $item|awk -F'/' '{print $2}')
	
	ip=$(echo $tmpip|awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
	if [ "x$ip" != "x" ]; then
		if [ "x$mask" != "x" ] && [ $mask -gt 1 ] && [ $mask -lt 32 ]; then
			ip=$item
		fi
		existed=$(uci get wipark.site.ip |grep "$ip" |wc -l)
		if [ $existed -eq 0 ]; then
			uci add_list wipark.site.ip=$ip
			iptables -t nat -I PREROUTING -d $ip -j ACCEPT
		fi
	else
		wipark_log error "$0 ip:$item was not added to site ip. format err."
	fi
done
uci commit
IFS="$oldIFS"
report $S_SUCCESS

