#!/bin/sh
# Copyright (C) 2014 wipark.cn

. /wiware/bin/wicore.sh
. /sbin/redis.sh

serverurlfile="/tmp/serverurl"
mac=$(/wiware/bin/wiget idmac)
key=$(/wiware/bin/wiget key)
token=$(echo -n "$mac$key"|md5sum|awk '{print substr($1,1,4)}')
msg=$(wiencode "$mac|$token")

errcnt=0

while true
do
	if [ "x$(pidof nginx)" == "x" ]; then
		nginx
	fi
	if [ "x$(pidof php-cgi)" == "x" ]; then
		(/usr/sbin/phpguy -b 127.0.0.1 -p 9009 -c 4 -f /usr/bin/php-cgi >/dev/null 2>&1) &
	fi

	if [ "x$(pidof dnsmasq)" == "x" ]; then
		wipark_log error "dnsmasq is not running, restart it."
		/etc/init.d/dnsmasq restart
	fi

        if [ "x$(pidof getmsg)" == "x" ]; then
                /sbin/getmsg &
        fi
        
        if [ "x$(pidof redis-server)" == "x" ]; then
                /etc/init.d/redis start &
        fi

        if [ "x$(pidof privoxy)" == "x" ]; then     
                /etc/init.d/privoxy start &         
        fi         
        
        InnerMsg=$($REDISCMD_L RPOP InnerMsg)
        if [ "$InnerMsg" != "" ]; then
           echo $InnerMsg >> /tmp/innermsg.log
           op=$(echo $InnerMsg|awk '{print $1}')
           case "$op" in
           rxprint)
               $InnerMsg >> /tmp/innermsg.log
               ;;
           *)
               ;;
           esac
        fi

	if [ ! -f $serverurlfile ]; then
		kill -9 $(ps -w|grep wigetserver|grep -v vi|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
		/bin/sh /wiware/bin/wigetserver.sh
	fi
	server=$(/wiware/bin/wiget server)

	if [ "$(date '+%Y')" = "1970" ]; then
		/bin/sh /wiware/bin/ntp.sh 
	fi

	if [ $(ps -w |grep curl |grep boxselect |grep -v grep |wc -l) -eq 0  ]; then
		let errcnt++
		kill -9 $(ps -w|grep wicmd|grep -v vi|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
		wipark_log "longpull url: $server/boxselect?msg=$msg" 
		curl -s --connect-timeout 10 "$server/boxselect" --data-urlencode "msg=$msg" -L -N 2>/dev/null |/bin/sh /wiware/bin/wicmd.sh &
	elif [ $(ps -w |grep wicmd.sh |grep -v grep |wc -l) -eq 0 ]; then
		#boxselect exist but wicmd not exist
		kill -9 $(ps -w|grep boxselect|grep -v grep|awk {'print $1'}|tr -s "\n" " ") 2>/dev/null
		continue
	fi
 
	if [ $errcnt -gt 5 ]; then
		rm $serverurlfile 2>/dev/null
		errcnt=0
	fi
	
        if [ -f "/tmp/needcheck" ]; then                                                               
                killall redis-cli && \                                                                 
                rm /tmp/needcheck                                                                      
        fi         

	sleep 3
done

