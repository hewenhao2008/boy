#!/bin/sh
# set whether pop window once associated wifi

. /wiware/bin/command/common.sh

popwindow=$(echo $msgbody|awk -F '#' '{print $1}')

if [ "x$popwindow" != "x" ] && [ $popwindow -eq 1 ]; then
	pop='on'
else
	pop='off'
fi

sh /wiware/bin/setpopwindow.sh $pop
result="$?"

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3033) set popwindow parameter error.")
        report "$S_FAILED#$base64out"
fi

