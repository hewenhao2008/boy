#!/bin/sh
# ping to check if box can access internet.
# shell exit status: 0-can access, 1-cann't access

. /wiware/bin/wicore.sh

ledgreenon(){
        led blue off 
	led red off
        led green on
}

ledgreenoff(){
        led green off
        led red off
        led blue on
}

checkudhcpc(){
        #hasgateway=$(route|grep default|wc -l)
        #if [ $hasgateway -eq 0 ]; then
        #        killall udhcpc
        #fi
        killall udhcpc
}

ping_wipark(){
	key=$(wiget key)
	mac=$(wiget idmac)
	token=$(echo -n "$mac$key"|md5sum|awk '{print substr($1,1,4)}')
	msg=$(wiencode "$mac|$token")

	server=$(wiget server)
	serverdate=$(curl -s --connect-timeout 5 -m 10 "$server/ntp" --data-urlencode "msg=$msg")
	date -s "$serverdate" 1>/dev/null 2>&1

	if [ $? -eq 0 ]; then
		return 0
	else
		return 1
	fi
}

ping_baidu(){
	connected=$(curl -v --connect-timeout 5 -m 10 --user-agent 'Mozilla/5.0' http://www.baidu.cn 2>&1|grep "Location: http://www.baidu.com"|wc -l)
	if [ $connected -eq 0 ]; then
		return 1
	else
		return 0
	fi
}

ping_dns(){
	pingcnt=$(ping -c 2 -w 3 114.114.114.114|grep 'time='|wc -l)
        if [ $pingcnt -gt 0 ]; then
		return 0
	fi

	pingcnt=$(ping -c 2 -w 3 8.8.8.8|grep 'time='|wc -l)
        if [ $pingcnt -gt 0 ]; then
		return 0
	else
		return 1
	fi
}

ping_apple(){
	appleurl='http://www.apple.com/library/test/success.html'
	ret=$(curl -s --connect-timeout 5 -m 10 "$appleurl"|grep Success|wc -l)
	if [ $ret -gt 0 ]; then
		return 0
	else
		return 1
	fi
}

ping_baidu || ping_dns || ping_wipark || ping_apple

pingresult=$?
if [ $pingresult -eq 0 ]; then
	touch /tmp/neton 
	ledgreenon
else
	rm /tmp/neton 
	ledgreenoff
	checkudhcpc
fi

exit $pingresult

# $? = 0: internet ON, other internet OFF

