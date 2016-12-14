#!/bin/sh

if [ $# -lt 3 ];then
    return
fi
logpath="/tmp/log"
logfile="$logpath/www.log"
tmplogfile="$logpath/www.log.tmp"

phoneip=$1
phonetime=$2
log=$3

year=`date "+%Y"`
if [ $year -eq 1970 ];then
    date -s "$phonetime"
fi

timetag=`date "+%Y%m%d%H%M%S"`
mac=`/wiware/bin/wiget ipmac $phoneip`
if [ "$mac" = "" ];then
	mac="00:00:00:00:00:00"
fi

echo "$mac $timetag $log" >> $logfile
#echo "$mac $timetag $log" 
sync

sz=`ls -l $logfile|awk '{print $5}'`
if [ $sz -gt 1000000 ];then
#    num=`(ls /wiware/log/www.log.* -l 2>/dev/null||echo 0)|awk -F'.' '{print $NF}'|sort -nr|awk '{print $NF+1}'|head -n 1`
#    mv $logfile $logfile.$num
    mv $logfile $tmplogfile
    md5out=$(md5sum $tmplogfile|awk '{print $1}')
    if [ "x$md5out" != "x" ]; then
    	mv $tmplogfile "$logpath/$md5out.log"
    fi
fi
