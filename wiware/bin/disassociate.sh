#!/bin/sh

if [ $# -ne 1 ]; then
        exit
fi

. /wiware/bin/wicore.sh

mac=$1

usermac=$(echo $1|tr [a-z] [A-Z])

# for iOS captive portal
rm -f "/tmp/captive.$usermac" 2>/dev/null

wipark_log "$mac dissassociated"

mac=$(wiget idmac)
token=$(wiget token)
msg=$(wiencode "$mac|$token|$usermac")

server=$(wiget server)
url="$server/userleave"

curl -s --connect-timeout 10 -m 10 $url --data-urlencode "msg=$msg" 2>/dev/null
