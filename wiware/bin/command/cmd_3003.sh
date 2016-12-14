#!/bin/sh
# set network to pppoe

. /wiware/bin/command/common.sh

account=$(echo $msgbody|awk -F '#' '{print $1}')
password=$(echo $msgbody|awk -F '#' '{print $2}')

result='1'
if [ "x$account" != "x" ] && [ "x$password" != "x" ]; then
	sh /wiware/bin/setpppoe.sh $account $password
	result="$?"
fi

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
	base64out=$(mbase64 "(3003) set network to pppoe, parameter error.");
        report "$S_FAILED#$base64out"
fi

sleep 3 && /etc/init.d/network restart

