#!/bin/sh
counted_interfaces="apcli0:|eth2.2:|eth0:"

if [ $(date "+%Y") -lt 2014 ]; then
	exit 0;
fi

month="$(date '+%m')"
flow_count_file="/wiware/tmp/flowcount.txt"

fa='/tmp/tmpcounted.txt'
fb='/tmp/tmpcounted.txt.b'

cat /proc/net/dev > $fb

down1="0";
up1="0";
if [ -f $fa ]; then
	eval $(cat $fa |grep -E $counted_interfaces |awk '{down1+=$2, up1+=$10}END{print "down1="down1";up1="up1}');
	eval $(cat $fb |grep -E $counted_interfaces |awk '{down2+=$2, up2+=$10}END{print "down2="down2";up2="up2}');
else
	eval $(cat $fb |grep -E $counted_interfaces |awk '{down2+=$2, up2+=$10}END{print "down2="down2";up2="up2}');
fi

mv $fb $fa

#echo "up2:$up2, down2:$down2"
#echo "up1:$up1, down1:$down1"

let d_up=$up2-$up1
let d_down=$down2-$down1

if [ ! -f $flow_count_file ]; then
        echo "0 0 0" > $flow_count_file
fi 

eval $(cat $flow_count_file 2>/dev/null |awk '{print "count_month="$1";up="$2";down="$3}')
#echo "co:($up, $down)"

if [ $count_month -ne $month ]; then
        #next month, re-count start
        up="0"
        down="0"
fi

let up=$up+$d_up
let down=$down+$d_down

echo "$month $up $down" > $flow_count_file

. /sbin/redis.sh

$REDISCMD_S SET box:$boxmac:wifi:uptotal $up
$REDISCMD_S SET box:$boxmac:wifi:downtotal $down

#echo "cn:($up, $down)"

