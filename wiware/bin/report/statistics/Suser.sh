#!/bin/sh
source /etc/profile >/dev/null
. /sbin/redis.sh

boxmac=$(wiget idmac)
[ "x$boxmac" = "x" ] && exit 1

maclist=$(apguy -g|awk '{print $1}'|tr -s '\n' ' ')
$REDISCMD_S SADD box:$boxmac:client:macset "$maclist"

count=$(apguy -g|wc -l)
$REDISCMD_S SET box:$boxmac:client:count $count
