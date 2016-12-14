#!/bin/sh
# set office wireless 

. /wiware/bin/command/common.sh

enabled=$(echo $msgbody|awk -F '#' '{print $1}')
[ "$enabled" != "1" ] && enabled='0'

ssid_name=$(echo $msgbody|awk -F '#' '{print $2}')
[ "x$ssid_name" = "x" ] ssid_name='WIPARK-OFFICE'

encryption=$(echo $msgbody|awk -F '#' '{print $3}')
key=$(echo $msgbody|awk -F '#' '{print $4}')

sh /wiware/bin/setwireless.sh office $enabled noprefix "$ssid_name" "$encryption" "$key"
result="$?"

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3022) setwireless office parameter error.")
        report "$S_FAILED#$base64out"
fi

#sleep 3 && /etc/init.d/network restart
/sbin/wifi restart
#restart again
/sbin/wifi restart

