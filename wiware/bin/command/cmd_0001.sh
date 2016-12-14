#!/bin/sh

. /wiware/bin/command/common.sh 

#-------
# symbol link in /tmp refer to ext disk tmp download dir, if this link exist, that means box extend with a disk  
TMPDIR_EXT="/tmp/extdownload"
# tmp download dir in /tmp
TMPDIR_FLASH="/tmp/download"

tmpoutput="/tmp/0001.out"

curlprogress(){
   rptprefix="$1"
   g_stime=0
   null_times=0
   line=""
   while true
   do
	read -t 10 line
        wipark_log "curlprogress get a line[$line]"
        if [ "x$line" = "x" ];then
   		let null_times=$null_times+1
		if [ $null_times -gt 10 ]; then
			wipark_log "line null over 10 times, progress return."
			return
		fi
                continue;
        fi
   	let null_times=0

        let l_stime=$g_stime+3
        l_ntime=$(cat /proc/uptime |awk -F'.' '{print $1}')
        if [ $l_ntime -lt $l_stime ];then
                continue
        fi
        let g_stime=$l_ntime

	tmpresult=$(echo "$line"|awk '{print $1"|"$2"|"$4"|"$9"|"$10"|"$11"|"$12}') 
	wipark_log $tmpresult
	base64out=$(echo $tmpresult|base64|sed -e ':a;N;$ s/\n//g;ba')
        report "$rptprefix:$base64out"
	percent=$(echo "$line"|awk '{print $1}')
	if [ $percent -ge 100 ]; then
		wipark_log "0001 download 100 percent,progress return."
		return
	fi
   done
}

#--------
# function download $1 resid
#--------
download(){
	resid="$1"
	tmpdir="$TMPDIR_FLASH/$resid"
	[ -d $TMPDIR_EXT ] && tmpdir="$TMPDIR_EXT/$resid"
	wipark_log "download dir: $tmpdir"

	[ -d $tmpdir ] && wipark_log "$tmpdir already existed, resume the previous download"
	mkdir -p $tmpdir
   
	olddir=$(pwd)
	downurl="$server/boxdl?id=$resid"
	wipark_log "download url: $downurl"
	downfile="$tmpdir/$resid.tar"

	[ -d /wiware/www ] || {
		linkto=$(ls -ld /wiware/www|awk -F"/wiware/www" '{print $NF}')
		errinfo="The symbol link /wiware/www $linkto broken."
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
		report "$resid:$S_DOWNFAILED:$base64out"
		return
	} 
   
	let diskavailablesize=$(df /wiware/www|grep -v Available|awk '{print $4}')*1024  

	httpcode=$(curl -I -s --connect-timeout 10 -m 20 -L $downurl 2>/dev/null|grep HTTP|grep -v grep|awk '{print $2}'|tail -n 1)
	[ $httpcode -ne 200 ] && {
		errinfo="Download $resid failed: httpcode $httpcode" 
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
                report "$resid:$S_DOWNFAILED:$base64out"
                return
	}
	downfilesize=$(curl -I -s --connect-timeout 10 -m 20 -L $downurl 2>/dev/null |grep Content-Length|grep -v grep|awk -F':' '{print $2}')
	if [ $downfilesize -gt $diskavailablesize ]; then
		errinfo="No enough space left: disk available($diskavailablesize), downfile($downfilesize)" 
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
	        report "$resid:$S_DOWNFAILED:$base64out"
		return
	fi
	wipark_log "Downloading. disk available($diskavailablesize), downfile($downfilesize)"
       (curl --connect-timeout 10 -C - --limit-rate 3M -o $downfile $downurl -L 2>&1;echo $?>/tmp/rtcode)|tr -s '\r' '\n'|awk '/:.*:/ {print}'|curlprogress "$resid:$S_DOWNLOADING"
   
	curlstatus=$(cat /tmp/rtcode)
   	wipark_log "curl status=$curlstatus"
	if [ "$curlstatus" = "33" ]; then
		rm -rf $tmpdir
	elif [ "$curlstatus" != "0" ]; then
		errinfo="download $resid failed. curl status code ($curlstatus)." 
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
		report "$resid:$S_DOWNFAILED:$base64out"
		return
	fi
   
	report "$resid:$S_DOWNSUCCESS"
	wipark_log "curl $resid done."
   
	calmd5=$(md5sum $downfile|awk '{print $1}')
	if [ "$calmd5" != "$resid" ]; then
		rm -rf $tmpdir
		errinfo="check md5 error, resid[$resid], calmd5[$calmd5]." 
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
		report "$resid:$S_CHECKFAILED:$base64out"
		return
	fi
   
	wipark_log "check md5 success."
	report "$resid:$S_CHECKSUCCESS"

	# 0002: md5 ok, next is to extract and exec install.sh
	cd "$tmpdir"
	tar -xf "$downfile" 1>"$tmpoutput" 2>&1
	tarstatus=$?
	if [ $tarstatus -ne 0 ]; then
  		errinfo="extract tar $downfile failed." 
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
		report "$resid:$S_UNTARFAILED:$base64out"
		cd $olddir
		rm -rf $tmpdir
		return
	fi
	wipark_log "cmd_0001 extract tar $downfile done."
	report "$resid:$S_UNTARSUCCESS"

	#delete tarbao after untar
	[ "x$downfile" != "x" ] &&  rm $downfile
   
	sh "$tmpdir/install.sh" 1>>"$tmpoutput" 2>&1
	shellstatus=$?
	cd $olddir
	rm -r $tmpdir
   
	if [ $shellstatus -ne 0 ];then
		errinfo="shell exec failed.($tmpdir/install.sh)" 
		wipark_log error "cmd_0001 $errinfo"
		base64out=$(mbase64 "$errinfo")
		report "$resid:$S_SHELLFAILED:$base64out"
		return
	fi
   
	wipark_log "shell exec done."
	report "$resid:$S_SHELLSUCCESS"
   
	wipark_log "Download task rsid=$resid done."
	report "$resid:$S_SUCCESS"
}

#--------
# main part
#--------

oldIFS="$IFS"
IFS="#"
arr="$msgbody"
for rsid in $arr
do
	echo "$rsid"
	download "$rsid"
done
IFS="$oldIFS"

