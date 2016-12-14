#!/bin/sh
# set whether pop window once associated wifi

. /wiware/bin/command/common.sh

hasprinter=$(echo $msgbody|awk -F '#' '{print $1}')

if ["x$hasprinter" != "x" ] && [ $hasprinter -eq 1 ]; then
	switch='on'
else
	switch='off'
fi

sh /wiware/bin/setprinter.sh $switch
result="$?"

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3036) set printer parameter error.")
        report "$S_FAILED#$base64out"
fi

