#!/bin/sh

if [ "$2" = "" ]; then
   echo "usage: $0 mac second"
   exit 1
fi

. /sbin/redis.sh

BOXID=$(wiget idmac)

cur_time=$(cat /proc/uptime| awk -F'.' '{printf $1}')
file_time=$(expr $cur_time + $2)
  
macip=$(wiget macip $1)
macid=$(echo $1 |sed 's/://g')
mac=$(echo $1 |tr [a-z] [A-Z])
count=$(ls /tmp/-.$mac* 2>/dev/null|wc -l)
  
if [ $count -eq 0 ];
then
	ipaddr=$(/sbin/uci get network.wlan.ipaddr 2>/dev/null || echo '10.10.10.1')

	touch /tmp/-.$mac.$file_time
	echo "[$(date +'%Y-%m-%d %H:%M:%S')][uptime:$cur_time] add $mac" >> /tmp/log/usermac.log
	iptables -t nat -I PREROUTING -m mac --mac-source $mac -j ACCEPT
	iptables -t nat -I PREROUTING  -p tcp --dport 80 ! -d $ipaddr -m mac --mac-source $mac -j DNAT --to $ipaddr:8118

#	iptables -t nat -I PREROUTING -p tcp -m multiport --dports 0:79,81:65535  -m mac --mac-source $mac -j ACCEPT
#	iptables -t nat -I PREROUTING -p udp --dport 0:65535  -m mac --mac-source $mac -j ACCEPT
	$REDISCMD_S SETEX "users:$macid:atime" $2 "$BOXID" 1>/dev/null 2>/dev/null &

	#weixin qrcode c to wifi
	wxblackurl=$(/sbin/uci get wipark.conf.wxblackurl 2>/dev/null || echo "http://10.1.0.6/redirect")
	if [ $(echo $wxblackurl|grep http://|grep -v grep|wc -l) -eq 0 ]; then
		wxblackip=$(echo $wxblackurl|awk -F'/' '{print $1}')
	else
		wxblackip=$(echo $wxblackurl|awk -F'http://' '{print $2}'|awk -F'/' '{print $1}')
	fi
	iptables -t nat -D PREROUTING -p tcp -j DNAT -d $wxblackip --to $ipaddr:8080
	iptables -t nat -I PREROUTING -p tcp -j DNAT -d $wxblackip --to $ipaddr:8080
else
	rm -f /tmp/-.$mac.*
	touch /tmp/-.$mac.$file_time
	echo "[$(date +'%Y-%m-%d %H:%M:%S')][uptime:$cur_time] add $mac" >> /tmp/log/usermac.log
fi

exit 0

