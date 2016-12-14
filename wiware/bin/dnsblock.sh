#!/bin/sh
#dnsblock.sh on|off
#on - set dns blocked, off clear dns block

restart_boxservice(){
    kill -9 $(ps |grep boxselect|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
    kill -9 $(ps |grep wicmd|grep -v vi|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
}

check_dnsblock(){
	dnsblocked=$(cat /etc/dnsmasq.conf |grep '8.8.8.8')
	if [ "x$dnsblocked" != "x" ]; then
		echo 'YES'
	else
		echo 'NO'
	fi
}

dnsblock_setup(){
	ipaddr=$(uci get network.lan.ipaddr)
	domain=$(uci get wipark.conf.domain 2>/dev/null||echo 'm.wipark.cn')
	domaintail=$(echo $domain |awk -F'.' 'sub($1".","")')
	
#	echo "resolv-file=/tmp/resolv.conf.auto" > /etc/dnsmasq.conf
  	echo "address=/.com/.cn/.net/.org/.pw/.name/.cc/.me/.so/.co/.info/.biz/.hk/.tv/.tel/.asia/.travel/.mobi/$ipaddr" >> /etc/dnsmasq.conf
  	echo "server=/.$domaintail/www.baidu.cn/www.apple.com/8.8.8.8" >> /etc/dnsmasq.conf
  	echo "server=/.$domaintail/www.baidu.cn/www.apple.com/114.114.114.114" >> /etc/dnsmasq.conf
}

dnsblock_setclear(){
	echo "#auto clear" > /etc/dnsmasq.conf
}

dnsblock_on(){
	if [ "x$(pidof dnsmasq)" != "x" ]; then
		dnsblock_setup
		/etc/init.d/dnsmasq restart &
		nginx -s reload
	fi
}

dnsblock_off(){
	if [ "x$(pidof dnsmasq)" != "x" ]; then
		echo "#auto clear" > /etc/dnsmasq.conf
		/etc/init.d/dnsmasq restart  && restart_boxservice
		nginx -s reload
	fi
}

#main part

case "$1" in
setup)
	dnsblock_setup
;;
setclear)
	dnsblock_setclear
;;
on)
	dnsblocked=$(check_dnsblock)
	if [ "$dnsblocked" != "YES" ]; then
		dnsblock_on
	fi
;;
off)
	dnsblocked=$(check_dnsblock)
	if [ "$dnsblocked" = "YES" ]; then
		dnsblock_off
	fi
;;
check)
	check_dnsblock
;;
*)                                                     
    echo "Usage: $0 on|off|setup|check"
;;     
esac

