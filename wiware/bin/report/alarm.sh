#!/bin/sh

. /wiware/bin/wicore.sh

if [ $# -lt 2 ]; then
	echo "Usage: $0 <alarmcode> <alarminfo>"
fi
alarmcode="$1"
alarminfo="$2"
server=$(wiget server)
if [ "x$server" = "x" ];  then
        wipark_log "$0 no server found. 'wiget server' got null." 
        exit 1
fi

mac=$(wiget idmac)
token=$(wiget token)
alarmtime=$(date "+%Y%m%d%H%M%S")
msgbody="$alarmcode#$alarmtime$alarminfo"
wipark_log "alarm: $msgbody"
alarmsg=$(wiencode "$mac|$token|$msgbody")
curl -s --connect-timeout 10 -m 10 "$server/alarm" --data-urlencode "msg=$alarmsg"

