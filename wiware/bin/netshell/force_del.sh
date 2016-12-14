#!/bin/sh

mac=$1
iptables -t nat -D PREROUTING -m mac --mac-source $mac -j ACCEPT 2>/dev/null
filename=$(ls /tmp/-.$mac* 2>/dev/null)
[ "x$filename" != "x" ] && {
	rm -f $filename
}
