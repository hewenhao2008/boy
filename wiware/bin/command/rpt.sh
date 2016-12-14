#!/bin/sh
# 

. /wiware/bin/command/common.sh

server=$(wiget server)
if [ "x$server" = "x" ];  then
        wipark_log "$0 no server found. 'wiget server' got null."
        exit 1
fi

$CURL -s --connect-timeout 10 -m 10 "$server/report" --data-urlencode "msg=$1"
