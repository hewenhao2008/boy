#!/bin/sh

#bandwidth shared

if [ $# -lt 2 ]; then
	echo "Usage: $0 <bandwidth> <shared> <maxclients>"
	exit 1
fi

bandwidth="$1"
shared="$2"
maxclients="$3"

if [ $bandwidth -le 0 ] || [ $bandwidth -gt 99999 ]; then
	exit 1
fi

if [ $maxclients -lt 3 ] || [ $maxclients -gt 50 ]; then
	maxclients=$(uci get wsqos.conf.maxclients 2>/dev/null || echo '32')
fi

uci set wsqos.conf.bandwidth="$bandwidth"
uci set wsqos.conf.shared="$shared"
uci set wsqos.conf.maxclients="$maxclients"
uci commit

sh /wiware/bin/startqos.sh
 
