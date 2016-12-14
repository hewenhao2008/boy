#!/bin/sh
# set wlan

. /wiware/bin/command/common.sh

ip=$(echo $msgbody|awk -F '#' '{print $1}')
netmask=$(echo $msgbody|awk -F '#' '{print $2}')

result='1'
if [ "x$ip" != "x" ]; then
	sh /wiware/bin/setwlan.sh $ip $netmask
	result="$?"
fi

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3012) setwlan parameter error.")
        report "$S_FAILED#$base64out"
fi

sleep 3 && /etc/init.d/network restart

