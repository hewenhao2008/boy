#!/bin/sh
# set accesstime

. /wiware/bin/command/common.sh

accesstime=$(echo $msgbody|awk -F '#' '{print $1}')
result='1'
if [ "x$accesstime" != "x" ] && [ $accesstime -ge 1 ] && [ $accesstime -lt 1000 ]; then
	let accesstime=3600*$accesstime
	uci set wipark.conf.accesstime="$accesstime"
	uci commit
	result='0'
fi

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3035) set accesstime parameter error.")
        report "$S_FAILED#$base64out"
fi

