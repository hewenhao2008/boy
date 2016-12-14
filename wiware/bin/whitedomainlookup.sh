#!/bin/sh

. /wiware/bin/wicore.sh

whitedomainlist='/wiware/etc/white.domain.list'
whiteiplist='/wiware/etc/white.ip.list'
tmpwhiteiplist='/tmp/tmpwhite.ip.list'
let tmpipcount=0

[ ! -f $whitedomainlist ] && touch $whitedomainlist
[ ! -f $whiteiplist ] && touch $whiteiplist
[ ! -f $tmpwhiteiplist ] && echo "" > $tmpwhiteiplist

boxip=$(uci get network.wlan.ipaddr || echo "10.10.10.1")

domainlist=$(cat $whitedomainlist)
for domain in ${serverlist}; 
do
	wipark_log "lookup domain: $domain"
	iplist=$(nslookup $domain|grep Address|grep -v localhost|awk '{print $3}')
	for domainip in ${iplist};
	do
		ip=$(echo $domainip|awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
		if [ "x$ip" != "x" ] && [ "$ip" != "$boxip" ];then
			echo "$ip" >> $tmpwhiteiplist
			wipark_log "found ip: $ip"
			let tmpipcount++
		fi
	done
done

if [ $tmpipcount -eq 0 ]; then 
	exit 0
fi

#tmpwhiteiplist - whiteiplist
diffaddlist=$(grep -F -v -f $whiteiplist $tmpwhiteiplist | sort | uniq)
for addip in ${diffaddlist}; 
do
	wipark_log "add white ip: $addip"
	iptables -t nat -I PREROUTING -d $addip -j ACCEPT
done

#whiteiplist - tmpwhiteiplist
diffdellist=$(grep -F -v -f $tmpwhiteiplist $whiteiplist | sort | uniq)
for delip in ${diffdellist}; 
do
	wipark_log "delete white ip: $delip"
	iptables -t nat -D PREROUTING -d $delip -j ACCEPT
done

