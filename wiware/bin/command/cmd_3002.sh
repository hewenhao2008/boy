#!/bin/sh
# set network to static

. /wiware/bin/command/common.sh

ip=$(echo $msgbody|awk -F '#' '{print $1}')
gatway=$(echo $msgbody|awk -F '#' '{print $2}')
netmask=$(echo $msgbody|awk -F '#' '{print $3}')
dns=$(echo $msgbody|awk -F '#' '{print $4}')

result='1'
if [ "x$ip" != "x" ] && [ "x$gateway" != "x" ] && [ "x$netmask" != "x" ]; then
	sh /wiware/bin/setstatic.sh $ip $gatway $netmask $dns
	result="$?"
fi

if [ $result -eq 0 ]; then
	report $S_SUCCESS
else
	base64out=$(mbase64 "(3002) set network to static, parameter error.")
	report "$S_FAILED#$base64out"
fi

sleep 3 && /etc/init.d/network restart

