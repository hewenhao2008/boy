#!/bin/sh
# set domain alies

. /wiware/bin/command/common.sh

domainalies=$(echo $msgbody|awk -F '#' '{print $1}'|sed -r '/^[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+){1,2}$/!d')
result='1'
if [ "x$domainalies" != "x" ]; then
	uci set wipark.conf.domain_alies="$domainalies"
	uci commit
	sh /wiware/bin/setuphosts.sh
	result="$?"
fi

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3034) set domainalies parameter error.")
        report "$S_FAILED#$base64out"
fi

