#!/bin/sh
#setuphosts.sh

source /wiware/bin/wicore.sh

lanip=$(/sbin/uci get network.lan.ipaddr 2>/dev/null|awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
lanip=${lanip:-192.168.88.253}
wlanip=$(/sbin/uci get network.wlan.ipaddr 2>/dev/null |awk -F'.' '$1<255 && $1>=0 && $2<255 && $2>=0 && $3<255 && $3>=0 && $4<255 && $4>=0 {print $0}')
wlanip=${wlanip:-10.10.10.1}

domain=$(/sbin/uci get wipark.conf.domain 2>/dev/null || echo 'm.wipark.cn')
domaintail=$(echo $domain |awk -F'.' 'sub($1".","")')

domain_alies=$(/sbin/uci get wipark.conf.domain_alies 2>/dev/null || echo "u.$domaintail")

#cp /wiware/etc/hosts /etc/hosts
cp /wiware/etc/hosts /tmp/hosts
if [ "$domain_alies" != "$domain" ]; then
	echo "$wlanip $domain_alies" >> /tmp/hosts
fi
echo "$wlanip $domain" >> /tmp/hosts
echo "$wlanip w.$domaintail" >> /tmp/hosts
if [ "$domaintail" != "wipark.cn" ]; then
	echo "$wlanip m.wipark.cn" >> /tmp/hosts
	echo "$wlanip w.wipark.cn" >> /tmp/hosts
fi
echo "$wlanip $domaintail" >> /tmp/hosts

if [ "$domain_alies" != "$domain" ]; then
	echo "$lanip $domain_alies" >> /tmp/hosts
fi
echo "$lanip $domain" >> /tmp/hosts
echo "$lanip w.$domaintail" >> /tmp/hosts
if [ "$domaintail" != "wipark.cn" ]; then
	echo "$lanip m.wipark.cn" >> /tmp/hosts
	echo "$lanip w.wipark.cn" >> /tmp/hosts
fi
echo "$lanip $domaintail" >> /tmp/hosts
sync

diffout=$(diff -b /tmp/hosts /etc/hosts)
if [ "x$diffout" != "x" ]; then
	cp -f /tmp/hosts /etc/hosts
fi
    
if [ "x$1" = "x" ]; then
	/etc/init.d/dnsmasq restart
fi

