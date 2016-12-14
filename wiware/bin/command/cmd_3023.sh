#!/bin/sh
# set wireless channel

. /wiware/bin/command/common.sh

channel=$(echo $msgbody|awk -F '#' '{print $1}')

if [ "x$channel" != "x" ]; then
	if [ "$channel" = "auto" ] || [ $channel -gt 0 ] && [ $channel -le 15 ]; then
        	report $S_SUCCESS
		sh /wiware/bin/setchannel.sh $channel
		result="$?"
		if [ $result -eq 0 ]; then
			exit 0
		fi
	fi
fi

base64out=$(mbase64 "(3023) setchannel parameter error.")
report "$S_FAILED#$base64out"

