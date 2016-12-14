#!/bin/sh

SYS_ERRLOG=/root/sys.error.log
FLAG_SYSOK=/root/sys.ok
FLAG_TFOK=/root/tf.ok

echo "">$SYS_ERRLOG

redblink(){
	while true
	do
		led all off
		sleep 1
		led red on
		sleep 1
	done
}

testsuccess(){
	touch $FLAG_SYSOK
	sync
}

errorlog(){
	echo "$1" >> $SYS_ERRLOG
	redblink
}

flash_available=$(df|grep rootfs|awk '{print $4}')
if [ $flash_available -lt 100 ]; then
	errorlog "test failed: flash_available is $flash_available" 
fi

tmp_available=$(df|grep /tmp|awk '{print $4}')
if [ $tmp_available -lt 1000 ]; then
	errorlog "test failed: /tmp available is $tmp_available" 
fi

extdisk='/dev/mmcblk0p1'
mounted=0
if [ -e $extdisk ]; then
	ext_fstype=$(blkid /dev/mmcblk0p1|awk -F" TYPE=" '{print $2}'|awk -F'"' '{print $2}')
	if [ "$ext_fstype" != "ext4" ]; then
		errorlog "test failed: ext_fstype=$ext_fstype" 
	fi

	firstboot=$(df |grep mmcblk0p1|grep /tmp/root/mnt|grep -v grep|wc -l)
	if [ $firstboot -gt 0 ]; then
		reboot
	fi 

	mounted=$(df |grep mmcblk0p1|grep -v grep|wc -l)
	if [ $mounted -gt 0 ]; then
		ext_total=$(df |grep mmcblk0p1|awk '{print $2}')
		ext_available=$(df |grep mmcblk0p1|awk '{print $4}')
		touch $FLAG_TFOK
	else
		errorlog "test failed: TF card not mounted." 
	fi
fi

www_linkto=$(ls /wiware/www -l|awk -F'->' '{print $2}'|sed 's/^[[:space:]]//')

if [ $mounted -gt 0 ]; then
	if [ "$www_linkto" != "/mnt/mmcblk0p1/www2" ]; then
		errorlog "test failed: www soft link error. [$www_linkto] should be [/mnt/mmcblk0p1/www2]" 
	fi
else
	if [ "$www_linkto" != "/wiware/www1" ]; then
		errorlog "test failed: www soft link error. [$www_linkto] should be [/wiware/www1]" 
	fi
fi

homepage='/wiware/www/index.cgi'
homepage_size=$(ls -l $homepage|awk '{print $5}')
if [ $homepage_size -lt 10 ]; then
	errorlog "test failed: home page($homepage) size is $homepage_size." 
fi

testsuccess

