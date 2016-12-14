#!/bin/sh

#-------
#dns block on or off
#-------
pinging=$(ps |grep -E 'ping.sh|checknet.sh' |grep -v grep |wc -l)
if [ $pinging -gt 2 ]; then
	exit 0;
fi

. /wiware/bin/wicore.sh

/bin/sh /wiware/bin/ping.sh
ping_result=$?

inmode=$(uci get wipark.conf.inmode 2>/dev/null|| echo "1")
#inmode 1: box, 0: ordinary router
if [ $inmode -eq 0 ]; then
        wipark_log "checknet box inmode = 0, dnsblock keep off"
	dnsblocked=$(/bin/sh /wiware/bin/dnsblock.sh check)
	if [ "$dnsblocked" = "YES" ]; then
		/bin/sh /wiware/bin/dnsblock.sh off
	fi
        exit 0;
fi

if [ $ping_result -eq 0 ]; then
    /bin/sh /wiware/bin/dnsblock.sh off
    /bin/sh /wiware/bin/flowcount.sh  &
else
    /bin/sh /wiware/bin/dnsblock.sh on
fi

# add white domains ip
for domain in $(uci get wipark.site.domain 2>/dev/null);
do
	iplist=$(nslookup $domain|grep Address|grep -v localhost|awk '{print $3}')
        for domainip in ${iplist};
        do
                ip=$(echo $domainip|awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
                if [ "x$ip" != "x" ] && [ "$ip" != "$boxip" ];then
			existed=$(uci get wipark.site.ip |grep "$ip" |wc -l)
                	if [ $existed -eq 0 ]; then
                        	uci add_list wipark.site.ip=$ip
                        	iptables -t nat -I PREROUTING -d $ip -j ACCEPT
                        	wipark_log "add white domain [$domain] ip: $ip"
                	fi
                fi
        done
done

uci commit

/bin/sh /wiware/bin/checklanip.sh &

