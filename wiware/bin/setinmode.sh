#!/bin/sh
#setinmode.sh 0|1

inmode="$1"

if [ "x$inmode" != "x" ]; then
	uci set wipark.conf.inmode=$inmode
	uci commit
	sh /wiware/bin/netshell/fw_add.sh
fi
