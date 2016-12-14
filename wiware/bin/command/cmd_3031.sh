#!/bin/sh
# set bandwidth contrl

. /wiware/bin/command/common.sh

bandwidth=$(echo $msgbody|awk -F '#' '{print $1}')
shared=$(echo $msgbody|awk -F '#' '{print $2}')

sh /wiware/bin/setbandctrl.sh $bandwidth $shared
result="$?"

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3031) setbandwidth control parameter error.")
        report "$S_FAILED#$base64out"
fi

