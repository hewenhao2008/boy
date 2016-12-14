#!/bin/sh

if [ $# -ne 1 ]; then
	exit
fi

. /wiware/bin/wicore.sh
. /sbin/redis.sh

usermac=$(echo $1|sed -r '/[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}/!d'|tr [a-z] [A-Z])
macpass=$(ls /tmp/*$usermac* 2>/dev/null)
if [ "x$macpass" != "x" ]; then
	wipark_log "associate: $usermac no need get access time"
	exit
fi

wipark_log "associate: $usermac trying get access time"

macid=$(echo $usermac |sed 's/://g')
timeleft=$($REDISCMD_S ttl "users:$macid:atime" 2>/dev/null)
if [ $timeleft -gt 0 ]; then
	/wiware/bin/netshell/mac_add.sh $usermac $timeleft
fi

mac=$(wiget idmac)
token=$(wiget token)
accesstime=$(/sbin/uci get wipark.conf.accesstime 2>/dev/null|| echo '86400')
msg=$(wiencode "$mac|$token|$usermac|$accesstime")

server=$(wiget server)
qauthurl="$server/authcheck"

curl -s --connect-timeout 10 -m 20 $qauthurl --data-urlencode "msg=$msg" 2>/dev/null &
#output=$(curl -s --connect-timeout 10 -m 20 $qauthurl --data-urlencode "msg=$msg" 2>/dev/null)
#timeleft=$(widecode $output|sed -n '/^[0-9]\+$/p')
wipark_log "associate: $usermac get accesstime timeleft $timeleft"

#if [ "x$timeleft" != "x" ] && [ "$timeleft" != "0" ]; then
#	/wiware/bin/netshell/mac_add.sh $usermac $timeleft
#fi

