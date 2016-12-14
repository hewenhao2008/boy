#!/bin/sh

bandwidth=$(uci get wsqos.conf.bandwidth 2>/dev/null)
shared=$(uci get wsqos.conf.shared 2>/dev/null)
maxclients=$(uci get wsqos.conf.maxclients 2>/dev/null)

pid=$(pidof wsqos)
[ "x$pid" != "x" ] && kill -9 $pid

wsqos -r ${bandwidth:-30} -s ${shared:-1} &

apguy -m ${maxclients:-32}

