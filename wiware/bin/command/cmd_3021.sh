#!/bin/sh
# set guest wireless 

. /wiware/bin/command/common.sh

ssid_prefix=$(echo $msgbody|awk -F '#' '{print $1}')
[ "x$ssid_prefix" = "x" ] ssid_prefix='none'

ssid_name=$(echo $msgbody|awk -F '#' '{print $2}')
[ "x$ssid_name" = "x" ] ssid_name='none'

encryption=$(echo $msgbody|awk -F '#' '{print $3}')
key=$(echo $msgbody|awk -F '#' '{print $4}')

sh /wiware/bin/setwireless.sh guest 1 "$ssid_prefix" "$ssid_name" "$encryption" "$key"
result="$?"

if [ $result -eq 0 ]; then
        report $S_SUCCESS
else
        base64out=$(mbase64 "(3021) setwireless guest parameter error.")
        report "$S_FAILED#$base64out"
fi

#sleep 3 && /etc/init.d/network restart
/sbin/wifi restart
# restart again for from psk2 to none will become wep state restart again will be ok
/sbin/wifi restart

