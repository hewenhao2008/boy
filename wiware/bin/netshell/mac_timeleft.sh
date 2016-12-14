#!/bin/sh
if [ $# -eq 0 ]; then
	echo 0
	exit
fi
mac=$1
left=$(ls /tmp/-.$mac* 2>/dev/null|wc -l)
if [ $left -ne 0 ]; then
        curtime=$(cat /proc/uptime| awk -F'.' '{printf $1}')
        endtime=$(ls /tmp/-.$mac* 2>/dev/null|awk -F'.' '{print $3}')
        if [ "x$endtime" != "x" ]; then
                let left=$endtime-$curtime
		[ $left -lt 0 ] && let left=0
        fi
fi
echo $left
