#!/bin/sh
if [ $# -lt 1 ]; then
        echo "Usage: $0 <url>"
	exit 1
fi

. /wiware/bin/wicore.sh

whitedomainlist='/wiware/etc/white.domain.list'
[ ! -f $whitedomainlist ] && touch $whitedomainlist

url=$1
if [ $(echo $url|grep http://|wc -l) -eq 0 ]; then
        domain=$(echo $url|awk -F'/' '{print $1}')
else
        domain=$(echo $url|awk -F'http://' '{print $2}'|awk -F'/' '{print $1}')
fi

domain=$(echo $domain|sed -r '/^[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+){1,2}$/!d')
if [ "x$domain" != "x" ]; then
	existed=$(grep "$domain" $whitedomainlist)
	if [ "x$existed" = "x" ]; then
		echo $domain >> $whitedomainlist
		wipark_log "add $domain to whitedomain list"
	else
		wipark_log "$domain already existed in whitedomain list"
	fi
fi

