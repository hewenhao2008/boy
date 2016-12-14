#!/bin/sh
. /wiware/bin/wicore.sh
export PATH=$PATH:/wiware/bin

FIRMWARE_SAVE_JSON='/tmp/firmware.json'

mac=$(wiget idmac)
token=$(wiget token)
hardware=$(wiget hardware)
version=$(wiget version)
msg=$(wiencode "$mac|$token|$hardware|$version")

server=$(wiget server)
url="$server/upgrade"
output=$(curl -s --connect-timeout 10 -m 10 "$url" --data-urlencode "msg=$msg" 2>/dev/null)
widecode $output > $FIRMWARE_SAVE_JSON

