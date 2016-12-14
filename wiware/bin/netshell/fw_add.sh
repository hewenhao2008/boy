#!/bin/sh
source /lib/functions/network.sh
source /wiware/bin/wicore.sh

iptables -t nat -F PREROUTING

inmode=$(uci get wipark.conf.inmode 2>/dev/null|| echo "1")
#inmode 1: box, 0: ordinary router
if [ $inmode -eq 0 ]; then
        wipark_log "fw_add box inmode = 0, no iptables rules" 
        exit 0;
fi
 
if ! network_get_ipaddr wlanip "wlan"; then
	wipark_log error "fw_add get wlan ip failed. return"
	return 1
fi
if ! network_get_subnet wlansubnet "wlan"; then
	wipark_log error "fw_add get wlan subnet failed. return"
        return 1
fi

banport=$(uci get wipark.conf.banport 2>/dev/null || echo '1')
if [ $banport -eq 1 ]; then
	iptables -t nat -I PREROUTING -p tcp -m tcp --dport 0:65535 -j DNAT -s $wlansubnet --to $wlanip:65531
	iptables -t nat -I PREROUTING -p udp -m udp --dport 0:65535 -j DNAT -s $wlansubnet --to $wlanip:65531
	iptables -I FORWARD -p tcp --dport 65531 -j DROP
fi

#iptables -t nat -I PREROUTING -p tcp -m tcp --dport 80 -j DNAT -s $ipmask.0/24 --to $wlanip:80
iptables -t nat -I PREROUTING -p tcp -m multiport --dports 80,443,8080 -j DNAT -s $wlansubnet --to $wlanip:80

iptables -t nat -I PREROUTING -p udp --dport 67:68 -j ACCEPT
iptables -t nat -I PREROUTING -d $wlanip -j ACCEPT

#RFC 1918 inner LAN address
openlannet=$(uci get wipark.conf.openlannet 2>/dev/null || echo '0')
if [ $openlannet -eq 1 ]; then
        iptables -t nat -I PREROUTING -d 10.0.0.0/8 -j ACCEPT
        iptables -t nat -I PREROUTING -d 172.16.0.0/12 -j ACCEPT
        iptables -t nat -I PREROUTING -d 192.168.0.0/16 -j ACCEPT
fi

#bypass wire lan
if network_get_subnet lansubnet "lan"; then
	iptables -t nat -I PREROUTING -s $lansubnet -j ACCEPT
fi

# server ip
for serverip in $(uci get wipark.server.ip 2>/dev/null);
do
        iptables -t nat -I PREROUTING -d $serverip -j ACCEPT
done

for siteip in $(uci get wipark.site.ip 2>/dev/null);
do
	iptables -t nat -I PREROUTING -d $siteip -j ACCEPT
done

if [ -f /wiware/etc/white.ip.list ]; then
   iplist=$(cat /wiware/etc/white.ip.list)
   for whiteip in ${iplist};
   do
	iptables -t nat -I PREROUTING -d $whiteip -j ACCEPT
   done
fi

for mac in $(uci get wipark.user.white 2>/dev/null);
do
      iptables -t nat -I PREROUTING -m mac --mac-source $mac -j ACCEPT
done

for accepteduser in $(ls /tmp/-*);
do
	usermac=$(echo $accepteduser|awk -F'.' '{print $2}')
	if [ $(uci get wipark.user.white 2>/dev/null| grep $usermac |wc -l) -eq 0 ]; then
      		iptables -t nat -I PREROUTING -m mac --mac-source $usermac -j ACCEPT
	fi
done

for mac in $(uci get wipark.user.black 2>/dev/null);
do
      iptables -t nat -I PREROUTING -p tcp -m mac --mac-source $mac -j DNAT --to-destination $wlanip:80
done

if [ -f /wiware/etc/whiteserver.list ];
then
   serverlist=$(cat /wiware/etc/whiteserver.list)
   for servernet in ${serverlist};
   do
        iptables -t nat -I PREROUTING -d $servernet -j ACCEPT
   done
fi

#weixin rules
wxblackurl=$(/sbin/uci get wipark.conf.wxblackurl 2>/dev/null || echo "http://10.1.0.6/redirect")
if [ $(echo $wxblackurl|grep http://|grep -v grep|wc -l) -eq 0 ]; then
        wxblackip=$(echo $wxblackurl|awk -F'/' '{print $1}')
else
        wxblackip=$(echo $wxblackurl|awk -F'http://' '{print $2}'|awk -F'/' '{print $1}')
fi
iptables -t nat -I PREROUTING -p tcp -j DNAT -d $wxblackip --to $wlanip:8080

