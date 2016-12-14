#!/bin/sh

. /wiware/bin/wicore.sh

key=$(wiget key)
mac=$(wiget idmac)
token=$(echo -n "$mac$key"|md5sum|awk '{print substr($1,1,4)}')
msg=$(wiencode "$mac|$token")

server=$(wiget server)
serverdate=$(curl -s --connect-timeout 5 -m 5 "$server/ntp" --data-urlencode "msg=$msg")
if [ "x$serverdate" != "x" ]; then
	wipark_log "ntp sync time from server: $serverdate"
	date -s "$serverdate"
fi

