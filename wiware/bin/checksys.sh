#!/bin/sh

trimlog(){
	log=$1
	sz=$(ls -l $log|awk '{print $5}')
	if [ $sz -gt 1000000 ];then
		echo ""> $log
	fi
}

checkdisk(){
    available=`df|grep rootfs|grep -v grep|awk '{print $4}'`
    if [ $available -gt 500 ];then
        return
    fi
    
    # if available less than 500KB, make an alarm
    alarmcode="1003"
    if [ -f /wiware/bin/report/alarm.sh ];then
	/bin/sh /wiware/bin/report/alarm.sh $alarmcode "Flash only $available Bits left."
    fi
}

checkdisk

